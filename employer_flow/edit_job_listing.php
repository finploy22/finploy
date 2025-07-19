<?php
// Database connection
include '../db/connection.php';

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection


// Ensure the required session variables exist
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => "Required session variables are not set."]));
}

$employer_name = $_SESSION['name'];
$employer_mobile = $_SESSION['mobile'];

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Set headers for JSON response
header('Content-Type: application/json');

// Check if form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 0) Grab & sanitize job_id if present
    $job_id = isset($_POST['job_id']) && is_numeric($_POST['job_id'])
        ? (int) $_POST['job_id']
        : null;

    if ($job_id) {
        // —————————————————————————
        // UPDATE existing row
        // —————————————————————————
        // Collect all the same form fields as you do for INSERT
        $jobrole                    = $_POST['jobrole'] ?? '';
        $department                 = $_POST['department'] ?? '';
        $companyname                = $_POST['companyname'] ?? '';
        $location                   = $_POST['location'] ?? '';
        $salary                     = ($_POST['salary_min'] ?? '') . "-" . ($_POST['salary_max'] ?? '');
        $age                        = ($_POST['age_min']    ?? '') . "-" . ($_POST['age_max']    ?? '');
        $gender                     = $_POST['gender'] ?? '';
        $experience                 = $_POST['experience'] ?? '';
        $product                    = $_POST['product'] ?? '';
        $role_overview              = $_POST['role_overview'] ?? '';
        $key_responsibilities       = $_POST['key_responsibilities'] ?? '';
        $job_requirements           = $_POST['job_requirements'] ?? '';
        $job_status                 = $_POST['job_status'] ?? '';
        $education                  = $_POST['education'] ?? '';
        $no_of_positions            = $_POST['no_of_positions'] ?? '';
        $sub_department             = $_POST['sub_department'] ?? '';
        $sub_product                = $_POST['sub_product'] ?? '';
        $specialization             = $_POST['specialization'] ?? '';
        $domain_relevant_experience = $_POST['domain_relevant_experience'] ?? '';
        $contact_person_name        = $_POST['contact_person_name'] ?? '';
        $contact_person_designation = $_POST['contact_person_designation'] ?? '';
        $contact_mobile_no          = $_POST['contact_mobile_no'] ?? '';
        $email_id                   = $_POST['email_id'] ?? '';
        $category                   = $_POST['category'] ?? '';
        $employer_mobile            = $_POST['employer_mobile_no'] ?? '';

        // Build UPDATE statement
        $sql = "
          UPDATE job_id
          SET
            jobrole                     = ?,
            department                  = ?,
            companyname                 = ?,
            location                    = ?,
            salary                      = ?,
            age                         = ?,
            gender                      = ?,
            experience                  = ?,
            product                     = ?,
            role_overview               = ?,
            key_responsibilities        = ?,
            job_requirements            = ?,
            job_status                  = ?,
            education                   = ?,
            no_of_positions             = ?,
            sub_department              = ?,
            sub_product                 = ?,
            specialization              = ?,
            domain_relevant_experience  = ?,
            contact_person_name         = ?,
            contact_person_designation  = ?,
            contact_mobile_no           = ?,
            email_id                    = ?,
            category                    = ?,
            employer_mobile_no          = ?
          WHERE id = ?
          LIMIT 1
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }

        // Bind 1 string per field + 1 integer for job_id
        $stmt->bind_param(
            "sssssssssssssssssssssssssi",
            $jobrole,
            $department,
            $companyname,
            $location,
            $salary,
            $age,
            $gender,
            $experience,
            $product,
            $role_overview,
            $key_responsibilities,
            $job_requirements,
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
            $job_id
        );

        // Execute UPDATE
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Job updated successfully',
                'job_id'  => $job_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Execution error: ' . $stmt->error
            ]);
        }

        $stmt->close();
        $conn->close();
        exit;
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
 
?>
