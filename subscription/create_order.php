<?php
require 'vendor/autoload.php';
use Razorpay\Api\Api;

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

include '../db/connection.php';

// Set Timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

// Razorpay API Keys
$api_key = "rzp_test_rtxMNYHT7oPGHY";
$api_secret = "xcixK7EM1bdFjDerQ1KflEFm";
// $api_key = "rzp_live_ceAIhcdU5YtpSc";
// $api_secret = "zINV6CvjdM52pHH8CpwKyymJ";
$api = new Api($api_key, $api_secret);

// Read JSON data
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    die(json_encode(['error' => 'No JSON data received']));
}

$amount = $data['amount'] * 100; // Convert to paise
$subPlanName = $data['subPlanName'];
$subPlanCode = $data['subPlanCode'];
$planCode = $data['planCode'];
$jobPostingCredit = $data['jobPostingCredit'];
$profileAccessCredit = $data['profileAccess'];


$employer_name =$data['name'];
$employer_mobile = $data['mobileNumber'];



// Get Employer Details from Session
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    die(json_encode(['error' => 'Employer details missing in session']));
}



// Get Employer ID from DB
$query = "SELECT id FROM employers WHERE mobile_number = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(['error' => 'Prepare failed: ' . $conn->error]));
}
$stmt->bind_param("s", $employer_mobile);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die(json_encode(['error' => 'Employer not found']));
}
$row = $result->fetch_assoc();
$employer_id = $row['id'];
$stmt->close();

// Create Order in Razorpay
try {
    $order = $api->order->create([
        'amount' => $amount,
        'currency' => 'INR',
        'payment_capture' => 1
    ]);
    $razorpay_order_id = $order['id'];

    // Get Current Timestamp in Asia/Kolkata
    date_default_timezone_set("Asia/Kolkata");
    $created_at = date("Y-m-d H:i:s");

$status = 'pending';
$expired = 0;

$insert_query = "INSERT INTO subscription_payments 
    (employer_id, employer_name, employer_mobile, plan, sub_plan, amount, razorpay_order_id, status, 
     total_profile_credits, profile_credits_available, 
     total_jobpost_credits, jobpost_credits_available, 
     created, expired) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($insert_query);
if (!$stmt) {
    die(json_encode(['error' => 'Prepare failed: ' . $conn->error]));
}


    // Bind parameters correctly (12 values)
        $stmt->bind_param("issssssssssssi", 
            $employer_id, 
            $employer_name, 
            $employer_mobile, 
            $planCode, 
            $subPlanCode, 
            $data['amount'], 
            $razorpay_order_id, 
            $status, 
            $profileAccessCredit, 
            $profileAccessCredit, 
            $jobPostingCredit, 
            $jobPostingCredit, 
            $created_at, 
            $expired
        );


    if ($stmt->execute()) {
        echo json_encode(['id' => $razorpay_order_id, 'amount' => $amount, 'created' => $created_at]);
    } else {
        die(json_encode(['error' => 'Insert failed: ' . $stmt->error]));
    }


    $stmt->close();
} catch (Exception $e) {
    die(json_encode(['error' => 'Razorpay error: ' . $e->getMessage()]));
}

$conn->close();
?>