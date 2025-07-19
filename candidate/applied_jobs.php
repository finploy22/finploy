<?php

session_start();
// Uncomment if session check is required
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    die();
}

$mobileNumber = isset($_SESSION['mobile']) ? $_SESSION['mobile'] : '';

require '../session.php';
include '../db/connection.php';
    include 'header.php'; 


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
    display: flex !important;
    align-items: center!important;
    text-align: center!important;
    margin: 1.5rem 0!important;
    width: 100%!important;
    margin-top: 11px!important;
}

        /* For Pagination  */
        .pagination-div {
            display: flex;
            justify-content: space-between;
        }
        #employer-login-btn:hover,
        #partner-login-btn:hover,
        .nav-link .icon-link:hover,
        .nav-items:hover {
            background: transparent;
        }

        .per-page-list {
            border: 0.5px solid var(--Deeesha-Blue, #175DA8);
            color: #175DA8;
            background: #FFF;
            border-radius: 3px;
            padding: 10px 8px 10px 10px;
            margin: 8px;
            width: 54px;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .page-links {
            display: flex;
            margin-top: 14px;
            background-color: #80808014;
            border-radius: 3px;
            margin-right: 5px;
            margin-bottom: 15px;
            margin-left: 645px;
            /* position: absolute; */
        }

        .page-link-style {
            background-color: #80808014;
            border-radius: 3px;
            padding: 6px;
            height: 33px;
        }

        .linkForPage {
            color: #175DA8;
            padding: 8px;
            text-decoration: none;
        }

        .page-link-p {
            border: 1px solid #175DA8;
            border-radius: 3px;
            padding: 4px 5px 0px 13px;
            margin-top: 4px;
        }

        .per-page-p {
            margin-top: 4px;
        }

        /* Inside select arrows */
        select.classic {
            background-image:
                linear-gradient(45deg, transparent 50%, #175DA8 50%),
                linear-gradient(135deg, #175DA8 50%, transparent 50%),
                linear-gradient(to right, white, white);
            background-position:
                calc(103% - 12px) calc(1em + 3px),
                calc(100% - 6px) calc(1em + 3px),
                100% 0;
            background-size:
                5px 5px,
                5px 5px,
                2.5em 2.5em;
            background-repeat: no-repeat;
        }
       /* For Mobile */
        @media (min-width: 992px) and (max-width: 1199px) {
            .page-links {
               margin-left: 510px;
            }
            .job-applied-btn{
                font-size: 14px;
                font-weight: 600;
            }
            .view-job-details{
                font-size: 14px;
                font-weight: 500;
            }
            .job-specifications{
                font-size: 14px;
                font-weight: 500;
            }
        }
        @media (min-width: 529px) and (max-width: 991px) {
            .page-links {
                margin-left: 330px;
            }
             .job-applied-btn{
                font-size: 13px;
                font-weight: 600;
            }
            .view-job-details{
                font-size: 13px;
                font-weight: 500;
            }
            .job-specifications{
                font-size: 13px;
                font-weight: 500;
            }
        }
        @media (min-width: 375px) and (max-width: 528px) {
            .page-links {
                margin-left: 95px;
                margin-top: 65px;
            }
            .per-page{
                margin-left: 85px;
            }
        }
        @media (min-width: 321px) and (max-width: 374px) {
            .page-links{
                margin-top: 65px;
                margin-left: 85px;
            }
            .per-page{
                margin-left: 75px;
            }
        }

        @media screen and (max-width: 320px) {
            .page-links {
                margin-top: 65px;
                margin-left: 50px;
            }
            .per-page{
                margin-left: 45px;
            }
        }

        /* Pagination End */
        
        /* filter Start */
        /*#job-list {*/
        /*flex: 1;*/
        overflow-y: auto; /* Vertical scroll only on this section */
        /*padding: 20px;*/
        /*background-color: #fff;*/
        /*height: 100vh;*/
        /*box-sizing: border-box;*/
        /*}*/
        
        #employer-login-btn:hover,
        #partner-login-btn:hover,
        .nav-link .icon-link:hover,
        .nav-items:hover {
        background: transparent;
        }
        /* Filter section styling */
        .filter-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        }
        
        .filter-header img {
        margin-right: 10px;
        }
        
        .filter-section {
        margin-bottom: 15px;
        }
        
        /* Checkbox styling */
        .custom-checkbox {
        margin-bottom: 8px;
        }
        
        .other-input {
        width: 100%;
        padding: 5px;
        margin-top: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        }
        
        /* Accordion styling for filter sections */
        .filter-accordion {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        color: #000;
        font-family: Poppins;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        }
        
        
        .filter-content {
        padding: 0 5px;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.2s ease-out;
        }
        
        .filter-content.active {
        max-height: 500px;
        }
        /* filter end */
        .filter-accordion .form-select{
            border: none !important;
        }
        .filter-accordion {
          display: flex;
          justify-content: space-between;
          align-items: center;
          cursor: pointer;
        }
        
        
        /* Mobile filter */
        
        .mobile-filter-popup {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #fff;
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }
        
        .popup-content {
            flex-grow: 1;
            overflow-y: auto;
            text-align: left;
            font-size: 13px;
        }
        #locationSelect{
            font-size: 13px;
        }
        .filter-categories {
            width: 40%;
            max-width: 160px;
            background-color: transparent !important;
        }
        
        .filter-options {
            width: 60%;
            overflow-y: auto;
        }
        
        .filter-categories .list-group-item {
            cursor: pointer;
        }
        
        .filter-categories .list-group-item.active {
            background-color: #fff !important;
            color: #175DA8;
        }
        
        .popup-footer {
            position: sticky;
            bottom: 0;
        }
          .list-group-item {
        	background-color: #fff !important;
        }
        .job-applied-btn{
            color: #175DA8;
            font-family: 'Poppins';
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #175DA8;
            border-radius: 5px;
            padding: 7px;
        }
        .view-job-details{
            color: #175DA8;
            font-family: 'Poppins';
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
        }
        .job-specifications{
            color: #000;
            font-family: 'Poppins';
            font-size: 14px;
            font-weight: 500;
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
                        class="btn btn-pimary border border-primary text-primary w-100 mb-4 btn-popup-btn2"
                        title="Refer a candidate">
                        <img src=/images/basil_bag-solid.svg width="16" height="16" style="margin: 0 4px 4px 0;">
                        Refer a candidate</button>
                    </div>
                   
                </div>
            </div>
            <style>
             .continue-btn{
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                align-content: center;
                align-items: center;
                justify-content: center;
            }
            </style>
   

            <div class="step" id="step-2" style="display: none;">
                <div class="mb-3 justify-center text-center">
                    <img class="login-logo" src="assets/finploy-logo.png" alt="FINPLOY" height="40">
                </div>
                <div class="text-center mt-5 mb-4">
                    <img src="assets/party.svg" alt="">
                    <h4 class="step-title mt-3">✨ Congratulations ✨</h4>
                    <p class="text-success mt-3">You have successfully applied for the job</p>
                </div>
                <a class="text-decoration-none continue-btn   text-light" href="landingpage.php"><button type="submit"
                        class="btn btn-success w-50 mt-5 mb-5">Continue</button></a>
                <!-- <button class="prev-step">Back</button> -->
            </div>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/index.js"></script>
<script>

    function showLoader() {
        $('.ajax-loader').fadeIn(200);
    }

    function hideLoader() {
        $('.ajax-loader').fadeOut(200);
    }


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
        if (typeof locations === 'string') {
            try {
                locations = JSON.parse(locations);
            } catch (e) {
                console.error('Invalid JSON response:', locations);
                hideLoader();
                return;
            }
        }

        if (!Array.isArray(locations)) {
            console.error('Response is not an array:', locations);
            hideLoader();
            return;
        }

        // Sort locations alphabetically
        locations.sort();

        // Initialize autocomplete for both input fields
        $('#location').autocomplete({
            source: locations,
            minLength: 1
        });

        $('#locationSelect').autocomplete({
            source: locations,
            minLength: 1
        });

        hideLoader();
    },
    error: function (xhr, status, error) {
        console.error('Failed to fetch locations:', error);
        hideLoader();
    }
});


    /////////////////////////////////////// fetch Full Jobs ////////////
    $(document).ready(function () {
        $(document).on("click", "#loadMoreBtn", function () {
            $.ajax({
                url: "fetch_applied_jobs.php",
                type: "POST",
                data: { load_more: true },
                beforeSend: function () {
                    showLoader();
                },
                // beforeSend: function() {
                //     $("#loadMoreBtn").text("Loading...").prop("disabled", true);

                // },
                success: function (response) {
                    $("#job-list").html(response); // Replace content with full job list
                    hideLoader();
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
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
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
    
    
   
    // /////////////////// Pagination ////////////////
    $(document).ready(function () {
        function loadJobs(page) {
            $.ajax({
                url: 'fetch_applied_jobs.php',
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

        // Load first page initially
        loadJobs(1);
    });

    // //////////// Search Bar Placeholder ///////////
    const searchValues = ["jobs", "departments", "companies", "locations"];

    let index = 0;
    function changePlaceholder() {
        const searchInputs = document.querySelectorAll("#search, #searchjob");

        searchInputs.forEach(input => {
            input.setAttribute("placeholder", `Search for '${searchValues[index]}'`);
        });

        index = (index + 1) % searchValues.length; // Loop through the array
    }

    // Change placeholder every 2.5 seconds
    setInterval(changePlaceholder, 2500);

    // Show search container when search icon is clicked
    document.getElementById("search-icon-mobile").addEventListener("click", function () {
        document.getElementById("search-container-mobile").style.display = "block";
    });
    
    ///////////////// Filter Toggle //////////////////////

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
                content.css('max-height', content.prop('scrollHeight') + "px");
                arrowImg.attr('src', 'assets/upward-arrow.svg');
            } else {
                content.css('max-height', 0);
                arrowImg.attr('src', 'assets/downward-arrow.svg');
            }
        });
    });


///////////////////////// filters ////////////
// Function to collect all filter values
function collectFilters() {
    const filters = {};

    // Sort by filters (handle both desktop and mobile views)
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

    // Department filters
    const departments = [];
    $('input[name="department"]:checked').each(function() {
        departments.push($(this).val());
    });
    if ($('#other-department').val()?.trim()) {
        departments.push($('#other-department').val().trim());
    }
    if ($('#jobdepartment').val()) {
        departments.push($('#jobdepartment').val());
    }
    // Mobile checkboxes
    $('#filterOptionsContent [data-content="department"] input[type="checkbox"]:checked').each(function () {
        departments.push($(this).attr('id'));
    });
    if (departments.length) {
        filters.departments = departments;
    }

    // Sub-Department filters
    const subDepartments = [];
    $('input[name="sub_department"]:checked').each(function() {
        subDepartments.push($(this).val());
    });
    if ($('#other-sub-department').val()?.trim()) {
        subDepartments.push($('#other-sub-department').val().trim());
    }
    if ($('#job-sub-department').val()) {
        subDepartments.push($('#job-sub-department').val());
    }
    $('#filterOptionsContent [data-content="subdepartment"] input[type="checkbox"]:checked').each(function () {
        subDepartments.push($(this).attr('id'));
    });
    if (subDepartments.length) {
        filters.sub_departments = subDepartments;
    }

    // Product filters
    const products = [];
    $('input[name="product"]:checked').each(function() {
        products.push($(this).val());
    });
    if ($('#other-product').val()?.trim()) {
        products.push($('#other-product').val().trim());
    }
    if ($('#job-product').val()) {
        products.push($('#job-product').val());
    }
    $('#filterOptionsContent [data-content="product"] input[type="checkbox"]:checked').each(function () {
        products.push($(this).attr('id'));
    });
    if (products.length) {
        filters.products = products;
    }

    // Sub-Product filters
    const subProducts = [];
    $('input[name="sub_product"]:checked').each(function() {
        subProducts.push($(this).val());
    });
    if ($('#other-sub-product').val()?.trim()) {
        subProducts.push($('#other-sub-product').val().trim());
    }
    $('#filterOptionsContent [data-content="subproduct"] input[type="checkbox"]:checked').each(function () {
        subProducts.push($(this).attr('id'));
    });
    if (subProducts.length) {
        filters.sub_products = subProducts;
    }

    // Designation filters
    const designations = [];
    $('input[name="designation"]:checked').each(function() {
        designations.push($(this).val());
    });
    if ($('#other-designation').val()?.trim()) {
        designations.push($('#other-designation').val().trim());
    }
    $('#filterOptionsContent [data-content="designation"] input[type="checkbox"]:checked').each(function () {
        designations.push($(this).attr('id'));
    });
    if (designations.length) {
        filters.designations = designations;
    }

    // Location filter
    const locationValue = $('#location').val() || $('#joblocation').val();
    if (locationValue) {
        filters.location = locationValue;
    }

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

// Function to fetch jobs with all filters
function fetchFilteredJobs(page = 1) {
    const filters = collectFilters();
    filters.page = page;
    console.log("Sending filters:", filters);
    showLoader();
    $.ajax({
        url: 'fetch_applied_jobs.php',
        type: 'POST',
        data: filters,
        success: function(response) {
            // console.log(response);
            $('#job-list').html(response);
            hideLoader();
        },
        error: function(xhr, status, error) {
            console.error("Filter request failed:", error);
            $('#job-list').html('<p class="text-danger">Failed to load jobs. Please try again later.</p>');
            hideLoader();
        }
    });
}

// Event bindings
$(document).ready(function() {
    fetchFilteredJobs();

    // Web filters
    $(document).on('change', 'input[type="checkbox"]', function() {
        fetchFilteredJobs(1);
    });

    $('#other-department, #other-sub-department, #other-product, #other-sub-product, #other-designation').on('input', function() {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function() {
            fetchFilteredJobs(1);
        }, 500));
    });

    $('#jobsort, #jobdepartment, #job-sub-department, #job-product, #location, #joblocation, #per-page-list').on('change', function() {
        fetchFilteredJobs(1);
    });

    $('#min_salary, #max_salary, #jobmin_salary, #jobmax_salary').on('input', function() {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function() {
            fetchFilteredJobs(1);
        }, 500));
    });

    $('#search, #searchjob').on('input', function() {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function() {
            fetchFilteredJobs(1);
        }, 300));
    });

    // Pagination
    $(document).on('click', '.linkForPage', function(e) {
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
        $('#mobileFilterPopup input[type="checkbox"]').prop('checked', false);
        fetchFilteredJobs(1);
    });

    // Optional: Close filter popup button
    $('#closeFilterPopup').on('click', function () {
        $('#mobileFilterPopup').addClass('d-none');
    });
});

////////////////  Mobile Filter Popup ///////////////
  
document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('mobileFilterPopup');
    const filterBtn = document.querySelector('#filters-section-mobile button'); // Your trigger button
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
    window.addEventListener("load", function () {
        setTimeout(function () {
            document.querySelector(".pagination-div").style.display = "block";
        }, 900);
    });
</script>
 <?php include '../footer.php'; ?>
</body>

</html>