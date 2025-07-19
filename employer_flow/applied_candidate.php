<?php
// Start session and check login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    header("Location: ../index.php");
    exit();
}
$employer_name = $_SESSION['name'] ?? '';
$employer_mobile = $_SESSION['mobile'] ?? '';

include 'posting_header.php';
include '../db/connection.php';

if ($employer_mobile) {
    $stmt = $conn->prepare("SELECT employer_status FROM employer_add_details WHERE employer_mobile_number = ?");
    $stmt->bind_param("i", $employer_mobile);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();
    // if ($status !== 'verified') {
    //     header("Location: index.php");
    //     exit();
    // }
} else {
    header("Location: index.php");
    exit();
}
// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT `id`, `username`, `mobile_number`, `password`, `created` 
                        FROM `employers` 
                        WHERE mobile_number = ? 
                        LIMIT 1");
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}
$stmt->bind_param("s", $employer_mobile);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['employer_id'] = $row['id'];

} else {
    echo "No employers found.";
}
$employer_id = $_SESSION['employer_id'];
// Main query to fetch all candidate data (pagination removed)
$query = "
     SELECT 
         cd.id, 
         cd.user_id, 
         cd.username, 
         cd.mobile_number, 
         cd.gender, 
         cd.employed, 
         cd.current_company, 
         cd.destination, 
         cd.sales_experience, 
         cd.work_experience, 
         cd.current_location, 
         cd.current_salary, 
         cd.`hl_lap`, 
         cd.personal_loan, 
         cd.business_loan, 
         cd.education_loan, 
         cd.gold_loan, 
         cd.credit_cards, 
         cd.casa, 
         cd.others, 
         cd.resume,
         cd.created,
         cd.modified
     FROM candidate_details cd
     WHERE NOT EXISTS (
         SELECT 1 
         FROM order_items oi
         INNER JOIN payments p ON oi.payment_id = p.id
         WHERE oi.candidate_id = cd.id 
           AND p.employer_id = {$employer_id}
           AND p.buyed_status = 0
     )
 ";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}
$cartCandidateIds = isset($_SESSION['cart']) ? array_column($_SESSION['cart'], 'candidate_id') : [];
function maskHalf($str)
{
    $length = strlen($str);
    $half = floor($length / 2);
    return substr($str, 0, $half) . str_repeat('x', $length - $half);
}
$expiredNotifications = [];
$expiredQuery = "
    SELECT 
        cd.username AS candidate_name,
        p.employer_username AS employer_name,
        p.created_at AS purchase_date,
        p.expired
    FROM candidate_details cd
    INNER JOIN order_items oi ON cd.id = oi.candidate_id
    INNER JOIN payments p ON oi.payment_id = p.id
    WHERE p.employer_id = {$employer_id}
      AND p.expired IN (8, 6, 4, 2)
    ORDER BY p.created_at DESC
    LIMIT 4
";
$expiredResult = $conn->query($expiredQuery);
if ($expiredResult) {
    while ($row = $expiredResult->fetch_assoc()) {
        $employerName = htmlspecialchars($row['employer_name']);
        $candidateName = htmlspecialchars($row['candidate_name']);
        $purchaseDate = date("M d, Y, h:i:s A", strtotime($row['purchase_date']));
        $expiredMinutes = (int) $row['expired'];
        $message = "";

        switch ($expiredMinutes) {
            case 8:
                $message = "{$employerName}, your selected candidate {$candidateName} will expire in 8 minutes on {$purchaseDate}.";
                break;
            case 6:
                $message = "{$employerName}, your selected candidate {$candidateName} will expire in 6 minutes on {$purchaseDate}.";
                break;
            case 4:
                $message = "{$employerName}, your selected candidate {$candidateName} will expire in 4 minutes on {$purchaseDate}.";
                break;
            case 2:
                $message = "{$employerName}, your selected candidate {$candidateName} will expire in 2 minutes on {$purchaseDate}.";
                break;
            default:
                $message = "{$employerName}, the expiration status for candidate {$candidateName} is unknown.";
                break;
        }
        $expiredNotifications[] = [
            'message' => $message,
            'username' => $candidateName,
            'purchase_date' => $row['purchase_date']
        ];
    }
}
$notificationCount = count($expiredNotifications);
$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
$stmt = $conn->prepare("
   SELECT 
    job_id.jobrole, 
    job_id.job_status, 
    locations.area, 
    locations.city, 
    locations.state
FROM job_id
LEFT JOIN locations ON job_id.location_code = locations.id
WHERE job_id.id = ?
LIMIT 1

");
if (! $stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $job_id);
if (! $stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}
$result = $stmt->get_result();

$job = $result->fetch_assoc();
//
$stmt = $conn->prepare("
    SELECT COUNT(*) AS job_count
    FROM jobs_applied     
    WHERE job_id = ? 
");
$stmt->bind_param('i', $job_id);
if (! $stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}
$result = $stmt->get_result();
$job_count = $result->fetch_assoc()['job_count'];





function fetchTable($conn, $table)
{
    $result = $conn->query("SELECT * FROM {$table}");
    return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$row_products = fetchTable($conn, 'products');
$row_sub_products = fetchTable($conn, 'sub_products');
$row_products_specialization = fetchTable($conn, 'products_specialization');
$row_departments = fetchTable($conn, 'departments');
$row_sub_departments = fetchTable($conn, 'sub_departments');
$row_departments_category = fetchTable($conn, 'departments_category');

$sql = "SELECT id,area,city FROM locations ORDER BY city ,area";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_locations = $result->fetch_all(MYSQLI_ASSOC);
}
?>

 <!-- Select2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <link rel="stylesheet" href="./css/applied_candidate.css">
<body>
  

 
    <section class="job-header-wrapper desresappjob">
      <div class="container job-header-content">
        <div class="job-info">
            <h1 class="job-title"><?= ucfirst($job['jobrole']) ?></h1>
            <span class="job-status"><?= ucfirst($job['job_status']) ?></span>
            <span class="location">
                <i class="icon-location"></i><?= ucfirst($job['area']." ,".$job['city']) ?>
            </span>
            <a href="#" class="details-link">More Details<span class="arrow"></span></a>
        </div>

        <div class="job-actions">
            <button class="edit-btn">Edit Job</button>
            <button class="icon-btn">
            <a href="update_posting_job.php?id=<?= $job_id ?>"><img src="assets/fluent_edit-24-filled.svg" alt="Edit" /></a>
            </button>
            <button class="icon-btn">
                <img src="assets/material-symbols_share.svg" alt="Copy" />
            </button>
            <button class="icon-btn dots">
                <img src="assets/iwwa_option.svg" alt="Copy" />
            </button>
        </div>
  </div>
    </section>
    <section class="job-header-wrapper mobresappjob">
        <div class="job-info">
            <h1 class="job-title"><?= ucfirst($job['jobrole']) ?></h1>
            <span class="job-status"><?= ucfirst($job['job_status']) ?></span>

        </div>
        <div class="location">
            <i class="icon-location"></i><?= ucfirst($job['area']." ,".$job['city']) ?>
        </div>

        <div class="job-actions">
            <div class="editbtns">
                <button class="edit-btn">Edit Job</button>
                <button class="icon-btn">
                <a href="edit_posting_job.php?job_id=<?= $job_id ?>"><img src="assets/fluent_edit-24-filled.svg" alt="Edit" /></a>
                </button>
                <button class="icon-btn">
                    <img src="assets/material-symbols_share.svg" alt="Copy" />
                </button>
                <button class="icon-btn dots">
                    <img src="assets/iwwa_option.svg" alt="Copy" />
                </button>
            </div>
            <div class="morebtn"><a href="#" class="details-link">More Details<span class="arrow"></span></a></div>
        </div>

    </section>
   
    <!-- <div class="container alljoblink">
        <a href="employer_joblisting_page.php"><span class="jobdet-link"></span>All Jobs >&nbsp;</a>
        <div class="roletit"><?= ucfirst($job['jobrole']) ?></div>
    </div> -->

  <!-- Main Content -->
  <div class="container mt-4 appjobcan">

    <div class=" overall-container">
      <div class="row mb-3 ">
        <!-- search and heding div -->
        <div class="job-liting-page" style="margin-top:8px">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h5 class="jobs-page-title">Showing Candidates</h5>
            <div class="search-container" id="search-container-web">
              <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                <path
                  d="M16.5674 16.1255L20.4141 20M18.6363 11.1111C18.6363 15.0385 15.4526 18.2222 11.5252 18.2222C7.59781 18.2222 4.41406 15.0385 4.41406 11.1111C4.41406 7.18375 7.59781 4 11.5252 4C15.4526 4 18.6363 7.18375 18.6363 11.1111Z"
                  stroke="#888888" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <input type="text" id="search" class="form-control" placeholder="Search for ' location '"
                autocomplete="off">
            </div>
            <div class="search-container p-2" id="search-icon-mobile">
              <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                <path
                  d="M16.5674 16.1255L20.4141 20M18.6363 11.1111C18.6363 15.0385 15.4526 18.2222 11.5252 18.2222C7.59781 18.2222 4.41406 15.0385 4.41406 11.1111C4.41406 7.18375 7.59781 4 11.5252 4C15.4526 4 18.6363 7.18375 18.6363 11.1111Z"
                  stroke="#888888" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
          </div>
        </div>
     
        <!-- Filters Section -->
        <div class="col-md-3 filters-section" id="filters-section-web">
          <h5 class="filter-header"><img src="../assets/filter-icon.svg" alt="">All Filters <a href="/index.php"
              class="clear-all-filter" style="display:none">Clear All</a></h5>
          <hr class="filter-line">
          <div id="keyword-badges-applied" class="mb-2 keyword-badges-filter"></div>
          <div class="mb-3 filter-div-sort">
            <h6 class="filter-accordion">Applied in <img class="accordion-arrow" src="../assets/downward-arrow.svg"
                alt="Toggle Arrow"></h6>
            <div class="filter-content ">

              <?php
              $filters = [
                '3d' => 'Last 3 days',
                '7d' => 'Last 7 days',
                '15' => 'Last 15 days',
                '1m' => '1 Month ago',
                '3m' => '3 Month ago',
                '6m' => '6 Month ago',
                '1y' => '1 Year ago',
                '1ys' => '1+ Years ago',
              ];
              ?>

              <div id="keyword-badges-applied" class="mb-2"></div>
              <?php foreach ($filters as $id => $label): ?>
                <div class="form-check">
                  <input class="form-check-input applied-input" type="checkbox" id="<?= $id ?>" name="applied"
                    value="<?= $id; ?>" data-name="<?= $label; ?>">
                  <label class="form-check-label" for="<?= $id ?>"><?= $label ?></label>
                </div>
              <?php endforeach; ?>

            </div>
          </div>
          <hr class="filter-line">












          <!-- Must Have Keyword Filter Group -->


          <div id="keyword-badges-musthave" class="mb-2"></div>
          <div class=" mb-3 filter-div">
            <h6 class="filter-accordion">Must Have Keyword </h6>
            <div class="custom-checkbox">

              <input type="text" class="form-control" id="Havekeyword" placeholder="Key words" name="Havekeyword"
                autocomplete="off">

              <!-- Suggestions dropdown -->
              <ul id="keywordSuggestions" class="suggestions-list" style="display:none; list-style: none; 
                                                 font-size: 13px;
                                                margin: 0 auto;
                                                padding: 0;
                                                border: 1px solid #ccc;
                                                border-right: 0px solid !important;
                                                border-left: 0px solid !important;
                                                border-radius: 0 0 8px 8px;
                                                background-color:white;
                                                width: 97%;
                                                max-height: 200px;
                                                overflow-y: auto;
                                                z-index: 10;
                                                box-shadow: 2px 0px 7.467px 0px rgba(108, 99, 99, 0.20);
                                            ">
              </ul>
            </div>
          </div>
          <hr class="filter-line">












          <!-- Department Filter (Updated) -->
          <div id="keyword-badges-department" class="mb-2 keyword-badges-filter"></div>
          <div class="mb-3 filter-div">
            <h6 class="filter-accordion" id="department-list-head">Department <img class="accordion-arrow"
                src="../assets/downward-arrow.svg" alt="Toggle Arrow"></h6>

            <div class="filter-content" id="department-list-div"
              style="">
              <?php if ($row_departments) {
                foreach ($row_departments as $department) { ?>
                  <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                    <input type="checkbox" class="checkbox-input" name="department"
                      value="<?= $department['department_id']; ?>" data-name="<?= $department['department_name']; ?>">
                    <label class="filter-lable checkbox-label"><?= $department['department_name']; ?></label>
                  </div>
                <?php }
              }
              ?>
            </div>
            <div class="custom-checkbox">
              <input id="departmentsearch" class="form-control" type="search" name="departmentsearch"
                placeholder="Search Department" aria-label="Search">
            </div>
          </div>

          <style>
            .checkbox-item {
              display: flex;
              align-items: flex-start;
              gap: 8px;
              margin-bottom: 6px;
            }

            .checkbox-input {
              margin-top: 3px;
            }

            .checkbox-label {
              white-space: normal;
              word-break: break-word;
              line-height: 17px;
            }
          </style>

          <hr class="filter-line">
          <!-- SubDepartment Filter (Updated) -->
          <div id="keyword-badges-subdepartment" class="mb-2 keyword-badges-filter"></div>
          <div class="mb-3 filter-div">
            <h6 class="filter-accordion subdepart-head">Sub-Department <img class="accordion-arrow"
                src="../assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
            <div class="filter-content" id="subdepartment-list-div"
              style="">
              <?php if ($row_sub_departments) {
                foreach ($row_sub_departments as $sub_departments) { ?>
                  <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                    <input type="checkbox" class="checkbox-input" name="subdepartment"
                      value="<?= $sub_departments['sub_department_id']; ?>"
                      data-name="<?= $sub_departments['sub_department_name']; ?>">
                    <label class="filter-lable checkbox-label"><?= $sub_departments['sub_department_name']; ?></label>
                  </div>
                <?php }
              }
              ?>
            </div>
            <div class="custom-checkbox">
              <input id="subdepartmentsearch" class="form-control" type="search" name="subdepartmentsearch"
                placeholder="Search Sub-Department" aria-label="Search">
            </div>
          </div>
          <hr class="filter-line">
          <!-- category Filter (Updated) -->
          <div id="keyword-badges-category" class="mb-2"></div>
          <div class="mb-3 filter-div">
            <h6 class="filter-accordion cate-head">Category <img class="accordion-arrow"
                src="../assets/downward-arrow.svg" alt="Toggle Arrow"></h6>

            <div class="filter-content" id="category-list-div"
              style="">

              <?php if ($row_departments_category) {
                foreach ($row_departments_category as $category) { ?>
                  <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                    <input type="checkbox" class="checkbox-input" name="category" value="<?= $category['category_id']; ?>"
                      data-name="<?= $category['category']; ?>">
                    <label class="filter-lable checkbox-label"><?= $category['category']; ?></label>
                  </div>
                <?php }
              }
              ?>
            </div>
            <div id="category-searchlist-div" style="display: none;"></div>
            <div class="custom-checkbox">
              <input id="categorysearch" class="form-control" type="search" name="categorysearch"
                placeholder="Search Category" aria-label="Search">
            </div>
          </div>
          <hr class="filter-line">
          <!-- product Filter (Updated) -->
          <div id="keyword-badges-product" class="mb-2"></div>
          <div class="mb-3 filter-div">
            <h6 class="filter-accordion product-head">Product <img class="accordion-arrow"
                src="../assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
            <div class="filter-content" id="product-list-div"
              style="">
              <?php if ($row_products) {
                foreach ($row_products as $product) { ?>
                  <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                    <input type="checkbox" class="checkbox-input" name="product" value="<?= $product['product_id']; ?>"
                      data-name="<?= $product['product_name']; ?>">
                    <label class="filter-lable checkbox-label"><?= $product['product_name']; ?></label>
                  </div>
                <?php }
              }
              ?>
            </div>
            <div id="product-searchlist-div" style="display: none;"></div>
            <div class="custom-checkbox">
              <input id="productsearch" class="form-control" type="search" name="productsearch"
                placeholder="Search Category" aria-label="Search">
            </div>
          </div>
          <hr class="filter-line">
          <!-- subproduct Filter (Updated) -->
          <div id="keyword-badges-subproduct" class="mb-2"></div>
          <div class="mb-3 filter-div">
            <h6 class="filter-accordion subpro-head">Sub-Product <img class="accordion-arrow"
                src="../assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
            <div class="filter-content" id="subproduct-list-div"
              style="">
              <?php if ($row_sub_products) {
                foreach ($row_sub_products as $sub_product) { ?>
                  <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                    <input type="checkbox" class="checkbox-input" name="subproduct"
                      value="<?= $sub_product['sub_product_id']; ?>" data-name="<?= $sub_product['sub_product_name']; ?>">
                    <label class="filter-lable checkbox-label"><?= $sub_product['sub_product_name']; ?></label>
                  </div>
                <?php }
              }
              ?>
            </div>
            <div id="subproduct-searchlist-div" style="display: none;"></div>
            <div class="custom-checkbox">

              <input id="subproductsearch" class="form-control" type="search" name="subproductsearch"
                placeholder="Search Sub-Product" aria-label="Search">
            </div>
          </div>
          <hr class="filter-line">
          <!-- specialization Filter (Updated) -->
          <div id="keyword-badges-specialization" class="mb-2"></div>
          <div class="mb-3 filter-div">
            <h6 class="filter-accordion specia-head">Specialization <img class="accordion-arrow"
                src="../assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
            <div class="filter-content" id="specialization-list-div"
              style="">
              <?php if ($row_products_specialization) {
                foreach ($row_products_specialization as $specialization) { ?>
                  <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                    <input type="checkbox" class="checkbox-input" name="specialization"
                      value="<?= $specialization['specialization_id']; ?>"
                      data-name="<?= $specialization['specialization']; ?>">
                    <label class="filter-lable checkbox-label"><?= $specialization['specialization']; ?></label>
                  </div>
                <?php }
              }
              ?>
            </div>
            <div id="specialization-searchlist-div" style="display: none;"></div>
            <div class="custom-checkbox">
              <input id="specializationsearch" class="form-control" type="search" name="specializationsearch"
                placeholder="Search Specialization" aria-label="Search">
            </div>
          </div>
          <hr class="filter-line">
          <!-- Designation Filter Group -->
          <div id="keyword-badges-designation" class="mb-2"></div>
          <div class="mb-3 filter-div">
            <h6 class="filter-accordion">Designation </h6>
            <div class="custom-checkbox">
              <input type="text" class="form-control " id="search_designation" placeholder="Search Designation"
                name="designation" autocomplete="off">
            </div>
            <div id="select-designation-div" style="display: none;"></div>
          </div>
          <hr class="filter-line">
          <!-- Location Filter (Updated) -->
          <div class=" mb-3 filter-div">
            <h6 class="filter-accordion">Location </h6>
            <div class="custom-checkbox">
              <div id="keyword-badges-location" class="mb-2"></div>
              <input type="text" class="form-control" id="search_location" placeholder="Search Location" name="location"
                autocomplete="off">
              <input type="hidden" id="location_id" name="location_id">
              <div id="select-div" style="display: none;"></div>
            </div>
          </div>
          <hr class="filter-line">
          <!-- Salary Filter (Existing) -->
          <div class="mb-3 filter-div">
            <h6>Salary (In LPA)</h6>
            <div class="input-group">
              <input type="number" id="min_salary" class="form-control" placeholder="Min">
              <input type="number" id="max_salary" class="form-control" placeholder="Max">
            </div>
          </div>
        </div>

        <!-- FIlters for Mobile View -->
        <div class="filters-section d-flex align-items-center overflow-auto" style="white-space: nowrap; gap: 1.5rem;"
          id="filters-section-mobile">
          <!-- Filter Button -->
          <button class="btn btn-outline-primary d-flex align-items-center" style="width: 46px; height: 35px;">
            <img src="../assets/filter-icon.svg" alt="Filter Icon" class="me-2" style="width: 20px;">
          </button>
          <!-- <script>
                function scrollToFiltersBottom() {
                    const filters = document.getElementById('filters-section-web');

                    if (filters) {
                        // Remove hiding class if exists
                        filters.classList.remove('d-none'); 
                        filters.style.display = 'block';
                        filters.scrollTo({
                            top: filters.scrollHeight,
                            behavior: 'smooth'
                        });
                    }
                }
                </script> -->
          <!-- <style>
                    #filters-section-web {
                        /* display: block !important; */
                        max-height: 100vh; /* or any suitable height */
                        overflow-y: auto;
                    }
                </style> -->

          <!-- Sort By Dropdown -->
          <select id="jobsort" class="form-select" style="width: 92px; height: 35px;">
            <option value="">Sort By</option>
            <option value="relevance">Relevance</option>
            <option value="salary_desc">Salary - High to Low</option>
            <option value="date_desc">Date Posted - New to Old</option>
          </select>
          <!-- Department Dropdown -->
          <?php if ($row_departments) { ?>
            <select id="jobdepartment" class="form-select" style="width: 122px; height: 35px;">
              <option value="">Department</option>
              <?php foreach ($row_departments as $department) { ?>
                <option value="<?= $department['department_id']; ?>"><?= $department['department_name']; ?></option>
              <?php } ?>
            </select>
          <?php } ?>
          <!-- Sub-Department Dropdown (Mobile) -->
          <?php if ($row_sub_departments) { ?>
            <select id="job-sub-department" class="form-select" style="width: 150px; height: 35px;">
              <option value="">Sub Departments</option>
              <?php foreach ($row_sub_departments as $sub_department) { ?>
                <option value="<?= $sub_department['sub_department_id']; ?>">
                  <?= $sub_department['sub_department_name']; ?>
                </option>
              <?php } ?>
            </select>
          <?php } ?>
          <!-- Category (Mobile) -->
          <?php if ($row_departments_category) { ?>
            <select id="job-category" class="form-select" style="width: 150px; height: 35px;">
              <option value="">Category</option>
              <?php foreach ($row_departments_category as $category) { ?>
                <option value="<?= $category['category_id']; ?>"><?= $category['category']; ?></option>
              <?php } ?>
            </select>
          <?php } ?>
          <!-- Product Dropdown (Mobile) -->
          <?php if ($row_products) { ?>
            <select id="job-product" class="form-select" style="width: 120px; height: 35px;">
              <option value="">Product</option>
              <?php foreach ($row_products as $product) { ?>
                <option value="<?= $product['product_id']; ?>"><?= $product['product_name']; ?></option>
              <?php } ?>
            </select>
          <?php } ?>
          <!-- Sub-Product Dropdown (Mobile) -->
          <?php if ($row_sub_products) { ?>
            <select id="job-sub-product" class="form-select" style="width: 120px; height: 35px;">
              <option value="">Sub Product</option>
              <?php foreach ($row_sub_products as $sub_product) { ?>
                <option value="<?= $sub_product['sub_product_id']; ?>"><?= $sub_product['sub_product_name']; ?>
                </option>
              <?php } ?>
            </select>
          <?php } ?>
          <!-- Specialization (Mobile) -->
          <?php if ($row_products_specialization) { ?>
            <select id="job-specialization" class="form-select" style="width: 120px; height: 35px;">
              <option value="">Specialization</option>
              <?php foreach ($row_products_specialization as $specialization) { ?>
                <option value="<?= $specialization['specialization_id']; ?>">
                  <?= $specialization['specialization']; ?>
                </option>
              <?php } ?>
            </select>
          <?php } ?>
          <!-- Location Dropdown -->
          <?php if ($row_locations) { ?>
            <select id="joblocation" class="form-select" style="width: 120px; height: 35px;">
              <option value="">Location</option>
              <?php foreach ($row_locations as $location) { ?>
                <option value="<?= $location['id']; ?>"><?= $location['area'] . ',' . $location['city']; ?></option>
              <?php } ?>
            </select>
          <?php } ?>
          <!-- Min Salary Input -->
          <input type="number" id="jobmin_salary" class="form-control" placeholder="Min Salary"
            style="width: 120px; height: 35px;">
          <!-- Max Salary Input -->
          <input type="number" id="jobmax_salary" class="form-control" placeholder="Max Salary"
            style="width: 120px; height: 35px;">
        </div>

        <!-- Candidate List Column -->
        <div class="col-md-9">

          <!-- Container where candidate cards will be loaded -->
          <!-- The ajax loader with the circle loader design -->
          <div class="ajax-loader" id="ajaxLoader" style="display: none;">
            <div class="ajax-loader-conatainer">
              <!-- New circle loader replacing the spinner with bouncing dots -->
              <div class="circle-loader"></div>

            </div>
          </div>
          <div id="applied-candidate-list">
            <!-- Candidate cards go here -->
          </div>
              <!-- For Pagination -->
          <div class="pagination-div" style="display: none;">
            <div class="per-page">
              <p class="requirement-details mb-1 per-page-p">
                <strong class="pe-2"> Showing
                  <select id="per-page-list" class="per-page-list classic">
                    <?php
                    $options = [5, 10, 20, 50, 100, 250];
                    foreach ($options as $option) {
                      $selected = ($option == 5) ? "selected" : "";
                      echo "<option value='$option' $selected>$option</option>";
                    }
                    ?>
                  </select> per page
                </strong>
              </p>
            </div>
          </div>
          <!-- Pagination End  -->
        </div>
      </div>
    </div>
  </div>



  <!--//feedback for reject-->
<div id="feedback-popup" style="display: none; position: fixed; top: 20%; left: 50%; transform: translateX(-50%);
  background: #fff; border: 1px solid #ccc; padding: 20px; width: 300px; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
  <h5>Give Feedback</h5>
  <textarea id="feedback-text" rows="4" style="width: 100%;" placeholder="Enter reason..."></textarea>
  <input type="hidden" id="popup-job-id">
  <br><br>
  <button id="submit-feedback" class="btn btn-primary btn-sm">Submit</button>
  <button id="cancel-feedback" class="btn btn-secondary btn-sm">Cancel</button>
</div>
<!--// conformtion for shortlist-->
<div id="confirmation-popup" style="display: none;">
  <div id="confirmation-popup-overlay"></div>
  <div id="confirmation-popup-box">
    <p>Are you sure you want to shortlist this candidate?</p>
    <button id="confirmation-confirm-shortlist" data-id="">Yes</button>
    <button id="confirmation-cancel-popup">No</button>
  </div>
</div>

<!--// confirmation for modify-->
<div id="modify-confirmation-popup" style="display: none;">
  <div class="modify-popup-overlay"></div>
  <div class="modify-popup-box">
    <p>Are you sure you want to modify this candidate's status?</p>
    <button id="modify-confirm-modify" data-id="">Yes</button>
    <button class="modify-cancel-popup">No</button>
  </div>
</div>


  <!-- Extra candidate Details  -->
  <div class="candidate-details-Popup">
    <span class="close-candidate-poup outter-close-btn" title="Close"><img src="../images/cross-sign-index-smu.svg"
        alt="x"></span>
    <div class="modal-content">
      <span class="close-candidate-poup inner-close-btn" title="Close"><img src="../images/cross-sign-index-smu.svg"
          alt="x"></span>
      <div class="modal-body">
      </div>
    </div>
  </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!--<script src="employer.js"></script>-->
     <script src="../new-js/employer.js"></script>
    <script>
        // $(document).ready(function () {
        //     $('.searchable-location').select2({
        //         tags: true,
        //         placeholder: "Select or enter a city",
        //         allowClear: true
        //     });
        // });
    </script>
    <script>
        const keywordInput = document.getElementById('Havekeyword');
        const suggestionBox = document.getElementById('keywordSuggestions');
        const filterForm = document.getElementById('filter-form');
        const submitBtn = document.getElementById('submitKeyword');
        keywordInput.addEventListener('input', function () {
            const input = this.value.toLowerCase();
            let hasMatch = false;
            suggestionBox.querySelectorAll('li').forEach(item => {
                const match = item.textContent.toLowerCase().includes(input);
                item.style.display = match ? 'block' : 'none';
                if (match) hasMatch = true;
            });
            suggestionBox.style.display = hasMatch ? 'block' : 'none';
        });
        suggestionBox.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', function () {
                keywordInput.value = this.textContent;
                suggestionBox.style.display = 'none';
                filterForm.submit();
            });
        });
        keywordInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                suggestionBox.style.display = 'none';
                filterForm.submit();
            }
        });
        // submitBtn.addEventListener('click', function () {
        //     filterForm.submit();
        // });
        document.addEventListener('click', function (e) {
            if (!suggestionBox.contains(e.target) && e.target !== keywordInput) {
                suggestionBox.style.display = 'none';
            }
        });
    </script>
    <script>
        // Wait for the DOM to be fully loaded
        // document.addEventListener('DOMContentLoaded', function () {
        //     const filtersCard = document.querySelector('.d-block.d-md-none.mb-3');
        //     if (filtersCard) {
        //         filtersCard.addEventListener('click', () => {
        //             const modal = new bootstrap.Modal(document.getElementById('filtersModal'));
        //             modal.show();
        //         });
        //     } else {
        //         console.error('Filter card element not found');
        //     }
        // });

        //////////////////////////////// Available Credits //////////////////////
        // document.getElementById("available-credits").addEventListener("click", function (event) {
        //     event.stopPropagation(); // Prevent closing immediately
        //     let dropdown = document.getElementById("credits-dropdown"); 
        //     if (!dropdown) {
        //         fetch("available_credits.php")
        //             .then(response => response.text())
        //             .then(html => {
        //                 document.getElementById("credits-container").innerHTML = html; // Load the popup
        //                 showDropdown();
        //             });
        //     } else {
        //         showDropdown();
        //     }
        // });

        // function showDropdown() {
        //     let dropdown = document.getElementById("credits-dropdown");
        //     let button = document.getElementById("available-credits");
        //     let rect = button.getBoundingClientRect();
        //     dropdown.style.top = 82 + "px";
        //     dropdown.style.left = 550 + "px";
        //     dropdown.style.display = "block";
        //     document.addEventListener("click", function closeDropdown(event) {
        //         if (!dropdown.contains(event.target) && event.target.id !== "available-credits") {
        //             dropdown.style.display = "none";
        //             document.removeEventListener("click", closeDropdown);
        //         }
        //     });
        // }

        // -----------------  For Exrta Candidate details ----------------->
$(document).on('click', '.candidate-card-landing', function (e) {

    // Avoid triggering the modal when clicking on buttons inside the card
    if ($(e.target).is('button') || $(e.target).closest('button').length) {
        return;
    }
    

    $(document).on('click', '.close-candidate-poup', function () {
        $('.candidate-details-Popup').removeClass('show');
    });
    var candidateId = $(this).data('candidate-id');
   $('.candidate-details-Popup').addClass('show');

    $('.candidate-details-Popup .modal-body').html('Loading candidate details...');
      // console.log(candidateId)
    var employerId = <?= $employer_id; ?>;
    $.ajax({
        url: 'get_applied_candidate_resume.php',
        type: 'GET',
        data: { id: candidateId, employerId: employerId },
        success: function (response) {
            $('.candidate-details-Popup .modal-body').html(response);
            // $('#candidateModal').modal('show');
        },
        error: function () {
            $('.candidate-details-Popup .modal-body').html('Error fetching candidate details.');
            // $('#candidateModal').modal('show');
        }
    });
});




        // document.addEventListener('DOMContentLoaded', function () {
        //     const profileToggle = document.getElementById('profileToggle');
        //     const profileDropdown = document.getElementById('profileDropdown');
        //     profileToggle.addEventListener('click', function (e) {
        //         e.preventDefault();
        //         profileDropdown.classList.toggle('show');
        //     });
        //     document.addEventListener('click', function (e) {
        //         if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
        //             profileDropdown.classList.remove('show');
        //         }
        //     });
        // });
        
        
//     document.addEventListener('DOMContentLoaded', function () {
//     const profileToggle = document.getElementById('dropdownMenuLink');
//     const profileDropdown = document.getElementById('custom-dropdown');

//     profileToggle.addEventListener('click', function (e) {
//         e.preventDefault(); // Prevent default link behavior
//         e.stopPropagation(); // Stop the event from bubbling up
//         profileDropdown.classList.toggle('show'); // Toggle visibility
//     });

//     // Hide dropdown when clicking outside
//     document.addEventListener('click', function (e) {
//         if (!profileDropdown.contains(e.target)) {
//             profileDropdown.classList.remove('show');
//         }
//     });
// });

    </script>
    <script>
    
    // For Add to cart 
function addToCart(candidateId, price) {
    $.ajax({
        url: 'check_candidate_purchase.php',
        type: 'POST',
        data: { candidate_id: candidateId },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                if (data.is_active && data.buyed_status === 0) {
                    alert("You have already selected this candidate and their access is still active. Please select another candidate.");
                    return;
                }
                // Candidate allowed: proceed with adding to cart
                $.ajax({
                    url: 'add_to_cart.php',
                    type: 'POST',
                    data: {
                        candidate_id: candidateId,
                        price: price
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                // Update cart count in navbar elements
                                $('#cart-count').text(data.cartCount);
                                $('#cart-count-mobile').text(data.cartCount);
                                $('#cart-count-dropdown').text(data.cartCount);
                                // Update the button for this candidate using its data attribute
                                $("button.add-to-cart-btn").each(function() {
                                    if ($(this).data('candidate-id') == candidateId) {
                                        $(this)
                                            .html('<i class="fa fa-shopping-cart"></i> Go to Cart')
                                            .attr('onclick', 'window.location.href = "cart_page.php"')
                                            .removeClass('btn-primary')
                                            .addClass('btn-success');
                                    }
                                });
                                alert('Candidate added to cart successfully!');
                            } else {
                                alert('Error: ' + data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error processing response');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', error);
                        alert('Error adding candidate to cart');
                    }
                });
            } else {
                alert("Error checking candidate purchase status: " + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error in AJAX check:", error);
            alert("Error in checking candidate purchase status.");
        }
    });
}

// Update the cart count on page load
function updateAllCartCounts() {
    $.ajax({
        url: 'get_cart_count.php',
        type: 'GET',
        success: function(response) {
            try {
                const data = JSON.parse(response);
                const count = data.cartCount;
                $('#cart-count').text(count);
                $('#cart-count-mobile').text(count);
                $('#cart-count-dropdown').text(count);
            } catch (e) {
                console.error('Error parsing cart count:', e);
            }
        }
    });
}

// Update button states based on cart status using data attributes
function updateButtonStates() {
    $.ajax({
        url: 'check_cart_status.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $.each(data.inCart, function(index, candidateId) {
                    // More specific and robust selector
                    $('button.add-to-cart-btn').each(function() {
                        var btnCandidateId = $(this).data('candidate-id');
                        if (btnCandidateId == candidateId) {
                            $(this)
                                .html('<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z" fill="white"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z" fill="white"/><path d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z" fill="white"/><path d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z" fill="white"/></svg> Go to Cart')
                                .attr('onclick', 'window.location.href = "cart_page.php"')
                                .removeClass('btn-primary')
                                .addClass('btn-success');
                        }
                    });
                });
            } else {
                console.log("Failed to get cart status:", data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking cart status:', error);
        }
    });
}

// On document ready, execute both functions
$(document).ready(function() {
    updateAllCartCounts();
    // Add a delay to ensure DOM is fully loaded
    setTimeout(function() {
        updateButtonStates();
    }, 500);
});

// Also update on window load as a backup
$(window).on('load', function() {
    updateButtonStates();
});

// Initialize when document is ready
$(document).ready(function() {
    // Ensure mobile menu closes when clicking outside
    $(document).click(function(event) {
        const $navbar = $('.navbar');
        const $navbarToggler = $('.navbar-toggler');

        if (!$navbar.is(event.target) &&
            $navbar.has(event.target).length === 0 &&
            !$navbarToggler.is(event.target) &&
            $('.navbar-collapse').hasClass('show')) {
            $navbarToggler.click();
        }
    });
});

function toggleNotifications(device, event) {
    // Prevent default behavior and stop propagation
    // Get the specific notification container using a template literal
    const containerId = `notifications-${device}`;
    const container = document.getElementById(containerId);
    if (container) {
        // Get current display state
        const currentDisplay = window.getComputedStyle(container).display;
        if (currentDisplay === 'none') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    } else {
        console.error(`Notification container #${containerId} not found`);
    }
}

// (Optional) Your existing DOMContentLoaded code for filter groups, etc.
document.addEventListener('DOMContentLoaded', function() {
    const filterGroups = document.querySelectorAll('.filter-group');
    filterGroups.forEach(group => {
        const heading = group.querySelector('.d-flex');
        const content = group.querySelector('.filter-content');
        const icon = heading.querySelector('i');
        // Ensure filter content is hidden on initial load
        content.style.display = 'none';
        icon.classList.replace('fa-chevron-down', 'fa-chevron-right'); // Ensure correct icon state
        heading.addEventListener('click', function() {
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.replace('fa-chevron-right', 'fa-chevron-down');
            } else {
                content.style.display = 'none';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-right');
            }
        });
    });
});

function showLoader() {
    $('.ajax-loader').fadeIn(200);
}

function hideLoader() {
    $('.ajax-loader').fadeOut(200);
}

//------------ Function to add a keyword badge And Remove bage ------------------
function addBadge(value, type, label) {
    const badgeClass = 'badge-keyword-' + type;
    const badgeContainer = $('#keyword-badges-' + type);
    // Avoid duplicate badges
    if (badgeContainer.find(`[data-check="${label}"]`).length > 0) {
        return;
    }
    // Create badge
    const badge = $('<span></span>')
        .addClass(badgeClass)
        .addClass('filter-badge keyword-filter-badge')
        .attr('data-check', label)
        .attr('data-value', value)
        .attr('data-key', type)
        .html(`${label} <span class="remove-badge">x</span>`);
    badgeContainer.append(badge);
    badgeContainer.show();
}

function removeBadge(value, type, label) {
    const badgeContainer = $('#keyword-badges-' + type);
    // Find and remove the badge
    const badge = badgeContainer.find(`[data-check="${label}"]`);
    if (badge.length > 0) {
        badge.remove();
    }
    // Hide the container if no badges remain
    if (badgeContainer.children().length === 0) {
        badgeContainer.hide();
    }
}
$(document).on('click', '.remove-badge', function() {
    const clickedSpan = $(this);
    // Get the parent span of the clicked span
    const parentSpan = clickedSpan.parent('span');
    const label = parentSpan.data('check');
    const type = parentSpan.data('key');
    const value = parentSpan.data('value');
    // Uncheck the corresponding checkbox
    const checkbox = $(`input[type="checkbox"][name="${type}"][value="${value}"]`);
    if (checkbox.length) {
        checkbox.prop('checked', false);
    }
    // Remove the badge using data-check
    $(`.filter-badge[data-check="${label}"][data-key="${type}"]`).remove();
    // Hide container if empty
    const badgeContainer = $(`.keyword-badge-container[data-key="${type}"]`);
    if (badgeContainer.length && badgeContainer.children().length === 0) {
        badgeContainer.hide();
    }
    fetchCandidates(1);
});
// ------------End Function to add a keyword badge And Remove bage ----------------

//------------ Fetch searched location and Filter useing location -----------------
$(document).on("input", '#search_location', function() {
    const query = $(this).val();
    if (query.length >= 1) {
        $('#select-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
        $.ajax({
            url: '../candidate/search_location.php',
            type: 'POST',
            data: { query: query },
            success: function(response) {
                if ($.trim(response)) {
                    $("#select-div").show().html(response);
                } else {
                    $("#select-div").hide().html("");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
            }
        });
    } else {
        $("#select-div").hide().html("");
    }
});
// Set input value and trigger function 
$(document).on("click", '#list_location', function() {
    const label = $(this).text();
    const location_id = $(this).data('id');
    $('#search_location').val(label);
    addBadge(location_id, "location", label);
    $('#select-div').hide();
    $('#search_location').val('');
    fetchCandidates(1);
});
// Target remover
$(document).on("click", function(event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div').length && !$target.is('#search_location')) {
        $('#select-div').hide();
    }
});
//------------ End Fetch searched location and Filter useing location -----------------
       //------------ Fetch havekeyword ------------

        // Format field names for display
        function formatFieldName(fieldName) {
            const fieldDisplayNames = {
                'username': 'Name',
                'mobile_number': 'Mobile Number',
                'current_company': 'Current Company',
                'companyname': 'Company Name',
                'sales_experience': 'Sales Experience',
                'work_experience': 'Work Experience',
                'current_location': 'Current Location',
                'location': 'Location',
                'current_salary': 'Current Salary',
                'salary': 'Salary',
                'jobrole': 'Job Role',
                'gender': 'Gender',
                'employed': 'Employment Status',
                'destination': 'Destination',
                'hl_lap': 'HL/LAP',
                'personal_loan': 'Personal Loan',
                'business_loan': 'Business Loan',
                'education_loan': 'Education Loan',
                'credit_cards': 'Credit Cards',
                'gold_loan': 'Gold Loan',
                'casa': 'CASA',
                'others': 'Others',
                'Credit_dept': 'Credit Department',
                'HR_Training': 'HR Training',
                'Legal_compliance_Risk': 'Legal Compliance/Risk',
                'Operations': 'Operations',
                'Others1': 'Other Details',
                'Sales': 'Sales',
                'associate_name': 'Associate Name',
                'associate_mobile': 'Associate Mobile'
            };

            return fieldDisplayNames[fieldName] || fieldName.split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        $('#Havekeyword').on('input', function () {
            const keyword = $(this).val().trim();
            if (keyword.length === 0) {
                $('#keywordSuggestions').hide().empty();
                return;
            }
            // Add loading indicator
            $('#keywordSuggestions').html('<li style="text-align:center;padding:10px;">Searching...</li>').show();
            $.ajax({
                url: 'candidate_search.php',
                type: 'GET',
                data: { keyword: keyword },
                dataType: 'json',
                success: function (response) {
                    const $suggestions = $('#keywordSuggestions').empty();
                    if (response && response.length > 0) {
                        // Group results by field type
                        const fieldGroups = {};
                        response.forEach(function (item) {
                            if (!fieldGroups[item.field]) {
                                fieldGroups[item.field] = [];
                            }
                            // Check if this exact value already exists in this field group
                            const exists = fieldGroups[item.field].some(function (existingItem) {
                                return existingItem.value === item.value;
                            });
                            if (!exists) {
                                fieldGroups[item.field].push(item);
                            }
                        });
                        // Loop through each field group and add its items to the dropdown
                        Object.keys(fieldGroups).forEach(function (fieldName) {
                            // Convert field name to display name
                            const displayFieldName = formatFieldName(fieldName);
                            // Add each value for this field
                            fieldGroups[fieldName].forEach(function (item) {
                                const $item = $(
                                    `<li class="suggestion-item" style="padding:8px 15px;cursor:pointer;border-bottom:1px solid #eee;display:flex;justify-content:space-between;">
                                            ${item.value}
                                        </li>`
                                ).data('item', item);

                                // New click handler
                                $item.on('click', function () {
                                    const selectedItem = $(this).data('item');
                                    const selectedValue = selectedItem.value;

                                    // Add a badge for the selected keyword
                                    addBadge(selectedValue, "havekeyword", selectedValue);

                                    // Clear the input field
                                    $('#Havekeyword').val('');
                                    $suggestions.hide().empty();

                                    // Trigger the search with all selected keywords
                                    fetchCandidates(1);
                                });
                                $suggestions.append($item);
                            });
                        });
                        $suggestions.css({
                            'max-height': '400px',
                            'overflow-y': 'auto',
                            'border': '1px solid #ddd',
                            'border-radius': '4px',
                            'box-shadow': '0 2px 5px rgba(0,0,0,0.15)'
                        }).show();
                    } else {
                        $suggestions.html(
                            '<li style="text-align:center;padding:10px;color:#777;">No matching results found</li>'
                        ).show();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching suggestions:', error);
                    $('#keywordSuggestions').html(
                        '<li style="text-align:center;padding:10px;color:#d9534f;">Error loading suggestions</li>'
                    );
                }
            });
        });
        // Hide suggestions when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#Havekeyword, #keywordSuggestions').length) {
                $('#keywordSuggestions').hide().empty();
            }
        });
        // -------------- End Fecth haveKeyword --------------
//------------ Search product and department and subproduct and subdepartment ---------------
$('#departmentsearch').on('input', function() {
    const input = $(this).val();
    $('#department-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { departmentsearch: input },
        success: function(response) {
            $('#department-list-div').html(response);
        },
        error: function() {
            console.error('AJAX request failed.');
        }
    });
});
$('#subdepartmentsearch').on('input', function() {
    const input = $(this).val();
    $('#subdepartment-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { subdepartmentsearch: input },
        success: function(response) {
            $('#subdepartment-list-div').html(response);
        },
        error: function() {
            console.error('AJAX request failed.');
        }
    });
});
$('#categorysearch').on('input', function() {
    const input = $(this).val();
    $('#category-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { categorysearch: input },
        success: function(response) {
            $('#category-list-div').html(response);
        },
        error: function() {
            console.error('AJAX request failed.');
        }
    });
});
$('#productsearch').on('input', function() {
    const input = $(this).val();
    $('#product-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { productsearch: input },
        success: function(response) {
            $('#product-list-div').html(response);
        },
        error: function() {
            console.error('AJAX request failed.');
        }
    });
});
$('#subproductsearch').on('input', function() {
    const input = $(this).val();
    $('#subproduct-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { subproductsearch: input },
        success: function(response) {
            $('#subproduct-list-div').html(response);
        },
        error: function() {
            console.error('AJAX request failed.');
        }
    });
});
$('#specializationsearch').on('input', function() {
    const input = $(this).val();
    $('#specialization-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { specializationsearch: input },
        success: function(response) {
            $('#specialization-list-div').html(response);
        },
        error: function() {
            console.error('AJAX request failed.');
        }
    });
});
//------------ End Search product and department and subproduct and subdepartment ---------------

//------------ Fetch Fetch searched designation -----------------
$(document).on("input", '#search_designation', function() {
    const query = $(this).val();
    if (query.length >= 1) {
        $('#select-designation-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
        $.ajax({
            url: 'search_designation.php',
            type: 'POST',
            data: { query: query },
            success: function(response) {
                if ($.trim(response)) {
                    $("#select-designation-div").show().html(response);
                } else {
                    $("#select-designation-div").hide().html("");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
            }
        });
    } else {
        $("#select-designation-div").hide().html("");
    }
});
// Set input value and trigger function 
$(document).on("click", '#list_designation', function() {
    const label = $(this).text();
    $('#search_designation').val(label);
    addBadge(label, "designation", label);
    $('#select-designation-div').hide();
    $('#search_designation').val('');
    fetchCandidates(1);
});
// Target remover
$(document).on("click", function(event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div').length && !$target.is('#search_designation')) {
        $('#select-designation-div').hide();
    }
});
//------------ End Fetch searched designation -----------------

// ------------Fetch selected values -----------
function getSelectedKeysValues() {
    const KeysValues = [];
    $('.keyword-filter-badge').each(function() {
        const key = $(this).data('key');
        const value = $(this).data('value');
        KeysValues.push({ key: key, value: value });
    });
    return KeysValues;
}
// ------------ End Fetch selected values -----------

// Ensure jQuery is loaded
const urlParams = new URLSearchParams(window.location.search);
const jobId = urlParams.get('job_id');

// ----------- Old filters ------------
function collectFilters() {
    const filters = {};
    // if ($('input[name="sort"]:checked').length) {
    //     filters.sort = $('input[name="sort"]:checked').val();
    // } else if ($('#jobsort').val()) {
    //     filters.sort = $('#jobsort').val();
    // } else if ($('#sort-relevance').is(':checked')) {
    //     filters.sort = 'relevance';
    // } else if ($('#sort-salary').is(':checked')) {
    //     filters.sort = 'salary';
    // } else if ($('#sort-date').is(':checked')) {
    //     filters.sort = 'date';
    // }

    // For Mobile extra details
    filters.department_m = $('#jobdepartment').val();
    filters.subdepartment_m = $('#job-sub-department').val();
    filters.category_m = $('#job-category').val();
    filters.product_m = $('#job-product').val();
    filters.subproduct_m = $('#job-sub-product').val();
    filters.specialization_m = $('#job-specialization').val();
    filters.location_m = $('#joblocation').val();

    // Salary range
    const minSalaryValue = $('#min_salary').val() || $('#jobmin_salary').val();
    const maxSalaryValue = $('#max_salary').val() || $('#jobmax_salary').val();
    if (minSalaryValue) filters.min_salary = minSalaryValue;
    if (maxSalaryValue) filters.max_salary = maxSalaryValue;

    // Search term
    const searchValue = $('#search').val() || $('#searchjob').val();
    if (searchValue) filters.search = searchValue;

    // Pagination
    const limitValue = $('#per-page-list').val();
    if (limitValue) filters.limit = limitValue;

    return filters;
}

// Function to fetch candidates with filters
function fetchCandidates(page = 1) {
     const filters = collectFilters();
    const values = getSelectedKeysValues();
    values.forEach(({ key, value }) => {
        if (!filters[`${key}[]`]) {
            filters[`${key}[]`] = [];
        }
        filters[`${key}[]`].push(value);
    });
     filters.page = page;
    showLoader();
         if (jobId) {
      filters.job_id = jobId;
    }
    $.ajax({
      url: 'applied_candidate_filter.php',
      type: 'GET',
      data: filters,
      success: function(response) {
          $('#applied-candidate-list').html(response);
           hideLoader();
          
          // Scroll back to top of results after page change
          $('html, body').animate({
              scrollTop: $("#applied-candidate-list").offset().top - 100
          }, 200);
      },
      error: function(xhr, status, error) {
           hideLoader();
          console.error('Error fetching candidates:', error);
      }
  });
    if (jobId) {
        filters.job_id = jobId;
    }
}

$(document).ready(function() {
    // Add search input event handler with debounce
    $('#searchInput').on('input', function() {
        clearTimeout($(this).data('timer'));
        $(this).data('timer', setTimeout(function() {
            fetchCandidates(1);
        }, 300));
    });

    // Prevent form submission and handle search on Enter key
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        fetchCandidates(1);
    });

    // Apply job filter 
    $(document).on('change', '.applied-input', function() {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "applied", label);
        } else {
            removeBadge(this.value, "applied", label);
        }
        fetchCandidates(1);
    });

    // Location filter mention in top 

    // Haveword filter mention in top 

    // Gender filter
    $(document).on('change', 'input[name="gender"]', function() {
        if (this.checked) {
            addBadge(this.value, "gender", this.value);
        } else {
            removeBadge(this.value, "gender", this.value)
        }
        fetchCandidates(1);

    });

    // Deparments Products Sub Products,Departments Filter
    $(document).on('change', 'input[name="department"]', function() {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "department", label);
        } else {
            removeBadge(this.value, "department", label)
        }
        fetchCandidates(1);
    });

    $(document).on('change', 'input[name="subdepartment"]', function() {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "subdepartment", label);
        } else {
            removeBadge(this.value, "subdepartment", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="category"]', function() {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "category", label);
        } else {
            removeBadge(this.value, "category", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="product"]', function() {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "product", label);
        } else {
            removeBadge(this.value, "product", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="subproduct"]', function() {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "subproduct", label);
        } else {
            removeBadge(this.value, "subproduct", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="specialization"]', function() {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "specialization", label);
        } else {
            removeBadge(this.value, "specialization", label)
        }
        fetchCandidates(1);
    });

    // Designation filter mention in top

    // Salary and Experience Filter
    $('input[name="min_salary"], input[name="max_salary"], input[name="min_experience"], input[name="max_experience"]').on('input', function() {
        fetchCandidates(1);
    });

    // Load candidates on page load

});


// Event bindings
$(document).ready(function () {
    fetchCandidates();

    // Web filters
    $(document).on('change', 'input[type="checkbox"]', function () {
        fetchCandidates(1);
    });

    $('#other-department, #other-sub-department, #other-product, #other-sub-product, #other-designation').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchCandidates(1);
        }, 500));
    });

    $('#jobsort, #jobdepartment, #job-sub-department,#job-category, #job-product,#job-sub-product,#job-specialization, #location, #joblocation, #per-page-list').on('change', function () {
        fetchCandidates(1);
    });

    $('#min_salary, #max_salary, #jobmin_salary, #jobmax_salary').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchCandidates(1);
        }, 500));
    });

    $('#search, #searchjob').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchCandidates(1);
        }, 300));
    });

    // Pagination
    $(document).on('click', '.linkForPage', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        fetchCandidates(page);
    });

    // Mobile filter checkboxes
    $('#mobileFilterPopup input[type="checkbox"]').on('change', function () {
        fetchCandidates(1);
    });

    // Mobile clear filters
    $('#clearFiltersBtn').on('click', function () {
        window.location.reload(); // Reloads the current page
    });


    // Optional: Close filter popup button
    $('#closeFilterPopup').on('click', function () {
        $('#mobileFilterPopup').addClass('d-none');
    });
});









</script>
 
<script>
$(document).on('click', '.reject', function () {
  const appliedJobId = $(this).data('id');
  $('#popup-job-id').val(appliedJobId);        
  $('#feedback-text').val('');                 
  $('#feedback-popup h5').text('Reject Feedback');
  $('#feedback-popup, #popup-overlay').fadeIn();
});
// Cancel button to hide popup
$('#cancel-feedback').on('click', function () {
  $('#feedback-popup, #popup-overlay').fadeOut();
});
// Submit button handler
$('#submit-feedback').on('click', function () {
  const jobId = $('#popup-job-id').val();
  const feedback = $('#feedback-text').val().trim();
  const actionType = "reject";
  if (!feedback) {
    alert('Please enter a reason.');
    return;
  }

  // Send via AJAX
$.post('submit_feedback.php', {
  job_id: jobId,
  feedback: feedback,
  action_type: actionType
}, function (response) {
  // Hide reject and shortlist buttons
  $('#reject_btn_' + jobId).hide();
  $('#shortlist_btn_' + jobId).hide();
   $('#modify_btn_' + jobId).show();

  // Show rejected status button
  $('#rejected_btn_' + jobId).show();

//   alert('Feedback submitted successfully!');
  $('#feedback-popup, #popup-overlay').fadeOut();
});

});


$(document).on('click', '.select', function () {
  const jobId = $(this).data('id');
  $('#confirmation-confirm-shortlist').data('id', jobId); 
  $('#confirmation-popup').fadeIn();
  $(' #confirmation-popup-overlay').fadeIn();
});

// Handle confirmation click
$('#confirmation-confirm-shortlist').on('click', function () {
  const jobId = $(this).data('id');

  $.post('submit_feedback.php', {
    job_id: jobId,
    action_type: "shortlist"
  }, function (response) {
    $('#reject_btn_' + jobId).hide();
    $('#shortlist_btn_' + jobId).hide();
    $('#shortlisted_btn_' + jobId).show();
    $('#modify_btn_' + jobId).show();

    $('#confirmation-popup, #confirmation-popup-overlay').fadeOut();
  });
});

// Cancel popup
$('#confirmation-cancel-popup').on('click', function () {
  $('#confirmation-popup, #confirmation-popup-overlay').fadeOut();
});








$(document).on('click', '.modify', function () {
  const jobId = $(this).data('id');
  $('#modify-confirm-modify').data('id', jobId);  
  $('#modify-confirmation-popup').fadeIn();
   $('.modify-popup-overlay').fadeIn();
});

// Handle confirmation click
$('#modify-confirm-modify').on('click', function () { 
  const jobId = $(this).data('id');

  $.post('submit_feedback.php', {
    job_id: jobId,
    action_type: "modify"
  }, function (response) {
    $('#reject_btn_' + jobId).show();
    $('#shortlist_btn_' + jobId).show();
    $('#shortlisted_btn_' + jobId).hide();
    $('#rejected_btn_' + jobId).hide();
    $('#modify_btn_' + jobId).hide();

    $('#modify-confirmation-popup').fadeOut();
  });
});

// Cancel popup
$('.modify-cancel-popup').on('click', function () {
  $('#modify-confirmation-popup, .modify-popup-overlay').fadeOut();  // matched class
});








</script>


 <?php include('../footer.php'); ?>
</body>

</html>