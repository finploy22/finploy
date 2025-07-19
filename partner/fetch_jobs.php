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
    // if (!$candidate_id) {
    //   echo "Candidate not found!";
    //   // header("Location: ../index.php");
    //   exit;
    // }
 
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
      ? '<button class="btn btn-outline-primary" id="apply-job-partner" ><img src=..//images/tick-circle_svgrepo.com.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Applied </button>'
      : '<button class="btn btn-outline-primary" id="apply-job-partner" id="job_apply_btn"><img src=..//images/codicon_git-stash-apply.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Apply for Job</button>';



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
              
                ' . $button . '
                <button class="btn btn-success" id="allready_apply_btn" style=display:none><img src=../images/tick-circle_svgrepo.com.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Applied </button>
                  <a href="refer_candidate.php?jobid='. $jobId .'" class="btn btn-success text-decoration-none" id="refer-candidates" style="padding: 8px !important;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M6.25856 4.96044V6.07942L4.92367 6.18817C4.46507 6.22508 4.03208 6.41469 3.69395 6.72668C3.35581 7.03868 3.13205 7.45504 3.05844 7.90919C3.02587 8.1125 2.99592 8.31607 2.9686 8.51991C2.96247 8.56922 2.97204 8.61922 2.99595 8.66279C3.01985 8.70636 3.05688 8.74129 3.10178 8.76261L3.16245 8.79098C7.44059 10.8162 12.5635 10.8162 16.8408 8.79098L16.9015 8.76261C16.9462 8.74117 16.9831 8.70619 17.0069 8.66263C17.0306 8.61907 17.0401 8.56914 17.0339 8.51991C17.0071 8.31591 16.9775 8.11232 16.9448 7.90919C16.8712 7.45504 16.6475 7.03868 16.3093 6.72668C15.9712 6.41469 15.5382 6.22508 15.0796 6.18817L13.7447 6.08021V4.96123C13.7448 4.63094 13.6263 4.31158 13.4108 4.06125C13.1954 3.81093 12.8972 3.64625 12.5706 3.59718L11.6092 3.45297C10.5434 3.29367 9.45986 3.29367 8.39408 3.45297L7.43271 3.59718C7.10621 3.64623 6.80815 3.81079 6.59269 4.06096C6.37723 4.31112 6.25867 4.63028 6.25856 4.96044ZM11.4335 4.6216C10.4842 4.47981 9.51909 4.47981 8.56981 4.6216L7.60843 4.7658C7.56179 4.77278 7.5192 4.79627 7.4884 4.83198C7.4576 4.8677 7.44063 4.91328 7.44059 4.96044V5.99668C9.14656 5.89925 10.8567 5.89925 12.5627 5.99668V4.96044C12.5626 4.91328 12.5457 4.8677 12.5149 4.83198C12.4841 4.79627 12.4415 4.77278 12.3948 4.7658L11.4335 4.6216Z" fill="white"/>
                  <path d="M17.1861 10.1685C17.1845 10.143 17.1768 10.1183 17.1636 10.0964C17.1503 10.0746 17.132 10.0563 17.1101 10.0431C17.0883 10.03 17.0635 10.0223 17.0381 10.0208C17.0126 10.0193 16.9871 10.024 16.9639 10.0345C12.5738 11.9785 7.42809 11.9785 3.03806 10.0345C3.0148 10.024 2.98934 10.0193 2.96386 10.0208C2.93838 10.0223 2.91364 10.03 2.89178 10.0431C2.86992 10.0563 2.85159 10.0746 2.83836 10.0964C2.82514 10.1183 2.81741 10.143 2.81584 10.1685C2.73603 11.6771 2.81713 13.19 3.05776 14.6814C3.13122 15.1357 3.35491 15.5523 3.69306 15.8644C4.03121 16.1766 4.46428 16.3663 4.923 16.4032L6.39816 16.5214C8.79609 16.7153 11.205 16.7153 13.6038 16.5214L15.0789 16.4032C15.5376 16.3663 15.9707 16.1766 16.3089 15.8644C16.647 15.5523 16.8707 15.1357 16.9442 14.6814C17.1853 13.1881 17.2672 11.6751 17.1861 10.1692" fill="white"/>
                </svg>
                Refer a Candidate</a>
                
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
                ' . $button . '
                <a href="refer_candidate.php?jobid='. $jobId .'" class="btn btn-success text-decoration-none" id="refer-candidates" style="padding: 8px !important;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M6.25856 4.96044V6.07942L4.92367 6.18817C4.46507 6.22508 4.03208 6.41469 3.69395 6.72668C3.35581 7.03868 3.13205 7.45504 3.05844 7.90919C3.02587 8.1125 2.99592 8.31607 2.9686 8.51991C2.96247 8.56922 2.97204 8.61922 2.99595 8.66279C3.01985 8.70636 3.05688 8.74129 3.10178 8.76261L3.16245 8.79098C7.44059 10.8162 12.5635 10.8162 16.8408 8.79098L16.9015 8.76261C16.9462 8.74117 16.9831 8.70619 17.0069 8.66263C17.0306 8.61907 17.0401 8.56914 17.0339 8.51991C17.0071 8.31591 16.9775 8.11232 16.9448 7.90919C16.8712 7.45504 16.6475 7.03868 16.3093 6.72668C15.9712 6.41469 15.5382 6.22508 15.0796 6.18817L13.7447 6.08021V4.96123C13.7448 4.63094 13.6263 4.31158 13.4108 4.06125C13.1954 3.81093 12.8972 3.64625 12.5706 3.59718L11.6092 3.45297C10.5434 3.29367 9.45986 3.29367 8.39408 3.45297L7.43271 3.59718C7.10621 3.64623 6.80815 3.81079 6.59269 4.06096C6.37723 4.31112 6.25867 4.63028 6.25856 4.96044ZM11.4335 4.6216C10.4842 4.47981 9.51909 4.47981 8.56981 4.6216L7.60843 4.7658C7.56179 4.77278 7.5192 4.79627 7.4884 4.83198C7.4576 4.8677 7.44063 4.91328 7.44059 4.96044V5.99668C9.14656 5.89925 10.8567 5.89925 12.5627 5.99668V4.96044C12.5626 4.91328 12.5457 4.8677 12.5149 4.83198C12.4841 4.79627 12.4415 4.77278 12.3948 4.7658L11.4335 4.6216Z" fill="white"/>
                  <path d="M17.1861 10.1685C17.1845 10.143 17.1768 10.1183 17.1636 10.0964C17.1503 10.0746 17.132 10.0563 17.1101 10.0431C17.0883 10.03 17.0635 10.0223 17.0381 10.0208C17.0126 10.0193 16.9871 10.024 16.9639 10.0345C12.5738 11.9785 7.42809 11.9785 3.03806 10.0345C3.0148 10.024 2.98934 10.0193 2.96386 10.0208C2.93838 10.0223 2.91364 10.03 2.89178 10.0431C2.86992 10.0563 2.85159 10.0746 2.83836 10.0964C2.82514 10.1183 2.81741 10.143 2.81584 10.1685C2.73603 11.6771 2.81713 13.19 3.05776 14.6814C3.13122 15.1357 3.35491 15.5523 3.69306 15.8644C4.03121 16.1766 4.46428 16.3663 4.923 16.4032L6.39816 16.5214C8.79609 16.7153 11.205 16.7153 13.6038 16.5214L15.0789 16.4032C15.5376 16.3663 15.9707 16.1766 16.3089 15.8644C16.647 15.5523 16.8707 15.1357 16.9442 14.6814C17.1853 13.1881 17.2672 11.6751 17.1861 10.1692" fill="white"/>
                </svg>
                Refer a Candidate</a>
                
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
