<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Uncomment if session check is required
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    die();
}
include 'partner_header.php';
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
<style>
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0;
        width: 100%;
        margin-top: 11px;
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
                <h5 class="filter-header"><img src="../candidate/assets/filter-icon.svg" alt="">All Filters <a
                        href="/index.php" class="clear-all-filter" style="display:none">Clear All</a></h5>
                <hr class="filter-line">
                <div id="keyword-badges-sort" class="mb-2 keyword-badges-filter"></div>
                <div class="mb-3 filter-div-sort">
                    <h6 class="filter-accordion">Sort By <img class="accordion-arrow"
                            src="../candidate/assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
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
                            src="../candidate/assets/downward-arrow.svg" alt="Toggle Arrow"></h6>

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
                            src="../candidate/assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
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
                            src="../candidate/assets/downward-arrow.svg" alt="Toggle Arrow"></h6>

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
                            src="../candidate/assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
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
                            src="../candidate/assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
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
                            src="../candidate/assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
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
                    <img src="../candidate/assets/filter-icon.svg" alt="Filter Icon" class="me-2" style="width: 20px;">
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
                        <img src=/images/codicon_git-stash-apply.svg width="16" height="16"
                            style="margin: 0 4px 4px 0;">
                        Apply for Job</button>
                    <button id="candidate-as-partner-after-login"
                        class="btn btn-pimary border border-primary text-primary w-100 mb-4 btn-popup-btn2"
                        title="Refer a candidate">
                        <img src=/images/basil_bag-solid.svg width="16" height="16" style="margin: 0 4px 4px 0;">
                        Refer a candidate</button>
                </div>
            </div>

            <div class="step" id="step-2" style="display: none;">
                <div class="mb-3 justify-center text-center">
                    <img class="login-logo" src="../candidate/assets/finploy-logo.png" alt="FINPLOY" height="40">
                </div>
                <div class="text-center mt-5 mb-4">
                    <img src="../candidate/assets/party.svg" alt="">
                    <h4 class="step-title mt-3">✨ Congratulations ✨</h4>
                    <p class="text-success mt-3">You have successfully applied for the job</p>
                </div>
                <a class="text-decoration-none text-light" href="index.php"><button type="submit"
                        class="btn btn-success w-50 mt-5 mb-5">Continue</button></a>
                <!-- <button class="prev-step">Back</button> -->
            </div>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function showLoader() {
        $('.ajax-loader').fadeIn(200);
    }

    function hideLoader() {
        $('.ajax-loader').fadeOut(200);
    }

    function fetchJobs() {
        // Create an empty object to store filters dynamically
        const filters = {};

        // Fetch values from all filter elements
        const sortValue = $('#sort').val() || $('#jobsort').val();
        const locationValue = $('#location').val() || $('#joblocation').val();
        const minSalaryValue = $('#min_salary').val() || $('#jobmin_salary').val();
        const maxSalaryValue = $('#max_salary').val() || $('#jobmax_salary').val();
        const departmentValue = $('#department').val() || $('#jobdepartment').val();
        const searchValue = $('#searchjob').val() || $('#search').val(); // Added search filter

        // Add values to the filters object only if they exist (not empty)
        if (sortValue) filters.sort = sortValue;
        if (locationValue) filters.location = locationValue;
        if (minSalaryValue) filters.min_salary = minSalaryValue;
        if (maxSalaryValue) filters.max_salary = maxSalaryValue;
        if (departmentValue) filters.department = departmentValue;
        if (searchValue) filters.search = searchValue; // Add search filter

        // console.log(filters); // Debugging: Check what filters are being sent

        // AJAX request to fetch jobs
        $.ajax({
            url: 'fetch_jobs.php',
            type: 'POST',
            data: filters, // Send only active filters
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                $('#job-list').html(response);
            },
            error: function () {
                hideLoader();
                $('#job-list').html('<p class="text-danger">Failed to load jobs. Please try again later.</p>');
            },
        });
    }

    // Fetch jobs on page load and on filter change
    $(document).ready(function () {
        fetchJobs(); // Initial fetch

        // Update job list whenever a filter is changed
        $('#sort, #jobsort, #location, #joblocation, #min_salary, #jobmin_salary, #max_salary, #jobmax_salary, #department, #jobdepartment').on('change', fetchJobs);

        // Call AJAX when the user enters salary values
        $('#min_salary, #jobmin_salary, #max_salary, #jobmax_salary').on('input', fetchJobs);

        // Call AJAX when the user types in the search field (added search functionality)
        $('#searchjob, #search').on('input', function () {
            fetchJobs();
        });
    });



    /////////////////////////////////////// Toggle Notificaiton Dropdown ///////////////////////////////////////        
    function toggleDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    /////////////////////////////////////// fetch Location ///////////////////////////////////////
    $.ajax({
        url: 'fetch_locations.php',
        type: 'GET',
        beforeSend: function () {
            showLoader();
        },
        success: function (locations) {
            // console.log(locations);
            if (typeof locations === 'string') {
                try {
                    hideLoader();
                    locations = JSON.parse(locations);
                } catch (e) {
                    hideLoader();
                    console.error('Invalid JSON response:', locations);
                    return;
                }
            }

            if (!Array.isArray(locations)) {
                console.error('Response is not an array:', locations);
                return;
            }

            // Sort locations alphabetically
            locations.sort();

            // Select both dropdowns
            const locationDropdown = $('#location');
            const jobLocationDropdown = $('#joblocation');

            // Empty existing options
            locationDropdown.empty();
            jobLocationDropdown.empty();

            // Add default option
            const defaultOption = '<option value="">Select city</option>';
            locationDropdown.append(defaultOption);
            jobLocationDropdown.append(defaultOption);

            // Populate both dropdowns
            locations.forEach(location => {
                const option = `<option value="${location}">${location}</option>`;
                locationDropdown.append(option);
                jobLocationDropdown.append(option);
            });
        },
        error: function (xhr, status, error) {
            hideLoader();
            console.error('Failed to fetch locations:', error);
        },
    });


    /////////////////////////////////////// fetch Full Jobs ////////////
    $(document).ready(function () {
        $(document).on("click", "#loadMoreBtn", function () {
            $.ajax({
                url: "fetch_jobs.php",
                type: "POST",
                data: { load_more: true },
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    $("#job-list").html(response); // Replace content with full job list
                }
            });
        });
    });

    $(document).on("click", ".job-grid, .job-card-container, .job-bio", function () {
        // alert("Job card clicked!");
        let jobId = $(this).data("id"); // Get job ID
        $("#jobDetailsPopup").fadeIn(); // Show popup

        // Fetch job details via AJAX if needed
        $.ajax({
            url: "get_job_details.php",
            type: "POST",
            data: { id: jobId },
            success: function (response) {
                $("#popupContent").html(response); // Update popup content
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
    // ////////////// Apply Job ///////////////
    $(document).ready(function () {
        $(document).on("click", "#apply-job-partner, #partner-apply-job", function () {
            var jobCard = $(this).closest(".job-card");
            var jobId = jobCard.find(".job-grid").attr("data-id"); // Get Job ID
            var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>"; // Ensure mobile number is set

            // console.log("Job ID:", jobId, "Mobile:", mobileNumber); // Debugging Output

            if (!jobId || !mobileNumber) {
                alert("Missing Job ID or Mobile Number!");
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
                        alert("Successfully applied for the job!");
                        fetchJobs()
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

    // //////////// Search Bar Placeholder ///////////
    const searchValues = ["jobs", "departments", "companies", "locations"];

    let index = 0;
    function changePlaceholder() {
        const searchInput = document.getElementById("search");
        searchInput.setAttribute("placeholder", `Search for ' ${searchValues[index]} '`);
        index = (index + 1) % searchValues.length; // Loop through the array
    }

    // Change placeholder every 3 seconds
    setInterval(changePlaceholder, 2500);

    // Show search container when search icon is clicked
    document.getElementById("search-icon-mobile").addEventListener("click", function () {
        document.getElementById("search-container-mobile").style.display = "block";
    });

</script>


<script>

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

       // ----------- Filter Toggle ------------
    $(document).ready(function () {
        // Ensure all accordions start closed
        $('.filter-accordion').removeClass('active');
        $('.filter-content').removeClass('active').css('max-height', 0);
        $('.filter-accordion .accordion-arrow').attr('src', '../candidate/assets/downward-arrow.svg');
        // Toggle filter accordion sections
        $('.filter-accordion').on('click', function () {
            $(this).toggleClass('active');
            const content = $(this).next('.filter-content');
            const arrowImg = $(this).find('.accordion-arrow');
            content.toggleClass('active');
            if (content.hasClass('active')) {
                content.css('max-height', "200px");
                arrowImg.attr('src', '../candidate/assets/upward-arrow.svg');
            } else {
                content.css('max-height', 0);
                arrowImg.attr('src', '../candidate/assets/downward-arrow.svg');
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



<?php include '../footer.php'; ?>
</body>

</html>