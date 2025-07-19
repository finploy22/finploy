<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userOtp = $_POST['otp'] ?? '';
    
    if (isset($_SESSION['verifyotp']) && $userOtp == $_SESSION['verifyotp']) {
        // OTP matched
        echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
    } else {
        // OTP mismatch
        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
