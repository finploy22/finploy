
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <link rel="stylesheet" href="/css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<style>
 /* Ensure body doesn't scroll */
/*body, html {*/
/*  margin: 0;*/
/*  padding: 0;*/
/*  height: 100vh;*/
/*  overflow: hidden;*/
/*}*/

/* Flex layout for the two columns */
/*.job-container {*/
/*  display: flex;*/
  height: 100vh; /* Full height of viewport */
/*}*/

/* Fixed-height filter section */
/*.filters-section {*/
  width: 250px; /* Adjust as needed */
/*  background: #f4f4f4;*/
/*  padding: 20px;*/
  overflow-y: auto; /* Scroll if content is too long */
/*  height: 100vh;*/
/*  box-sizing: border-box;*/
/*}*/

/* Scrollable job list section */
#job-list {
  flex: 1;
  overflow-y: auto; /* Vertical scroll only on this section */
  padding: 20px;
  background-color: #fff;
  height: 100vh;
  box-sizing: border-box;
}

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

.filter-accordion::after {
  content: '\25BC'; /* Down arrow */
  font-size: 12px;
}

.active.filter-accordion::after {
  content: '\25B2'; /* Up arrow */
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
</style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4 mb-5">
        <div class="job-liting-page">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="jobs-page-title">Showing All Jobs</h5>
                <div class="search-container" id="search-container-web">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                      <path d="M16.5674 16.1255L20.4141 20M18.6363 11.1111C18.6363 15.0385 15.4526 18.2222 11.5252 18.2222C7.59781 18.2222 4.41406 15.0385 4.41406 11.1111C4.41406 7.18375 7.59781 4 11.5252 4C15.4526 4 18.6363 7.18375 18.6363 11.1111Z" stroke="#888888" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <input type="text" id="search" class="form-control" placeholder="Search for ' location '" autocomplete="off">
                </div>
                <div class="search-container p-2" id="search-icon-mobile">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                      <path d="M16.5674 16.1255L20.4141 20M18.6363 11.1111C18.6363 15.0385 15.4526 18.2222 11.5252 18.2222C7.59781 18.2222 4.41406 15.0385 4.41406 11.1111C4.41406 7.18375 7.59781 4 11.5252 4C15.4526 4 18.6363 7.18375 18.6363 11.1111Z" stroke="#888888" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="search-container" id="search-container-mobile">
            <input type="text" id="searchjob" class="form-control" placeholder="Search for ' location '" autocomplete="off">
        </div>

        <div class="row">
            <!-- Filters Section -->
            <div class="col-md-3 filters-section" id="filters-section-web">
                <h5 class="filter-header"><img src="assets/filter-icon.svg" alt=""> All Filters</h5>

                <div class="mb-3">
                    <h6 class="filter-accordion">Sort By</h6>
                    <div class="filter-content active">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sort-relevance" name="sort" value="relevance">
                            <label for="sort-relevance">Relevance</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sort-salary" name="sort" value="salary_desc">
                            <label for="sort-salary">Salary - High to Low</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sort-date" name="sort" value="date_desc">
                            <label for="sort-date">Date Posted - New to Old</label>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Department Filter (Updated) -->
                <div class="mb-3">
                    <h6 class="filter-accordion">Department</h6>
                    <div class="filter-content active">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sales" name="department" value="Sales">
                            <label for="sales">Sales</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="credit" name="department" value="Credit">
                            <label for="credit">Credit</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="operations" name="department" value="Operations">
                            <label for="operations">Operations</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="hr" name="department" value="HR / Training">
                            <label for="hr">HR / Training</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="legal" name="department" value="Legal / Compliance / Risk">
                            <label for="legal">Legal / Compliance / Risk</label>
                        </div>
                        <div class="custom-checkbox">
                            <label>Other Departments:</label>
                            <input type="text" class="other-input" id="other-department" placeholder="Type here">
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Sub-Department Filter (New) -->
                <div class="mb-3">
                    <h6 class="filter-accordion">Sub-Department</h6>
                    <div class="filter-content">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-dept-aaa" name="sub_department" value="aaa">
                            <label for="sub-dept-aaa">aaa</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-dept-bbb" name="sub_department" value="bbb">
                            <label for="sub-dept-bbb">bbb</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-dept-ccc" name="sub_department" value="ccc">
                            <label for="sub-dept-ccc">ccc</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-dept-ddd" name="sub_department" value="ddd">
                            <label for="sub-dept-ddd">ddd</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-dept-eee" name="sub_department" value="eee">
                            <label for="sub-dept-eee">eee</label>
                        </div>
                        <div class="custom-checkbox">
                            <label>Other Sub-Departments:</label>
                            <input type="text" class="other-input" id="other-sub-department" placeholder="Type here">
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Product Filter (New) -->
                <div class="mb-3">
                    <h6 class="filter-accordion">Product</h6>
                    <div class="filter-content">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="hlhp" name="product" value="HL/HP">
                            <label for="hlhp">HL/HP</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="business-loan" name="product" value="Business Loan">
                            <label for="business-loan">Business Loan</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="gold-loan" name="product" value="Gold Loan">
                            <label for="gold-loan">Gold Loan</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="casa" name="product" value="CASA">
                            <label for="casa">CASA</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="personal-loan" name="product" value="Personal Loan">
                            <label for="personal-loan">Personal Loan</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="education-loan" name="product" value="Education Loan">
                            <label for="education-loan">Education Loan</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="credit-cards" name="product" value="Credit Cards">
                            <label for="credit-cards">Credit Cards</label>
                        </div>
                        <div class="custom-checkbox">
                            <label>Other Products:</label>
                            <input type="text" class="other-input" id="other-product" placeholder="Type here">
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Sub-Product Filter (New) -->
                <div class="mb-3">
                    <h6 class="filter-accordion">Sub-Product</h6>
                    <div class="filter-content">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-prod-aaa" name="sub_product" value="aaa">
                            <label for="sub-prod-aaa">aaa</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-prod-bbb" name="sub_product" value="bbb">
                            <label for="sub-prod-bbb">bbb</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-prod-ccc" name="sub_product" value="ccc">
                            <label for="sub-prod-ccc">ccc</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-prod-ddd" name="sub_product" value="ddd">
                            <label for="sub-prod-ddd">ddd</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sub-prod-eee" name="sub_product" value="eee">
                            <label for="sub-prod-eee">eee</label>
                        </div>
                        <div class="custom-checkbox">
                            <label>Other Sub-Product:</label>
                            <input type="text" class="other-input" id="other-sub-product" placeholder="Type here">
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Designation Filter (New) -->
                <div class="mb-3">
                    <h6 class="filter-accordion">Designation</h6>
                    <div class="filter-content">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="it-head" name="designation" value="IT Head">
                            <label for="it-head">IT Head</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="manager" name="designation" value="Manager">
                            <label for="manager">Manager</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="executive" name="designation" value="Executive">
                            <label for="executive">Executive</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sales-head" name="designation" value="Sales Head">
                            <label for="sales-head">Sales Head</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="sales-executive" name="designation" value="Sales Executive">
                            <label for="sales-executive">Sales Executive</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="legal-manager" name="designation" value="Legal Manager">
                            <label for="legal-manager">Legal Manager</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="credit-manager" name="designation" value="Credit Manager">
                            <label for="credit-manager">Credit Manager</label>
                        </div>
                        <div class="custom-checkbox">
                            <label>Other Designations:</label>
                            <input type="text" class="other-input" id="other-designation" placeholder="Type here">
                        </div>
                    </div>
                </div>
                <hr>

                <!-- Location Filter (Existing) -->
                <div class="mb-3">
                    <h6>Location</h6>
                    <select id="location" class="form-select">
                        <!-- Location options will be populated dynamically -->
                    </select>
                </div>
                <hr>

                <!-- Salary Filter (Existing) -->
                <div class="mb-3">
                    <h6>Monthly Salary</h6>
                    <div class="input-group">
                        <input type="number" id="min_salary" class="form-control" placeholder="Min">
                        <input type="number" id="max_salary" class="form-control" placeholder="Max">
                    </div>
                </div>
            </div>

            <!-- FIlters for Mobile View -->
            <div class="filters-section d-flex align-items-center overflow-auto" style="white-space: nowrap; gap: 1.5rem;" id="filters-section-mobile">
                <!-- Filter Button -->
                <button class="btn btn-outline-primary d-flex align-items-center" style="width: 46px; height: 35px;">
                    <img src="assets/filter-icon.svg" alt="Filter Icon" class="me-2" style="width: 20px;">
                </button>

                <!-- Sort By Dropdown -->
                <select id="jobsort" class="form-select" style="width: 92px; height: 35px;">
                    <option value="">Sort By</option>
                    <option value="relevance">Relevance</option>
                    <option value="salary_desc">Salary - High to Low</option>
                    <option value="date_desc">Date Posted - New to Old</option>
                </select>

                <!-- Department Dropdown -->
                <select id="jobdepartment" class="form-select" style="width: 122px; height: 35px;">
                    <option value="">Department</option>
                    <option value="Sales">Sales</option>
                    <option value="Credit">Credit</option>
                    <option value="Operations">Operations</option>
                    <option value="HR / Training">HR / Training</option>
                    <option value="Legal / Compliance / Risk">Legal / Compliance / Risk</option>
                </select>

                <!-- Sub-Department Dropdown (Mobile) -->
                <select id="job-sub-department" class="form-select" style="width: 150px; height: 35px;">
                    <option value="">Sub-Department</option>
                    <option value="aaa">aaa</option>
                    <option value="bbb">bbb</option>
                    <option value="ccc">ccc</option>
                    <option value="ddd">ddd</option>
                    <option value="eee">eee</option>
                </select>

                <!-- Product Dropdown (Mobile) -->
                <select id="job-product" class="form-select" style="width: 120px; height: 35px;">
                    <option value="">Product</option>
                    <option value="HL/HP">HL/HP</option>
                    <option value="Business Loan">Business Loan</option>
                    <option value="Gold Loan">Gold Loan</option>
                    <option value="CASA">CASA</option>
                    <option value="Personal Loan">Personal Loan</option>
                    <option value="Education Loan">Education Loan</option>
                    <option value="Credit Cards">Credit Cards</option>
                </select>

                <!-- Location Dropdown -->
                <select id="joblocation" class="form-select" style="width: 120px; height: 35px;">
                    <!-- Location options will be populated dynamically -->
                </select>

                <!-- Min Salary Input -->
                <input type="number" id="jobmin_salary" class="form-control" placeholder="Min Salary" style="width: 120px; height: 35px;">

                <!-- Max Salary Input -->
                <input type="number" id="jobmax_salary" class="form-control" placeholder="Max Salary" style="width: 120px; height: 35px;">
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
            </div>
        </div>
    </div>

    <div id="jobDetailsPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            
            <div class="step" id="step-1">
                <div id="popupContent">Loading...</div>
                <div class="me-3 ms-3 ps-3 pt-2">
                <button class="next-step btn btn-success w-100 mb-3" id="apply-job-btn"><svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                <path d="M6.00913 7.38242L9.16013 4.23023V6.25012V6.34178H9.2518H10.5018H10.5935V6.25012V4.23017L13.7457 7.38244L13.8106 7.4473L13.8754 7.38239L14.7592 6.49739L14.8239 6.43257L14.7591 6.3678L10.3841 1.9928L10.3573 1.96595H10.3193H9.43555H9.39758L9.37073 1.9928L4.99573 6.3678L4.93096 6.43257L4.99568 6.49739L5.87943 7.38239L5.94426 7.44731L6.00913 7.38242Z" fill="white" stroke="white" stroke-width="0.183333"/>
                <path d="M2.375 11.1586H2.33703L2.31018 11.1854L1.68518 11.8104L1.65833 11.8373V11.8752V18.1252V18.1632L1.68518 18.1901L2.31018 18.8151L2.33703 18.8419H2.375H17.375H17.413L17.4398 18.8151L18.0648 18.1901L18.0917 18.1632V18.1252V11.8752V11.8373L18.0648 11.8104L17.4398 11.1854L17.413 11.1586H17.375H12.9375H12.8626L12.8477 11.232C12.7084 11.9177 12.3364 12.5342 11.7947 12.977C11.2529 13.4198 10.5747 13.6617 9.875 13.6617C9.1753 13.6617 8.49711 13.4198 7.95535 12.977C7.41359 12.5342 7.04157 11.9177 6.90233 11.232L6.88742 11.1586H6.8125H2.375ZM9.25 7.40858H9.15833V7.50024V8.75024V8.84191H9.25H10.5H10.5917V8.75024V7.50024V7.40858H10.5H9.25ZM9.25 9.90858H9.15833V10.0002V11.2502V11.3419H9.25H10.5H10.5917V11.2502V10.0002V9.90858H10.5H9.25ZM12.2634 14.4016C12.9616 13.9598 13.5232 13.3332 13.8863 12.5919H16.6583V17.4086H3.09167V12.5919H5.86366C6.22676 13.3332 6.78838 13.9598 7.48656 14.4016C8.20113 14.8538 9.02938 15.0938 9.875 15.0938C10.7206 15.0938 11.5489 14.8538 12.2634 14.4016Z" fill="white" stroke="white" stroke-width="0.183333"/>
                </svg> Apply for Job</button>
                <button id="candidate-as-partner" class="btn btn-pimary border border-primary text-primary w-100 mb-4"><svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.79177 4.96044V6.07942L5.45687 6.18817C4.99827 6.22508 4.56529 6.41469 4.22715 6.72668C3.88901 7.03868 3.66525 7.45504 3.59164 7.90919C3.55907 8.1125 3.52912 8.31607 3.50181 8.51991C3.49567 8.56922 3.50524 8.61922 3.52915 8.66279C3.55306 8.70636 3.59009 8.74129 3.63498 8.76261L3.69566 8.79098C7.97379 10.8162 13.0967 10.8162 17.374 8.79098L17.4347 8.76261C17.4794 8.74117 17.5163 8.70619 17.5401 8.66263C17.5638 8.61907 17.5733 8.56914 17.5671 8.51991C17.5404 8.31591 17.5107 8.11232 17.478 7.90919C17.4044 7.45504 17.1807 7.03868 16.8425 6.72668C16.5044 6.41469 16.0714 6.22508 15.6128 6.18817L14.2779 6.08021V4.96123C14.278 4.63094 14.1595 4.31158 13.944 4.06125C13.7286 3.81093 13.4304 3.64625 13.1038 3.59718L12.1424 3.45297C11.0766 3.29367 9.99307 3.29367 8.92729 3.45297L7.96591 3.59718C7.63941 3.64623 7.34135 3.81079 7.12589 4.06096C6.91043 4.31112 6.79188 4.63028 6.79177 4.96044ZM11.9667 4.6216C11.0174 4.47981 10.0523 4.47981 9.10301 4.6216L8.14164 4.7658C8.09499 4.77278 8.0524 4.79627 8.0216 4.83198C7.9908 4.8677 7.97384 4.91328 7.97379 4.96044V5.99668C9.67976 5.89925 11.3899 5.89925 13.0959 5.99668V4.96044C13.0958 4.91328 13.0789 4.8677 13.0481 4.83198C13.0173 4.79627 12.9747 4.77278 12.928 4.7658L11.9667 4.6216Z" fill="#175DA8"/>
                <path d="M17.7203 10.1687C17.7187 10.1432 17.711 10.1185 17.6977 10.0967C17.6845 10.0748 17.6662 10.0566 17.6443 10.0434C17.6225 10.0302 17.5977 10.0225 17.5722 10.021C17.5468 10.0195 17.5213 10.0242 17.498 10.0347C13.108 11.9788 7.96227 11.9788 3.57224 10.0347C3.54898 10.0242 3.52352 10.0195 3.49804 10.021C3.47256 10.0225 3.44782 10.0302 3.42596 10.0434C3.4041 10.0566 3.38577 10.0748 3.37254 10.0967C3.35932 10.1185 3.35159 10.1432 3.35002 10.1687C3.27021 11.6773 3.35131 13.1902 3.59194 14.6817C3.6654 15.136 3.88909 15.5525 4.22724 15.8647C4.56539 16.1768 4.99846 16.3665 5.45718 16.4035L6.93234 16.5217C9.33027 16.7155 11.7392 16.7155 14.1379 16.5217L15.6131 16.4035C16.0718 16.3665 16.5049 16.1768 16.843 15.8647C17.1812 15.5525 17.4049 15.136 17.4783 14.6817C17.7195 13.1884 17.8014 11.6754 17.7203 10.1695" fill="#175DA8"/>
                </svg> Refer a candidate</button>
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
                <a class="text-decoration-none continue-btn text-light" href="landingpage.php"><button type="submit" class="btn btn-success w-50 mt-5 mb-5">Continue</button></a>
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
        // Function to fetch jobs based on selected filters
  function fetchJobs() {
    // Create an empty object to store filters dynamically
    const filters = {};

    // Fetch values from all filter elements
    const sortValue = $('#sort').val() || $('#jobsort').val();
    const locationValue = $('#location').val() || $('#joblocation').val();
    const minSalaryValue = $('#min_salary').val() || $('#jobmin_salary').val();
    const maxSalaryValue = $('#max_salary').val() || $('#jobmax_salary').val();
    const departmentValue = $('#department').val() || $('#jobdepartment').val();
    const searchValue = $('#search').val() || $('#searchjob').val(); // Added search filter

    // Add values to the filters object only if they exist (not empty)
    if (sortValue) filters.sort = sortValue;
    if (locationValue) filters.location = locationValue;
    if (minSalaryValue) filters.min_salary = minSalaryValue;
    if (maxSalaryValue) filters.max_salary = maxSalaryValue;
    if (departmentValue) filters.department = departmentValue;
    if (searchValue) filters.search = searchValue; // Add search filter

    console.log(filters); // Debugging: Check what filters are being sent

    // AJAX request to fetch jobs
    $.ajax({
        url: 'fetch_jobs.php',
        type: 'POST',
        data: filters, 
        beforeSend: function() {
        showLoader();
    },
    // Send only active filters
        success: function (response) {
            $('#job-list').html(response);
            hideLoader();
        },
        error: function () {
            $('#job-list').html('<p class="text-danger">Failed to load jobs. Please try again later.</p>');
            hideLoader();
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
    $('#search, #searchjob').on('input', function () {
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
            url: 'candidate/fetch_locations.php',
            type: 'GET',
            beforeSend: function() {
        showLoader();
    },
            success: function (locations) {
                console.log(locations);
                if (typeof locations === 'string') {
                    try {
                        locations = JSON.parse(locations);
                        hideLoader();
                    } catch (e) {
                        console.error('Invalid JSON response:', locations);
                         hideLoader();
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
                console.error('Failed to fetch locations:', error);
                 hideLoader();
            },
        });


/////////////////////////////////////// fetch Full Jobs ////////////
        $(document).ready(function() {
            $(document).on("click", "#loadMoreBtn", function() {
                $.ajax({
                    url: "fetch_jobs.php",
                    type: "POST",
                    data: { load_more: true },
                    beforeSend: function() {
        showLoader();
    },
                    // beforeSend: function() {
                    //     $("#loadMoreBtn").text("Loading...").prop("disabled", true);
                      
                    // },
                    success: function(response) {
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
              beforeSend: function() {
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
// ////////////////////////////Apply job /////////////////////////
    $(document).ready(function () {
    $(document).on("click", "#apply-job-btn, #job-apply-btn", function () {
        var jobCard = $(this).closest(".job-card");
        var jobId = jobCard.find(".job-grid").attr("data-id");
        var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>"; // Ensure mobile number is set

        console.log("Job ID:", jobId, "Mobile:", mobileNumber); // Debugging Output

        if (!jobId || !mobileNumber) {
            alert("Missing Job ID or Mobile Number!");
            return;
        }

        $.ajax({
            url: "candidate/apply_job.php",
            type: "POST",
            data: { job_id: jobId, mobile_number: mobileNumber },
            dataType: "json",
          beforeSend: function() {
        showLoader();
    },
            success: function (response) {
                if (response.status === "success") {
                   hideLoader();
                    alert("Successfully applied for the job!5");
                    fetchJobs();
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

//////////////////////////Insert candidate into Partners Table///////////////////
$(document).ready(function () {
    $(document).on("click", "#candidate-as-partner, #candidate-to-partner", function () {
        var jobCard = $(this).closest(".job-card");
        var jobId = jobCard.find(".job-grid").attr("data-id"); // Find the nearest job-grid parent
        var mobileNumber = "<?= $_SESSION['mobile'] ?? '' ?>"; // Ensure mobile number is set

        console.log("Job ID:", jobId, "Mobile:", mobileNumber); // Debugging Output

        if (!jobId || !mobileNumber) {
            alert("Missing Job ID or Mobile Number!");
            return;
        }

        $.ajax({
            url: "candidate/insert_partner.php",
            type: "POST",
            data: { job_id: jobId, mobile_number: mobileNumber },
            dataType: "json",
             beforeSend: function() {
        showLoader();
    },
            success: function (response) {
                console.log(response);
                if (response.status === "success") {
                   hideLoader();
                    alert("Successfully Changed as a Partner!");
                    window.location.href = "../partner/refer_candidate.php?jobid=" + jobId;
                } else if (response.status === "exist") {
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
// /////////////////// Pagination ////////////////
   $(document).ready(function() {
    function loadJobs(page) {
        $.ajax({
            url: 'fetch_jobs.php',
            type: 'POST',
            data: { page: page },
            beforeSend: function() {
                showLoader();
                // $('#job-container').html('<p>Loading...</p>');
            },
            success: function(response) {
                hideLoader();
                $('#job-container').html(response);
            }
        });
    }

    // Load first page initially
    loadJobs(1);

    // Handle pagination button clicks
    $(document).on('click', '.pagination-btn', function() {
        let page = $(this).data('page');
        loadJobs(page);
    });
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

// Add this JavaScript code to make the filter accordions work
$(document).ready(function() {
    // Toggle filter accordion sections
    $('.filter-accordion').on('click', function() {
        $(this).toggleClass('active');
        
        // Toggle the content panel
        const content = $(this).next('.filter-content');
        content.toggleClass('active');
        
        // Adjust the content panel max-height for smooth animation
        if (content.hasClass('active')) {
            content.css('max-height', content.prop('scrollHeight') + "px");
        } else {
            content.css('max-height', 0);
        }
    });
    
    // Initialize all filter sections (optional - if you want some open by default)
    $('.filter-accordion').first().addClass('active');
    $('.filter-content').first().addClass('active').css('max-height', $('.filter-content').first().prop('scrollHeight') + "px");
    
    // Rest of your existing JavaScript...
});
    </script>
</body>
</html>
