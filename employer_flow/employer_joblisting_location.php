<?php
// Database connection
include '../db/connection.php';
session_start();

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Query to get unique locations
$query = "SELECT DISTINCT location FROM job_id ORDER BY location ASC";
$result = $conn->query($query);

if (!$result) {
    die(json_encode(['error' => 'Query failed: ' . $conn->error]));
}

// Prepare response
$locations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['location'];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['locations' => $locations]);

$conn->close();
?>