<?php
session_start();
include 'db/connection.php';

// Check if mobile number is sent via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logintype = $_POST['logintype'];
    $mobile = $_POST['mobile'];

    // Determine the table based on logintype
    $table = '';
    if ($logintype === 'candidate') {
        $table = 'candidates';
    } elseif ($logintype === 'partner') {
        $table = 'associate';
    } elseif ($logintype === 'employer') {
        $table = 'employers';
    } else {
        echo 'Invalid logintype';
        exit;
    }
    

    // Query to check if mobile number exists
    $query = "SELECT * FROM `$table` WHERE mobile_number = '$mobile'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo 'exists';
    } else {
        echo 'not_exists';
    }
}

// Close the database connection
$conn->close();
?>
