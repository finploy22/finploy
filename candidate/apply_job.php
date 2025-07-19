<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db/connection.php';
header("Content-Type: application/json");
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}
// Validate and sanitize inputs
$mobileNumber = isset($_POST["mobile_number"]) ? trim($_POST["mobile_number"]) : '';
$jobId = isset($_POST["job_id"]) ? intval($_POST["job_id"]) : 0;
if (empty($mobileNumber)) {
    echo json_encode(["status" => "error", "message" => "Mobile number is required."]);
    exit;
}
// Validate job ID
if ($jobId <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid Job ID."]);
    exit;
}
// Fetch candidate_id and partner_id
$sql = "SELECT user_id AS candidate_id, associate_id FROM candidates WHERE mobile_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $mobileNumber);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Candidate not found."]);
    exit;
}
$row = $result->fetch_assoc();
$candidateId = $row["candidate_id"];
$partnerId = $row["associate_id"];
// Check if the user has already applied for this job
$sql = "SELECT id FROM jobs_applied WHERE job_id = ? AND candidate_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $jobId, $candidateId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "You have already applied for this job."]);
    exit;
}
//Get candidate extra details
$sql = "SELECT username, mobile_number, work_experience, current_location, current_salary,location_code
        FROM candidate_details 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $candidateId);
$stmt->execute();
$candidateDetailsResult = $stmt->get_result();
if ($candidateDetailsResult->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Candidate additional details not found."]);
    exit;
}
$candidateData = $candidateDetailsResult->fetch_assoc();
// 4. Get job details (including employer mobile)
$sql = "SELECT jobrole, companyname, location, salary, experience, department, employer_mobile_no 
        FROM job_id 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $jobId);
$stmt->execute();
$jobDetailsResult = $stmt->get_result();
if ($jobDetailsResult->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Job details not found."]);
    exit;
}
$jobData = $jobDetailsResult->fetch_assoc();
//Get employer_id using employer_mobile_no
$employerMobile = $jobData['employer_mobile_no'];

$sql = "SELECT id FROM employers WHERE mobile_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $employerMobile);
$stmt->execute();
$employerResult = $stmt->get_result();
if ($employerResult->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Employer details not found."]);
    exit;
}
$employerRow = $employerResult->fetch_assoc();
$employerId = $employerRow['id'];
//Insert into jobs_applied with all required fields
$sql = "INSERT INTO jobs_applied (
    job_id, candidate_id, partner_id, employer_id, created,
    job_role, company_name, company_location, job_salary, job_experience, job_department,
    candidate_name, candidate_mobile, work_experience, candidate_location, candidate_current_salary
) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iiiisssssssssss",
    $jobId,
    $candidateId,
    $partnerId,
    $employerId,
    $jobData['jobrole'],
    $jobData['companyname'],
    $jobData['location'],
    $jobData['salary'],
    $jobData['experience'],
    $jobData['department'],
    $candidateData['username'],
    $candidateData['mobile_number'],
    $candidateData['work_experience'],
    $candidateData['location_code'],
    $candidateData['current_salary']
);
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Application submitted successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to submit application."]);
}
$stmt->close();
$conn->close();
?>
