<?php
include '../db/connection.php';
// Prevent any output before JSON response
ob_start();

session_start();
header('Content-Type: application/json');

// Make sure the employer is logged in
if (!isset($_SESSION['employer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Employer not logged in']);
    exit;
}



// Retrieve candidates purchased by the current employer
$employer_id = $_SESSION['employer_id'];
$query = "SELECT cd.id AS candidate_id, p.created_at as purchase_date, p.id AS payment_id, p.expired
          FROM candidate_details cd
          INNER JOIN order_items oi ON cd.id = oi.candidate_id
          INNER JOIN payments p ON oi.payment_id = p.id
          WHERE p.employer_id = ?
          AND p.status = 'completed'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();

$candidatesArray = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidatesArray[] = $row;
    }
}

date_default_timezone_set('Asia/Kolkata');
$ACCESS_DURATION = 600; // 10 minutes (in seconds)
$updates = [];

foreach ($candidatesArray as $candidate) {
    // Convert the candidate's purchase date into a Unix timestamp
    $purchaseTime = strtotime($candidate['purchase_date']);
    $currentTime = time();
    
    // Calculate the absolute difference between expiration time and current time
    $timeDifference = ($purchaseTime + $ACCESS_DURATION) - $currentTime;
    
    // Determine the expired value based on time difference
    if ($timeDifference >= 480) {
        $expiredValue = 8;
    } else if ($timeDifference >= 360) {
        $expiredValue = 6;
    } else if ($timeDifference >= 240) {
        $expiredValue = 4;
    } else if ($timeDifference >= 120) {
        $expiredValue = 2;
    } else {
        $expiredValue = 0;
       
    }
    
    // Update the payments table
    $updatePaymentQuery = "UPDATE payments SET expired = ? WHERE id = ? AND employer_id = ?";
    $updateStmt = $conn->prepare($updatePaymentQuery);
    $updateStmt->bind_param("iii", $expiredValue, $candidate['payment_id'], $employer_id);
    $updateStmt->execute();
    
    // Update the order_items table
    $updateOrderItemsQuery = "UPDATE order_items SET expired = ? WHERE payment_id = ? AND candidate_id = ?";
    $updateStmt2 = $conn->prepare($updateOrderItemsQuery);
    $updateStmt2->bind_param("iii", $expiredValue, $candidate['payment_id'], $candidate['candidate_id']);
    $updateStmt2->execute();
   
    // Save update details for response
    $updates[] = [
        'candidate_id' => $candidate['candidate_id'],
        'payment_id' => $candidate['payment_id'],
        'expired' => $expiredValue
    ];
}

// Clear any output buffers before sending JSON response
ob_clean();

// Return the JSON response
echo json_encode(['status' => 'success', 'updates' => $updates]);

$conn->close();
?>