<?php
session_start();
include '../db/connection.php'; // Include your database connection file

echo 'id:'.$mobile = $_SESSION['mobile'];

$getUserId = "SELECT user_id FROM candidates WHERE mobile_number = ?";
$stmt = $conn->prepare($getUserId);
$stmt->bind_param("s", $mobile);
$stmt->execute();
$resultId = $stmt->get_result();

while ($row = $resultId->fetch_assoc()) {
    echo 'User id : '.$userId = $row['user_id'];
}

// Step 1: Fetch applied jobs for the logged-in candidate
$sqlAppliedJobs = "SELECT job_id FROM jobs_applied WHERE candidate_id = ?";
$stmt = $conn->prepare($sqlAppliedJobs);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$jobIds = [];
while ($row = $result->fetch_assoc()) {
    $jobIds[] = $row['job_id'];
}

if (!empty($jobIds)) {
    // Convert array to comma-separated values for SQL query
    $jobIdsStr = implode(",", $jobIds);

    // Step 2: Fetch job details from job_id table
    $sqlJobs = "SELECT id, jobrole, department, companyname, location, salary, age, gender, experience, product, role_overview 
                FROM job_id WHERE id IN ($jobIdsStr)";
    $resultJobs = $conn->query($sqlJobs);

    // Step 3: Display job listings
    while ($row = $resultJobs->fetch_assoc()) {
        $jobId = $row['id'];
        $initials = strtoupper(substr($row['companyname'], 0, 2)); // Get company initials
        echo '<div class="job-card mb-3" id="job-listing-web" data-id="' . $jobId . '">
                <div class="job-grid" data-id="' . $jobId . '">
                    <div class="col-2 job-logo">
                        <span class="company-initials">' . $initials . '</span>
                    </div>
                    <div class="col-9 job-description">
                        <h5 class="job-role">' . htmlspecialchars($row['jobrole']) . '</h5>
                        <div class="job-info mb-2">
                            <span class="requirement-details"><strong>Age:</strong> ' . htmlspecialchars($row['age']) . ' yrs</span> 
                            <span class="devider-line">|</span>
                            <span class="requirement-details"><strong>Gender:</strong> ' . htmlspecialchars($row['gender']) . '</span> 
                            <span class="devider-line">|</span>
                            <span class="requirement-details"><strong>Experience:</strong> ' . htmlspecialchars($row['experience']) . ' years</span>
                        </div>
                        <p class="job-overview">' . htmlspecialchars($row['role_overview']) . '</p>
                    </div>
                </div>
              </div>';
    }
} else {
    echo "<p>No jobs found.</p>";
}

$conn->close();
?>
