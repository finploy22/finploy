<?php
include '../db/connection.php';

// Query to fetch unique locations from the job_id table
$query = "SELECT DISTINCT location FROM job_id WHERE location IS NOT NULL";
$result = $conn->query($query);

$locations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['location'];
    }
}

// Return locations as a JSON response
echo json_encode($locations);
?>
