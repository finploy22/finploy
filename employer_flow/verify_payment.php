<?php
include '../db/connection.php';
session_start();
require 'razorpay-php/Razorpay.php';
use Razorpay\Api\Api;
$api = new Api('rzp_test_rtxMNYHT7oPGHY', 'xcixK7EM1bdFjDerQ1KflEFm');

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo $employer_mobile = $_SESSION['mobile'] ?? '';
// Initialize Razorpay API
// $api = new Api('rzp_live_ceAIhcdU5YtpSc', 'zINV6CvjdM52pHH8CpwKyymJ');
$date = new DateTime("now", new DateTimeZone("Asia/Kolkata"));
$istTime = $date->format("Y-m-d H:i:s");

function logPaymentFailure($conn, $failureData) {
    //  global $employer_mobile;
    $check_existing_sql = "SELECT COUNT(*) as count FROM payments WHERE razorpay_order_id = ? AND status = 'failed'";
    $check_stmt = $conn->prepare($check_existing_sql);
    $check_stmt->bind_param("s", $failureData['order_id']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $existing_record = $result->fetch_assoc();

    if ($existing_record['count'] == 0) {
        $sql = "INSERT INTO payments (
            razorpay_payment_id, 
            razorpay_order_id, 
            amount, 
            status, 
            error_code, 
            error_description, 
            error_source, 
            error_step, 
            error_reason,
            payment_method,
            employer_id,
            employer_username,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param(
            "ssdsssssssiss", 
            $failureData['payment_id'], 
            $failureData['order_id'], 
            $failureData['amount'], 
            $failureData['status'], 
            $failureData['error_code'], 
            $failureData['error_description'], 
            $failureData['error_source'], 
            $failureData['error_step'], 
            $failureData['error_reason'],
            $failureData['payment_method'],
            $_SESSION['employer_id'],
            $_SESSION['name'],
            $GLOBALS['istTime'] // Use global variable if needed
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert payment failure record: " . $stmt->error);
        }
        return true;
    }
    return false;
}

function logPaymentSuccess($conn, $paymentData) {
    $conn->begin_transaction();
    global $employer_mobile;

    try {
        // Insert payment record
        $sql = "INSERT INTO payments (
            razorpay_payment_id, 
            razorpay_order_id, 
            razorpay_signature,
            amount, 
            status,
            payment_method,
            employer_id,
            employer_username,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param(
            "sssdssiss",
            $paymentData['payment_id'], 
            $paymentData['order_id'], 
            $paymentData['signature'], 
            $paymentData['amount'], 
            $paymentData['status'],
            $paymentData['payment_method'],
            $_SESSION['employer_id'], 
            $_SESSION['name'],
            $GLOBALS['istTime']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert payment record: " . $stmt->error);
        }
        
        // Get the last inserted payment ID
        $payment_id = $conn->insert_id;
        
        // Get cart items from database
        $cart_query = "SELECT candidate_id, price FROM user_cart WHERE user_id = ?";
        $cart_stmt = $conn->prepare($cart_query);
        if (!$cart_stmt) {
            throw new Exception("Cart query prepare failed: " . $conn->error);
        }
        $cart_stmt->bind_param("i", $_SESSION['employer_id']);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        
        // Calculate expiration date (90 days from purchase time)
        $expires_at = (new DateTime($GLOBALS['istTime']))->add(new DateInterval('P90D'))->format('Y-m-d H:i:s');

        // Updated SQL for order_items
        $order_item_sql = "INSERT INTO order_items (payment_id, candidate_id, candidate_name, candidate_mobile, price, employer_id, employer_username, employer_mobile, purchased, created_at, expires_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        $order_item_stmt = $conn->prepare($order_item_sql);
        if (!$order_item_stmt) {
            throw new Exception("Order item prepare failed: " . $conn->error);
        }

        while ($cart_item = $cart_result->fetch_assoc()) {
            $candidate_id = $cart_item['candidate_id'];

            // Fetch candidate details
            $candidate_query = "SELECT username, mobile_number FROM candidate_details WHERE id = ?";
            $candidate_stmt = $conn->prepare($candidate_query);
            if (!$candidate_stmt) {
                throw new Exception("Candidate details query prepare failed: " . $conn->error);
            }
            $candidate_stmt->bind_param("i", $candidate_id);
            $candidate_stmt->execute();
            $candidate_result = $candidate_stmt->get_result();
            $candidate_data = $candidate_result->fetch_assoc();

            if (!$candidate_data) {
                throw new Exception("Candidate details not found for candidate_id: " . $candidate_id);
            }

            $candidate_name = $candidate_data['username'];
            $candidate_mobile = $candidate_data['mobile_number'];

            // Bind values and insert into order_items
            $order_item_stmt->bind_param(
                "iissdissss", 
                $payment_id, 
                $candidate_id, 
                $candidate_name, 
                $candidate_mobile,
                $cart_item['price'],
                $_SESSION['employer_id'],
                $_SESSION['name'],
                $employer_mobile,
                $GLOBALS['istTime'],
                $expires_at
            );

            if (!$order_item_stmt->execute()) {
                throw new Exception("Failed to insert order item: " . $order_item_stmt->error);
            }
        }
        
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Payment logging error: " . $e->getMessage());
        return false;
    }
}

// Process payment verification for successful payments
if (isset($_POST['razorpay_payment_id']) && isset($_POST['razorpay_order_id']) && isset($_POST['razorpay_signature'])) {
    try {
        // Get payment method from Razorpay API
        $payment = $api->payment->fetch($_POST['razorpay_payment_id']);
        $payment_method = $payment->method; // This could be 'card', 'upi', 'netbanking', etc.
        
        $paymentData = [
            'payment_id' => $_POST['razorpay_payment_id'],
            'order_id' => $_POST['razorpay_order_id'],
            'signature' => $_POST['razorpay_signature'],
            'amount' => $_SESSION['payment_amount'] ?? 0,
            'status' => 'completed',
            'payment_method' => $payment_method
        ];
        
        // Verify payment signature
        $attributes = [
            'razorpay_payment_id' => $paymentData['payment_id'],
            'razorpay_order_id' => $paymentData['order_id'],
            'razorpay_signature' => $paymentData['signature']
        ];
        
        $api->utility->verifyPaymentSignature($attributes);
        
        // Log successful payment and order items
        if (logPaymentSuccess($conn, $paymentData)) {
            // Clear cart and payment session
            unset($_SESSION['cart']);
            unset($_SESSION['payment_amount']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Payment verified and order processed successfully',
                'payment_method' => $payment_method
            ]);
        } else {
            throw new Exception('Failed to log payment and order items');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Payment verification failed: ' . $e->getMessage()
        ]);
    }
}

// Capture payment failure scenarios
if (isset($_POST['error'])) {
    $failureData = [
        'payment_id' => $_POST['error']['payment_id'] ?? null,
        'order_id' => $_POST['error']['order_id'] ?? null,
        'amount' => $_SESSION['payment_amount'] ?? 0,
        'status' => 'failed',
        'error_code' => $_POST['error']['code'] ?? 'UNKNOWN',
        'error_description' => $_POST['error']['description'] ?? 'No description',
        'error_source' => $_POST['error']['source'] ?? 'Unknown',
        'error_step' => $_POST['error']['step'] ?? 'Unknown',
        'error_reason' => $_POST['error']['reason'] ?? 'Unknown',
        'payment_method' => $_POST['error']['payment_method'] ?? 'Unknown'
    ];
    
    if (logPaymentFailure($conn, $failureData)) {
        echo json_encode([
            'success' => false,
            'message' => 'Payment failure logged'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Payment failure already recorded'
        ]);
    }
}

$conn->close();
?>
