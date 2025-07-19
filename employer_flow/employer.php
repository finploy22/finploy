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

if (isset($_SESSION['planDetails'])) {
  header("Location: ../subscription/plans.php");
  exit();
}

// Get employer ID
$stmt = $conn->prepare("SELECT id FROM employers WHERE mobile_number = ? LIMIT 1");
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
  die("No employer found.");
}
$employer_id = $_SESSION['employer_id'];

// Fetch candidates (exclude already purchased)
$candidateQuery = "
    SELECT 
        cd.id, cd.user_id, cd.username, cd.mobile_number, cd.gender, cd.employed, 
        cd.current_company, cd.destination, cd.sales_experience, cd.work_experience, 
        cd.current_location, cd.current_salary, cd.hl_lap, cd.personal_loan, 
        cd.business_loan, cd.education_loan, cd.gold_loan, cd.credit_cards, 
        cd.casa, cd.others, cd.resume, cd.created, cd.modified
    FROM candidate_details cd
    WHERE NOT EXISTS (
        SELECT 1 
        FROM order_items oi
        INNER JOIN payments p ON oi.payment_id = p.id
        WHERE oi.candidate_id = cd.id 
          AND p.employer_id = ? 
          AND p.buyed_status = 0
    )
";
$stmt = $conn->prepare($candidateQuery);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
  die("Candidate query failed: " . $conn->error);
}

// Fetch cart candidate IDs
$cartCandidateIds = isset($_SESSION['cart']) ? array_column($_SESSION['cart'], 'candidate_id') : [];

// Mask function
function maskHalf($str)
{
  $length = strlen($str);
  $half = floor($length / 2);
  return substr($str, 0, $half) . str_repeat('x', $length - $half);
}

// Notifications: candidates about to expire
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
    WHERE p.employer_id = ?
      AND p.expired IN (8, 6, 4, 2)
    ORDER BY p.created_at DESC
    LIMIT 4
";
$stmt = $conn->prepare($expiredQuery);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$expiredResult = $stmt->get_result();
while ($row = $expiredResult->fetch_assoc()) {
  $employerName = htmlspecialchars($row['employer_name']);
  $candidateName = htmlspecialchars($row['candidate_name']);
  $purchaseDate = date("M d, Y, h:i:s A", strtotime($row['purchase_date']));
  $expiredMinutes = (int) $row['expired'];
  //   $message = match ($expiredMinutes) {
//     8 => "{$employerName}, your selected candidate {$candidateName} will expire in 8 minutes on {$purchaseDate}.",
//     6 => "{$employerName}, your selected candidate {$candidateName} will expire in 6 minutes on {$purchaseDate}.",
//     4 => "{$employerName}, your selected candidate {$candidateName} will expire in 4 minutes on {$purchaseDate}.",
//     2 => "{$employerName}, your selected candidate {$candidateName} will expire in 2 minutes on {$purchaseDate}.",
//     default => "{$employerName}, the expiration status for candidate {$candidateName} is unknown."
//   };

  $message = "";
  $expiredNotifications[] = [
    'message' => $message,
    'username' => $candidateName,
    'purchase_date' => $row['purchase_date']
  ];
}
$notificationCount = count($expiredNotifications);

// Load dropdown filters (no dynamic params, so regular queries are fine)
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

<body>

  <!-- Main Content -->
  <div class="container mt-4 ">

    <div class=" overall-container">
      <div class="row mb-3 ">
        <!-- search and heding div -->
        <div class="job-liting-page">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h5 class="jobs-page-title">Showing All Candidates</h5>
            <div class="search-container" id="search-container-web">
              <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                <path
                  d="M16.5674 16.1255L20.4141 20M18.6363 11.1111C18.6363 15.0385 15.4526 18.2222 11.5252 18.2222C7.59781 18.2222 4.41406 15.0385 4.41406 11.1111C4.41406 7.18375 7.59781 4 11.5252 4C15.4526 4 18.6363 7.18375 18.6363 11.1111Z"
                  stroke="#888888" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <input type="text" id="search" class="form-control" placeholder="Search for ' location '"
                autocomplete="off">
              <div class="custom-tooltip-container me-2">
                  <span>
                    <img src="/images/ep_info-default-icon-index-smu.svg" class="info-icon " alt="Info"
                         width="20" height="20" data-default-src="/images/ep_info-default-icon-index-smu.svg"
                         data-hover-src="/images/ep_info-blue-icon-index-smu.svg">
                  </span>
                  <div class="custom-tooltip">
                        <strong>You can search by:</strong> <br>
                        Use this to refine your search by Age, Gender, Experience, Salary, Location, Department,
                        Sub-Department, Category, Specialization and Product. This helps you find the relevant
                        opportunities Better.
                    </div>
              </div>
              <div class="search-container p-2" id="search-icon-mobile">
                <img src="/images/search-index-icon-smu.svg" alt="" width="20" height="21">
              </div>
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
        <div class="search-container" id="search-container-mobile">
          <input type="text" id="searchjob" class="form-control" placeholder="Search for ' location '"
            autocomplete="off">
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
              style="max-height: 200px !important; overflow-y: auto !important;">
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
              style="max-height: 200px !important; overflow-y: auto !important;">
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
              style="max-height: 200px !important; overflow-y: auto !important;">

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
              style="max-height: 200px !important; overflow-y: auto !important;">
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
              style="max-height: 200px !important; overflow-y: auto !important;">
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
              style="max-height: 200px !important; overflow-y: auto !important;">
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
          <div id="candidate-list">
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
                      $selected = ($option == 20) ? "selected" : "";
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



  <!-- Bootstrap & Custom Scripts -->
  <!-- Bootstrap & Custom Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="employer.js"></script>
  <script src="../new-js/employer.js"></script>
  <!-- At the end of your HTML, before the closing body tag -->
  <script>
 
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
        url: 'get_candidate_details.php',
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
      // Script for search function 
      document.addEventListener('DOMContentLoaded', function () {
      const infoIcon = document.querySelector('.info-icon');
      infoIcon.addEventListener('mouseover', function () {
          this.src = this.dataset.hoverSrc;
      });
      infoIcon.addEventListener('mouseout', function () {
          this.src = this.dataset.defaultSrc;
      });
    });
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
      $.ajax({
          url: 'candidate_filter.php',
          type: 'GET',
          data: filters,
          success: function (response) {
              $('#candidate-list').html(response);
              hideLoader();
          },
          error: function (xhr, status, error) {
              console.error('Error fetching candidates:', error);
              hideLoader();
          }
      });
      if (jobId) {
          filters.job_id = jobId;
      }
      }
      
    function getSelectedKeysValues() {
    const KeysValues = [];
    $('.keyword-filter-badge').each(function () {
        const key = $(this).data('key');
        const value = $(this).data('value');
        KeysValues.push({ key: key, value: value });
    });
    // Show or hide the clear-all button
    if (KeysValues.length > 0) {
        document.querySelector('.clear-all-filter').style.display = 'block';
    } else {
        document.querySelector('.clear-all-filter').style.display = 'none';
    }
    return KeysValues;
}
//

    // Wait for the DOM to be fully loaded
    // document.addEventListener('DOMContentLoaded', function () {
    //   const filtersCard = document.querySelector('.d-block.d-md-none.mb-3');
    //   if (filtersCard) {
    //     filtersCard.addEventListener('click', () => {
    //       const modal = new bootstrap.Modal(document.getElementById('filtersModal'));
    //       modal.show();
    //     });
    //   } else {
    //     console.error('Filter card element not found');
    //   }
    // });

    //////////////////////////////// Available Credits //////////////////////
    // document.getElementById("available-credits").addEventListener("click", function (event) {
    //   event.stopPropagation(); // Prevent closing immediately

    //   let dropdown = document.getElementById("credits-dropdown"); // Check if modal already exists

    //   if (!dropdown) {
    //     // Fetch the modal content from external file
    //     fetch("available_credits.php")
    //       .then(response => response.text())
    //       .then(html => {
    //         document.getElementById("credits-container").innerHTML = html; // Load the popup
    //         showDropdown();
    //       });
    //   } else {
    //     showDropdown();
    //   }
    // });

    // function showDropdown() {
    //   let dropdown = document.getElementById("credits-dropdown");

    //   // Positioning near button
    //   let button = document.getElementById("available-credits");
    //   let rect = button.getBoundingClientRect();
    //   dropdown.style.top = 82 + "px";
    //   dropdown.style.left = 550 + "px";
    //   dropdown.style.display = "block";

    //   // Close dropdown when clicking outside
    //   document.addEventListener("click", function closeDropdown(event) {
    //     if (!dropdown.contains(event.target) && event.target.id !== "available-credits") {
    //       dropdown.style.display = "none";
    //       document.removeEventListener("click", closeDropdown);
    //     }
    //   });
    // }

    // $(document).on('click', '.candidate-card-landing', function (e) {

    //   // Avoid triggering the modal when clicking on buttons inside the card
    //   if ($(e.target).is('button') || $(e.target).closest('button').length) {
    //     return;
    //   }

    //   $(document).on('click', '.close-pop-up', function () {
    //     $('#candidateModal').modal('hide');
    //   });
    //   var candidateId = $(this).data('candidate-id');
    //   $('#candidateModal .modal-body').html('Loading candidate details...');
    //   var employerId = <?= $employer_id; ?>;
    //   $.ajax({
    //     url: 'get_candidate_details.php',
    //     type: 'GET',
    //     data: { id: candidateId, employerId: employerId },
    //     success: function (response) {
    //       $('#candidateModal .modal-body').html(response);
    //       $('#candidateModal').modal('show');
    //     },
    //     error: function () {
    //       $('#candidateModal .modal-body').html('Error fetching candidate details.');
    //       $('#candidateModal').modal('show');
    //     }
    //   });
    // });
    // document.addEventListener('DOMContentLoaded', function () {
    //   // Get the profile toggle button and dropdown
    //   const profileToggle = document.getElementById('profileToggle');
    //   const profileDropdown = document.getElementById('profileDropdown');

    //   // Add click event to toggle profile dropdown
    //   profileToggle.addEventListener('click', function (e) {
    //     e.preventDefault();
    //     profileDropdown.classList.toggle('show');
    //   });

    //   // Close dropdown when clicking outside
    //   document.addEventListener('click', function (e) {
    //     if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
    //       profileDropdown.classList.remove('show');
    //     }
    //   });
    // });

    //  For profile icon
    // document.addEventListener('DOMContentLoaded', function () {
    //   const profileToggle = document.getElementById('dropdownMenuLink');
    //   const profileDropdown = document.getElementById('custom-dropdown');

    //   profileToggle.addEventListener('click', function (e) {
    //     e.preventDefault(); // Prevent default link behavior
    //     e.stopPropagation(); // Stop the event from bubbling up
    //     profileDropdown.classList.toggle('show'); // Toggle visibility
    //   });

    //   // Hide dropdown when clicking outside
    //   document.addEventListener('click', function (e) {
    //     if (!profileDropdown.contains(e.target)) {
    //       profileDropdown.classList.remove('show');
    //     }
    //   });
    // });




  </script>

  <?php include('../footer.php'); ?>
</body>

</html>