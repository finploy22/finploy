<?php
require 'vendor/autoload.php';
use Razorpay\Api\Api;

include '../db/connection.php';

$api_key = "rzp_test_rtxMNYHT7oPGHY";
$api_secret = "xcixK7EM1bdFjDerQ1KflEFm";
$api = new Api($api_key, $api_secret);

// Read the Razorpay webhook payload
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Validate the Razorpay webhook signature
$provided_signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'];
$expected_signature = hash_hmac('sha256', $payload, $api_secret);

if ($provided_signature !== $expected_signature) {
    die(json_encode(['error' => 'Invalid signature']));
}

// If it's a captured payment event
if ($data['event'] == 'payment.captured') {
    $payment_id = $data['payload']['payment']['entity']['id'];
    $order_id = $data['payload']['payment']['entity']['order_id'];
    $status = $data['payload']['payment']['entity']['status'];

    // Update the payment status in the database
    $query = "UPDATE subscription_payments SET status = '$status', razorpay_payment_id = '$payment_id' WHERE razorpay_order_id = '$order_id'";

    if ($conn->query($query) === TRUE) {
        echo json_encode(['success' => 'Payment status updated']);
    } else {
        echo json_encode(['error' => 'Failed to update status: ' . $conn->error]);
    }
}
$conn->close();
?>
