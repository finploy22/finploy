<?php
session_start();
include '../db/connection.php';

if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    die();
}
$isLoggedIn = isset($_SESSION['name']);
$candidate_name = $isLoggedIn ? $_SESSION['name'] : null;
$candidate_mobile = $isLoggedIn ? $_SESSION['mobile'] : null;
$firstLetter = $isLoggedIn ? strtoupper($candidate_name[0]) : null;
$dynamicColor = $isLoggedIn ? '#' . substr(md5($candidate_name), 0, 6) : null;

$query = "SELECT candidate_image FROM candidate_details WHERE username='$candidate_name' AND mobile_number='$candidate_mobile'";
$result = mysqli_query($conn, $query);

$candidateImage = '';
if ($row = mysqli_fetch_assoc($result)) {
    $candidateImage = $row['candidate_image'];
}

if ($candidate_name && $candidate_mobile) {
    $stmt = $conn->prepare("SELECT gender,current_company,employed,destination,work_experience,current_location,current_salary,resume,hl_lap,personal_loan,gold_loan,business_loan,casa,credit_cards,Sales,HR_Training,Operations,Credit_dept,sales_experience  FROM candidate_details WHERE username = ? AND mobile_number = ?");
    $stmt->bind_param("ss", $candidate_name, $candidate_mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $candidate_gender = $row['gender'];
        $candidate_company = $row['current_company'];
        $candidate_employed = $row['employed'];
        $candidate_destination = $row['destination'];
        $candidate_work_expereince = $row['work_experience'];
        $candidate_bank = $row['sales_experience'];
        $candidate_current_location = $row['current_location'];
        $candidate_current_salary = $row['current_salary'];
        $candidate_resume = $row['resume'];
        $hl_lap = $row['hl_lap'];
        $personal_loan = $row['personal_loan'];
        $gold_loan = $row['gold_loan'];
        $business_loan = $row['business_loan'];
        $credit_cards = $row['credit_cards'];
        $casa = $row['casa'];
        $business_loan = $row['business_loan'];
        $sales = $row['Sales'];
        $hr = $row['HR_Training'];
        $operations = $row['Operations'];
        $credit = $row['Credit_dept'];
    } else {
        $candidate_gender = null;
        $candidate_company = null;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_update'])) {
    $updatedName = trim($_POST['username']);
    $updatedMobile = preg_replace('/[^0-9]/', '', $_POST['mobile_number']);
    $updatedGender = trim($_POST['gender']);
    $updatedCompany = trim($_POST['current_company']);

    $safeName = $conn->real_escape_string($updatedName);
    $safeMobile = $conn->real_escape_string($updatedMobile);
    $safeGender = $conn->real_escape_string($updatedGender);
    $safeCompany = $conn->real_escape_string($updatedCompany);
    $passwordQuery = "";
    if (!empty($_POST['password']) && !empty($_POST['repassword'])) {
        $updatedPassword = $_POST['password'];
        $rePassword = $_POST['repassword'];
        if ($updatedPassword !== $rePassword) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
            exit;
        }
        $safePassword = $conn->real_escape_string($updatedPassword);
        $passwordQuery = ", password='$safePassword'";
    }
    // Update employers table
    $updateEmployers = "UPDATE candidates 
                        SET username='$safeName', mobile_number='$safeMobile' $passwordQuery 
                        WHERE username='$candidate_name' AND mobile_number='$candidate_mobile'";
    // Update employer_add_details table
    $updateDetails = "UPDATE candidate_details 
                      SET gender='$safeGender', 
                          current_company='$safeCompany', 
                          username='$safeName', 
                          mobile_number='$safeMobile' 
                      WHERE username='$candidate_name' AND mobile_number='$candidate_mobile'";

    $updateAssociates = "UPDATE associate 
SET username='$safeName', mobile_number='$safeMobile' $passwordQuery 
WHERE username='$candidate_name' AND mobile_number='$candidate_mobile'";

    $employersUpdated = $conn->query($updateEmployers);
    $associateUpdated = $conn->query($updateAssociates);
    $detailsUpdated = $conn->query($updateDetails);
    if ($employersUpdated && $detailsUpdated) {
        $_SESSION['name'] = $safeName;
        $_SESSION['mobile'] = $safeMobile;
        $stmt = $conn->prepare("
         SELECT gender, current_company 
         FROM candidate_details 
         WHERE username = ? AND mobile_number = ?
         LIMIT 1
     ");
        $stmt->bind_param("ss", $safeName, $safeMobile);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $candidate_gender = $row['gender'];
            $candidate_company = $row['current_company'];
        }
        echo json_encode(['status' => 'success', 'message' => 'Profile Updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update Failed']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_addupdate'])) {
    $designation = trim($_POST['destination']);
    $workExperience = trim($_POST['work_experience']);
    $currentLocation = trim($_POST['current_location']);
    $currentSalary = trim($_POST['current_salary']);
    $employed = trim($_POST['employed']);
    $bank = trim($_POST['sales_experience']);
    $updatedCompany = trim($_POST['current_company']);

    $products = isset($_POST['products']) ? $_POST['products'] : [];
    $department = isset($_POST['departments']) ? $_POST['departments'] : [];

    $safeHlLap = in_array('hl_lap', $products) ? 'yes' : 'no';
    $safePersonalLoan = in_array('personal_loan', $products) ? 'yes' : 'no';
    $safeBusinessLoan = in_array('business_loan', $products) ? 'yes' : 'no';
    $safeGoldLoan = in_array('gold_loan', $products) ? 'yes' : 'no';
    $safeCreditCards = in_array('credit_cards', $products) ? 'yes' : 'no';
    $safeCasa = in_array('casa', $products) ? 'yes' : 'no';

    $safesales = in_array('Sales', $department) ? 'yes' : 'no';
    $safecredit = in_array('Credit_dept', $department) ? 'yes' : 'no';
    $safehr = in_array('HR_Training', $department) ? 'yes' : 'no';
    $safeoperations = in_array('Operations', $department) ? 'yes' : 'no';
    $safeCompany = $conn->real_escape_string($updatedCompany);
    $safeDesignation = $conn->real_escape_string($designation);
    $safeWorkExp = $conn->real_escape_string($workExperience);
    $safeCurrentLoc = $conn->real_escape_string($currentLocation);
    $safeCurrentSalary = $conn->real_escape_string($currentSalary);
    $safeEmployed = $conn->real_escape_string($employed);
    $safebank = $conn->real_escape_string($bank);

    $resumeFilePath = null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $fileTmpName = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileSize = $_FILES['resume']['size'];
        $fileType = $_FILES['resume']['type'];
        $uploadDir = '../uploads/resumes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uniqueFileName = uniqid() . '-' . basename($fileName);
        $filePath = $uploadDir . $uniqueFileName;
        if (move_uploaded_file($fileTmpName, $filePath)) {
            $resumeFilePath = $conn->real_escape_string($filePath);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
            exit;
        }
    }
    $updateQuery = "UPDATE candidate_details 
                    SET current_company='$safeCompany', 
                        destination='$safeDesignation',
                        work_experience='$safeWorkExp',
                        current_location='$safeCurrentLoc',
                        current_salary='$safeCurrentSalary',hl_lap='$safeHlLap',
                    personal_loan='$safePersonalLoan',
                    business_loan='$safeBusinessLoan',
                    gold_loan='$safeGoldLoan',
                    credit_cards='$safeCreditCards',
                    casa='$safeCasa',
                    Sales='$safesales',
                    Credit_dept='$safecredit',
                    Operations='$safeoperations',
                    HR_Training='$safehr',
                    sales_experience='$safebank',
                        employed='$safeEmployed'";
    if ($resumeFilePath) {
        $updateQuery .= ", resume='$resumeFilePath'";
    }
    $updateQuery .= " WHERE username='$candidate_name' AND mobile_number='$candidate_mobile'";
    $detailsUpdated = $conn->query($updateQuery);
    if ($detailsUpdated) {
        echo json_encode(['status' => 'success', 'message' => 'Profile Updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $filename = time() . '_' . basename($file['name']);
    $upload_dir = '../assets/candidate_partner_images/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $upload_dir)) {

        $getUserId = "SELECT user_id FROM candidates WHERE username='$candidate_name' AND mobile_number='$candidate_mobile'";
        $result = $conn->query($getUserId);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['user_id'];

            // Update candidate_details table
            $updateCandidateDetails = "UPDATE candidate_details 
                                       SET candidate_image='$filename' 
                                       WHERE user_id='$userId'";
            $candidateDetailsUpdated = $conn->query($updateCandidateDetails);

            // Update associate table
            $updateAssociates = "UPDATE associate 
                                 SET partner_image='$filename'
                                 WHERE username='$candidate_name' AND mobile_number='$candidate_mobile'";
            $associatesUpdated = $conn->query($updateAssociates);

            if ($candidateDetailsUpdated && $associatesUpdated) {
                echo json_encode(['status' => 'success', 'message' => 'Profile Updated', 'filename' => $filename]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Update Failed']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="/css/candidate.css">
    <link rel="stylesheet" href="./css/candidate_profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        .error-message {
            color: red;
            font-size: 12px;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
                left: -125px !important;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="main-content">
        <div class="container">
            <div class="profmain">
                <div class="profile-header">
                    <div class="profile-title">My Profile</div>
                </div>
                <div class="flexprofcard">
                    <div class="profile-card">
                        <div class="profile-avatar" style="background-color: <?php echo $dynamicColor; ?>"
                            id="avatarBox">
                            <?php if (!empty($candidateImage)): ?>
                                <img src="../assets/candidate_partner_images/<?php echo $candidateImage; ?>">
                            <?php else: ?>
                                <?php echo strtoupper($firstLetter); ?>
                            <?php endif; ?>
                            <div class="avaedit" onclick="enableImageEditing()">
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none"
                                    onchange="uploadImage()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M4 15.9997L3 19.9997L7 18.9997L18.586 7.4137C18.9609 7.03864 19.1716 6.53003 19.1716 5.9997C19.1716 5.46937 18.9609 4.96075 18.586 4.5857L18.414 4.4137C18.0389 4.03876 17.5303 3.82812 17 3.82812C16.4697 3.82813 15.9611 4.03876 15.586 4.4137L4 15.9997Z"
                                        stroke="#175DA8" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M4 15.9995L3 19.9995L7 18.9995L17 8.99951L14 5.99951L4 15.9995Z"
                                        fill="#175DA8" />
                                    <path d="M14 5.99951L17 8.99951M12 19.9995H20" stroke="#175DA8" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                        </div>
                        <div class="profile-name">
                            <?php echo ucfirst($candidate_name); ?>
                        </div>
                        <div class="profile-contact">
                            <div class="phicon">
                                <img src="./assets/ic_baseline-phone-in-talk.svg">
                            </div>
                            <div class="profile-mobile">+91
                                <?php echo $candidate_mobile; ?>
                            </div>
                        </div>
                        <div class="profile-contact">
                            <div class="phicon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M16.499 2.25V3.75H19.1712L16.64 6.3045C15.7353 5.62123 14.6327 5.25108 13.499 5.25C12.0965 5.25 10.787 5.805 9.79548 6.79725C9.30539 7.28144 8.91629 7.8581 8.65075 8.4938C8.38521 9.1295 8.24851 9.8116 8.24858 10.5005C8.24865 11.1895 8.38548 11.8715 8.65115 12.5072C8.91682 13.1428 9.30604 13.7194 9.79623 14.2035C10.4487 14.856 11.2257 15.3247 12.0927 15.5625C12.1782 15.4972 12.2742 15.429 12.3507 15.3525C12.6651 15.0328 12.891 14.6368 13.0062 14.2035C12.1896 14.103 11.4303 13.7315 10.85 13.1483C10.142 12.441 9.74898 11.502 9.74898 10.5C9.74898 9.498 10.1427 8.5605 10.8515 7.8525C11.558 7.14225 12.497 6.75 13.499 6.75C14.501 6.75 15.4392 7.14375 16.1465 7.8525C16.4963 8.19889 16.7739 8.61127 16.9631 9.06573C17.1524 9.5202 17.2495 10.0077 17.249 10.5C17.249 11.1488 17.0652 11.7525 16.757 12.3045C16.829 12.6915 16.874 13.098 16.874 13.5C16.874 13.8745 16.8427 14.242 16.7802 14.6025C16.9302 14.4802 17.0637 14.3415 17.2025 14.2035C18.194 13.2105 18.749 11.9025 18.749 10.5C18.749 9.3525 18.371 8.262 17.6945 7.359L20.249 4.8285V7.5H21.749V2.25H16.499ZM11.9052 8.4375C11.8197 8.50275 11.7237 8.571 11.6472 8.6475C11.3232 8.973 11.1117 9.3675 10.9917 9.79725C11.8092 9.90225 12.5555 10.2592 13.148 10.8517C13.8567 11.559 14.2505 12.498 14.2505 13.4993C14.2505 14.5005 13.8567 15.4387 13.148 16.1467C12.44 16.8577 11.501 17.25 10.499 17.25C9.49698 17.25 8.55873 16.8563 7.85148 16.1475C7.50134 15.8014 7.22354 15.389 7.03427 14.9345C6.84499 14.48 6.74801 13.9924 6.74898 13.5C6.74898 12.8512 6.93273 12.2475 7.24098 11.6955C7.16653 11.3013 7.12738 10.9012 7.12398 10.5C7.12398 10.126 7.15523 9.7585 7.21773 9.3975C7.06773 9.51975 6.93498 9.6585 6.79548 9.79725C5.80548 10.788 5.24898 12.0975 5.24898 13.5C5.24898 14.6475 5.62698 15.738 6.30348 16.641L5.03898 17.9062L3.53898 16.4062L2.45898 17.4608L3.95898 18.9608L2.45898 20.4608L3.53898 21.5408L5.03898 20.0408L6.53898 21.5408L7.59273 20.4608L6.09273 18.9608L7.35873 17.6962C8.26255 18.3806 9.36533 18.7506 10.499 18.75C11.9015 18.75 13.211 18.195 14.2025 17.2028C15.194 16.212 15.749 14.9025 15.749 13.5C15.749 12.0975 15.194 10.7895 14.2017 9.7965C13.5492 9.144 12.7722 8.67525 11.9052 8.4375Z"
                                        fill="#175DA8" />
                                </svg>
                            </div>
                            <div class="profile-mobile">
                                <?php echo ucfirst($candidate_gender); ?>
                            </div>
                        </div>
                        <div class="profile-contact">
                            <div class="phicon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"
                                    fill="none">
                                    <path
                                        d="M15.6696 15.5893H13.9286V1.66071H14.5089C14.6628 1.66071 14.8105 1.59957 14.9193 1.49073C15.0281 1.38189 15.0893 1.23428 15.0893 1.08036C15.0893 0.926437 15.0281 0.778821 14.9193 0.669983C14.8105 0.561145 14.6628 0.5 14.5089 0.5H1.74107C1.58715 0.5 1.43954 0.561145 1.3307 0.669983C1.22186 0.778821 1.16071 0.926437 1.16071 1.08036C1.16071 1.23428 1.22186 1.38189 1.3307 1.49073C1.43954 1.59957 1.58715 1.66071 1.74107 1.66071H2.32143V15.5893H0.580357C0.426437 15.5893 0.278821 15.6504 0.169983 15.7593C0.0611446 15.8681 0 16.0157 0 16.1696C0 16.3236 0.0611446 16.4712 0.169983 16.58C0.278821 16.6889 0.426437 16.75 0.580357 16.75H15.6696C15.8236 16.75 15.9712 16.6889 16.08 16.58C16.1889 16.4712 16.25 16.3236 16.25 16.1696C16.25 16.0157 16.1889 15.8681 16.08 15.7593C15.9712 15.6504 15.8236 15.5893 15.6696 15.5893ZM5.22321 3.40179H6.96429C7.11821 3.40179 7.26582 3.46293 7.37466 3.57177C7.4835 3.68061 7.54464 3.82822 7.54464 3.98214C7.54464 4.13606 7.4835 4.28368 7.37466 4.39252C7.26582 4.50136 7.11821 4.5625 6.96429 4.5625H5.22321C5.06929 4.5625 4.92168 4.50136 4.81284 4.39252C4.704 4.28368 4.64286 4.13606 4.64286 3.98214C4.64286 3.82822 4.704 3.68061 4.81284 3.57177C4.92168 3.46293 5.06929 3.40179 5.22321 3.40179ZM5.22321 6.30357H6.96429C7.11821 6.30357 7.26582 6.36472 7.37466 6.47355C7.4835 6.58239 7.54464 6.73001 7.54464 6.88393C7.54464 7.03785 7.4835 7.18546 7.37466 7.2943C7.26582 7.40314 7.11821 7.46429 6.96429 7.46429H5.22321C5.06929 7.46429 4.92168 7.40314 4.81284 7.2943C4.704 7.18546 4.64286 7.03785 4.64286 6.88393C4.64286 6.73001 4.704 6.58239 4.81284 6.47355C4.92168 6.36472 5.06929 6.30357 5.22321 6.30357ZM4.64286 9.78571C4.64286 9.63179 4.704 9.48418 4.81284 9.37534C4.92168 9.2665 5.06929 9.20536 5.22321 9.20536H6.96429C7.11821 9.20536 7.26582 9.2665 7.37466 9.37534C7.4835 9.48418 7.54464 9.63179 7.54464 9.78571C7.54464 9.93963 7.4835 10.0873 7.37466 10.1961C7.26582 10.3049 7.11821 10.3661 6.96429 10.3661H5.22321C5.06929 10.3661 4.92168 10.3049 4.81284 10.1961C4.704 10.0873 4.64286 9.93963 4.64286 9.78571ZM9.86607 15.5893H6.38393V12.6875H9.86607V15.5893ZM11.0268 10.3661H9.28571C9.13179 10.3661 8.98418 10.3049 8.87534 10.1961C8.7665 10.0873 8.70536 9.93963 8.70536 9.78571C8.70536 9.63179 8.7665 9.48418 8.87534 9.37534C8.98418 9.2665 9.13179 9.20536 9.28571 9.20536H11.0268C11.1807 9.20536 11.3283 9.2665 11.4372 9.37534C11.546 9.48418 11.6071 9.63179 11.6071 9.78571C11.6071 9.93963 11.546 10.0873 11.4372 10.1961C11.3283 10.3049 11.1807 10.3661 11.0268 10.3661ZM11.0268 7.46429H9.28571C9.13179 7.46429 8.98418 7.40314 8.87534 7.2943C8.7665 7.18546 8.70536 7.03785 8.70536 6.88393C8.70536 6.73001 8.7665 6.58239 8.87534 6.47355C8.98418 6.36472 9.13179 6.30357 9.28571 6.30357H11.0268C11.1807 6.30357 11.3283 6.36472 11.4372 6.47355C11.546 6.58239 11.6071 6.73001 11.6071 6.88393C11.6071 7.03785 11.546 7.18546 11.4372 7.2943C11.3283 7.40314 11.1807 7.46429 11.0268 7.46429ZM11.0268 4.5625H9.28571C9.13179 4.5625 8.98418 4.50136 8.87534 4.39252C8.7665 4.28368 8.70536 4.13606 8.70536 3.98214C8.70536 3.82822 8.7665 3.68061 8.87534 3.57177C8.98418 3.46293 9.13179 3.40179 9.28571 3.40179H11.0268C11.1807 3.40179 11.3283 3.46293 11.4372 3.57177C11.546 3.68061 11.6071 3.82822 11.6071 3.98214C11.6071 4.13606 11.546 4.28368 11.4372 4.39252C11.3283 4.50136 11.1807 4.5625 11.0268 4.5625Z"
                                        fill="#175DA8" />
                                </svg>
                            </div>
                            <div class="profile-mobile">
                                <?php echo ucfirst($candidate_company); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $candidate_name = $_SESSION['name'];
                    $candidate_mobile = $_SESSION['mobile'];
                    $candidate_gender = isset($candidate_gender) ? $candidate_gender : '';
                    $candidate_company = isset($candidate_company) ? $candidate_company : '';
                    $query = "SELECT password FROM candidates WHERE username = '$candidate_name' AND mobile_number = '$candidate_mobile'";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $passwordField = $row['password'];
                        if ($passwordField === 'otp_verified') { ?>
                            <div class="profile-detail-card">
                                <form id="profileForm">
                                    <div class="profile-detail-header">
                                        <div class="profile-detail-title">Profile Details:</div>
                                        <div class="edit-icon" onclick="enableEditing()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path
                                                    d="M4 15.9997L3 19.9997L7 18.9997L18.586 7.4137C18.9609 7.03864 19.1716 6.53003 19.1716 5.9997C19.1716 5.46937 18.9609 4.96075 18.586 4.5857L18.414 4.4137C18.0389 4.03876 17.5303 3.82812 17 3.82812C16.4697 3.82813 15.9611 4.03876 15.586 4.4137L4 15.9997Z"
                                                    stroke="#175DA8" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M4 15.9995L3 19.9995L7 18.9995L17 8.99951L14 5.99951L4 15.9995Z"
                                                    fill="#175DA8" />
                                                <path d="M14 5.99951L17 8.99951M12 19.9995H20" stroke="#175DA8" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Name:</div>
                                            <div class="field-value">
                                                <input type="text" class="inputfield" name="username"
                                                    value="<?php echo ucfirst($candidate_name); ?>" readonly />
                                            </div>
                                            <span id="nameError" class="error-message"></span>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Contact Number:</div>
                                            <div class="field-value"><span>+91</span><input type="text" name="mobile_number"
                                                    class="inputfield" value="<?php echo $candidate_mobile; ?> " readonly />
                                            </div>
                                            <span id="mobileError" class="error-message"></span>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Gender:</div>
                                            <div class="field-value">
                                                <input type="text" class="inputfield" name="gender"
                                                    value="<?php echo ucfirst($candidate_gender); ?>" readonly />
                                            </div>
                                            <span id="genderError" class="error-message"></span>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Company:</div>
                                            <div class="field-value"><input type="text" name="current_company"
                                                    class="inputfield" value="<?php echo ucfirst($candidate_company); ?> "
                                                    readonly />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php } else { ?>
                            <div class="profile-detail-card">
                                <form id="profileForm">
                                    <div class="profile-detail-header">
                                        <div class="profile-detail-title">Profile Details:</div>
                                        <div class="edit-icon" onclick="enableEditing()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path
                                                    d="M4 15.9997L3 19.9997L7 18.9997L18.586 7.4137C18.9609 7.03864 19.1716 6.53003 19.1716 5.9997C19.1716 5.46937 18.9609 4.96075 18.586 4.5857L18.414 4.4137C18.0389 4.03876 17.5303 3.82812 17 3.82812C16.4697 3.82813 15.9611 4.03876 15.586 4.4137L4 15.9997Z"
                                                    stroke="#175DA8" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M4 15.9995L3 19.9995L7 18.9995L17 8.99951L14 5.99951L4 15.9995Z"
                                                    fill="#175DA8" />
                                                <path d="M14 5.99951L17 8.99951M12 19.9995H20" stroke="#175DA8" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Name:</div>
                                            <div class="field-value">
                                                <input type="text" class="inputfield" name="username"
                                                    value="<?php echo ucfirst($candidate_name); ?>" readonly />
                                            </div>
                                            <span id="nameError" class="error-message"></span>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Contact Number:</div>
                                            <div class="field-value"><span>+91</span> <input type="text" name="mobile_number"
                                                    class="inputfield" value="<?php echo $candidate_mobile; ?> " readonly />
                                            </div>
                                            <span id="mobileError" class="error-message"></span>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Gender:</div>
                                            <div class="field-value">
                                                <input type="text" class="inputfield" name="gender"
                                                    value="<?php echo ucfirst($candidate_gender); ?>" readonly />
                                            </div>
                                            <span id="genderError" class="error-message"></span>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Company:</div>
                                            <div class="field-value"><input type="text" name="current_company"
                                                    class="inputfield" value="<?php echo ucfirst($candidate_company); ?> "
                                                    readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Password:</div>
                                            <div class="field-value passfie">
                                                <input type="password" id="password" name="password" class="inputfield"
                                                    value="<?php echo htmlspecialchars($passwordField); ?>" readonly />
                                                <img id="toggle-password" class="toggle-icon" src="./assets/visibility_off.svg"
                                                    onclick="togglePassword('password', 'toggle-password')">
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Re-Enter Password:</div>
                                            <div class="field-value passfie">
                                                <input type="password" name="repassword" id="repassword" class="inputfield"
                                                    value="<?php echo htmlspecialchars($passwordField); ?>" readonly />
                                                <img id="toggle-repassword" class="toggle-icon"
                                                    src="./assets/visibility_off.svg"
                                                    onclick="togglePassword('repassword', 'toggle-repassword')">
                                            </div>
                                        </div>
                                        <div class="subbtn" id="saveButton" onclick="sendProfileUpdate()">
                                            <div class="submitwrapper">
                                                <img src="../assets/fluent_save-24-filled.svg">
                                                <span>Save Details</span>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        <?php }
                    } else {
                        echo "<p>User not found</p>";
                    } ?>
                </div>
            </div>
            <div class="additionalsection">
                <form id="addprofileForm" enctype="multipart/form-data">
                    <div class="profile-adddetail-header">
                        <div class="profile-detail-title">Additional Details:</div>
                        <div class="addedit" onclick="enableEditingAdditional()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M4 15.9997L3 19.9997L7 18.9997L18.586 7.4137C18.9609 7.03864 19.1716 6.53003 19.1716 5.9997C19.1716 5.46937 18.9609 4.96075 18.586 4.5857L18.414 4.4137C18.0389 4.03876 17.5303 3.82812 17 3.82812C16.4697 3.82813 15.9611 4.03876 15.586 4.4137L4 15.9997Z"
                                    stroke="#175DA8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path d="M4 15.9995L3 19.9995L7 18.9995L17 8.99951L14 5.99951L4 15.9995Z"
                                    fill="#175DA8">
                                </path>
                                <path d="M14 5.99951L17 8.99951M12 19.9995H20" stroke="#175DA8" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="addidata two-column-layout">
                        <div class="column">
                            <div class="field">
                                <div class="field-label">Are you Currently Employed?</div>
                                <div class="field-value"><select name="employed" class="inputfield">

                                        <option value="yes" <?php echo ($candidate_employed == 'yes') ? 'selected' : ''; ?>>&emsp;&emsp;
                                            Yes</option>
                                        <option value="no" <?php echo ($candidate_employed == 'no') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;
                                            NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <div class="field-label">Which Company are you currently working in?</div>
                                <div class="field-value"><input type="text" name="current_company" class="inputfield"
                                        value="<?php echo ucfirst($candidate_company); ?>" readonly></div>
                            </div>
                            <div class="field">
                                <div class="field-label">Designation & Product / Department</div>
                                <div class="field-value"><input type="text" name="destination" class="inputfield"
                                        value="<?php echo ucfirst($candidate_destination); ?>" readonly></div>
                            </div>
                            <div class="field">
                                <div class="field-label">Do have any past experience working in Bank / NBFC?</div>
                                <div class="field-value"><select name="sales_experience" class="inputfield">
                                        <option value="yes" <?php echo ($candidate_bank == 'yes') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;
                                            Yes</option>
                                        <option value="no" <?php echo ($candidate_bank == 'no') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;
                                            NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <div class="field-label">The Banking Products in which you work Experience?</div>
                                <div class="field-value"><select name="products[]" class="inputfield">
                                        <option value="hl_lap" <?php echo ($hl_lap == 'yes') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;HLP/LAP
                                        </option>
                                        <option value="personal_loan" <?php echo ($personal_loan == 'yes') ? 'selected' : ''; ?>>&emsp;&emsp;
                                            Personal Loan</option>
                                        <option value="business_loan" <?php echo ($business_loan == 'yes') ? 'selected' : ''; ?>>&emsp;&emsp;
                                            Business Loan</option>
                                        <option value="gold_loan" <?php echo ($gold_loan == 'yes') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;Gold Loan
                                        </option>
                                        <option value="credit_cards" <?php echo ($credit_cards == 'yes') ? 'selected' : ''; ?>>&emsp;&emsp;Credit Cards</option>
                                        <option value="casa" <?php echo ($casa == 'yes') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;CASA
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <div class="field-label">The Department in which you have work Experience?</div>
                                <div class="field-value">
                                    <select name="departments[]" class="inputfield">
                                        <option value="Sales" <?php echo ($sales == 'yes') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;SALES
                                        </option>
                                        <option value="Credit_dept" <?php echo ($credit == 'yes') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;Credit
                                        </option>
                                        <option value="Operations" <?php echo ($operations == 'yes') ? 'selected' : ''; ?>>&emsp;&emsp;Operations</option>
                                        <option value="HR_Training" <?php echo ($hr == 'yes') ? 'selected' : ''; ?>>
                                            &emsp;&emsp;HR/Training</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <div class="field-label">Total Work Experience in Years?</div>
                                <div class="field-value"><input type="text" name="work_experience" class="inputfield"
                                        value="<?php echo $candidate_work_expereince; ?>" readonly></div>
                                <span id="workExpError" class="error-message"></span>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <div class="field-label">Current Location / Preferred Job Location?</div>
                                <div class="field-value"><input type="text" name="current_location" class="inputfield"
                                        value="<?php echo ucfirst($candidate_current_location); ?>" readonly>
                                </div>
                                <span id="locationError" class="error-message"></span>
                            </div>
                            <div class="field">
                                <div class="field-label">Yearly Current Salary – CTC (Rs lakhs)</div>
                                <div class="field-value"><input type="text" name="current_salary" class="inputfield"
                                        value="<?php echo $candidate_current_salary; ?>" readonly></div>
                                <span id="salaryError" class="error-message"></span>
                            </div>
                            <div class="resume-upload">
                                <div class="field-label">Resume:</div>
                                <div class="resume-wrapper">
                                    <div class="resume-preview" id="previewBox">
                                        <div class="resumeshadow">
                                            <canvas id="pdf-canvas" style="border:1px solid #ccc;"></canvas>

                                        </div>
                                    </div>
                                    <input type="file" name="resume" class="inputfield" id="resumeInput">
                                </div>
                                <div class="upload-btn-wrapper">
                                    <img src="./assets/uim_paperclip.svg">
                                    <button type="button" class="upload-btn" onclick="uploadResume()">Upload
                                        Resume</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="subabtn" id="saveAButton" onclick="sendaddProfileUpdate()">
                        <div class="submitwrapper">
                            <img src="../assets/fluent_save-24-filled.svg">
                            <span>Save Details</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
   
        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('dropdownMenuLink');
            const profileDropdown = document.getElementById('custom-dropdown');

            profileToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileDropdown.contains(e.target) && !profileToggle.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        });
  
    </script>
    <?php include '../employer_flow/demofooter.php'; ?>
    <script>
        function validateName() {
            const nameInput = document.querySelector("input[name='username']");
            const nameError = document.getElementById('nameError');
            const name = nameInput.value.trim();
            const nameRegex = /^[a-zA-Z\s]+$/;
            if (!nameRegex.test(name)) {
                nameError.textContent = "Name can only contain letters and spaces.";
                return false;
            } else {
                nameError.textContent = '';
                return true;
            }
        }
        function validateMobile() {
            const mobileInput = document.querySelector("input[name='mobile_number']");
            const mobileError = document.getElementById('mobileError');
            const mobile = mobileInput.value.trim();
            const mobileRegex = /^[0-9]{10}$/;

            if (!mobileRegex.test(mobile)) {
                mobileError.textContent = "Mobile number must be exactly 10 digits.";
                return false;
            } else {
                mobileError.textContent = '';
                return true;
            }
        }
        function validateGender() {
            const genderInput = document.querySelector("input[name='gender']");
            const genderError = document.getElementById('genderError');
            const gender = genderInput.value.trim();
            const genderRegex = /^(Male|Female|Other)$/i;

            if (!genderRegex.test(gender)) {
                genderError.textContent = "Gender must be Male, Female, or Other.";
                return false;
            } else {
                genderError.textContent = '';
                return true;
            }
        }
        function validateWorkExp() {
            const workExpInput = document.querySelector("input[name='work_experience']");
            const workExpError = document.getElementById('workExpError');
            const workExp = workExpInput.value.trim();
            const workExpRegex = /^\d+(\.\d+)?$/;

            if (!workExpRegex.test(workExp)) {
                workExpError.textContent = "Work experience must be a valid number.";
                return false;
            } else {
                workExpError.textContent = '';
                return true;
            }
        }

        function validateLocation() {
            const locationInput = document.querySelector("input[name='current_location']");
            const locationError = document.getElementById('locationError');
            const location = locationInput.value.trim();
            const locationRegex = /^[a-zA-Z\s]+$/;

            if (!locationRegex.test(location)) {
                locationError.textContent = "Location must contain only letters and spaces.";
                return false;
            } else {
                locationError.textContent = '';
                return true;
            }
        }

        function validateSalary() {
            const salaryInput = document.querySelector("input[name='current_salary']");
            const salaryError = document.getElementById('salaryError');
            const salary = salaryInput.value.trim();
            const salaryRegex = /^\d+(\.\d+)?$/;

            if (!salaryRegex.test(salary)) {
                salaryError.textContent = "Salary must be a valid number without special characters.";
                return false;
            } else {
                salaryError.textContent = '';
                return true;
            }
        }

    </script>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            icon.src = isPassword ? "./assets/visibility.svg" : "./assets/visibility_off.svg";
        }
        function enableEditing() {
            const inputs = document.querySelectorAll('.inputfield');
            inputs.forEach(input => input.removeAttribute('readonly'));
            const saveButton = document.getElementById('saveButton');
            saveButton.style.display = 'inline-flex';
            const addeditDiv = document.querySelector('.edit-icon');
            if (addeditDiv) {
                addeditDiv.style.border = '0.1px solid var(--Deeesha-Blue, #175DA8)';
            }
            const addeditDivFields = document.querySelectorAll('.profile-detail-card .field-value');
            addeditDivFields.forEach(field => {
                field.style.border = '0.1px solid var(--Deeesha-Blue, #175DA8)';
            });
            const fieldLabels = document.querySelectorAll('.fieldlabels');
            if (fieldLabels.length > 0) {
                const lastFieldLabel = fieldLabels[fieldLabels.length - 1];

                function updateFieldLabelMargin() {
                    const width = window.innerWidth;
                    if (width <= 454) {
                        lastFieldLabel.style.marginBottom = '15%';
                    } else if (width <= 760) {
                        lastFieldLabel.style.marginBottom = '10%';
                    } else {
                        lastFieldLabel.style.marginBottom = '6%';
                    }
                }
                updateFieldLabelMargin();
                window.addEventListener('resize', updateFieldLabelMargin);
            }
            const profileContacts = document.querySelectorAll('.profile-contact');
            if (profileContacts.length > 0) {
                const lastProfileContact = profileContacts[profileContacts.length - 1];

                function updateProfileContactMargin(e) {
                    if (e.matches) {
                        lastProfileContact.style.marginBottom = '0';
                    } else {
                        lastProfileContact.style.marginBottom = '13%';
                    }
                }
                const mediaQuery = window.matchMedia('(min-width: 360px) and (max-width: 880px)');
                updateProfileContactMargin(mediaQuery); // Initial call
                mediaQuery.addEventListener('change', updateProfileContactMargin);
            }
        }
    </script>
    <script>
        function sendProfileUpdate() {
            const isNameValid = validateName();
            const isMobileValid = validateMobile();
            const isGenderValid = validateGender();

            if (!isNameValid || !isMobileValid || !isGenderValid) return;

            const formData = new FormData(document.getElementById('profileForm'));
            formData.append('ajax_update', true);

            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        document.querySelectorAll('.inputfield').forEach(input => input.setAttribute('readonly', true));
                        document.getElementById('saveButton').style.display = 'none';
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('AJAX error: ' + error);
                });
        }

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('profileForm');

            form.addEventListener('focusout', (event) => {
                const target = event.target;

                if (target.name === 'username') {
                    validateName();
                } else if (target.name === 'mobile_number') {
                    validateMobile();
                } else if (target.name === 'gender') {
                    validateGender();
                }
            });
        });
    </script>
    <script>
        document.querySelector(".resumeshadow").addEventListener("click", function () {
            document.getElementById("resumeInput").click();
        });
    </script>
    <script>
        const fileInput = document.getElementById("resumeInput");
        const previewBox = document.getElementById("previewBox");
        previewBox.addEventListener("click", function () {
            fileInput.click();
        });
        fileInput.addEventListener("change", function () {
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                previewBox.innerHTML = `
                <img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg" alt="PDF Icon" style="width: 40px; margin-bottom: 10px;">
                <span style="font-size: 14px; text-align: center;">${fileName}</span>
            `;
            }
        });
    </script>

    <script>
        function enableEditingAdditional() {
            const inputs = document.querySelectorAll('.additionalsection .inputfield, .additionalsection select');
            inputs.forEach(input => input.removeAttribute('readonly'));
            const saveButton = document.getElementById('saveAButton');
            saveButton.style.display = 'inline-flex';
            const addeditDiv = document.querySelector('.addedit');
            if (addeditDiv) {
                addeditDiv.style.border = '0.1px solid var(--Deeesha-Blue, #175DA8)';
            }
            const addeditDivFields = document.querySelectorAll('.additionalsection .field-value');
            addeditDivFields.forEach(field => {
                field.style.border = '0.1px solid var(--Deeesha-Blue, #175DA8)';
            });

            const fieldLabels = document.querySelectorAll('.addidata');
            if (fieldLabels.length > 0) {
                const lastFieldLabel = fieldLabels[fieldLabels.length - 1];

                function updateFieldLabelMargin() {
                    const width = window.innerWidth;
                    if (width <= 454) {
                        lastFieldLabel.style.marginBottom = '10%';
                    } else if (width <= 760) {
                        lastFieldLabel.style.marginBottom = '9%';
                    } else {
                        lastFieldLabel.style.marginBottom = '7%';
                    }
                }
                updateFieldLabelMargin();
                window.addEventListener('resize', updateFieldLabelMargin);
            }
            const upbtn = document.querySelectorAll('.upload-btn-wrapper');
            if (upbtn.length > 0) {
                const lastFieldLabel = upbtn[upbtn.length - 1];

                function upbtnwrap() {
                    const width = window.innerWidth;
                    if (width <= 454) {
                        lastFieldLabel.style.marginBottom = '5%';
                    }
                }
                upbtnwrap();
                window.addEventListener('resize', upbtnwrap);
            }
            // inputs.forEach(input => input.addEventListener('change', sendaddProfileUpdate));
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addProfileForm = document.getElementById('addprofileForm');

            addProfileForm.addEventListener('focusout', (event) => {
                const target = event.target;

                if (target.name === 'work_experience') {
                    validateWorkExp();
                } else if (target.name === 'current_location') {
                    validateLocation();
                } else if (target.name === 'current_salary') {
                    validateSalary();
                }
            });
        });
    </script>

    <script>
        function sendaddProfileUpdate() {
            const isWorkExpValid = validateWorkExp();
            const isLocationValid = validateLocation();
            const isSalaryValid = validateSalary();

            if (!isWorkExpValid || !isLocationValid || !isSalaryValid) return;

            const formData = new FormData(document.getElementById('addprofileForm'));
            formData.append('ajax_addupdate', true); // flag to detect AJAX request in PHP

            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        document.querySelectorAll('.additionalsection .inputfield').forEach(input => input.setAttribute('readonly', true));
                        document.getElementById('saveAButton').style.display = 'none';
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('AJAX error: ' + error);
                });
        }
    </script>

    <script>
        function uploadResume() {
            const formData = new FormData(document.getElementById('addprofileForm'));
            formData.append('ajax_addupdate', true);
            const fileInput = document.getElementById('resumeInput');
            if (fileInput.files.length === 0) {
                alert("Please select a resume file to upload.");
                return;
            }
            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    alert(data.message);
                    if (data.status === 'success') {
                        const previewBox = document.getElementById('previewBox');
                        previewBox.innerHTML = `<div class="resumeshadow"><img src="./assets/vscode-icons_file-type-pdf2.svg" alt="PDF Icon"><span>${data.fileName}</span></div>`;
                    }
                })
                .catch(error => {
                    alert('AJAX error: ' + error);
                });
        }
    </script>
    <script>
        var url = "<?php echo $candidate_resume; ?>";
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        pdfjsLib.getDocument(url).promise.then(function (pdf) {
            pdf.getPage(1).then(function (page) {
                var desiredWidth = 300;
                var viewport = page.getViewport({ scale: 1 });
                var scale = desiredWidth / viewport.width;
                var scaledViewport = page.getViewport({ scale: scale });

                var canvas = document.getElementById('pdf-canvas');
                var context = canvas.getContext('2d');
                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;

                page.render({ canvasContext: context, viewport: scaledViewport });
            });
        });
    </script>
    <script>
        function enableImageEditing() {
            document.getElementById('avatarInput').click();
        }

        function uploadImage() {
            var input = document.getElementById('avatarInput');
            var file = input.files[0];

            if (file) {
                var formData = new FormData();
                formData.append("avatar", file);

                fetch(window.location.href, {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())  // ✅ changed from .text() to .json()
                    .then(result => {
                        if (result.status === "success") {
                            // Change avatar immediately after upload
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var avatarBox = document.getElementById('avatarBox');
                                avatarBox.innerHTML = `
                        <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <div class="avaedit" onclick="enableImageEditing()" style="position: absolute; bottom: 10px; right: 10px; background: #fff; border-radius: 50%; padding: 8px; cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
                                <path d="M4 16L3 20L7 19L18.586 7.414C18.961 7.039 19.172 6.53 19.172 6C19.172 5.469 18.961 4.961 18.586 4.586L18.414 4.414C18.039 4.039 17.53 3.828 17 3.828C16.47 3.828 15.961 4.039 15.586 4.414L4 16Z" stroke="#175DA8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 16L3 20L7 19L17 9L14 6L4 16Z" fill="#175DA8"/>
                                <path d="M14 6L17 9M12 20H20" stroke="#175DA8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    `;
                                location.reload();
                            }
                            reader.readAsDataURL(file);
                        } else {
                            alert('Upload Failed: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Upload error: Server sent invalid response (maybe PHP error)');
                    });
            }
        }
    </script>
</body>

</html>