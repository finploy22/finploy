<?php
// check session is there or not
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// For subcription
$planDetails = isset($_SESSION['planDetails']) ? $_SESSION['planDetails'] : [];
// Get cuurent page
$current = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finploy</title>
    <link rel="canonical" href="https://www.finploy.co.uk/" />
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap 5.3.3 CSS (only once) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/new-css/footer.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 5.3.3 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!--For Google add--> <!-- Google tag (gtag.js) -->

    <!-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P2LPJBLN"-->
    <!--height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>-->
    <!--<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':-->
    <!--new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],-->
    <!--j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=-->
    <!--'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);-->
    <!--})(window,document,'script','dataLayer','GTM-P2LPJBLN');</script>-->

    <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-B4VT8FJEHG"></script> -->
    <!-- <script> -->
        <!-- window.dataLayer = window.dataLayer || []; -->
        <!-- function gtag() { dataLayer.push(arguments); } -->
        <!-- gtag('js', new Date()); -->
        <!-- gtag('config', 'G-B4VT8FJEHG'); -->
    <!-- </script> -->
    

    <!--For Google add End-->
</head>
<!-- Full Navbar Before Login & After login -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="/assets/finploy-logo.png" alt="FINPLOY" height="40" class="me-2">
        </a>
        <!-- Login Buttons For Tab -->
        <div class="login-btn-div-tab" style="display: none;">
            <li class="nav-item">
                <a class="nav-link nav-right text-center login-link" href="#" id="employer-login-btn-tab">Employer
                    Login</a>
            </li>
            <li class="">
                <a class="nav-link  text-center login-link-separator" href="#">|</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-right  login-link" href="#" id="partner-login-btn-tab">Partner Login</a>
            </li>
            <li class="">
                <a class="nav-link  login-link-separator" href="#">|</a>
            </li>
            <li class=" ms-2">
                <a class="btn btn-success text-center text-white fw-medium rounded-medium " href="#"
                    id="candidate-login-btn-tab">Candidate Login</a>
            </li>
        </div>
        <!-- Hamburger Menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <img src="/images/ci_hamburger-md.svg" alt="hamburger">
        </button>
        <!-- Navbar Links -->
        <div class="collapse navbar-collapse " id="navbarNav">
            <div class="text-end mb-3 d-lg-none">
                <span class="close" id="closeModalBtn">×</span>
            </div>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 left-side-nav">
                <!-- Left Side Links -->
                <li class="nav-item m-3 left-side-links">
                    <a class="nav-link side-link <?= ($current == 'index.php') ? ' active' : '' ?>" href="/">
                        <svg style="padding-bottom: 5px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 20 20" fill="none">
                            <path
                                d="M18.9008 9.34915L10.6508 1.09915C10.5656 1.01323 10.4642 0.945039 10.3525 0.898501C10.2408 0.851963 10.121 0.828003 9.99996 0.828003C9.87895 0.828003 9.75914 0.851963 9.64744 0.898501C9.53573 0.945039 9.43435 1.01323 9.34913 1.09915L1.09913 9.34915C0.971937 9.47806 0.885774 9.64175 0.851514 9.81957C0.817255 9.9974 0.836434 10.1814 0.906632 10.3483C0.9754 10.5157 1.09218 10.659 1.24226 10.7602C1.39234 10.8613 1.56899 10.9157 1.74997 10.9167H2.66663V17.6083C2.68334 18.0371 2.86923 18.4418 3.18362 18.7339C3.49801 19.0259 3.91528 19.1815 4.34413 19.1667H6.79163C7.03475 19.1667 7.2679 19.0701 7.43981 18.8982C7.61172 18.7263 7.7083 18.4931 7.7083 18.25V13.7583C7.7083 13.5152 7.80488 13.282 7.97678 13.1101C8.14869 12.9382 8.38185 12.8417 8.62496 12.8417H11.375C11.6181 12.8417 11.8512 12.9382 12.0231 13.1101C12.1951 13.282 12.2916 13.5152 12.2916 13.7583V18.25C12.2916 18.4931 12.3882 18.7263 12.5601 18.8982C12.732 19.0701 12.9652 19.1667 13.2083 19.1667H15.6558C16.0846 19.1815 16.5019 19.0259 16.8163 18.7339C17.1307 18.4418 17.3166 18.0371 17.3333 17.6083V10.9167H18.25C18.4309 10.9157 18.6076 10.8613 18.7577 10.7602C18.9077 10.659 19.0245 10.5157 19.0933 10.3483C19.1635 10.1814 19.1827 9.9974 19.1484 9.81957C19.1142 9.64175 19.028 9.47806 18.9008 9.34915Z"
                                fill="#175DA8" />
                        </svg> Home
                    </a>
                </li>
                <li class="nav-item m-3 left-side-links pricing-style">
                    <a class="nav-link side-link <?= ($current == 'plans.php') ? ' active' : '' ?>"
                        href="subscription/plans.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                            <path
                                d="M19.2503 7.33325C19.2503 8.26825 18.1632 9.09325 16.5003 9.59009C15.4608 9.90175 14.1967 10.0833 12.8337 10.0833C11.4706 10.0833 10.2065 9.90084 9.16699 9.59009C7.50508 9.09325 6.41699 8.26825 6.41699 7.33325C6.41699 5.81434 9.28983 4.58325 12.8337 4.58325C16.3775 4.58325 19.2503 5.81434 19.2503 7.33325Z"
                                fill="#175DA8" />
                            <path
                                d="M19.2503 7.33325C19.2503 5.81434 16.3775 4.58325 12.8337 4.58325C9.28983 4.58325 6.41699 5.81434 6.41699 7.33325M19.2503 7.33325V10.9999C19.2503 11.9349 18.1632 12.7599 16.5003 13.2568C15.4608 13.5684 14.1967 13.7499 12.8337 13.7499C11.4706 13.7499 10.2065 13.5675 9.16699 13.2568C7.50508 12.7599 6.41699 11.9349 6.41699 10.9999V7.33325M19.2503 7.33325C19.2503 8.26825 18.1632 9.09325 16.5003 9.59009C15.4608 9.90175 14.1967 10.0833 12.8337 10.0833C11.4706 10.0833 10.2065 9.90084 9.16699 9.59009C7.50508 9.09325 6.41699 8.26825 6.41699 7.33325"
                                stroke="#175DA8" stroke-width="1.83333" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M2.75 11V14.6666C2.75 15.6016 3.83808 16.4266 5.5 16.9235C6.5395 17.2351 7.80358 17.4166 9.16667 17.4166C10.5297 17.4166 11.7938 17.2342 12.8333 16.9235C14.4952 16.4266 15.5833 15.6016 15.5833 14.6666V13.75M2.75 11C2.75 9.90273 4.24875 8.95581 6.41667 8.51489M2.75 11C2.75 11.935 3.83808 12.76 5.5 13.2568C6.5395 13.5685 7.80358 13.75 9.16667 13.75C9.80375 13.75 10.4188 13.7106 11 13.6363"
                                stroke="#175DA8" stroke-width="1.83333" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg> Pricing
                    </a>
                </li>
                <li class="nav-item m-3 left-side-links">
                    <a class="nav-link side-link<?= ($current == 'testimonial.php') ? ' active' : '' ?>"
                        href="/testimonial.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="22" viewBox="0 0 28 22" fill="none">
                            <path d="M13.7505 11C14.6787 11 15.569 10.6313 16.2254 9.97487C16.8817 9.3185 17.2505 8.42826 17.2505 7.5C17.2505 6.57174 16.8817 5.6815 16.2254 5.02513C15.569 4.36875 14.6787 4 13.7505 4C12.8222 4 11.932 4.36875 11.2756 5.02513C10.6192 5.6815 10.2505 6.57174 10.2505 7.5C10.2505 8.42826 10.6192 9.3185 11.2756 9.97487C11.932 10.6313 12.8222 11 13.7505 11ZM7.84971 15.375C7.70205 15.7852 7.62549 16.2281 7.62549 16.6875C7.62549 17.4148 8.21065 18 8.93799 18H18.563C19.2903 18 19.8755 17.4148 19.8755 16.6875C19.8755 16.2281 19.7989 15.7852 19.6513 15.375C19.6239 15.2984 19.5966 15.2273 19.5638 15.1563C19.0497 13.9367 17.9341 13.0289 16.5942 12.8047C16.381 12.7664 16.1622 12.75 15.938 12.75H11.563C11.3388 12.75 11.12 12.7664 10.9067 12.8047C10.863 12.8102 10.8138 12.8211 10.77 12.832C9.4083 13.1109 8.30361 14.0898 7.84971 15.375ZM5.86455 9.25C6.20927 9.25 6.55062 9.1821 6.8691 9.05018C7.18757 8.91827 7.47695 8.72491 7.72071 8.48116C7.96446 8.2374 8.15782 7.94802 8.28974 7.62954C8.42165 7.31106 8.48955 6.96972 8.48955 6.625C8.48955 6.28028 8.42165 5.93894 8.28974 5.62046C8.15782 5.30198 7.96446 5.0126 7.72071 4.76884C7.47695 4.52509 7.18757 4.33173 6.8691 4.19982C6.55062 4.0679 6.20927 4 5.86455 4C5.51983 4 5.17849 4.0679 4.86001 4.19982C4.54153 4.33173 4.25215 4.52509 4.0084 4.76884C3.76464 5.0126 3.57129 5.30198 3.43937 5.62046C3.30745 5.93894 3.23955 6.28028 3.23955 6.625C3.23955 6.96972 3.30745 7.31106 3.43937 7.62954C3.57129 7.94802 3.76464 8.2374 4.0084 8.48116C4.25215 8.72491 4.54153 8.91827 4.86001 9.05018C5.17849 9.1821 5.51983 9.25 5.86455 9.25ZM4.41533 11C2.80752 11 1.50049 12.307 1.50049 13.9148C1.50049 14.7188 2.15127 15.375 2.96064 15.375H6.02861C6.46611 13.5102 7.82236 12.0008 9.59424 11.35C9.18408 11.1258 8.7083 11 8.21064 11H4.4208H4.41533ZM24.5403 15.375C25.3442 15.375 26.0005 14.7242 26.0005 13.9148C26.0005 12.3016 24.6935 11 23.0856 11H19.2958C18.7927 11 18.3224 11.1258 17.9122 11.35C19.6841 12.0008 21.0403 13.5102 21.4778 15.375H24.5458H24.5403ZM21.6255 9.25C21.9702 9.25 22.3116 9.1821 22.63 9.05018C22.9485 8.91827 23.2379 8.72491 23.4816 8.48116C23.7254 8.2374 23.9188 7.94802 24.0507 7.62954C24.1826 7.31106 24.2505 6.96972 24.2505 6.625C24.2505 6.28028 24.1826 5.93894 24.0507 5.62046C23.9188 5.30198 23.7254 5.0126 23.4816 4.76884C23.2379 4.52509 22.9485 4.33173 22.63 4.19982C22.3116 4.0679 21.9702 4 21.6255 4C21.2808 4 20.9394 4.0679 20.6209 4.19982C20.3025 4.33173 20.0131 4.52509 19.7693 4.76884C19.5256 5.0126 19.3322 5.30198 19.2003 5.62046C19.0684 5.93894 19.0005 6.28028 19.0005 6.625C19.0005 6.96972 19.0684 7.31106 19.2003 7.62954C19.3322 7.94802 19.5256 8.2374 19.7693 8.48116C20.0131 8.72491 20.3025 8.91827 20.6209 9.05018C20.9394 9.1821 21.2808 9.25 21.6255 9.25Z" fill="#175DA8"></path>
                        </svg> Testimonials
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto ml-2  mb-lg-0">
                <!-- Show login buttons -->
                <li class="nav-item">
                    <a class="nav-link nav-right text-center login-link" href="#" id="employer-login-btn">Employer
                        Login</a>
                </li>
                <li class="">
                    <a class="nav-link  text-center login-link-separator" href="#">|</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-right  login-link" href="#" id="partner-login-btn">Partner Login</a>
                </li>
                <li class="">
                    <a class="nav-link  login-link-separator" href="#">|</a>
                </li>
                <li class=" ms-2">
                    <a class="btn btn-success text-center text-white fw-medium rounded-medium " href="#"
                        id="candidate-login-btn">Candidate Login</a>
                </li>
            </ul>
        </div>
        <div class="navbar-backdrop-blur"></div>
    </div>
</nav>
<!-- Login Buttons Second header for Mobile  -->
<div class="finploy-contact p-2 text-center">
    <a href="#" class="users-btn text-decoration-none font-weight-bold head-mobile" id="employer-login-mobile">
        EmployerLogin</a>
    <a href="#" class="users-btn text-decoration-none"> | </a>
    <a href="#" class="users-btn text-decoration-none font-weight-bold head-mobile" id="partner-login-mobile"> Partner
        Login</a>
    <a href="#" class="users-btn text-decoration-none ">|</a>
    <a href="#" class="btn btn-success head-mobile" id="candidate-login-mobile"> Candidate Login</a>
</div>
<!-- Whatapp icon show  -->
<a class="nav-link icon-link whatsapp-float d-flex flex-column align-items-center text-decoration-none "
    href="https://wa.me/919137523589" target="_blank">
    <img src="/images/wa_img.svg" width="40" height="40" alt="WhatsApp Logo" class="circle pulse">
    <span style="color:#198754; font-size: 14px; margin-top: 4px;font-weight: 600;">Chat us</span>
</a>

<!-- Phone icon show  -->

<a class="nav-link icon-link tel-float d-flex flex-column align-items-center text-decoration-none "
    href="tel:8169449669" target="_blank">
    <img src="/images/phone_img.svg" width="40" height="40" alt="Call Logo" class="">
    <span style="color:#175DA8;font-size: 14px; margin: 4px -7px 0 -15px; font-weight: 600;">Call us</span>
</a>

<!-- <div class="circle pulse green"></div> -->

<style>
    .circle {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        box-shadow: 0px 0px 1px 1px #0000001a;
    }

    .pulse {
        animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
        0% {
            box-shadow: 0 0 0 0px rgba(0, 0, 0, 0.2);
        }

        100% {
            box-shadow: 0 0 0 10px rgba(0, 0, 0, 0);
        }
    }
</style>

<script>
    // For subcription 
    const planDetails = <?php echo json_encode($_SESSION['planDetails'] ?? []); ?>;
    // Hamburger Popup
    document.querySelectorAll('.navbar-toggler').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelector('.navbar-collapse').classList.toggle('show');
        });
    });
    document.addEventListener('click', function (event) {
        const navbar = document.querySelector('.navbar-collapse');
        const toggler = document.querySelector('.navbar-toggler');
        const backdrop = document.querySelector('.navbar-backdrop-blur');
        // If navbar is open and clicked outside of navbar and toggler
        if (navbar.classList.contains('show') &&
            !navbar.contains(event.target) &&
            !toggler.contains(event.target)) {
            navbar.classList.remove('show');
            backdrop.classList.remove('active');
        }
    });
    // Colse button in burgerpopup
    document.getElementById('closeModalBtn').addEventListener('click', function () {
        document.querySelector('.navbar-collapse').classList.remove('show');
    });
</script>