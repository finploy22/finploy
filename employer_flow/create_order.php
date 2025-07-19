<?php
include '../db/connection.php';
session_start();
require 'razorpay-php/Razorpay.php';
use Razorpay\Api\Api;

$api = new Api('rzp_test_rtxMNYHT7oPGHY', 'xcixK7EM1bdFjDerQ1KflEFm');
// $api = new Api('rzp_live_ceAIhcdU5YtpSc', 'zINV6CvjdM52pHH8CpwKyymJ');

try {
    $amount = $_POST['amount'];
    
    // Store amount in session for later reference
    $_SESSION['payment_amount'] = $amount / 100;  // Convert paise to rupees

    $orderData = [
        'receipt' => 'rcpt_' . time(),
        'amount' => $amount,
        'currency' => 'INR',
        'notes' => [
            'employer_id' => $_SESSION['employer_id'] ?? null,
            'candidates' => count($_SESSION['cart'] ?? [])
        ]
    ];

    $order = $api->order->create($orderData);

    echo json_encode([
        'success' => true,
        'order_id' => $order->id,
        'amount' => $amount
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>