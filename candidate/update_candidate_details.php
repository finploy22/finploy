<?php
session_start();
include '../db/connection.php';

// Get mobile number
$mobile_number = $_POST['mobile_number'] ?? '';
if (empty($mobile_number)) {
    die(json_encode(["status" => "error", "message" => "Mobile number is missing."]));
}

// Fetch user_id
$query = "SELECT user_id FROM candidates WHERE mobile_number = '$mobile_number'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die(json_encode(["status" => "error", "message" => "User not found."]));
}
$user_id = mysqli_fetch_assoc($result)['user_id'];

// Get POST data
$age = $_POST['age'] ?? '';
$gender = $_POST['gender'] ?? '';
$employed = $_POST['employed'] ?? '';


$sales_experience = $_POST['bankExperience'] ?? '';

$current_location = $_POST['current_location'] ?? '';


$products = $_POST['products'] ?? '';
$sub_products = $_POST['sub_products'] ?? '';
$specialization = $_POST['specialization'] ?? '';
$departments = $_POST['departments'] ?? '';
$sub_departments = $_POST['sub_departments'] ?? '';
$category = $_POST['category'] ?? '';

$products_array = $_POST['products_array'] ?? '';
$sub_products_array = $_POST['sub_products_array'] ?? '';
$specialization_array = $_POST['specialization_array'] ?? '';
$departments_array = $_POST['departments_array'] ?? '';
$sub_departments_array = $_POST['sub_departments_array'] ?? '';
$category_array = $_POST['category_array'] ?? '';

// Upload resume
$resume_path = '';
$target_dir = "../uploads/resumes/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (!empty($_FILES['resume']['name'])) {
    $file_extension = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);
    $new_filename = "Finploy_" . $username . "-" . $mobile_number . "-" . date("Y-m-d-H-i-s") . "." . $file_extension;
    $resume_path = $target_dir . $new_filename;

    if (!move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_path)) {
        die(json_encode(["status" => "error", "message" => "Failed to upload resume."]));
    }
}

// Update query
$update_sql = "UPDATE `candidate_details` SET
  

    `gender` = '$gender',
    `employed` = '$employed',
    

    `sales_experience` = '$sales_experience',
  
    `location_code` = '$current_location',
 
    " . (!empty($resume_path) ? "`resume` = '$resume_path'," : "") . "
  
   
    `modified` = NOW(),
    `products` = '$products',
    `sub_products` = '$sub_products',
    `departments` = '$departments',
    `sub_departments` = '$sub_departments',
    `specialization` = '$specialization',
    `category` = '$category',
    `products_array` = '$products_array',
    `sub_products_array` = '$sub_products_array',
    `specialization_array` = '$specialization_array',
    `departments_array` = '$departments_array',
    `sub_departments_array` = '$sub_departments_array',
    `category_array` = '$category_array',
    `age` = '$age'
WHERE `user_id` = '$user_id'";

if (mysqli_query($conn, $update_sql)) {
    echo json_encode(["status" => "success", "message" => "Candidate details updated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Database update failed: " . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
