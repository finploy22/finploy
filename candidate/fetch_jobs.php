<?php
include '../db/connection.php';
session_start();
// Utility Functions

function truncateText($text, $limit = 15) {
    return (strlen($text) > $limit) ? substr($text, 0, $limit) . '...' : $text;
}
function truncateName($text, $limit = 35) {
    return (strlen($text) > $limit) ? substr($text, 0, $limit) . '...' : $text;
}
function timeAgo($timestamp) {
    date_default_timezone_set('Asia/Kolkata');
    
    if (empty($timestamp) || strtotime($timestamp) === false) {
        return "-";
    }

    $timeAgo = strtotime($timestamp);
    $currentTime = time();
    $diff = $currentTime - $timeAgo;

    if ($diff < 60) return "Just now";
    elseif ($diff < 3600) return round($diff / 60) . " mins ago";
    elseif ($diff < 86400) return round($diff / 3600) . " hours ago";
    elseif ($diff < 604800) return round($diff / 86400) . " days ago";
    elseif ($diff < 2629440) return round($diff / 604800) . " weeks ago";
    elseif ($diff < 31553280) return round($diff / 2629440) . " months ago";
    else return round($diff / 31553280) . " years ago";
}

// Pagination and Limit
$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 3;
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$start_from = ($page - 1) * $limit;

// Capture filters
$filters = [
  'sort' => $_POST['sort'] ?? '',
  'location' => $_POST['location'] ?? '',
  'min_salary' => $_POST['min_salary'] ?? '',
  'max_salary' => $_POST['max_salary'] ?? '',
  'departments' => $_POST['department'] ?? [],
  'sub_departments' => $_POST['subdepartment'] ?? [],
  'categorys' => $_POST['category'] ?? [],
  'products' => $_POST['product'] ?? [],
  'sub_products' => $_POST['subproduct'] ?? [],
  'specializations' => $_POST['specialization'] ?? [],
  'designations' => $_POST['designation'] ?? [],
  'search' => $_POST['search'] ?? '',
  
  'location_m' => $_POST['location_m'] ?? '',
  'department_m' => $_POST['department_m'] ??'',
  'sub_department_m' => $_POST['subdepartment_m'] ??'',
  'category_m' => $_POST['category_m'] ?? '',
  'product_m' => $_POST['product_m'] ?? '',
  'sub_product_m' => $_POST['subproduct_m'] ??'',
  'specialization_m' => $_POST['specialization_m'] ??'',
  
];

// Start query
$query = "SELECT 
    job_id.id,
    job_id.jobrole,
    job_id.companyname,
    job_id.location,
    job_id.salary,
    job_id.department,
    job_id.age,
    job_id.gender,
    job_id.experience,
    job_id.product,
    job_id.created,
    job_id.sub_department,
    job_id.sub_product,
    job_id.specialization,
    job_id.category,
    locations.area,
    locations.city,
    locations.state
FROM 
    job_id
LEFT JOIN 
    locations ON job_id.location_code = locations.id
WHERE 
    1=1";
    
$query .= " AND job_status = 'active'";
// Apply filters
  // Apply filters
  if (!empty($filters['location']) && is_array($filters['location'])) {
    $escaped_ids = array_map(function ($id) use ($conn) {
      return (int) $id;
    }, $filters['location']);

    $id_list = implode(',', $escaped_ids);
    $city_query = "SELECT city_wise_id FROM locations WHERE id IN ($id_list)";
    $city_result = mysqli_query($conn, $city_query);


    $cities = [];
    while ($row = mysqli_fetch_assoc($city_result)) {
      $cities[] = $conn->real_escape_string($row['city_wise_id']);
    }

    $matching_ids = [];
    foreach ($cities as $city) {
      $like_query = "SELECT id FROM locations WHERE city_wise_id =   '$city'";
      $like_result = mysqli_query($conn, $like_query);

      while ($like_row = mysqli_fetch_assoc($like_result)) {
        $matching_ids[] = (int) $like_row['id'];
      }
    }
    //  echo "<pre>";
// print_r($filters['location']);
// print_r($cities);
// exit;
    // echo "<pre>";
    $all_location_ids = array_unique(array_merge($escaped_ids, $matching_ids));
    if (!empty($all_location_ids)) {
      $all_ids_list = implode(',', $matching_ids);
      // print_r($all_ids_list);
      $query .= " AND location_code IN ($all_ids_list)";
    }
  }
if (!empty($filters['min_salary'])) {
  $query .= " AND salary >= " . intval($filters['min_salary']);
}

if (!empty($filters['max_salary'])) {
  $query .= " AND salary <= " . intval($filters['max_salary']);
}

if (!empty($filters['departments']) && is_array($filters['departments'])) {
  $safe = array_map('intval', $filters['departments']);
  $query .= " AND department IN (" . implode(",", $safe) . ")";
}

if (!empty($filters['sub_departments']) && is_array($filters['sub_departments'])) {
  $safe = array_map('intval', $filters['sub_departments']);
  $query .= " AND sub_department IN (" . implode(",", $safe) . ")";
}

if (!empty($filters['categorys']) && is_array($filters['categorys'])) {
  $safe = array_map('intval', $filters['categorys']);
  $query .= " AND category IN (" . implode(",", $safe) . ")";
}

if (!empty($filters['products']) && is_array($filters['products'])) {
  $safe = array_map('intval', $filters['products']);
  $query .= " AND product IN (" . implode(",", $safe) . ")";
}

if (!empty($filters['sub_products']) && is_array($filters['sub_products'])) {
  $safe = array_map('intval', $filters['sub_products']);
  $query .= " AND sub_product IN (" . implode(",", $safe) . ")";
}
if (!empty($filters['specializations']) && is_array($filters['specializations'])) {
  $safe = array_map('intval', $filters['specializations']);
  $query .= " AND specialization IN (" . implode(",", $safe) . ")";
}

if (!empty($filters['designations']) && is_array($filters['designations'])) {
  $safe = array_map([$conn, 'real_escape_string'], $filters['designations']);
  $query .= " AND jobrole IN ('" . implode("','", $safe) . "')";
}


// Apply filter for mobile 

if (!empty($filters['location_m'])) {
    $safe = $conn->real_escape_string($filters['location_m']);
    $query .= " AND location_code = '$safe'";
}

if (!empty($filters['department_m'])) {
    $safe = $conn->real_escape_string($filters['department_m']);
    $query .= " AND department = '$safe'";
}

if (!empty($filters['sub_department_m'])) {
    $safe = $conn->real_escape_string($filters['sub_department_m']);
    $query .= " AND sub_department = '$safe'";
}

if (!empty($filters['category_m'])) {
    $safe = $conn->real_escape_string($filters['category_m']);
    $query .= " AND category = '$safe'";
}

if (!empty($filters['product_m'])) {
    $safe = $conn->real_escape_string($filters['product_m']);
    $query .= " AND product = '$safe'";
}

if (!empty($filters['sub_product_m'])) {
    $safe = $conn->real_escape_string($filters['sub_product_m']);
    $query .= " AND sub_product = '$safe'";
}

if (!empty($filters['specialization_m'])) {
    $safe = $conn->real_escape_string($filters['specialization_m']);
    $query .= " AND specialization = '$safe'";
}

// Search query (loose match)
if (!empty($filters['search'])) {
    $s = $conn->real_escape_string($filters['search']);
    $query .= " AND (
        jobrole LIKE '%$s%' OR
        department LIKE '%$s%' OR
        companyname LIKE '%$s%' OR
        location LIKE '%$s%' OR
        salary LIKE '%$s%' OR
        age LIKE '%$s%' OR
        gender LIKE '%$s%' OR
        experience LIKE '%$s%' OR
        product LIKE '%$s%' OR
        sub_product LIKE '%$s%'
    )";
}

// Sorting
switch ($filters['sort']) {
    case 'salary_desc':
        $query .= " ORDER BY salary DESC";
        break;
    case 'date_desc':
        $query .= " ORDER BY id DESC";
        break;
    default:
        $query .= " ORDER BY id DESC";
        break;
}

// Pagination count before limit
$resultNoLimit = $conn->query($query);
$total_pages = ceil($resultNoLimit->num_rows / $limit);

// Apply pagination
$query .= " LIMIT $start_from, $limit";
$result = $conn->query($query);

$jobs = [];

if ($result && $result->num_rows > 0) {
     while ($row = $result->fetch_assoc()) {
    // Extract company initials
    $jobId = htmlspecialchars($row['id']);
    $companyName = htmlspecialchars($row['companyname']);
    // Remove non-letter characters and extra spaces
    $cleanedName = preg_replace('/[^a-zA-Z\s]/', '', $companyName);
    $cleanedName = preg_replace('/\s+/', ' ', $cleanedName); // Normalize spaces
    $cleanedName = trim($cleanedName);
    // Extract initials
    $words = explode(" ", $cleanedName);
    $initials = '';
    if (isset($words[0])) {
        $initials .= strtoupper(substr($words[0], 0, 1));
    }
    if (isset($words[1])) {
        $initials .= strtoupper(substr($words[1], 0, 1));
    }

    $companyName = truncateName($companyName);
    $salary = truncateText($row['salary']);
    $location = truncateText($row['location']);
    $posted_time = timeAgo($row['created']);
    $mobileNumber = $_SESSION['mobile']??'';
    $candidate_query = "SELECT user_id FROM candidates WHERE mobile_number = '$mobileNumber'";
    $candidate_result = mysqli_query($conn, $candidate_query);
    $candidate_row = mysqli_fetch_assoc($candidate_result);
    $candidate_id = $candidate_row['user_id'] ?? null; // Handle case when no candidate is found

    // Check if candidate_id is retrieved
    if (!$candidate_id) {
      echo "Candidate not found!";
      // header("Location: ../index.php");
      exit;
    }
 
    $applied_sql = "SELECT id FROM jobs_applied WHERE job_id = '$jobId' AND candidate_id = '$candidate_id'";
    
    
    
    
    
    
       
            $dept_ids = $row['department'];
    $sub_department_ids = $row['sub_department'];
    $product_ids = $row['product'];
    $sub_product_ids = $row['sub_product'];
    $specialization_ids = $row['specialization'];
    $category_ids = $row['category'];

    // --- Departments ---
    $departments = [];

    if (!empty($dept_ids) && is_numeric($dept_ids)) {
      $department_query = "SELECT department_name FROM departments WHERE department_id = $dept_ids";
      $dept_result = mysqli_query($conn, $department_query);
      while ($dept_row = mysqli_fetch_assoc($dept_result)) {
        $departments[] = $dept_row['department_name'];
      }
    }

    // --- Sub-departments ---
    $sub_departments = [];
    if (!empty($sub_department_ids) && is_numeric($sub_department_ids)) {
      $sub_department_query = "SELECT sub_department_name FROM sub_departments WHERE sub_department_id = $sub_department_ids";
      $sub_dept_result = mysqli_query($conn, $sub_department_query);
      while ($sub_dept_row = mysqli_fetch_assoc($sub_dept_result)) {
        $sub_departments[] = $sub_dept_row['sub_department_name'];
      }
    }

    // --- Products ---
    $products = [];
    if (!empty($product_ids) && is_numeric($product_ids)) {
      $product_query = "SELECT product_name FROM products WHERE product_id = $product_ids";
      $product_result = mysqli_query($conn, $product_query);
      while ($prod_row = mysqli_fetch_assoc($product_result)) {
        $products[] = $prod_row['product_name'];
      }
    }

    // --- Sub-products ---
    $sub_products = [];
    if (!empty($sub_product_ids) && is_numeric($sub_product_ids)) {
      $sub_product_query = "SELECT sub_product_name FROM sub_products WHERE sub_product_id = $sub_product_ids";
      $sub_product_result = mysqli_query($conn, $sub_product_query);
      while ($sub_prod_row = mysqli_fetch_assoc($sub_product_result)) {
        $sub_products[] = $sub_prod_row['sub_product_name'];
      }
    }

    // --- Specialization ---
    $specialization = [];
    if (!empty($specialization_ids) && is_numeric($specialization_ids)) {
      $specialization_query = "SELECT specialization FROM products_specialization WHERE specialization_id = $specialization_ids";
      $specialization_result = mysqli_query($conn, $specialization_query);
      while ($specialization_row = mysqli_fetch_assoc($specialization_result)) {
        $specialization[] = $specialization_row['specialization'];
      }
    }

    // --- Category ---
    $categorys = [];
    if (!empty($category_ids) && is_numeric($category_ids)) {
      $category_query = "SELECT category FROM departments_category WHERE category_id = $category_ids";
      $category_result = mysqli_query($conn, $category_query);
      while ($category_row = mysqli_fetch_assoc($category_result)) {
        $categorys[] = $category_row['category'];
      }
    }
    // --- Strings ---
    $departmentStr = !empty($departments) ? implode(", ", $departments) : "-";
    $sub_departmentStr = !empty($sub_departments) ? implode(", ", $sub_departments) : "-";
    $productStr = !empty($products) ? implode(", ", $products) : "-";
    $sub_productStr = !empty($sub_products) ? implode(", ", $sub_products) : "-";
    $specializationStr = !empty($specialization) ? implode(", ", $specialization) : "-";
    $categoryStr = !empty($categorys) ? implode(", ", $categorys) : "-";

    
    
    
    
    
    
    $applied_result = mysqli_query($conn, $applied_sql);
    // Determine button status
    $button = (mysqli_num_rows($applied_result) > 0)
      ? '<button class="btn btn-success" ><img src=..//images/tick-circle_svgrepo.com.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Applied </button>'
      : '<button class="btn btn-success" id="job_apply_btn"><img src=..//images/codicon_git-stash-apply.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Apply for Job</button>';



    $jobs[] = '
    <div class="job-card mb-3" id="job-listing-web" data-id="' . $jobId . '">
        <div class="job-grid" data-id="' . $jobId . '">
            
            <!-- Column 1: Profile Logo -->
            <div class="col-1 job-logo">
                <span class="company-initials">' . $initials . '</span>
            </div>

            <!-- Column 2: Job Description -->
            <div class="col-10 job-description">
                <h5 class="job-role">' . ucfirst(htmlspecialchars($row['jobrole'])) . '</h5>
                 
                <div class="job-meta mb-1">
                   <span class="requirement-details"><img src=/images/ph_building-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;">' . ucfirst($companyName) . '</span> <span class="devider-line">|</span>
                    <span class="view-details" style="color: #4EA647;">View Full Description 
                    
                <img src=/images/iconamoon_arrow-left-2.svg  width="16" height="16" style="margin: 0 4px 4px 0;">
                    
                    </span>
                </div>
            </div>
            <!-- Column 3: Matched Job Button -->
            <div class="col-1 job-matched">
               
            </div>
        </div>
        <div class="job-info mb-2 mt-2">
            <span class="requirement-details"><img src=/images/uis_calender.svg  width="16" height="16" style="margin: 0 4px 4px 0;"><strong>Age:</strong> ' . htmlspecialchars($row['age']) . ' yrs</span> <span class="devider-line">|</span>
            <span class="requirement-details"><img src=/images/icons8_gender.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong>Gender:</strong> ' . htmlspecialchars($row['gender']) . '</span> </span> <span class="devider-line">|</span>
            <span class="requirement-details"><img src=/images/basil_bag-solid-c.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong>Exp:</strong> ' . htmlspecialchars($row['experience']) ." yrs". '</span> </span> <span class="devider-line">|</span>
            <span class="requirement-details"><img src=/images/ri_money-rupee-circle-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong></strong> ' . htmlspecialchars($row['salary']) ." LPA". '</span> </span> <span class="devider-line">|</span>
            <span class="requirement-details"><img src=/images/weui_location-filled.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong> </strong> ' . ucfirst(htmlspecialchars($row['area'] . " ," . $row['city'])) . '</span>
        </div>
        <div class="department-section row mb-2 mt-2" style="margin-bottom: -7px !important;">
           <div class="col-4">
            <div class="requirement-details mb-1 d-flex">
              <strong class="pe-2" style="white-space: nowrap;">
                <img src="/images/mingcute_department-fill.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                Department:
              </strong>
              
              <div style="padding-left: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                  title="' . htmlspecialchars($departmentStr) . '">
                ' . htmlspecialchars(mb_strimwidth($departmentStr, 0, 22, '...')) . '
              </div>
            </div>
          </div>
            <div class="col-4">
                <div class="requirement-details mb-1 d-flex">
                  <strong class="pe-2" style="white-space: nowrap;">
                    <img src="/images/mingcute_department-fill.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                    Sub-Dept:
                  </strong>
                  
                    <div style="padding-left: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                  title="' . htmlspecialchars($sub_departmentStr) . '">
                ' . htmlspecialchars(mb_strimwidth($sub_departmentStr, 0, 22, '...')) . '
              </div>
                </div>
              </div>

              <div class="col-4">
                <div class="requirement-details mb-1 d-flex">
                  <strong class="pe-2" style="white-space: nowrap;">
                    <img src="/images/mingcute_department-fill.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                    Category:
                  </strong>
                  
                    <div style="padding-left: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                  title="' . htmlspecialchars($categoryStr) . '">
                ' . htmlspecialchars(mb_strimwidth($categoryStr, 0, 22, '...')) . '
              </div>
                </div>
              </div>
        </div>
        <div class="product-section row mb-2 mt-2">
           <div class="col-4">
            <div class="requirement-details mb-1 d-flex">
              <strong class="pe-2" style="white-space: nowrap;">
                <img src="/images/mdi_cash.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                Product:
              </strong>
              
               <div style="padding-left: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                  title="' . htmlspecialchars($productStr) . '">
                ' . htmlspecialchars(mb_strimwidth($productStr, 0, 22, '...')) . '
              </div>
            </div>
          </div>

          <div class="col-4">
            <div class="requirement-details mb-1 d-flex">
              <strong class="pe-2" style="white-space: nowrap;">
                <img src="/images/mdi_cash.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                Sub-Product:
              </strong>
              
               <div style="padding-left: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                  title="' . htmlspecialchars($sub_productStr) . '">
                ' . htmlspecialchars(mb_strimwidth($sub_productStr, 0, 20, '...')) . '
              </div>
            </div>
          </div>

          <div class="col-4">
            <div class="requirement-details mb-1 d-flex">
              <strong class="pe-2" style="white-space: nowrap;">
                <img src="/images/mdi_cash.svg" width="16" height="16" style="margin: 0 4px 4px 0;">
                Specialization:
              </strong>
              
               <div style="padding-left: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;"
                  title="' . htmlspecialchars($specializationStr) . '">
                ' . htmlspecialchars(mb_strimwidth($specializationStr, 0, 22, '...')) . '
              </div>
            </div>
          </div>
        </div>
        <hr class="divider">
        <div class="job-actions">
            <div class="col-4 job-logo mt-2 ms-3">
                <p class="job-posted"><img src=/images/hugeicons_clock-05.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Job posted ' . $posted_time . '</p>
            </div>
            
            <div class="col-8 job-logo text-center">
                <button class="btn btn-outline-primary" id="candidate-as-partner"><img src=/images/basil_bag-solid.svg  width="20" height="20" style="margin: 0 4px 4px 0;"> Refer a Candidate</button>
                ' . $button . '
                <button class="btn btn-success" id="allready_apply_btn" style=display:none><img src=../images/tick-circle_svgrepo.com.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Applied </button>
            </div>
        </div>
    </div>

        
       <div class="job-card-wrapper mt-2" id="job-listing-mobile" data-id="' . $jobId . '" style="display: none;">
        <div class="job-card-container" data-id="' . $jobId . '">
            <div class="job-card-logo col-2">
                <div class="job-logo">
                    <span class="company-initials">' . $initials . '</span>
                </div>
            </div>
            <div class="job-card-details col-9">
                <h5 class="job-role pt-3">' . ucfirst(htmlspecialchars($row['jobrole'])) . '</h5>
                
            </div>
            
            <div class="job-card-matched col-1">
                    <img src=/images/tick-box_svgrepo.com.svg  width="16" height="16" style="margin: 0 4px 4px 0;">
            </div>
        </div>
        <div class="job-bio ps-3" data-id="' . $jobId . '">
            <div class="job-info mb-2">
                <span class="requirement-details"><img src=/images/uis_calender.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong>Age:</strong> ' . htmlspecialchars($row['age']) . ' yrs</span> <span class="devider-line">|</span>
                <span class="requirement-details"><img src=/images/icons8_gender.svg  width="16" height="16" style="margin: 0 4px 4px 0;"><strong>Gender:</strong> ' . htmlspecialchars($row['gender']) . '</span> </span> <span class="devider-line">|</span>
                <span class="requirement-details"><img src=/images/basil_bag-solid-c.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong>Exp:</strong> ' . htmlspecialchars($row['experience']) . '</span> </span> <span class="devider-line">|</span>
                <span class="requirement-details"><img src=/images/ri_money-rupee-circle-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong></strong> ' . htmlspecialchars($row['salary']) . '</span> </span> <span class="devider-line">|</span>
                <span class="requirement-details"><img src=/images/weui_location-filled.svg  width="16" height="16" style="margin: 0 4px 4px 0;"><strong> </strong> ' . htmlspecialchars($row['area'] . " ," . $row['city']) . '</span>
            </div>
            <div class="job-meta mb-1">
                <p class="requirement-details"><img src=/images/ph_building-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"><strong> Company  &nbsp; &nbsp; &nbsp;</strong> &nbsp; : ' . $companyName . '</p>
            </div>
            <div class="department-section">
                <p class="requirement-details mb-2"><strong class="pe-2"><img src=/images/mingcute_department-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Department </strong>  : ' . htmlspecialchars($departmentStr) . '</p>
                <p class="requirement-details mb-2"><strong class="pe-2"><img src=/images/mingcute_department-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Sub Department </strong>  : ' . htmlspecialchars($sub_departmentStr) . '</p>
                <p class="requirement-details mb-2"><strong class="pe-2"><img src=/images/mingcute_department-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Category </strong>  : ' . htmlspecialchars($categoryStr) . '</p>
                <p class="requirement-details mb-2"><strong class="pe-2"><img src=/images/mdi_cash.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Product </strong>  : ' . htmlspecialchars($productStr) . '</p>
                <p class="requirement-details mb-2"><strong class="pe-2"><img src=/images/mdi_cash.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Sub product </strong>  : ' . htmlspecialchars($sub_productStr) . '</p>
                <p class="requirement-details"><strong class="pe-2"><img src=/images/mdi_cash.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Specialization </strong> &nbsp; &nbsp; &nbsp; &nbsp; : ' . htmlspecialchars($specializationStr) . '</p>
            </div>
        </div>
        <div class="job-actions ps-3">
            <div class="">
                <button class="btn btn-outline-primary w-100 mb-2" id="candidate-to-partner"><img src=/images/basil_bag-solid.svg  width="16" height="16" style="margin: 0 4px 4px 0;">Refer a Candidate</button>
                ' . $button . '
                
                <hr class="divider mt-3">
                <div class="mt-2 ms-3">
                    <p class="job-posted text-center mb-0"><img src=/images/hugeicons_clock-05.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Job posted ' . $posted_time . '</p>
                </div>
            </div>
        </div>    
</div>
' ;
  }

    echo implode("", $jobs);

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
            
            
} else {
     echo '<div class="job-card text-center">';
  echo '    <img class="no-jobs-image" src="assets/no-jobs-found.png" alt="No Jobs Available">';
  echo '    <p class="text-danger mt-4">Currently, there are no jobs available that match your criteria.</p>';
  echo '</div>';
}
?>
