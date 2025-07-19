<?php
include '../db/connection.php';

$jobId = $_POST['job_id'] ?? '';
$feedback = $_POST['feedback'] ?? '';
$actionType = $_POST['action_type'] ?? '';

if (!$jobId || !$actionType) {
    echo "Missing job ID or action type";
    exit;
}

if ($actionType == 'reject') {
    // Set reject_status = 1, shortlist_status = 1, and update reason
    $stmt = $conn->prepare("UPDATE jobs_applied SET reject_status = 1,  reject_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $feedback, $jobId);
    $stmt->execute();
    echo "Rejected with reason.";

} elseif ($actionType == 'shortlist') {
    // Set shortlist_status = 1, reset reject_status, clear reason
    $stmt = $conn->prepare("UPDATE jobs_applied SET shortlist_status = 1, reject_reason = NULL WHERE id = ?");
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    echo "Shortlisted.";

} elseif ($actionType == 'modify') {
    // Reset shortlist_status to 0 only; keep reject_reason and reject_status unchanged
    $stmt = $conn->prepare("UPDATE jobs_applied SET shortlist_status = 0 , reject_status = 0 WHERE id = ?");
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    echo "Modified successfully.";

} else {
    echo "Invalid action type.";
}
?>
