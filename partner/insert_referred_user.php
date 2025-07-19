<?php
session_start(); // Start the session to get session variables
ini_set('display_errors', 1);  // Enable the display of errors
ini_set('display_startup_errors', 1);  // Enable errors during PHP startup
error_reporting(E_ALL);  

header('Content-Type: text/plain');
include '../whatsapp_integration/whatsapp_messages.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $userlocation = isset($_POST['location']) ? trim($_POST['location']) : '';
    $jobid = isset($_POST['jobid']) ? trim($_POST['jobid']) : '';

    // Basic validation
    if (empty($mobile) || empty($name) || empty($userlocation)) {
        echo "All fields are required.";
        exit;
    }
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "Invalid mobile number.";
        exit;
    }

    include '../db/connection.php';

    // Get mobile_number from session
    $session_mobile = isset($_SESSION['mobile']) ? $_SESSION['mobile'] : '';

    if (empty($session_mobile)) {
        echo "Session mobile number is not set.";
        exit;
    }

    // Query to get associate details from associate table using session's mobile number
    $associate_stmt = $conn->prepare("SELECT associate_id, username, mobile_number FROM associate WHERE mobile_number = ?");
    $associate_stmt->bind_param("s", $session_mobile);
    $associate_stmt->execute();
    $associate_stmt->store_result();

    if ($associate_stmt->num_rows > 0) {
        $associate_stmt->bind_result($associate_id, $associate_name, $associate_mobile);
        $associate_stmt->fetch();
    } else {
        echo "No associate found for the provided session mobile number.";
        $associate_stmt->close();
        $conn->close();
        exit;
    }

    $associate_stmt->close();
    
    // Get Job Details 
    $sql = "SELECT jobrole, location, salary, companyname FROM job_id WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $jobid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if job exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Store values in separate variables
        $jobrole = $row['jobrole'];
        $location = $row['location'];
        $salary = $row['salary'];
        $companyname = $row['companyname'];
    } else {
        echo "Job not found.";
    }
    

    // Check if user already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM candidates WHERE mobile_number = ?");
    $check_stmt->bind_param("s", $mobile);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "User Already Exists";
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();

    // Insert query for candidate details
   $stmt = $conn->prepare("INSERT INTO candidates (username, mobile_number, current_location, associate_id, associate_name, associate_mobile, jobrole, companyname, location, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $name, $mobile, $userlocation, $associate_id, $associate_name, $associate_mobile, $jobrole, $companyname, $location, $salary);


    if ($stmt->execute()) {
        $response = sendMessageToCandidate(
            '91' . $mobile,      // WhatsApp expects full phone number with country code
            $templateName,
            $name,               // Candidate Name
            $associate_name,     // Partner/Referrer Name
            $location            // Location
        );
        // echo "<pre>";
        // print_r($response);
        // echo "</pre>";
        $toPartner = sendMessageToPartner(
            '91' . $mobile,      // WhatsApp expects full phone number with country code
            $templateName,
            $name,               // Candidate Name
            $associate_name,     // Partner/Referrer Name
            $location            // Location
        );
        //  echo "<pre>";
        //     print_r($toPartner);
        //     echo "</pre>";
        echo "success";
    } else {
        echo "Failed to insert data.";
    }

    // Close resources
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
