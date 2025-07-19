<?php
session_start();

function sendOtp($number, $username = 'finployOTP', $apiKey = '1B658-E1335', $sender = 'FNPLOY', $templateid = '1207174190832742849') {
    // Generate OTP
    $otp = rand(1000, 9999);
    $_SESSION['verifyotp'] = $otp; 

    // Message details
    $apiRequest = 'Text';
    $apiRoute = 'OTP';
    $message = "OTP: {$otp} To Verify your contact on DEEESHA Finance. Regards Team DEEESHA. IW9Q1U/xNXQ";

    // Prepare data for POST request
    $data = [
        'username' => $username,
        'apikey' => $apiKey,
        'apirequest' => $apiRequest,
        'route' => $apiRoute,
        'mobile' => $number,
        'sender' => $sender,
        'TemplateID' => $templateid,
        'message' => $message
    ];

    // Build the URL query string
    $url = 'http://www.alots.in/sms-panel/api/http/index.php?' . http_build_query($data);

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['success' => false, 'error' => "Failed to send OTP. cURL Error: $error"];
    }

    curl_close($ch);
    return ['success' => true, 'otp' => $otp, 'response' => $response];
}

echo $mobile = 6379328342;
// Example usage
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mobile) {
    echo 'entered';
    //$number = $_POST['mobile'] ?? null;
    $number = $mobile;

    if ($number) {
        $result = sendOtp($number);

        if ($result['success']) {
            echo "<script>alert('OTP sent successfully! OTP: {$result['otp']}');</script>";
            echo "<script>window.location.replace('forget_password_associate.php?ass_show_otp_varify=true&user_id={$number}');</script>";
        } else {
            echo "<script>alert('Failed to send OTP: {$result['error']}');</script>";
            echo "<script>window.location.replace('index.php');</script>";
        }
    } else {
        echo "<script>alert('Mobile number is required.');</script>";
        echo "<script>window.location.replace('index.php');</script>";
    }
}
?>
