<?php
include '../db/connection.php';
session_start();

// Make sure you have the candidate's ID (e.g., from a POST variable or other logic)
$candidateId = isset($_POST['candidate_id']) ? (int) $_POST['candidate_id'] : 0;

// Get the employer ID from the session (adjust if your session variable is named differently)
$employerId  = isset($_SESSION['employer_id']) ? (int) $_SESSION['employer_id'] : 0;

if ($candidateId === 0 || $employerId === 0) {
    // Exit if candidate or employer id is missing
    die("Invalid candidate or employer id.");
}


// 1. Fetch the candidate name from candidate_details table
$candidateQuery = "SELECT username FROM candidate_details WHERE id = ?";
$candidateStmt  = $conn->prepare($candidateQuery);
$candidateStmt->bind_param("i", $candidateId);
$candidateStmt->execute();
$candidateResult = $candidateStmt->get_result();

if ($candidateResult->num_rows > 0) {
    $candidateRow = $candidateResult->fetch_assoc();
    $candidateName = $candidateRow['username'];
} else {
    $candidateName = "Unknown Candidate";
}
$candidateStmt->close();

// 2. Fetch the employer name from payments table
// In this example, we fetch the latest payment record for the employer
$paymentQuery = "SELECT employer_username FROM payments WHERE employer_id = ? ORDER BY created_at DESC LIMIT 1";
$paymentStmt  = $conn->prepare($paymentQuery);
$paymentStmt->bind_param("i", $employerId);
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();

if ($paymentResult->num_rows > 0) {
    $paymentRow = $paymentResult->fetch_assoc();
    $employerName = $paymentRow['employer_username'];
} else {
    $employerName = "Unknown Employer";
}
$paymentStmt->close();

// 3. Build the dynamic notification message
$message = "Employer {$employerName} has selected candidate {$candidateName}.";

// 4. Insert the notification into the notifications table
$insertQuery = "INSERT INTO notifications (employer_id, candidate_id, message, created_at) VALUES (?, ?, ?, NOW())";
$insertStmt  = $conn->prepare($insertQuery);
$insertStmt->bind_param("iis", $employerId, $candidateId, $message);

if ($insertStmt->execute()) {
    // You can return a JSON response or simply continue processing
    echo json_encode([
        'success'   => true,
        'message'   => 'Notification inserted successfully.',
        'data'      => [
            'employer_name' => $employerName,
            'candidate_name'=> $candidateName,
            'notification_message' => $message
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Error inserting notification: " . $conn->error
    ]);
}

$insertStmt->close();
$conn->close();
?>
