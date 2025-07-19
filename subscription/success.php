<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db/connection.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $razorpay_order_id = $_POST['razorpay_order_id'] ?? null;
    $razorpay_payment_id = $_POST['razorpay_payment_id'] ?? null; // Get payment ID from POST

    if (!$razorpay_order_id || !$razorpay_payment_id) {
        die("Invalid payment data received!");
    }

    // Debug connection
    if (!$conn) {
        die("Database connection error: " . mysqli_connect_error());
    }

    // Update status to 'success' and insert the Razorpay Payment ID
    $sql = "UPDATE subscription_payments 
            SET status = 'success', razorpay_payment_id = ?  
            WHERE razorpay_order_id = ? AND status = 'pending'";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $razorpay_payment_id, $razorpay_order_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Get the updated_at, sub_plan and expires_at values
        $sql = "SELECT updated_at, sub_plan, expires_at FROM subscription_payments 
                WHERE razorpay_order_id = ? AND status = 'success'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $razorpay_order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $updated_at = $row['updated_at'];
            $sub_plan = $row['sub_plan'];

            // Determine the subscription period based on the plan
            $days = 0;
            switch ($sub_plan) {
                case '1M':
                    $days = 30;
                    break;
                case '1MS':
                    $days = 30;
                    break;
                case '3M':
                    $days = 90;
                    break;
                case '6M':
                    $days = 180;
                    break;
                case 'PAYG':
                    $days = 90;
                    break;
                default:
                    die("Unknown subscription plan: $sub_plan");
            }

            // Ensure updated_at is valid
            if (!$updated_at || !strtotime($updated_at)) {
                die("Invalid 'updated_at' value.");
            }

            // Calculate the new expires_at date (add the expire period)
            $new_expiration_date = date('Y-m-d H:i:s', strtotime($updated_at . " +$days days"));

            // Update expires_at field
            $sql = "UPDATE subscription_payments 
                    SET expires_at = ?
                    WHERE razorpay_order_id = ? AND status = 'success'";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare statement failed: " . $conn->error);
            }

            $stmt->bind_param("ss", $new_expiration_date, $razorpay_order_id);
            if ($stmt->execute()) {
                echo "Payment successful and expiration date updated!";
            } else {
                die("Error updating expiration date: " . $stmt->error);
            }
        } else {
            die("No data found for the provided order ID.");
        }
    } else {
        die("Error updating payment status: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request method!");
}
?>