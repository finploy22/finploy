<?php
// check session is there or not
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finploy-Employer</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Favicons -->

    <link rel="icon" type="image/png" href="/images/favicon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (latest only once) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Font Awesome (only latest version) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../new-css/employer.css">
    <link rel="stylesheet" href="css/employer.css">
    <link rel="stylesheet" href="../new-css/posting_header.css">
    <link rel="stylesheet" href="../new-css/footer.css">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">

    <!-- jQuery (latest only once) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Header -->
    <!-- Desktop Navbar: Visible on lg (desktop) screens and above -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">

            <!-- Hamburger Menu mobile -->
            <button class="navbar-toggler bar-mobile" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <img src="/images/ci_hamburger-md.svg" alt="hamburger">
            </button>

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="../assets/finploy-logo.png" alt="FINPLOY" height="40" class="me-2">
            </a>

            <!-- link for mobile  -->
            <li class="nav-item m-3 left-side-links-emp-head postjob-mobile">
                <a class="nav-link btn post-job-btn-header postjob-btn " href="/employer_flow/posting_job.php">Post Job
                    <svg width="24" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M6.5873 5.08188C5.99326 4.78486 5.33471 5.3499 5.53772 5.98245L6.96683 10.4243C6.99493 10.5116 7.0465 10.5895 7.11591 10.6495C7.18532 10.7094 7.26989 10.7492 7.36036 10.7643L13.2953 11.7539C13.5738 11.8004 13.5738 12.2004 13.2953 12.2469L7.36086 13.236C7.27029 13.251 7.18562 13.2907 7.11612 13.3507C7.04662 13.4107 6.99496 13.4886 6.96683 13.576L5.53772 18.0193C5.33421 18.6519 5.99276 19.2169 6.5873 18.9199L19.0852 12.6719C19.6383 12.3954 19.6383 11.6069 19.0852 11.3298L6.5873 5.08188Z"
                            fill="white" />
                    </svg>
                </a>
            </li>
            <li class="nav-item m-3 left-side-links-emp-head cart-mobile">
                <a class="nav-link position-relative" href="/employer_flow/cart_page.php">
                    <svg width="24" height="24" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M16.1106 6.66675H9.33333V5.33341C9.33333 4.62617 9.05238 3.94789 8.55229 3.4478C8.05219 2.9477 7.37391 2.66675 6.66667 2.66675H4V5.33341H6.66667V20.0001C6.66667 20.7073 6.94762 21.3856 7.44772 21.8857C7.94781 22.3858 8.62609 22.6667 9.33333 22.6667H25.3333C25.3333 21.9595 25.0524 21.2812 24.5523 20.7811C24.0522 20.281 23.3739 20.0001 22.6667 20.0001H9.33333V17.3334H23.1947C23.8013 17.3333 24.3897 17.1264 24.8629 16.7468C25.1699 16.5005 25.4164 16.1912 25.5877 15.8425C25.0745 15.9458 24.5436 16.0001 24 16.0001C19.5817 16.0001 16 12.4183 16 8.00006C16 7.54578 16.0379 7.10035 16.1106 6.66675ZM9.33333 29.3334C10.8 29.3334 12 28.1334 12 26.6667C12 25.2001 10.8 24.0001 9.33333 24.0001C7.86667 24.0001 6.68 25.2001 6.68 26.6667C6.68 28.1334 7.86667 29.3334 9.33333 29.3334ZM20.0133 26.6667C20.0133 25.2001 21.2 24.0001 22.6667 24.0001C24.1333 24.0001 25.3333 25.2001 25.3333 26.6667C25.3333 28.1334 24.1333 29.3334 22.6667 29.3334C21.2 29.3334 20.0133 28.1334 20.0133 26.6667Z"
                            fill="#175DA8" />
                    </svg>
                    <!-- <span id="cart-count" class="badge position-absolute top-10 start-100 translate-middle">0</span> -->
                </a>
            </li>


            <!-- Hamburger Menu -->
            <button class="navbar-toggler bar-web" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <img src="/images/ci_hamburger-md.svg" alt="hamburger">
            </button>










            <!-- Navbar Links -->
            <div class="collapse navbar-collapse " id="navbarNav">
                <div class="text-end mb-3 d-lg-none">
                    <span class="close" id="closeModalBtn">×</span>
                </div>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 left-side-nav-emp-head">
                    <!-- Left Side Links -->
                    <li class="nav-item m-3 left-side-links-emp-head">
                        <a class="nav-link side-link <?= ($current == 'employer.php') ? ' active' : '' ?>" href="/">
                             <img src="../images/home-emp-header-icon-smu.svg" alt="" height="25px" width="25px" style="padding-bottom:3px"> Home
                        </a>
                    </li>
                    <li class="nav-item m-3 left-side-links-emp-head">
                        <a class="nav-link side-link <?= ($current == 'accessed_candidates.php') ? ' active' : '' ?>"
                            href="/employer_flow/accessed_candidates.php">
                           <img src="../images/acc-emp-header-icon-smu.svg"  height="27px" width="25px" style="padding-bottom:3px; margin-right: 0px;">Accessed CVs</a>
                    </li>
                    <li class="nav-item m-3 left-side-links-emp-head" id="available-credits">
                        <a class="nav-link side-link" href="javascript:(void)">
                            <img src="../images/credit-emp-header-icon-smu.svg"  height="25px" width="25px" style="padding-bottom:3px; margin-right: 0px;">My Credits
                        </a>
                        </a>
                    </li>
                    <li class="nav-item m-3 left-side-links-emp-head">
                        <a class="nav-link side-link<?= ($current == 'employer_joblisting_page.php') ? ' active' : '' ?>"
                            href="/employer_flow/employer_joblisting_page.php"><img src="../images/basil_bag-solid.svg" width="25px" alt="Post Job Icon" height="27px" style="padding-bottom:4px; margin-right: 4px;" >Posted Jobs</a>

                    </li>
                    <li class="nav-item m-3 left-side-links-emp-head cart-web">
                        <a class="nav-link position-relative" href="/employer_flow/cart_page.php">
                            <svg width="24" height="24" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.1106 6.66675H9.33333V5.33341C9.33333 4.62617 9.05238 3.94789 8.55229 3.4478C8.05219 2.9477 7.37391 2.66675 6.66667 2.66675H4V5.33341H6.66667V20.0001C6.66667 20.7073 6.94762 21.3856 7.44772 21.8857C7.94781 22.3858 8.62609 22.6667 9.33333 22.6667H25.3333C25.3333 21.9595 25.0524 21.2812 24.5523 20.7811C24.0522 20.281 23.3739 20.0001 22.6667 20.0001H9.33333V17.3334H23.1947C23.8013 17.3333 24.3897 17.1264 24.8629 16.7468C25.1699 16.5005 25.4164 16.1912 25.5877 15.8425C25.0745 15.9458 24.5436 16.0001 24 16.0001C19.5817 16.0001 16 12.4183 16 8.00006C16 7.54578 16.0379 7.10035 16.1106 6.66675ZM9.33333 29.3334C10.8 29.3334 12 28.1334 12 26.6667C12 25.2001 10.8 24.0001 9.33333 24.0001C7.86667 24.0001 6.68 25.2001 6.68 26.6667C6.68 28.1334 7.86667 29.3334 9.33333 29.3334ZM20.0133 26.6667C20.0133 25.2001 21.2 24.0001 22.6667 24.0001C24.1333 24.0001 25.3333 25.2001 25.3333 26.6667C25.3333 28.1334 24.1333 29.3334 22.6667 29.3334C21.2 29.3334 20.0133 28.1334 20.0133 26.6667Z"
                                    fill="#175DA8" />
                            </svg>
                            <span id="cart-count"
                                class="badge position-absolute top-10 start-100 translate-middle">0</span>
                        </a>
                    </li>
                    <li class="nav-item m-3 left-side-links-emp-head postjob-web">
                        <a class="nav-link btn post-job-btn-header postjob-btn "
                            href="/employer_flow/posting_job.php">Post Job <svg width="24" height="24"
                                viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6.5873 5.08188C5.99326 4.78486 5.33471 5.3499 5.53772 5.98245L6.96683 10.4243C6.99493 10.5116 7.0465 10.5895 7.11591 10.6495C7.18532 10.7094 7.26989 10.7492 7.36036 10.7643L13.2953 11.7539C13.5738 11.8004 13.5738 12.2004 13.2953 12.2469L7.36086 13.236C7.27029 13.251 7.18562 13.2907 7.11612 13.3507C7.04662 13.4107 6.99496 13.4886 6.96683 13.576L5.53772 18.0193C5.33421 18.6519 5.99276 19.2169 6.5873 18.9199L19.0852 12.6719C19.6383 12.3954 19.6383 11.6069 19.0852 11.3298L6.5873 5.08188Z"
                                    fill="white" />
                            </svg>
                        </a>
                    </li>

                    <div id="credits-container"></div>
                </ul>

            </div>
            <div class="navbar-backdrop-blur"></div>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 profile-icon-device ">
                <li class="nav-item dropdown left-side-links-emp-head">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuLink" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <?php //echo $employer_name; ?>
                        <span class="profile-desk"><?php echo strtoupper(substr($employer_name, 0, 1)); ?></span>
                    </a>
                    <ul class="dropdown-menu custom-dropdown" id="custom-dropdown" aria-labelledby="dropdownMenuLink"
                        style="margin-left: -137px !important;">
                        <li>
                            <!-- employer_profile.php -->
                            <a class="dropdown-item" href="javascript:(void)">
                                <svg width="53" height="25" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.935 9.8432C5.07807 10.7252 3 13.3885 3 16.5333C3 16.6571 3.04917 16.7758 3.13668 16.8633C3.2242 16.9508 3.3429 17 3.46667 17C3.59043 17 3.70913 16.9508 3.79665 16.8633C3.88417 16.7758 3.93333 16.6571 3.93333 16.5333C3.93333 13.185 6.65167 10.4667 10 10.4667C13.3483 10.4667 16.0667 13.185 16.0667 16.5333C16.0667 16.6571 16.1158 16.7758 16.2034 16.8633C16.2909 16.9508 16.4096 17 16.5333 17C16.6571 17 16.7758 16.9508 16.8633 16.8633C16.9508 16.7758 17 16.6571 17 16.5333C17 13.3885 14.9219 10.7252 12.065 9.8432C12.5782 9.5028 12.9991 9.04066 13.2902 8.49802C13.5813 7.95539 13.7335 7.34912 13.7333 6.73333C13.7333 4.673 12.0603 3 10 3C7.93967 3 6.26667 4.673 6.26667 6.73333C6.26648 7.34912 6.41873 7.95539 6.70984 8.49802C7.00094 9.04066 7.42185 9.5028 7.935 9.8432ZM10 3.93333C11.5451 3.93333 12.8 5.1882 12.8 6.73333C12.8 8.27847 11.5451 9.53333 10 9.53333C8.45487 9.53333 7.2 8.27847 7.2 6.73333C7.2 5.1882 8.45487 3.93333 10 3.93333Z"
                                        fill="#175DA8" stroke="#175DA8" stroke-width="0.5" />
                                </svg>My Profile
                            </a>
                        </li>
                        <li>
                            <!-- billing.php -->
                            <a class="dropdown-item" href="javascript:(void)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.0443 3.52559V2.55059C4.04442 2.20144 4.1698 1.86393 4.39765 1.59937C4.6255 1.33482 4.9407 1.1608 5.28597 1.10892L6.30264 0.956424C7.42971 0.787963 8.57556 0.787963 9.70264 0.956424L10.7193 1.10892C11.0646 1.1608 11.3798 1.33482 11.6076 1.59937C11.8355 1.86393 11.9609 2.20144 11.961 2.55059V3.52559L13.3893 3.64059C13.9207 3.68352 14.4224 3.9034 14.8141 4.26506C15.2058 4.62672 15.465 5.10928 15.5501 5.63559C15.9382 8.02941 15.9382 10.4701 15.5501 12.8639C15.465 13.3902 15.2058 13.8728 14.8141 14.2345C14.4224 14.5961 13.9207 14.816 13.3893 14.8589L11.8293 14.9839C9.28234 15.1898 6.72293 15.1898 4.17597 14.9839L2.61597 14.8589C2.08455 14.816 1.58288 14.5961 1.19115 14.2345C0.799426 13.8728 0.540277 13.3902 0.455138 12.8639C0.0670372 10.4701 0.0670372 8.02941 0.455138 5.63559C0.540446 5.10944 0.799669 4.62706 1.19138 4.26557C1.58309 3.90407 2.08467 3.68431 2.61597 3.64142L4.0443 3.52559ZM6.48847 2.19226C7.49235 2.04232 8.51293 2.04232 9.5168 2.19226L10.5335 2.34476C10.5828 2.35214 10.6278 2.37697 10.6604 2.41474C10.693 2.45251 10.7109 2.50072 10.711 2.55059V3.43809C8.90689 3.33506 7.09839 3.33506 5.2943 3.43809V2.54976C5.29435 2.49988 5.31229 2.45168 5.34487 2.41391C5.37744 2.37614 5.42248 2.35131 5.4718 2.34392L6.48847 2.19226ZM4.2768 4.76059C6.7568 4.56059 9.24847 4.56059 11.7285 4.76059L13.2885 4.88726C13.5413 4.90748 13.78 5.01191 13.9664 5.18384C14.1528 5.35577 14.2762 5.58526 14.3168 5.83559C14.3685 6.15615 14.4129 6.47781 14.4501 6.80059C12.4442 7.78791 10.2384 8.30136 8.00264 8.30136C5.76691 8.30136 3.56105 7.78791 1.55514 6.80059C1.5918 6.47837 1.63625 6.1567 1.68847 5.83559C1.72906 5.58526 1.85244 5.35577 2.03887 5.18384C2.2253 5.01191 2.46401 4.90748 2.7168 4.88726L4.2768 4.76059ZM1.44347 8.13059C3.50337 9.06698 5.73989 9.55146 8.00264 9.55146C10.2654 9.55146 12.5019 9.06698 14.5618 8.13059C14.6416 9.64612 14.5595 11.1658 14.3168 12.6639C14.2764 12.9144 14.1531 13.1441 13.9666 13.3162C13.7802 13.4883 13.5414 13.5928 13.2885 13.6131L11.7285 13.7381C9.24847 13.9381 6.7568 13.9381 4.2768 13.7381L2.7168 13.6131C2.46389 13.5928 2.22509 13.4883 2.03864 13.3162C1.8522 13.1441 1.72889 12.9144 1.68847 12.6639C1.44597 11.1639 1.36347 9.64392 1.44347 8.13059Z"
                                        fill="#175DA8" />
                                </svg> Billing
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:(void)">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.876 1.81785C11.3327 1.6742 10.7777 1.9971 10.634 2.54044L10.4782 3.12982C8.11719 2.99165 5.90455 4.52828 5.27636 6.90423L5.0702 7.68393C4.70173 9.07758 3.868 10.3055 2.71196 11.1625L2.1028 11.6156C1.86632 11.7895 1.7522 12.0845 1.80763 12.3718C1.86305 12.659 2.07789 12.8899 2.3603 12.9646L14.6391 16.211C14.9215 16.2857 15.2224 16.1912 15.4125 15.9689C15.6027 15.7466 15.6493 15.4337 15.5297 15.1656L15.2232 14.4737C14.6426 13.1543 14.5248 11.6748 14.8932 10.2811L15.0994 9.50142C15.7276 7.12547 14.5636 4.69606 12.4428 3.64926L12.5986 3.05988C12.7423 2.51654 12.4194 1.96151 11.876 1.81785ZM11.0969 4.76476C12.997 5.26716 14.1283 7.2117 13.6259 9.11184L13.4198 9.89155C13.031 11.3619 13.0787 12.9083 13.546 14.3456L4.23255 11.8831C5.34918 10.8646 6.15489 9.5439 6.54366 8.07351L6.74981 7.29381C7.25221 5.39366 9.19675 4.26237 11.0969 4.76476ZM10.2046 16.0895L6.27537 15.0507C6.1374 15.5725 6.21077 16.1273 6.48174 16.593C6.75271 17.0588 7.19869 17.3967 7.72054 17.5347C8.24239 17.6727 8.79713 17.5993 9.26289 17.3283C9.72865 17.0574 10.0666 16.6114 10.2046 16.0895Z"
                                        fill="#175DA8" />
                                </svg>Notification
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:(void)">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_1816_1884)">
                                        <path
                                            d="M10.0001 18.7619C14.8391 18.7619 18.7619 14.8391 18.7619 10.0001C18.7619 5.16107 14.8391 1.23828 10.0001 1.23828C5.16107 1.23828 1.23828 5.16107 1.23828 10.0001C1.23828 14.8391 5.16107 18.7619 10.0001 18.7619Z"
                                            stroke="#175DA8" stroke-width="1.31458" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M7.97803 7.97801C7.97803 7.5781 8.09661 7.18718 8.31879 6.85467C8.54096 6.52216 8.85675 6.263 9.22621 6.10997C9.59567 5.95693 10.0022 5.91689 10.3944 5.99491C10.7867 6.07292 11.1469 6.2655 11.4297 6.54827C11.7125 6.83105 11.9051 7.19132 11.9831 7.58354C12.0611 7.97576 12.0211 8.38231 11.868 8.75177C11.715 9.12124 11.4558 9.43702 11.1233 9.6592C10.7908 9.88137 10.3999 9.99996 9.99998 9.99996V11.3479"
                                            stroke="#175DA8" stroke-width="1.31458" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M9.99975 13.3699C9.79979 13.3699 9.60433 13.4292 9.43808 13.5403C9.27182 13.6513 9.14224 13.8092 9.06573 13.994C8.98921 14.1787 8.96919 14.382 9.0082 14.5781C9.0472 14.7742 9.14349 14.9543 9.28488 15.0957C9.42627 15.2371 9.6064 15.3334 9.80251 15.3724C9.99862 15.4114 10.2019 15.3914 10.3866 15.3149C10.5714 15.2383 10.7293 15.1088 10.8403 14.9425C10.9514 14.7763 11.0107 14.5808 11.0107 14.3808C11.0072 14.1138 10.8996 13.8587 10.7108 13.6698C10.5219 13.481 10.2668 13.3734 9.99975 13.3699Z"
                                            fill="#175DA8" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1816_1884">
                                            <rect width="20" height="20" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>FAQ
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:(void)">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.99996 17.5C9.76385 17.5 9.56579 17.42 9.40579 17.26C9.24579 17.1 9.16607 16.9022 9.16663 16.6667C9.16663 16.4306 9.24663 16.2325 9.40663 16.0725C9.56663 15.9125 9.76441 15.8328 9.99996 15.8333H15.8333V9.91667C15.8333 9.11111 15.6805 8.35417 15.375 7.64583C15.0694 6.9375 14.6527 6.31945 14.125 5.79167C13.5972 5.26389 12.9791 4.84722 12.2708 4.54167C11.5625 4.23611 10.8055 4.08333 9.99996 4.08333C9.19441 4.08333 8.43746 4.23611 7.72913 4.54167C7.02079 4.84722 6.40274 5.26389 5.87496 5.79167C5.34718 6.31945 4.93052 6.9375 4.62496 7.64583C4.3194 8.35417 4.16663 9.11111 4.16663 9.91667V14.1667C4.16663 14.4028 4.08691 14.6008 3.92746 14.7608C3.76802 14.9208 3.56996 15.0006 3.33329 15C2.87496 15 2.48246 14.8367 2.15579 14.51C1.82913 14.1833 1.66607 13.7911 1.66663 13.3333V11.6667C1.66663 11.3472 1.74302 11.0661 1.89579 10.8233C2.04857 10.5806 2.24996 10.3825 2.49996 10.2292L2.56246 9.125C2.68746 8.11111 2.97579 7.19445 3.42746 6.375C3.87913 5.55556 4.44163 4.86111 5.11496 4.29167C5.78829 3.72222 6.54524 3.28111 7.38579 2.96833C8.22635 2.65556 9.09774 2.49945 9.99996 2.5C10.9166 2.5 11.7952 2.65639 12.6358 2.96917C13.4764 3.28195 14.2297 3.72639 14.8958 4.3025C15.5625 4.87861 16.1216 5.57306 16.5733 6.38583C17.025 7.19861 17.313 8.10472 17.4375 9.10417L17.5 10.1875C17.75 10.3125 17.9513 10.4967 18.1041 10.74C18.2569 10.9833 18.3333 11.2506 18.3333 11.5417V13.4583C18.3333 13.7639 18.2569 14.0347 18.1041 14.2708C17.9513 14.5069 17.75 14.6875 17.5 14.8125V15.8333C17.5 16.2917 17.3366 16.6842 17.01 17.0108C16.6833 17.3375 16.2911 17.5006 15.8333 17.5H9.99996Z"
                                        fill="#175DA8" />
                                </svg>Support
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/logout.php">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.16663 5.83333L7.99996 7L10.1666 9.16667H1.66663V10.8333H10.1666L7.99996 13L9.16663 14.1667L13.3333 10L9.16663 5.83333ZM16.6666 15.8333H9.99996V17.5H16.6666C17.5833 17.5 18.3333 16.75 18.3333 15.8333V4.16667C18.3333 3.25 17.5833 2.5 16.6666 2.5H9.99996V4.16667H16.6666V15.8333Z"
                                        fill="#FF3333" />
                                </svg>Log out
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <script>

        // <----------------   Profile icon open and close --------------- 
        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('dropdownMenuLink');
            const profileDropdown = document.getElementById('custom-dropdown');
            profileToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });
            document.addEventListener('click', function (e) {
                if (!profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        });


        // <----------------   Available Credits--------------- 
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
            dropdown.style.top = 68 + "px";
            dropdown.style.marginLeft  =  "-770px";
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

</body>

</html>