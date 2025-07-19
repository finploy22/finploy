<?php
// Database connection
include '../db/connection.php';
header('Content-Type: application/json');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success'=>false, 'message'=>"Connection failed: ".$conn->connect_error]);
    exit;
}

// echo "<pre>";
// print_r($_POST);
// exit;


// GET by id – unchanged
if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM job_id WHERE id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($job = $result->fetch_assoc()) {
        echo json_encode(['success'=>true,'data'=>$job]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Job not found']);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// POST → Update by job_id (now including mobile)
if ($_SERVER['REQUEST_METHOD']==='POST') {
    // require job_id
    if (!isset($_POST['job_id']) || !is_numeric($_POST['job_id'])) {
        echo json_encode(['success'=>false,'message'=>'Job ID is required']);
        exit;
    }
    $id = intval($_POST['job_id']);

    // collect inputs
    $jobrole    = $_POST['jobrole']    ?? '';
    $dept       = $_POST['department'] ?? '';
    $company    = $_POST['companyname']?? '';
    $location   = $_POST['location']   ?? '';
    $salary     = ($_POST['salary_min'] ?? '').'-'.($_POST['salary_max'] ?? '');
    $age        = ($_POST['age_min']    ?? '').'-'.($_POST['age_max'] ?? '');
    $gender     = $_POST['gender']     ?? '';
    $experience = $_POST['experience'] ?? '';
    $product    = $_POST['product']    ?? '';
    $overview   = $_POST['role_overview']       ?? '';
    $keyResp    = $_POST['key_responsibilities']?? '';
    $jobReq     = $_POST['job_requirements']    ?? '';
    $timestamp  = date('Y-m-d H:i:s');
    $status     = $_POST['job_status']          ?? '';
    $education  = $_POST['education']           ?? '';
    $positions  = $_POST['no_of_positions']     ?? '';
    $subDept    = $_POST['sub_department']      ?? '';
    $subProd    = $_POST['sub_product']         ?? '';
    $spec       = $_POST['specialization']      ?? '';
    $domainExp  = $_POST['domain_relevant_experience'] ?? '';
    $contactName= $_POST['contact_person_name'] ?? '';
    $contactDesg= $_POST['contact_person_designation'] ?? '';
    $contactMob = $_POST['contact_mobile_no']   ?? '';
    $contactEmail = $_POST['email_id']          ?? '';
    $category   = $_POST['category']            ?? '';



     $city_name = '';
        if (!empty($location)) {
            $stmt = $conn->prepare("SELECT city FROM locations WHERE id = ?");
            $stmt->bind_param("i", $location);
            $stmt->execute();
            $stmt->bind_result($city_name);
            $stmt->fetch();
            $stmt->close();
        }
         


    // now 25 string columns + 1 int id
    $sql = "UPDATE job_id SET
                jobrole                     = ?,
                department                  = ?,
                companyname                 = ?,
                location                    = ?,
                location_code               = ?,
                salary                      = ?,
                age                         = ?,
                gender                      = ?,
                experience                  = ?,
                product                     = ?,
                role_overview               = ?,
                key_responsibilities        = ?,
                job_requirements            = ?,
                created                     = ?,
                job_status                  = ?,
                education                   = ?,
                no_of_positions             = ?,
                sub_department              = ?,
                sub_product                 = ?,
                specialization              = ?,
                domain_relevant_experience  = ?,
                contact_person_name         = ?,
                contact_person_designation  = ?,
                contact_mobile_no           = ?,   /* ← added */
                email_id                    = ?,
                category                    = ?
                WHERE id = ?";

    $stmt = $conn->prepare($sql);
 $types = 'ssssssssssssssssssssssssssi'; // 26 's' + 1 'i'



    $stmt->bind_param(
        $types,
        $jobrole, $dept, $company,$city_name, $location, $salary,
        $age, $gender, $experience, $product, $overview,
        $keyResp, $jobReq, $timestamp, $status, $education,
        $positions, $subDept, $subProd, $spec, $domainExp,
        $contactName, $contactDesg, $contactMob,  /* ← bind mobile here */
        $contactEmail, $category,
        $id
    );

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success'=>true,'message'=>'Job updated successfully']);
        } else {
            echo json_encode([
                'success'=>false,
                'message'=>'No job found with that ID or no changes made'
            ]);
        }
    } else {
        echo json_encode([
            'success'=>false,
            'message'=>'Update failed: '.$stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Method not allowed
http_response_code(405);
echo json_encode(['success'=>false,'message'=>'Invalid request method']);
