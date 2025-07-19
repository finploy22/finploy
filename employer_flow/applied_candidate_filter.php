<?php
// check session is there or not
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include '../db/connection.php';
// Get the employer id from the session
$employer_id = $_SESSION['employer_id'];

// Pagination and Limit
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $limit;

// Array to hold WHERE conditions
$whereClauses[] = "NOT EXISTS (
    SELECT 1 
    FROM order_items oi
    INNER JOIN payments p ON oi.payment_id = p.id
    WHERE oi.candidate_id = cd.id 
      AND p.employer_id = {$employer_id}
      AND p.buyed_status = 0
)";

/*
 * 2. General Search Filter - IMPROVED for Sales and Gender
 */
if (!empty($_GET['search'])) {
  // Get the search term and escape it
  $search = $conn->real_escape_string($_GET['search']);
  $searchLower = strtolower($search);

  // Remove everything except digits and the decimal point.
  $numericSearch = preg_replace('/[^0-9.]/', '', $search);

  if (is_numeric($numericSearch) && $numericSearch !== '') {
    // Use a LIKE comparison for numeric values
    $whereClauses[] = "(cd.current_salary LIKE '%{$numericSearch}%' 
                         OR cd.work_experience LIKE '%{$numericSearch}%')";
  } else {
    // Special case for sales search
    if ($searchLower == 'sales') {
      $whereClauses[] = "(LOWER(cd.Sales) = 'yes' 
                             OR LOWER(cd.jobrole) LIKE '%sales%' 
                             OR LOWER(cd.destination) LIKE '%sales%')";
    }
    // Special case for gender search
    else if ($searchLower == 'male' || $searchLower == 'female') {
      $whereClauses[] = "LOWER(cd.gender) = '{$searchLower}'";
    }
    // Default text search for other terms
    else {
      $searchConditions = [];
      $searchConditions[] = "LOWER(cd.username) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.mobile_number) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.employed) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.current_company) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.sales_experience) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.destination) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.current_location) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.resume) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.hl_lap) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.personal_loan) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.business_loan) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.education_loan) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.credit_cards) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.gold_loan) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.casa) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.others) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.unique_link) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.associate_id) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.associate_name) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.associate_mobile) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.associate_link) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.jobrole) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.companyname) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.location) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.Credit_dept) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.HR_Training) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.Legal_compliance_Risk) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.Operations) LIKE '%{$searchLower}%'";
      $searchConditions[] = "LOWER(cd.Others1) LIKE '%{$searchLower}%'";

      $whereClauses[] = '(' . implode(' OR ', $searchConditions) . ')';
    }
  }
}


/*
 * Combine all WHERE conditions.
 */
// Fetch and sanitize job_id from the URL
$jobId = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

if ($jobId <= 0) {
  echo "Invalid Job ID me.";
  exit;
}

// Build additional filters if any (existing logic)
$whereSQL = count($whereClauses) > 0 ? 'AND ' . implode(' AND ', $whereClauses) : '';

// Count total candidates who applied for the specific job
$countQuery = "
SELECT COUNT(*) AS total
FROM jobs_applied ja
JOIN candidate_details cd ON cd.user_id = ja.candidate_id
JOIN candidates c ON cd.user_id = c.user_id
WHERE ja.job_id = {$jobId} {$whereSQL}
";



$countResult = $conn->query($countQuery);
if (!$countResult) {
  echo "Count Query Error: " . $conn->error;
  exit;
}

// $totalCandidates = $countResult->fetch_assoc()['total'];
// $totalPages = ceil($totalCandidates / $recordsPerPage);

// Fetch paginated candidates who applied for the specific job
$query = "
SELECT 
    
    ja.id AS applied_job_id,
    ja.shortlist_status, 
    ja.reject_status, 
    ja.reject_reason,
    ja.candidate_id,


    cd.id, 
    cd.user_id, 
    cd.username, 
    cd.mobile_number, 
    cd.gender, 
    cd.age, 
    cd.current_company, 
    cd.current_salary, 
    cd.products, 
    cd.sub_products, 
    cd.departments, 
    cd.sub_departments, 
    cd.specialization, 
    cd.category, 
    cd.specialization, 
    cd.work_experience, 


     c.updated,
     l.area, l.city, l.state
    
FROM jobs_applied ja
JOIN candidate_details cd ON cd.user_id = ja.candidate_id
JOIN candidates c ON cd.user_id = c.user_id
LEFT JOIN locations l ON cd.location_code = l.id
WHERE ja.job_id = {$jobId} 
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


<!-- HTML output for candidate cards -->
<?php if ($result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <?php
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


    $mobile = $row['mobile_number'];

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


    <div class="card candidate-card-landing " data-candidate-id="<?php echo $row['id']; ?>">
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

                <button class="dotted-border-btn copy-btn" data-value="<?php echo htmlspecialchars($row['username']); ?>"
                  title="Copy">
                  <?php echo htmlspecialchars(ucfirst($row['username'])); ?>
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
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
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                    <path
                      d="M17.4069 12.9167C16.3652 12.9167 15.3652 12.75 14.4319 12.4417C14.2854 12.395 14.1289 12.389 13.9793 12.4242C13.8296 12.4594 13.6922 12.5346 13.5819 12.6417L11.7486 14.475C9.38306 13.2716 7.46029 11.3488 6.2569 8.98333L8.09023 7.14167C8.20061 7.03695 8.27883 6.90293 8.31573 6.75533C8.35263 6.60773 8.34668 6.45267 8.29857 6.30833C7.983 5.34824 7.82265 4.34395 7.82357 3.33333C7.82357 2.875 7.44857 2.5 6.99023 2.5H4.07357C3.61523 2.5 3.24023 2.875 3.24023 3.33333C3.24023 11.1583 9.5819 17.5 17.4069 17.5C17.8652 17.5 18.2402 17.125 18.2402 16.6667V13.75C18.2402 13.2917 17.8652 12.9167 17.4069 12.9167ZM16.5736 10H18.2402C18.2402 8.01088 17.4501 6.10322 16.0435 4.6967C14.637 3.29018 12.7294 2.5 10.7402 2.5V4.16667C13.9652 4.16667 16.5736 6.775 16.5736 10ZM13.2402 10H14.9069C14.9069 7.7 13.0402 5.83333 10.7402 5.83333V7.5C12.1236 7.5 13.2402 8.61667 13.2402 10Z"
                      fill="#175DA8" />
                  </svg> <?php echo htmlspecialchars($row['mobile_number']); ?>
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
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
                  <img src="../images/mingcute_department-fill.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
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
                  <img src="../images/mingcute_department-fill.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
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
                  <img src="../images/mingcute_department-fill.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
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
          <?php
          $shortlisted = $row['shortlist_status'];
          $rejected = $row['reject_status'];
          $applied_job_id = $row['applied_job_id'];
          if ($shortlisted == 1 && $rejected == 0) {
            // Shortlisted and not rejected
            ?>
            <button class="select btn-sm" id="shortlisted_btn_<?= $applied_job_id; ?>" disabled
              style="color: white !important;background-color: #4EA647 !important;">Shortlisted</button>
            <button class="modify btn-sm" id="modify_btn_<?= $applied_job_id; ?>"
              data-id="<?= $applied_job_id; ?>">Modify</button>
            <?php
          } elseif ($shortlisted == 0 && $rejected == 1) {
            // Rejected and not shortlisted
            ?>
            <button class="reject btn-sm me-2" id="rejected_btn_<?= $applied_job_id; ?>" disabled
              style="color: white !important;background-color: #ED4C5C !important;">Rejected</button>
            <button class="modify btn-sm" id="modify_btn_<?= $applied_job_id; ?>"
              data-id="<?= $applied_job_id; ?>">Modify</button>
            <?php
          } else {
            // Neither shortlisted nor rejected — show action buttons
            ?>
            <button class="reject btn-sm me-2" id="reject_btn_<?= $applied_job_id; ?>" data-id="<?= $applied_job_id; ?>"
              type="button" title="Reject Candidate">
              <img src="../images/solar_close-square-bold.svg" width="23" height="22" alt="Reject Icon"> Reject
            </button>
            <button class="select btn-sm" id="shortlist_btn_<?= $applied_job_id; ?>" data-id="<?= $applied_job_id; ?>"
              type="button" title="Shortlist Candidate">
              <img src="../images/tick-square_svgrepo.com.svg" width="23" height="22" alt="Shortlist Icon"> Shortlist
            </button>
            <?php
          }
          ?>
          <!-- Hidden buttons for dynamic JS changes -->
          <button style="display:none" class="reject btn-sm me-2" id="rejected_btn_<?= $applied_job_id; ?>" disabled
            style="color: white !important;background-color: #ED4C5C !important;">Rejected</button>
          <button style="display:none" class="select btn-sm" id="shortlisted_btn_<?= $applied_job_id; ?>" disabled
            style="color: white !important;background-color: #4EA647 !important;">Shortlisted</button>
          <button style="display:none" class="reject btn-sm me-2" id="reject_btn_<?= $applied_job_id; ?>"
            data-id="<?= $applied_job_id; ?>"><img src="../images/solar_close-square-bold.svg" width="23" height="22"
              alt="Reject Icon"> Reject</button>
          <button style="display:none" class="select btn-sm" id="shortlist_btn_<?= $applied_job_id; ?>"
            data-id="<?= $applied_job_id; ?>"> <img src="../images/tick-square_svgrepo.com.svg" width="23" height="22"
              alt="Shortlist Icon"> Shortlist</button>
          <button style="display:none" class="modify btn-sm" id="modify_btn_<?= $applied_job_id; ?>"
            data-id="<?= $applied_job_id; ?>">Modify</button>
        </div>
        <div class="text-muted small">

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
  // First, log outside any event handler to confirm script execution
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
  /////////////////////// View Details ////////////////
  function viewDetails(button) {
    // console.log('working');
    let candidateId = button.getAttribute("data-candidate-id"); // Get candidate ID from data attribute
    let maskedDetails = document.getElementById("masked-details");
    let unmaskedDetails = document.getElementById("unmasked-details");

    if (maskedDetails && unmaskedDetails) {
      // console.log('working2');
      maskedDetails.style.display = "none";  // Hide masked details
      unmaskedDetails.style.display = "block"; // Show unmasked details

      // Make AJAX request after displaying unmasked details
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "plans_logic.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // console.log("Server Response:", xhr.responseText);
        }
      };
      xhr.send("action=view_number&candidate_id=" + encodeURIComponent(candidateId));
    }
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
      url: 'applied_candidate_filter.php',
      type: 'GET',
      data: filters,
      success: function (response) {
        $('#applied-candidate-list').html(response);
      }
    });
  }

  $(document).ready(function () {
    $("#dateFilter").change(function (event) {
      event.preventDefault(); // Prevents form submission

      var selectedValue = $(this).val();
      const urlParams = new URLSearchParams(window.location.search);
      const jobId = urlParams.get('job_id');

      $.ajax({
        url: 'applied_candidate_filter.php',
        type: "GET",
        // data: { duration: selectedValue },
        data: {
          duration: selectedValue,
          job_id: jobId // Include job_id in request
        },
        success: function (response) {
          $('#applied-candidate-list').html(response);
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