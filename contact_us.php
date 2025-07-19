<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Finploy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Custom styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
        }
        label{
            color: #4EA647;
            font-family: Poppins;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }
        .contact-container {
            max-width: 1200px;
            margin: 20px auto;
            border-radius: 10px;
            padding: 30px;
        }
        .contact-us-title {
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .contact-icon-section {
            position: relative;
            padding: 20px;
        }
        .central-icon {
            width: 150px;
            height: 150px;
            background-color: #0d1e40;
            border-radius: 50%;
            position: relative;
            margin: 0 auto;
        }
        .icon-circle {
            background: #ffffff;
            border: 2px solid #e1e1e1;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            color: #0056b3;
        }
        .icon-circle.phone {
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
        }
        .icon-circle.bell {
            top: 10px;
            right: -15px;
        }
        .icon-circle.wifi {
            top: 10px;
            left: -15px;
        }
        .icon-circle.like {
            bottom: 10px;
            left: -15px;
        }
        .icon-circle.check {
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
        }
        .icon-circle.chat {
            bottom: 10px;
            right: -15px;
        }
        .icon-circle.email {
            right: -25px;
            bottom: 50%;
            transform: translateY(50%);
        }
        .icon-circle.cloud {
            left: -25px;
            bottom: 50%;
            transform: translateY(50%);
        }
        .contact-text {
            margin-top: 40px;
        }
        .contact-text h3 {
            color: #0056b3;
            font-weight: 600;
        }
        .contact-text span.green-text {
            color: #4CAF50;
        }
        .divider {
            height: 1px;
            background-color: #dee2e6;
            margin: 20px 0;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .feature-item i {
            color: #4CAF50;
            margin-right: 10px;
            margin-top: 3px;
        }
        .address-section {
            margin-top: 20px;
        }
        .address-title {
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .address-content {
            margin-left: 25px;
        }
        .address-content a {
            color: #0056b3;
            text-decoration: none;
        }
        .address-content p {
            color: #6c757d;
            margin-bottom: 5px;
        }
        .form-container {
            border: 2px solid #4CAF50 !important;
            border-radius: 10px;
            padding: 25px;
        }
        .form-title {
            color: #175DA8;
            font-family: Poppins;
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-decoration: underline;
        }
        .form-label {
            color: #4CAF50;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .form-control,
        .dropdown-toggle{
            border-radius: 8px;
            border: 0.373px solid #C6C6C6;
            background: #FFF;
            box-shadow: 0px 0px 7.467px 0px rgba(99, 99, 99, 0.20);
        }
        .dropdown-toggle {
            width: 100%;
            text-align: left;
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
            background-color: white;
            color: #6c757d;
        }
        .dropdown-toggle::after {
            float: right;
            margin-top: 8px;
        }
        .submit-btn {
            background-color: #4CAF50 !important;
            color: #fff !important;
            border: none;
            border-radius: 5px;
            padding: 12px;
            width: 100%;
            font-weight: 500;
            margin-top: 10px;
        }
        .submit-btn:hover {
            background-color: #3e8e41;
        }
        textarea.form-control {
            min-height: 120px;
            resize: none;
            font-size: 14px;
        }
        .required::after {
            content: "*";
            color: red;
        }
        .dropdown-toggle{
            font-size: 13px;
        }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .contact-container {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include'header.php'; ?>
    <div class="container contact-container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="contact-us-title">Contact Us</h2>
            </div>
        </div>
        
        <div class="row">
            <!-- Left Column - Contact Information -->
            <div class="col-lg-6">
                <div class="contact-icon-section">
                    <!-- Central circle with surrounding icons -->
                    <div class="position-relative">
                        <img src="assets/customer-support.svg" alt="Customer Support" >
                        
                        <!-- 24h badge -->
                        <div class="position-absolute" style="top: 40%; left: 50%; transform: translate(-50%, -50%); color: white; font-weight: bold;">
                            24h
                        </div>
                    </div>
                </div>
                
                <div class="contact-text">
                    <h3>Join over <span class="green-text">1 Lakhs+ employers</span>.</h3>
                    <h3>We're here to help!</h3>
                </div>
                
                <div class="divider"></div>
                
                <div class="features-section">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <div>Fill out this quick form and a dedicated talent executive will reach out soon.</div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <div>We'll collaborate with you to understand your business needs and explore how Finploy can assist.</div>
                    </div>
                </div>
                
                <div class="address-section">
                    <!--<div class="address-title">Address:</div>-->
                    <div class="address-content">
                        <a href="mailto:support@finploy.com">Email: support@finploy.com</a>
                        <!--<p># 55, Office no. 2547, Gokul , 1st Floor, Gandhinagar Opposite MIG Club Cricket Ground, Bandra East, Mumbai, Maharashtra 400051</p>-->
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Contact Form -->
            <div class="col-lg-6">
                <div class="form-container">
                    <h3 class="form-title">Get in touch with us !</h3>
                    
                    <form id="contactForm" method="POST" action="process_contact.php">
                        <div class="mb-3">
                            <label for="fullName" class="form-label required">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter Full Name" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="workEmail" class="form-label required">Work Email</label>
                                    <input type="email" class="form-control" id="workEmail" name="workEmail" placeholder="finploy@support.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mobileNo" class="form-label required">Mobile No</label>
                                    <input type="tel" class="form-control" id="mobileNo" name="mobileNo" placeholder="Enter 10 Digit Number" pattern="[0-9]{10}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="inquiryType" class="form-label required">Inquiry Type</label>
                                    <div class="dropdown">
                                        <button class="dropdown-toggle" type="button" id="inquiryTypeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            Select Inquiry
                                        </button>
                                        <ul class="dropdown-menu w-100" aria-labelledby="inquiryTypeDropdown">
                                            <li><a class="dropdown-item" href="#" data-value="General">General Inquiry</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Sales">Sales</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Support">Support</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Partnership">Partnership</a></li>
                                        </ul>
                                        <input type="hidden" name="inquiryType" id="inquiryType" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="companyName" class="form-label">Company Name</label>
                                    <div class="dropdown">
                                        <button class="dropdown-toggle" type="button" id="companyNameDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            Select Company
                                        </button>
                                        <ul class="dropdown-menu w-100" aria-labelledby="companyNameDropdown">
                                            <li><a class="dropdown-item" href="#" data-value="Company1">Company 1</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Company2">Company 2</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Company3">Company 3</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="Other">Other</a></li>
                                        </ul>
                                        <input type="hidden" name="companyName" id="companyName">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comments" class="form-label">Comments</label>
                            <textarea class="form-control" id="comments" name="comments" placeholder="Type here..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn submit-btn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- PHP for form processing (saved as process_contact.php) -->
    <!-- 
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $fullName = $_POST['fullName'] ?? '';
        $workEmail = $_POST['workEmail'] ?? '';
        $mobileNo = $_POST['mobileNo'] ?? '';
        $inquiryType = $_POST['inquiryType'] ?? '';
        $companyName = $_POST['companyName'] ?? '';
        $comments = $_POST['comments'] ?? '';
        
        // Validate data
        $errors = [];
        
        if (empty($fullName)) {
            $errors[] = "Full name is required";
        }
        
        if (empty($workEmail) || !filter_var($workEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid work email is required";
        }
        
        if (empty($mobileNo) || !preg_match("/^[0-9]{10}$/", $mobileNo)) {
            $errors[] = "Valid 10-digit mobile number is required";
        }
        
        if (empty($inquiryType)) {
            $errors[] = "Inquiry type is required";
        }
        
        // If no errors, process the form
        if (empty($errors)) {
            // Database connection
            $servername = "localhost";
            $username = "username";
            $password = "password";
            $dbname = "contact_form";
            
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $conn->prepare("INSERT INTO contact_inquiries (full_name, work_email, mobile_no, inquiry_type, company_name, comments, created_at) 
                                        VALUES (:fullName, :workEmail, :mobileNo, :inquiryType, :companyName, :comments, NOW())");
                
                $stmt->bindParam(':fullName', $fullName);
                $stmt->bindParam(':workEmail', $workEmail);
                $stmt->bindParam(':mobileNo', $mobileNo);
                $stmt->bindParam(':inquiryType', $inquiryType);
                $stmt->bindParam(':companyName', $companyName);
                $stmt->bindParam(':comments', $comments);
                
                $stmt->execute();
                
                // Send email notification
                $to = "support@finploy.com";
                $subject = "New Contact Form Submission: $inquiryType";
                $message = "Name: $fullName\n";
                $message .= "Email: $workEmail\n";
                $message .= "Mobile: $mobileNo\n";
                $message .= "Inquiry Type: $inquiryType\n";
                $message .= "Company Name: $companyName\n";
                $message .= "Comments: $comments\n";
                
                $headers = "From: noreply@finploy.com";
                
                mail($to, $subject, $message, $headers);
                
                // Redirect with success message
                header("Location: contact.php?status=success");
                exit();
            } catch(PDOException $e) {
                // Log error and redirect with error message
                error_log("Database Error: " . $e->getMessage());
                header("Location: contact.php?status=error&message=database");
                exit();
            }
        } else {
            // Redirect with validation errors
            $errorString = implode(",", $errors);
            header("Location: contact.php?status=error&message=$errorString");
            exit();
        }
    }
    ?>
    -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle dropdown selections
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const value = this.getAttribute('data-value');
                    const parentDropdown = this.closest('.dropdown');
                    const toggleButton = parentDropdown.querySelector('.dropdown-toggle');
                    const hiddenInput = parentDropdown.querySelector('input[type="hidden"]');
                    
                    toggleButton.textContent = this.textContent;
                    hiddenInput.value = value;
                });
            });
            
            // Form validation
            const contactForm = document.getElementById('contactForm');
            contactForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Basic validation
                const requiredFields = contactForm.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value) {
                        isValid = false;
                        const formGroup = field.closest('.mb-3');
                        if (!formGroup.querySelector('.invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.style.display = 'block';
                            feedback.textContent = 'This field is required';
                            formGroup.appendChild(feedback);
                        }
                    }
                });
                
                // Email validation
                const emailField = document.getElementById('workEmail');
                if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
                    isValid = false;
                    const formGroup = emailField.closest('.mb-3');
                    if (!formGroup.querySelector('.invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.style.display = 'block';
                        feedback.textContent = 'Please enter a valid email address';
                        formGroup.appendChild(feedback);
                    }
                }
                
                // Phone validation
                const phoneField = document.getElementById('mobileNo');
                if (phoneField.value && !/^[0-9]{10}$/.test(phoneField.value)) {
                    isValid = false;
                    const formGroup = phoneField.closest('.mb-3');
                    if (!formGroup.querySelector('.invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.style.display = 'block';
                        feedback.textContent = 'Please enter a valid 10-digit phone number';
                        formGroup.appendChild(feedback);
                    }
                }
                if (!isValid) {
                    e.preventDefault();
                }
            });
           
        });

        document.getElementById('mobileNo').addEventListener('keyup', function () {
            validatePhone(this);
        });
        function validatePhone(input) {
        let cleaned = input.value.replace(/\D/g, '');
        cleaned = cleaned.slice(0, 10);
        input.value = cleaned;
        const regex = /^[6-9]\d{9}$/;
    }

    </script>
</body>
<footer><?php include'footer.php'; ?></footer>
</html>