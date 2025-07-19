<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['name']);
$loggedInName = $isLoggedIn ? $_SESSION['name'] : null;

// Generate the first letter of the name (uppercase)
$firstLetter = $isLoggedIn ? strtoupper($loggedInName[0]) : null;

// Generate a dynamic color based on the name (hashing logic)
$dynamicColor = $isLoggedIn ? '#' . substr(md5($loggedInName), 0, 6) : null;
$current = basename($_SERVER['PHP_SELF']); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finploy-Candidate</title>
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../new-css/posting_header.css">
    <link rel="stylesheet" href="../new-css/footer.css">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">

    <!--For Google add--> <!-- Google tag (gtag.js) -->

    <!--<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P2LPJBLN"-->
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

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        ul {
            padding-left: 0 !important;
        }

        ul li {
            list-style: none;
        }

        .navbar {
            padding: 0.5rem 1rem;
        }

        .profile-circle {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-primary-custom {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: bold;
        }

        .btn-primary-custom:hover {
            background-color: #0056b3;
        }

        /* Desktop styles */
        .desktop-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-info i {
            color: #25D366;
            /* WhatsApp Green */
        }

        .desktop-navbar .contact-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .refer-cand-btn {
            font-size: 13px !important;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            /* Ensures vertical alignment */
        }

        .notify-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .profile-wrapper {
            cursor: pointer;
        }

        .profile-dropdown {
            position: absolute;
            top: 40px !important;
            right: -8px !important;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            display: none;
            min-width: 150px;
            z-index: 1000;
            box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;
        }

        .profile-dropdown ul li a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }

        .profile-dropdown ul li a:hover {
            background-color: #f1f1f1;
        }

        .profile-dropdown::before {
            content: "";
            position: absolute;
            top: -10px;
            /* Move it above the menu */
            right: 20px;
            /* Adjust horizontal position */
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #fff;
            /* same color as dropdown */
        }


        /* Mobile styles */
        @media (max-width: 768px) {
            .desktop-navbar {
                display: none;
            }

            .mobile-navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        }

        @media (min-width: 769px) {
            .mobile-navbar {
                display: none;
            }
        }


        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0px;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }
    </style>
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

                <ul class="navbar-nav me-auto mb-2 mb-lg-0 left-side-nav" style="    margin-left: 320px;">
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

                    <li class="nav-item m-3 left-side-links" style="margin: 0px 58px !important;">
                        <a class="nav-link side-link<?= ($current == 'testimonial.php') ? ' active' : '' ?>"
                            href="/testimonial.php">
                           <svg xmlns="http://www.w3.org/2000/svg" width="28" height="22" viewBox="0 0 28 22" fill="none">
                            <path d="M13.7505 11C14.6787 11 15.569 10.6313 16.2254 9.97487C16.8817 9.3185 17.2505 8.42826 17.2505 7.5C17.2505 6.57174 16.8817 5.6815 16.2254 5.02513C15.569 4.36875 14.6787 4 13.7505 4C12.8222 4 11.932 4.36875 11.2756 5.02513C10.6192 5.6815 10.2505 6.57174 10.2505 7.5C10.2505 8.42826 10.6192 9.3185 11.2756 9.97487C11.932 10.6313 12.8222 11 13.7505 11ZM7.84971 15.375C7.70205 15.7852 7.62549 16.2281 7.62549 16.6875C7.62549 17.4148 8.21065 18 8.93799 18H18.563C19.2903 18 19.8755 17.4148 19.8755 16.6875C19.8755 16.2281 19.7989 15.7852 19.6513 15.375C19.6239 15.2984 19.5966 15.2273 19.5638 15.1563C19.0497 13.9367 17.9341 13.0289 16.5942 12.8047C16.381 12.7664 16.1622 12.75 15.938 12.75H11.563C11.3388 12.75 11.12 12.7664 10.9067 12.8047C10.863 12.8102 10.8138 12.8211 10.77 12.832C9.4083 13.1109 8.30361 14.0898 7.84971 15.375ZM5.86455 9.25C6.20927 9.25 6.55062 9.1821 6.8691 9.05018C7.18757 8.91827 7.47695 8.72491 7.72071 8.48116C7.96446 8.2374 8.15782 7.94802 8.28974 7.62954C8.42165 7.31106 8.48955 6.96972 8.48955 6.625C8.48955 6.28028 8.42165 5.93894 8.28974 5.62046C8.15782 5.30198 7.96446 5.0126 7.72071 4.76884C7.47695 4.52509 7.18757 4.33173 6.8691 4.19982C6.55062 4.0679 6.20927 4 5.86455 4C5.51983 4 5.17849 4.0679 4.86001 4.19982C4.54153 4.33173 4.25215 4.52509 4.0084 4.76884C3.76464 5.0126 3.57129 5.30198 3.43937 5.62046C3.30745 5.93894 3.23955 6.28028 3.23955 6.625C3.23955 6.96972 3.30745 7.31106 3.43937 7.62954C3.57129 7.94802 3.76464 8.2374 4.0084 8.48116C4.25215 8.72491 4.54153 8.91827 4.86001 9.05018C5.17849 9.1821 5.51983 9.25 5.86455 9.25ZM4.41533 11C2.80752 11 1.50049 12.307 1.50049 13.9148C1.50049 14.7188 2.15127 15.375 2.96064 15.375H6.02861C6.46611 13.5102 7.82236 12.0008 9.59424 11.35C9.18408 11.1258 8.7083 11 8.21064 11H4.4208H4.41533ZM24.5403 15.375C25.3442 15.375 26.0005 14.7242 26.0005 13.9148C26.0005 12.3016 24.6935 11 23.0856 11H19.2958C18.7927 11 18.3224 11.1258 17.9122 11.35C19.6841 12.0008 21.0403 13.5102 21.4778 15.375H24.5458H24.5403ZM21.6255 9.25C21.9702 9.25 22.3116 9.1821 22.63 9.05018C22.9485 8.91827 23.2379 8.72491 23.4816 8.48116C23.7254 8.2374 23.9188 7.94802 24.0507 7.62954C24.1826 7.31106 24.2505 6.96972 24.2505 6.625C24.2505 6.28028 24.1826 5.93894 24.0507 5.62046C23.9188 5.30198 23.7254 5.0126 23.4816 4.76884C23.2379 4.52509 22.9485 4.33173 22.63 4.19982C22.3116 4.0679 21.9702 4 21.6255 4C21.2808 4 20.9394 4.0679 20.6209 4.19982C20.3025 4.33173 20.0131 4.52509 19.7693 4.76884C19.5256 5.0126 19.3322 5.30198 19.2003 5.62046C19.0684 5.93894 19.0005 6.28028 19.0005 6.625C19.0005 6.96972 19.0684 7.31106 19.2003 7.62954C19.3322 7.94802 19.5256 8.2374 19.7693 8.48116C20.0131 8.72491 20.3025 8.91827 20.6209 9.05018C20.9394 9.1821 21.2808 9.25 21.6255 9.25Z" fill="#175DA8"></path>
                        </svg>Testimonials
                        </a>
                    </li>
                    <li class="nav-item m-3 left-side-links">
                        <a class="nav-link side-link <?= ($current == 'plans.php') ? ' active' : '' ?>"
                            href="applied_jobs.php">
                            <img src=../images/codicon_git-stash-apply_blue.svg width="22" height="22"
                                style="margin: 0 4px 4px 0;"> Applied Jobs
                        </a>
                    </li>

                    <div class="notify-btn text-end position-relative ms-auto mt-2 mt-md-0 " onclick="toggleDropdown()">

                        <img src="/images/bell.svg" alt="bell" style="height: 33px;">
                        <span id="notification-count"
                            class="notification-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger me-4"
                            style="font-size: 4px; top: 12px !important;left: 26px !important;color: #dc3545;"
                            onclick="toggleDropdown()">.</span>
                        <div id="notificationDropdown" class="dropdown-content position-absolute bg-white shadow p-3"
                            style="display: none; width: 328px; right: 0; top: 50px; border-radius: 8px; text-align: start; font-size: 13px;">
                            <h4 class="notification-header text-primary fw-bold p-2">Notifications (0
                                <?php //echo $selected_count; ?>)
                            </h4>
                            <ul class="list-unstyled mb-0">
                                <?php if ($selected_count > 0): ?>
                                    <?php
                                    // Display only the first 4 notifications
                                    $counter = 0;
                                    foreach ($selected_candidates as $candidate):
                                        if ($counter >= 3)
                                            break;
                                        $counter++;
                                        ?>
                                        <li class="py-2 notification-list">
                                            You Referred a Candidate Secured a job - <strong>4
                                                <?php //echo ucfirst($candidate['name']); ?>
                                            </strong>
                                            <br>
                                            <span class="notification-date">
                                                <?php echo date('M d, Y, h:i:s A', strtotime($candidate['updated_at'])); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="py-2 text-center">No Data Found</li>
                                <?php endif; ?>
                            </ul>
                            <!-- See All button -->
                            <div class="text-center mt-3">
                                <a href="notification_list.php" class="text-primary fw-bold text-decoration-none">See
                                    All Notification</a>
                            </div>
                        </div>
                    </div>
                    <div id="credits-container"></div>
                </ul>







                <?php if ($isLoggedIn): ?>
                    <div class="profile-wrapper" onclick="toggleProfileDropdown()">
                        <ul>
                            <li class="nav-item dropdown d-flex align-items-center me-2" style="    margin-bottom: -18px;">
                                <p class="mb-0 me-2">Hello, User
                                </p>
                                <div class="profile-circle" style="background-color: <?php echo $dynamicColor; ?>">
                                    <?php echo $firstLetter; ?>
                                </div>


                                <div id="profileDropdown" class="profile-dropdown">
                                    <ul>
                                        <li><a href="candidate_profile.php"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M7.935 9.8432C5.07807 10.7252 3 13.3885 3 16.5333C3 16.6571 3.04917 16.7758 3.13668 16.8633C3.2242 16.9508 3.3429 17 3.46667 17C3.59043 17 3.70913 16.9508 3.79665 16.8633C3.88417 16.7758 3.93333 16.6571 3.93333 16.5333C3.93333 13.185 6.65167 10.4667 10 10.4667C13.3483 10.4667 16.0667 13.185 16.0667 16.5333C16.0667 16.6571 16.1158 16.7758 16.2034 16.8633C16.2909 16.9508 16.4096 17 16.5333 17C16.6571 17 16.7758 16.9508 16.8633 16.8633C16.9508 16.7758 17 16.6571 17 16.5333C17 13.3885 14.9219 10.7252 12.065 9.8432C12.5782 9.5028 12.9991 9.04066 13.2902 8.49802C13.5813 7.95539 13.7335 7.34912 13.7333 6.73333C13.7333 4.673 12.0603 3 10 3C7.93967 3 6.26667 4.673 6.26667 6.73333C6.26648 7.34912 6.41873 7.95539 6.70984 8.49802C7.00094 9.04066 7.42185 9.5028 7.935 9.8432ZM10 3.93333C11.5451 3.93333 12.8 5.1882 12.8 6.73333C12.8 8.27847 11.5451 9.53333 10 9.53333C8.45487 9.53333 7.2 8.27847 7.2 6.73333C7.2 5.1882 8.45487 3.93333 10 3.93333Z"
                                                        fill="#175DA8" stroke="#175DA8" stroke-width="0.5" />
                                                </svg> My Profile</a></li>
                                        <li><a href="applied_jobs.php"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                    height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M9.85876 1.95776L14.2631 6.36206L14.3344 6.43237L14.2631 6.50269L13.3793 7.38843L13.309 7.45874L10.1 4.24976V6.35034H8.64978V4.24976L5.51306 7.38843L5.44275 7.45874L5.37244 7.38843L4.48865 6.50269L4.41736 6.43237L4.48865 6.36206L8.89294 1.95776H9.85876Z"
                                                        fill="#175DA8" stroke="#175DA8" stroke-width="0.2" />
                                                    <path
                                                        d="M2.59961 17.4004H16.1504V12.5996H13.3896C13.0724 13.2452 12.6064 13.8043 12.0293 14.2295L11.7676 14.4082C11.0517 14.8612 10.2221 15.1016 9.375 15.1016C8.63376 15.1016 7.90583 14.9178 7.25586 14.5684L6.98242 14.4082C6.28455 13.9666 5.7242 13.3401 5.36035 12.5996H2.59961V17.4004ZM10.0996 9.90039V11.3496H8.65039V9.90039H10.0996ZM10.0996 7.40039V8.84961H8.65039V7.40039H10.0996ZM17.5996 18.166L16.916 18.8496H1.83398L1.15039 18.166V11.834L1.83398 11.1504H6.39453L6.41016 11.2305C6.54906 11.9142 6.92074 12.5292 7.46094 12.9707C8.00115 13.4121 8.67738 13.6533 9.375 13.6533C10.0726 13.6533 10.7489 13.4121 11.2891 12.9707L11.4844 12.7969C11.919 12.3741 12.2183 11.8286 12.3398 11.2305L12.3555 11.1504H16.916L17.5996 11.834V18.166Z"
                                                        fill="#175DA8" stroke="#175DA8" stroke-width="0.2" />
                                                </svg> Applied Jobs</a></li>
                                        <li><a href="notification_list.php"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M11.876 1.81785C11.3327 1.6742 10.7777 1.9971 10.634 2.54044L10.4782 3.12982C8.11719 2.99165 5.90455 4.52828 5.27636 6.90423L5.0702 7.68393C4.70173 9.07758 3.868 10.3055 2.71196 11.1625L2.1028 11.6156C1.86632 11.7895 1.7522 12.0845 1.80763 12.3718C1.86305 12.659 2.07789 12.8899 2.3603 12.9646L14.6391 16.211C14.9215 16.2857 15.2224 16.1912 15.4125 15.9689C15.6027 15.7466 15.6493 15.4337 15.5297 15.1656L15.2232 14.4737C14.6426 13.1543 14.5248 11.6748 14.8932 10.2811L15.0994 9.50142C15.7276 7.12547 14.5636 4.69606 12.4428 3.64926L12.5986 3.05988C12.7423 2.51654 12.4194 1.96151 11.876 1.81785ZM11.0969 4.76476C12.997 5.26716 14.1283 7.2117 13.6259 9.11184L13.4198 9.89155C13.031 11.3619 13.0787 12.9083 13.546 14.3456L4.23255 11.8831C5.34918 10.8646 6.15489 9.5439 6.54366 8.07351L6.74981 7.29381C7.25221 5.39366 9.19675 4.26237 11.0969 4.76476ZM10.2046 16.0895L6.27537 15.0507C6.1374 15.5725 6.21077 16.1273 6.48174 16.593C6.75271 17.0588 7.19869 17.3967 7.72054 17.5347C8.24239 17.6727 8.79713 17.5993 9.26289 17.3283C9.72865 17.0574 10.0666 16.6114 10.2046 16.0895Z"
                                                        fill="#175DA8" />
                                                </svg> Notification</a></li>
                                        <li><a href="../contact_us.php"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                    height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M9.99996 17.5C9.76385 17.5 9.56579 17.42 9.40579 17.26C9.24579 17.1 9.16607 16.9022 9.16663 16.6667C9.16663 16.4306 9.24663 16.2325 9.40663 16.0725C9.56663 15.9125 9.76441 15.8328 9.99996 15.8333H15.8333V9.91667C15.8333 9.11111 15.6805 8.35417 15.375 7.64583C15.0694 6.9375 14.6527 6.31945 14.125 5.79167C13.5972 5.26389 12.9791 4.84722 12.2708 4.54167C11.5625 4.23611 10.8055 4.08333 9.99996 4.08333C9.19441 4.08333 8.43746 4.23611 7.72913 4.54167C7.02079 4.84722 6.40274 5.26389 5.87496 5.79167C5.34718 6.31945 4.93052 6.9375 4.62496 7.64583C4.3194 8.35417 4.16663 9.11111 4.16663 9.91667V14.1667C4.16663 14.4028 4.08691 14.6008 3.92746 14.7608C3.76802 14.9208 3.56996 15.0006 3.33329 15C2.87496 15 2.48246 14.8367 2.15579 14.51C1.82913 14.1833 1.66607 13.7911 1.66663 13.3333V11.6667C1.66663 11.3472 1.74302 11.0661 1.89579 10.8233C2.04857 10.5806 2.24996 10.3825 2.49996 10.2292L2.56246 9.125C2.68746 8.11111 2.97579 7.19445 3.42746 6.375C3.87913 5.55556 4.44163 4.86111 5.11496 4.29167C5.78829 3.72222 6.54524 3.28111 7.38579 2.96833C8.22635 2.65556 9.09774 2.49945 9.99996 2.5C10.9166 2.5 11.7952 2.65639 12.6358 2.96917C13.4764 3.28195 14.2297 3.72639 14.8958 4.3025C15.5625 4.87861 16.1216 5.57306 16.5733 6.38583C17.025 7.19861 17.313 8.10472 17.4375 9.10417L17.5 10.1875C17.75 10.3125 17.9513 10.4967 18.1041 10.74C18.2569 10.9833 18.3333 11.2506 18.3333 11.5417V13.4583C18.3333 13.7639 18.2569 14.0347 18.1041 14.2708C17.9513 14.5069 17.75 14.6875 17.5 14.8125V15.8333C17.5 16.2917 17.3366 16.6842 17.01 17.0108C16.6833 17.3375 16.2911 17.5006 15.8333 17.5H9.99996Z"
                                                        fill="#175DA8" />
                                                </svg> Support</a></li>
                                        <li><a href="logout.php"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                    height="20" viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M9.16663 5.83333L7.99996 7L10.1666 9.16667H1.66663V10.8333H10.1666L7.99996 13L9.16663 14.1667L13.3333 10L9.16663 5.83333ZM16.6666 15.8333H9.99996V17.5H16.6666C17.5833 17.5 18.3333 16.75 18.3333 15.8333V4.16667C18.3333 3.25 17.5833 2.5 16.6666 2.5H9.99996V4.16667H16.6666V15.8333Z"
                                                        fill="#FF3333" />
                                                </svg> Logout</a></li>
                                    </ul>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>

        </div>


        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleProfileDropdown() {
            var dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            var dropdown = document.getElementById('profileDropdown');
            var profileWrapper = document.querySelector('.profile-wrapper');
            if (!profileWrapper.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('dropdownMenuLink');
            const profileDropdown = document.getElementById('custom-dropdown');

            profileToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileDropdown.contains(e.target) && !profileToggle.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        });
    </script>

</body>

</html>