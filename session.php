<?php
// check session is there or not
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function is_logged_in() {
    if (!empty($_SESSION['name']) && !empty($_SESSION['mobile']) && !empty($_SESSION['user_type'])) {
        return $_SESSION['user_type'];
    }
    return false; 
}
function get_user_type() {
    return $_SESSION['user_type'] ?? '';
}

function get_username() {
    return $_SESSION['username'] ?? '';
}
?>
