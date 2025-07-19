<?php
include '../db/connection.php';
session_start();

// Get the employer id from the session
$employer_id = $_SESSION['employer_id'];

// Array to hold WHERE conditions
$whereClauses[] = "NOT EXISTS (
    SELECT 1 
    FROM order_items oi
    INNER JOIN payments p ON oi.payment_id = p.id
    WHERE oi.candidate_id = candidate_details.id 
      AND p.employer_id = {$employer_id}
      AND p.buyed_status = 0
)";

// Pagination and Limit
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $limit;

/*
 * 1. General Search Filter - IMPROVED for Sales and Gender
 */
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
//     $orConditions[] = "candidate_details.location_code IN (" . implode(',', $safeLocations) . ")";
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







/*
 * Combine all WHERE conditions.
 */
// $whereSQL = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$countQuery = "SELECT COUNT(*) as total 
               FROM candidate_details 
               JOIN candidates 
               ON candidate_details.user_id = candidates.user_id
               {$whereSQL}";

$countResult = $conn->query($countQuery);
if (!$countResult) {
    echo "Count Query Error: " . $conn->error;
    exit;
}
// $totalCandidates = $countResult->fetch_assoc()['total'];
// $totalPages = ceil($totalCandidates / $recordsPerPage);

$query = "SELECT 
    candidate_details.id, 
    candidate_details.user_id, 
    candidate_details.username, 
    candidate_details.mobile_number, 
    candidate_details.gender, 
    candidate_details.age, 
    candidate_details.current_company, 
    candidate_details.current_salary, 
    candidate_details.products, 
    candidate_details.sub_products, 
    candidate_details.departments, 
    candidate_details.sub_departments, 
    candidate_details.specialization, 
    candidate_details.category, 
    candidate_details.specialization, 
    candidate_details.work_experience, 
    candidate_details.location_code, 

    candidates.updated, 
    locations.area,
    locations.city,
    locations.state
FROM candidate_details
JOIN candidates 
    ON candidate_details.user_id = candidates.user_id

LEFT JOIN locations 
    ON candidate_details.location_code = locations.id
{$whereSQL}";

// Pagination count before limit
$resultNoLimit = $conn->query($query);
$total_pages = ceil($resultNoLimit->num_rows / $limit);

// Apply pagination
$query .= " LIMIT $start_from, $limit";

$result = $conn->query($query);
if (!$result) {
    echo "Query Error: " . $conn->error;
    exit;
}





/*
 * A helper function to mask sensitive information.
 */
function maskHalf($str)
{
    if (strlen($str) <= 2) {
        return $str;
    }
    // Return the first two characters and exactly four asterisks
    return substr($str, 0, 2) . '*********';
}


// Preserve all GET parameters for pagination links
function buildQueryString($page)
{
    $params = $_GET;
    $params['page'] = $page;
    return http_build_query($params);
}
?>

<div id="confirmationModal" class="popup-modal">
    <div class="modal-content" id="popup-modal-content">
        <div class="modal-body" id="popup-modal-body">
            <p class="popup-modal-text">Are you sure you want to view the full details?</p>
        </div>
        <div class="modal-footer" id="popup-modal-footer">
            <button type="button" id="confirmBtn" class="popup-modal-btn confirm-btn">Yes</button>
            <button type="button" id="cancelBtn" class="popup-modal-btn cancel-btn">Cancel</button>
        </div>
    </div>
</div>




<!-- HTML output for candidate cards -->
<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()):
        // echo "<pre>";
        // print_r($row);
        // echo "</pre>";

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

        // Format work experience for badge
        $workExp = htmlspecialchars($row['work_experience']) . " yrs";

        if (!isset($_SESSION['employer_id'])) {
            die("Employer not found in session.");
        }

        $employer_id = $_SESSION['employer_id'];
        $candidate_id = $row['id']; // Ensure $row is defined before using it

        // Prepare SQL statement to check if the candidate is in the cart
        $stmt = $conn->prepare("SELECT `id` FROM `user_cart` WHERE `user_id` = ? AND `candidate_id` = ?");
        if (!$stmt) {
            die("Preparation failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $employer_id, $candidate_id);
        $stmt->execute();
        $result_of_button = $stmt->get_result();
        $cart_exists = $result_of_button->num_rows > 0;

        // Format notice period/availability 



        // fetch candidates data if alreay employer buy it in orser_items table
        $stmt = $conn->prepare("SELECT * FROM `order_items` WHERE `employer_id` = ? AND `candidate_id` = ? AND `expired` = 0 AND `purchased` = 1");
        if (!$stmt) {
            die("Preparation failed: " . $conn->error);
        }
        $stmt->bind_param("ii", $employer_id, $candidate_id);
        $stmt->execute();
        $buy_candidate = $stmt->get_result();
        $buycandidate_ids = [];

        while ($row_buycandidate = $buy_candidate->fetch_assoc()) {
            $buycandidate_ids[] = $row_buycandidate['candidate_id'];
        }

        $unique_ids = array_unique($buycandidate_ids);

        ?>


        <div class="card candidate-card-landing shadow-sm mb-3 show-extra-details"
            data-candidate-id="<?php echo $row['id']; ?>">
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
                        <div class="row mb-1">
                            <div class="col-8 col-md-12 candidate-name-number">
                                <?php foreach ($unique_ids as $unique_id) {
                                    if ($row['id'] == $unique_id) { ?>
                                        <div id="unmasked-details">
                                            <span
                                                class="username-text me-1"><?php echo htmlspecialchars(ucfirst($row['username'])); ?></span>
                                            <span class="mx-2 span-line span-line-username-number">|</span>
                                            <span class="text-dark me-1 mobile-number-text">
                                                <img src="../images/ic_baseline-phone-in-talk.svg" alt="Phone Icon" width="21"
                                                    height="20">
                                                <?php echo htmlspecialchars($row['mobile_number']); ?>
                                            </span>
                                            <span class="ms-1 text-success">
                                                View full Profile
                                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22"
                                                    fill="none">
                                                    <path d="M12.4167 6.4165L17 10.9998L12.4167 15.5832" stroke="#4EA647"
                                                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M6.00065 6.4165L10.584 10.9998L6.00065 15.5832" stroke="#4EA647"
                                                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                        <?php
                                        break;
                                    }
                                } ?>
                                <?php if (!in_array($row['id'], $unique_ids)) { ?>
                                    <div id="masked-details">
                                        <span class="username-text me-1 for_buy_name_<?php echo $row['id']; ?>">
                                            <?php echo htmlspecialchars(maskHalf(ucfirst($row['username']))); ?>
                                            <img src="../images/tabler_eye-off.svg" alt="Hidden Icon" width="21" height="20">
                                        </span>
                                        <span class="mx-2 span-line span-line-username-number">|</span>
                                        <span class="text-dark me-1 mobile-number-text for_buy_mobile_<?php echo $row['id']; ?>">
                                            <img src="../images/ic_baseline-phone-in-talk.svg" alt="Phone Icon" width="21"
                                                height="20">
                                            <?php echo htmlspecialchars(maskHalf($row['mobile_number'])); ?>
                                            <img src="../images/tabler_eye-off.svg" alt="Hidden Icon" width="21" height="20">
                                        </span>
                                    </div>
                                <?php } ?>
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
                                data-original-text="<?php echo htmlspecialchars(ucfirst($row['current_company'])); ?>"><?php echo htmlspecialchars($row['current_company']); ?>
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
                                        <?php echo htmlspecialchars(mb_strimwidth($departmentStr, 0, 19, '...')); ?>
                                    </span>

                                </div>
                                <div class="mb-1">
                                    <img src="../images/mdi_cash.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                                    <span class="text-subheadd me-1">Product:</span>
                                    <span class="product-string" title="<?php echo htmlspecialchars($productStr); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($productStr, 0, 23, '...')); ?>
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

            <!-- Card Footer with Buttons and Additional Info -->
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
                <div class="d-flex align-items-center">
                    <?php
                    // Build an array for footer items so that the separator is added only between items.
                    $footerItems = array();

                    $mobile = $row['mobile_number'];
                    // Query to fetch the updated timestamp for this mobile number
                    $active_sql = "SELECT updated FROM candidates WHERE mobile_number = '$mobile'";
                    $active_result = $conn->query($active_sql);

                    // Default value if no data is found
                    $last_active = "Long Ago";
                    $footerItems = array();

                    // Check if the resume is attached
                    if (isset($row['resume']) && !empty($row['resume'])) {
                        $footerItems[] = '<div class="cv-status"><svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.15155 2.26629L8.15154 2.26629L3.18356 7.23512L3.18354 7.2351L3.18229 7.23641C2.44103 8.00508 2.03114 9.03402 2.04079 10.1018C2.05045 11.1697 2.47888 12.191 3.23392 12.9462C3.98895 13.7013 5.01025 14.1299 6.07806 14.1397C7.14588 14.1495 8.17488 13.7398 8.94367 12.9986L8.94368 12.9987L8.94497 12.9974L13.7705 8.17269L13.8412 8.10198L13.7705 8.03126L13.1771 7.43792L13.1064 7.3672L13.0357 7.43793L8.21193 12.2626C8.21178 12.2627 8.21164 12.2629 8.21149 12.263C7.63969 12.8207 6.87119 13.1306 6.07246 13.1257C5.27354 13.1208 4.50873 12.8012 3.9438 12.2363C3.37886 11.6714 3.0593 10.9065 3.05438 10.1076C3.04947 9.3089 3.35942 8.5404 3.91709 7.96859L8.88463 3.00021C8.88464 3.00021 8.88464 3.00021 8.88464 3.00021C9.05656 2.82829 9.26066 2.69192 9.48528 2.59888C9.7099 2.50584 9.95065 2.45795 10.1938 2.45795C10.4369 2.45795 10.6777 2.50584 10.9023 2.59888C11.1269 2.69192 11.331 2.82829 11.5029 3.00021C11.6748 3.17213 11.8112 3.37623 11.9043 3.60085C11.9973 3.82548 12.0452 4.06623 12.0452 4.30936C12.0452 4.55249 11.9973 4.79324 11.9043 5.01786C11.8112 5.24249 11.6748 5.44658 11.5029 5.6185L6.67825 10.4432C6.61638 10.505 6.54294 10.5541 6.46211 10.5876C6.38128 10.6211 6.29465 10.6383 6.20716 10.6383C6.11967 10.6383 6.03303 10.6211 5.9522 10.5876C5.87137 10.5541 5.79793 10.505 5.73607 10.4432C5.6742 10.3813 5.62513 10.3079 5.59165 10.227C5.55817 10.1462 5.54093 10.0596 5.54093 9.97209C5.54093 9.8846 5.55817 9.79797 5.59165 9.71714C5.62513 9.63631 5.6742 9.56287 5.73606 9.50101C5.73606 9.501 5.73606 9.501 5.73607 9.501L10.4174 4.82047L10.4882 4.74976L10.4174 4.67905L9.82409 4.0857L9.75338 4.01499L9.68267 4.08571L5.00213 8.76708L5.00213 8.76709C4.68254 9.08679 4.50304 9.52036 4.50313 9.9724C4.50321 10.4245 4.68287 10.858 5.00257 11.1775C5.32228 11.4971 5.75584 11.6766 6.20789 11.6765C6.43172 11.6765 6.65335 11.6324 6.86013 11.5467C7.0669 11.461 7.25477 11.3354 7.41301 11.1771C7.41302 11.1771 7.41302 11.1771 7.41303 11.1771L12.2377 6.35243C12.7795 5.81057 13.084 5.07566 13.084 4.30936C13.084 3.54306 12.7795 2.80814 12.2377 2.26629C11.6958 1.72443 10.9609 1.42002 10.1946 1.42002C9.42832 1.42002 8.6934 1.72443 8.15155 2.26629Z" fill="#175DA8" stroke="#175DA8" stroke-width="0.2"/>
                        </svg> CV Attached</div>';
                    }

                    // Process active status
                    $lastActive = '';
                    if ($active_result && $active_result->num_rows > 0) {
                        $active_data = $active_result->fetch_assoc();
                        $active_date = $active_data['updated'] ?? null;

                        if ($active_date) {
                            $timezone = new DateTimeZone('Asia/Kolkata');
                            $updated_time = new DateTime($active_date, $timezone);
                            $current_time = new DateTime('now', $timezone);

                            // If the updated time is in the future, fix it
                            if ($updated_time > $current_time) {
                                $updated_time = clone $current_time;
                            }

                            $time_diff = $current_time->getTimestamp() - $updated_time->getTimestamp();

                            if ($time_diff < 600) {
                                $lastActive = "Active";
                            } elseif ($time_diff < 3600) {
                                $lastActive = floor($time_diff / 60) . " mins ago";
                            } elseif ($time_diff < 86400) {
                                $lastActive = floor($time_diff / 3600) . " hrs ago";
                            } elseif ($time_diff < 604800) {
                                $lastActive = floor($time_diff / 86400) . " days ago";
                            } elseif ($time_diff < 2592000) {
                                $lastActive = floor($time_diff / 604800) . " weeks ago";
                            } else {
                                $lastActive = floor($time_diff / 2592000) . " months ago";
                            }

                            $activeIcon = '<img src="../images/hugeicons_clock-05.svg" alt="huge Icon" width="17" height="16">';
                            $footerItems[] = "<span class='active-status text-active'>{$activeIcon} {$lastActive}</span>";
                        }
                    }

                    // Join the footer items with the separator.
                    echo implode(' <span class="mx-2 span-line">|</span> ', $footerItems);
                    ?>
                    <div class="col-12 col-md-4">
                        <!-- Updated button includes data attributes -->

                        <?php    // Query to fetch records
                                $employer_mobile = mysqli_real_escape_string($conn, $_SESSION['mobile']);
                                $plan_query = "SELECT * FROM subscription_payments WHERE employer_mobile = '$employer_mobile' 
                                                                                            AND plan_status = 'ACTIVE'
                                                                                            AND status = 'success'
                                                                                            AND profile_credits_available != 0
                                                                                            AND expired != 1 ORDER BY id DESC LIMIT 1";
                                $plan_result = mysqli_query($conn, $plan_query);

                                // Check if query executed successfully
                                if (!$plan_result) {
                                    die("Query failed: " . mysqli_error($conn));
                                }

                                // Check if records exist
                                if (mysqli_num_rows($plan_result) > 0) { ?>


                            <?php if (!in_array($row['id'], $unique_ids)) { ?>
                                <button class="btn btn-sm btn-success for_buy_button_<?php echo $row['id']; ?>" id="view-number"
                                    onclick="confirmViewDetails(this)" data-candidate-id="<?php echo $row['id']; ?>"
                                    data-price="25">view Phone Number</button>

                            <?php } else { ?>
                                <button class="btn btn-sm btn-success" id="view-number" data-candidate-id="<?php echo $row['id']; ?>"
                                    data-price="25" disabled="true">Already Purchased</button>

                            <?php } ?>


                        <?php } else { ?>


                            <?php if (in_array($row['id'], $unique_ids)) { ?>
                                <button class="btn btn-sm btn-success" id="view-number" data-candidate-id="<?php echo $row['id']; ?>"
                                    data-price="25" disabled="true">Already Purchased</button>

                            <?php } else if ($cart_exists): ?>
                                    <button class="btn btn-sm add-to-cart-btn btn-success" data-candidate-id="<?php echo $candidate_id; ?>"
                                        data-price="25" onclick="goToCart()">
                                        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z"
                                                fill="white" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z"
                                                fill="white" />
                                            <path
                                                d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z"
                                                fill="white" />
                                            <path
                                                d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z"
                                                fill="white" />
                                        </svg>
                                        <!-- SVG icon here -->
                                        </svg>
                                        Go to Cart
                                    </button>
                            <?php else: ?>
                                    <button class="btn btn-sm add-to-cart-btn btn-success" data-candidate-id="<?php echo $candidate_id; ?>"
                                        data-price="25" onclick="addToCart(<?php echo $candidate_id; ?>, 25)">
                                        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z"
                                                fill="white" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z"
                                                fill="white" />
                                            <path
                                                d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z"
                                                fill="white" />
                                            <path
                                                d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z"
                                                fill="white" />
                                        </svg>
                                        <!-- SVG icon here -->
                                        </svg>
                                        Add to Cart
                                    </button>
                            <?php endif; ?>
                        <?php }
                                ?>

                    </div>
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
    // // First, log outside any event handler to confirm script execution
    // console.log('Script loaded');

    // Try multiple event listeners to see which one fires
    window.addEventListener('load', function () {
        // console.log('Window load event fired');
        runTruncationCode();
    });

    document.addEventListener('DOMContentLoaded', function () {
        // console.log('DOMContentLoaded event fired');
        runTruncationCode();
    });

    // Execute immediately to see if there's a timing issue
    (function immediateExecution() {
        // console.log('Immediate execution');
        // Check if document is already loaded
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            // console.log('Document already ready, state:', document.readyState);
            runTruncationCode();
        }
    })();

    // Main functionality separated into its own function
    function runTruncationCode() {
        // console.log('Running truncation code');

        // Error handling to catch issues
        try {
            // Function to truncate text by character count
            function truncateText(text, limit) {
                // console.log('truncateText called with:', text, limit);
                if (!text) return '';
                const trimmed = text.trim();
                if (trimmed.length <= limit) return trimmed;
                return trimmed.substring(0, limit) + '...';
            }

            // Function to check if device is mobile (by screen width)
            function isMobile() {
                const result = window.innerWidth < 768;
                // console.log('isMobile check - width:', window.innerWidth, 'result:', result);
                return result;
            }

            // Apply truncation to the specified elements
            function applyTruncation() {
                // console.log('applyTruncation function started');

                try {
                    // Select elements by class
                    const productElements = document.querySelectorAll('.product-string');
                    const companyElements = document.querySelectorAll('.current-company');

                    // console.log('Elements found - products:', productElements.length, 'companies:', companyElements.length);

                    if (productElements.length === 0 && companyElements.length === 0) {
                        console.warn('No elements found to truncate. DOM may not be ready or selectors are incorrect.');

                        // Log all available elements for debugging
                        // console.log('All elements with class attributes:', document.querySelectorAll('[class]'));
                        return;
                    }

                    const elements = [...productElements, ...companyElements];
                    const charLimit = 10;

                    // Process each element
                    elements.forEach((element, index) => {
                        // console.log(`Processing element ${index}:`, element);

                        // If we haven't stored the original text yet, do so now
                        if (!element.hasAttribute('data-full-text')) {
                            const originalText = element.getAttribute('data-original-text') || element.textContent;
                            // console.log('Original text:', originalText);
                            element.setAttribute('data-full-text', originalText);
                        }

                        const fullText = element.getAttribute('data-full-text');
                        // console.log('Full text:', fullText);
                        // 
                        // Apply truncation if on mobile
                        if (isMobile()) {
                            const truncated = truncateText(fullText, charLimit);
                            // console.log('Truncated to:', truncated);
                            element.textContent = truncated;
                        } else {
                            // console.log('Setting full text (not mobile)');
                            element.textContent = fullText;
                        }
                    });
                } catch (innerError) {
                    console.error('Error in applyTruncation:', innerError);
                }
            }


            // Apply truncation on page load
            // console.log('Calling applyTruncation');
            applyTruncation();

            // Apply truncation when window is resized
            // console.log('Adding resize event listener');
            window.addEventListener('resize', function () {
                // console.log('Resize event triggered');
                applyTruncation();
            });

        } catch (error) {
            console.error('Error in truncation code:', error);
        }
    }

    // -------- View Details --------- 
    function confirmViewDetails(button) {
        const modal = document.getElementById("confirmationModal");
        const confirmBtn = document.getElementById("confirmBtn");
        const cancelBtn = document.getElementById("cancelBtn");

        modal.style.display = "block";

        confirmBtn.onclick = function () {
            modal.style.display = "none";
            viewDetails(button); // Call actual logic
        };

        cancelBtn.onclick = function () {
            modal.style.display = "none";
        };

        // Optional: close modal when clicking outside
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    }

    function viewDetails(button) {
        var candidateId = button.getAttribute("data-candidate-id");
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "plans_logic.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // console.log("Raw response:", xhr.responseText);
                try {
                    let response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        // window.location.reload();


                        $(".for_buy_name_" + candidateId).html(response.candidate_name);
                        $(".for_buy_mobile_" + candidateId).html(
                            '<img src="../images/ic_baseline-phone-in-talk.svg" alt="Phone Icon" width="21" height="20"> ' +
                            response.candidate_mobile
                        );
                        $(".for_buy_button_" + candidateId)
                            .removeClass("btn-primary")
                            .addClass("btn-success")
                            .text("Already Purchased")
                            .prop("disabled", true);


                    } else {
                        alert(response.message || "Failed to reveal details.");
                    }
                } catch (e) {
                    console.error("Invalid JSON response:", xhr.responseText);
                    alert("Unexpected response from server.");
                }
            }
        };

        xhr.send("action=view_number&candidate_id=" + encodeURIComponent(candidateId));
    }




    function changeRecordsPerPage(records) {
        let filters = {
            page: 1,
            recordsPerPage: records,
            // Other existing filter parameters
            last3days: $('#last3days').is(':checked') ? 1 : 0,
            // ... other filters
        };

        $.ajax({
            url: 'candidate_filter.php',
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
                url: 'candidate_filter.php',
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