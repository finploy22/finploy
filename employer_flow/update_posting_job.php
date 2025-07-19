<?php
// employer_joblisting_page.php
// ----------------------------------------------------
// This file lists all jobs for an employer. Make sure
// there is no whitespace or BOM before the opening tag.
// ----------------------------------------------------

// Optionally turn on output buffering to avoid "headers sent" issues
ob_start();

// Show full error reporting during development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session before any output
session_start();
include '../db/connection.php';
// Redirect unauthorized users to login page
if (empty($_SESSION['name']) || empty($_SESSION['mobile'])) {
    header('Location: login.php');
    exit;
}

// Grab employer info from session
$employer_name   = $_SESSION['name'];
$employer_mobile = $_SESSION['mobile'];

// Fetch product, sub prdouct, department, sub department 
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_products = $result->fetch_all(MYSQLI_ASSOC);
}

$sql = "SELECT * FROM departments";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_departments = $result->fetch_all(MYSQLI_ASSOC);
}else{
    $row_departments = [];
}

// Fetch old data from job_id table 
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "
    SELECT 
        job_id.*, 
        locations.area, 
        locations.city, 
        locations.state, 
        sub_departments.sub_department_name, 
        sub_products.sub_product_name, 
        products_specialization.specialization AS specialization_name,
        departments_category.category AS category_name
    FROM job_id 
    JOIN locations ON job_id.location_code = locations.id 
    JOIN sub_departments ON job_id.sub_department = sub_departments.sub_department_id 
    JOIN sub_products ON job_id.sub_product = sub_products.sub_product_id 
    JOIN products_specialization ON job_id.specialization = products_specialization.specialization_id 
    JOIN departments_category ON job_id.category = departments_category.category_id 
    WHERE job_id.id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $job_id);
$stmt->execute();

$result = $stmt->get_result();
$row_datas = [];

if ($result->num_rows > 0) {
    $row_datas = $result->fetch_assoc();
}

$stmt->close();

    // echo "<pre>";
    // print_r($row_datas);
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
        <!-- Left Section -->
        <div class="col-12 col-md-auto mb-md-0">
          <span class="left-text">Post a New Job</span>
        </div>

        <!-- Right Section -->
        <div class="col-12 col-md-auto d-flex align-items-center justify-content-md-end justify-content-between gap-3">
          <span class="help-text">Need Help? Call us at +91 8169449669</span>
          <span  id="close-icon" style="cursor: pointer;">      <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M12.7275 11.8545L27.273 26.3999M12.7275 26.3999L27.273 11.8545" stroke="#FF1A1A" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
</svg></span >
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
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M9.99965 1.25C12.3205 1.25 14.5462 2.17194 16.1873 3.813C17.8283 5.45406 18.7503 7.67981 18.7503 10.0006C18.7503 12.3214 17.8283 14.5472 16.1873 16.1883C14.5462 17.8293 12.3205 18.7513 9.99965 18.7513C7.67884 18.7513 5.45308 17.8293 3.81202 16.1883C2.17096 14.5472 1.24902 12.3214 1.24902 10.0006C1.24902 7.67981 2.17096 5.45406 3.81202 3.813C5.45308 2.17194 7.67884 1.25 9.99965 1.25ZM11.3121 6.6225C11.9621 6.6225 12.4896 6.17125 12.4896 5.5025C12.4896 4.83375 11.9609 4.3825 11.3121 4.3825C10.6621 4.3825 10.1371 4.83375 10.1371 5.5025C10.1371 6.17125 10.6621 6.6225 11.3121 6.6225ZM11.5409 13.6562C11.5409 13.5225 11.5871 13.175 11.5609 12.9775L10.5334 14.16C10.3209 14.3837 10.0546 14.5387 9.92965 14.4975C9.87294 14.4766 9.82553 14.4362 9.79597 14.3835C9.76641 14.3308 9.75663 14.2693 9.7684 14.21L11.4809 8.8C11.6209 8.11375 11.2359 7.4875 10.4196 7.4075C9.5584 7.4075 8.2909 8.28125 7.51965 9.39C7.51965 9.5225 7.49465 9.8525 7.5209 10.05L8.54715 8.86625C8.75965 8.645 9.00715 8.48875 9.13215 8.53125C9.19373 8.55335 9.2442 8.59872 9.27271 8.65762C9.30122 8.71651 9.30551 8.78423 9.28465 8.84625L7.58715 14.23C7.3909 14.86 7.76215 15.4775 8.66215 15.6175C9.98715 15.6175 10.7696 14.765 11.5421 13.6562H11.5409Z" fill="#175DA8"/>
</svg>
            <span class="fw-bold text-active">Active:</span> <span class="text-active">Job post is visible to job seekers and open for applications. </span> 
            <span class="fw-bold text-active">Inactive:</span> <span class="text-active"> Job post is hidden, and applicants cannot apply.</span> 
        </p>
    </div>
</div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label job-title">Job Title / Description</label>
                <input type="text" class="form-control" placeholder="Type Job Title here" autocomplete="off" value="<?=$row_datas['jobrole'];?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label Company-name ">Company You Are Hiring For</label>
                <input type="text" class="form-control" placeholder="Type Company Name here"autocomplete="off" value="<?=$row_datas['companyname'];?>">
            </div>
            </div>
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label job-jocation ">Job Location</label>
              <style>
                    #suggestion-list li {
                        color: black !important;
                        border-bottom: 1px solid #eee !important;
                        padding: 8px 15px !important;
                    }
                    #searching-item {
                        font-size: 13px;
                        list-style: none;
                        margin: 0 auto;
                        padding: 0;
                        border: 1px solid #ccc;
                        border-right: 0px solid !important;
                        border-left: 0px solid !important;
                        border-radius: 0 0 8px 8px;
                        background-color: white;
                        width: 97%;
                        max-height: 200px;
                        overflow-y: auto;
                        z-index: 10;
                        box-shadow: 2px 0px 7.467px 0px rgba(108, 99, 99, 0.20);
                    }

                    #searching-item li {
                        color: #888888;
                        padding: 8px;
                        cursor: pointer;
                        text-align: left;
                    }
                </style>
             <input type="text" class="form-control login-inputfield" id="search_location"
    placeholder="Select city here" name="location" required autocomplete="off"
    value="<?= $row_datas['area'] . ", " . $row_datas['city'] . ", " . $row_datas['state']; ?>">

<input type="hidden" id="location_id" name="location_id" value="<?= $row_datas['location_code']; ?>">

                                        <div id="select-div"  style="display: none;"></div>
          </div>
        </div>
              <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label education">Education</label>
                <input type="text" class="form-control" value="<?= $row_datas['education']; ?>" placeholder="Enter required education"autocomplete="off">
            </div>
            
            <div class="col-md-2">
                <label class="form-label No-of-Positions">No. of Positions</label>
              <input class="form-control" value="<?= $row_datas['no_of_positions']; ?>" placeholder="No"autocomplete="off">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Gender</label>
                     <select class="form-select gender" name="gender" required>
                        <option disabled <?= empty($row_datas['gender']) ? 'selected' : '' ?>>Gender</option>
                        <option <?= $row_datas['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option <?= $row_datas['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option <?= $row_datas['gender'] === 'Both' ? 'selected' : '' ?>>Both</option>
                    </select>

            </div>
        <div class="col-md-4">
            <?php
            $age_min = '';
            $age_max = '';
            
            if (!empty($row_datas['age'])) {
                $age_parts = explode('-', $row_datas['age']);
                $age_min = $age_parts[0];
                $age_max = $age_parts[1] ?? ''; // in case there's no max
            }
            ?>
            <div class="age">
                <label class="form-label">Age</label>
                <div class="d-flex align-items-center">
                    <input class="form-control me-2 w-25 age-min" placeholder="Min" autocomplete="off" value="<?= $age_min; ?>">
                    <span class="mx-2 to-age">To</span>
                    <input class="form-control w-25 age-max" placeholder="Max" autocomplete="off" value="<?= $age_max; ?>">
                </div>
            </div>
        </div>
       </div>
            
             <div class="row g-3 mb-4">
               
            <div class="col-md-4">
                
   <?php
$salary_min = '';
$salary_max = '';

if (!empty($row_datas['salary'])) {
    $salary_parts = explode('-', $row_datas['salary']);
    $salary_min = $salary_parts[0];
    $salary_max = $salary_parts[1] ?? ''; 
}
?>

<div class="salary">
    <label class="form-label">Salary (In Lakhs Per Annum - LPA)</label>
    <div class="d-flex align-items-center">
        <input class="form-control me-2 w-25 salary-min" placeholder="Min" autocomplete="off" value="<?= $salary_min; ?>">
        <span class="mx-2 to-salary">To</span>
        <input class="form-control w-25 salary-max" placeholder="Max" autocomplete="off" value="<?= $salary_max; ?>">
    </div>
</div>

            </div>
            <div class="col-md-3">
                <label class="form-label">Domain / Relevant Experience</label>
                 <input type="text" class="form-control domain-experience" placeholder="Enter relevant experience"autocomplete="off" value="<?= $row_datas['domain_relevant_experience']; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Total Experience</label>
                  <input type="text" class="form-control total-experience" placeholder="Enter total experience"autocomplete="off" value="<?= $row_datas['experience']; ?>">
            </div>
            
            </div>
            
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <label class="form-label">Department</label>
              <?php if ($row_departments): ?>
                  <select class="form-select Department" id="departments" name="departments">
                          <option selected>Select Department</option>
                      <?php foreach ($row_departments as $department): ?>
                          <option value="<?= $department['department_id']; ?>" data-name="<?= $department['department_name']; ?>"  <?= $row_datas['department'] == $department['department_id'] ? 'selected' : '' ?>>
                              <?= $department['department_name']; ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              <?php endif; ?>

          </div>
          <div class="col-md-3" id="subdepartment-div">
               <label class="form-label">Sub - Department</label>
                 <select class="form-select Department" id="sub-departments" name="sub_department">
                  <option value="<?= $row_datas['department']; ?>" data-name="<?= $row_datas['sub_department_name']; ?>">
                              <?= $row_datas['sub_department_name']; ?>
                          </option>
                </select>
          </div>
          <div class="col-md-3" id="departmentcategory-div">
         <label class="form-label">Category</label>
       <!--<input type="text" class="form-control" name="department_category" id="department_category" placeholder="Enter category" autocomplete="off">-->
                <select class="form-select Department" id="department_category" name="department_category">
                    <!--<option >Select Category</option>-->
                 <option selected value="<?= $row_datas['category']; ?>" data-name="<?= $row_datas['category_name']; ?>">
                              <?= $row_datas['category_name']; ?>
                          </option>
                </select>
          </div>
        </div>


            
         <div class="row g-3 mb-4">
          <div class="col-md-3">
            <label class="form-label">Product</label>
              <?php if ($row_products): ?>
                  <select class="form-select Product" id="products" name="products">
                          <option selected>Select Product</option>
                      <?php foreach ($row_products as $product): ?>
                          <option value="<?= $product['product_id']; ?>" data-name="<?= $product['product_name']; ?>"  <?= $row_datas['product'] == $product['product_id'] ? 'selected' : '' ?>>
                              <?= $product['product_name']; ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              <?php endif; ?>
          </div>
          <div class="col-md-3" id="subproduct-div" >
              <label class="form-label">Sub - Product</label>
                <select class="form-select Product" id="sub-products" name="sub_product">
                    <option value="<?= $row_datas['sub_product']; ?>" data-name="<?= $row_datas['sub_product_name']; ?>">
                              <?= $row_datas['sub_product_name']; ?>
                          </option>
                </select>
          </div>
          <div class="col-md-3" id="productspecialization-div">
               <label class="form-label">Specialization</label>
                 <select class="form-select Product" id="product_specialization" name="product_specialization">
                    <option value="<?= $row_datas['specialization']; ?>" data-name="<?= $row_datas['specialization_name']; ?>">
                              <?= $row_datas['specialization_name']; ?>
                          </option>
                </select>
           <!--<input type="text" class="form-control specialization"  name="product_specialization" id="product_specialization" placeholder="Enter specialization"autocomplete="off">-->

          </div>
        </div>

            
            
             <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label Job-Description">Job Description</label>
                 <label class="form-label">Job Description</label>
                 <div id="editor-container" style="height: 200px;"></div>
            </div>
              </div>
               <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Contact Person Name</label>
                <input type="text" class="form-control Contact-Person-Name" placeholder="Enter Contact Person Name"autocomplete="off" value="<?= $row_datas['contact_person_name']; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Contact Person Designation</label>
                <input type="text" class="form-control Enter-Designation"placeholder="Enter Designation"autocomplete="off" value="<?= $row_datas['contact_person_designation']; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Contact Mobile No</label>
                <input type="text" maxlength="10" class="form-control Contact-Mobile-No"placeholder="Enter Mobile Number"autocomplete="off" value="<?= $row_datas['contact_mobile_no']; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Email Id</label>
                <input type="email" class="form-control Email-Id"placeholder="Enter Mail Id"autocomplete="off" value="<?= $row_datas['email_id']; ?>">
            </div>
            </div>
        

        <div class="text-center mt-4">
            <button class="btn post-job-btn px-4">Post a Job Now <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M6.5873 5.08188C5.99326 4.78486 5.33471 5.3499 5.53772 5.98245L6.96683 10.4243C6.99493 10.5116 7.0465 10.5895 7.11591 10.6495C7.18532 10.7094 7.26989 10.7492 7.36036 10.7643L13.2953 11.7539C13.5738 11.8004 13.5738 12.2004 13.2953 12.2469L7.36086 13.236C7.27029 13.251 7.18562 13.2907 7.11612 13.3507C7.04662 13.4107 6.99496 13.4886 6.96683 13.576L5.53772 18.0193C5.33421 18.6519 5.99276 19.2169 6.5873 18.9199L19.0852 12.6719C19.6383 12.3954 19.6383 11.6069 19.0852 11.3298L6.5873 5.08188Z" fill="white"/>
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
document.addEventListener("DOMContentLoaded", function() {
  // Get form elements
  const form = document.querySelector(".form-posting");
  const postJobBtn = document.querySelector(".post-job-btn");
  
  // Initialize Quill editor
  var quill = new Quill('#editor-container', {
    theme: 'snow',
    placeholder: 'Write Role Overview, Key Responsibilities, Job Requirements...',
    modules: {
      toolbar: [['bold', 'italic', 'underline'], [{ 'list': 'ordered'}, { 'list': 'bullet' }]]
    }
  });
  quill.root.innerHTML = `<?= $row_datas['role_overview']; ?>`;
  
  // Check if we're in edit mode by looking for job_id in URL
  const urlParams = new URLSearchParams(window.location.search);
  const jobId = urlParams.get('id');
  
  // If job ID exists, load job data
  if (jobId) {
    // Change button text to indicate we're updating
    if (postJobBtn) {
      postJobBtn.textContent = "Update Job";
    }
    
    // Fetch job data
    // fetch(`update_joblisting.php?id=${jobId}`)
    //   .then(response => response.json())
    //   .then(data => {
    //     if (data.success) {
    //       // Populate form with job data
    //       populateForm(data.data);
    //     } else {
    //       alert('Error loading job data: ' + data.message);
    //     }
    //   })
    //   .catch(error => {
    //     console.error('Error:', error);
    //     alert('An error occurred while loading the job data.');
    //   });
  }
  

  // Quill editor content getter
  const getQuillContent = () => {
     return quill.root.innerHTML;
  };
  
  // Form validation function - Same as your original code
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
  
  function isValidPhone(phone) {
    const phoneRegex = /^[0-9]\d{9}$/;
    return phoneRegex.test(phone);
  }
  
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
  
  function clearRangeError(minInput, maxInput) {
    minInput.classList.remove("is-invalid");
    maxInput.classList.remove("is-invalid");
    
    const container = minInput.parentNode;
    const errorElement = container.querySelector(".error-message");
    if (errorElement) {
      errorElement.remove();
    }
  }
  
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
  
  // Form validation function - with all your validation logic
  function validateForm() {
    // Clear all previous error messages
    clearAllErrors();
    
    // Get all required field values and elements
    const jobTitleInput = form.querySelector('input[placeholder="Type Job Title here"]');
    const companyNameInput = form.querySelector('input[placeholder="Type Company Name here"]');
 const jobLocationInput = form.querySelector('input[id="location_id"]');
    const educationInput = form.querySelector('input[placeholder="Enter required education"]');
    const positionsInput = form.querySelector('input[placeholder="No"]');
    const genderSelect = form.querySelector('.gender');
    const ageMinInput = form.querySelector('.age-min');
    const ageMaxInput = form.querySelector('.age-max');
    
    const salaryMinInput = form.querySelector('.salary-min');
    const salaryMaxInput = form.querySelector('.salary-max');
    const domainExperienceInput = form.querySelector('.domain-experience');
    const totalExperienceInput = form.querySelector('.total-experience');
    
        const departmentSelect = form.querySelector('#departments');
        const subDepartmentInput = form.querySelector('#sub-departments');
        const categoryInput = form.querySelector('#department_category');
        const productSelect = form.querySelector('#products');
        const subProductInput = form.querySelector('#sub-products');
        const specializationInput = form.querySelector('#product_specialization');
        
    const quillEditor = document.querySelector('.ql-container');
    const contactNameInput = form.querySelector('.Contact-Person-Name');
    const contactDesignationInput = form.querySelector('.Enter-Designation');
    const contactMobileInput = form.querySelector('.Contact-Mobile-No');
    const contactEmailInput = form.querySelector('.Email-Id');
    
    // Extract values
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
    
    // Validation checks with inline error messages
    let isValid = true;
    
    // Required fields validation
    if (!jobTitle) {
      showError(jobTitleInput, "Job Title is required");
      isValid = false;
    }
    // else if(!/^[a-zA-Z\s]+$/.test(jobTitle)) {
    //   showError(jobTitleInput, "Only letters and spaces allowed");
    //   isValid = false;
    // }
    
    if (!companyName) {
      showError(companyNameInput, "Company Name is required");
      isValid = false;
    }
    // else if(!/^[a-zA-Z\s]+$/.test(companyName)) {
    //   showError(companyNameInput, "Only letters and spaces allowed");
    //   isValid = false;
    // }
    
    if (!jobLocation) {
      showError(jobLocationInput, "Job Location is required");
      isValid = false;
    }
    
    if (!education) {
      showError(educationInput, "Education is required");
      isValid = false;
    } 
    // else if(!/^[a-zA-Z\s]+$/.test(education)) {
    //   showError(educationInput, "Only letters and spaces allowed");
    //   isValid = false;
    // }
    
    if (!positions) {
      showError(positionsInput, "Number of Positions is required");
      isValid = false;
    } else if (!/^\d+$/.test(positions)) {
      showError(positionsInput, "Only whole numbers allowed");
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
    }
    // else if (!/^\d+$/.test(salaryMin) || !/^\d+$/.test(salaryMax)) {
    //   showRangeError(salaryMinInput.parentNode, salaryMinInput, salaryMaxInput, "Only whole numbers allowed in Salary");
    //   isValid = false;
    // }
    
    // Experience validation
    if (!domainExperience) {
      showError(domainExperienceInput, "Domain Experience is required");
      isValid = false;
    }
    
    if (!totalExperience) {
      showError(totalExperienceInput, "Total Experience is required");
      isValid = false;
    }
    
    // Department validation
    if (department === "Select Department") {
      showError(departmentSelect, "Department is required");
      isValid = false;
    }
    if (subDepartment === "Select Sub Department") {
      showError(subDepartmentInput, "Sub-Department is required");
      isValid = false;
    }
    if (category === "Select Category") {
      showError(categoryInput, "Category is required");
      isValid = false;
    }
    if (product === "Select Product") {
      showError(productSelect, "Department is required");
      isValid = false;
    }
    if (subProduct === "Select Sub Product") {
      showError(subProductInput, "Sub-Product is required");
      isValid = false;
    }
    if (specialization === "Select Specialization") {
      showError(specializationInput, "Specialization is required");
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
    input.addEventListener('input', function() {
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
      input.addEventListener('change', function() {
        clearError(this);
      });
    }
  });
  
  // Special handler for Quill editor
  quill.on('text-change', function() {
    clearError(document.querySelector('.ql-container'));
  });
  
  // Form submission handler
  postJobBtn.addEventListener("click", function(e) {
    e.preventDefault();
    
    if (validateForm()) {
      // Create form data for submission
      const formData = new FormData();
      
      // Add Quill content
      formData.append('role_overview', getQuillContent());
       formData.append('jobrole', form.querySelector('input[placeholder="Type Job Title here"]').value.trim());
      formData.append('companyname', form.querySelector('input[placeholder="Type Company Name here"]').value.trim());
      formData.append('location', form.querySelector('input[id="location_id"]').value.trim());
      formData.append('education', form.querySelector('input[placeholder="Enter required education"]').value.trim());
      formData.append('no_of_positions', form.querySelector('input[placeholder="No"]').value.trim());
      formData.append('gender', form.querySelector('.gender').value);
      formData.append('age_min', form.querySelector('.age-min').value.trim());
      formData.append('age_max', form.querySelector('.age-max').value.trim());
      formData.append('salary_min', form.querySelector('.salary-min').value.trim());
      formData.append('salary_max', form.querySelector('.salary-max').value.trim());
      formData.append('domain_relevant_experience', form.querySelector('.domain-experience').value.trim());
      formData.append('experience', form.querySelector('.total-experience').value.trim());
      
          formData.append('department', form.querySelector('#departments').value);
          formData.append('sub_department', form.querySelector('#sub-departments').value);
          formData.append('category', form.querySelector('#department_category').value);
          formData.append('product', form.querySelector('#products').value);
          formData.append('sub_product', form.querySelector('#sub-products').value);
          formData.append('specialization', form.querySelector('#product_specialization').value);
          
      formData.append('role_overview', getQuillContent());
      formData.append('contact_person_name', form.querySelector('.Contact-Person-Name').value.trim());
      formData.append('contact_person_designation', form.querySelector('.Enter-Designation').value.trim());
      formData.append('contact_mobile_no', form.querySelector('.Contact-Mobile-No').value.trim());
      formData.append('email_id', form.querySelector('.Email-Id').value.trim());
      formData.append('job_status', form.querySelector('.status-active').value);
      formData.append('job_id', jobId);
      
      // Submit form using fetch API
      fetch('update_joblisting.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (jobId) {
            alert('Job updated successfully!');
          } else {
            alert('Job posted successfully!');
          }
          // Redirect to employer dashboard
          window.location.href = 'employer_joblisting_page.php';
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the job.');
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



  $(document).on("change", "#departments", function () {
    var department_id = $(this).val();
    $.ajax({
        url: 'fetch_subdetails_employer.php',
        method: 'POST',
        data: { department_id: department_id },
        success: function (response) {
            // $("#subdepartment-div").css("display", "block");
            // $("#departmentcategory-div").css("display", "none");
            $("#subdepartment-div").html(response);
            $("#department_category").html('<option>Select Category</option>');
        },
        error: function () {
            alert("Failed to fetch subdepartment details.");
        }
    });
});

$(document).on("change", " #sub-departments", function () {
    var sub_department_id = $(this).val();
    $.ajax({
        url: 'fetch_subdetails_employer.php',
        method: 'POST',
        data: { sub_department_id: sub_department_id },
        success: function (response) {
            $("#departmentcategory-div").css("display", "block");
            $("#departmentcategory-div").html(response);
        },
        error: function () {
            alert("Failed to fetch department category details.");
        }
    });
});

  $(document).on("change", "#products", function () {
    var product_id = $(this).val();
    $.ajax({
        url: 'fetch_subdetails_employer.php',
        method: 'POST',
        data: { product_id: product_id },
        success: function (response) {
            // $("#subproduct-div").css("display", "block");
            // $("#productspecialization-div").css("display", "none");
            $("#subproduct-div").html(response);
            $("#product_specialization").html('<option>Select Specialization</option>');
        },
        error: function () {
            alert("Failed to fetch subproduct details.");
        }
    });
});

$(document).on("change", " #sub-products", function () {
    var sub_product_id = $(this).val();
    $.ajax({
        url: 'fetch_subdetails_employer.php',
        method: 'POST',
        data: { sub_product_id: sub_product_id },
        success: function (response) {
            $("#productspecialization-div").css("display", "block");
            $("#productspecialization-div").html(response);
        },
        error: function () {
            alert("Failed to fetch product specialization details.");
        }
    });
});



//------------ Fetch searched location and Filter useing location -----------------
let debounceTimer;

$(document).on("input", '#search_location', function () {
    clearTimeout(debounceTimer); 

    const query = $(this).val();

    if (query.length >= 1) {
        $('#select-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();

        debounceTimer = setTimeout(function () {
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
        }, 500);
    } else {
        clearTimeout(debounceTimer);
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

    // const validator = $("#multiStepForm").validate();
    // validator.element("#search_location");

});
$('#search_location').on('input', function () {
    $('#location_id').val('');
    locationSelected = false; 
});
$('#search_location').on('blur', function () {
        if (!locationSelected) {
            $('#search_location').val('');
            $('#location_id').val('');        }
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

        //------------ End Fetch searched location and Filter useing location -----------------
        
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('close-icon').addEventListener('click', function() {
    window.location.href = 'employer_joblisting_page.php';
  });
});

</script>
</body>
</html>
