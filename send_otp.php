<?php
session_start();
header('Content-Type: application/json');

function sendOtp($number, $username = 'finployOTP', $apiKey = '1B658-E1335', $sender = 'FNPPLY', $templateid = '1207174190832742849') {

    $otp = rand(1000, 9999);
    $_SESSION['verifyotp'] = $otp;

    $apiRequest = 'Text';
    $apiRoute = 'OTP';
    $message = "{$otp} is your OTP code for Finploy Finance Employment.";

    $data = [
        'username'   => $username,
        'apikey'     => $apiKey,
        'apirequest' => $apiRequest,
        'route'      => $apiRoute,
        'mobile'     => $number,
        'sender'     => $sender,
        'TemplateID' => $templateid,
        'message'    => $message
    ];

    $url = 'http://www.alots.in/sms-panel/api/http/index.php?' . http_build_query($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    if ($response === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to connect to OTP API: ' . curl_error($ch)]);
        curl_close($ch);
        exit();
    }

    curl_close($ch);

    // Log the API response to see if it's successful
    file_put_contents('otp_log.txt', "Response: $response\n", FILE_APPEND);

    // Validate the API response
    if (strpos($response, 'success') !== false) {
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully.', 'otp' => $otp]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Check API response.', 'response' => $response]);
    }
}
    
// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = $_POST['mobile'] ?? '';

    if (!preg_match('/^[6-9]\d{9}$/', $number)) {
        echo json_encode(['success' => false, 'message' => 'Invalid mobile number format.']);
        exit();
    }

    sendOtp($number);
}
?>
