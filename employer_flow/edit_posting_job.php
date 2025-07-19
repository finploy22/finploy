<?php
include '../db/connection.php';
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    die("Required session variables are not set.");
}
$employer_name = $_SESSION['name'];
$employer_mobile = $_SESSION['mobile'];
$job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
$stmt = $conn->prepare("
    SELECT
      jobrole,
      department,
      companyname,
      location,
      salary,
      age,
      gender,
      experience,
      product,
      role_overview,
      key_responsibilities,
      job_requirements,
      created,
      job_status,
      education,
      no_of_positions,
      sub_department,
      sub_product,
      specialization,
      domain_relevant_experience,
      contact_person_name,
      contact_person_designation,
      contact_mobile_no,
      email_id,
      category,
      employer_mobile_no
    FROM job_id
    WHERE id = ?
    LIMIT 1
");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $job_id);
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();
$ageRange = explode('-', $job['age'] ?? '');
$age_min = $ageRange[0] ?? '';
$age_max = $ageRange[1] ?? '';
$salaryRange = explode('-', $job['salary'] ?? '');
$salary_min = $salaryRange[0] ?? '';
$salary_max = $salaryRange[1] ?? '';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Detail Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/posting_job.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<body>
    <?php include 'posting_header.php'; ?>
    <div class="main-div-posting-job">
        <div class="container mt-5">
            <div class="row mb-5 align-items-center justify-content-between">
                <div class="col-12 col-md-auto mb-md-0">
                    <span class="left-text">Post a New Job</span>
                </div>
                <div
                    class="col-12 col-md-auto d-flex align-items-center justify-content-md-end justify-content-between gap-3">
                    <span class="help-text">Need Help? Call us at +91 8169449669</span>
                    <span class="close-icon"> <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.7275 11.8545L27.273 26.3999M12.7275 26.3999L27.273 11.8545" stroke="#FF1A1A"
                                stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                </div>
            </div>
            <div class="p-5 form-posting ">
                <h4 class="mb-4 job-posting-form-title">Job Detail</h4>

                <div class="row mb-3 mb-4 align-items-center">

                    <label class="form-label fw-bold">Status</label>

                    <div class="col-auto">
                        <select class="form-select status-active">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                    </div>
                    <div class="col">
                        <p class="mb-0 ms-1 text-muted">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.99965 1.25C12.3205 1.25 14.5462 2.17194 16.1873 3.813C17.8283 5.45406 18.7503 7.67981 18.7503 10.0006C18.7503 12.3214 17.8283 14.5472 16.1873 16.1883C14.5462 17.8293 12.3205 18.7513 9.99965 18.7513C7.67884 18.7513 5.45308 17.8293 3.81202 16.1883C2.17096 14.5472 1.24902 12.3214 1.24902 10.0006C1.24902 7.67981 2.17096 5.45406 3.81202 3.813C5.45308 2.17194 7.67884 1.25 9.99965 1.25ZM11.3121 6.6225C11.9621 6.6225 12.4896 6.17125 12.4896 5.5025C12.4896 4.83375 11.9609 4.3825 11.3121 4.3825C10.6621 4.3825 10.1371 4.83375 10.1371 5.5025C10.1371 6.17125 10.6621 6.6225 11.3121 6.6225ZM11.5409 13.6562C11.5409 13.5225 11.5871 13.175 11.5609 12.9775L10.5334 14.16C10.3209 14.3837 10.0546 14.5387 9.92965 14.4975C9.87294 14.4766 9.82553 14.4362 9.79597 14.3835C9.76641 14.3308 9.75663 14.2693 9.7684 14.21L11.4809 8.8C11.6209 8.11375 11.2359 7.4875 10.4196 7.4075C9.5584 7.4075 8.2909 8.28125 7.51965 9.39C7.51965 9.5225 7.49465 9.8525 7.5209 10.05L8.54715 8.86625C8.75965 8.645 9.00715 8.48875 9.13215 8.53125C9.19373 8.55335 9.2442 8.59872 9.27271 8.65762C9.30122 8.71651 9.30551 8.78423 9.28465 8.84625L7.58715 14.23C7.3909 14.86 7.76215 15.4775 8.66215 15.6175C9.98715 15.6175 10.7696 14.765 11.5421 13.6562H11.5409Z"
                                    fill="#175DA8" />
                            </svg>
                            <span class="fw-bold text-active">Active:</span> <span class="text-active">Job post is
                                visible to job
                                seekers and open for applications. </span>
                            <span class="fw-bold text-active">Inactive:</span> <span class="text-active"> Job post is
                                hidden, and
                                applicants cannot apply.</span>
                        </p>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <input type="hidden" name="job_id" value="<?= htmlspecialchars($job_id ?? '') ?>">

                    <div class="col-md-4">
                        <label class="form-label job-title">Job Title / Description</label>
                        <input type="text" name="jobrole" class="form-control" placeholder="Type Job Title here"
                            autocomplete="off" value="<?= isset($job['jobrole']) ? ucfirst($job['jobrole']) : '' ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label Company-name ">Company You Are Hiring For</label>
                        <input type="text" name="companyname" class="form-control" placeholder="Type Company Name here"
                            autocomplete="off"
                            value="<?= isset($job['companyname']) ? ucfirst($job['companyname']) : '' ?>">
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label job-jocation ">Job Location</label>
                        <input type="text" name="location" class="form-control" placeholder="Type Job Location here"
                            autocomplete="off" value="<?= isset($job['location']) ? ucfirst($job['location']) : '' ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label education">Education</label>
                        <input type="text" name="education" class="form-control" placeholder="Enter required education"
                            autocomplete="off"
                            value="<?= isset($job['education']) ? ucfirst($job['education']) : '' ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label No-of-Positions">No. of Positions</label>
                        <input class="form-control" name="no_of_positions" placeholder="No" autocomplete="off"
                            value="<?= isset($job['no_of_positions']) ? $job['no_of_positions'] : '' ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Gender</label>
                        <select class="form-select gender" name="gender">
                            <option value="">Gender</option>
                            <option value="Male" <?= (isset($job['gender']) && $job['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= (isset($job['gender']) && $job['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>

                    </div>
                    <div class="col-md-4">
                        <div class="age">
                            <label class="form-label">Age</label>
                            <div class="d-flex align-items-center">
                                <input class="form-control me-2 w-25 age-min" name="age_min" placeholder="Min"
                                    autocomplete="off" value="<?= $age_min ?>">
                                <span class="mx-2 to-age">To</span>
                                <input class="form-control w-25 age-max" name="age_max" placeholder="Max"
                                    autocomplete="off" value="<?= $age_max ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="salary">
                            <label class="form-label">Salary (In Lakhs Per Annum - LPA)</label>
                            <div class="d-flex align-items-center">
                                <input class="form-control me-2 w-25 salary-min" name="salary_min" placeholder="Min"
                                    autocomplete="off" value="<?= $salary_min ?>">
                                <span class="mx-2 to-salary">To</span>
                                <input class="form-control w-25 salary-max" name="salary_max" placeholder="Max"
                                    autocomplete="off" value="<?= $salary_max ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Domain / Relevant Experience</label>
                        <input type="text" name="domain_relevant_experience" class="form-control domain-experience"
                            placeholder="Enter relevant experience" autocomplete="off"
                            value="<?= isset($job['domain_relevant_experience']) ? $job['domain_relevant_experience'] : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Total Experience</label>
                        <input type="text" name="experience" class="form-control total-experience"
                            placeholder="Enter total experience" autocomplete="off"
                            value="<?= isset($job['experience']) ? $job['experience'] : '' ?>">
                    </div>

                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Department</label>
                        <select class="form-select Department" name="department">
                            <option value="">Select Dept</option>
                            <option value="Sales" <?= (isset($job['department']) && $job['department'] == 'Sales') ? 'selected' : '' ?>>Sales</option>
                            <option value="Credit Dept" <?= (isset($job['department']) && $job['department'] == 'Credit Dept') ? 'selected' : '' ?>>Credit Dept</option>
                            <option value="HR / Training" <?= (isset($job['department']) && $job['department'] == 'HR / Training') ? 'selected' : '' ?>>HR / Training</option>
                            <option value="Operations" <?= (isset($job['department']) && $job['department'] == 'Operations') ? 'selected' : '' ?>>Operations</option>
                            <option value="Legal/Complaince/Risk" <?= (isset($job['department']) && $job['department'] == 'Legal/Complaince/Risk') ? 'selected' : '' ?>>Legal/Complaince/Risk
                            </option>
                            <option value="Others" <?= (isset($job['department']) && $job['department'] == 'Others') ? 'selected' : '' ?>>Others</option>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sub - Department</label>
                        <input type="text" name="sub_department" class="form-control" placeholder="Enter sub department"
                            autocomplete="off"
                            value="<?= isset($job['sub_department']) ? ucfirst($job['sub_department']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="Enter category"
                            autocomplete="off" value="<?= isset($job['category']) ? ucfirst($job['category']) : '' ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Product</label>
                        <select class="form-select Product" name="product">
                            <option value="">Select Product</option>
                            <option value="HL/LAP" <?= (isset($job['product']) && $job['product'] == 'HL/LAP') ? 'selected' : '' ?>>HL/LAP</option>
                            <option value="Business Loant" <?= (isset($job['product']) && $job['product'] == 'Business Loant') ? 'selected' : '' ?>>Business Loant</option>
                            <option value="Gold Loan" <?= (isset($job['product']) && $job['product'] == 'Gold Loan') ? 'selected' : '' ?>>Gold Loan</option>
                            <option value="CASA" <?= (isset($job['product']) && $job['product'] == 'CASA') ? 'selected' : '' ?>>CASA</option>
                            <option value="Personal Loan" <?= (isset($job['product']) && $job['product'] == 'Personal Loan') ? 'selected' : '' ?>>Personal Loan</option>
                            <option value="Education Loan" <?= (isset($job['product']) && $job['product'] == 'Education Loan') ? 'selected' : '' ?>>Education Loan</option>
                            <option value="Credit Cards" <?= (isset($job['product']) && $job['product'] == 'Credit Cards') ? 'selected' : '' ?>>Credit Cards</option>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sub - Product </label>
                        <input type="text" name="sub_product" class="form-control Sub-Product"
                            placeholder="Enter sub product" autocomplete="off"
                            value="<?= isset($job['sub_product']) ? ucfirst($job['sub_product']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" class="form-control specialization"
                            placeholder="Enter specialization" autocomplete="off"
                            value="<?= isset($job['specialization']) ? ucfirst($job['specialization']) : '' ?>">
                    </div>
                </div>


                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label Job-Description">Job Description</label>
                        <input type="hidden" name="role_overview" id="role_overview">
                        <div id="editor-container" style="height: 200px;"></div>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Contact Person Name</label>
                        <input type="text" name="contact_person_name" class="form-control Contact-Person-Name"
                            placeholder="Enter Contact Person Name" autocomplete="off"
                            value="<?= isset($job['contact_person_name']) ? ucfirst($job['contact_person_name']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contact Person Designation</label>
                        <input type="text" name="contact_person_designation" class="form-control Enter-Designation"
                            placeholder="Enter Designation" autocomplete="off"
                            value="<?= isset($job['contact_person_designation']) ? ucfirst($job['contact_person_designation']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contact Mobile No</label>
                        <input type="text" name="contact_mobile_no" class="form-control Contact-Mobile-No"
                            placeholder="Enter Mobile Number" autocomplete="off"
                            value="<?= isset($job['contact_mobile_no']) ? $job['contact_mobile_no'] : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email Id</label>
                        <input type="email" name="email_id" class="form-control Email-Id" placeholder="Enter Mail Id"
                            autocomplete="off" value="<?= isset($job['email_id']) ? $job['email_id'] : '' ?>">
                    </div>
                </div>


                <div class="text-center mt-4">
                    <button class="btn post-job-btn px-4">Post a Job Now <svg width="25" height="24" viewBox="0 0 25 24"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.5873 5.08188C5.99326 4.78486 5.33471 5.3499 5.53772 5.98245L6.96683 10.4243C6.99493 10.5116 7.0465 10.5895 7.11591 10.6495C7.18532 10.7094 7.26989 10.7492 7.36036 10.7643L13.2953 11.7539C13.5738 11.8004 13.5738 12.2004 13.2953 12.2469L7.36086 13.236C7.27029 13.251 7.18562 13.2907 7.11612 13.3507C7.04662 13.4107 6.99496 13.4886 6.96683 13.576L5.53772 18.0193C5.33421 18.6519 5.99276 19.2169 6.5873 18.9199L19.0852 12.6719C19.6383 12.3954 19.6383 11.6069 19.0852 11.3298L6.5873 5.08188Z"
                                fill="white" />
                        </svg></button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <?php
    require('demofooter.php');
    ?>
    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Write Role Overview, Key Responsibilities, Job Requirements...',
            modules: {
                toolbar: [['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }]]
            }
        });
        var savedDescription = <?= json_encode($job['role_overview'] ?? '') ?>;
        quill.root.innerHTML = savedDescription;

        document.addEventListener("DOMContentLoaded", function () {
            // Get form elements


            const form = document.querySelector(".form-posting");
            const postJobBtn = document.querySelector(".post-job-btn");

            // Quill editor content getter
            const getQuillContent = () => {
                return quill.getText().trim();
            };

            // Function to validate email format
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Function to validate phone number format
            function isValidPhone(phone) {
                // Allow +91 followed by 10 digits
                const phoneRegex = /^[6-9]\d{9}$/;
                return phoneRegex.test(phone);
            }

            // Function to add error message after an input field
            function showError(inputElement, message) {
                // Remove any existing error message
                clearError(inputElement);

                // Create error message element
                const errorElement = document.createElement("div");
                errorElement.className = "error-message text-danger mt-1";
                errorElement.innerText = message;

                // Insert error message after the input element
                if (inputElement.classList.contains("ql-container")) {
                    // Special case for Quill editor
                    inputElement.parentNode.after(errorElement);
                } else if (inputElement.type === "select-one") {
                    // Special case for select boxes
                    inputElement.parentNode.appendChild(errorElement);
                } else {
                    // For regular input fields
                    inputElement.after(errorElement);
                }

                // Add error class to input
                inputElement.classList.add("is-invalid");
            }

            // Function to add error message for range inputs (min/max pairs)
            function showRangeError(container, minInput, maxInput, message) {
                // Clear any existing errors
                clearError(minInput);
                clearError(maxInput);

                // Create error message element
                const errorElement = document.createElement("div");
                errorElement.className = "error-message text-danger mt-1";
                errorElement.innerText = message;

                // Insert error message after the container element
                container.insertAdjacentElement("afterend", errorElement);

                // Add error class to both inputs
                minInput.classList.add("is-invalid");
                maxInput.classList.add("is-invalid");
            }


            // Function to clear error message
            function clearError(inputElement) {
                // Remove error class from input
                inputElement.classList.remove("is-invalid");

                // Find and remove error message
                const parent = inputElement.parentNode;
                const errorElement = parent.querySelector(".error-message");
                if (errorElement) {
                    errorElement.remove();
                }
            }

            // Function to clear error for a range
            function clearRangeError(minInput, maxInput) {
                minInput.classList.remove("is-invalid");
                maxInput.classList.remove("is-invalid");

                const container = minInput.parentNode;
                const errorElement = container.querySelector(".error-message");
                if (errorElement) {
                    errorElement.remove();
                }
            }

            // Function to clear all error messages
            function clearAllErrors() {
                const errorMessages = form.querySelectorAll(".error-message");
                errorMessages.forEach(element => {
                    element.remove();
                });

                const invalidInputs = form.querySelectorAll(".is-invalid");
                invalidInputs.forEach(input => {
                    input.classList.remove("is-invalid");
                });
            }

            // Form validation function
            function validateForm() {
                // Clear all previous error messages
                clearAllErrors();
                const jobTitleInput = form.querySelector('input[placeholder="Type Job Title here"]');
                const companyNameInput = form.querySelector('input[placeholder="Type Company Name here"]');
                const jobLocationInput = form.querySelector('input[placeholder="Type Job Location here"]');
                const educationInput = form.querySelector('input[placeholder="Enter required education"]');
                const positionsInput = form.querySelector('input[placeholder="No"]');
                const genderSelect = form.querySelector('.gender');
                const ageMinInput = form.querySelector('.age-min');
                const ageMaxInput = form.querySelector('.age-max');
                const salaryMinInput = form.querySelector('.salary-min');
                const salaryMaxInput = form.querySelector('.salary-max');
                const domainExperienceInput = form.querySelector('.domain-experience');
                const totalExperienceInput = form.querySelector('.total-experience');
                const departmentSelect = form.querySelector('.Department');
                const subDepartmentInput = form.querySelector('input[placeholder="Enter sub department"]');
                const categoryInput = form.querySelector('input[placeholder="Enter category"]');
                const productSelect = form.querySelector('.Product');
                const subProductInput = form.querySelector('.Sub-Product');
                const specializationInput = form.querySelector('.specialization');
                const quillEditor = document.querySelector('.ql-container');
                const contactNameInput = form.querySelector('.Contact-Person-Name');
                const contactDesignationInput = form.querySelector('.Enter-Designation');
                const contactMobileInput = form.querySelector('.Contact-Mobile-No');
                const contactEmailInput = form.querySelector('.Email-Id');
                const jobTitle = jobTitleInput.value.trim();
                const companyName = companyNameInput.value.trim();
                const jobLocation = jobLocationInput.value.trim();
                const education = educationInput.value.trim();
                const positions = positionsInput.value.trim();
                const gender = genderSelect.value;
                const ageMin = ageMinInput.value.trim();
                const ageMax = ageMaxInput.value.trim();
                const salaryMin = salaryMinInput.value.trim();
                const salaryMax = salaryMaxInput.value.trim();
                const domainExperience = domainExperienceInput.value.trim();
                const totalExperience = totalExperienceInput.value.trim();
                const department = departmentSelect.value;
                const subDepartment = subDepartmentInput.value.trim();
                const category = categoryInput.value.trim();
                const product = productSelect.value;
                const subProduct = subProductInput.value.trim();
                const specialization = specializationInput.value.trim();
                const jobDescription = getQuillContent();
                const contactName = contactNameInput.value.trim();
                const contactDesignation = contactDesignationInput.value.trim();
                const contactMobile = contactMobileInput.value.trim();
                const contactEmail = contactEmailInput.value.trim();
                let isValid = true;

                if (!jobTitle) {
                    showError(jobTitleInput, "Job Title is required");
                    isValid = false;
                } else if (!/^[a-zA-Z0-9\s&.'\-]+$/.test(jobTitle)) {
                    showError(jobTitleInput, "Only letters, numbers, spaces, periods, apostrophes, hyphens, and ampersands are allowed");
                    isValid = false;
                }

                if (!companyName) {
                    showError(companyNameInput, "Company Name is required");
                    isValid = false;
                } else if (!/^[a-zA-Z0-9\s&.'\-]+$/.test(companyName)) {
                    showError(companyNameInput, "Only letters, numbers, spaces, periods, apostrophes, hyphens, and ampersands are allowed");
                    isValid = false;
                }

                if (!jobLocation) {
                    showError(jobLocationInput, "Job Location is required");
                    isValid = false;
                } else if (!/^[a-zA-Z\s]+$/.test(jobLocation)) {
                    showError(jobLocationInput, "Only letters and spaces allowed");
                    isValid = false;
                }

                if (!education) {
                    showError(educationInput, "Education is required");
                    isValid = false;
                } else if (!/^[a-zA-Z0-9\s().\-]+$/.test(education)) {
                    showError(educationInput, "Only letters, numbers, spaces, periods, hyphens, and parentheses are allowed");
                    isValid = false;
                }

                if (!positions) {
                    showError(positionsInput, "Number of Positions is required");
                    isValid = false;
                } else if (!/^\d+$/.test(positions)) {
                    showError(positionsInput, "Only whole numbers allowed");
                    isValid = false;
                } else if (parseInt(positions) < 1) {
                    showError(positionsInput, "Must be at least 1");
                    isValid = false;
                }


                if (gender === "Gender") {
                    showError(genderSelect, "Please select a Gender");
                    isValid = false;
                }

                // Age validation - COMBINED
                if (!ageMin && !ageMax) {
                    showRangeError(ageMinInput.parentNode, ageMinInput, ageMaxInput, "Age range is required");
                    isValid = false;
                } else if (!ageMin) {
                    showRangeError(ageMinInput.parentNode, ageMinInput, ageMaxInput, "Complete age range is required");
                    isValid = false;
                } else if (!ageMax) {
                    showRangeError(ageMinInput.parentNode, ageMinInput, ageMaxInput, "Complete age range is required");
                    isValid = false;
                } else if (Number(ageMin) >= Number(ageMax)) {
                    showRangeError(ageMinInput.parentNode, ageMinInput, ageMaxInput, "Maximum Age must be greater than Minimum Age");
                    isValid = false;
                } else if (!/^\d+$/.test(ageMin) || !/^\d+$/.test(ageMax)) {
                    showRangeError(ageMinInput.parentNode, ageMinInput, ageMaxInput, "Only whole numbers allowed in Age");
                    isValid = false;
                }


                // Salary validation - COMBINED
                if (!salaryMin && !salaryMax) {
                    showRangeError(salaryMinInput.parentNode, salaryMinInput, salaryMaxInput, "Salary range is required");
                    isValid = false;
                } else if (!salaryMin) {
                    showRangeError(salaryMinInput.parentNode, salaryMinInput, salaryMaxInput, "Complete salary range is required");
                    isValid = false;
                } else if (!salaryMax) {
                    showRangeError(salaryMinInput.parentNode, salaryMinInput, salaryMaxInput, "Complete salary range is required");
                    isValid = false;
                } else if (Number(salaryMin) >= Number(salaryMax)) {
                    showRangeError(salaryMinInput.parentNode, salaryMinInput, salaryMaxInput, "Maximum Salary must be greater than Minimum Salary");
                    isValid = false;
                } else if (!/^\d+$/.test(salaryMin) || !/^\d+$/.test(salaryMax)) {
                    showRangeError(salaryMinInput.parentNode, salaryMinInput, salaryMaxInput, "Only whole numbers allowed in Salary");
                    isValid = false;
                }


                // Experience validation
                const experiencePattern = /^(\d+(\.\d{1,2})?)\s*(years?|yrs?)?(\s+\d+\s*(months?|mos?))?$/i;

                if (!domainExperience) {
                    showError(domainExperienceInput, "Domain Experience is required");
                    isValid = false;
                } else if (!experiencePattern.test(domainExperience.trim())) {
                    showError(domainExperienceInput, "Enter valid experience (e.g., 2, 2.5 years, or 2 years 6 months)");
                    isValid = false;
                } else if (domainExperience.includes('-')) {
                    showError(domainExperienceInput, "Experience cannot be negative");
                    isValid = false;
                }

                if (!totalExperience) {
                    showError(totalExperienceInput, "Domain Experience is required");
                    isValid = false;
                } else if (!experiencePattern.test(totalExperience.trim())) {
                    showError(totalExperienceInput, "Enter valid experience (e.g., 2, 2.5 years, or 2 years 6 months)");
                    isValid = false;
                } else if (totalExperience.includes('-')) {
                    showError(totalExperienceInput, "Experience cannot be negative");
                    isValid = false;
                }

                // Department validation
                if (department === "Select Dept") {
                    showError(departmentSelect, "Department is required");
                    isValid = false;
                }

                if (!subDepartment) {
                    showError(subDepartmentInput, "Sub-Department is required");
                    isValid = false;
                } else if (!/^[a-zA-Z\s&\-]+$/.test(subDepartment)) {
                    showError(subDepartmentInput, "Only letters, spaces, hyphens, and ampersands allowed");
                    isValid = false;
                }

                if (!category) {
                    showError(categoryInput, "Category is required");
                    isValid = false;
                } else if (!/^[a-zA-Z\s&\-]+$/.test(category)) {
                    showError(categoryInput, "Only letters, spaces, hyphens, and ampersands allowed");
                    isValid = false;
                }


                // Product validation
                if (product === "Select Product") {
                    showError(productSelect, "Product is required");
                    isValid = false;
                }

                if (!subProduct) {
                    showError(subProductInput, "Sub-Product is required");
                    isValid = false;
                } else if (!/^[a-zA-Z\s&\-]+$/.test(subProduct)) {
                    showError(subProductInput, "Only letters, spaces, hyphens, and ampersands allowed");
                    isValid = false;
                }

                if (!specialization) {
                    showError(specializationInput, "Specialization is required");
                    isValid = false;
                } else if (!/^[a-zA-Z\s&\-]+$/.test(specialization)) {
                    showError(specializationInput, "Only letters, spaces, hyphens, and ampersands allowed");
                    isValid = false;
                }

                // Job description validation
                if (jobDescription === "<p><br></p>" || !jobDescription.trim()) {
                    showError(quillEditor, "Job Description is required");
                    isValid = false;
                }

                // Contact information validation
                if (!contactName) {
                    showError(contactNameInput, "Contact Person Name is required");
                    isValid = false;
                } else if (!/^[a-zA-Z\s]+$/.test(contactName)) {
                    showError(contactNameInput, "Only letters and spaces allowed");
                    isValid = false;
                }

                if (!contactDesignation) {
                    showError(contactDesignationInput, "Contact Person Designation is required");
                    isValid = false;
                } else if (!/^[a-zA-Z\s]+$/.test(contactDesignation)) {
                    showError(contactDesignationInput, "Only letters and spaces allowed");
                    isValid = false;
                }

                if (!contactMobile) {
                    showError(contactMobileInput, "Contact Mobile Number is required");
                    isValid = false;
                } else if (!isValidPhone(contactMobile)) {
                    showError(contactMobileInput, "Please Enter Correct Mobile Number");
                    isValid = false;
                }

                if (!contactEmail) {
                    showError(contactEmailInput, "Email ID is required");
                    isValid = false;
                } else if (!isValidEmail(contactEmail)) {
                    showError(contactEmailInput, "Please enter a valid email address");
                    isValid = false;
                }

                return isValid;
            }

            // Add input event listeners to clear errors on input
            const allInputs = form.querySelectorAll('input, select');
            allInputs.forEach(input => {
                input.addEventListener('input', function () {
                    // Special handling for min/max pairs
                    if (this.classList.contains('age-min') || this.classList.contains('age-max')) {
                        const minInput = form.querySelector('.age-min');
                        const maxInput = form.querySelector('.age-max');
                        clearRangeError(minInput, maxInput);
                    } else if (this.classList.contains('salary-min') || this.classList.contains('salary-max')) {
                        const minInput = form.querySelector('.salary-min');
                        const maxInput = form.querySelector('.salary-max');
                        clearRangeError(minInput, maxInput);
                    } else {
                        clearError(this);
                    }
                });

                if (input.type === 'select-one') {
                    input.addEventListener('change', function () {
                        clearError(this);
                    });
                }
            });

            // Special handler for Quill editor
            quill.on('text-change', function () {
                clearError(document.querySelector('.ql-container'));
            });

            // Form submission handler
            postJobBtn.addEventListener("click", function (e) {
                e.preventDefault();

                if (validateForm()) {
                    // Create form data for submission
                    const formData = new FormData();

                    // Add all form fields to formData
                    formData.append('job_id', document.querySelector('input[name="job_id"]').value);


                    formData.append('jobrole', form.querySelector('input[placeholder="Type Job Title here"]').value.trim());
                    formData.append('companyname', form.querySelector('input[placeholder="Type Company Name here"]').value.trim());
                    formData.append('location', form.querySelector('input[placeholder="Type Job Location here"]').value.trim());
                    formData.append('education', form.querySelector('input[placeholder="Enter required education"]').value.trim());
                    formData.append('no_of_positions', form.querySelector('input[placeholder="No"]').value.trim());
                    formData.append('gender', form.querySelector('.gender').value);
                    formData.append('age_min', form.querySelector('.age-min').value.trim());
                    formData.append('age_max', form.querySelector('.age-max').value.trim());
                    formData.append('salary_min', form.querySelector('.salary-min').value.trim());
                    formData.append('salary_max', form.querySelector('.salary-max').value.trim());
                    formData.append('domain_relevant_experience', form.querySelector('.domain-experience').value.trim());
                    formData.append('experience', form.querySelector('.total-experience').value.trim());
                    formData.append('department', form.querySelector('.Department').value);
                    formData.append('sub_department', form.querySelector('input[placeholder="Enter sub department"]').value.trim());
                    formData.append('category', form.querySelector('input[placeholder="Enter category"]').value.trim());
                    formData.append('product', form.querySelector('.Product').value);
                    formData.append('sub_product', form.querySelector('.Sub-Product').value.trim());
                    formData.append('specialization', form.querySelector('.specialization').value.trim());
                    formData.append('role_overview', getQuillContent());
                    formData.append('contact_person_name', form.querySelector('.Contact-Person-Name').value.trim());
                    formData.append('contact_person_designation', form.querySelector('.Enter-Designation').value.trim());
                    formData.append('contact_mobile_no', form.querySelector('.Contact-Mobile-No').value.trim());
                    formData.append('email_id', form.querySelector('.Email-Id').value.trim());
                    formData.append('job_status', form.querySelector('.status-active').value);

                    // Submit form using fetch API
                    fetch('edit_job_listing.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => {
                            return response.json().catch(() => {
                                // If the response is not valid JSON, log the raw response for debugging
                                return response.text().then(text => {
                                    console.error('Error: Response is not valid JSON', text);
                                    throw new Error('Invalid JSON response');
                                });
                            });
                        })
                        .then(data => {
                            if (data.success) {
                                alert('Job Updated successfully!');
                                window.location.href = 'employer_joblisting_page.php';
                            } else {
                                alert('Error posting job: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while posting the job.');
                        });

                }
            });
        });

        document.getElementById("available-credits").addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent closing immediately

            let dropdown = document.getElementById("credits-dropdown"); // Check if modal already exists

            if (!dropdown) {
                // Fetch the modal content from external file
                fetch("available_credits.php")
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById("credits-container").innerHTML = html; // Load the popup
                        showDropdown();
                    });
            } else {
                showDropdown();
            }
        });

        function showDropdown() {
            let dropdown = document.getElementById("credits-dropdown");

            // Positioning near button
            let button = document.getElementById("available-credits");
            let rect = button.getBoundingClientRect();
            dropdown.style.top = 82 + "px";
            dropdown.style.left = 550 + "px";
            dropdown.style.display = "block";

            // Close dropdown when clicking outside
            document.addEventListener("click", function closeDropdown(event) {
                if (!dropdown.contains(event.target) && event.target.id !== "available-credits") {
                    dropdown.style.display = "none";
                    document.removeEventListener("click", closeDropdown);
                }
            });
        }
    </script>
    <script>
        document.querySelector('form').addEventListener('submit', function () {
            document.querySelector('#role_overview').value = quill.root.innerHTML;
        });
    </script>

</body>

</html>