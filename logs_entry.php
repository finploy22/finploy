<?php
// logs_entry.php

include 'db/connection.php';

// Set timezone to IST (India Standard Time)
date_default_timezone_set('Asia/Kolkata');

// Get POST data
$user_type = $_POST['userType'] ?? '';
$mobile_number = $_POST['mobile'] ?? '';
$user_name = $_POST['name'] ?? '';
$india_time = date("Y-m-d H:i:s"); 

// Prepare and execute SQL
$stmt = $conn->prepare("INSERT INTO logs_entry (date_time, user_type, mobile_number, user_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $india_time, $user_type, $mobile_number, $user_name);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
