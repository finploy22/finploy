<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['name']);
$loggedInName = $isLoggedIn ? $_SESSION['name'] : null;

// Generate the first letter of the name (uppercase)
$firstLetter = $isLoggedIn ? strtoupper($loggedInName[0]) : null;

// Generate a dynamic color based on the name (hashing logic)
$dynamicColor = $isLoggedIn ? '#' . substr(md5($loggedInName), 0, 6) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Finploy-Partner</title>
 <link rel="icon" type="image/png" href="/images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
            color: #25D366; /* WhatsApp Green */
        }
        .desktop-navbar .contact-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .refer-cand-btn{
                font-size: 13px !important;
            }
          

        /* Mobile styles */
        @media (max-width: 768px) {
            .desktop-navbar {
                display: none;
            }
            .refer-cand-btn{
                font-size: 12px !important;
            }
            
            .mobile-navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .notify-btn{
                border-radius: 12px;
                padding: 1px;
            }  
        }

        @media (min-width: 769px) {
            .mobile-navbar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container">
            <!-- Desktop View -->
            <div class="desktop-navbar w-100">
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="assets/finploy-logo.png" alt="FINPLOY" height="40">
                </a>
                <!-- Contact Info -->
                <div class="contact-info">
                    <a href="https://wa.me/919850469695" class="text-decoration-none">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="tel:9850469695" class="text-decoration-none">
                        <i class="fas fa-phone"></i> Contact Us (9850469695)
                    </a>
                </div>
                <!-- Refer Candidate Button -->
                <!--<a href="#" class="btn btn-success">-->
                <!--    <i class="fas fa-briefcase me-2"></i> Refer Candidate-->
                <!--</a>-->
                <div class="notify-btn text-end position-relative mt-2 mt-md-0" onclick="toggleDropdown()">
                    <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 52 53" fill="none">
                        <g filter="url(#filter0_d_1800_1522)">
                            <rect x="6" y="6" width="40" height="40" rx="8.66071" fill="white"/>
                            <path d="M34.3084 35.8887L34.0099 36.9909L13.9622 31.6746L14.2606 30.5723L17.085 28.9584L18.8756 22.3447C19.8008 18.9277 22.8764 16.518 26.4469 16.4252L26.5335 16.1055C26.6918 15.5209 27.0783 15.0224 27.6079 14.7197C28.1376 14.417 28.7671 14.335 29.3579 14.4917C29.9486 14.6484 30.4523 15.0309 30.7581 15.5551C31.0639 16.0793 31.1468 16.7023 30.9885 17.287L30.902 17.6066C33.9472 19.4538 35.3934 23.0626 34.4683 26.4797L32.6777 33.0934L34.3084 35.8887ZM25.9152 36.0257C25.7569 36.6104 25.3704 37.1089 24.8407 37.4116C24.311 37.7142 23.6816 37.7963 23.0908 37.6396C22.5 37.4829 21.9963 37.1004 21.6905 36.5762C21.3847 36.052 21.3018 35.429 21.4601 34.8443" fill="#175DA8"/>
                        </g>
                        <defs>
                            <filter id="filter0_d_1800_1522" x="0.226191" y="0.947917" width="51.5476" height="51.5476" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                            <feOffset dy="0.721726"/>
                            <feGaussianBlur stdDeviation="2.8869"/>
                            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.24 0"/>
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1800_1522"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1800_1522" result="shape"/>
                            </filter>
                        </defs>
                    </svg>
                    <span id="notification-count" class="notification-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;" onclick="toggleDropdown()">
                        0<?php //echo $selected_count; ?>
                    </span>
                    <div id="notificationDropdown-mobile" class="dropdown-content position-absolute bg-white shadow p-3" style="display: none; width: 328px; right: 0; top: 50px; border-radius: 8px; text-align: start; font-size: 13px;">
                        <h4 class="notification-header text-primary fw-bold p-2">Notifications (0<?php //echo $selected_count; ?>)</h4>
                        <ul class="list-unstyled mb-0">
                            <?php if ($selected_count > 0): ?>
                                <?php 
                                // Display only the first 4 notifications
                                $counter = 0;
                                foreach ($selected_candidates as $candidate): 
                                    if ($counter >= 3) break;
                                    $counter++;
                                ?>
                                    <li class="py-2 notification-list">
                                        You Referred a Candidate Secured a job - <strong>4<?php //echo ucfirst($candidate['name']); ?></strong>
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
                            <a href="notification_list.php" class="text-primary fw-bold text-decoration-none">See All Notification</a>
                        </div>
                    </div>
                </div>

                <?php if ($isLoggedIn): ?>
                    <!-- Show profile circle with dropdown if logged in -->
                        <ul>
                    <li class="nav-item dropdown d-flex align-items-center mt-4 me-2">
                        <p class="mb-0 me-2">Hi, <?php echo ucfirst($loggedInName); ?></p>
                        <div 
                            class="profile-circle" 
                            style="background-color: <?php echo $dynamicColor; ?>;"
                            id="profileDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                            <?php echo strtoupper($firstLetter); ?>
                        </div>
                        <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
                <?php endif; ?>
            </div>

            <!-- Mobile View -->
            <div class="mobile-navbar w-100">
                
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="assets/finploy-logo.png" alt="FINPLOY" height="40">
                </a>
                <!-- Refer Candidate Button -->
                <!--<a href="#" class="refer-cand-btn btn btn-success" id="refer-cand-btn">-->
                <!--    <i class="fas fa-briefcase me-2"></i> Refer Candidate-->
                <!--</a>-->
                <!-- Profile -->
                <div class="notify-btn text-end position-relative mt-2 mt-md-0" onclick="toggleDropdown()">
                    <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 52 53" fill="none">
                        <g filter="url(#filter0_d_1800_1522)">
                            <rect x="6" y="6" width="40" height="40" rx="8.66071" fill="white"/>
                            <path d="M34.3084 35.8887L34.0099 36.9909L13.9622 31.6746L14.2606 30.5723L17.085 28.9584L18.8756 22.3447C19.8008 18.9277 22.8764 16.518 26.4469 16.4252L26.5335 16.1055C26.6918 15.5209 27.0783 15.0224 27.6079 14.7197C28.1376 14.417 28.7671 14.335 29.3579 14.4917C29.9486 14.6484 30.4523 15.0309 30.7581 15.5551C31.0639 16.0793 31.1468 16.7023 30.9885 17.287L30.902 17.6066C33.9472 19.4538 35.3934 23.0626 34.4683 26.4797L32.6777 33.0934L34.3084 35.8887ZM25.9152 36.0257C25.7569 36.6104 25.3704 37.1089 24.8407 37.4116C24.311 37.7142 23.6816 37.7963 23.0908 37.6396C22.5 37.4829 21.9963 37.1004 21.6905 36.5762C21.3847 36.052 21.3018 35.429 21.4601 34.8443" fill="#175DA8"/>
                        </g>
                        <defs>
                            <filter id="filter0_d_1800_1522" x="0.226191" y="0.947917" width="51.5476" height="51.5476" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                            <feOffset dy="0.721726"/>
                            <feGaussianBlur stdDeviation="2.8869"/>
                            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.24 0"/>
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1800_1522"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1800_1522" result="shape"/>
                            </filter>
                        </defs>
                    </svg>
                    <span id="notification-count" class="notification-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;" onclick="toggleDropdown()">
                        0<?php //echo $selected_count; ?>
                    </span>
                    <div id="notificationDropdown" class="dropdown-content position-absolute bg-white shadow p-3" style="display: none; width: 328px; right: 0; top: 50px; border-radius: 8px; text-align: start; font-size: 13px;">
                        <h4 class="notification-header text-primary fw-bold p-2">Notifications (0<?php //echo $selected_count; ?>)</h4>
                        <ul class="list-unstyled mb-0">
                            <?php if ($selected_count > 0): ?>
                                <?php 
                                // Display only the first 4 notifications
                                $counter = 0;
                                foreach ($selected_candidates as $candidate): 
                                    if ($counter >= 3) break;
                                    $counter++;
                                ?>
                                    <li class="py-2 notification-list">
                                        You Referred a Candidate Secured a job - <strong>4<?php //echo ucfirst($candidate['name']); ?></strong>
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
                            <a href="notification_list.php" class="text-primary fw-bold text-decoration-none">See All Notification</a>
                        </div>
                    </div>
                </div>
                <?php if ($isLoggedIn): ?>
                    <!-- Show profile circle with dropdown if logged in -->
                        <ul style="padding: 0 !important;">
                    <li class="nav-item dropdown d-flex align-items-center mt-3">
                        <div 
                            class="profile-circle" 
                            style="background-color: <?php echo $dynamicColor; ?>;"
                            id="profileDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                            <?php echo strtoupper($firstLetter); ?>
                        </div>
                        <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/index.js"></script>
</body>
</html>
