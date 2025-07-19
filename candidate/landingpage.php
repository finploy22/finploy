<?php
require '../session.php';
// include '../employer_flow/posting_header.php';
// include '../header.php';
include 'header.php';
include '../db/connection.php';

$user_type = is_logged_in();
if ($user_type == '') {
    header("Location: index.php");
    exit;
}

// Check candidate is there or Not
$mobileNumber = isset($_SESSION['mobile']) ? $_SESSION['mobile'] : '';
$candidate_query = "SELECT user_id FROM candidates WHERE mobile_number = '$mobileNumber'";
$candidate_result = mysqli_query($conn, $candidate_query);
$candidate_row = mysqli_fetch_assoc($candidate_result);
$candidate_id = $candidate_row['user_id'] ?? null;
if (!$candidate_id) {
    echo "Candidate not found!";
    exit;
}

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

// Check wia register whatsapp
$NotUserDataUpdated = false;
$stmt = $conn->prepare("SELECT * FROM candidate_details  WHERE user_id = ?");
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row["current_location"]) && empty($row['location_code'])) {
            if ($row["location_code"] == 0) {
                $NotUserDataUpdated = true;
            }
        }

    }
}




$sql = "SELECT 
            id, user_id, username, mobile_number, gender,
            employed, current_company, sales_experience, destination, 
            work_experience, current_location, current_salary, resume, 
            products, sub_products, departments, sub_departments, 
             location_code, age
        FROM candidate_details 
        WHERE mobile_number = '$mobileNumber'";

$result = $conn->query($sql);

$all_data_isthere = false;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $required_fields = [
        'username',
        'mobile_number',
        'gender',
        'age',
        'employed',
        'current_company',
        'sales_experience',
        'destination',
        'products',
        'sub_products',
        'departments',
        'sub_departments',
        'work_experience',
        'current_salary',
        'location_code'


    ];

    $all_data_isthere = true;
    foreach ($required_fields as $field) {
        if (empty($row[$field]) || $row['location_code'] == 0) {
            $all_data_isthere = false;
            break;
        }
    }
}

if (!$all_data_isthere) {
    header("Location: index.php");
    exit();
}
?>
<style>
    .divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 1.5rem 0;
    width: 100%;
    margin-top: 11px;
}
 .continue-btn{
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
    align-items: center;
    justify-content: center;
}
        .share-btn{
    color:#175DA8;
    border-color:#175DA8;
}
.share-btn:hover{
    color:#175DA8;
    border-color:#175DA8;
}
.btn-popup-btn2{ 
    color: #175DA8 !important;
    border-color: #175DA8 !important;
    margin-bottom: 0px !important;
    width: 83.5%;
    margin-left: 10px;
}
</style>

<body>
    <div class="container mt-4 mb-5">
        <div class="job-liting-page">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="jobs-page-title">Showing All Bank, Finance And Insurance Jobs</h5>
                <div class="search-container" style=" padding-right: 13px;" id="search-container-web">

                    <img src="/images/search-index-icon-smu.svg" alt="" width="20" height="21">

                    <input type="text" id="search" class="form-control" placeholder="Search for ' location '"
                        autocomplete="off" title="Search for Results">
                    <!-- serach engine tooltip -->
                    <div class="custom-tooltip-container">
                        <span>
                            <img src="/images/ep_info-default-icon-index-smu.svg" class="info-icon" alt="Info"
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
                </div>
                <div class="search-container p-2" id="search-icon-mobile">
                    <img src="/images/search-index-icon-smu.svg" alt="" width="20" height="21">
                </div>

            </div>
        </div>
        <div class="search-container" id="search-container-mobile">
            <input type="text" id="searchjob" class="form-control" placeholder="Search for ' location '"
                autocomplete="off">
        </div>
        <div class="row">
            <!-- Filters Section -->
           <div class="col-md-3 filters-section" id="filters-section-web">
                <h5 class="filter-header"><img src="assets/filter-icon.svg" alt="">All Filters <a href="/index.php"
                        class="clear-all-filter" style="display:none">Clear All</a></h5>
                <hr class="filter-line">
                <div id="keyword-badges-sort" class="mb-2 keyword-badges-filter"></div>
                <div class="mb-3 filter-div-sort">
                    <h6 class="filter-accordion">Sort By <img class="accordion-arrow" src="assets/downward-arrow.svg"
                            alt="Toggle Arrow"></h6>
                    <div class="filter-content active">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sort-relevance" name="sort" data-name="Relevance"
                                value="relevance">
                            <label class="filter-lable" for="sort-relevance">Relevance</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sort-salary" name="sort" data-name="Salary-High to Low"
                                value="salary_desc">
                            <label class="filter-lable" for="sort-salary">Salary - High to Low</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sort-date" name="sort" data-name="Date Posted-New to Old"
                                value="date_desc">
                            <label class="filter-lable" for="sort-date">Date Posted - New to Old</label>
                        </div>
                    </div>
                </div>
                <hr class="filter-line">
                <!-- Department Filter (Updated) -->
                <div id="keyword-badges-department" class="mb-2 keyword-badges-filter"></div>
                <div class="mb-3 filter-div">
                    <h6 class="filter-accordion" id="department-list-head">Department <img class="accordion-arrow"
                            src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>

                    <div class="filter-content" id="department-list-div"
                        style="max-height: 200px !important; overflow-y: auto !important;">
                        <?php if ($row_departments) {
                            foreach ($row_departments as $department) { ?>
                                <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                                    <input type="checkbox" class="checkbox-input" name="department"
                                        value="<?= $department['department_id']; ?>"
                                        data-name="<?= $department['department_name']; ?>">
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
                            src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
                    <div class="filter-content" id="subdepartment-list-div"
                        style="max-height: 200px !important; overflow-y: auto;">
                        <?php if ($row_sub_departments) {
                            foreach ($row_sub_departments as $sub_departments) { ?>
                                <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                                    <input type="checkbox" class="checkbox-input" name="subdepartment"
                                        value="<?= $sub_departments['sub_department_id']; ?>"
                                        data-name="<?= $sub_departments['sub_department_name']; ?>">
                                    <label
                                        class="filter-lable checkbox-label"><?= $sub_departments['sub_department_name']; ?></label>
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
                            src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>

                    <div class="filter-content" id="category-list-div" style="max-height: 200px; overflow-y: auto;">

                        <?php if ($row_departments_category) {
                            foreach ($row_departments_category as $category) { ?>
                                <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                                    <input type="checkbox" class="checkbox-input" name="category"
                                        value="<?= $category['category_id']; ?>" data-name="<?= $category['category']; ?>">
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
                            src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
                    <div class="filter-content" id="product-list-div" style="max-height: 200px; overflow-y: auto;">
                        <?php if ($row_products) {
                            foreach ($row_products as $product) { ?>
                                <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                                    <input type="checkbox" class="checkbox-input" name="product"
                                        value="<?= $product['product_id']; ?>" data-name="<?= $product['product_name']; ?>">
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
                            src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
                    <div class="filter-content" id="subproduct-list-div" style="max-height: 200px; overflow-y: auto;">
                        <?php if ($row_sub_products) {
                            foreach ($row_sub_products as $sub_product) { ?>
                                <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                                    <input type="checkbox" class="checkbox-input" name="subproduct"
                                        value="<?= $sub_product['sub_product_id']; ?>"
                                        data-name="<?= $sub_product['sub_product_name']; ?>">
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
                            src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
                    <div class="filter-content" id="specialization-list-div"
                        style="max-height: 200px; overflow-y: auto;">
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
                        <input type="text" class="form-control " id="search_designation"
                            placeholder="Search Designation" name="designation" autocomplete="off">
                    </div>
                    <div id="select-designation-div" style="display: none;"></div>
                </div>
                <hr class="filter-line">
                <!-- Location Filter (Updated) -->
                <div class=" mb-3 filter-div">
                    <h6 class="filter-accordion">Location </h6>
                    <div class="custom-checkbox">
                        <div id="keyword-badges-location" class="mb-2"></div>
                        <input type="text" class="form-control" id="search_location" placeholder="Search Location"
                            name="location" autocomplete="off">
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
            <div class="filters-section d-flex align-items-center overflow-auto"
                style="white-space: nowrap; gap: 1.5rem;" id="filters-section-mobile">
                <!-- Filter Button -->
                <button class="btn btn-outline-primary d-flex align-items-center" style="width: 46px; height: 35px;">
                    <img src="assets/filter-icon.svg" alt="Filter Icon" class="me-2" style="width: 20px;">
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

           <!-- Job Listings Section -->
            <div class="col-md-9">
                <div class="ajax-loader" id="ajaxLoader" style="display: none;">
                    <div class="ajax-loader-conatainer">
                        <!-- New circle loader replacing the spinner with bouncing dots -->
                        <div class="circle-loader"></div>
                    </div>
                </div>
                <div id="job-list">
                    <!-- Job cards will be dynamically populated here -->
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
    <!-- Mobile Filter Popup (Updated with New Titles and Values) -->
    <div id="mobileFilterPopup" class="mobile-filter-popup d-none">
        <div class="popup-header d-flex justify-content-between align-items-center p-2 border-bottom">
            <button id="closeFilterPopup" class="btn btn-link text-dark"><i class="bi bi-arrow-left"></i></button>
            <h5 class="m-0">Filters</h5>
            <button id="clearFiltersBtn" class="btn btn-link text-secondary">Clear Filters</button>
        </div>

        <div class="popup-content d-flex">
            <!-- Left Categories -->
            <div class="filter-categories border-end">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item active" data-category="sortby">Sort By</li>
                    <li class="list-group-item" data-category="department">Department</li>
                    <li class="list-group-item" data-category="subdepartment">Sub-Department</li>
                    <li class="list-group-item" data-category="category">Category</li>
                    <li class="list-group-item" data-category="product">Product</li>
                    <li class="list-group-item" data-category="subproduct">Sub-Product</li>
                    <li class="list-group-item" data-category="specialization">Specialization</li>
                    <li class="list-group-item" data-category="designation">Designation</li>
                    <li class="list-group-item" data-category="location">Location</li>
                </ul>
            </div>

            <!-- Right Filter Options -->
            <div class="filter-options flex-grow-1 p-3" id="filterOptionsContent">

                <!-- Sort By -->
                <div data-content="sortby">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sort-relevance">
                        <label class="form-check-label" for="sort-relevance">Relevance</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sort-salary">
                        <label class="form-check-label" for="sort-salary">Salary - High to Low</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sort-date">
                        <label class="form-check-label" for="sort-date">Date Posted - New to Old</label>
                    </div>
                </div>

                <!-- Department -->
                <div data-content="department" class="d-none">
                    <?php if ($row_departments) {
                        foreach ($row_departments as $department) { ?>
                            <div class="form-check" style="display:flex;gap:6px">
                                <input class="form-check-input" type="checkbox" name="department"
                                    value="<?= $department['department_id']; ?>"
                                    data-name="<?= $department['department_name']; ?>">
                                <label class="form-check-label"><?= $department['department_name']; ?></label>
                            </div>
                        <?php }
                    }
                    ?>

                </div>

                <!-- Sub-Department -->
                <div data-content="subdepartment" class="d-none">
                    <?php if ($row_sub_departments) {
                        foreach ($row_sub_departments as $sub_departments) { ?>
                            <div class="form-check" style="display:flex;gap:6px">
                                <input class="form-check-input" type="checkbox" name="subdepartment"
                                    value="<?= $sub_departments['sub_department_id']; ?>"
                                    data-name="<?= $sub_departments['sub_department_name']; ?>">
                                <label class="form-check-label"><?= $sub_departments['sub_department_name']; ?></label>
                            </div>
                        <?php }
                    }
                    ?>
                </div>
                <!-- Category -->
                <div data-content="category" class="d-none">
                    <?php if ($row_departments_category) {
                        foreach ($row_departments_category as $category) { ?>
                            <div class="form-check" style="display:flex;gap:6px">
                                <input class="form-check-input" type="checkbox" name="category"
                                    value="<?= $category['category_id']; ?>" data-name="<?= $category['category']; ?>">
                                <label class="form-check-label"><?= $category['category']; ?></label>
                            </div>
                        <?php }
                    }
                    ?>
                </div>

                <!-- Product -->
                <div data-content="product" class="d-none">
                    <?php if ($row_products) {
                        foreach ($row_products as $product) { ?>
                            <div class="form-check" style="display:flex;gap:6px">
                                <input class="form-check-input" type="checkbox" name="product"
                                    value="<?= $product['product_id']; ?>" data-name="<?= $product['product_name']; ?>">
                                <label class="form-check-label"><?= $product['product_name']; ?></label>
                            </div>
                        <?php }
                    }
                    ?>
                </div>

                <!-- Sub-Product -->
                <div data-content="subproduct" class="d-none">
                    <?php if ($row_sub_products) {
                        foreach ($row_sub_products as $sub_product) { ?>
                            <div class="form-check" style="display:flex;gap:6px">
                                <input class="form-check-input" type="checkbox" name="subproduct"
                                    value="<?= $sub_product['sub_product_id']; ?>"
                                    data-name="<?= $sub_product['sub_product_name']; ?>">
                                <label class="form-check-label"><?= $sub_product['sub_product_name']; ?></label>
                            </div>
                        <?php }
                    }
                    ?>
                </div>


                <!-- Specialization -->
                <div data-content="specialization" class="d-none">
                    <?php if ($row_products_specialization) {
                        foreach ($row_products_specialization as $specialization) { ?>
                            <div class="form-check" style="display:flex;gap:6px">
                                <input class="form-check-input" type="checkbox" name="specialization"
                                    value="<?= $specialization['specialization_id']; ?>"
                                    data-name="<?= $specialization['specialization']; ?>">
                                <label class="form-check-label"><?= $specialization['specialization']; ?></label>
                            </div>
                        <?php }
                    }
                    ?>
                </div>

                <!-- Designation -->
                <div data-content="designation" class="d-none">
                    <div class="form-group">
                        <input type="text" class="form-control " id="search_designation-m"
                            placeholder="Select designation here" name="designation" autocomplete="off">
                    </div>
                    <div id="select-designation-div-m" style="display: none;"></div>

                </div>

                <!-- Location -->
                <div data-content="location" class="d-none">
                    <div class="form-group">
                        <div id="keyword-badges-location" class="mb-2"></div>
                        <input type="text" class="form-control" id="search_location-m" placeholder="Select city here"
                            name="location" autocomplete="off">
                        <input type="hidden" id="location_id-m" name="location_id">
                        <div id="select-div-m" style="display: none;"></div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="popup-footer border-top d-flex justify-content-between align-items-center p-2 bg-white">
            <button id="applyFiltersBtn" class="btn btn-warning text-white px-4">Apply</button>
        </div>
    </div>



   <!-- Extra Job Details  -->
    <div id="jobDetailsPopup" class="popup-overlay" style="display: none;">

        <span class="close-popup outter-close-btn" title="Close"><img src="/images/cross-sign-index-smu.svg"
                alt="x"></span>
        <div class="popup-content" style="padding:20px 20px 0px 20px;">
            <span class="close-popup inner-close-btn" title="Close"><img src="/images/cross-sign-index-smu.svg"
                    alt="x"></span>

            <div class="step" id="step-1">
                <div id="popupContent">Loading...</div>
                <div class="me-3 ms-3 ps-3 pt-2 btn-popup-div">
                    <button class="next-step btn btn-success w-100 mb-3" id="apply-job-btn" title="Click to Apply">
                        <img src=/images/codicon_git-stash-apply.svg width="16" height="16" style="margin: 0 4px 4px 0;">
                        Apply for Job</button>
                    <div class="share-ref-block">
                        <button class="btn share-btn" id="share-job-btn">
                            <img src="/images/finploy-share-icon-smu.svg" width= "20px";
                            height="20px"; alt="share" title="Share Job">
                        </button>
                         <button id="candidate-as-partner-after-login"
                        class="btn btn-pimary border border-primary text-primary mb-4 btn-popup-btn2"
                        title="Refer a candidate">
                        <img src=/images/basil_bag-solid.svg width="16" height="16" style="margin: 0 4px 4px 0;">
                        Refer a candidate</button>
                    </div>
                </div>
            </div>
            <div class="step" id="step-2" style="display: none;">
                <div class="mb-3 justify-center text-center">
                    <img class="login-logo" src="assets/finploy-logo.png" alt="FINPLOY" height="40">
                </div>
                <div class="text-center mt-5 mb-4">
                    <img src="assets/party.svg" alt="">
                    <h4 class="step-title mt-3">✨ Congratulations ✨</h4>
                    <p class="text-success mt-3">You have successfully applied for the job</p>
                </div>
                <a class="text-decoration-none continue-btn text-light" href="landingpage.php"><button type="submit"
                        class="btn btn-success w-50 mt-5 mb-5">Continue</button></a>
                <!-- <button class="prev-step">Back</button> -->
            </div>
        </div>
    </div>
    <style>



        #userdataupdate-form {
            background: white;
            padding: 30px;
            width: 415px;
            border-radius: 10px;
            text-align: center;
            position: relative;
            max-height: 100vh;
            z-index: 1050 !important;
            right: 5px;
            position: absolute;
            top: 5px;
            overflow: overlay;
        }

        .form-step-updateuserdata.active {
            display: block;
        }

        .form-step-updateuserdata {
            display: none;
        }

        input.error,
        select.error,
        textarea.error {
            border-color: #dc3545 !important;

        }

        label.error {
            color: #dc3545;
            /* Bootstrap danger red */
            font-size: 0.875rem;
            /* Smaller than normal text */
            margin-top: 0.25rem;
            display: block;
            text-align: left;
        }

        .step-title {
            margin-bottom: 25px;
            color: #4EA647;
            font-weight: bold;
        }

        @media screen and (max-width: 576px) {
            #userdataupdate-form {
                width: 100%;
                right: 0;
                left: 0;
                border-radius: 0;
                /* Optional: remove border-radius on small screens */
            }
        }
    </style>
    <!--For update user datas -->
    <div id="userdataupdate" class="popup-overlay" style="display:none">

        <form id="userdataupdateForm">
            <div id="userdataupdate-form">
                <h4 class="step-title text-center">Provide Details That Help Employers Connect With You Better</h4>

                <div class="form-step-updateuserdata active" id="step-userdataupdate1">
                    <span class="close-popup" style="margin-right: -7px;">&times;</span>
                    <div class="mb-4">
                        <div class="mb-2 text-start">
                            <label class="text-success">Checkmark the Banking Products in which you work
                                Experience ?</label>
                        </div>
                        <div class="row">
                            <?php
                            $total_products = count($row_products);
                            $half = ceil($total_products / 2);
                            $chunked_products = array_chunk($row_products, $half);
                            foreach ($chunked_products as $product_group) {
                                echo '<div class="col-md-6">';
                                foreach ($product_group as $product) {
                                    ?>
                                    <div class="form-check mb-2" style="display: flex; gap: 6px;">
                                        <input class="form-check-input border-primary" type="checkbox" name="products[]"
                                            value="<?= $product['product_id']; ?>" data-name="<?= $product['product_name']; ?>"
                                            style="flex-shrink: 0;">
                                        <label class="form-check-label"
                                            style="text-align: left;"><?= $product['product_name']; ?></label>
                                    </div>

                                    <?php
                                }
                                echo '</div>';
                            }
                            ?>

                        </div>
                        <div id="products-error" class="text-danger error-container"></div>
                    </div>
                    <div class="mb-4" id="sub_products_div" style="display: none;">
                        <div class="mb-2 text-start">
                            <label class="text-success">Sub Products</label>
                        </div>
                        <div class="row" id="sub_products_row">
                        </div>
                        <div id="subproducts-error" class="text-danger error-container"></div>
                    </div>
                    <div class="mb-4" id="specialization_div" style="display: none;">
                        <div class="mb-2 text-start">
                            <label class="text-success">Specialization</label>
                        </div>
                        <div class="row" id="specialization_row">
                        </div>
                    </div>

                    <div class="nav-btn mb-4">
                        <button type="button" class="btn btn-success w-100"
                            onclick="nextuserupdateStep(1)">Continue</button>
                    </div>
                </div>
                <div class="form-step-updateuserdata" id="step-userdataupdate2">
                    <span class="close-popup" style="margin-right: -7px;">&times;</span>
                    <div class="mb-4">
                        <div class="mb-2 text-start">
                            <label class="text-success">Checkmark the Department in which you have work
                                Experience ?</label>
                        </div>
                        <div class="row">
                            <?php
                            $total_departments = count($row_departments);
                            $half = ceil($total_departments / 2);
                            $chunked_departments = array_chunk($row_departments, $half);
                            foreach ($chunked_departments as $department_group) {
                                echo '<div class="col-md-6">';
                                foreach ($department_group as $department) {
                                    ?>
                                    <div class="form-check mb-2" style="display: flex; gap: 6px;">
                                        <input class="form-check-input border-primary" type="checkbox" name="departments[]"
                                            value="<?= $department['department_id']; ?>"
                                            data-name="<?= $department['department_name']; ?>" style="flex-shrink: 0;">
                                        <label class="form-check-label"
                                            style="text-align: left;"><?= $department['department_name']; ?></label>
                                    </div>
                                    <?php
                                }
                                echo '</div>';
                            }
                            ?>

                        </div>
                        <div id="departments-error" class="text-danger error-container"></div>
                    </div>
                    <div class="mb-4" id="sub_departments_div" style="display: none;">
                        <div class="mb-2 text-start">
                            <label class="text-success">Sub Department</label>
                        </div>
                        <div class="row" id="sub_departments_row">
                        </div>
                        <div id="subdepartments-error" class="text-danger error-container"></div>
                    </div>
                    <div class="mb-4" id="category_div" style="display: none;">
                        <div class="mb-2 text-start">
                            <label class="text-success">Category</label>
                        </div>
                        <div class="row" id="category_row">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-primary border-light w-100"
                                onclick="prevuserupdateStep(2)">
                                < Back</button>
                        </div>
                        <div class="col-8">
                            <button type="button" class="btn btn-success w-100"
                                onclick="nextuserupdateStep(2)">Continue</button>
                        </div>
                    </div>
                </div>
                <div class="form-step-updateuserdata" id="step-userdataupdate3">
                    <span class="close-popup" style="margin-right: -7px;">&times;</span>

                    <div class="mb-3">
                        <div class="mb-2 text-start">
                            <label class="text-success">Gender:</label>
                        </div>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input border-primary border-box" type="radio" name="gender"
                                    value="male" required>
                                <label class="form-check-label">Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input border-primary" type="radio" name="gender"
                                    value="female">
                                <label class="form-check-label">Female</label>
                            </div>
                        </div>
                        <div class="error-container"></div>
                    </div>

                    <div class="mb-4">

                        <div class="mb-2 text-start">
                            <label class="text-success">Age:</label>
                        </div>
                        <input type="number" class="form-control login-inputfield" id="age" name="age" required
                            placeholder="Enter your Age">
                        <div class="error-container"></div>
                    </div>
                    <div class="mb-3">
                        <div class="mb-2 text-start">
                            <label class="text-success">Are you Currently Employed?</label>
                        </div>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input border-primary" type="radio" name="employed" value="yes"
                                    required>
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input border-primary" type="radio" name="employed" value="no">
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                        <div class="error-container"></div>
                    </div>
                    <div class="mb-3">
                        <div class="mb-2 text-start">
                            <label class="text-success">Do have any past experience working in Bank / NBFC
                                ?</label>
                        </div>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input border-primary" type="radio" name="bankExperience"
                                    value="yes" required>
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input border-primary" type="radio" name="bankExperience"
                                    value="no">
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                        <div class="error-container"></div>
                    </div>




                    <div class="mb-3">
                        <label class="form-label text-success">Current Location / Preferred Job Location?</label>
                        <input type="text" class="form-control login-inputfield" id="search_location"
                            placeholder="Enter your Current Location" name="location" required autocomplete="off">
                        <div id="select-div" style="display: none;"></div>
                        <div class="error-container"></div>
                    </div>
                    <div class="mb-3">
                        <div class="mb-2 text-start">
                            <label class="form-label text-success">Upload Resume:</label>
                        </div>

                        <input type="file" name="resume" class="form-control login-inputfield" id="resume"
                            accept=".pdf,.doc,.docx" style="height: 38px !important;">
                        <small class="text-muted">Max file size: 5MB</small>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-primary border-light w-100"
                                onclick="prevuserupdateStep(3)">
                                < Back</button>
                        </div>
                        <div class="col-8">
                            <button type="button" class="btn btn-success w-100" id="create-profile"
                                onclick="nextuserupdateStep(3)">Continue</button>
                        </div>
                    </div>
                </div>


            </div>
        </form>
    </div>
    <?php include '../footer.php'; ?>
</body>
<script>
    const notUserDataUpdated = <?= json_encode($NotUserDataUpdated) ?>;
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="../js/index.js"></script>
<script>
    window.addEventListener("load", function () {
        setTimeout(function () {
            document.querySelector(".pagination-div").style.display = "block";
        }, 900);
    });
</script>
<script>

    // --------------- User data popup update -------------

    let currentStep = 1;
    const totalSteps = 3;

    function showStep(step) {
        document.querySelectorAll('.form-step-updateuserdata').forEach(s => s.classList.remove('active'));
        document.getElementById(`step-userdataupdate${step}`).classList.add('active');
    }

    function nextuserupdateStep() {
        if ($("#userdataupdateForm").valid()) {
            currentStep++;
            if (currentStep <= totalSteps) {
                showStep(currentStep);
            }
        }
    }

    function prevuserupdateStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }

    // For product, sub product mapping
    $(document).on("change", 'input[name="products[]"]', function () {
        const changedCheckbox = this;
        const changedValue = changedCheckbox.value;
        const isChecked = changedCheckbox.checked;
        const checkedValues = $('input[name="products[]"]:checked').map(function () {
            return this.value;
        }).get();
        if (!isChecked) {
            checkedValues.push({ id: changedValue, value: 0 });
        }
        const dataToSend = [];

        $('input[name="products[]"]').each(function () {
            if (this.checked) {
                dataToSend.push({ id: this.value, value: this.value });
            } else if (this === changedCheckbox) {
                dataToSend.push({ id: this.value, value: 0 });
            }
        });

        $.ajax({
            url: "fetch_subdetails.php",
            type: "POST",
            data: { selectedProducts: JSON.stringify(dataToSend) },
            success: function (response) {
                if ($.trim(response)) {
                    document.getElementById("sub_products_div").style.display = "block";
                    document.getElementById("specialization_div").style.display = "none";
                    $("#sub_products_row").html(response);
                } else {
                    document.getElementById("sub_products_div").style.display = "none";
                    $("#sub_products_row").html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error: " + error);
                alert("Something went wrong! Please try again.");
            }
        });
    });

    $(document).on("change", 'input[name="sub_products"]', function () {
        const changedCheckbox = this;
        const changedValue = changedCheckbox.value;
        const isChecked = changedCheckbox.checked;
        const checkedValues = $('input[name="sub_products"]:checked').map(function () {
            return this.value;
        }).get();
        if (!isChecked) {
            checkedValues.push({ id: changedValue, value: 0 });
        }
        const dataToSend = [];

        $('input[name="sub_products"]').each(function () {
            if (this.checked) {
                dataToSend.push({ id: this.value, value: this.value });
            } else if (this === changedCheckbox) {
                dataToSend.push({ id: this.value, value: 0 });
            }
        });

        $.ajax({
            url: "fetch_subdetails.php",
            type: "POST",
            data: { selectedSubProducts: JSON.stringify(dataToSend) },
            success: function (response) {
                if ($.trim(response)) {
                    document.getElementById("specialization_div").style.display = "block";
                    $("#specialization_row").html(response);
                } else {
                    document.getElementById("specialization_div").style.display = "none";
                    $("#specialization_row").html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error: " + error);
                alert("Something went wrong! Please try again.");
            }
        });
    });

    // For departments, sub departments mapping
    $(document).on("change", 'input[name="departments[]"]', function () {
        const changedCheckbox = this;
        const changedValue = changedCheckbox.value;
        const isChecked = changedCheckbox.checked;
        const checkedValues = $('input[name="departments[]"]:checked').map(function () {
            return this.value;
        }).get();
        if (!isChecked) {
            checkedValues.push({ id: changedValue, value: 0 });
        }
        const dataToSend = [];
        $('input[name="departments[]"]').each(function () {
            if (this.checked) {
                dataToSend.push({ id: this.value, value: this.value });
            } else if (this === changedCheckbox) {
                dataToSend.push({ id: this.value, value: 0 });
            }
        });
        $.ajax({
            url: "fetch_subdetails.php",
            type: "POST",
            data: { selecteddepartments: JSON.stringify(dataToSend) },
            success: function (response) {
                if ($.trim(response)) {
                    document.getElementById("sub_departments_div").style.display = "block";
                    document.getElementById("category_div").style.display = "none";
                    $("#sub_departments_row").html(response);
                } else {
                    document.getElementById("sub_departments_div").style.display = "none";
                    $("#sub_departments_row").html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error: " + error);
                alert("Something went wrong! Please try again.");
            }
        });
    });

    $(document).on("change", 'input[name="sub_departments"]', function () {
        const changedCheckbox = this;
        const changedValue = changedCheckbox.value;
        const isChecked = changedCheckbox.checked;
        const checkedValues = $('input[name="sub_departments"]:checked').map(function () {
            return this.value;
        }).get();
        if (!isChecked) {
            checkedValues.push({ id: changedValue, value: 0 });
        }
        const dataToSend = [];

        $('input[name="sub_departments"]').each(function () {
            if (this.checked) {
                dataToSend.push({ id: this.value, value: this.value });
            } else if (this === changedCheckbox) {
                dataToSend.push({ id: this.value, value: 0 });
            }
        });

        $.ajax({
            url: "fetch_subdetails.php",
            type: "POST",
            data: { selectedSubdepartments: JSON.stringify(dataToSend) },
            success: function (response) {
                if ($.trim(response)) {
                    document.getElementById("category_div").style.display = "block";
                    $("#category_row").html(response);
                } else {
                    document.getElementById("category_div").style.display = "none";
                    $("#category_row").html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error: " + error);
                alert("Something went wrong! Please try again.");
            }
        });
    });

    // When input is clicked, fetch locations all location
    $(document).ready(function () {
        $(document).on("click", '#search_location', function () {
            const allLocation = $(this).val();
            $.ajax({
                url: 'search_location.php',
                type: 'POST',
                data: { allLocation: allLocation },
                success: function (response) {
                    if ($.trim(response)) {
                        $("#select-div").show().html(response);
                    } else {
                        $("#select-div").hide().html("");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                }
            });
        });

        // Hide suggestion list when clicking outside
        $(document).on("click", function (e) {
            const $target = $(e.target);
            if (
                !$target.closest('#search_location').length &&
                !$target.closest('#select-div').length
            ) {
                $("#select-div").hide().html("");
            }
        });
    });

    // fetch selctecd location
    $(document).on("input", '#search_location', function () {
        const query = $(this).val();
        if (query.length >= 1) {
            $.ajax({
                url: 'search_location.php',
                type: 'POST',
                data: { query: query },
                success: function (response) {
                    if ($.trim(response)) {
                        $("#select-div").show().html(response);
                    } else {
                        $("#select-div").hide().html("");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                }
            });
        } else {
            $("#select-div").hide().html("");
        }
    });


    let locationSelected = false;

    // Set location input
    $(document).on("click", '#list_location', function () {
        const location = $(this).text();
        const location_id = $(this).data('id');
        $('#search_location').val(location);
        $('#search_location').data('location-id', location_id);
        $('#location_id').val(location_id);
        locationSelected = true;
        $('#select-div').hide();
        $('#search_location').css({
            'border-radius': '8px',
            'border-bottom': '1px solid #ccc'
        });

        const validator = $("#userdataupdateForm").validate();
        validator.element("#search_location");

        // Optional: Backend tracking
        $.ajax({
            url: 'search_location.php',
            type: 'POST',
            data: { location: location, location_id: location_id }
        });
    });
    $('#search_location').on('input', function () {
        $('#location_id').val('');
        locationSelected = false;
    });
    $('#search_location').on('blur', function () {
        if (!locationSelected) {
            $('#search_location').val('');
            $('#location_id').val('');
        }
    });


    // ---------------   End Popup userdat update ------------







    // ----------- Loader Show and Hide -----------
    function showLoader() {
        $('.ajax-loader').fadeIn(200);
    }
    function hideLoader() {
        $('.ajax-loader').fadeOut(200);
    }

    // ------------ Toggle Notificaiton Dropdown --------------        
    function toggleDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }



    // ----------------- fetch Full Jobs -------------
    $(document).ready(function () {
        $(document).on("click", "#loadMoreBtn", function () {
            $.ajax({
                url: "fetch_jobs.php",
                type: "POST",
                data: { load_more: true },
                beforeSend: function () {
                    showLoader();
                },
                // beforeSend: function() {
                //     $("#loadMoreBtn").text("Loading...").prop("disabled", true);
                // },
                success: function (response) {
                    $("#job-list").html(response);
                    hideLoader();
                }
            });
        });
    });

    // ----------------- Job details Popup --------------- 
    $(document).on("click", ".job-grid, .job-card-container, .job-bio", function () {
        // alert("Job card clicked!");
        let jobId = $(this).data("id"); // Get job ID
        $("#jobDetailsPopup").fadeIn(); // Show popup

        var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>";
        if (!jobId || !mobileNumber) {
            alert("Missing Job ID or Mobile Number!1");
            return;
        }

        // Fetch job details via AJAX if needed
        $.ajax({
            url: "get_job_details.php",
            type: "POST",
            data: { id: jobId, mobile_number: mobileNumber },
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                $("#popupContent").html(response);
            }
        });
    });
    $(".close-popup").on("click", function () {
        $("#jobDetailsPopup").fadeOut();
    });



    
    // Multi-step navigation
    $(".next-step").on("click", function () {
        $(".step").hide();
        $("#step-2").show();
    });

    $(".prev-step").on("click", function () {
        $(".step").hide();
        $("#step-1").show();
    });

    $(".close-popup").on("click", function () {
        $("#userdataupdate").fadeOut();
    });


    // ----------------- Apply Job  --------------- 


    $(document).ready(function () {
        $(document).on("click", "#job_apply_btn", function () {

            const notUserDataUpdated = <?= json_encode($NotUserDataUpdated) ?>;

            var jobCard = $(this).closest(".job-card");
            var jobId = jobCard.find(".job-grid").attr("data-id");
            var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>";

            var jobCardMobile = $(this).closest(".job-card-wrapper");



            if (!jobId) {
                var jobWrapper = $(this).closest(".job-card-wrapper");
                var jobId = jobWrapper.data("id");
            }

            if (!jobId || !mobileNumber) {
                alert("Missing Job ID or Mobile Number!2");
                return;
            }


            if (notUserDataUpdated === true) {
                $("#userdataupdate").css("display", "block");
            } else {
                $.ajax({
                    url: "apply_job.php",
                    type: "POST",
                    data: { job_id: jobId, mobile_number: mobileNumber },
                    dataType: "json",
                    beforeSend: function () {
                        showLoader();
                    },
                    success: function (response) {
                        if (response.status === "success") {
                            hideLoader();
                            alert("Successfully applied for the job!");
                            jobCard.find('#job_apply_btn').hide();
                            jobCard.find('#allready_apply_btn').show();
                            jobCardMobile.find('#job_apply_btn').hide();
                            jobCardMobile.find('#allready_apply_btn').show();
                        } else {
                            hideLoader();
                            alert("Failed to apply: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        hideLoader();
                        console.log("AJAX Error:", xhr.responseText);
                        alert("Something went wrong! Please try again.");
                    }
                });
            }




            // Initialize validation
            const validator = $("#userdataupdateForm").validate({
                rules: {


                    gender: { required: true },
                    employed: { required: true },


                    "products[]": { required: true, minlength: 1 },
                    "departments[]": { required: true, minlength: 1 },
                    "sub_products": { required: true, minlength: 1 },
                    "sub_departments": { required: true, minlength: 1 },
                },
                messages: {
                    gender: "Please select your gender",
                    age: "Please enter your age",
                    employed: "Please select your employment status",
                    bankExperience: "Please select your banking experience",
                    location: "Please select a valid location from the list",
                    "products[]": "Please select at least one product",
                    "departments[]": "Please select at least one department",
                    "sub_products": "Please select at least one Sub product",
                    "sub_departments": "Please select at least one Sub department",

                },
                errorPlacement: function (error, element) {
                    if (element.attr("name") === "products[]") {
                        error.appendTo("#products-error");
                    } else if (element.attr("name") === "departments[]") {
                        error.appendTo("#departments-error");
                    }
                    else if (element.attr("name") === "sub_products") {
                        error.appendTo("#subproducts-error");

                    } else if (element.attr("name") === "sub_departments") {
                        error.appendTo("#subdepartments-error");

                    }
                    else {
                        var container = element.closest(".mb-3").find(".error-container");
                        if (container.length) {
                            container.html(error);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                }
            });


            $("#create-profile").on("click", function (e) {
                e.preventDefault();

                if (!validator.form()) {
                    return; // Stop if the form is invalid
                }
                let formData = new FormData();

                formData.append("gender", $("input[name='gender']:checked").val());
                formData.append("age", $("#age").val());
                formData.append("employed", $("input[name='employed']:checked").val());
                formData.append("bankExperience", $("input[name='bankExperience']:checked").val());
                formData.append("mobile_number", "<?= $_SESSION['mobile'] ?? '' ?>");

                let products = [], sub_products = [], specialization = [],
                    departments = [], sub_departments = [], category = [];

                $("input[name='products[]']:checked").each(function () {
                    products.push($(this).val());
                });
                formData.append("products", products.join(","));

                $("input[name='sub_products']:checked").each(function () {
                    sub_products.push($(this).val());
                });
                formData.append("sub_products", sub_products.join(","));

                $("input[name='specialization']:checked").each(function () {
                    specialization.push($(this).val());
                });
                formData.append("specialization", specialization.join(","));

                $("input[name='departments[]']:checked").each(function () {
                    departments.push($(this).val());
                });
                formData.append("departments", departments.join(","));

                $("input[name='sub_departments']:checked").each(function () {
                    sub_departments.push($(this).val());
                });
                formData.append("sub_departments", sub_departments.join(","));

                $("input[name='category']:checked").each(function () {
                    category.push($(this).val());
                });
                formData.append("category", category.join(","));

                // Arrays with data-name attributes
                let nameFields = [
                    { name: 'products', arr: [] },
                    { name: 'sub_products', arr: [] },
                    { name: 'specialization', arr: [] },
                    { name: 'departments', arr: [] },
                    { name: 'sub_departments', arr: [] },
                    { name: 'category', arr: [] },
                ];
                nameFields.forEach(field => {
                    $("input[name='" + field.name + "']:checked").each(function () {
                        field.arr.push($(this).data("name"));
                    });
                    formData.append(field.name + "_array", field.arr.join(","));
                });


                formData.append("current_location", $('#search_location').data('location-id'));


                let resumeFile = $("#resume")[0].files[0];
                if (resumeFile) {
                    formData.append("resume", resumeFile);
                }
                // console.log(formData);
                $.ajax({
                    url: "update_candidate_details.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $("#userdataupdate").css("display", "none");
                        $.ajax({
                            url: "apply_job.php",
                            type: "POST",
                            data: { job_id: jobId, mobile_number: mobileNumber },
                            dataType: "json",
                            beforeSend: function () {
                                showLoader();
                            },
                            success: function (response) {
                                if (response.status === "success") {
                                    hideLoader();
                                    alert("Successfully applied for the job!2");
                                    window.location.reload();
                                    // fetchJobs(1);
                                } else {
                                    hideLoader();
                                    alert("Failed to apply: " + response.message);
                                }
                            },
                            error: function (xhr, status, error) {
                                hideLoader();
                                console.log("AJAX Error:", xhr.responseText);
                                alert("Something went wrong! Please try again.");
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error: " + error);
                        alert("Something went wrong! Please try again.");
                    }
                });
            });




        });
    });


    // ----------------- Apply popupjob  --------------- 

    $(document).ready(function () {

        $(document).on("click", ".not_apply_btn", function () {

            var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>"; // Ensure mobile number is set

            jobId = $('#JobID').val();

            if (!jobId || !mobileNumber) {

                alert("Missing Job ID or Mobile Number!3");

                return;

            }



            $.ajax({

                url: "apply_job.php",

                type: "POST",

                data: { job_id: jobId, mobile_number: mobileNumber },

                dataType: "json",

                beforeSend: function () {

                    showLoader();

                },

                success: function (response) {

                    if (response.status === "success") {

                        hideLoader();

                        alert("Successfully applied for the job!3");

                        // fetchJobs(1);

                        $("#step-1").hide();

                        $("#step-2").show();

                    } else {

                        hideLoader();

                        alert("Failed to apply: " + response.message);

                    }

                },

                error: function (xhr, status, error) {

                    hideLoader();

                    console.log("AJAX Error:", xhr.responseText);

                    alert("Something went wrong! Please try again.");

                }

            });

        });

    });

    $(document).ready(function () {
        const pendingJobId = localStorage.getItem('pendingJobApplication');


        if (pendingJobId) {
            const notUserDataUpdated = <?= json_encode($NotUserDataUpdated) ?>;
            var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>";

            console.log(pendingJobId);
            console.log(notUserDataUpdated);

            if (notUserDataUpdated === true) {
                $("#userdataupdate").css("display", "block");
            } else {
                applyForJob(pendingJobId, mobileNumber);
            }

            // Form validation setup
            const validator = $("#userdataupdateForm").validate({
                rules: {
                    gender: { required: true },
                    employed: { required: true },
                    "products[]": { required: true, minlength: 1 },
                    "departments[]": { required: true, minlength: 1 },
                    "sub_products": { required: true, minlength: 1 },
                    "sub_departments": { required: true, minlength: 1 },
                },
                messages: {
                    gender: "Please select your gender",
                    employed: "Please select your employment status",
                    "products[]": "Please select at least one product",
                    "departments[]": "Please select at least one department",
                    "sub_products": "Please select at least one Sub product",
                    "sub_departments": "Please select at least one Sub department",
                },
                errorPlacement: function (error, element) {
                    const fieldMap = {
                        "products[]": "#products-error",
                        "departments[]": "#departments-error",
                        "sub_products": "#subproducts-error",
                        "sub_departments": "#subdepartments-error"
                    };
                    const target = fieldMap[element.attr("name")];
                    if (target) {
                        error.appendTo(target);
                    } else {
                        const container = element.closest(".mb-3").find(".error-container");
                        if (container.length) {
                            container.html(error);
                        } else {
                            error.insertAfter(element);
                        }
                    }
                }
            });

            // Submit user data then apply job
            $("#create-profile").on("click", function (e) {
                e.preventDefault();

                if (!validator.form()) return;

                let formData = new FormData();
                formData.append("gender", $("input[name='gender']:checked").val());
                formData.append("age", $("#age").val());
                formData.append("employed", $("input[name='employed']:checked").val());
                formData.append("bankExperience", $("input[name='bankExperience']:checked").val());
                formData.append("mobile_number", mobileNumber);

                const multiFields = ["products", "sub_products", "specialization", "departments", "sub_departments", "category"];
                multiFields.forEach(name => {
                    let values = [];
                    $(`input[name='${name}[]']:checked, input[name='${name}']:checked`).each(function () {
                        values.push($(this).val());
                    });
                    formData.append(name, values.join(","));

                    // Append labels
                    let names = [];
                    $(`input[name='${name}[]']:checked, input[name='${name}']:checked`).each(function () {
                        names.push($(this).data("name"));
                    });
                    formData.append(`${name}_array`, names.join(","));
                });

                formData.append("current_location", $('#search_location').data('location-id'));

                let resumeFile = $("#resume")[0].files[0];
                if (resumeFile) formData.append("resume", resumeFile);

                $.ajax({
                    url: "update_candidate_details.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $("#userdataupdate").css("display", "none");
                        applyForJob(pendingJobId, mobileNumber); // Apply job after update
                    },
                    error: function (xhr, status, error) {
                        console.error("Profile Update Error:", error);
                        alert("Something went wrong! Please try again.");
                    }
                });
            });

            // Apply Job function
            function applyForJob(jobId, mobile) {
                $.ajax({
                    url: "apply_job.php",
                    type: "POST",
                    data: { job_id: jobId, mobile_number: mobile },
                    dataType: "json",
                    beforeSend: function () {
                        showLoader();
                    },
                    success: function (response) {
                        hideLoader();
                        if (response.status === "success") {
                            alert("Successfully applied for the job!");
                            localStorage.removeItem('pendingJobApplication');
                            window.location.reload();
                        } else {
                            localStorage.removeItem('pendingJobApplication');
                            alert("Failed to apply: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        hideLoader();
                        console.log("AJAX Error:", xhr.responseText);
                        alert("Something went wrong! Please try again.");
                    }
                });
            }
        }
    });






    // ---------------- Insert candidate into Partners Table------------------

    $(document).ready(function () {

        $(document).on("click", "#candidate-as-partnerafter-login, #candidate-to-partner", function () {

            var jobCard = $(this).closest(".job-card");

            var jobId = jobCard.find(".job-grid").attr("data-id");

            var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>";

            if (!jobId || !mobileNumber) {

                alert("Missing Job ID or Mobile Number!");

                return;

            }



            $.ajax({

                url: "insert_partner.php",

                type: "POST",

                data: { job_id: jobId, mobile_number: mobileNumber },

                dataType: "json",

                beforeSend: function () {

                    showLoader();

                },

                success: function (response) {

                    if (response.status === "success") {

                        hideLoader();

                        alert("Successfully Changed as a Partner!");

                        window.location.href = "../partner/refer_candidate.php?jobid=" + jobId;

                    } else {

                        hideLoader();

                        alert("Failed to apply: " + response.message);

                    }

                },

                error: function (xhr, status, error) {

                    hideLoader();

                    console.log("AJAX Error:", xhr.responseText);

                    alert("Something went wrong! Please try again.");

                }

            });

        });

    });



    // -------------- Pagination -------------
    $(document).ready(function () {
        function loadJobs(page) {
            $.ajax({
                url: 'fetch_jobs.php',
                type: 'POST',
                data: {
                    page: page,
                    // filters:filters
                },
                beforeSend: function () {
                    showLoader();
                    // $('#job-container').html('<p>Loading...</p>');
                },
                success: function (response) {
                    hideLoader();
                    $('#job-list').html(response);
                }
            });
        }
        loadJobs(1);
    });

    // ------------ Search Input Placeholder auto change ----------------
    const searchValues = ["jobs", "departments", "companies", "locations"];
    let index = 0;
    function changePlaceholder() {
        const searchInputs = document.querySelectorAll("#search, #searchjob");
        searchInputs.forEach(input => {
            input.setAttribute("placeholder", `Search for '${searchValues[index]}'`);
        });
        index = (index + 1) % searchValues.length;
    }
    // Change placeholder every 2.5 seconds
    setInterval(changePlaceholder, 2500);
    // Show search container when search icon is clicked
    document.getElementById("search-icon-mobile").addEventListener("click", function () {
        document.getElementById("search-container-mobile").style.display = "block";
    });



    // ----------- Filter Toggle ------------
    $(document).ready(function () {
        // Ensure all accordions start closed
        $('.filter-accordion').removeClass('active');
        $('.filter-content').removeClass('active').css('max-height', 0);
        $('.filter-accordion .accordion-arrow').attr('src', 'assets/downward-arrow.svg');
        // Toggle filter accordion sections
        $('.filter-accordion').on('click', function () {
            $(this).toggleClass('active');
            const content = $(this).next('.filter-content');
            const arrowImg = $(this).find('.accordion-arrow');
            content.toggleClass('active');
            if (content.hasClass('active')) {
                content.css('max-height', "200px");
                arrowImg.attr('src', 'assets/upward-arrow.svg');
            } else {
                content.css('max-height', 0);
                arrowImg.attr('src', 'assets/downward-arrow.svg');
            }
        });
    });


    // ----------- Old filters ------------
    function collectFilters() {
        const filters = {};
        if ($('input[name="sort"]:checked').length) {
            filters.sort = $('input[name="sort"]:checked').val();
        } else if ($('#jobsort').val()) {
            filters.sort = $('#jobsort').val();
        } else if ($('#sort-relevance').is(':checked')) {
            filters.sort = 'relevance';
        } else if ($('#sort-salary').is(':checked')) {
            filters.sort = 'salary';
        } else if ($('#sort-date').is(':checked')) {
            filters.sort = 'date';
        }

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
    // ------------ New Added Filters -----------
    // ------------ Function to add a keyword badge And Remove bage ------------------
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
    // Trigger removeBadge
    $(document).on('click', '.remove-badge', function () {
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
        fetchFilteredJobs(1);
    });
    // ------------End Function to add a keyword badge And Remove bage ----------------

    //------------ Fetch searched location and Filter useing location -----------------
    $(document).on("input", '#search_location', function () {
        const query = $(this).val();
        if (query.length >= 1) {
            $('#select-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
            $.ajax({
                url: '../candidate/search_location.php',
                type: 'POST',
                data: { query: query },
                success: function (response) {
                    if ($.trim(response)) {
                        $("#select-div").show().html(response);
                    } else {
                        $("#select-div").hide().html("");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                }
            });
        } else {
            $("#select-div").hide().html("");
        }
    });
    // Set input value and trigger function 
    $(document).on("click", '#list_location', function () {
        const label = $(this).text();
        const location_id = $(this).data('id');
        $('#search_location').val(label);
        addBadge(location_id, "location", label);
        $('#select-div').hide();
        $('#search_location').val('');
        fetchFilteredJobs(1);
    });
    // Target remover
    $(document).on("click", function (event) {
        const $target = $(event.target);
        if (!$target.closest('#select-div').length && !$target.is('#search_location')) {
            $('#select-div').hide();
        }
    });
    //------------ End Fetch searched location and Filter useing location -----------------
    //------------ Fetch searched location and Filter useing location For mobile-----------------
    $(document).on("input", '#search_location-m', function () {
        const query = $(this).val();
        if (query.length >= 1) {
            $('#select-div-m').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
            $.ajax({
                url: '../candidate/search_location.php',
                type: 'POST',
                data: { query: query },
                success: function (response) {
                    if ($.trim(response)) {
                        $("#select-div-m").show().html(response);
                    } else {
                        $("#select-div-m").hide().html("");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                }
            });
        } else {
            $("#select-div-m").hide().html("");
        }
    });
    // Set input value and trigger function 
    $(document).on("click", '#list_location', function () {
        const label = $(this).text();
        const location_id = $(this).data('id');
        $('#search_location-m').val(label);
        addBadge(location_id, "location", label);
        $('#select-div-m').hide();
        $('#search_location-m').val('');
        fetchFilteredJobs(1);
    });
    // Target remover
    $(document).on("click", function (event) {
        const $target = $(event.target);
        if (!$target.closest('#select-div-m').length && !$target.is('#search_location-m')) {
            $('#select-div-m').hide();
        }
    });
    //------------ End Fetch searched location and Filter useing location For mobile -----------------

    //------------ Search product and department and subproduct and subdepartment ---------------
    function setupSearch(inputSelector, dataKey, resultDivSelector) {
        let debounceTimer;
        $(inputSelector).on('input', function () {
            const input = $(this).val();
            const $resultDiv = $(resultDivSelector);
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                $resultDiv.html('<p style="padding-top: 10px;text-align: center;color: #999;">Searching...</p>').show();

                const ajaxStartTime = Date.now();

                $.ajax({
                    url: '../employer_flow/serach_sub_data.php',
                    type: 'POST',
                    data: { [dataKey]: input },
                    success: function (response) {
                        const elapsed = Date.now() - ajaxStartTime;
                        const remainingTime = 400 - elapsed;
                        if (remainingTime > 0) {
                            setTimeout(() => {
                                $resultDiv.html(response);
                            }, remainingTime);
                        } else {
                            $resultDiv.html(response);
                        }
                    },
                    error: function () {
                        $resultDiv.html('<p style="padding: 10px;text-align: center;color: red;">Search failed. Please try again.</p>');
                    }
                });
            }, 500);
        });
    }
    setupSearch('#departmentsearch', 'departmentsearch', '#department-list-div');
    setupSearch('#subdepartmentsearch', 'subdepartmentsearch', '#subdepartment-list-div');
    setupSearch('#categorysearch', 'categorysearch', '#category-list-div');
    setupSearch('#productsearch', 'productsearch', '#product-list-div');
    setupSearch('#subproductsearch', 'subproductsearch', '#subproduct-list-div');
    setupSearch('#specializationsearch', 'specializationsearch', '#specialization-list-div');
    //------------ End Search product and department and subproduct and subdepartment ---------------

    //------------ Fetch Fetch searched designation -----------------
    $(document).on("input", '#search_designation', function () {
        const query = $(this).val();
        if (query.length >= 1) {
            $('#select-designation-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
            $.ajax({
                url: '../employer_flow/search_designation.php',
                type: 'POST',
                data: { query: query },
                success: function (response) {
                    if ($.trim(response)) {
                        $("#select-designation-div").show().html(response);
                    } else {
                        $("#select-designation-div").hide().html("");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                }
            });
        } else {
            $("#select-designation-div").hide().html("");
        }
    });
    // Set input value and trigger function 
    $(document).on("click", '#list_designation', function () {
        const label = $(this).text();
        $('#search_designation').val(label);
        addBadge(label, "designation", label);
        $('#select-designation-div').hide();
        $('#search_designation').val('');
        fetchFilteredJobs(1);
    });
    // Target remover
    $(document).on("click", function (event) {
        const $target = $(event.target);
        if (!$target.closest('#select-div').length && !$target.is('#search_designation')) {
            $('#select-designation-div').hide();
        }
    });
    //------------ End Fetch searched designation -----------------

    //------------ Fetch searched designation For Mobile -----------------
    $(document).on("input", '#search_designation-m', function () {
        const query = $(this).val();
        if (query.length >= 1) {
            $('#select-designation-div-m').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
            $.ajax({
                url: '../employer_flow/search_designation.php',
                type: 'POST',
                data: { query: query },
                success: function (response) {
                    if ($.trim(response)) {
                        $("#select-designation-div-m").show().html(response);
                    } else {
                        $("#select-designation-div-m").hide().html("");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", status, error);
                }
            });
        } else {
            $("#select-designation-div-m").hide().html("");
        }
    });
    // Set input value and trigger function 
    $(document).on("click", '#list_designation', function () {
        const label = $(this).text();
        $('#search_designation-m').val(label);
        addBadge(label, "designation", label);
        $('#select-designation-div-m').hide();
        $('#search_designation-m').val('');
        fetchFilteredJobs(1);
    });
    // Target remover
    $(document).on("click", function (event) {
        const $target = $(event.target);
        if (!$target.closest('#select-div').length && !$target.is('#search_designation-m')) {
            $('#select-designation-div-m').hide();
        }
    });
    //------------ End Fetch searched designation For Mobile -----------------
    // ------------Fetch selected values -----------
    function getSelectedKeysValues() {
        const KeysValues = [];
        $('.keyword-filter-badge').each(function () {
            const key = $(this).data('key');
            const value = $(this).data('value');
            KeysValues.push({ key: key, value: value });
        });
        return KeysValues;
    }
    // ------------ End Fetch selected values -----------

    // ----------- Deparments Products Sub Products,Departments Filter -------
    $(document).on('change', 'input[name="department"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "department", label);
        } else {
            removeBadge(this.value, "department", label)
        }
        fetchFilteredJobs(1);
    });
    $(document).on('change', 'input[name="subdepartment"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "subdepartment", label);
        } else {
            removeBadge(this.value, "subdepartment", label)
        }
        fetchFilteredJobs(1);
    });
    $(document).on('change', 'input[name="category"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "category", label);
        } else {
            removeBadge(this.value, "category", label)
        }
        fetchFilteredJobs(1);
    });
    $(document).on('change', 'input[name="product"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "product", label);
        } else {
            removeBadge(this.value, "product", label)
        }
        fetchFilteredJobs(1);
    });
    $(document).on('change', 'input[name="subproduct"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "subproduct", label);
        } else {
            removeBadge(this.value, "subproduct", label)
        }
        fetchFilteredJobs(1);
    });
    $(document).on('change', 'input[name="specialization"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "specialization", label);
        } else {
            removeBadge(this.value, "specialization", label)
        }
        fetchFilteredJobs(1);
    });

    // Function to fetch jobs with all filters
    function fetchFilteredJobs(page = 1) {
        const filters = collectFilters();
        const values = getSelectedKeysValues();
        values.forEach(({ key, value }) => {
            if (!filters[`${key}[]`]) {
                filters[`${key}[]`] = [];
            }
            filters[`${key}[]`].push(value);
        });
        filters.page = page;
        // console.log("Sending filters:", filters);
        showLoader();
        $.ajax({
            url: 'fetch_jobs.php',
            type: 'POST',
            data: filters,
            success: function (response) {
                $('#job-list').html(response);
                hideLoader();
            },
            error: function (xhr, status, error) {
                console.error("Filter request failed:", error);
                $('#job-list').html('<p class="text-danger">Failed to load jobs. Please try again later.</p>');
                hideLoader();
            }
        });
    }

    // Event bindings
    $(document).ready(function () {
        fetchFilteredJobs();

        // Web filters
        $(document).on('change', 'input[type="checkbox"]', function () {
            fetchFilteredJobs(1);
        });

        $('#other-department, #other-sub-department, #other-product, #other-sub-product, #other-designation').on('input', function () {
            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function () {
                fetchFilteredJobs(1);
            }, 500));
        });

        $('#jobsort, #jobdepartment, #job-sub-department, #job-product, #location, #joblocation, #per-page-list').on('change', function () {
            fetchFilteredJobs(1);
        });

        $('#min_salary, #max_salary, #jobmin_salary, #jobmax_salary').on('input', function () {
            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function () {
                fetchFilteredJobs(1);
            }, 500));
        });

        $('#search, #searchjob').on('input', function () {
            clearTimeout($(this).data('timeout'));
            $(this).data('timeout', setTimeout(function () {
                fetchFilteredJobs(1);
            }, 300));
        });

        // Pagination
        $(document).on('click', '.linkForPage', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            fetchFilteredJobs(page);
        });

        // Mobile filter checkboxes
        $('#mobileFilterPopup input[type="checkbox"]').on('change', function () {
            fetchFilteredJobs(1);
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

    // --------------- Mobile Filter Popup ---------------
    document.addEventListener('DOMContentLoaded', function () {
        const popup = document.getElementById('mobileFilterPopup');
        const filterBtn = document.querySelector('#filters-section-mobile button');
        const closeBtn = document.getElementById('closeFilterPopup');
        const clearBtn = document.getElementById('clearFiltersBtn');
        const applyBtn = document.getElementById('applyFiltersBtn');
        // Show popup
        filterBtn.addEventListener('click', () => popup.classList.remove('d-none'));
        // Close popup
        closeBtn.addEventListener('click', () => popup.classList.add('d-none'));
        // Clear filters
        clearBtn.addEventListener('click', () => {
            popup.querySelectorAll('input[type=checkbox]').forEach(chk => chk.checked = false);
        });
        // Apply filters
        applyBtn.addEventListener('click', () => {
            // Trigger your filter logic here...
            popup.classList.add('d-none');
        });
        // Handle category tab switching
        document.querySelectorAll('.filter-categories .list-group-item').forEach(item => {
            item.addEventListener('click', function () {
                document.querySelectorAll('.filter-categories .list-group-item').forEach(li => li.classList.remove('active'));
                this.classList.add('active');

                const category = this.dataset.category;
                document.querySelectorAll('#filterOptionsContent > div').forEach(content => {
                    content.classList.toggle('d-none', content.dataset.content !== category);
                });
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileToggle = document.getElementById('dropdownMenuLink');
        const profileDropdown = document.getElementById('custom-dropdown');

        profileToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!profileDropdown.contains(e.target) && !profileToggle.contains(e.target)) {
                profileDropdown.classList.remove('show');
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




     // Refer Candidate Functionality
    $(document).ready(function () {
        // console.log("Refer candidate handler initialized");

        // Remove old handlers if any to avoid duplication
        $(document).off('click', '#candidate-as-partner, #candidate-to-partner');

        // Add click handler for refer-candidate button
        $(document).on("click", "#candidate-as-partner, #candidate-to-partner", function () {
            var jobCard = $(this).closest(".job-card");
            var jobId = jobCard.find(".job-grid").attr("data-id");

            // If not found, try hidden input
            if (!jobId) {
                jobId = $('#JobID').val();
            }

            if (!jobId) {
                alert("Job ID not found!");
                return;
            }

            // console.log("Refer as Partner clicked for Job ID:", jobId);

            // Store the Job ID for later use after login
            localStorage.setItem('pendingReferPartnerJobId', jobId);

            // Check login status via AJAX
            $.ajax({
                url: 'check_login_status.php',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    // console.log("Login status:",p response);

                    if (response.loggedIn) {
                        // console.log("User is logged in, referring directly with mobile:", response.mobile);
                        referAsPartner(jobId, response.mobile);
                    } else {
                        // console.log("User not logged in, loading login form");
                        loadLoginForm('partner');
                    }
                },
                error: function (xhr, status, error) {
                    // console.log("Login status check error:", error);
                    alert('Failed to check login status. Please try again.');
                }
            });
        });

        // Listen for successful login events
        $(document).on('loginSuccess', function (e, userData) {
            var pendingJobId = localStorage.getItem('pendingReferPartnerJobId');
            if (pendingJobId) {
                referAsPartner(pendingJobId, userData.mobile);
                localStorage.removeItem('pendingReferPartnerJobId');
            }
        });
    });
</script>


</body>

</html>