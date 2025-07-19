<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Function to check login status and return user type
function is_logged_in() {
    if (!empty($_SESSION['name']) && !empty($_SESSION['mobile']) && !empty($_SESSION['user_type'])) {
        return $_SESSION['user_type'];
    }
    return false; 
}
// Get user type from session
$user_type = is_logged_in();
// Redirect based on user type
if ($user_type == 'candidate') {
    header("Location: /candidate/landingpage.php");
    exit;
} elseif ($user_type == 'employer') {
    header("Location: /employer_flow/employer.php");
    exit;
} elseif ($user_type == 'partner') {
    header("Location: /partner/index.php");
    exit;
}
?>
