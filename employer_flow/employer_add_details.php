<?php
session_start();
include '../db/connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form fields
    $name          = trim($_POST['name']);
    $mobile        = trim($_POST['mobile']);
    $gender        = trim($_POST['gender']);
    $company_name  = trim($_POST['company_name']);
    $document_name = trim($_POST['document_name']);
    

    // if (empty($name) || empty($mobile) || empty($gender) || empty($company_name) || empty($document_name)) {
    //     echo "All fields are required.";
    //     exit;
    // }

    // Initialize variable to store the final file name/path
    $storedFileName = '';
    // Handle file upload if a file is provided
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] == 0) {
        $fileTmpPath = $_FILES['upload_file']['tmp_name'];
        $fileName = $_FILES['upload_file']['name'];
        $fileSize = $_FILES['upload_file']['size']; // Get file size
        $fileType = $_FILES['upload_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Define allowed file extensions
        $allowedfileExtensions = array('pdf', 'doc', 'docx');

        // File size limit (5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes

        if (!in_array($fileExtension, $allowedfileExtensions)) {
            echo "Upload failed. Only PDF and DOC/DOCX files are allowed.";
            exit;
        }

        if ($fileSize > $maxFileSize) {
            echo "Upload failed. File size must be less than 5MB.";
            exit;
        }

        // Sanitize the document name to be used as the file name
        $docNameSanitized = preg_replace('/[^A-Za-z0-9_\-]/', '_', $name);
        // Create a unique file name based on the document name
      date_default_timezone_set('Asia/Kolkata'); // Set timezone to India
      $timestamp = date('d-m-Y_H-i-s'); 
       $newFileName = "Finploy_" . $docNameSanitized . '_' . $mobile . '_' . $timestamp . '.' . $fileExtension;
        // Directory where the file will be stored
        $uploadFileDir = 'employer_uploads/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // File uploaded successfully.
            $storedFileName = $newFileName; // Store file name for database
        } else {
            echo "There was an error moving the uploaded file.";
            exit;
        }
    }
    // else {
    //     echo "No file uploaded or there was an upload error.";
    //     exit;
    // }

    // Insert data into your database
    $insertEmployerSql = "INSERT INTO employer_add_details (employer_name, employer_mobile_number, employer_gender, employer_company_name, employer_document_name, employer_uploaded_file) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertEmployerSql);
    $stmt->bind_param("ssssss", $name, $mobile, $gender, $company_name, $document_name, $storedFileName);

    if ($stmt->execute()) {
        echo "success"; // This response is handled in AJAX
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
