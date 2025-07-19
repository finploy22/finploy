<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Include database connection
include '../db/connection.php';

// Validate and sanitize inputs
$mobileNumber = isset($_POST["mobile_number"]) ? trim($_POST["mobile_number"]) : '';
$jobId = isset($_POST["job_id"]) ? intval($_POST["job_id"]) : 0;

// Validate mobile number
if (empty($mobileNumber)) {
    echo json_encode(["status" => "error", "message" => "Mobile number is required."]);
    exit;
}

// Validate job ID
if ($jobId <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid Job ID."]);
    exit;
}

// Fetch username and password from associates table
$sql = "SELECT username, password FROM associate WHERE mobile_number = '$mobileNumber'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo json_encode(["status" => "error", "message" => "Associate not found."]);
    exit;
}

$associateData = mysqli_fetch_assoc($result);
$username = $associateData["username"];
$password = $associateData["password"];

// Check if candidate already exists in candidates table
$sql = "SELECT user_id AS candidate_id, associate_id FROM candidates WHERE mobile_number = '$mobileNumber'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $candidateId = $row["candidate_id"];
    $partnerId = $row["associate_id"] ?? null;
} else {
    // Insert new candidate into candidates table
    $sql = "INSERT INTO candidates (username, mobile_number, password) VALUES ('$username', '$mobileNumber', '$password')";
    if (mysqli_query($conn, $sql)) {
        $candidateId = mysqli_insert_id($conn);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to insert candidate."]);
        exit;
    }
}

// Fetch associate ID from associates table
$sql = "SELECT associate_id FROM candidates WHERE mobile_number = '$mobileNumber'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$partnerId = $row["associate_id"] ?? null;

// Check if the user has already applied for this job
$sql = "SELECT id FROM jobs_applied WHERE job_id = '$jobId' AND candidate_id = '$candidateId'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo json_encode(["status" => "error", "message" => "You have already applied for this job."]);
    exit;
}

// Insert application into jobs_applied table
$sql = "INSERT INTO jobs_applied (job_id, candidate_id, partner_id, created) VALUES ('$jobId', '$candidateId', '1', NOW())";
if (mysqli_query($conn, $sql)) {
    echo json_encode(["status" => "success", "message" => "Application submitted successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to submit application."]);
}

// Close connection
mysqli_close($conn);
?>
