<?php
include '../db/connection.php';
session_start();

// Get the employer id from the session
$employer_id = $_SESSION['employer_id'];

// Pagination and Limit
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $limit;

$mobileNumbers = [];
$whereClauses = [];
// Fetch candidate_mobile from order_items
$employer_mobile = $_SESSION['mobile'] ?? null;

if ($employer_mobile) {
    $sql = "SELECT candidate_mobile 
            FROM order_items 
            WHERE purchased = 1 
            AND employer_mobile = ? 
            AND expired = 0";

    // Using prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $employer_mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $mobileNumbers[] = "'" . $conn->real_escape_string($row['candidate_mobile']) . "'";
    }
    $stmt->close();
}

// Check if we have any mobile numbers
if (empty($mobileNumbers)) {
    echo '<div class="alert alert-info">No candidates matching your criteria.</div>';
    exit;
}

// Build WHERE clause with mobile numbers
$whereClauses[] = "candidate_details.mobile_number IN (" . implode(',', $mobileNumbers) . ")";
$whereSQL = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';



// 1.----------- Search input ----------
if (!empty($_GET['search'])) {
    // Get the search term and escape it
    $search = $conn->real_escape_string($_GET['search']);
    $searchLower = strtolower($search);

    // Remove everything except digits and the decimal point.
    $numericSearch = preg_replace('/[^0-9.]/', '', $search);

    if (is_numeric($numericSearch) && $numericSearch !== '') {
        // Use a LIKE comparison for numeric values
        $whereClauses[] = "(candidate_details.current_salary LIKE '%{$numericSearch}%' 
                         OR candidate_details.work_experience LIKE '%{$numericSearch}%')";
    } else {
        // Special case for sales search
        if ($searchLower == 'sales') {
            $whereClauses[] = "(LOWER(candidate_details.Sales) = 'yes' 
                             OR LOWER(candidate_details.jobrole) LIKE '%sales%' 
                             OR LOWER(candidate_details.destination) LIKE '%sales%')";
        }
        // Special case for gender search
        else if ($searchLower == 'male' || $searchLower == 'female') {
            $whereClauses[] = "LOWER(candidate_details.gender) = '{$searchLower}'";
        }
        // Default text search for other terms
        else {
            $searchConditions = [];
            $searchConditions[] = "LOWER(candidate_details.username) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.mobile_number) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.employed) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.current_company) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.sales_experience) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.destination) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.current_location) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.resume) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.hl_lap) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.personal_loan) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.business_loan) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.education_loan) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.credit_cards) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.gold_loan) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.casa) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.others) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.unique_link) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.associate_id) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.associate_name) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.associate_mobile) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.associate_link) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.jobrole) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.companyname) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.location) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.Credit_dept) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.HR_Training) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.Legal_compliance_Risk) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.Operations) LIKE '%{$searchLower}%'";
            $searchConditions[] = "LOWER(candidate_details.Others1) LIKE '%{$searchLower}%'";

            $whereClauses[] = '(' . implode(' OR ', $searchConditions) . ')';
        }
    }
}

// 2.------------ Apply filter ---------
if (!empty($_GET['applied'])) {
    $applied = $_GET['applied'] ?? [];
    $shortestInterval = null;
    $olderThanOneYear = false;

    $intervalMap = [
        '3d' => '3 days',
        '7d' => '7 days',
        '15d' => '15 days',
        '1m' => '1 month',
        '3m' => '3 months',
        '6m' => '6 months',
        '1y' => '1 year',
    ];

    foreach ($applied as $val) {
        if ($val === '1ys') {
            $olderThanOneYear = true;
            continue;
        }

        if (isset($intervalMap[$val])) {
            $intervalStr = $intervalMap[$val];
            $timestamp = strtotime("-$intervalStr");
            if ($shortestInterval === null || $timestamp > $shortestInterval) {
                $shortestInterval = $timestamp;
            }
        }
    }

    $orConditions = [];

    if ($shortestInterval !== null) {
        $formattedDate = date('Y-m-d H:i:s', $shortestInterval);
        $orConditions[] = "candidate_details.created >= '$formattedDate'";
    }

    if ($olderThanOneYear) {
        $formattedDate = date('Y-m-d H:i:s', strtotime("-1 year"));
        $orConditions[] = "candidate_details.created < '$formattedDate'";
    }


}

// 3.------------- Loaction filter ---------
// if (!empty($_GET['location'])) {
//     $locations = $_GET['location'];
//     $safeLocations = array_map(function ($loc) use ($conn) {
//         return "'" . $conn->real_escape_string($loc) . "'";
//     }, $locations);
//     $orConditions[] = "candidate_details.current_location IN (" . implode(',', $safeLocations) . ")";
// }


if (!empty($_GET['location']) && is_array($_GET['location'])) {
    $escaped_ids = array_map(function ($id) use ($conn) {
        return (int) $id;
    }, $_GET['location']);

    $id_list = implode(',', $escaped_ids);
    $city_query = "SELECT city_wise_id FROM locations WHERE id IN ($id_list)";
    $city_result = mysqli_query($conn, $city_query);

    $cities = [];
    while ($row = mysqli_fetch_assoc($city_result)) {
        $cities[] = $conn->real_escape_string($row['city_wise_id']);
    }
    $matching_ids = [];
    foreach ($cities as $city) {
        $like_query = "SELECT id FROM locations  WHERE city_wise_id =   '$city'";
        $like_result = mysqli_query($conn, $like_query);

        while ($like_row = mysqli_fetch_assoc($like_result)) {
            $matching_ids[] = (int) $like_row['id'];
        }
    }
    $all_location_ids = array_unique(array_merge($escaped_ids, $matching_ids));
    if (!empty($all_location_ids)) {
        $all_ids_list = implode(',', $matching_ids);
        $orConditions[] = "candidate_details.location_code IN  ($all_ids_list)";
    }
}






// 3.-------------- Keyword Filter ----------
if (!empty($_GET['havekeyword'])) {

    // Get the search term and escape it
    $havekeyword = $conn->real_escape_string($_GET['havekeyword']);
    $havekeywordLower = strtolower($havekeyword);

    // Check if it's a phone number (10 digits)
    if (preg_match('/^\d{10}$/', $havekeyword)) {
        // 10-digit phone number search
        $mobileCleaned = preg_replace('/[^0-9]/', '', $havekeyword);
        $whereClauses[] = "(candidate_details.mobile_number = '{$mobileCleaned}' OR 
                          candidate_details.associate_mobile = '{$mobileCleaned}' OR
                          candidate_details.mobile_number LIKE '%{$mobileCleaned}%' OR 
                          candidate_details.associate_mobile LIKE '%{$mobileCleaned}%')";
    }
    // Check for salary patterns (e.g. 10LPA, 10 lpa, 10L, 10 lakhs, etc.)
    else if (preg_match('/(\d+)[\s]*(l|lpa|lakhs?|lacs?)/i', $havekeyword, $matches)) {
        $salaryValue = $matches[1]; // Extract the numeric part
        $whereClauses[] = "(candidate_details.salary LIKE '{$salaryValue}%' OR 
                          candidate_details.current_salary LIKE '{$salaryValue}%' OR
                          candidate_details.salary LIKE '%{$salaryValue}L%' OR 
                          candidate_details.current_salary LIKE '%{$salaryValue}L%' OR
                          candidate_details.salary LIKE '%{$salaryValue} L%' OR 
                          candidate_details.current_salary LIKE '%{$salaryValue} L%' OR
                          candidate_details.salary LIKE '%{$salaryValue} lakhs%' OR 
                          candidate_details.current_salary LIKE '%{$salaryValue} lakhs%')";
    }
    // Check for experience patterns (e.g. 5 years, 5yr, 5y, etc.)
    else if (preg_match('/(\d+)[\s]*(y|yr|yrs?|years?)/i', $havekeyword, $matches)) {
        $expValue = $matches[1]; // Extract the numeric part
        $whereClauses[] = "(candidate_details.work_experience LIKE '{$expValue}%' OR 
                          candidate_details.work_experience LIKE '%{$expValue} y%' OR
                          candidate_details.work_experience LIKE '%{$expValue}y%' OR
                          candidate_details.sales_experience LIKE '{$expValue}%' OR 
                          candidate_details.sales_experience LIKE '%{$expValue} y%' OR
                          candidate_details.sales_experience LIKE '%{$expValue}y%')";
    }
    // Check for pure numeric values that could be salary or experience
    else if (is_numeric($havekeyword)) {
        $numericValue = (float) $havekeyword;

        // If it's a larger number (>15), likely a salary in thousands/lakhs
        if ($numericValue > 15) {
            $whereClauses[] = "(candidate_details.salary LIKE '{$numericValue}%' OR 
                              candidate_details.current_salary LIKE '{$numericValue}%' OR
                              candidate_details.salary LIKE '%{$numericValue}K%' OR 
                              candidate_details.current_salary LIKE '%{$numericValue}K%' OR
                              candidate_details.salary LIKE '%{$numericValue}L%' OR 
                              candidate_details.current_salary LIKE '%{$numericValue}L%')";
        }
        // If it's a smaller number (<=15), likely years of experience
        else {
            $whereClauses[] = "(candidate_details.work_experience LIKE '{$numericValue}%' OR 
                              candidate_details.work_experience LIKE '%{$numericValue} y%' OR
                              candidate_details.sales_experience LIKE '{$numericValue}%' OR 
                              candidate_details.sales_experience LIKE '%{$numericValue} y%')";
        }
    }
    // Otherwise continue with your regular processing for other search terms
    else {
        // Special case for sales search
        if ($havekeywordLower == 'sales') {
            $whereClauses[] = "(LOWER(candidate_details.Sales) = 'yes' 
                             OR LOWER(candidate_details.jobrole) LIKE '%sales%' 
                             OR LOWER(candidate_details.destination) LIKE '%sales%')";
        }
        // Special case for gender search
        else if ($havekeywordLower == 'male' || $havekeywordLower == 'female') {
            $whereClauses[] = "LOWER(candidate_details.gender) = '{$havekeywordLower}'";
        }
        // Default text search for other terms
        else {
            $havekeywordConditions = [];
            $havekeywordConditions[] = "LOWER(candidate_details.username) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.mobile_number) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.employed) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.current_company) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.sales_experience) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.destination) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.current_location) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.resume) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.hl_lap) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.personal_loan) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.business_loan) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.education_loan) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.credit_cards) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.gold_loan) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.casa) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.others) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.unique_link) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.associate_id) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.associate_name) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.associate_mobile) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.associate_link) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.jobrole) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.companyname) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.location) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.Credit_dept) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.HR_Training) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.Legal_compliance_Risk) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.Operations) LIKE '%{$havekeywordLower}%'";
            $havekeywordConditions[] = "LOWER(candidate_details.Others1) LIKE '%{$havekeywordLower}%'";

            $whereClauses[] = '(' . implode(' OR ', $havekeywordConditions) . ')';
        }
    }
}

// 4.----------- Gender Filter------------

if (!empty($_GET['gender']) && is_array($_GET['gender'])) {
    $genders = $_GET['gender']; // this is an array
    $escapedGenders = [];

    foreach ($genders as $g) {
        $escapedGenders[] = "'" . strtolower($conn->real_escape_string($g)) . "'";
    }

    // Build the OR conditions for all selected genders
    $orConditions[] = "LOWER(candidate_details.gender) IN (" . implode(',', $escapedGenders) . ")";
}

// 5.------------- Deaprtment filter ---------------
if (!empty($_GET['department'])) {
    $departments = $_GET['department'];
    $orParts = [];

    foreach ($departments as $dep) {
        $dep = $conn->real_escape_string($dep); // Sanitize input
        $orParts[] = "FIND_IN_SET('{$dep}', candidate_details.departments)";
    }

    if (!empty($orParts)) {
        $orConditions[] = '(' . implode(' OR ', $orParts) . ')';
    }
}
// 6.------------- subdepartment filter ---------------
if (!empty($_GET['subdepartment'])) {
    $departments = $_GET['subdepartment'];
    $orParts = [];

    foreach ($departments as $subdep) {
        $subdep = $conn->real_escape_string($subdep);
        $orParts[] = "FIND_IN_SET('{$subdep}', candidate_details.sub_departments)";
    }

    if (!empty($orParts)) {
        $orConditions[] = '(' . implode(' OR ', $orParts) . ')';
    }
}

// 7.------------- Category filter ---------------
if (!empty($_GET['category'])) {
    $categorys = $_GET['category'];
    $orParts = [];

    foreach ($categorys as $cate) {
        $cate = $conn->real_escape_string($cate);
        $orParts[] = "FIND_IN_SET('{$cate}', candidate_details.category)";
    }

    if (!empty($orParts)) {
        $orConditions[] = '(' . implode(' OR ', $orParts) . ')';
    }
}

// 8.------------- Product filter ---------------
if (!empty($_GET['product']) && is_array($_GET['product'])) {
    $product = $_GET['product'];
    $orParts = [];

    foreach ($product as $pro) {
        $pro = $conn->real_escape_string($pro);
        $orParts[] = "FIND_IN_SET('$pro', candidate_details.products)";
    }

    if (!empty($orParts)) {
        $orConditions[] = '(' . implode(' OR ', $orParts) . ')';
    }
}


// 9.------------- Subproduct filter ---------------
if (!empty($_GET['subproduct'])) {
    $subproducts = $_GET['subproduct'];
    $orParts = [];

    foreach ($subproducts as $subpro) {
        $subpro = $conn->real_escape_string($subpro);
        $orParts[] = "FIND_IN_SET('{$subpro}', candidate_details.sub_products)";
    }

    if (!empty($orParts)) {
        $orConditions[] = '(' . implode(' OR ', $orParts) . ')';
    }
}
// 10.------------- specialization filter ---------------
if (!empty($_GET['specialization'])) {
    $specialization = $_GET['specialization'];
    $orParts = [];

    foreach ($specialization as $specia) {
        $specia = $conn->real_escape_string($specia); // Sanitize input
        $orParts[] = "FIND_IN_SET('{$specia}', candidate_details.specialization)";
    }
    if (!empty($orParts)) {
        $orConditions[] = '(' . implode(' OR ', $orParts) . ')';
    }
}


// 11.------------- Designation filter ---------
if (!empty($_GET['designation'])) {
    $designations = $_GET['designation'];
    $safedesignations = array_map(function ($desi) use ($conn) {
        return "'" . $conn->real_escape_string($desi) . "'";
    }, $designations);
    $orConditions[] = "candidate_details.destination IN (" . implode(',', $safedesignations) . ")";
}


//12.-------------- Experience Filter ------------

if (!empty($_GET['min_experience']) || !empty($_GET['max_experience'])) {
    // Common conversion logic for experience values
    $expConversion = "CAST(REGEXP_REPLACE(LOWER(work_experience), '[^0-9.]', '') AS DECIMAL(10,2))";

    // Apply minimum experience filter if provided
    if (!empty($_GET['min_experience'])) {
        $minExp = (float) $_GET['min_experience'];
        $whereClauses[] = "$expConversion >= {$minExp}";
    }

    // Apply maximum experience filter if provided
    if (!empty($_GET['max_experience'])) {
        $maxExp = (float) $_GET['max_experience'];
        $whereClauses[] = "$expConversion <= {$maxExp}";
    }
}

//13.-------------- Salary Filter ------------

if (!empty($_GET['min_salary']) || !empty($_GET['max_salary'])) {
    // Extract just the numeric value from the salary string
    $numericSalary = "CAST(REGEXP_REPLACE(LOWER(candidate_details.current_salary), '[^0-9.]', '') AS DECIMAL(10,2))";

    // Create a SQL condition that compares the extracted numeric value
    if (!empty($_GET['min_salary']) && !empty($_GET['max_salary'])) {
        $minSalary = (float) $_GET['min_salary'];
        $maxSalary = (float) $_GET['max_salary'];

        // The key fix: Use strict less than for upper bound with a small buffer
        $whereClauses[] = "({$numericSalary} >= {$minSalary} AND {$numericSalary} < {$maxSalary} + 0.01)";
    } elseif (!empty($_GET['min_salary'])) {
        $minSalary = (float) $_GET['min_salary'];
        $whereClauses[] = "{$numericSalary} >= {$minSalary}";
    } elseif (!empty($_GET['max_salary'])) {
        $maxSalary = (float) $_GET['max_salary'];
        // Important: Use strict less than for upper bound
        $whereClauses[] = "{$numericSalary} <= {$maxSalary}";
    }
}

/*
 * Add debug information
 */
if (!empty($_GET['min_salary']) || !empty($_GET['max_salary']) || !empty($_GET['search'])) {
    // echo "<div style='background: #ffeeee; padding: 10px; margin: 10px;'>";
    // echo "<h3>Debug Information</h3>";
    if (!empty($_GET['min_salary']))
        echo "<p>Min Salary: " . htmlspecialchars($_GET['min_salary']) . "</p>";
    if (!empty($_GET['max_salary']))
        echo "<p>Max Salary: " . htmlspecialchars($_GET['max_salary']) . "</p>";
    // if (!empty($_GET['search'])) echo "<p>Search Term: " . htmlspecialchars($_GET['search']) . "</p>";
    htmlspecialchars(implode(' AND ', $whereClauses));

}

/*
 * Combine all WHERE conditions.
 */
// $whereSQL = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
$finalWhereClauses = $whereClauses;
if (!empty($orConditions)) {
    $finalWhereClauses[] = '(' . implode(' OR ', $orConditions) . ')';
}

$whereSQL = count($finalWhereClauses) > 0 ? 'WHERE ' . implode(' AND ', $finalWhereClauses) : '';


// Pagination parameters
// $recordsPerPage = 10;
// $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
// $offset = ($page - 1) * $recordsPerPage;

// Get total candidate count
$countQuery = "SELECT COUNT(*) as total 
               FROM candidate_details 
               JOIN candidates 
               ON candidate_details.mobile_number = candidates.mobile_number 
               {$whereSQL}";

$countResult = $conn->query($countQuery);

if (!$countResult) {
    echo "Count Query Error: " . $conn->error;
    exit;
}

// $totalCandidates = $countResult->fetch_assoc()['total'];
// $totalPages = ceil($totalCandidates / $recordsPerPage);

// Main query with pagination
// $query = "SELECT candidate_details.*, candidates.updated 
//           FROM candidate_details 
//           JOIN candidates 
//           ON candidate_details.mobile_number = candidates.mobile_number 
//           {$whereSQL} 
//           LIMIT {$offset}, {$recordsPerPage}";








$query = "SELECT 
    candidate_details.*, 
    candidates.updated, 
    locations.area,
    locations.city,
    locations.state
FROM candidate_details
JOIN candidates 
    ON candidate_details.mobile_number = candidates.mobile_number

LEFT JOIN locations 
    ON candidate_details.current_location = locations.id
{$whereSQL}";


// Pagination count before limit
$resultNoLimit = $conn->query($query);
$total_pages = ceil($resultNoLimit->num_rows / $limit);

// Apply pagination
$query .= " LIMIT $start_from, $limit";

// echo "<pre>$query</pre>";
// exit;
$result = $conn->query($query);

if (!$result) {
    echo "Query Error: " . $conn->error;
    exit;
}

// Pagination links function
function buildPaginationUrl($page)
{
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>

<!-- HTML output for candidate cards -->
<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php
        // Compute Product and department field.
        if (!function_exists('getNamesByIds')) {
            function getNamesByIds($conn, $table, $id_column, $name_column, $ids_string)
            {
                $ids = array_filter(array_map('intval', explode(',', $ids_string))); // ensures only valid integers
                if (empty($ids))
                    return [];
                $ids_sql = implode(',', $ids);
                $query = "SELECT $name_column FROM $table WHERE $id_column IN ($ids_sql)";
                $result = mysqli_query($conn, $query);
                $names = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $names[] = $row[$name_column];
                }
                return $names;
            }
        }
        // Usage examples:
        $departments = getNamesByIds($conn, 'departments', 'department_id', 'department_name', $row['departments']);
        $sub_departments = getNamesByIds($conn, 'sub_departments', 'sub_department_id', 'sub_department_name', $row['sub_departments']);
        $products = getNamesByIds($conn, 'products', 'product_id', 'product_name', $row['products']);
        $sub_products = getNamesByIds($conn, 'sub_products', 'sub_product_id', 'sub_product_name', $row['sub_products']);
        $specialization = getNamesByIds($conn, 'products_specialization', 'specialization_id', 'specialization', $row['specialization']);
        $categorys = getNamesByIds($conn, 'departments_category', 'category_id', 'category', $row['category']);

        $departmentStr = !empty($departments) ? implode(", ", $departments) : "N/A";
        $sub_departmentStr = !empty($sub_departments) ? implode(", ", $sub_departments) : "N/A";
        $productStr = !empty($products) ? implode(", ", $products) : "N/A";
        $sub_productStr = !empty($sub_products) ? implode(", ", $sub_products) : "N/A";
        $specializationStr = !empty($specialization) ? implode(", ", $specialization) : "N/A";
        $categoryStr = !empty($categorys) ? implode(", ", $categorys) : "N/A";



        // Get initials for avatar
        $initials = substr($row['username'], 0, 2);
        $workExp = htmlspecialchars($row['work_experience']) . " yrs";





        $mobile = $row['mobile_number'];
        $employer_mobile = $_SESSION['mobile'];
        $created_sql = "SELECT created_at FROM order_items WHERE candidate_mobile = '$mobile' AND employer_mobile= '$employer_mobile'";
        $created_result = $conn->query($created_sql);
        $expiry_days = "N/A"; // Default value if no record found

        if ($created_result->num_rows > 0) {
            $data = $created_result->fetch_assoc();
            $created_date = $data['created_at'];

            // Calculate expiry date (90 days from created)
            $expiry_date = date('Y-m-d', strtotime($created_date . ' +90 days'));

            // Calculate remaining days
            $current_date = date('Y-m-d');
            $days_left = (strtotime($expiry_date) - strtotime($current_date)) / 86400; // Convert seconds to days

            if ($days_left > 0) {
                $expiry_days = $days_left;
            } else {
                $expiry_days = "Expired";
            }
        }


        $active_sql = "SELECT updated FROM candidates WHERE mobile_number = '$mobile'";
        $active_result = $conn->query($active_sql);

        $last_active = "Long Ago"; // Default value

        if ($active_result && $active_result->num_rows > 0) {
            $active_data = $active_result->fetch_assoc();
            $active_date = $active_data['updated'] ?? null;

            if ($active_date) {
                $timezone = new DateTimeZone('Asia/Kolkata');
                $updated_time = new DateTime($active_date, $timezone);
                $current_time = new DateTime('now', $timezone);

                if ($updated_time > $current_time) {
                    $updated_time = clone $current_time;
                }

                $time_diff = $current_time->getTimestamp() - $updated_time->getTimestamp();
                $days = floor($time_diff / 86400);  // seconds to days

                if ($days <= 3) {
                    $last_active = "Last 3 days";
                } elseif ($days <= 7) {
                    $last_active = "Last 7 days";
                } elseif ($days <= 15) {
                    $last_active = "Last 15 days";
                } elseif ($days <= 30) {
                    $last_active = "Last 1 month";
                } elseif ($days <= 90) {
                    $last_active = "Last 3 months";
                } elseif ($days <= 180) {
                    $last_active = "Last 6 months";
                } elseif ($days <= 365) {
                    $last_active = "Last 1 year";
                } else {
                    $last_active = "1+ years ago";
                }

                $activeIcon = '<svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.49967 14.6663C4.81767 14.6663 1.83301 11.6817 1.83301 7.99967C1.83301 4.31767 4.81767 1.33301 8.49967 1.33301C11.485 1.33301 13.9837 3.29501 14.833 5.99967H13.1663" stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8.5 5.33301V7.99967L9.83333 9.33301M15.1367 8.66634C15.1567 8.44634 15.1667 8.22412 15.1667 7.99967M10.5 14.6663C10.7267 14.5912 10.9493 14.504 11.1667 14.405M14.36 11.333C14.4893 11.085 14.6038 10.8286 14.7033 10.5637M12.628 13.4857C12.8582 13.2955 13.0753 13.0906 13.2793 12.871" stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>';

                $footerItems[] = "<span class='active-status text-active'>{$activeIcon} {$last_active}</span>";
            }
        }

        ?>
        <div class="card candidate-card-landing shadow-sm mb-3" data-candidate-id="<?php echo $row['id']; ?>">
            <div class="card-body p-1 body-of-card">
                <div class="row">
                    <!-- Left column with profile check and initials -->
                    <div class="col-12 col-md-2">
                        <div>
                            <!-- <div class="form-check profile-round-candidate">
                                <input class="form-check-input" type="checkbox" id="card-check" name="sales" value="Sales">
                            </div> -->
                            <div class="profile-employer">
                                <span class="company-initial"><?php echo strtoupper($initials); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right column with candidate information -->
                    <div class="col-12 col-md-10 mt-4 candidate-detail-text">
                        <!-- Username and Mobile Number -->
                        <div class="row mb-1 copy-content">
                            <div class="col-8 col-md-12 candidate-name-number">

                                <button class="dotted-border-btn copy-btn"
                                    data-value="<?php echo htmlspecialchars($row['username']); ?>" title="Copy">
                                    <?php echo htmlspecialchars(ucfirst($row['username'])); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20"
                                        fill="none">
                                        <path
                                            d="M9.48829 15.417C9.32485 15.417 8.41338 15.417 7.73794 15.417C7.32372 15.417 6.98829 15.0812 6.98829 14.667V13.542V6.66699H4.98829C4.85568 6.66699 4.72851 6.71638 4.63474 6.80429C4.54097 6.89219 4.48829 7.01142 4.48829 7.13574L4.48828 15.917C4.48828 17.0216 5.38371 17.917 6.48828 17.917H13.4883C13.7535 17.917 14.0079 17.8182 14.1954 17.6424C14.3829 17.4666 14.4883 17.2281 14.4883 16.9795V15.417H9.48829Z"
                                            fill="#175DA8" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12.9683 1.66846H9.20177C8.67073 1.66846 8.24023 2.09895 8.24023 2.63V13.2069C8.24023 13.738 8.67073 14.1685 9.20177 14.1685H16.4133C16.9444 14.1685 17.3748 13.738 17.3748 13.2069V6.07504C17.3748 6.02403 17.3546 5.97512 17.3185 5.93905L13.1043 1.72478C13.0682 1.68872 13.0193 1.66846 12.9683 1.66846ZM16.8919 6.47677H13.0457V2.63062L16.8919 6.47677Z"
                                            fill="#175DA8" />
                                    </svg>
                                </button>
                                &nbsp; <span class="separator">|</span> &nbsp;

                                <button class="dotted-border-btn copy-btn"
                                    data-value="<?php echo htmlspecialchars($row['mobile_number']); ?>" title="Copy">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20"
                                        fill="none">
                                        <path
                                            d="M17.4069 12.9167C16.3652 12.9167 15.3652 12.75 14.4319 12.4417C14.2854 12.395 14.1289 12.389 13.9793 12.4242C13.8296 12.4594 13.6922 12.5346 13.5819 12.6417L11.7486 14.475C9.38306 13.2716 7.46029 11.3488 6.2569 8.98333L8.09023 7.14167C8.20061 7.03695 8.27883 6.90293 8.31573 6.75533C8.35263 6.60773 8.34668 6.45267 8.29857 6.30833C7.983 5.34824 7.82265 4.34395 7.82357 3.33333C7.82357 2.875 7.44857 2.5 6.99023 2.5H4.07357C3.61523 2.5 3.24023 2.875 3.24023 3.33333C3.24023 11.1583 9.5819 17.5 17.4069 17.5C17.8652 17.5 18.2402 17.125 18.2402 16.6667V13.75C18.2402 13.2917 17.8652 12.9167 17.4069 12.9167ZM16.5736 10H18.2402C18.2402 8.01088 17.4501 6.10322 16.0435 4.6967C14.637 3.29018 12.7294 2.5 10.7402 2.5V4.16667C13.9652 4.16667 16.5736 6.775 16.5736 10ZM13.2402 10H14.9069C14.9069 7.7 13.0402 5.83333 10.7402 5.83333V7.5C12.1236 7.5 13.2402 8.61667 13.2402 10Z"
                                            fill="#175DA8" />
                                    </svg> <?php echo htmlspecialchars($row['mobile_number']); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20"
                                        fill="none">
                                        <path
                                            d="M9.48829 15.417C9.32485 15.417 8.41338 15.417 7.73794 15.417C7.32372 15.417 6.98829 15.0812 6.98829 14.667V13.542V6.66699H4.98829C4.85568 6.66699 4.72851 6.71638 4.63474 6.80429C4.54097 6.89219 4.48829 7.01142 4.48829 7.13574L4.48828 15.917C4.48828 17.0216 5.38371 17.917 6.48828 17.917H13.4883C13.7535 17.917 14.0079 17.8182 14.1954 17.6424C14.3829 17.4666 14.4883 17.2281 14.4883 16.9795V15.417H9.48829Z"
                                            fill="#175DA8" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12.9683 1.66846H9.20177C8.67073 1.66846 8.24023 2.09895 8.24023 2.63V13.2069C8.24023 13.738 8.67073 14.1685 9.20177 14.1685H16.4133C16.9444 14.1685 17.3748 13.738 17.3748 13.2069V6.07504C17.3748 6.02403 17.3546 5.97512 17.3185 5.93905L13.1043 1.72478C13.0682 1.68872 13.0193 1.66846 12.9683 1.66846ZM16.8919 6.47677H13.0457V2.63062L16.8919 6.47677Z"
                                            fill="#175DA8" />
                                    </svg>
                                </button>

                            </div>
                        </div>

                        <!-- Dynamic Data Row (Gender, Work Experience, Salary, Location) -->
                        <div class="row mb-1">
                            <div class="col-12 extra-details-1">
                                <?php
                                // Build an array with each data field only if it has a non-empty value
                                $dataItems = array();

                                if (!empty($row['gender'])) {
                                    $genderIcon = '<img src=../images/icons8_gender.svg  width="16" height="16" style="margin: 0 4px 4px 0;">';
                                    $dataItems[] = $genderIcon . ' ' . htmlspecialchars(ucfirst($row['gender'])) . ', ' . $row['age'] . ' yrs';
                                }
                                if (!empty($workExp)) {
                                    $workExpIcon = '<img src=../images/basil_bag-solid-c.svg  width="16" height="16" style="margin: 0 4px 4px 0;">';
                                    $dataItems[] = ucfirst($workExpIcon . ' ' . $workExp);
                                }

                                // Salary with its icon
                                if (!empty($row['current_salary'])) {
                                    $salaryValue = (int) $row['current_salary'];
                                    $formattedSalary = '';

                                    if ($salaryValue < 100) {
                                        // 1 or 2 digit salary, append 'LPA'
                                        $formattedSalary = htmlspecialchars(strtoupper($salaryValue . ' LPA'));
                                    } else {
                                        // More than 2 digits, show number only
                                        $formattedSalary = htmlspecialchars(strtoupper($salaryValue));
                                    }

                                    $salaryIcon = '<img src=../images/ri_money-rupee-circle-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;">';

                                    $dataItems[] = $salaryIcon . ' ' . $formattedSalary;
                                }


                                // Current Location with its icon
                                if (!empty($row['location_code'])) {
                                    $locationIcon = '<img src=../images/weui_location-filled.svg  width="16" height="16" style="margin: 0 4px 4px 0;">';
                                    $dataItems[] = $locationIcon . ' ' . htmlspecialchars($row['area'] . " ," . $row['city']);
                                }

                                // Join the fields with the separator only if there are multiple items.
                                echo implode(' <span class="mx-2 span-line">|</span> ', $dataItems);
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Current Company -->
                    <div class="ms-2 ps-3 pe-3 mt-2 emp-candidate-div">
                        <div class="mb-1">
                            <img src=../images/ph_building-fill.svg width="16" height="16" style="margin: 0 4px 4px 0;">
                            <span class="text-subheadd me-1">Current / Latest</span>
                            <span class="current-company"
                                data-original-text="<?php echo htmlspecialchars(ucfirst($row['current_company'])); ?>">
                                <?php echo htmlspecialchars(mb_strimwidth($row['current_company'], 0, 85, '..')); ?>
                            </span>
                        </div>

                        <!-- Department and Product Details -->
                        <div class="row mb-1 mt-2">
                            <div class="col-12 col-md-4 candidate-detail-text">
                                <div class="mb-1">
                                    <img src="../images/mingcute_department-fill.svg" width="16" height="16"
                                        style="margin: 0 4px 4px 0;">
                                    <span class="text-subheadd me-1">Department:</span>
                                    <span class="department-string" title="<?php echo htmlspecialchars($departmentStr); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($departmentStr, 0, 16, '...')); ?>
                                    </span>

                                </div>
                                <div class="mb-1">
                                    <img src="../images/mdi_cash.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                                    <span class="text-subheadd me-1">Product:</span>
                                    <span class="product-string" title="<?php echo htmlspecialchars($productStr); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($productStr, 0, 16, '...')); ?>
                                    </span>

                                </div>
                            </div>
                            <div class="col-12 col-md-4 candidate-detail-text">
                                <div class="mb-1">
                                    <img src="../images/mingcute_department-fill.svg" width="16" height="16"
                                        style="margin: 0 4px 4px 0;">
                                    <span class="text-subheadd me-1">Sub-Dept:</span>
                                    <span class="department-string" title="<?php echo htmlspecialchars($sub_departmentStr); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($sub_departmentStr, 0, 19, '...')); ?>
                                    </span>

                                </div>
                                <div class="mb-1">
                                    <img src="../images/mdi_cash.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                                    <span class="text-subheadd me-1">Sub-Product:</span>
                                    <span class="product-string" title="<?php echo htmlspecialchars($sub_productStr); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($sub_productStr, 0, 15, '...')); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 candidate-detail-text">
                                <div class="mb-1">
                                    <img src="../images/mingcute_department-fill.svg" width="16" height="16"
                                        style="margin: 0 4px 4px 0;">
                                    <span class="text-subheadd me-1">Category:</span>
                                    <span class="department-string" title="<?php echo htmlspecialchars($categoryStr); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($categoryStr, 0, 19, '...')); ?>
                                    </span>
                                </div>
                                <div class="mb-1">
                                    <img src="../images/mdi_cash.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                                    <span class="text-subheadd me-1">Specialization:</span>

                                    <span class="product-string" title="<?php echo htmlspecialchars($specializationStr); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($specializationStr, 0, 14, '...')); ?>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="final-card d-flex justify-content-between align-items-center">
                <div>
                    <button class="reject btn-sm me-2">
                        <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.5 6.52002L15.5 14.52M7.5 14.52L15.5 6.52002" stroke="#ED4C5C" stroke-width="2.2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg> Reject
                    </button>
                    <button class="select btn-sm">
                        <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 11.72L9.675 15.395L17.025 7.52002" stroke="#4EA647" stroke-width="2.1"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg> Shortlist
                    </button>
                </div>
                <div class="text-muted small">
                    <?php if (!empty($row['resume'])): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.22226 2.337C8.74536 1.8139 9.45484 1.52002 10.1946 1.52002C10.9344 1.52002 11.6439 1.8139 12.167 2.337C12.6901 2.8601 12.984 3.56958 12.984 4.30936C12.984 5.04914 12.6901 5.75862 12.167 6.28172L7.3423 11.1064C7.19334 11.2554 7.01649 11.3736 6.82185 11.4543C6.6272 11.535 6.41857 11.5765 6.20787 11.5765C5.78235 11.5766 5.37422 11.4077 5.07327 11.1068C4.77232 10.806 4.60321 10.3979 4.60313 9.97239C4.60305 9.54686 4.77201 9.13873 5.07285 8.83778L9.75338 4.15641L10.3467 4.74976L5.66535 9.43029C5.5942 9.50144 5.53776 9.58591 5.49926 9.67887C5.46075 9.77183 5.44093 9.87147 5.44093 9.97209C5.44093 10.0727 5.46075 10.1723 5.49926 10.2653C5.53776 10.3583 5.5942 10.4427 5.66535 10.5139C5.73651 10.585 5.82097 10.6415 5.91394 10.68C6.0069 10.7185 6.10653 10.7383 6.20716 10.7383C6.30778 10.7383 6.40742 10.7185 6.50038 10.68C6.59334 10.6415 6.67781 10.585 6.74896 10.5139L11.5736 5.68921C11.7548 5.50801 11.8986 5.29289 11.9966 5.05613C12.0947 4.81938 12.1452 4.56562 12.1452 4.30936C12.1452 4.0531 12.0947 3.79934 11.9966 3.56259C11.8986 3.32583 11.7548 3.11071 11.5736 2.9295C11.3924 2.7483 11.1773 2.60456 10.9406 2.50649C10.7038 2.40842 10.45 2.35795 10.1938 2.35795C9.93752 2.35795 9.68376 2.40842 9.44701 2.50649C9.21025 2.60456 8.99513 2.7483 8.81392 2.9295L3.84594 7.89833C3.26963 8.48901 2.9493 9.283 2.95439 10.1082C2.95947 10.9335 3.28955 11.7235 3.87308 12.307C4.45662 12.8905 5.24661 13.2206 6.07185 13.2257C6.89708 13.2308 7.69108 12.9105 8.28176 12.3341L13.1064 7.50863L13.6998 8.10197L8.87426 12.9267C8.12434 13.6496 7.12059 14.0493 6.07898 14.0397C5.03737 14.0301 4.04114 13.6121 3.30463 12.8755C2.56813 12.1388 2.15021 11.1425 2.14079 10.1009C2.13137 9.05932 2.53121 8.05564 3.25427 7.30582L8.22226 2.337Z"
                                fill="#175DA8" />
                        </svg> CV Attached &nbsp; | &nbsp;
                    <?php endif; ?>
                    <?php if ($expiry_days == "Expired"): ?>
                        <i class="fa fa-circle text-danger"></i> Expired
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                            <g clip-path="url(#clip0_2603_650)">
                                <path
                                    d="M16.1968 5.9338C16.0203 5.3451 15.7677 4.77996 15.4462 4.25427L14.2617 4.97889C14.521 5.40276 14.7246 5.85818 14.8668 6.33259L16.1968 5.9338Z"
                                    fill="#888888" />
                                <path
                                    d="M15.3902 11.8402L14.2169 11.0977C13.9516 11.5168 13.6328 11.9013 13.2695 12.2405L14.217 13.2555C14.6669 12.8355 15.0616 12.3594 15.3902 11.8402Z"
                                    fill="#888888" />
                                <path
                                    d="M16.368 9.37746L15.0049 9.11292C14.9103 9.59983 14.753 10.0732 14.5371 10.5199L15.7873 11.124C16.0553 10.5695 16.2507 9.98192 16.368 9.37746Z"
                                    fill="#888888" />
                                <path
                                    d="M11.4648 13.3866L11.9808 14.6756C12.551 14.4474 13.091 14.1458 13.5859 13.779L12.7593 12.6633C12.36 12.9593 11.9244 13.2025 11.4648 13.3866Z"
                                    fill="#888888" />
                                <path
                                    d="M16.5002 8.00038C16.5002 7.57431 16.4627 7.14711 16.3884 6.73083L15.0215 6.97465C15.0813 7.31067 15.1118 7.65577 15.1117 8.00038C15.1117 8.15303 15.1058 8.30697 15.094 8.45786L16.4782 8.56607C16.4928 8.37924 16.5002 8.18893 16.5002 8.00038Z"
                                    fill="#888888" />
                                <path
                                    d="M10.841 13.5964C10.3644 13.7262 9.86992 13.7944 9.37109 13.7994L9.38459 15.1877C10.002 15.1817 10.6147 15.0971 11.2057 14.9361L10.841 13.5964Z"
                                    fill="#888888" />
                                <path
                                    d="M8.65741 13.7852C8.16639 13.732 7.68113 13.6149 7.21496 13.4373L6.72049 14.7347C7.29746 14.9546 7.89876 15.0996 8.50775 15.1656L8.65741 13.7852Z"
                                    fill="#888888" />
                                <path
                                    d="M2.64311 9.8033C2.74021 9.92207 2.89748 9.92207 2.99459 9.8033L5.08676 7.24349C5.18387 7.12473 5.13813 7.02847 4.98476 7.02847H3.59618C3.60719 6.96349 3.61784 6.89842 3.63099 6.83417C3.63118 6.83332 3.63136 6.83239 3.63154 6.83157C3.64866 6.74781 3.66838 6.66477 3.68911 6.58191C3.9718 5.46186 4.58089 4.47029 5.40926 3.71505C5.41407 3.7107 5.41897 3.70634 5.42378 3.7019C5.47403 3.65637 5.52515 3.61175 5.57697 3.56795C5.73249 3.43669 5.89493 3.31339 6.06379 3.19877C6.06766 3.1962 6.07147 3.19342 6.07535 3.19081C6.92021 2.62032 7.92268 2.26664 9.00246 2.20952C9.01662 2.2088 9.03078 2.20849 9.04493 2.20786C9.11974 2.20453 9.19471 2.20266 9.26979 2.20211C9.29154 2.20193 9.31329 2.20148 9.33504 2.20154C9.41269 2.20181 9.4901 2.20387 9.56721 2.2071C9.59468 2.20831 9.62202 2.20998 9.6494 2.21155C9.70984 2.21497 9.7701 2.21941 9.83036 2.22471C9.85368 2.22674 9.87712 2.22843 9.90042 2.23073C9.97798 2.2385 10.0552 2.24803 10.132 2.25886C10.1565 2.26228 10.1808 2.26618 10.2051 2.26996C10.4549 2.30838 10.7007 2.36289 10.9413 2.43298C10.9777 2.44363 11.0139 2.45504 11.05 2.46632C11.0881 2.47827 11.1261 2.49067 11.164 2.50344C11.2267 2.52452 11.2889 2.54684 11.3509 2.57008C11.3862 2.58333 11.4217 2.59636 11.4568 2.61034C11.5015 2.6281 11.5458 2.64688 11.5901 2.66579C11.615 2.67644 11.6399 2.68745 11.6648 2.69846C11.7546 2.73836 11.8434 2.78047 11.9312 2.82491C11.9412 2.82999 11.9514 2.83474 11.9613 2.83991C12.005 2.86242 12.0483 2.88583 12.0914 2.90943C12.1164 2.92313 12.1412 2.93701 12.1659 2.95108C12.2057 2.97359 12.2455 2.99606 12.2848 3.0196L12.2849 3.01942C12.4853 3.13948 12.6787 3.27276 12.8648 3.41763C12.9372 3.47393 13.0083 3.53204 13.0783 3.59193C13.0975 3.60839 13.1171 3.62433 13.1362 3.64109C13.2084 3.7045 13.2791 3.77014 13.3486 3.83742C13.5413 4.02412 13.7216 4.22359 13.8869 4.43541L14.9814 3.58111C14.7867 3.33172 14.5756 3.09568 14.3503 2.87415C14.3485 2.87231 14.3469 2.87037 14.3451 2.86859C14.3365 2.86018 14.3274 2.8524 14.3188 2.84399C14.2542 2.78122 14.1885 2.72003 14.1219 2.65998C14.1023 2.64232 14.0827 2.62444 14.063 2.60695C13.9902 2.5427 13.9163 2.48014 13.8414 2.41913C13.8145 2.39729 13.787 2.37608 13.7598 2.3546C13.7317 2.3324 13.704 2.30971 13.6755 2.28796L13.6746 2.28917C13.4567 2.12216 13.2303 1.96927 12.9968 1.8294L12.9975 1.8281C12.9438 1.79597 12.8894 1.76508 12.835 1.73432C12.8131 1.72201 12.7913 1.7097 12.7694 1.69766C12.5852 1.59638 12.3971 1.50363 12.2053 1.41923C12.1883 1.41173 12.1714 1.40432 12.1543 1.39703C11.967 1.31632 11.7763 1.24375 11.5825 1.1795C11.5505 1.16885 11.5184 1.15829 11.4863 1.14813C11.4311 1.13064 11.3756 1.11334 11.3199 1.09713C11.3166 1.09619 11.3134 1.0951 11.3101 1.09416L11.3101 1.09443C11.0482 1.01899 10.7814 0.959093 10.5107 0.913839L10.511 0.912266C10.4836 0.907637 10.4557 0.904038 10.4281 0.899682C10.3928 0.894206 10.3576 0.88867 10.3222 0.883679C10.2294 0.870611 10.1362 0.859146 10.0426 0.849708C10.0123 0.846653 9.98194 0.844535 9.95157 0.841843C9.87752 0.835369 9.80319 0.829894 9.72865 0.825629C9.69562 0.823783 9.66265 0.821666 9.62955 0.820153C9.53281 0.815979 9.4358 0.813498 9.33842 0.813135C9.32962 0.813135 9.32094 0.8125 9.31214 0.8125C9.29565 0.8125 9.27956 0.813619 9.26307 0.81371C9.1679 0.814345 9.07316 0.816584 8.9789 0.82091C8.96214 0.821666 8.94538 0.822029 8.92862 0.822936C8.84392 0.827383 8.75931 0.832949 8.67518 0.840361L8.67537 0.842297C8.40063 0.866557 8.12989 0.904522 7.86487 0.958912L7.86478 0.958458C7.75212 0.981509 7.64041 1.00816 7.52903 1.03659C7.52228 1.03838 7.51542 1.04001 7.50858 1.04177C7.03123 1.16516 6.56701 1.33707 6.12462 1.55627L6.1262 1.5595C5.88099 1.68132 5.64401 1.81702 5.41556 1.96504L5.41295 1.96099C4.89718 2.29459 4.42501 2.69383 4.00946 3.14777L4.01233 3.15046C3.82814 3.3516 3.65613 3.56386 3.49544 3.7849L3.49157 3.78212C3.12953 4.28095 2.83305 4.82424 2.61044 5.39694L2.61376 5.39825C2.515 5.65159 2.429 5.91114 2.35912 6.17746L2.35773 6.1771C2.32672 6.29559 2.29967 6.4152 2.27478 6.53524C2.27263 6.5454 2.27042 6.5555 2.2684 6.56567C2.24646 6.67369 2.22692 6.78256 2.20989 6.89225C2.20759 6.90726 2.20563 6.92244 2.20342 6.93742C2.19897 6.96767 2.19443 6.99789 2.19026 7.02823H0.652965C0.499566 7.02823 0.453948 7.12451 0.550931 7.24325L2.64311 9.8033Z"
                                    fill="#888888" />
                                <path d="M9.37308 5.81836H8.13672V8.72745L11.0458 10.5456L11.7731 9.81836L9.37308 8.27797V5.81836Z"
                                    fill="#888888" />
                            </g>
                            <defs>
                                <clipPath id="clip0_2603_650">
                                    <rect width="16" height="16" fill="white" transform="translate(0.5)" />
                                </clipPath>
                            </defs>
                        </svg> Expire in <span id="expiry-timer"><?php echo $expiry_days; ?></span> Days <?php endif; ?> &nbsp; |
                    &nbsp;
                    <?php if ($last_active == "Active"): ?>
                        <i class="fa fa-circle text-success"></i> Active
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                            <path
                                d="M8.4987 14.6663C4.8167 14.6663 1.83203 11.6817 1.83203 7.99967C1.83203 4.31767 4.8167 1.33301 8.4987 1.33301C11.484 1.33301 13.9827 3.29501 14.832 5.99967H13.1654"
                                stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M8.5 5.33301V7.99967L9.83333 9.33301M15.1367 8.66634C15.1567 8.44634 15.1667 8.22412 15.1667 7.99967M10.5 14.6663C10.7267 14.5912 10.9493 14.504 11.1667 14.405M14.36 11.333C14.4893 11.085 14.6038 10.8286 14.7033 10.5637M12.628 13.4857C12.8582 13.2955 13.0753 13.0906 13.2793 12.871"
                                stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg> Active <?php echo $last_active; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>



    <?php endwhile; ?>

      <?php
    if ($result->num_rows != 1) {
        // Pagination controls
        echo "<div class='page-links'>";

        // First page link
        if ($page > 1) {
            echo "<a class='linkForPage' id='FirstLink' data-page='1' href='javascript:void(0)'><<</a> ";
        } else {
            echo "<a class='linkForPageDisable disabled' href='javascript:void(0)'><<</a> ";
        }

        // Previous link
        if ($page <= 1) {
            echo "<a class='linkForPageDisable disabled' href='javascript:void(0)'>&lt;</a> ";
        } else {
            echo "<a class='linkForPage' id='PreviousLink' data-page='" . ($page - 1) . "' href='javascript:void(0)'>&lt;</a> ";
        }

        // Current page indicator
        echo "<p class='requirement-details mb-1 page-link-p'><strong class='pe-2'> Page " . $page . " of " . $total_pages . " </strong></p>";

        // Next link
        if ($page >= $total_pages) {
            echo "<a class='linkForPageDisable disabled' href='javascript:void(0)'>&gt;</a> ";
            echo "<a class='linkForPageDisable disabled' href='javascript:void(0)'>>></a> ";
        } else {
            echo "<a class='linkForPage' id='NextLink' data-page='" . ($page + 1) . "' href='javascript:void(0)'>&gt;</a> ";
            echo "<a class='linkForPage' id='LastLink' data-page='" . $total_pages . "' href='javascript:void(0)'>>></a> ";
        }

        echo "</div>";

    }
?>

<?php else: ?>
    <div class="alert alert-info">No candidates found matching your criteria.</div>
<?php endif; ?>

<script>
    function changeRecordsPerPage(records) {
        let filters = {
            page: 1,
            recordsPerPage: records,
            // Other existing filter parameters
            last3days: $('#last3days').is(':checked') ? 1 : 0,
            // ... other filters
        };

        $.ajax({
            url: 'fetch_accessed_candidates.php',
            type: 'GET',
            data: filters,
            success: function (response) {
                $('#candidate-list').html(response);
            }
        });
    }

    $(document).ready(function () {
        $("#dateFilter").change(function (event) {
            event.preventDefault(); // Prevents form submission

            var selectedValue = $(this).val();

            $.ajax({
                url: 'fetch_accessed_candidates.php',
                type: "GET",
                data: { duration: selectedValue },
                success: function (response) {
                    $('#candidate-list').html(response);
                    // Force selection to stay
                    $("#dateFilter option[value='" + selectedValue + "']").prop("selected", true);
                },
                error: function () {
                    alert("Error fetching data.");
                }
            });
        });
    });
</script>