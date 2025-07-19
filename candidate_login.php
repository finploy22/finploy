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
<style>
    #nameField {
        display: none;
    }
</style>
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
                        <button type="button" class="login-type-btn active-btn" id="loadCandidate"><svg
                                xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.03261 4.21207V5.31798L3.71331 5.42546C3.26007 5.46193 2.83214 5.64933 2.49795 5.95768C2.16377 6.26603 1.94262 6.67753 1.86987 7.12638C1.83768 7.32731 1.80808 7.5285 1.78108 7.72995C1.77502 7.7787 1.78448 7.82811 1.80811 7.87117C1.83173 7.91424 1.86833 7.94876 1.9127 7.96983L1.97267 7.99787C6.20083 9.99941 11.2639 9.99941 15.4913 7.99787L15.5512 7.96983C15.5955 7.94864 15.6319 7.91406 15.6554 7.87101C15.6788 7.82796 15.6882 7.77861 15.6821 7.72995C15.6557 7.52835 15.6263 7.32713 15.5941 7.12638C15.5213 6.67753 15.3002 6.26603 14.966 5.95768C14.6318 5.64933 14.2039 5.46193 13.7506 5.42546L12.4313 5.31876V4.21285C12.4314 3.88641 12.3143 3.57079 12.1013 3.32338C11.8884 3.07598 11.5937 2.91323 11.2709 2.86473L10.3207 2.72221C9.26741 2.56477 8.19652 2.56477 7.14319 2.72221L6.19304 2.86473C5.87036 2.91321 5.57578 3.07585 5.36284 3.32309C5.1499 3.57033 5.03272 3.88577 5.03261 4.21207ZM10.1471 3.87718C9.20887 3.73705 8.25506 3.73705 7.31687 3.87718L6.36672 4.0197C6.32062 4.0266 6.27852 4.04981 6.24808 4.08511C6.21764 4.12041 6.20088 4.16546 6.20083 4.21207V5.2362C7.88688 5.13991 9.57705 5.13991 11.2631 5.2362V4.21207C11.2631 4.16546 11.2463 4.12041 11.2158 4.08511C11.1854 4.04981 11.1433 4.0266 11.0972 4.0197L10.1471 3.87718Z"
                                    fill="#175DA8" />
                                <path
                                    d="M15.8333 9.35924C15.8318 9.33406 15.8241 9.30963 15.8111 9.28805C15.798 9.26648 15.7799 9.2484 15.7583 9.23538C15.7367 9.22235 15.7122 9.21477 15.687 9.21328C15.6618 9.2118 15.6367 9.21645 15.6137 9.22684C11.2749 11.1482 6.1893 11.1482 1.85055 9.22684C1.82756 9.21645 1.8024 9.2118 1.77721 9.21328C1.75203 9.21477 1.72758 9.22235 1.70598 9.23538C1.68437 9.2484 1.66626 9.26648 1.65318 9.28805C1.64011 9.30963 1.63247 9.33406 1.63093 9.35924C1.55205 10.8503 1.6322 12.3454 1.87002 13.8195C1.94261 14.2685 2.16369 14.6802 2.4979 14.9887C2.8321 15.2972 3.26011 15.4847 3.71347 15.5212L5.1714 15.638C7.54132 15.8296 9.92214 15.8296 12.2928 15.638L13.7508 15.5212C14.2041 15.4847 14.6321 15.2972 14.9663 14.9887C15.3005 14.6802 15.5216 14.2685 15.5942 13.8195C15.8325 12.3436 15.9135 10.8483 15.8333 9.36001"
                                    fill="#175DA8" />
                            </svg> Candidate</button>
                        <button type="button" class="login-type-btn" id="loadEmployer"><svg
                                xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17"
                                fill="none">
                                <g clip-path="url(#clip0_910_2184)">
                                    <path
                                        d="M7.23242 8.33337C9.4418 8.33337 11.2324 6.54275 11.2324 4.33337C11.2324 2.124 9.4418 0.333374 7.23242 0.333374C5.02305 0.333374 3.23242 2.124 3.23242 4.33337C3.23242 6.54275 5.02305 8.33337 7.23242 8.33337ZM10.2262 9.35212L8.73242 15.3334L7.73242 11.0834L8.73242 9.33337H5.73242L6.73242 11.0834L5.73242 15.3334L4.23867 9.35212C2.01055 9.45837 0.232422 11.2802 0.232422 13.5334V14.8334C0.232422 15.6615 0.904297 16.3334 1.73242 16.3334H12.7324C13.5605 16.3334 14.2324 15.6615 14.2324 14.8334V13.5334C14.2324 11.2802 12.4543 9.45837 10.2262 9.35212Z"
                                        fill="#888888" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_910_2184">
                                        <rect width="14" height="16" fill="white"
                                            transform="translate(0.232422 0.333374)" />
                                    </clipPath>
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
                    <div class="mb-5">
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
                    <p class="desclaimer-text">By Continuing, you agree to Finploy's <span class="text-success">Terms of
                            Service</span> and <span class="text-success">Privacy Policy</span></p>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        $(document).ready(function () {
            // === Mobile input check ===
            $('#mobile').on('input', function () {
                const mobile = $(this).val();
                $('#otp-mobile').html("+91 " + mobile);
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