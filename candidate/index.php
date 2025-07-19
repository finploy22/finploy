<?php
session_start();

if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
    $mobile = $_SESSION['mobile'];
} else {
    header("Location: ../index.php");
    die();
}

include '../db/connection.php';

$sql = "SELECT 
            id, user_id, username, mobile_number, gender,
            employed, current_company, sales_experience, destination, 
            work_experience, current_location, current_salary, resume, 
            products, sub_products, departments, sub_departments, 
            specialization, category, location_code, age
        FROM candidate_details 
        WHERE mobile_number = '$mobile'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $required_fields = [
        'username', 'mobile_number', 'gender', 'age',
        'employed', 'current_company', 'sales_experience', 'destination',
        'products', 'sub_products', 
        'departments', 'sub_departments',
        'work_experience', 'current_salary',  'location_code'
    ];

    $all_data_isthere = true;
    foreach ($required_fields as $field) {
        if (empty($row[$field]) || $row['location_code']==0 ) {
            $all_data_isthere = false;
            break;
        }
    }

    if ($all_data_isthere) {
        // Uncomment this to debug
        // echo "Redirecting to landing page..."; exit();
        header("Location: landingpage.php");
        exit();
    }
}











// Fetch product, sub prdouct, department, sub department 
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "products failed.";
}

$sql = "SELECT * FROM departments";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row_departments = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "departments failed.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-step Form</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style error labels shown by jQuery Validate */
        label.error {
            color: #dc3545;
            /* Bootstrap danger red */
            font-size: 0.875rem;
            /* Smaller than normal text */
            margin-top: 0.25rem;
            display: block;
            text-align: left;
        }

        /* Highlight inputs with errors */
        input.error,
        select.error,
        textarea.error {
            border-color: #dc3545 !important;

        }

        .error-container {
            /* min-height: 20px; */
        }
    </style>

    <style>
        .form-step {
            display: none;
        }

        .finploy-contact {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .progress {
            border-radius: 20px !important;
            background: #FFF !important;
            box-shadow: 0px 1px 6px 0px rgba(0, 0, 0, 0.16);
            height: 20px;
        }

        .progress-bar-20 {
            width: 30% !important;
            border-radius: 20px;
            background: #4EA647 !important;
        }

        .progress-bar-40 {
            width: 50% !important;
            border-radius: 40px;
            background: #4EA647 !important;
        }

        .progress-bar-60 {
            width: 60% !important;
            border-radius: 20px;
            background: #4EA647 !important;
        }

        .progress-bar-80 {
            width: 80% !important;
            border-radius: 20px;
            background: #4EA647 !important;
        }

        .step-indicator {
            margin-bottom: 23px;
        }

        .progress-head {
            color: var(--Deeesha-Green, #4EA647);
            font-family: Poppins;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: 21.877px;
            letter-spacing: 0.684px;
            margin-bottom: 0;
        }

        .login-inputfield {
            border-radius: 8px !important;
            border: 0.350px solid #C6C6C6 !important;
            background: #FFF !important;
            box-shadow: 0px 0px 8px 0px rgba(99, 99, 99, 0.08) !important;
            height: 48px !important;
        }

        .form-container {
            width: 100%;
            max-width: 380px !important;
            margin-left: auto !important;
            margin-right: 10px !important;
            padding: 30px !important;
            background: #fefefe;
            border-radius: 8px;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            border: none;
        }

        .step-title {
            margin-bottom: 25px;
            color: #4EA647;
            font-weight: bold;
        }

        .nav-btn {
            margin-top: 20px;
        }

        .form-label {
            float: left;
            text-align: left;
        }

        .content {
            margin-top: 20%;
            text-align: center;
            padding: 30px;
        }

        .content p {
            color: #232323;
            text-align: center;
            font-family: 'Poppins';
            font-size: 22px;
            font-style: normal;
            font-weight: 500;
            line-height: 48px;
            /* 218.182% */
            text-align: left;
            padding: 0 20px;
        }

        .content h1 {
            color: var(--Deeesha-Blue, #175DA8);
            font-family: 'Poppins';
            font-size: 60px;
            font-style: normal;
            font-weight: 600;
            line-height: 70.461px;
            /* 162.435% */
            text-align: left;
            padding: 20px;
        }

        .form-check-label {
            float: left;
        }

        @media (max-width: 768px) {
            .content {
                display: none;
            }

            .form-container {
                margin-right: 0px !important;
                padding: 30px !important;
            }

            .hero-section {
                background: none;
                padding: 0;
                margin: 0;
            }

            .container {
                padding: 0 !important;
                margin: 0 !important;
            }

            .form-check-label {
                float: left;
            }

            .btn-outline-primary:hover {
                background-color: transparent !important;
                border-color: #007bff !important;
                /* Keep the border color */
                color: #007bff !important;
                /* Keep the text color */
            }
        }
    </style>
</head>

<body>
    <?php
    include '../header.php';
    ?>
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <div class="content">
                        <h6 class="text-success fw-bold text-start ps-4">INDIA'S #1 TRUSTED PLATFORM</h6>
                        <h1>Your Search for the Perfect Job Stops Here</h1>
                        <p>Discover 1 lakh+ Career Opportunities</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-container">
                        <div class="mb-3 justify-center text-center">
                            <img class="login-logo" src="../assets/finploy-logo.png" alt="FINPLOY" height="40">
                        </div>

                        <form id="multiStepForm">
                            <!-- Step 1: Personal Information -->
                            <div class="form-step active" id="step1">
                                <h4 class="step-title text-center">Enter Additional Details</h4>
                                <p class="progress-head">20% Completed</p>
                                <div class="step-indicator">
                                    <div class="progress">
                                        <div class="progress-bar-20" role="progressbar">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success">Mobile No:</label>
                                    <input type="tel" class="form-control login-inputfield" id="mobile" maxlength="10"
                                        name="mobile" value="<?php echo $mobile; ?>" required
                                        placeholder="Enter your Phone Number">
                                    <div class="error-container"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success">Candidate Name:</label>
                                    <input type="text" class="form-control login-inputfield" id="fullName"
                                        name="fullName" value="<?php echo $name; ?>" required
                                        placeholder="Enter your Name">
                                    <div class="error-container"></div>
                                </div>
                               <div class="mb-3">
    <div class="mb-2 text-start">
        <label class="text-success">Gender:</label>
    </div>
    <div class="d-flex gap-4">
        <div class="form-check">
            <input class="form-check-input border-primary border-box" type="radio"
                name="gender" value="male" <?= ($row['gender'] === 'male') ? 'checked' : '' ?> required>
            <label class="form-check-label">Male</label>
        </div>
        <div class="form-check">
            <input class="form-check-input border-primary" type="radio" 
                name="gender" value="female" <?= ($row['gender'] === 'female') ? 'checked' : '' ?>>
            <label class="form-check-label">Female</label>
        </div>
    </div>
    <div class="error-container"></div>
</div>

<div class="mb-4">
    <label class="form-label text-success">Age:</label>
    <input type="number" class="form-control login-inputfield" id="age" name="age"
        value="<?= htmlspecialchars($row['age']) ?>" required placeholder="Enter your Age">
    <div class="error-container"></div>
</div>


                                <div class="nav-btn mb-4">
                                    <button type="button" class="btn btn-success w-100" id="create-profile1"
                                        onclick="nextStep(1)">Continue</button>
                                </div>
                            </div>

                            <!-- Step 2: Professional Details -->
                            <div class="form-step" id="step2">

                                <h4 class="step-title text-center">Enter Additional Details</h4>
                                <p class="progress-head">40% Completed</p>
                                <div class="step-indicator">
                                    <div class="progress">
                                        <div class="progress-bar-40 " role="progressbar">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-2 text-start">
                                        <label class="text-success">Are you Currently Employed?</label>
                                    </div>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input border-primary" type="radio" name="employed"
                                                value="yes" <?= ($row['employed'] === 'yes') ? 'checked' : '' ?> required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input border-primary" type="radio" name="employed"
                                                value="no" <?= ($row['employed'] === 'no') ? 'checked' : '' ?>>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                    <div class="error-container"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success">Which Company are you currently working in
                                        ?</label>
                                    <input type="text" class="form-control login-inputfield" id="currentCompany"
                                        required placeholder="Enter your Current Company Name" value="<?= $row['current_company'] ?? '' ?>" name="currentCompany">
                                    <div class="error-container"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success">Designation & Product/Department</label>
                                    <input type="text" class="form-control login-inputfield" name="designation" value="<?= $row['destination'] ?? '' ?>"
                                        id="designation" required placeholder="Enter your Designation">
                                    <div class="error-container"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="mb-2 text-start">
                                        <label class="text-success">Do have any past experience working in Bank / NBFC
                                            ?</label>
                                    </div>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input border-primary" type="radio"
                                                name="bankExperience" value="yes" <?= ($row['sales_experience'] === 'yes') ? 'checked' : '' ?> required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input border-primary" type="radio"
                                                name="bankExperience" value="no" <?= ($row['sales_experience'] === 'no') ? 'checked' : '' ?>>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                    <div class="error-container"></div>
                                </div>
                                <div class="nav-btn mb-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-primary border-light w-100"
                                                onclick="prevStep(2)">
                                                < Back</button>
                                        </div>
                                        <div class="col-8">
                                            <button type="button" class="btn btn-success w-100" id="create-profile2"
                                                onclick="nextStep(2)">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Step 3: Banking Experience -->
                            <div class="form-step" id="step3">
                                <h4 class="step-title text-center">Banking Experience</h4>
                                <p class="progress-head">60% Completed</p>
                                <div class="step-indicator">
                                    <div class="progress">
                                        <div class="progress-bar-60" role="progressbar">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="mb-2 text-start">
                                        <label class="text-success">Checkmark the Banking Products in which you work
                                            Experience ?</label>
                                    </div>
                                    <div class="row">
                                    <?php
                                    // Convert comma-separated string to array
                                    $selected_products = isset($row['products']) ? explode(',', $row['products']) : [];
                                
                                    $total_products = count($row_products);
                                    $half = ceil($total_products / 2);
                                    $chunked_products = array_chunk($row_products, $half);
                                
                                    foreach ($chunked_products as $product_group) {
                                        echo '<div class="col-md-6">';
                                        foreach ($product_group as $product) {
                                            $product_id = $product['product_id'];
                                            $checked = in_array($product_id, $selected_products) ? 'checked' : '';
                                            ?>
                                            <div class="form-check mb-2" style="display: flex; gap: 6px;">
                                                <input class="form-check-input border-primary"
                                                    type="checkbox" name="products[]" value="<?= $product_id; ?>"
                                                    data-name="<?= $product['product_name']; ?>" style="flex-shrink: 0;" <?= $checked; ?>>
                                                <label class="form-check-label" style="text-align: left;"><?= $product['product_name']; ?></label>
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
                                </div>
                                <div class="mb-4" id="specialization_div" style="display: none;">
                                    <div class="mb-2 text-start">
                                        <label class="text-success">Specialization</label>
                                    </div>
                                    <div class="row" id="specialization_row">
                                    </div>
                                </div>
                                <div class="nav-btn mb-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-primary border-light w-100"
                                                onclick="prevStep(3)">
                                                < Back</button>
                                        </div>
                                        <div class="col-8">
                                            <button type="button" class="btn btn-success w-100" id="create-profile3"
                                                onclick="nextStep(3)">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-step" id="step4">
                                <h4 class="step-title text-center">Department Experience</h4>
                                <p class="progress-head">60% Completed</p>
                                <div class="step-indicator">
                                    <div class="progress">
                                        <div class="progress-bar-60" role="progressbar">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="mb-2 text-start">
                                        <label class="text-success">Checkmark the Department in which you have work
                                            Experience ?</label>
                                    </div>
                                    <div class="row">
                                        <?php
                                        // Convert comma-separated string into array
                                        $selected_departments = isset($row['departments']) ? explode(',', $row['departments']) : [];
                                    
                                        $total_departments = count($row_departments);
                                        $half = ceil($total_departments / 2);
                                        $chunked_departments = array_chunk($row_departments, $half);
                                    
                                        foreach ($chunked_departments as $department_group) {
                                            echo '<div class="col-md-6">';
                                            foreach ($department_group as $department) {
                                                $department_id = $department['department_id'];
                                                $checked = in_array($department_id, $selected_departments) ? 'checked' : '';
                                                ?>
                                                <div class="form-check mb-2" style="display: flex; gap: 6px;">
                                                    <input class="form-check-input border-primary" type="checkbox"
                                                        name="departments[]" value="<?= $department_id; ?>"
                                                        data-name="<?= $department['department_name']; ?>" style="flex-shrink: 0;" <?= $checked; ?>>
                                                    <label class="form-check-label" style="text-align: left;">
                                                        <?= $department['department_name']; ?>
                                                    </label>
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
                                </div>
                                <div class="mb-4" id="category_div" style="display: none;">
                                    <div class="mb-2 text-start">
                                        <label class="text-success">Category</label>
                                    </div>
                                    <div class="row" id="category_row">
                                    </div>
                                </div>
                                <div class="nav-btn mb-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-primary border-light w-100"
                                                onclick="prevStep(4)">
                                                < Back</button>
                                        </div>
                                        <div class="col-8">
                                            <button type="button" class="btn btn-success w-100" id="create-profile4"
                                                onclick="nextStep(4)">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Step 5: Additional Details -->
                            <div class="form-step" id="step5">
                                <h4 class="step-title text-center">Enter Additional Details</h4>
                                <p class="progress-head">80% Completed</p>
                                <div class="step-indicator">
                                    <div class="progress">
                                        <div class="progress-bar-80" role="progressbar">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success">Total Work Experience in Years ?</label>
                                    <input type="number" name="experience" class="form-control login-inputfield"
                                        id="experience" placeholder="Enter your Work Experience Years (Eg.1,2)"
                                        required  value="<?= $row['work_experience'] ?? '' ?>">
                                    <div class="error-container"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success">Current Location / Preferred Job Location
                                        ?</label>
                                    <input type="text" class="form-control login-inputfield" id="search_location"
                                        placeholder="Enter your Current Location" name="location"  required autocomplete="off">
                                    <div id="select-div" style="display: none;"></div>
                                    <div class="error-container"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-success">Yearly Current Salary - CTC (Rs lakhs)
                                        ?</label>
                                    <input type="number" name="salary" placeholder="Enter your Current Salary"
                                        class="form-control login-inputfield" id="salary" value="<?= $row['current_salary'] ?? '' ?>" required>
                                    <div class="error-container"></div>
                                </div>
                              <div class="mb-3">
    <label class="form-label text-success">Upload Resume:</label>
    <input type="file" name="resume" class="form-control login-inputfield" id="resume"
        accept=".pdf,.doc,.docx" style="height: 38px !important;">
    <small class="text-muted">Max file size: 5MB</small>

    <?php if (!empty($row['resume']) && file_exists($row['resume'])): ?>
        <div class="mt-2">
            <a href="<?= $row['resume']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                View Existing Resume
            </a>
        </div>
    <?php endif; ?>
</div>

                                <div class="nav-btn mb-4">
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-primary border-light w-100"
                                                onclick="prevStep(5)">
                                                < Back</button>
                                        </div>
                                        <div class="col-8">
                                            <button type="button" class="btn btn-success w-100" id="create-profile5"
                                                onclick="nextStep(5)">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 6: Confirmation -->
                            <div class="form-step" id="step6">
                                <div class="text-center mt-5 mb-4">
                                    <img src="assets/party.svg" alt="">
                                    <h4 class="step-title mt-3">✨ Congratulations ✨</h4>
                                    <p class="text-success mt-3">Your profile has been created successfully.</p>
                                </div>
                                <button type="submit" class="btn btn-success w-100 mt-5 mb-5">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    include '../footer.php';
    include '../candidate_login.php';
    ?>

    <script>
        let currentStep = 1;
        const totalSteps = 5;
        function showStep(step) {
            document.querySelectorAll('.form-step').forEach(s => {
                s.classList.remove('active');
            });
            document.getElementById(`step${step}`).classList.add('active');
        }

        function validateStep(step) {
            const currentStepElement = document.getElementById(`step${step}`);
            const requiredFields = currentStepElement.querySelectorAll('[required]');
            let valid = true;
            requiredFields.forEach(field => {
                if (!field.value) {
                    valid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (step === 3 || step === 4 || step === 5) {
                const isValid = $("#multiStepForm").valid();
                if (!isValid) return;
            }

            return valid;
        }

        function nextStep(step) {
            // This triggers jQuery Validate
            const isValid = $("#multiStepForm").valid();
            if (!isValid) {
                return;
            }
            if (validateStep(step)) {
                currentStep++;
                showStep(currentStep);
            }
        }

        function prevStep(step) {
            currentStep--;
            showStep(currentStep);
        }

        document.getElementById('multiStepForm').addEventListener('submit', function (e) {
            e.preventDefault();
            // Handle form submission here
            window.location.href = 'landingpage.php';
        });

        // Initialize form
        showStep(1);

        // For product, sub product mapping
     $(document).ready(function () {
    // Common AJAX handler
    function sendAjax(dataKey, dataValue, targetDivId, targetRowId, showDivIdToHide = null) {
        $.ajax({
            url: "fetch_subdetails.php",
            type: "POST",
            data: { [dataKey]: JSON.stringify(dataValue) },
            success: function (response) {
                if ($.trim(response)) {
                    document.getElementById(targetDivId).style.display = "block";
                    if (showDivIdToHide) {
                        document.getElementById(showDivIdToHide).style.display = "none";
                    }
                    $("#" + targetRowId).html(response);
                } else {
                    document.getElementById(targetDivId).style.display = "none";
                    $("#" + targetRowId).html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error: " + error);
                alert("Something went wrong! Please try again.");
            }
        });
    }

    // Generic checkbox change handler
    function handleCheckboxChange(nameAttr, dataKey, targetDivId, targetRowId, optionalHideDivId = null) {
        $(document).on("change", `input[name="${nameAttr}"]`, function () {
            const changedCheckbox = this;
            const dataToSend = [];

            $(`input[name="${nameAttr}"]`).each(function () {
                if (this.checked) {
                    dataToSend.push({ id: this.value, value: this.value });
                } else if (this === changedCheckbox) {
                    dataToSend.push({ id: this.value, value: 0 });
                }
            });

            sendAjax(dataKey, dataToSend, targetDivId, targetRowId, optionalHideDivId);
        });
    }

    // Initialize checkbox listeners
    handleCheckboxChange("products[]", "selectedProducts", "sub_products_div", "sub_products_row", "specialization_div");
    handleCheckboxChange("sub_products", "selectedSubProducts", "specialization_div", "specialization_row");
    handleCheckboxChange("departments[]", "selecteddepartments", "sub_departments_div", "sub_departments_row", "category_div");
    handleCheckboxChange("sub_departments", "selectedSubdepartments", "category_div", "category_row");

    // Trigger change manually for pre-checked checkboxes
    function triggerFirstChecked(nameAttr) {
        const $checked = $(`input[name="${nameAttr}"]:checked`);
        if ($checked.length) {
            $checked.first().trigger('change');
        }
    }

    // Chained initialization using timeouts
    triggerFirstChecked("products[]");
    setTimeout(() => triggerFirstChecked("sub_products"), 400);
    triggerFirstChecked("departments[]");
    setTimeout(() => triggerFirstChecked("sub_departments"), 400);
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
        
            const validator = $("#multiStepForm").validate();
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
                    $('#location_id').val('');        }
        });



    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
$(document).ready(function () {
    const form = $("#multiStepForm");

    // Common validator
    const validator = form.validate({
        rules: {
            fullName: { required: true },
            mobile_number: { required: true },
            gender: { required: true },
            age: { required: true },

            employed: { required: true },
            current_company: { required: true },
            designation: { required: true },
            bankExperience: { required: true },

            "products[]": { required: true, minlength: 1 },
            "sub_products": { required: true, minlength: 1 },

            "departments[]": { required: true, minlength: 1 },
            "sub_departments": { required: true, minlength: 1 },

            salary: { required: true },
            experience: { required: true },
            location: { required: true }
        },
        messages: {
            fullName: "Please enter your name",
            mobile_number: "Please enter your mobile number",
            gender: "Please select your gender",
            age: "Please enter your age",
            employed: "Please select your employment status",
            current_company: "Please enter your current company",
            designation: "Please enter your designation",
            bankExperience: "Please select your banking experience",
            "products[]": "Select at least one product",
            "sub_products": "Select at least one subproduct",
            "departments[]": "Select at least one department",
            "sub_departments": "Select at least one subdepartment",
            salary: "Please enter your current salary",
            experience: "Enter your work experience",
            location: "Select a valid location"
        },
        errorPlacement: function (error, element) {
            const name = element.attr("name");
            if (name === "products[]") error.appendTo("#products-error");
            else if (name === "departments[]") error.appendTo("#departments-error");
            else {
                const container = element.closest(".mb-3").find(".error-container");
                container.length ? container.html(error) : error.insertAfter(element);
            }
        }
    });

    // AJAX helper
    function submitFormData(formData) {
        $.ajax({
            url: "insert_details.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                $(".step:visible").hide().next(".step").show();
            },
            error: function (xhr, status, error) {
                console.error("Error: " + error);
                alert("Something went wrong!");
            }
        });
    }

    // Step 1
    $("#create-profile1").click(function (e) {
        e.preventDefault();
        if (!validator.element("#fullName") || !validator.element("#mobile") || !validator.element("input[name='gender']:checked") || !validator.element("#age")) return;

        const data = new FormData();
        data.append("step", "1");
        data.append("username", $("#fullName").val());
        data.append("mobile_number", $("#mobile").val());
        data.append("age", $("#age").val());
        data.append("gender", $("input[name='gender']:checked").val());
        submitFormData(data);
    });

    // Step 2
    $("#create-profile2").click(function (e) {
        e.preventDefault();
        if (!validator.element("input[name='employed']:checked") || !validator.element("#currentCompany") || !validator.element("#designation") || !validator.element("input[name='bankExperience']:checked")) return;

        const data = new FormData();
        data.append("step", "2");
        data.append("employed", $("input[name='employed']:checked").val());
        data.append("current_company", $("#currentCompany").val());
        data.append("designation", $("#designation").val());
        data.append("bankExperience", $("input[name='bankExperience']:checked").val());
         data.append("mobile_number", $("#mobile").val());
        submitFormData(data);
    });

    // Step 3
    $("#create-profile3").click(function (e) {
        e.preventDefault();
        if (!$("input[name='products[]']:checked").length || !$("input[name='sub_products']:checked").length) return;
    
        const data = new FormData();
        data.append("step", "3");
    
        // Values
        const products = $("input[name='products[]']:checked").map((_, el) => el.value).get();
        const subProducts = $("input[name='sub_products']:checked").map((_, el) => el.value).get();
        const specialization = $("input[name='specialization']:checked").map((_, el) => el.value).get();
    
        data.append("products", products.join(","));
        data.append("sub_products", subProducts.join(","));
        data.append("specialization", specialization.join(","));
        data.append("mobile_number", $("#mobile").val());
    
        // Arrays with data-name attributes
        const nameFields = [
            { name: 'products[]', fieldKey: 'products_array' },
            { name: 'sub_products', fieldKey: 'sub_products_array' },
            { name: 'specialization', fieldKey: 'specialization_array' },
        ];
    
        nameFields.forEach(field => {
            const arr = [];
            $("input[name='" + field.name + "']:checked").each(function () {
                arr.push($(this).data("name"));
            });
            data.append(field.fieldKey, arr.join(","));
        });
    
        submitFormData(data);
    });


    // Step 4
    $("#create-profile4").click(function (e) {
        e.preventDefault();
        if (!$("input[name='departments[]']:checked").length || !$("input[name='sub_departments']:checked").length) return;

        const data = new FormData();
        data.append("step", "4");
        const departments = $("input[name='departments[]']:checked").map((_, el) => el.value).get();
        const subDepartments = $("input[name='sub_departments']:checked").map((_, el) => el.value).get();
        const categories = $("input[name='category']:checked").map((_, el) => el.value).get();

        data.append("departments", departments.join(","));
        data.append("sub_departments", subDepartments.join(","));
        data.append("category", categories.join(","));
         data.append("mobile_number", $("#mobile").val());
         
         
         // Arrays with data-name attributes
        const nameFields = [
            { name: 'departments', fieldKey: 'departments_array' },
            { name: 'sub_departments', fieldKey: 'sub_departments_array' },
            { name: 'category', fieldKey: 'category_array' },
        ];
    
        nameFields.forEach(field => {
            const arr = [];
            $("input[name='" + field.name + "']:checked").each(function () {
                arr.push($(this).data("name"));
            });
            data.append(field.fieldKey, arr.join(","));
        });
         
        submitFormData(data);
    });

    // Step 5
    $("#create-profile5").click(function (e) {
        e.preventDefault();
        if (!validator.element("#experience") || !validator.element("#salary")) return;

        const data = new FormData();
        data.append("step", "5");
        data.append("work_experience", $("#experience").val());
        data.append("current_location", $('#search_location').data('location-id'));
        data.append("current_salary", $("#salary").val());
         data.append("mobile_number", $("#mobile").val());

        const resume = $("#resume")[0].files[0];
        if (resume) data.append("resume", resume);
        submitFormData(data);
    });

    // Validate on blur/change
    $("#multiStepForm input, #multiStepForm select").on("blur change", function () {
        $(this).valid();
    });
});
</script>

</body>

</html>