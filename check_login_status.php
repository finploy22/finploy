<?php
// check_login_status.php

session_start();

// Check if user is logged in by looking for mobile session variable
$response = [
    'loggedIn' => isset($_SESSION['mobile']) && !empty($_SESSION['mobile']),
    'mobile' => $_SESSION['mobile'] ?? '',
    'userType' => $_SESSION['userType'] ?? ''
];
// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;