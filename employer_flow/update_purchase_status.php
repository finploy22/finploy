<?php
// update_status.php
include '../db/connection.php';
session_start();


// Check that the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = isset($_POST['payment_id']) ? (int) $_POST['payment_id'] : 0;
    
    if ($payment_id > 0 && isset($_SESSION['employer_id'])) {
        $employer_id = (int) $_SESSION['employer_id'];
        $expire_seconds = 172800; // 2 days

        // Fetch the payment record to verify its created_at time and status
        $stmt = $conn->prepare("SELECT created_at FROM payments WHERE id = ? AND employer_id = ? AND status = 'completed'");
        $stmt->bind_param("ii", $payment_id, $employer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($payment = $result->fetch_assoc()) {
            $created_timestamp = strtotime($payment['created_at']);
            
            if (($created_timestamp + $expire_seconds) <= time()) {
                // Payment expired: update buyed_status to 1
                $update_stmt = $conn->prepare("UPDATE payments SET buyed_status = 1 WHERE id = ?");
                $update_stmt->bind_param("i", $payment_id);
                if ($update_stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "Payment status updated to expired."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to update payment status."]);
                }
            } else {
                // Payment is still valid: you might want to update buyed_status to 0 or do nothing
                $update_stmt = $conn->prepare("UPDATE payments SET buyed_status = 0 WHERE id = ?");
                $update_stmt->bind_param("i", $payment_id);
                $update_stmt->execute();
                echo json_encode(["success" => true, "message" => "Payment status remains active."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Payment record not found."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid payment id or session not set."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
