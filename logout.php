<?php
session_start(); // Start or resume the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page (or homepage)
header("Location: ../index.php"); // Change to your login page
exit();
?>
