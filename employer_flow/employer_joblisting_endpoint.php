<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../db/connection.php';

// Validate session variables
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    die(json_encode(['error' => 'Required session variables are not set.']));
}

$employer_name = $_SESSION['name'];
$employer_mobile = $_SESSION['mobile'];

// Get search parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['filter'] ?? 'all';
$location_filter = $_GET['location'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = max(1, intval($_GET['per_page'] ?? 20));
$offset = ($page - 1) * $per_page;

// Base query
$base_query = "
  FROM job_id AS ji
  JOIN locations ON ji.location_code = locations.id
  WHERE ji.employer_mobile_no = ?
";

$params = [$employer_mobile];
$types = "s"; // string

// Search filter
if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $base_query .= " AND (ji.jobrole LIKE ? OR ji.location LIKE ? OR ji.contact_person_name LIKE ?)";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Status filter
if ($status_filter !== 'all') {
    $base_query .= " AND ji.job_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Location filter
if (!empty($location_filter)) {
    $location_param = "%" . $location_filter . "%";
    $base_query .= " AND ji.location LIKE ?";
    $params[] = $location_param;
    $types .= "s";
}

// Count query
$count_query = "SELECT COUNT(*) " . $base_query;
$stmt_count = $conn->prepare($count_query);
if (!$stmt_count) {
    die(json_encode(['error' => 'Count query preparation failed: ' . $conn->error]));
}

// Bind parameters (reference-safe)
$tmp = [];
$tmp[] = &$types;
foreach ($params as $key => $value) {
    $tmp[] = &$params[$key];
}
call_user_func_array([$stmt_count, 'bind_param'], $tmp);

$stmt_count->execute();
$stmt_count->bind_result($total_records);
$stmt_count->fetch();
$stmt_count->close();

$total_pages = ceil($total_records / $per_page);

// Main query with pagination
$main_query = "
  SELECT
    ji.id,
    ji.companyname,
    ji.jobrole,
    ji.location,
    ji.created,
    ji.job_status,
    ji.contact_person_name,
    ji.department,
    ji.product,
    locations.area,
    locations.city,
    (
      SELECT COUNT(*)
      FROM jobs_applied AS ja
      WHERE ja.job_id = ji.id
    ) AS applied_count
" . $base_query . " LIMIT $offset, $per_page";

$stmt = $conn->prepare($main_query);
if (!$stmt) {
    die(json_encode(['error' => 'Main query preparation failed: ' . $conn->error]));
}

// Bind again with same parameters
$tmp2 = [];
$tmp2[] = &$types;
foreach ($params as $key => $value) {
    $tmp2[] = &$params[$key];
}
call_user_func_array([$stmt, 'bind_param'], $tmp2);

$stmt->execute();
$result = $stmt->get_result();

// Fetch result
$jobs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dept_ids = $row['department']; // "1,2"
        $product_ids = $row['product']; // "3,5"

        // Departments
        $departments = [];
        if (!empty($dept_ids)) {
            $dept_query = "SELECT department_name FROM departments WHERE department_id IN ($dept_ids)";
            $dept_result = $conn->query($dept_query);
            while ($d = $dept_result->fetch_assoc()) {
                $departments[] = $d['department_name'];
            }
        }

        // Products
        $products = [];
        if (!empty($product_ids)) {
            $prod_query = "SELECT product_name FROM products WHERE product_id IN ($product_ids)";
            $prod_result = $conn->query($prod_query);
            while ($p = $prod_result->fetch_assoc()) {
                $products[] = $p['product_name'];
            }
        }

        $jobs[] = [
            'id' => $row['id'],
            'companyname' => $row['companyname'],
            'jobrole' => $row['jobrole'],
            'location' => $row['area'] . " , " . $row['city'],
            'created' => $row['created'],
            'job_status' => $row['job_status'],
            'department' => implode(', ', $departments),
            'product' => implode(', ', $products),
            'contact_person_name' => $row['contact_person_name'],
            'applied_count' => (int) $row['applied_count'],
        ];
    }
}

// Pagination info
$pagination = [
    'total_records' => $total_records,
    'total_pages' => $total_pages,
    'current_page' => $page,
    'per_page' => $per_page
];

// Return response
header('Content-Type: application/json');
echo json_encode(['jobs' => $jobs, 'pagination' => $pagination]);

$stmt->close();
$conn->close();
?>
