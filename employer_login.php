<?php
include 'db/connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/index.js"></script>
</head>

<body>

    <!-- The Modal -->
    <div id="loginModal" class="modal">

        <!-- Modal Content -->
        <div class="modal-content m-2">
            <span class="close" id="closeModalBtn">&times;</span>
            <form class="" id="loginForm">
                <div class="mb-3 justify-center text-center">
                    <img class="login-logo" src="assets/finploy-logo.png" alt="FINPLOY" height="40">
                </div>
                <div class="mb-4">
                    <h6 class="text-success fw-bold text-center">LOGIN / REGISTER</h6>
                </div>


                <!-- Step 1: Mobile Number Input -->
                <div class="form-step active" id="step-1">
                    <div class="mb-4 btn-group-container text-center">
                        <button type="button" class="login-type-btn" id="loadCandidate"><svg
                                xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.03408 4.21195V5.31786L3.71478 5.42533C3.26153 5.46181 2.83361 5.64921 2.49942 5.95756C2.16523 6.26591 1.94408 6.6774 1.87133 7.12626C1.83914 7.32719 1.80955 7.52838 1.78255 7.72983C1.77649 7.77858 1.78594 7.82799 1.80957 7.87105C1.8332 7.91411 1.8698 7.94863 1.91417 7.96971L1.97413 7.99774C6.2023 9.99929 11.2653 9.99929 15.4927 7.99774L15.5527 7.96971C15.5969 7.94851 15.6333 7.91394 15.6568 7.87089C15.6803 7.82784 15.6897 7.77849 15.6835 7.72983C15.6571 7.52823 15.6278 7.32701 15.5955 7.12626C15.5228 6.6774 15.3016 6.26591 14.9674 5.95756C14.6333 5.64921 14.2053 5.46181 13.7521 5.42533L12.4328 5.31864V4.21273C12.4329 3.88629 12.3158 3.57067 12.1028 3.32326C11.8898 3.07586 11.5952 2.9131 11.2724 2.86461L10.3222 2.72208C9.26887 2.56464 8.19799 2.56464 7.14466 2.72208L6.19451 2.86461C5.87183 2.91309 5.57725 3.07572 5.3643 3.32297C5.15136 3.57021 5.03419 3.88564 5.03408 4.21195ZM10.1485 3.87706C9.21033 3.73693 8.25653 3.73693 7.31833 3.87706L6.36818 4.01958C6.32208 4.02648 6.27999 4.04969 6.24955 4.08499C6.21911 4.12029 6.20234 4.16534 6.2023 4.21195V5.23608C7.88834 5.13979 9.57851 5.13979 11.2646 5.23608V4.21195C11.2645 4.16534 11.2477 4.12029 11.2173 4.08499C11.1869 4.04969 11.1448 4.02648 11.0987 4.01958L10.1485 3.87706Z"
                                    fill="#888888" />
                                <path
                                    d="M15.8348 9.35911C15.8332 9.33393 15.8256 9.30951 15.8125 9.28793C15.7994 9.26636 15.7813 9.24828 15.7597 9.23525C15.7381 9.22223 15.7137 9.21465 15.6885 9.21316C15.6633 9.21167 15.6381 9.21633 15.6152 9.22672C11.2764 11.148 6.19077 11.148 1.85202 9.22672C1.82903 9.21633 1.80386 9.21167 1.77868 9.21316C1.75349 9.21465 1.72905 9.22223 1.70744 9.23525C1.68584 9.24828 1.66772 9.26636 1.65465 9.28793C1.64157 9.30951 1.63394 9.33393 1.63239 9.35911C1.55351 10.8501 1.63366 12.3453 1.87149 13.8194C1.94408 14.2684 2.16516 14.68 2.49936 14.9885C2.83356 15.297 3.26157 15.4845 3.71493 15.5211L5.17286 15.6379C7.54278 15.8295 9.92361 15.8295 12.2943 15.6379L13.7522 15.5211C14.2056 15.4845 14.6336 15.297 14.9678 14.9885C15.302 14.68 15.5231 14.2684 15.5957 13.8194C15.834 12.3435 15.915 10.8482 15.8348 9.35989"
                                    fill="#888888" />
                            </svg> Candidate</button>
                        <button type="button" class="login-type-btn active-btn" id="loadEmployer"><svg
                                xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17"
                                fill="none">
                                <g filter="url(#filter0_d_1529_1325)">
                                    <path
                                        d="M7.23389 8.33325C9.44326 8.33325 11.2339 6.54263 11.2339 4.33325C11.2339 2.12388 9.44326 0.333252 7.23389 0.333252C5.02451 0.333252 3.23389 2.12388 3.23389 4.33325C3.23389 6.54263 5.02451 8.33325 7.23389 8.33325ZM10.2276 9.352L8.73389 15.3333L7.73389 11.0833L8.73389 9.33325H5.73389L6.73389 11.0833L5.73389 15.3333L4.24014 9.352C2.01201 9.45825 0.233887 11.2801 0.233887 13.5333V14.8333C0.233887 15.6614 0.905762 16.3333 1.73389 16.3333H12.7339C13.562 16.3333 14.2339 15.6614 14.2339 14.8333V13.5333C14.2339 11.2801 12.4558 9.45825 10.2276 9.352Z"
                                        fill="#175DA8" />
                                </g>
                                <defs>
                                    <filter id="filter0_d_1529_1325" x="-3.76611" y="0.333252" width="22" height="24"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="4" />
                                        <feGaussianBlur stdDeviation="2" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_1529_1325" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1529_1325"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg> Employer</button>
                        <button type="button" class="login-type-btn" id="loadPartner"><svg
                                xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16"
                                fill="none">
                                <g clip-path="url(#clip0_910_2188)">
                                    <path
                                        d="M10.332 2.99609L7.49609 5.29297C7.02441 5.67383 6.93359 6.35937 7.29102 6.84863C7.66895 7.37012 8.4043 7.47266 8.91113 7.07715L11.8203 4.81543C12.0254 4.65723 12.3184 4.69238 12.4795 4.89746C12.6406 5.10254 12.6025 5.39551 12.3975 5.55664L11.7852 6.03125L15.8574 9.78125V4.25H15.8369L15.7227 4.17676L13.5957 2.81445C13.1475 2.52734 12.623 2.375 12.0898 2.375C11.4512 2.375 10.8301 2.59473 10.332 2.99609ZM11 6.64062L9.48535 7.81836C8.5625 8.53906 7.22363 8.35156 6.53223 7.40234C5.88184 6.50879 6.0459 5.26074 6.9043 4.56641L9.3418 2.59473C9.00195 2.45117 8.63574 2.37793 8.26367 2.37793C7.71289 2.375 7.17676 2.53906 6.7168 2.84375L4.60742 4.25V10.8125H5.43359L8.11133 13.2559C8.68555 13.7803 9.57324 13.7393 10.0977 13.165C10.2588 12.9863 10.3672 12.7783 10.4229 12.5615L10.9209 13.0186C11.4922 13.543 12.3828 13.5049 12.9072 12.9336C13.0391 12.79 13.1357 12.623 13.1973 12.4502C13.7656 12.8311 14.5391 12.752 15.0166 12.2305C15.541 11.6592 15.5029 10.7686 14.9316 10.2441L11 6.64062ZM1.32617 4.25C1.06836 4.25 0.857422 4.46094 0.857422 4.71875V10.8125C0.857422 11.3311 1.27637 11.75 1.79492 11.75H2.73242C3.25098 11.75 3.66992 11.3311 3.66992 10.8125V4.25H1.32617ZM2.26367 9.875C2.38799 9.875 2.50722 9.92439 2.59513 10.0123C2.68304 10.1002 2.73242 10.2194 2.73242 10.3437C2.73242 10.4681 2.68304 10.5873 2.59513 10.6752C2.50722 10.7631 2.38799 10.8125 2.26367 10.8125C2.13935 10.8125 2.02012 10.7631 1.93222 10.6752C1.84431 10.5873 1.79492 10.4681 1.79492 10.3437C1.79492 10.2194 1.84431 10.1002 1.93222 10.0123C2.02012 9.92439 2.13935 9.875 2.26367 9.875ZM16.7949 4.25V10.8125C16.7949 11.3311 17.2139 11.75 17.7324 11.75H18.6699C19.1885 11.75 19.6074 11.3311 19.6074 10.8125V4.71875C19.6074 4.46094 19.3965 4.25 19.1387 4.25H16.7949ZM17.7324 10.3437C17.7324 10.2194 17.7818 10.1002 17.8697 10.0123C17.9576 9.92439 18.0769 9.875 18.2012 9.875C18.3255 9.875 18.4447 9.92439 18.5326 10.0123C18.6205 10.1002 18.6699 10.2194 18.6699 10.3437C18.6699 10.4681 18.6205 10.5873 18.5326 10.6752C18.4447 10.7631 18.3255 10.8125 18.2012 10.8125C18.0769 10.8125 17.9576 10.7631 17.8697 10.6752C17.7818 10.5873 17.7324 10.4681 17.7324 10.3437Z"
                                        fill="#888888" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_910_2188">
                                        <rect width="18.75" height="15" fill="white"
                                            transform="translate(0.857422 0.5)" />
                                    </clipPath>
                                </defs>
                            </svg> Partner</button>
                    </div>
                    <div class="mb-4 mt-5">
                        <a href="https://wa.me/9137523589" class="btn btn-success w-100 login-btn"><i
                                class="fab fa-whatsapp me-2"></i> Login with WhatsApp</a>
                    </div>
                    <div class="divider-container">
                        <div class="divider login-divider">
                            <span class="divider-line"></span>
                            <span class="divider-text">or</span>
                            <span class="divider-line"></span>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="mobile" class="form-label text-success">Mobile No:</label>
                        <input type="tel" class="form-control login-inputfield" maxlength="10" id="mobile"
                            placeholder="Enter your Mobile Number" required>
                    </div>
                    <div class="mb-5" id="nameField">
                        <label for="name" class="form-label text-success">Name:</label>
                        <input type="name" class="form-control login-inputfield" id="name"
                            placeholder="Enter your Full Name" required>
                    </div>
                    <div class="mb-5 static-login-div">
                        <button type="button" class="btn btn-outline-success w-100 fw-600 login-btn"
                            id="step1Continue"><i class="fa fa-sign-in me-2" aria-hidden="true"></i> Login with OTP /
                            Password</button>
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
                                <input class="form-check-input" type="radio" name="method" id="password"
                                    value="password">
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
                        <p class="otp-help-text">We have send OTP on: <span class="text-success"
                                id="otp-mobile">+91</span></p>
                        <input type="text" class="form-control" id="otpField" placeholder="Enter OTP" required><br>
                        <p class="resend-help-text mt-2">Didn’t get the OTP ? <span class="text-success text-underlined"
                                id="resend-otp">Resend OTP</span></p>
                    </div>
                    <div class="mb-5 position-relative" id="password-input" style="display: none;">
                        <label for="passwordField" class="form-label text-success">Enter Password:</label>
                        <input type="password" class="form-control" id="passwordField" placeholder="Enter Password"
                            required>
                        <img id="toggle-password" src="./assets/visibility_off.svg"
                            style="position: absolute; right: 15px; top: 41px; width: 22px; height: 22px; cursor: pointer;"
                            onclick="togglePassword('passwordField', 'toggle-password')" />
                    </div>
                    <div class="d-flex justify-content-between mb-5">
                        <!-- <button type="button" class="btn btn-secondary" id="step3Back">Back</button> -->
                        <button type="button" class="btn btn-success w-100" id="submitBtn">Submit</button>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="desclaimer-text">By Continuing, you agree to Finploy's <span class="text-success"><a
                                style="text-decoration: none" class="text-success" href="../terms.php">Terms of
                                Service</a></span> and <span class="text-success"><a class="text-success"
                                style="text-decoration: none" href="../privacy_policy.php">Privacy Policy</a></span></p>
                </div>
            </form>
        </div>
    </div>
    <script src="js/index.js"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            icon.src = isPassword ? "./assets/visibility.svg" : "./assets/visibility_off.svg";
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentStep = 1;
            const mobileInput = document.getElementById('mobile');
            const step1Continue = document.getElementById('step1Continue');
            const step2Continue = document.getElementById('step2Continue');
            const step2Back = document.getElementById('step2Back');
            const step3Back = document.getElementById('step3Back');
            const submitBtn = document.getElementById('submitBtn');
            const otpRadio = document.getElementById('otp');
            const passwordRadio = document.getElementById('password');

            const modal = document.getElementById("loginModal");
            const closeModalBtn = document.getElementById("closeModalBtn");

            // Close the modal
            closeModalBtn.onclick = function () {
                modal.style.display = "none";
            }

            // Enable/disable continue button based on mobile input
            mobileInput.addEventListener('input', function () {
                step1Continue.disabled = !this.value.trim();
            });

            // Initialize first step button state
            step1Continue.disabled = !mobileInput.value.trim();

            // Step 1 Continue button
            step1Continue.addEventListener('click', function () {
                if (mobileInput.value.trim()) {
                    showStep(2);
                }
            });

            // Step 2 Continue button
            step2Continue.addEventListener('click', function () {
                showStep(3);
                updateStep3Fields();
            });

            // // Back buttons
            // step2Back.addEventListener('click', function() {
            //     showStep(1);
            // });

            // step3Back.addEventListener('click', function() {
            //     showStep(2);
            // });

            // Method selection change
            otpRadio.addEventListener('change', updateStep3Fields);
            passwordRadio.addEventListener('change', updateStep3Fields);

            // Submit button
            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                console.log('submit btn clicked')
                submitForm();
            });

            function showStep(step) {
                currentStep = step;
                document.querySelectorAll('.form-step').forEach(el => {
                    el.classList.remove('active');
                });
                document.getElementById(`step-${step}`).classList.add('active'); // Use backticks here
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
        });
    </script>
    <script>
        $(document).ready(function () {
            // === Mobile input check ===
            $('#mobile').on('input', function () {
                const mobile = $(this).val();
                $('#otp-mobile').html("+91" + mobile);
                if (mobile.length === 10) {
                    $.ajax({
                        url: 'check_mobile.php',
                        type: 'POST',
                        data: {
                            mobile: mobile,
                            logintype: 'candidate',
                        },
                        success: function (response) {
                            if (response === 'exists') {
                                $('#nameField').hide();
                            } else {
                                $('#nameField').show();
                            }
                        }
                    });
                } else {
                    $('#nameField').hide();
                }
                if (mobile === '9082658097') {
                    $('.static-login-div').html(`
          <button type="button" class="btn btn-outline-success w-100 fw-600 login-btn" id="login-otp">
            <i class="fa fa-sign-in me-2" aria-hidden="true"></i> Login with OTP
          </button>
        `);
                } else {
                    $('.static-login-div').html(`
          <button type="button" class="btn btn-outline-success w-100 fw-600 login-btn" id="step1Continue">
            <i class="fa fa-sign-in me-2" aria-hidden="true"></i> Login with OTP / Password
          </button>
        `);
                }
            });

            // Direct OTP login for mobile 9082658097
            $(document).on('click', '#login-otp', function () {
                const mobile = $('#mobile').val().trim();
                const name = $('#name').val().trim();
                $('#step-1').hide();
                $.post('send_otp.php', { mobile, name }, function () {
                    $('#step-2').hide();
                    $('#step-3').show();
                    $('#otp-input').show();
                    $('#password-input').hide();
                }, 'json');
            });

            // === Handle Continue or Resend OTP ===
            $('#step2Continue, #resend-otp').off('click').on('click', function () {
                const mobile = $('#mobile').val().trim();
                const name = $('#name').val().trim();
                const isOtpSelected = $('#otp').prop('checked');
                if (!isOtpSelected && this.id === 'step2Continue') {
                    $('#step-2').hide();
                    $('#step-3').show();
                    $('#otp-input').hide();
                    $('#password-input').show();
                    return;
                }

                $.ajax({
                    url: 'send_otp.php',
                    type: 'POST',
                    data: { mobile, name },
                    dataType: 'json',
                    success: function (response) {
                        $('#step-2').hide();
                        $('#step-3').show();
                        $('#otp-input').show();
                        $('#password-input').hide();
                    },
                    error: function (xhr, status, error) {
                        console.error('❌ AJAX Error:', status, error);
                    }
                });
            });
            // === Handle OTP submission ===
            $('#submitBtn').off('click').on('click', function (e) {
                e.preventDefault();

                if (!$('#otp').prop('checked')) {
                    submitForm('employer');
                    return;
                }
                const otp = $('#otpField').val().trim();
                if (otp === '') {
                    showError('Please enter the OTP.');
                    return;
                }
                $.ajax({
                    url: 'verify_otp.php',
                    type: 'POST',
                    data: { otp: otp },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            submitForm('candidate');
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function () {
                        showError('An error occurred. Please try again.');
                    }
                });
            });
            function showError(message) {
                $('#otp-error').remove();
                $('#otpField').after(`<div class="text-danger mt-2" id="otp-error">${message}</div>`);
            }
        });
    </script>


</body>

</html>