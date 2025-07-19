<?php
// Database connection
include '../db/connection.php';
session_start();

if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => "Session expired or not set."]));
}

$employer_name = $_SESSION['name'];
$employer_mobile = $_SESSION['mobile'];
$locations = explode(',', $_POST['location']); 

// Fetch subscription once
$creditQuery = "SELECT id, jobpost_credits_available, profile_credits_available, amount, razorpay_payment_id, sub_plan
                FROM subscription_payments 
                WHERE employer_mobile = ? 
                AND jobpost_credits_available >=1
                AND plan_status = 'ACTIVE' 
                AND status = 'success'
                ORDER BY created ASC LIMIT 1";

$creditStmt = $conn->prepare($creditQuery);
$creditStmt->bind_param("s", $employer_mobile);
$creditStmt->execute();
$creditResult = $creditStmt->get_result();
$subscription = $creditResult->fetch_assoc();
$creditStmt->close();

if (!$subscription || $subscription['jobpost_credits_available'] <= 0) {
    echo json_encode(["status" => "error", "message" => "Insufficient credits"]);
    exit;
}

// Get base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/';

// Input data
$jobrole = $_POST['jobrole'] ?? '';
$department = $_POST['department'] ?? '';
$companyname = $_POST['companyname'] ?? '';
$salary = $_POST['salary_min'] . "-" . $_POST['salary_max'];
$age = $_POST['age_min'] . "-" . $_POST['age_max'];
$gender = $_POST['gender'] ?? '';
$experience = $_POST['experience'] ?? '';
$product = $_POST['product'] ?? '';
$role_overview = $_POST['role_overview'] ?? '';
$job_status = $_POST['job_status'] ?? '';
$education = $_POST['education'] ?? '';
$no_of_positions = $_POST['no_of_positions'] ?? '';
$sub_department = $_POST['sub_department'] ?? '';
$sub_product = $_POST['sub_product'] ?? '';
$specialization = $_POST['specialization'] ?? '';
$domain_relevant_experience = $_POST['domain_relevant_experience'] ?? '';
$contact_person_name = $_POST['contact_person_name'] ?? '';
$contact_person_designation = $_POST['contact_person_designation'] ?? '';
$contact_mobile_no = $_POST['contact_mobile_no'] ?? '';
$email_id = $_POST['email_id'] ?? '';
$category = $_POST['category'] ?? '';
$created = date('Y-m-d H:i:s');
$key_responsibilities = $_POST['key_responsibilities'] ?? '';
$job_requirements = $_POST['job_requirements'] ?? '';

$successJobs = [];

foreach ($locations as $location) {
    $location = intval(trim($location));
    if ($location <= 0) continue;

    // Get city name from location ID
    $city_name = '';
    $stmt = $conn->prepare("SELECT city FROM locations WHERE id = ?");
    $stmt->bind_param("i", $location);
    $stmt->execute();
    $stmt->bind_result($city_name);
    $stmt->fetch();
    $stmt->close();

    try {
        $job_geturl = '';

        $stmt = $conn->prepare("INSERT INTO job_id (
            jobrole, department, companyname, location, location_code, salary, age, gender, 
            experience, product, role_overview, key_responsibilities, 
            job_requirements, created, job_status, education, no_of_positions, 
            sub_department, sub_product, specialization, domain_relevant_experience, 
            contact_person_name, contact_person_designation, contact_mobile_no, 
            email_id, category, employer_mobile_no, job_geturl
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )");

        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        $stmt->bind_param(
            "ssssssssssssssssssssssssssss",
            $jobrole,
            $department,
            $companyname,
            $city_name,
            $location,
            $salary,
            $age,
            $gender,
            $experience,
            $product,
            $role_overview,
            $key_responsibilities,
            $job_requirements,
            $created,
            $job_status,
            $education,
            $no_of_positions,
            $sub_department,
            $sub_product,
            $specialization,
            $domain_relevant_experience,
            $contact_person_name,
            $contact_person_designation,
            $contact_mobile_no,
            $email_id,
            $category,
            $employer_mobile,
            $job_geturl
        );

        if ($stmt->execute()) {
            $inserted_id = $conn->insert_id;
            $job_geturl = $base_url . 'index.php?job_id=' . $inserted_id;

            $update_stmt = $conn->prepare("UPDATE job_id SET job_geturl = ? WHERE id = ?");
            $update_stmt->bind_param("si", $job_geturl, $inserted_id);
            $update_stmt->execute();
            $update_stmt->close();

            $successJobs[] = [
                'job_id' => $inserted_id,
                'location_id' => $location,
                'job_geturl' => $job_geturl
            ];

            // Decrement credit only for successful posts
            $subscription['jobpost_credits_available']--;
        } else {
            throw new Exception("Execution failed: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Update remaining credits
$expired = ($subscription['jobpost_credits_available'] == 0 && $subscription['profile_credits_available'] == 0) ? 1 : 0;
$subscription_id = $subscription['id'];
$updateCreditQuery = "UPDATE subscription_payments SET jobpost_credits_available = ?, expired = ? WHERE id = ?";
$updateStmt = $conn->prepare($updateCreditQuery);
$updateStmt->bind_param("iii", $subscription['jobpost_credits_available'], $expired, $subscription_id);
$updateStmt->execute();
$updateStmt->close();

$conn->close();

echo json_encode([
    'success' => true,
    'message' => 'Jobs posted successfully for all selected locations',
    'jobs' => $successJobs
]);
?>
