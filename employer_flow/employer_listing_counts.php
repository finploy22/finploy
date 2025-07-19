<?php
include '../db/connection.php';
session_start();

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Check if session variables are set
if (!isset($_SESSION['mobile'])) {
    die(json_encode(['error' => 'Required session variables are not set.']));
}

$employer_mobile = $_SESSION['mobile'];

// Prepare query with parameter placeholder
$query = "SELECT job_status, COUNT(*) as count 
          FROM job_id 
          WHERE employer_mobile_no = ? 
          GROUP BY job_status";

// Prepare statement
$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(['error' => 'Query preparation failed: ' . $conn->error]));
}

// Bind parameter
$stmt->bind_param("s", $employer_mobile);

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// Initialize counts
$active_count = 0;
$inactive_count = 0;
$expired_count = 0;
$total_count = 0;

// Process results
while ($row = $result->fetch_assoc()) {
    $status = $row['job_status'];
    $count = (int)$row['count'];
    
    switch ($status) {
        case 'active':
            $active_count = $count;
            break;
        case 'inactive':
            $inactive_count = $count;
            break;
        case 'expired':
            $expired_count = $count;
            break;
    }
    
    $total_count += $count;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'active' => $active_count,
    'inactive' => $inactive_count,
    'expired' => $expired_count,
    'total' => $total_count
]);

$stmt->close();
$conn->close();
?>