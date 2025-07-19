<?php
include 'redirect_check.php';
include 'header.php';
include 'db/connection.php';


// get seo url 
// location
$seo_city = $_GET['seo_city'] ?? null;
$seolabel = "";
$seolocation_id = "";
if ($seo_city) {
    $seo_city = $conn->real_escape_string($seo_city);
    $sql = "SELECT id, area, city, state, city_wise_id, state_wise_id 
            FROM locations 
            WHERE city = '$seo_city' AND area = '$seo_city' 
            LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $seolabel = "{$row['area']}, {$row['city']}, {$row['state']}";
        $seolocation_id = $row['id'];
    }
}
// designation,companyname,department,sub-department,product....
$seo_url = $_GET['route'] ?? null;

$seo_designation = null;
$seo_companyname = null;

$seo_product = null;
$seo_sub_product = null;
$seo_specialization = null;
$seo_department = null;
$seo_sub_department = null;
$seo_category = null;
$seo_product_id = null;
$seo_sub_product_id = null;
$seo_specialization_id = null;
$seo_department_id = null;
$seo_sub_department_id = null;
$seo_category_id = null;

if ($seo_url) {
    $seo_url_format = ucwords(str_replace('-', ' ', $seo_url));

    // job_id -> jobrole (designation)
    $stmt = $conn->prepare("SELECT jobrole FROM job_id WHERE jobrole = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $seo_designation = $seo_url_format;
    }
    $stmt->close();

    // job_id -> companyname
    $stmt = $conn->prepare("SELECT companyname FROM job_id WHERE companyname = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $seo_companyname = $seo_url_format;
    }
    $stmt->close();

    // products -> product_name
    $stmt = $conn->prepare("SELECT product_id FROM products WHERE product_name = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seo_product = $seo_url_format;
        $seo_product_id = $row['product_id'];
    }
    $stmt->close();

    // sub_products -> sub_product_name
    $stmt = $conn->prepare("SELECT sub_product_id FROM sub_products WHERE sub_product_name = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    $result = $stmt->get_result(); // <-- You were missing this line!
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seo_sub_product = $seo_url_format;
        $seo_sub_product_id = $row['sub_product_id'];
    }
    $stmt->close();

    // products_specialization -> specialization
    $stmt = $conn->prepare("SELECT specialization_id FROM products_specialization WHERE specialization = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seo_specialization = $seo_url_format;
        $seo_specialization_id = $row['specialization_id'];
    }
    $stmt->close();

    // departments -> department_name
    $stmt = $conn->prepare("SELECT department_id FROM departments WHERE department_name = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seo_department = $seo_url_format;
        $seo_department_id = $row['department_id'];
    }
    $stmt->close();

    // sub_departments -> sub_department_name
    $stmt = $conn->prepare("SELECT sub_department_id FROM sub_departments WHERE sub_department_name = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seo_sub_department = $seo_url_format;
        $seo_sub_department_id = $row['sub_department_id'];
    }
    $stmt->close();

    // departments_category -> category
    $stmt = $conn->prepare("SELECT category_id FROM departments_category WHERE category = ?");
    $stmt->bind_param("s", $seo_url_format);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seo_category = $seo_url_format;
        $seo_category_id = $row['category_id'];
    }
    $stmt->close();

}


// Load dropdown filters for product, subproduct, department, subdepartment.. 
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

$sql = "SELECT id,area,city,state,city_wise_id,state_wise_id FROM locations ORDER BY city ,area";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_locations = $result->fetch_all(MYSQLI_ASSOC);
}

// Fect Jobrole and company from job_id table 
$sql = "SELECT jobrole,companyname  FROM job_id ORDER BY jobrole ,companyname";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_job_id = $result->fetch_all(MYSQLI_ASSOC);
}

// Manually Share job URL from jobid table 
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
?>

<body>
    <div id="modal-placeholder"></div>
    <!-- Main Div  -->
    <div class="container mt-4 mb-5">
        <!-- Search and Heding Div -->
        <div class="job-liting-page">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="jobs-page-title">Showing All Bank, Finance And Insurance Jobs</h5>
                <div class="search-container" style=" padding-right: 13px;" id="search-container-web">
                    <img src="/images/search-index-icon-smu.svg" alt="" width="20" height="21">
                    <input type="text" id="search" class="form-control" placeholder="Search for ' location '"
                        autocomplete="off" title="Search for Results">
                    <!-- Serach engine tooltip -->
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

        <!-- Inside Div 1  -->
        <div class="row">
            <!-- Filters Div -->
            <div class="col-md-3 filters-section" id="filters-section-web">
                <div>
                    <h5 class="filter-header"><img src="assets/filter-icon.svg" alt="">All Filters <a href="/index.php"
                            class="clear-all-filter" style="display:none">Clear All</a></h5>
                    <div id="keyword-badges-companyname" class="mb-2 keyword-badges-filter"></div>
                    <hr class="filter-line">
                </div>
                <div class="inside-scroll">
                    <!-- Sort By Filter -->
                    <div id="keyword-badges-sort" class="mb-2 keyword-badges-filter"></div>
                    <div class="mb-3 filter-div-sort">
                        <h6 class="filter-accordion">Sort By <img class="accordion-arrow"
                                src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
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
                    <!-- Department Filter -->
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
                                        <label
                                            class="filter-lable checkbox-label"><?= $department['department_name']; ?></label>
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
                    <hr class="filter-line">
                    <!-- SubDepartment Filter -->
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
                            <input id="subdepartmentsearch" class="form-control" type="search"
                                name="subdepartmentsearch" placeholder="Search Sub-Department" aria-label="Search">
                        </div>
                    </div>
                    <hr class="filter-line">
                    <!-- Category Filter -->
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
                    <!-- Product Filter  -->
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
                    <!-- Subproduct Filter  -->
                    <div id="keyword-badges-subproduct" class="mb-2"></div>
                    <div class="mb-3 filter-div">
                        <h6 class="filter-accordion subpro-head">Sub-Product <img class="accordion-arrow"
                                src="assets/downward-arrow.svg" alt="Toggle Arrow"></h6>
                        <div class="filter-content" id="subproduct-list-div"
                            style="max-height: 200px; overflow-y: auto;">
                            <?php if ($row_sub_products) {
                                foreach ($row_sub_products as $sub_product) { ?>
                                    <div class="custom-checkbox checkbox-item" style="display:flex;gap:6px">
                                        <input type="checkbox" class="checkbox-input" name="subproduct"
                                            value="<?= $sub_product['sub_product_id']; ?>"
                                            data-name="<?= $sub_product['sub_product_name']; ?>">
                                        <label
                                            class="filter-lable checkbox-label"><?= $sub_product['sub_product_name']; ?></label>
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
                    <!-- Specialization Filter  -->
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
                                        <label
                                            class="filter-lable checkbox-label"><?= $specialization['specialization']; ?></label>
                                    </div>
                                <?php }
                            }
                            ?>
                        </div>
                        <div id="specialization-searchlist-div" style="display: none;"></div>
                        <div class="custom-checkbox">
                            <input id="specializationsearch" class="form-control" type="search"
                                name="specializationsearch" placeholder="Search Specialization" aria-label="Search">
                        </div>
                    </div>
                    <hr class="filter-line">
                    <!-- Designation Filter  -->
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
                    <!-- Location Filter  -->
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
                    <!-- Salary Filter -->
                    <div class="mb-3 filter-div">
                        <h6>Salary (In LPA)</h6>
                        <div class="input-group">
                            <input type="number" id="min_salary" class="form-control" placeholder="Min">
                            <input type="number" id="max_salary" class="form-control" placeholder="Max">
                        </div>
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

                <?php if ($job_id <= 0): ?>
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
                <?php endif; ?>
            </div>
        </div>

        <div class="seo-links-div">
            <div class="seo-section">
                <p class="seo-links-heads">
                    Jobs by location
                    <img src="/images/tdesign_plus.svg" alt="plus" class="toggle-plus me-2" width="24" height="24"
                        style="float: right;">
                    <img src="/images/tdesign_minus.svg" alt="minus" class="toggle-minus me-2" width="24" height="24"
                        style="float: right; display: none;">

                </p>
                <hr class="hr-line" style="display: none;">
                <div class="jobs-location-div" style="display: none;">
                    <?php
                    if ($row_locations) {
                        $printed_cities = [];

                        // Get unique cities
                        foreach ($row_locations as $location) {
                            if (!in_array($location['city'], $printed_cities)) {
                                $printed_cities[] = $location['city'];
                            }
                        }

                        // Chunk cities into columns of 6 items each and limit to 4 columns
                        $chunks = array_chunk($printed_cities, 6);
                        $chunks = array_slice($chunks, 0, 4);

                        foreach ($chunks as $column_data) {
                            echo '<ul class="jobs-location-ul">';
                            foreach ($column_data as $city) {
                                $city_wise_id = '';
                                foreach ($row_locations as $loc) {
                                    if ($loc['city'] === $city) {
                                        $city_wise_id = $loc['city_wise_id'];
                                        break;
                                    }
                                }
                                $url = '/jobs-in-' . strtolower(str_replace(' ', '-', $city));

                                echo "<li class='seo-links'><a href='{$url}'>Jobs in {$city}</a></li>";
                            }
                            echo '</ul>';
                        }
                    }
                    ?>

                </div>
                <hr class="hr-line-bottom" style="display: none;">
                <div class="search-by-viewmore-div" style="display: none;">
                    <p class="search-by-viewmore-word active">Search by</p>
                    <p class="search-by-viewmore-word">A</p>
                    <p class="search-by-viewmore-word">B</p>
                    <p class="search-by-viewmore-word">C</p>
                    <p class="search-by-viewmore-word">D</p>
                    <p class="search-by-viewmore-word">E</p>
                    <p class="search-by-viewmore-word">F</p>
                    <p class="search-by-viewmore-word">G</p>
                    <p class="search-by-viewmore-word">H</p>
                    <p class="search-by-viewmore-word">I</p>
                    <p class="search-by-viewmore-word">J</p>
                    <p class="search-by-viewmore-word">K</p>
                    <p class="search-by-viewmore-word">L</p>
                    <p class="search-by-viewmore-word">M</p>
                    <p class="search-by-viewmore-word">N</p>
                    <p class="search-by-viewmore-word">O</p>
                    <p class="search-by-viewmore-word">P</p>
                    <p class="search-by-viewmore-word">Q</p>
                    <p class="search-by-viewmore-word">R</p>
                    <p class="search-by-viewmore-word">S</p>
                    <p class="search-by-viewmore-word">T</p>
                    <p class="search-by-viewmore-word">U</p>
                    <p class="search-by-viewmore-word">V</p>
                    <p class="search-by-viewmore-word">W</p>
                    <p class="search-by-viewmore-word">X</p>
                    <p class="search-by-viewmore-word">Y</p>
                    <p class="search-by-viewmore-word">Z</p>
                    <p class="search-by-viewmore-word">&#8377;</p>
                    <p class="search-by-viewmore-word">0-9</p>
                    <a href="/browse_jobs.php?flow=jobs-in-location" class="search-by-viewmore-btn">View More</a>
                </div>
            </div>

            <div class="seo-section">
                <p class="seo-links-heads">
                    Start Hiring
                    <img src="/images/tdesign_plus.svg" alt="plus" class="toggle-plus me-2" width="24" height="24"
                        style="float: right;">
                    <img src="/images/tdesign_minus.svg" alt="minus" class="toggle-minus me-2" width="24" height="24"
                        style="float: right; display: none;">
                </p>
                <hr class="hr-line" style="display: none;">
                <div class="jobs-location-div" style="display: none;">
                    <?php
                    if ($row_locations) {
                        $printed_cities = [];

                        // Get unique cities
                        foreach ($row_locations as $location) {
                            if (!in_array($location['city'], $printed_cities)) {
                                $printed_cities[] = $location['city'];
                            }
                        }

                        // Chunk cities into columns of 6 items each and limit to 4 columns
                        $chunks = array_chunk($printed_cities, 6);
                        $chunks = array_slice($chunks, 0, 4);

                        foreach ($chunks as $column_data) {
                            echo '<ul class="jobs-location-ul">';
                            foreach ($column_data as $city) {
                                $city_wise_id = '';
                                foreach ($row_locations as $loc) {
                                    if ($loc['city'] === $city) {
                                        $city_wise_id = $loc['city_wise_id'];
                                        break;
                                    }
                                }
                                $url = '/hire-in-' . strtolower(str_replace(' ', '-', $city));

                                echo "<li class='seo-links'><a href='{$url}'>Hire in {$city}</a></li>";
                            }
                            echo '</ul>';
                        }
                    }
                    ?>

                </div>
                <hr class="hr-line-bottom" style="display: none;">
                <div class="search-by-viewmore-div" style="display: none;">
                    <p class="search-by-viewmore-word active">Search by</p>
                    <p class="search-by-viewmore-word">A</p>
                    <p class="search-by-viewmore-word">B</p>
                    <p class="search-by-viewmore-word">C</p>
                    <p class="search-by-viewmore-word">D</p>
                    <p class="search-by-viewmore-word">E</p>
                    <p class="search-by-viewmore-word">F</p>
                    <p class="search-by-viewmore-word">G</p>
                    <p class="search-by-viewmore-word">H</p>
                    <p class="search-by-viewmore-word">I</p>
                    <p class="search-by-viewmore-word">J</p>
                    <p class="search-by-viewmore-word">K</p>
                    <p class="search-by-viewmore-word">L</p>
                    <p class="search-by-viewmore-word">M</p>
                    <p class="search-by-viewmore-word">N</p>
                    <p class="search-by-viewmore-word">O</p>
                    <p class="search-by-viewmore-word">P</p>
                    <p class="search-by-viewmore-word">Q</p>
                    <p class="search-by-viewmore-word">R</p>
                    <p class="search-by-viewmore-word">S</p>
                    <p class="search-by-viewmore-word">T</p>
                    <p class="search-by-viewmore-word">U</p>
                    <p class="search-by-viewmore-word">V</p>
                    <p class="search-by-viewmore-word">W</p>
                    <p class="search-by-viewmore-word">X</p>
                    <p class="search-by-viewmore-word">Y</p>
                    <p class="search-by-viewmore-word">Z</p>
                    <p class="search-by-viewmore-word">&#8377;</p>
                    <p class="search-by-viewmore-word">0-9</p>
                    <a href="/browse_jobs.php?flow=hire-in-location" class="search-by-viewmore-btn">View More</a>
                </div>
            </div>

            <div class="seo-section">
                <p class="seo-links-heads">
                    Jobs by Designation
                    <img src="/images/tdesign_plus.svg" alt="plus" class="toggle-plus me-2" width="24" height="24"
                        style="float: right;">
                    <img src="/images/tdesign_minus.svg" alt="minus" class="toggle-minus me-2" width="24" height="24"
                        style="float: right; display: none;">
                </p>
                <hr class="hr-line" style="display: none;">
                <div class="jobs-location-div" style="display: none;">
                    <?php
                    if ($row_job_id) {
                        $printed_jobrole = [];

                        // Get unique cities
                        foreach ($row_job_id as $jobrole) {
                            if (!in_array($jobrole['jobrole'], $printed_jobrole)) {
                                $printed_jobrole[] = $jobrole['jobrole'];
                            }
                        }

                        // Chunk cities into columns of 6 items each and limit to 4 columns
                        $chunks = array_chunk($printed_jobrole, 6);
                        $chunks = array_slice($chunks, 0, 4);

                        foreach ($chunks as $column_data) {
                            echo '<ul class="jobs-location-ul">';
                            foreach ($column_data as $job_role) {

                                $url = '/' . strtolower(str_replace(' ', '-', $job_role));

                                echo "<li class='seo-links'><a href='{$url}'>{$job_role}</a></li>";
                            }
                            echo '</ul>';
                        }
                    }
                    ?>

                </div>
                <hr class="hr-line-bottom" style="display: none;">
                <div class="search-by-viewmore-div" style="display: none;">

                    <p class="search-by-viewmore-word active">Search by</p>
                    <p class="search-by-viewmore-word">A</p>
                    <p class="search-by-viewmore-word">B</p>
                    <p class="search-by-viewmore-word">C</p>
                    <p class="search-by-viewmore-word">D</p>
                    <p class="search-by-viewmore-word">E</p>
                    <p class="search-by-viewmore-word">F</p>
                    <p class="search-by-viewmore-word">G</p>
                    <p class="search-by-viewmore-word">H</p>
                    <p class="search-by-viewmore-word">I</p>
                    <p class="search-by-viewmore-word">J</p>
                    <p class="search-by-viewmore-word">K</p>
                    <p class="search-by-viewmore-word">L</p>
                    <p class="search-by-viewmore-word">M</p>
                    <p class="search-by-viewmore-word">N</p>
                    <p class="search-by-viewmore-word">O</p>
                    <p class="search-by-viewmore-word">P</p>
                    <p class="search-by-viewmore-word">Q</p>
                    <p class="search-by-viewmore-word">R</p>
                    <p class="search-by-viewmore-word">S</p>
                    <p class="search-by-viewmore-word">T</p>
                    <p class="search-by-viewmore-word">U</p>
                    <p class="search-by-viewmore-word">V</p>
                    <p class="search-by-viewmore-word">W</p>
                    <p class="search-by-viewmore-word">X</p>
                    <p class="search-by-viewmore-word">Y</p>
                    <p class="search-by-viewmore-word">Z</p>
                    <p class="search-by-viewmore-word">&#8377;</p>
                    <p class="search-by-viewmore-word">0-9</p>
                    <a href="/browse_jobs.php?flow=jobs-in-category" class="search-by-viewmore-btn">View More</a>
                </div>
            </div>

            <div class="seo-section">
                <p class="seo-links-heads">
                    Jobs at Company
                    <img src="/images/tdesign_plus.svg" alt="plus" class="toggle-plus me-2" width="24" height="24"
                        style="float: right;">
                    <img src="/images/tdesign_minus.svg" alt="minus" class="toggle-minus me-2" width="24" height="24"
                        style="float: right; display: none;">
                </p>
                <hr class="hr-line" style="display: none;">
                <div class="jobs-location-div" style="display: none;">
                    <?php
                    if ($row_job_id) {
                        $printed_companyname = [];

                        // Get unique cities
                        foreach ($row_job_id as $companyname) {
                            if (!in_array($companyname['companyname'], $printed_companyname)) {
                                $printed_companyname[] = $companyname['companyname'];
                            }
                        }

                        // Chunk cities into columns of 6 items each and limit to 4 columns
                        $chunks = array_chunk($printed_companyname, 6);
                        $chunks = array_slice($chunks, 0, 4);

                        foreach ($chunks as $column_data) {
                            echo '<ul class="jobs-location-ul">';
                            foreach ($column_data as $company_name) {

                                $url = '/' . strtolower(str_replace(' ', '-', $company_name));

                                echo "<li class='seo-links'><a href='{$url}'>{$company_name}</a></li>";
                            }
                            echo '</ul>';
                        }
                    }
                    ?>

                </div>
                <hr class="hr-line-bottom" style="display: none;">
                <div class="search-by-viewmore-div" style="display: none;">

                    <p class="search-by-viewmore-word active">Search by</p>
                    <p class="search-by-viewmore-word">A</p>
                    <p class="search-by-viewmore-word">B</p>
                    <p class="search-by-viewmore-word">C</p>
                    <p class="search-by-viewmore-word">D</p>
                    <p class="search-by-viewmore-word">E</p>
                    <p class="search-by-viewmore-word">F</p>
                    <p class="search-by-viewmore-word">G</p>
                    <p class="search-by-viewmore-word">H</p>
                    <p class="search-by-viewmore-word">I</p>
                    <p class="search-by-viewmore-word">J</p>
                    <p class="search-by-viewmore-word">K</p>
                    <p class="search-by-viewmore-word">L</p>
                    <p class="search-by-viewmore-word">M</p>
                    <p class="search-by-viewmore-word">N</p>
                    <p class="search-by-viewmore-word">O</p>
                    <p class="search-by-viewmore-word">P</p>
                    <p class="search-by-viewmore-word">Q</p>
                    <p class="search-by-viewmore-word">R</p>
                    <p class="search-by-viewmore-word">S</p>
                    <p class="search-by-viewmore-word">T</p>
                    <p class="search-by-viewmore-word">U</p>
                    <p class="search-by-viewmore-word">V</p>
                    <p class="search-by-viewmore-word">W</p>
                    <p class="search-by-viewmore-word">X</p>
                    <p class="search-by-viewmore-word">Y</p>
                    <p class="search-by-viewmore-word">Z</p>
                    <p class="search-by-viewmore-word">&#8377;</p>
                    <p class="search-by-viewmore-word">0-9</p>
                    <a href="/browse_jobs.php?flow=jobs-in-company" class="search-by-viewmore-btn">View More</a>
                </div>
            </div>
        </div>



    </div>

    <style>
        .seo-section {
            color: #175DA8;
            font-family: Poppins;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0px 0px 0px 1px rgba(0, 0, 0, 0.08);
            margin: 48px 0 -36px -12px;
            background: white;
            transition: height 0.3s ease;
            overflow: hidden;
        }

        .seo-links-heads {
            margin-bottom: 0 !important;
        }


        .hr-line {
            stroke-width: 0.6px;
            stroke: #C0C0C0;
        }

        .seo-links a {
            text-decoration: none;
            color: #5C788A;
            font-family: Poppins;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .jobs-location-div {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .jobs-location-ul {
            list-style-type: none;
            padding-left: 0;
            width: 22%;
            /* Around 25% - some space for margin/gap */
        }

        .search-by-viewmore-div {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            flex-wrap: nowrap;
            /* border-bottom: 0.6px solid #C0C0C0; */
        }

        .search-by-viewmore-word.active {
            color: var(--Deeesha-Blue, #175DA8);

            font-size: 15px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .search-by-viewmore-word {
            color: #5C788A;
            cursor: pointer;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .hr-line-bottom {
            margin-top: 0px;
            margin-bottom: -6px;
        }

        .search-by-viewmore-btn {
            border-radius: 4px;
            background: var(--Deeesha-Green, #4EA647);
            color: #FFF;
            font-size: 13px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-decoration: none;
            padding: 11px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .seo-section {
                overflow: auto !important;
            }
            .jobs-location-div {
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 0;
            }
            .seo-links-heads {
                position: sticky;
                top: -17px;
                padding: 10px 0px;
                background: white;
                z-index: 9;
            }
            .hr-line {    
                position: sticky;
                top: 24px;
                z-index: 9;
            }
            .hr-line-bottom {
                margin-top: 0px !important;
                margin-bottom: 30px !important;
            }
            @media (max-width: 480px) {
            .search-by-viewmore-div {
                flex-wrap: wrap;
            }
            .search-by-viewmore-btn {  
                margin-top: 10px; 
                text-align: center; 
                width: 50% !important;
            }
            }
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.seo-section').each(function () {
                const $section = $(this);
                const $p = $section.find('.seo-links-heads');
                const $plus = $p.find('.toggle-plus');
                const $minus = $p.find('.toggle-minus');
                const $hr = $section.find('.hr-line');
                const $hrBottom = $section.find('.hr-line-bottom');
                const $jobsLocationDiv = $section.find('.jobs-location-div');
                const $viewmoreDiv = $section.find('.search-by-viewmore-div');

                $plus.on('click', function (e) {
                    e.stopPropagation();
                    $section.css('height', '325px');
                    $plus.hide();
                    $minus.show();
                    $hr.show();
                    $hrBottom.show();
                    $jobsLocationDiv.show();
                    $viewmoreDiv.show();
                });

                $minus.on('click', function (e) {
                    e.stopPropagation();
                    $section.css('height', 'auto');
                    $minus.hide();
                    $plus.show();
                    $hr.hide();
                    $hrBottom.hide();
                    $jobsLocationDiv.hide();
                    $viewmoreDiv.hide();
                });
            });
        });

    </script>


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
                        <img src=images/codicon_git-stash-apply.svg width="16" height="16" style="margin: 0 4px 4px 0;">
                        Apply for Job</button>
                    <div class="share-ref-block">
                        <button class="btn share-btn" id="share-job-btn">
                            <img src="/images/finploy-share-icon-smu.svg" width= "20px";
                            height="20px"; alt="share" title="Share Job">
                        </button>
                        <button id="candidate-as-partner"
                            class="btn btn-pimary border mb-4 btn-popup-btn2"
                            title="Refer a candidate">
                            <img src=images/basil_bag-solid.svg width="20" height="20" style="margin: 0 4px 2px 0;">
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
                <a class="text-decoration-none text-light" href="landingpage.php"><button type="submit"
                        class="btn btn-success w-50 mt-5 mb-5">Continue</button></a>
                <!-- <button class="prev-step">Back</button> -->
            </div>
        </div>
    </div>

    <?php include 'candidate_login.php'; ?>
</body>
<?php include 'footer.php'; ?>
<script>
    const jobIdUrl = <?php echo $job_id; ?>;
    // Show pagination after a delay
    window.addEventListener("load", function () {
        setTimeout(function () {
            document.querySelector(".pagination-div").style.display = "block";
        }, 900);
    });
    // ----------------- Job Details Popup -----------------
    $(document).on("click", ".job-grid,.job-card, .job-card-container, .job-bio", function (e) {
        if ($(e.target).is("#candidate-as-partner, #candidate-to-partner, #job-apply-btn","#apply-job-btn") ||
            $(e.target).closest("#candidate-as-partner, #candidate-to-partner, #job-apply-btn","#apply-job-btn").length) {
            return false;
        }
        let jobId = $(this).data("id");
        $("#jobDetailsPopup").fadeIn();

        $.ajax({
            url: "candidate/get_job_details.php",
            type: "POST",
            data: { id: jobId },
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                $("#popupContent").html(response);
            }
        });
    });
    // Close Job Details Popup
    $(".close-popup").on("click", function () {
        $("#jobDetailsPopup").fadeOut();
    });
    // ----------------- Log Entry on Input Blur -----------------
    let activeInput = null;
    // Track focused input
    $(document).on('focusin', '#mobile, #name', function () {
        activeInput = this.id;
    });
    // Detect outside click and send log data
    $(document).on('click', function (e) {
        const clickedInsideInput = $(e.target).is('#mobile') || $(e.target).is('#name');
        if (!clickedInsideInput && activeInput) {
            const mobileNumber = $('#mobile').val().trim();
            const nameField = $('#name').val().trim();
            const loadType = $('.login-type-btn.active-btn').attr('id') || '';
            const loginType = loadType.replace(/^load/, '');
            $.ajax({
                url: 'logs_entry.php',
                type: 'POST',
                data: {
                    mobile: mobileNumber,
                    name: nameField,
                    userType: loginType
                },
                success: function (response) {
                    console.log('Log entry submitted:', response);
                },
                error: function (xhr, status, error) {
                    console.error('Log entry failed:', error);
                }
            });
            activeInput = null;
        }
    });
    // for while clik input open dropdown 
    function activateSearch(inputId, listDivId, arrowSelector) {
        $(`#${inputId}`).on('focus', function () {
            const content = $(`#${listDivId}`);
            content.addClass('active').css('max-height', '200px');

            const arrowImg = $(arrowSelector);
            arrowImg.attr('src', 'assets/upward-arrow.svg');
        });
    }
    // Apply to all search boxes
    activateSearch('departmentsearch', 'department-list-div', '#department-list-head .accordion-arrow');
    activateSearch('subdepartmentsearch', 'subdepartment-list-div', '.filter-accordion.subdepart-head .accordion-arrow');
    activateSearch('categorysearch', 'category-list-div', '.filter-accordion.cate-head .accordion-arrow');
    activateSearch('productsearch', 'product-list-div', '.filter-accordion.product-head .accordion-arrow');
    activateSearch('subproductsearch', 'subproduct-list-div', '.filter-accordion.subpro-head .accordion-arrow');
    activateSearch('specializationsearch', 'specialization-list-div', '.filter-accordion.specia-head .accordion-arrow');

    // checkbox need to work like radio 
    document.querySelectorAll('input[name="sort"]').forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                document.querySelectorAll('input[name="sort"]').forEach((other) => {
                    if (other !== this) other.checked = false;
                });
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


    // Send seo_city in js file.
    var seoCity = <?= json_encode($seo_city) ?>;
    var seocityLabel = <?= json_encode($seolabel) ?>;
    var seolocationId = <?= json_encode($seolocation_id) ?>;
    var seoDesignation = <?= json_encode($seo_designation) ?>;
    var seoCompanyname = <?= json_encode($seo_companyname) ?>;
    var seoProduct = <?= json_encode($seo_product) ?>;
    var seoProductId = <?= json_encode($seo_product_id) ?>;
    var seoSubProduct = <?= json_encode($seo_sub_product) ?>;
    var seoSubProductId = <?= json_encode($seo_sub_product_id) ?>;
    var seoSpecialization = <?= json_encode($seo_specialization) ?>;
    var seoSpecializationId = <?= json_encode($seo_specialization_id) ?>;
    var seoDepartment = <?= json_encode($seo_department) ?>;
    var seoDepartmentId = <?= json_encode($seo_department_id) ?>;
    var seoSubDepartment = <?= json_encode($seo_sub_department) ?>;
    var seoSubDepartmentId = <?= json_encode($seo_sub_department_id) ?>;
    var seoCategory = <?= json_encode($seo_category) ?>;
    var seoCategoryId = <?= json_encode($seo_category_id) ?>;

     // Detect outside click and send log data

</script>
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom JS Files -->
<script src="js/index.js"></script>
<script src="js/home.js"></script>