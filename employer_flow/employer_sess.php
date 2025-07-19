<?php
include '../db/connection.php';
session_start();

// Debug: display the session variables
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Ensure the required session variables exist
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    die("Required session variables are not set.");
}

$employer_name = $_SESSION['name'];
$employer_mobile = $_SESSION['mobile'];

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT `id`, `username`, `mobile_number`, `password`, `created` 
                        FROM `employers` 
                        WHERE mobile_number = ? 
                        LIMIT 1");
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}

$stmt->bind_param("s", $employer_mobile);
$stmt->execute();

// Get the result and check if an employer was found
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Store the employer id in the session
    $_SESSION['employer_id'] = $row['id'];
    echo "Employer ID stored in session: " . $_SESSION['employer_id'];
} else {
    echo "No employers found.";
}

// Display updated session for debugging
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Clean up
$stmt->close();
$conn->close();
?>
