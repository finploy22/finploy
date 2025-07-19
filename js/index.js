

// Immediately-invoked function expression to isolate scope
(function ($) {
    // Global submission flag as a stronger prevention measure
    var globalIsSubmitting = false;

    // Store our event handlers in an object for easy removal
    var eventHandlers = {};

    // Function to safely bind events, ensuring they're only bound once
    function safeBindEvent(selector, event, handlerName, handler) {
        // First, unbind any existing handler
        $(document).off(event, selector);

        // Store the handler for future reference
        eventHandlers[handlerName] = handler;

        // Bind the new handler
        $(document).on(event, selector, handler);
    }

    // Initialize everything when document is ready
    $(document).ready(function () {
        initLoginButtons();
        initReferCandidateButton();
        handleModalBackdrop();
    });

    // Initialize login button handlers
    function initLoginButtons() {
        $('#candidate-login-btn').off('click').on('click', function () {
            loadLoginForm('candidate');
        });
        $('#candidate-login-btn-tab').off('click').on('click', function () {
            loadLoginForm('candidate');
        });

        $('#search-jobs-btn, #search-jobs-mobile').off('click').on('click', function () {
            loadLoginForm('candidate');
        });

        // $('#job-apply-btn').off('click').on('click', function(e) {
        //     e.preventDefault(); // Prevent default action if it's a link or form button
        //     console.log('job apply clicked');
        //     loadLoginForm('candidate');
        // });

        $('#employer-login-btn').off('click').on('click', function () {
            loadLoginForm('employer');
        });

        $('#partner-login-btn').off('click').on('click', function () {
            loadLoginForm('partner');
        });
        $('#employer-login-btn-tab').off('click').on('click', function () {
            loadLoginForm('employer');
        });

        $('#partner-login-btn-tab').off('click').on('click', function () {
            loadLoginForm('partner');
        });

        $('#candidate-login-mobile').off('click').on('click', function () {
            loadLoginForm('candidate');
        });

        $('#employer-login-mobile').off('click').on('click', function () {
            loadLoginForm('employer');
        });

        $('#partner-login-mobile').off('click').on('click', function () {
            loadLoginForm('partner');
        });
        $('#loadEmployer').on('click', function () {
            loadLoginForm('employer');
        });
        $('#loadPartner').on('click', function () {
            loadLoginForm('partner');
        });
        $('#loadCandidate').on('click', function () {
            loadLoginForm('candidate');
        });

    }

    // Apply Job Functionality //
    $(document).ready(function () {
        // console.log("Job application handler initialized");

        // Remove the existing click handler to avoid duplication

        $(document).off('click', '#apply-job-btn, #job-apply-btn,#share-job-btn');
        $(document).on('click',"#share-job-btn",function(){
            const baseUrl="http://localhost/";
            console.log("test");
            var jobCard = $(this).closest(".job-card");
            var jobId = jobCard.find(".job-grid").attr("data-id");
            console.log("jobId",jobId)
            // window.location.href=baseUrl+"index.php?job_id="+jobId;
        });

        // Add the new click handler with session check
        $(document).on('click', '#apply-job-btn, #job-apply-btn', function () {
            var jobCard = $(this).closest(".job-card");
            var jobId = jobCard.find(".job-grid").attr("data-id");
            console.log("jobId",jobId)

            // If job ID isn't found in the card, try getting it from a hidden field
            if (!jobId) {
                var jobWrapper = $(this).closest(".job-card-wrapper");
                jobId = jobWrapper.data("id");
            }

            if (!jobId) {
                alert("Job ID not found!");
                return;
            }

            // console.log("Job application clicked for job ID:", jobId);

            // Save the job ID to localStorage for retrieval after login
            localStorage.setItem('pendingJobApplication', jobId);

            // Check if user is logged in via AJAX
            $.ajax({
                url: 'check_login_status.php',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    // console.log("Login status check response:", response);
                    if (response.loggedIn) {
                        // User is logged in, apply for the job directly
                        // console.log("User is logged in, applying directly with mobile:", response.mobile);
                        applyForJob(jobId, response.mobile);
                    } else {
                        // User is not logged in, show login form
                        // console.log("User is not logged in, showing login form");
                        loadLoginForm('candidate');
                    }
                },
                error: function (xhr, status, error) {
                    // console.log("Login status check error:", error);
                    // console.log("Response text:", xhr.responseText);
                    alert('Failed to check login status. Please try again.');
                }
            });
           
        });


        // Listen for successful login events
        $(document).on('loginSuccess', function (e, userData) {
            // Check if there's a pending job application
            var pendingJobId = localStorage.getItem('pendingJobApplication');
            if (pendingJobId) {
                // Apply for the job using the stored ID
                applyForJob(pendingJobId, userData.mobile);
                // Clear the pending application
                localStorage.removeItem('pendingJobApplication');
            }
        });
    });


    // Function to apply for a job
    function applyForJob(jobId, mobileNumber) {
        if (!jobId || !mobileNumber) {
            // console.log("Missing data for job application:", { jobId: jobId, mobileNumber: mobileNumber });
            alert("Missing Job ID or Mobile Number!");
            return;
        }

        // console.log("Applying for job ID:", jobId, "with mobile:", mobileNumber);

        $.ajax({
            url: "candidate/apply_job.php",
            type: "POST",
            data: { job_id: jobId, mobile_number: mobileNumber },
            dataType: "json",
            beforeSend: function () {
                if (typeof showLoader === 'function') {
                    showLoader();
                } else {
                    console.log("showLoader function not found");
                }
            },
            success: function (response) {
                if (typeof hideLoader === 'function') {
                    hideLoader();
                } else {
                    console.log("hideLoader function not found");
                }

                // console.log("Job application response:", response);

                if (response.status === "success") {
                    alert("Successfully applied for the job!");

                    // After successful application, redirect to candidate dashboard
                    if (localStorage.getItem('pendingJobApplication')) {
                        localStorage.removeItem('pendingJobApplication');
                        window.location.href = "candidate/index.php";
                    }

                    // Refresh job list if the function exists
                    if (typeof fetchJobs === 'function') {
                        fetchJobs(1);
                    }
                } else {
                    alert("Failed to apply: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                if (typeof hideLoader === 'function') {
                    hideLoader();
                }

                // console.log("AJAX Error:", xhr.responseText);
                // console.log("Status:", status);
                // console.log("Error:", error);
                alert("Something went wrong! Please try again.");
            }
        });
    }

    // Refer Candidate Functionality
    $(document).ready(function () {
        // console.log("Refer candidate handler initialized");

        // Remove old handlers if any to avoid duplication
        $(document).off('click', '#candidate-as-partner, #candidate-to-partner');

        // Add click handler for refer-candidate button
        $(document).on("click", "#candidate-as-partner, #candidate-to-partner", function(e) {
            e.stopPropagation(); 
            e.stopImmediatePropagation(); //sakthi
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

    // Function to perform partner referral
    function referAsPartner(jobId, mobileNumber) {
        if (!jobId || !mobileNumber) {
            alert("Missing Job ID or Mobile Number!");
            return;
        }

        // console.log("Referring candidate to partner. Job ID:", jobId, "Mobile:", mobileNumber);

        $.ajax({
            url: "candidate/insert_partner.php",
            type: "POST",
            data: { job_id: jobId, mobile_number: mobileNumber },
            dataType: "json",
            beforeSend: function () {
                if (typeof showLoader === 'function') {
                    showLoader();
                }
            },
            success: function (response) {
                if (typeof hideLoader === 'function') {
                    hideLoader();
                }

                if (response.status === "success") {
                    alert("Successfully changed as a Partner!");
                    window.location.href = "/partner/refer_candidate.php?jobid=" + jobId;
                } else {
                    alert("Failed to refer: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                if (typeof hideLoader === 'function') {
                    hideLoader();
                }
                // console.log("AJAX Error:", xhr.responseText);
                alert("Something went wrong! Please try again.");
            }
        });
    }


    // For trigger employer login for plan 
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof planDetails !== 'undefined' && Object.keys(planDetails).length > 0) {
            loadLoginForm('employer');
        }
    });

    // Function to load the login form dynamically
    function loadLoginForm(type) {
        // Clear any existing form handlers before loading a new form
        clearFormHandlers();

        var url = type + '_login.php';

        $.ajax({
            url: url,
            type: 'GET',
            data: { type: type },
            success: function (response) {
                $('#modal-placeholder').html(response);

                // Add a hidden input field to store the login type
                if ($('#loginType').length === 0) {
                    $('#modal-placeholder form').append('<input type="hidden" id="loginType" name="loginType" value="' + type + '">');
                } else {
                    $('#loginType').val(type);
                }

                $('#loginModal').fadeIn();
                $('#loginModal').data('loginType', type);

                // Initialize form functionality after content is loaded
                initializeFormSteps(type);
            },
            error: function () {
                alert('Failed to load the login modal. Please try again.');
            }
        });
    }

    // Function to clear all form-related event handlers
    function clearFormHandlers() {
        $(document).off('input', '#mobile, #name');
        $(document).off('click', '#step1Continue');
        $(document).off('click', '#step2Continue');
        $(document).off('change', 'input[name="method"]');
        $(document).off('click', '#submitBtn');
        $(document).off('click', '#closeModalBtn');

        // Reset submission flag
        globalIsSubmitting = false;
    }

    // Function to initialize form steps and event listeners
    function initializeFormSteps(type) {
        // console.log('Initializing form steps for type:', type);

        // Input validation events - with safe binding
        safeBindEvent('#mobile, #name', 'input', 'validateFields', function () {
            validateField($(this));
            validateStep1Fields();
        });

        // Function to validate individual fields
        function validateField($field) {
            const fieldId = $field.attr('id');
            const value = $field.val().trim();
            let errorMessage = '';

            // Remove any existing error message
            $(`#${fieldId}-error`).remove();

            // Validation rules
            if (fieldId === 'mobile') {
                if (!/^\d{10}$/.test(value)) {
                    errorMessage = 'Please enter a valid 10-digit mobile number';
                }
            } else if (fieldId === 'name' && $('#nameField').is(':visible')) {
                if (value.length === 0) {
                    errorMessage = 'Name is required';
                } else if (!/^[A-Za-z\s]+$/.test(value)) {
                    errorMessage = 'Name can only contain letters and spaces';
                }
            }

            // Show error message if validation fails
            if (errorMessage) {
                $field.after(`<div id="${fieldId}-error" class="text-danger mt-1 mb-2 small">${errorMessage}</div>`);
                return false;
            }

            return true;
        }

        // Function to validate step 1 fields
        function validateStep1Fields() {
            const modal = $('#loginModal');
            const mobileInput = modal.find('#mobile').val() ? modal.find('#mobile').val().trim() : '';
            const nameInput = modal.find('#name').val() ? modal.find('#name').val().trim() : '';
            const step1Continue = $('#step1Continue');

            // Mobile number validation
            const mobileValid = /^\d{10}$/.test(mobileInput);

            // Name validation (only if visible)
            const nameVisible = $('#nameField').is(':visible');
            const nameValid = !nameVisible || (nameInput.length > 0);

            // Enable/disable continue button based on validation
            step1Continue.prop('disabled', !(mobileValid && nameValid));

            // Check if the mobile number exists in the system
            if (mobileValid) {
                $.ajax({
                    url: 'check_mobile.php',
                    type: 'POST',
                    data: { mobile: mobileInput, logintype: type },
                    success: function (response) {
                        if (response === 'exists') {
                            $('#nameField').hide();
                            $('#name-error').remove(); // Remove name error if exists
                            step1Continue.prop('disabled', !mobileValid);
                        } else {
                            $('#nameField').show();
                            if (nameInput.length === 0) {
                                if (!$('#name-error').length) {
                                    $('#name').after('<div id="name-error" class="text-danger mt-1 mb-2 small">Name is required</div>');
                                }
                            }
                            step1Continue.prop('disabled', !(mobileValid && nameInput.length > 0));
                        }
                    }
                });
            } else {
                $('#nameField').hide();
                step1Continue.prop('disabled', true);
            }
        }

        // Step navigation with safe binding
        safeBindEvent('#step1Continue', 'click', 'step1Continue', function () {
            const mobileValid = validateField($('#mobile'));
            const nameVisible = $('#nameField').is(':visible');
            const nameValid = nameVisible ? validateField($('#name')) : true;

            if (mobileValid && nameValid) {
                showStep(2);
                $('#mobile-error, #name-error').remove();
            }
        });

        safeBindEvent('#step2Continue', 'click', 'step2Continue', function () {
            showStep(3);
            updateStep3Fields();
        });

        safeBindEvent('input[name="method"]', 'change', 'methodChange', function () {
            updateStep3Fields();
        });

        // Form submission with safe binding and debouncing
        safeBindEvent('#submitBtn', 'click', 'submitForm', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Stop event propagation

            // Get the stored login type from the hidden field
            const loginType = $('#loginType').val() || $('#loginModal').data('loginType');
            // console.log('Submit button clicked, isSubmitting:', globalIsSubmitting, 'type:', loginType);

            // Strong guard against multiple submissions
            if (globalIsSubmitting) {
                // console.log('Prevented duplicate submission!');
                return false;
            }

            globalIsSubmitting = true;

            // Force a small delay to ensure proper synchronization
            setTimeout(function () {
                handleFormSubmission(loginType);
            }, 10);

            return false; // Prevent default and stop propagation
        });

        safeBindEvent('#closeModalBtn', 'click', 'closeModal', function () {
            $('#loginModal').fadeOut();
            clearFormHandlers();
        });

        // Initialize validation on form load
        setTimeout(validateStep1Fields, 100);
    }

    // Helper functions
    function showStep(step) {
        $('.form-step').removeClass('active');
        $(`#step-${step}`).addClass('active');
    }

    function updateStep3Fields() {
        const isOtp = $('#otp').prop('checked');
        $('#otp-input').toggle(isOtp);
        $('#password-input').toggle(!isOtp);
    }

    // Modify the handleFormSubmission function to trigger an event on successful login
    function handleFormSubmission(type) {
        // console.log('Handling form submission, isSubmitting:', globalIsSubmitting, 'type:', type);

        // If using password method, submit the form directly
        if (!$('#otp').prop('checked')) {
            processFormSubmission(type);
            return;
        }

        // For OTP method, verify OTP first
        const otp = $('#otpField').val().trim();

        if (!otp) {
            showError('Please enter the OTP.');
            globalIsSubmitting = false;
            return;
        }

        // Verify OTP and then submit form
        $.ajax({
            url: 'verify_otp.php',
            type: 'POST',
            data: { otp: otp },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // If OTP verification successful, submit the form
                    processFormSubmission(type);
                } else {
                    showError(response.message);
                    globalIsSubmitting = false;
                }
            },
            error: function () {
                showError('An error occurred. Please try again.');
                globalIsSubmitting = false;
            }
        });
    }

    function showError(message) {
        const errorDiv = `
            <div class="text-danger mt-2" id="otp-error">${message}</div>
        `;

        // Remove any existing error messages
        $('#otp-error').remove();
        $('#otpField').after(errorDiv);
    }

    // Modify the processFormSubmission function to automatically apply for job after login
    function processFormSubmission(type) {
        // Always get the type from the hidden field if available
        const formType = $('#loginType').val() || type;
        // console.log('Inside processFormSubmission, type param:', type, 'using formType:', formType);

        const mobileNumber = $('#mobile').val();
        const isOtpMethod = $('#otp').prop('checked');
        const userName = $('#name').val();

        // Different handling based on authentication method
        let inputValue;
        let auth_method;

        if (isOtpMethod) {
            inputValue = $('#otpField').val();
            auth_method = 'otp';
        } else {
            inputValue = $('#passwordField').val();
            auth_method = 'password';
        }

        // console.log('Mobile:', mobileNumber);
        // console.log('Auth value:', inputValue);
        // console.log('Method:', isOtpMethod ? 'OTP' : 'Password');
        // console.log('Login type:', formType);

        if (mobileNumber && inputValue) {
            $.ajax({
                url: 'insert_user.php',
                type: 'POST',
                data: {
                    logintype: formType,
                    name: userName,
                    mobile: mobileNumber,
                    password: inputValue,
                    auth_method: auth_method,
                    created: new Date().toISOString(),
                    updated: new Date().toISOString()
                },
                success: function (response) {
                    // console.log('Insert Response:', response);
                    // console.log('Type from Response:', formType);

                    // Always reset the submission flag
                    globalIsSubmitting = false;

                    switch (response) {
                        case 'success':
                        case 'matching':
                        case 'exist':
                            $('#loginModal').fadeOut();

                            const pendingJobId = localStorage.getItem('pendingJobApplication');
                            const pendingReferPartnerJobId = localStorage.getItem('pendingReferPartnerJobId');

                            if (pendingJobId && formType === 'candidate') {
                                window.location.href = "candidate/index.php";
                                // applyForJob(pendingJobId, mobileNumber);
                                // localStorage.removeItem('pendingJobApplication');
                            } else if (pendingReferPartnerJobId && formType === 'partner') {
                                localStorage.removeItem('pendingReferPartnerJobId');
                                window.location.href = "/partner/refer_candidate.php?jobid=" + pendingReferPartnerJobId;
                            } else {
                                window.location.href = (formType === 'employer') ? "/employer_flow/index.php" : formType + "/index.php";
                            }
                            break;

                        case 'notmatching':
                            alert("Invalid Username or Password. Please try again.");
                            break;

                        default:
                            // console.log(response);
                            alert("Error submitting the form. Please try again.");
                    }
                },
                error: function () {
                    globalIsSubmitting = false;
                    alert("An error occurred while processing your request.");
                }
            });

        } else {
            globalIsSubmitting = false;
            alert("Please fill out all required fields.");
        }
    }

    // Initialize refer candidate button
    function initReferCandidateButton() {
        $(document).off('click', '#refer-candidate, #refer-cand-btn');
        $(document).on('click', '#refer-candidate, #refer-cand-btn', function () {
            if ($('#referModal').length === 0) {
                $.get('refer_candidate.php', function (data) {
                    $('body').append(data);
                    $('#referModal').show();
                });
            } else {
                $('#referModal').show();
            }
        });
    }

    // Handle modal backdrop
    function handleModalBackdrop() {
        $(window).on('load', function () {
            $('.modal-backdrop').removeClass('show');
        });

        $(document).ready(function () {
            $('.modal-backdrop.show').removeClass('show');
        });
    }

    // Make functions accessible to window if needed
    window.submitForm = function (type) {
        // Always use the stored login type from the modal or hidden field
        const loginType = $('#loginType').val() || $('#loginModal').data('loginType') || type;
        // console.log('window.submitForm called with type:', type, 'using loginType:', loginType);

        if (!globalIsSubmitting) {
            globalIsSubmitting = true;
            processFormSubmission(loginType);
        } else {
            console.log('Prevented duplicate global submission');
        }
    };


    
})(jQuery);







