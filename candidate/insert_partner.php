<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include '../db/connection.php'; // Ensure this file correctly establishes a connection

function generateDefaultPassword(): string
{
    // fin@ + zero‑padded 4‑digit random number (0000‑9999)
    return 'fin@' . str_pad(strval(mt_rand(0, 9999)), 4, '0', STR_PAD_LEFT);
}

// Validate session
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    die(json_encode(["status" => "error", "message" => "User not logged in"]));
}

$username = $_SESSION['name'];
$mobile_number = $_SESSION['mobile'];
$job_id = $_POST['job_id'] ?? '';

if (empty($job_id)) {
    die(json_encode(["status" => "error", "message" => "Job ID is required"]));
}

if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . mysqli_connect_error()]));
}

// Check if user exists in 'associate'
$check_query = "SELECT associate_id FROM `associate` WHERE `mobile_number` = ?";
$stmt = mysqli_prepare($conn, $check_query);

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Query preparation failed: " . mysqli_error($conn)]));
}

mysqli_stmt_bind_param($stmt, "s", $mobile_number);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $associate_id);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt); // Close statement before inserting

if ($associate_id) {
    echo json_encode(["status" => "exist", "message" => "User already exists, skipping insertion"]);
    exit;
}

// Generate unique link
$encoded_link = base64_encode($mobile_number);
$unique_link = "https://finploy.co.uk/candidate/mobile=" . $encoded_link;
$password  = generateDefaultPassword();
// Insert user
$insert_query = "INSERT INTO `associate` (`username`, `mobile_number`, `password`, `unique_link`) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $insert_query);

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Insert query preparation failed: " . mysqli_error($conn)]));
}

mysqli_stmt_bind_param($stmt, "ssss", $username, $mobile_number, $password, $unique_link);
$execute = mysqli_stmt_execute($stmt);

if ($execute) {
    echo json_encode(["status" => "success", "message" => "Application submitted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to insert data: " . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
