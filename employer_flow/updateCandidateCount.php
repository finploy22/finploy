<?php
session_start();

if (isset($_POST['candidate_count'])) {
    // Cast the value to an integer and save it
    $_SESSION['accessed_candidate_count'] = (int) $_POST['candidate_count'];
    error_log("Updated accessed_candidate_count to " . $_SESSION['accessed_candidate_count']);
    // Optional: force the session data to be written immediately
    session_write_close();
    echo json_encode(['status' => 'success']);
} else {
    error_log("No candidate_count provided in POST");
    echo json_encode(['status' => 'error', 'message' => 'No candidate count provided.']);
}
?>
