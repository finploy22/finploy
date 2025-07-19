<!DOCTYPE html>
<html lang="en">
<!-- Head section remains the same -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    #nameField {
        display: none;
    }
</style>
<body>
    <div id="loginModal" class="modal">
        <div class="modal-content m-2">
            <span class="close" id="closeModalBtn">&times;</span>
            <form class="" id="loginForm">
                <!-- Existing header content remains the same -->
                <div class="mb-3 justify-center text-center">
                    <img class="login-logo" src="assets/finploy-logo.png" alt="FINPLOY" height="40">
                </div>
                <div class="mb-4" id="login-helptext">
                    <h6 class="text-success fw-bold text-center">LOGIN / REGISTER</h6>
                </div>

                <div class="form-step active" id="step-1">
                    <div class="mb-5">
                        <label for="mobile" class="form-label text-success">Mobile No:</label>
                        <input type="tel" class="form-control login-inputfield" maxlength="10" id="mobile" placeholder="Enter your Mobile Number" required>
                    </div>
                    <div class="mb-5" id="nameField">
                        <label for="name" class="form-label text-success">Name:</label>
                        <input type="name" class="form-control login-inputfield" id="name" placeholder="Enter your Full Name" required>
                    </div>
                    <div class="mb-5">
                        <button type="button" class="btn btn-success w-100" id="step1Continue">Continue</button>
                    </div>
                </div>

                <!-- Step 2: Choose OTP or Password -->
                <div class="form-step me-3" id="step-2">
                    <div class="m-5">
                        <label class="mb-2 form-label text-success">Login With</label>
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="form-check me-5">
                                <input class="form-check-input" type="radio" name="method" id="otp" value="otp" checked>
                                <label class="form-check-label text-success" for="otp">OTP</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="method" id="password" value="password">
                                <label class="form-check-label text-success" for="password">Password</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-5">
                        <!-- <button type="button" class="btn btn-secondary" id="step2Back">Back</button> -->
                        <button type="button" class="btn btn-success w-100" id="step2Continue">Continue</button>
                    </div>
                </div>

                <!-- Step 3: OTP or Password Input -->
                <div class="form-step" id="step-3">
                    <div class="mb-5 text-center" id="otp-input">
                        <label for="otpField" class="form-label text-success enter-otp">Enter OTP</label>
                        <p class="otp-help-text">We have send OTP on: <span class="text-success">+91 1234567890</span></p>
                        <input type="text" class="form-control" id="otpField" placeholder="Enter OTP" required><br>
                        <p class="resend-help-text mt-2">Didn’t get the OTP ? <span class="text-success text-underlined">Resend OTP</span></p>
                    </div>
                    <div class="mb-5" id="password-input" style="display: none;">
                        <label for="passwordField" class="form-label text-success">Enter Password:</label>
                        <input type="password" class="form-control" id="passwordField" placeholder="Enter Password" required>
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <!-- <button type="button" class="btn btn-secondary" id="step3Back">Back</button> -->
                        <button type="button" class="btn btn-success w-100" id="step3Continue">Submit</button>
                    </div>
                </div>
                <!-- Adding Steps 4-8 -->

                <!-- Step 4: Personal Information -->
                <div class="form-step" id="step-4">
                    <div class="mb-4">
                        <h6 class="text-success text-center fw-bold">Enter Additional Details</h6>
                        <div class="mb-4">
                            <label for="mobile" class="form-label text-success">Mobile No:</label>
                            <input type="tel" class="form-control login-inputfield" maxlength="10" id="candidate-mobile" placeholder="Enter your Mobile Number" required>
                        </div>
                        <div class="mb-4">
                            <label for="name" class="form-label text-success">Candidate Name:</label>
                            <input type="name" class="form-control login-inputfield" id="candidate-name" placeholder="Enter your Full Name" required>
                        </div>
                        <div class="mb-4">
                            <label class="mb-2 form-label text-success">Gender:</label>
                            <div class="d-flex flex-wrap">
                                <div class="form-check me-5">
                                    <input class="form-check-input" type="radio" name="method" id="male" value="male" checked>
                                    <label class="form-check-label text-success" for="male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="method" id="female" value="female">
                                    <label class="form-check-label text-success" for="female">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="age" class="form-label text-success">Age:</label>
                            <input type="text" class="form-control login-inputfield" id="age" placeholder="Enter your Age" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <button type="button" class="btn btn-success w-100" id="step4Continue">Continue</button>
                    </div>
                </div>

                <!-- Step 5: Professional Details -->
                <div class="form-step" id="step-5">
                    <div class="mb-4">
                        <h6 class="mb-4 text-success text-center fw-bold">Enter Additional Details</h6>
                        <div class="mb-4">
                            <label class="mb-2 form-label text-success">Are you Currently Employed:</label>
                            <div class="d-flex flex-wrap">
                                <div class="form-check me-5">
                                    <input class="form-check-input" type="radio" name="method" id="employed" value="employed" checked>
                                    <label class="form-check-label text-success" for="employed">Yes</label>
                                </div>
                                <div class="form-check me-5">
                                    <input class="form-check-input" type="radio" name="method" id="not-employed" value="not-employed">
                                    <label class="form-check-label text-success" for="not-employed">No</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="method" id="others-employed" value="others-employed">
                                    <label class="form-check-label text-success" for="others-employed">Others</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="current_company" class="form-label text-success">Which Company are you currently working in ?:</label>
                            <input type="text" class="form-control login-inputfield" id="current_company" placeholder="Enter your Current Company Name">
                        </div>
                        <div class="mb-4">
                            <label for="designation" class="form-label text-success">Designation & Product/Department:</label>
                            <input type="text" class="form-control login-inputfield" id="designation" placeholder="Enter your Designation">
                        </div>
                        <div class="mb-4">
                            <label class="mb-2 form-label text-success">Do have any past experience working in Bank / NBFC ?:</label>
                            <div class="d-flex flex-wrap">
                                <div class="form-check me-5">
                                    <input class="form-check-input" type="radio" name="method" id="experience-bank" value="Yes" checked>
                                    <label class="form-check-label text-success" for="male">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="method" id="fresher-bank" value="No">
                                    <label class="form-check-label text-success" for="no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <button type="button" class="btn btn-success w-100" id="step5Continue">Continue</button>
                    </div>
                </div>

                <!-- Step 6: Educational Background -->
                <div class="form-step" id="step-6">
                    <div class="mb-4">
                        <h6 class="mb-4 text-success text-center fw-bold">Enter Additional Details</h6>
                        
                        <div class="form-group mb-4">
                            <label class="form-label text-success">Checkmark the Banking Products in which you work Experience ?:</label>
                            <div class="container">
                                <div class="row">
                                    <!-- First Column -->
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="hl/lap" id="hlLap">
                                            <label class="form-check-label text-secondary" for="hlLap">HL/LAP</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="personal_loan" id="personalLoan">
                                            <label class="form-check-label text-secondary" for="personalLoan">Personal Loan</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="business_loan" id="businessLoan">
                                            <label class="form-check-label text-secondary" for="businessLoan">Business Loan</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="education_loan" id="educationLoan">
                                            <label class="form-check-label text-secondary" for="educationLoan">Education Loan</label>
                                        </div>
                                    </div>

                                    <!-- Second Column -->
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="gold_loan" id="goldLoan">
                                            <label class="form-check-label text-secondary" for="goldLoan">Gold Loan</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="credit_cards" id="creditCards">
                                            <label class="form-check-label text-secondary" for="creditCards">Credit Cards</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="casa" id="casa" >
                                            <label class="form-check-label text-secondary" for="casa">CASA</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products[]" value="others" id="others" >
                                            <label class="form-check-label text-secondary" for="others">Others</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold text-success">Checkmark the department in which you have work experience:</label>
                            <div class="container">
                                <div class="row">
                                    <!-- First Column -->   
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products1[]" value="Sales" id="sales" >
                                            <label class="form-check-label text-secondary" for="sales">Sales</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products1[]" value="Credit_dept" id="creditDept">
                                            <label class="form-check-label text-secondary" for="creditDept">Credit Dept</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products1[]" value="HR/Training" id="hrTraining">
                                            <label class="form-check-label text-secondary" for="hrTraining">HR / Training</label>
                                        </div>
                                    </div>

                                    <!-- Second Column -->
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products1[]" value="Legal/compliance/Risk" id="legalComplianceRisk">
                                            <label class="form-check-label text-secondary" for="legalComplianceRisk">Legal / Compliance / Risk</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products1[]" value="Operations" id="operations">
                                            <label class="form-check-label text-secondary" for="operations">Operations</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input border border-primary" type="checkbox" name="banking_products1[]" value="Others1" id="others1">
                                            <label class="form-check-label text-secondary" for="others1">Others</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <button type="button" class="btn btn-success w-100" id="step6Continue">Continue</button>
                    </div>
                </div>

                <!-- Step 7: Skills & Preferences -->
                <div class="form-step" id="step-7">
                    <div class="mb-4">
                        <h6 class="mb-4 text-success text-center fw-bold">Enter Additional Details</h6>
                        <div class="mb-3">
                            <label for="skills" class="form-label text-success">Total Work Experience in Years ?:</label>
                            <input type="text" class="form-control login-inputfield" id="work-experience" placeholder="Enter your Work Experience Years (Eg.1,2)">
                        </div>
                        <div class="mb-3">
                            <label for="skills" class="form-label text-success">Current Location / Preferred Job Location ?:</label>
                            <input type="text" class="form-control login-inputfield" id="current-location" placeholder="Enter your Current Location">
                        </div>
                        <div class="mb-3">
                            <label for="skills" class="form-label text-success">Yearly Current Salary - CTC (Rs lakhs) ?:</label>
                            <input type="text" class="form-control login-inputfield" id="current-salary" placeholder="Enter your Current Salary">
                        </div>
                        <div class="mb-3">
                            <label for="resume_upload" class="form-label text-success">Upload Resume:</label>
                            <div class="form-control d-flex align-items-center m-2">
                                <label for="resume_upload" class="btn btn-success me-2">Browse</label>
                                <input type="file" class="d-none" id="resume_upload" name="resume" accept=".pdf,.doc,.docx" required>
                                <span id="file-name" class="text-muted">No file chosen</span>
                                <span id="file-name" class="text-primary">/ upload .pdf or .docx</span>
                            </div>
                            <small class="form-text text-muted fst-italic">Note : Max file size - 5 MB</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <button type="button" class="btn btn-success w-100" id="step7Continue">Continue</button>
                    </div>
                </div>

                <!-- Step 8: Final Verification -->
                <div class="form-step" id="step-8">
                    <div class="mb-4">
                        
                        <div class="mb-3">
                            <div class="form-check text-center">
                            <img class="congrats-logo" src="assets/party.svg" alt="Congratulations">
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="mb-3 text-warning text-center fw-bold">✨ Congratulations ✨</h6>
                            <p class="mb-4 text-success text-center fw-bold">Your Profile is Successfully Created</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <button type="button" class="btn btn-success w-100" id="finalSubmit">Continue</button>
                    </div>
                </div>

                <!-- Disclaimer remains the same -->
                <div class="mb-3">
                    <p class="desclaimer-text">By Continuing, you agree to Finploy's <span class="text-success">Terms of Service</span> and <span class="text-success">Privacy Policy</span></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('resume_upload').addEventListener('change', function () {
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        document.getElementById('file-name').textContent = fileName;
    });    
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
        $('#mobile').on('input', function () {
            const mobile = $(this).val();
            // Check if mobile number is exactly 10 digits
            if (mobile.length === 10) {
                $.ajax({
                    url: 'check_mobile.php',
                    type: 'POST',
                    data: { mobile: mobile },
                    success: function (response) {
                        if (response === 'exists') {
                            $('#nameField').hide(); // Hide name field if mobile exists
                        } else {
                            $('#nameField').show(); // Show name field if mobile does not exist
                        }
                    }
                });
            } else {
                $('#nameField').hide(); // Hide name field if mobile number is not 10 digits
            }
        });
    });

    
    let currentStep = 1;
    const mobileInput = document.getElementById('mobile');
    const step1Continue = document.getElementById('step1Continue');
    const step2Continue = document.getElementById('step2Continue');
    const step3Continue = document.getElementById('step3Continue');
    const step4Continue = document.getElementById('step4Continue');
    const step5Continue = document.getElementById('step5Continue');
    const step6Continue = document.getElementById('step6Continue');
    const step7Continue = document.getElementById('step7Continue');
    // const step8Continue = document.getElementById('step8Continue');
    const submitBtn = document.getElementById('submitBtn');
    const otpRadio = document.getElementById('otp');
    const passwordRadio = document.getElementById('password');

    const modal = document.getElementById("loginModal");
    const closeModalBtn = document.getElementById("closeModalBtn");

    // Close the modal
    closeModalBtn.onclick = function () {
        modal.style.display = "none";
    };

    // Enable/disable continue button based on mobile input
    mobileInput.addEventListener('input', function () {
        step1Continue.disabled = !this.value.trim();
    });

    // Initialize first step button state
    step1Continue.disabled = !mobileInput.value.trim();

    // Step navigation
    step1Continue.addEventListener('click', function () {
        if (mobileInput.value.trim()) {
            showStep(2);
        }
    });

    step2Continue.addEventListener('click', function () {
        showStep(3);
        updateStep3Fields();
    });

    step3Continue.addEventListener('click', function () {
        showStep(4);
    });

    step4Continue.addEventListener('click', function () {
        showStep(5);
    });

    step5Continue.addEventListener('click', function () {
        showStep(6);
    });

    step6Continue.addEventListener('click', function () {
        showStep(7);
    });

    step7Continue.addEventListener('click', function () {
        showStep(8);
    });

    // step8Continue.addEventListener('click', function () {
    //     alert("You have reached the final step!");
    // });

    submitBtn.addEventListener('click', function (e) {
        e.preventDefault();
        submitForm();
    });

    function showStep(step) {
        currentStep = step;
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.remove('active');
            el.style.display = 'none'; // Hide all steps
        });
        const activeStep = document.getElementById(`step-${step}`);
        if (activeStep) {
            activeStep.classList.add('active');
            activeStep.style.display = 'block'; // Show the current step
        }

        // Hide #login-helptext if step is 4 or beyond
        const loginHelpText = document.getElementById('login-helptext');
        if (step >= 4) {
            if (loginHelpText) {
                loginHelpText.style.display = 'none';
            }
        } else {
            if (loginHelpText) {
                loginHelpText.style.display = 'block';
            }
        }

    }

    function updateStep3Fields() {
        const otpInput = document.getElementById('otp-input');
        const passwordInput = document.getElementById('password-input');

        if (otpRadio.checked) {
            otpInput.style.display = 'block';
            passwordInput.style.display = 'none';
        } else {
            otpInput.style.display = 'none';
            passwordInput.style.display = 'block';
        }
    }

    function submitForm() {
        const mobileNumber = mobileInput.value;
        const inputValue = otpRadio.checked ?
            document.getElementById('otpField').value :
            document.getElementById('passwordField').value;

        const nameField = document.getElementById('name');
        const userName = nameField ? nameField.value : ''; // Get the name if visible

        const passwordType = otpRadio.checked ? 'otp' : inputValue;

        if (mobileNumber && inputValue) {
            $.ajax({
                url: 'insert_user.php', // Server-side script to handle insertion
                type: 'POST',
                data: {
                    logintype: 'candidate',
                    name: userName,
                    mobile: mobileNumber,
                    password: passwordType,
                    created: new Date().toISOString(),
                    updated: new Date().toISOString()
                },
                success: function (response) {
                    if (response === 'success') {
                        alert("Form Submitted Successfully!");
                        modal.style.display = "none"; // Close modal after submission
                    } else {
                        console.log(response);
                        alert("Error submitting the form. Please try again.");
                    }
                },
                error: function () {
                    alert("An error occurred while processing your request.");
                }
            });
        } else {
            alert("Please fill out all required fields.");
        }
    }

    

    // Initialize the first step
    showStep(currentStep);
});


    </script>
</body>
</html>