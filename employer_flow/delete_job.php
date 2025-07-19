<?php
// delete_job.php

include '../db/connection.php';
header('Content-Type: application/json');

// 1. Check that connection is live
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// 2. Validate input
if (!isset($_POST['job_id']) || !is_numeric($_POST['job_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or missing job ID'
    ]);
    exit;
}

$jobId = (int) $_POST['job_id'];

// 3. Prepare and execute DELETE
$stmt = $conn->prepare("DELETE FROM `job_id` WHERE `id` = ?");
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param('i', $jobId);

if (!$stmt->execute()) {
    echo json_encode([
        'success' => false,
        'message' => 'Execute failed: ' . $stmt->error
    ]);
    $stmt->close();
    $conn->close();
    exit;
}

// 4. Check result
if ($stmt->affected_rows > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Job deleted successfully'
    ]);
} else {
    // no rows matched that ID
    echo json_encode([
        'success' => false,
        'message' => 'No job found with ID ' . $jobId
    ]);
}

$stmt->close();
$conn->close();
