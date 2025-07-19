<?php
session_start();

if (isset($_POST['candidateCount_download'])) {
    $_SESSION['candidateCount_download'] = (int) $_POST['candidateCount_download'];
    error_log("Updated candidateCount_download to " . $_SESSION['candidateCount_download']);
    session_write_close();
    echo json_encode(['status' => 'success']);
} else {
    error_log("Missing candidateCount_download in POST");
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Missing candidate count value.'
    ]);
}
?>
