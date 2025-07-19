<?php
session_start();
include '../db/connection.php';
date_default_timezone_set('Asia/Kolkata');
$now = date('Y-m-d H:i:s');

// Get mobile number
$mobile_number = $_POST['mobile_number'] ?? '';
if (empty($mobile_number)) {
    die(json_encode(["status" => "error", "message" => "Mobile number is missing."]));
}

// Get step number
$step = $_POST['step'] ?? '';
if (empty($step)) {
    die(json_encode(["status" => "error", "message" => "Step is missing."]));
}

// Fetch user_id
$query = "SELECT user_id FROM candidates WHERE mobile_number = '$mobile_number'";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die(json_encode(["status" => "error", "message" => "User not found."]));
}
$user_id = mysqli_fetch_assoc($result)['user_id'];

// Check if record exists
$checkQuery = "SELECT id FROM candidate_details WHERE mobile_number = '$mobile_number'";
$checkResult = mysqli_query($conn, $checkQuery);
$exists = ($checkResult && mysqli_num_rows($checkResult) > 0);






// STEP-WISE UPDATE/INSERT LOGIC
switch ($step) {
    case '1':
        $username = $_POST['username'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $age = $_POST['age'] ?? '';
        if ($exists) {
            $update = "UPDATE candidate_details SET username='$username', gender='$gender', age='$age', modified='$now' WHERE mobile_number='$mobile_number'";
            mysqli_query($conn, $update);
        } else {
            $insert = "INSERT INTO candidate_details (user_id, username, mobile_number, gender, age, created, modified) VALUES ('$user_id', '$username', '$mobile_number', '$gender', '$age', '$now', '$now')";
            mysqli_query($conn, $insert);
        }
        break;

    case '2':
        $employed = $_POST['employed'] ?? '';
        $current_company = $_POST['current_company'] ?? '';
        $designation = $_POST['designation'] ?? '';
        $sales_experience = $_POST['bankExperience'] ?? '';
        $update = "UPDATE candidate_details SET employed='$employed', current_company='$current_company', destination='$designation', sales_experience='$sales_experience', modified='$now' WHERE mobile_number='$mobile_number'";
        mysqli_query($conn, $update);
        break;

    case '3':
        
        $products_array = $_POST['products_array'] ?? '';
$sub_products_array = $_POST['sub_products_array'] ?? '';
$specialization_array = $_POST['specialization_array'] ?? '';
        
        $products = $_POST['products'] ?? '';
        $sub_products = $_POST['sub_products'] ?? '';
        $specialization = $_POST['specialization'] ?? '';
       $update = "UPDATE candidate_details SET 
    products='$products', sub_products='$sub_products', specialization='$specialization',
    products_array='$products_array', sub_products_array='$sub_products_array', specialization_array='$specialization_array',
    modified='$now' 
WHERE mobile_number='$mobile_number'";
        mysqli_query($conn, $update);
        break;

    case '4':
        
        $departments_array = $_POST['departments_array'] ?? '';
$sub_departments_array = $_POST['sub_departments_array'] ?? '';
$category_array = $_POST['category_array'] ?? '';
        $departments = $_POST['departments'] ?? '';
        $sub_departments = $_POST['sub_departments'] ?? '';
        $category = $_POST['category'] ?? '';
       $update = "UPDATE candidate_details SET 
    departments='$departments', sub_departments='$sub_departments', category='$category',
    departments_array='$departments_array', sub_departments_array='$sub_departments_array', category_array='$category_array',
    modified='$now' 
WHERE mobile_number='$mobile_number'";
        mysqli_query($conn, $update);
        break;

    case '5':
        $work_experience = $_POST['work_experience'] ?? '';
        $current_location = $_POST['current_location'] ?? '';
        $current_salary = $_POST['current_salary'] ?? '';
        $resume_path = '';
        $target_dir = "../uploads/resumes/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        if (!empty($_FILES['resume']['name'])) {
            $file_extension = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);
            $new_filename = "Finploy_" . $mobile_number . "-" . date("YmdHis") . "." . $file_extension;
            $resume_path = $target_dir . $new_filename;
            move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_path);
        }
        
        
        
        
         $city_name = '';
    if (!empty($current_location)) {
        $stmt = $conn->prepare("SELECT city FROM locations WHERE id = ?");
        $stmt->bind_param("i", $current_location);
        $stmt->execute();
        $stmt->bind_result($city_name);
        $stmt->fetch();
        $stmt->close();
    }
        
        
        
        
        $update = "UPDATE candidate_details SET work_experience='$work_experience',current_location='$city_name', location_code='$current_location', current_salary='$current_salary', resume='$new_filename', modified='$now' WHERE mobile_number='$mobile_number'";
        mysqli_query($conn, $update);
        break;

    default:
        die(json_encode(["status" => "error", "message" => "Invalid step."]));
}

// Success response
echo json_encode(["status" => "success", "message" => "Step $step processed successfully."]);
mysqli_close($conn);
?>
