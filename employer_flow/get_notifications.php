<?php
include '../db/connection.php';
session_start();
header('Content-Type: application/json');

// Ensure the employer is logged in.
if (!isset($_SESSION['employer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized.']);
    exit;
}

$employer_id = $_SESSION['employer_id'];



// Query notifications for this employer.
$query = "SELECT id, message, created_at FROM notifications WHERE employer_id = ? ORDER BY created_at DESC";
$stmt  = $conn->prepare($query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(['status' => 'success', 'notifications' => $notifications]);

$stmt->close();
$conn->close();
?>
