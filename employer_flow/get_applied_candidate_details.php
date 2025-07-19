<?php
include '../db/connection.php';
session_start();

// Use a function to mask phone
function maskPhone($str) {
   if (strlen($str) <= 2) {
        return $str;
    }
    // Return the first two characters and exactly four asterisks
    return substr($str, 0, 2) . '*******';
}

// Convert "yes" fields into product lists
function getSelectedItems($row, $fields) {
    $items = [];
    foreach ($fields as $dbField => $label) {
        if (isset($row[$dbField]) && strtolower(trim($row[$dbField])) === 'yes') {
            $items[] = $label;
        }
    }
    return !empty($items) ? implode(", ", $items) : "N/A";
}

if (isset($_GET['id'])) {
    $candidateId = intval($_GET['id']);

    // Fetch candidate details
    //$query = "SELECT * FROM candidate_details WHERE id = ?";
    // Fetch candidate details who applied for a specific job
    $query = "
        SELECT cd.*
        FROM candidate_details cd
        INNER JOIN jobs_applied ja ON ja.candidate_id = cd.user_id
        WHERE ja.job_id = ? AND cd.id = ?
    ";
    // print_r($query);exit;
    $stmt  = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $candidateId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Basic fields
        $username     = $row['username'] ?? '';
        $initials     = strtoupper(substr($username, 0, 2));
        $fullName     = ucfirst($username);
        $maskedMobile = maskPhone($row['mobile_number'] ?? '');
        $maskedfullname = maskPhone($fullName ?? '');
        $gender       = ucfirst($row['gender'] ?? 'Male');

        // Age display (if you want to hardcode months, you can do so)
        // Example: "23 years, 7 months" => you could store months in DB or add logic
        // For now, we'll just do "23 years" if `age` is available
        $ageDisplay = isset($row['age']) ? $row['age'] . ' years' : '23 years';

        $workExp  = $row['work_experience']    ?? 'N/A';
        $salary   = $row['current_salary']     ?? 'N/A';
        $location = ucfirst($row['current_location'] ?? 'N/A');

        // Current company & role
        $companyName = !empty($row['companyname']) 
                        ? $row['companyname'] 
                        : ($row['current_company'] ?? 'N/A');

        $jobRole = !empty($row['destination']) 
                    ? $row['destination'] 
                    : '';

        // Booleans
        $employed      = (isset($row['employed']) && strtolower($row['employed']) === 'yes') ? 'Yes' : 'No';
        $bankNbfcExp   = (isset($row['sales_experience']) && strtolower($row['sales_experience']) === 'yes') ? 'Yes' : 'No';

        // Product list
        $productFields = [
            'hl_lap'         => "HL LAP",
            'business_loan'  => "Business Loan",
            'gold_loan'      => "Gold Loan",
            'casa'           => "CASA",
            'personal_loan'  => "Personal Loan",
            'education_loan' => "Education Loan",
            'credit_cards'   => "Credit Cards"
        ];
        $products = getSelectedItems($row, $productFields);

        // Department list
        $departmentFields = [
            'Sales'                 => "Sales",
            'Credit_dept'           => "Credit Dept",
            'HR_Training'           => "HR / Training",
            'Operations'            => "Operations",
            'Legal_compliance_Risk' => "Legal/Compliance/Risk"
        ];
        $departments = getSelectedItems($row, $departmentFields);

        // Resume
        $resume = $row['resume'] ?? '';

        // Query last active time (from 'candidates' table, based on mobile)
        $mobile        = $row['mobile_number'];
        $active_sql    = "SELECT updated FROM candidates WHERE mobile_number = '$mobile'";
        $active_result = $conn->query($active_sql);

        $lastActive = '';  // default
        if ($active_result && $active_result->num_rows > 0) {
            $active_data = $active_result->fetch_assoc();
            $active_date = $active_data['updated'] ?? null;

            if ($active_date) {
                $timezone     = new DateTimeZone('Asia/Kolkata');
                $updated_time = new DateTime($active_date, $timezone);
                $current_time = new DateTime('now', $timezone);

                // If the updated time is in the future, fix it
                if ($updated_time > $current_time) {
                    $updated_time = clone $current_time;
                }

                $time_diff = $current_time->getTimestamp() - $updated_time->getTimestamp();

                if ($time_diff < 600) {
                    $lastActive = "Active";
                } elseif ($time_diff < 3600) {
                    $lastActive = floor($time_diff / 60) . " mins ago";
                } elseif ($time_diff < 86400) {
                    $lastActive = floor($time_diff / 3600) . " hrs ago";
                } elseif ($time_diff < 604800) {
                    $lastActive = floor($time_diff / 86400) . " days ago";
                } elseif ($time_diff < 2592000) {
                    $lastActive = floor($time_diff / 604800) . " weeks ago";
                } else {
                    $lastActive = floor($time_diff / 2592000) . " months ago";
                }
            }
        }
        
        $employer_name = $_SESSION['name'];
$employer_mobile = $_SESSION['mobile'];

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT `id`, `username`, `mobile_number`, `password`, `created` 
                        FROM `employers` 
                        WHERE mobile_number = ? 
                        LIMIT 1");
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}

$stmt->bind_param("s", $employer_mobile);
$stmt->execute();

// Get the result and check if an employer was found
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $row_of_emp_id= $result->fetch_assoc();
    
    // Store the employer id in the session
    $_SESSION['employer_id'] =  $row_of_emp_id['id'];
   
} else {
    echo "No employers found.";
}

// Display updated session for debugging

 
 
if (!isset($_SESSION['employer_id'])) {
    die("Employer not found in session.");
}

$employer_id = $_SESSION['employer_id'];
$candidate_id = $row['id']; // Ensure $row is defined before using it

// Prepare SQL statement to check if the candidate is in the cart
$stmt = $conn->prepare("SELECT `id` FROM `user_cart` WHERE `user_id` = ? AND `candidate_id` = ?");
if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}

$stmt->bind_param("ii", $employer_id, $candidate_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_exists = $result->num_rows > 0;
        ?>

        <!-- Bootstrap Card / Popup Container -->
        <div class="position-relative d-inline-block">
            <!-- Close Button (Outside the Card) -->
            <!-- <button class="btn close-pop-up position-absolute">
                <i class="fas fa-times"></i>
            </button> -->

            <div class=" candidate-popup p-3">
                <!-- Header Row: Avatar + Name + Masked Phone + Action Buttons -->
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <!-- Avatar -->
                    <div class="avatar-circle-pop d-flex align-items-center justify-content-center bg-primary text-white fw-bold rounded-circle" >
                        <?php echo htmlspecialchars($initials); ?>
                    </div>
                    <!-- Name + Masked Phone -->
                    <div class="ms-3 flex-grow-1 ">
                        <span class="fw-semibold pop-fullname "><?php echo htmlspecialchars( $maskedfullname ); ?></span>
                        <span class="mx-2 pop-span-line">|</span>
                        <span class="pop-mobile">
                         <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M16.6667 12.9167C15.625 12.9167 14.625 12.75 13.6917 12.4417C13.5451 12.395 13.3887 12.389 13.239 12.4242C13.0893 12.4594 12.952 12.5346 12.8417 12.6417L11.0083 14.475C8.64283 13.2716 6.72005 11.3488 5.51667 8.98333L7.35 7.14167C7.46037 7.03695 7.5386 6.90293 7.5755 6.75533C7.6124 6.60773 7.60644 6.45267 7.55833 6.30833C7.24277 5.34824 7.08242 4.34395 7.08333 3.33333C7.08333 2.875 6.70833 2.5 6.25 2.5H3.33333C2.875 2.5 2.5 2.875 2.5 3.33333C2.5 11.1583 8.84167 17.5 16.6667 17.5C17.125 17.5 17.5 17.125 17.5 16.6667V13.75C17.5 13.2917 17.125 12.9167 16.6667 12.9167ZM15.8333 10H17.5C17.5 8.01088 16.7098 6.10322 15.3033 4.6967C13.8968 3.29018 11.9891 2.5 10 2.5V4.16667C13.225 4.16667 15.8333 6.775 15.8333 10ZM12.5 10H14.1667C14.1667 7.7 12.3 5.83333 10 5.83333V7.5C11.3833 7.5 12.5 8.61667 12.5 10Z" fill="#175DA8"/>
                         </svg> <?php echo htmlspecialchars($maskedMobile); ?>
                        </span>
                    </div>
                </div>

                <!-- Candidate Meta Info -->
                <div class="candidate-meta  mb-3">
                    <div class="mb-2">
                      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M10.0006 9.1714C12.3675 9.1714 14.2863 7.25262 14.2863 4.88569C14.2863 2.51875 12.3675 0.599976 10.0006 0.599976C7.63362 0.599976 5.71484 2.51875 5.71484 4.88569C5.71484 7.25262 7.63362 9.1714 10.0006 9.1714Z" fill="#175DA8"/>
                     <path d="M9.99999 19.35C14.1421 19.35 17.5 17.4312 17.5 15.0643C17.5 12.6973 14.1421 10.7786 9.99999 10.7786C5.85786 10.7786 2.5 12.6973 2.5 15.0643C2.5 17.4312 5.85786 19.35 9.99999 19.35Z" fill="#175DA8"/>
                     </svg>
                       <span class="gender-pop"> <?php echo $gender . ', ' . $ageDisplay; ?> </span>
                        <span class="mx-2">|</span>
                      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M17.4549 10.2181C17.4533 10.1917 17.4453 10.166 17.4316 10.1434C17.4178 10.1207 17.3988 10.1017 17.3761 10.0881C17.3535 10.0744 17.3278 10.0664 17.3013 10.0649C17.2749 10.0633 17.2485 10.0682 17.2243 10.0791C12.6687 12.0965 7.32881 12.0965 2.77316 10.0791C2.74902 10.0682 2.72259 10.0633 2.69615 10.0649C2.66971 10.0664 2.64404 10.0744 2.62136 10.0881C2.59867 10.1017 2.57965 10.1207 2.56592 10.1434C2.55219 10.166 2.54417 10.1917 2.54255 10.2181C2.45973 11.7837 2.54388 13.3536 2.7936 14.9013C2.86982 15.3728 3.10195 15.805 3.45286 16.129C3.80377 16.4529 4.25318 16.6498 4.7292 16.6881L6.26002 16.8108C8.74842 17.0119 11.2483 17.0119 13.7375 16.8108L15.2683 16.6881C15.7443 16.6498 16.1937 16.4529 16.5446 16.129C16.8955 15.805 17.1277 15.3728 17.2039 14.9013C17.4541 13.3517 17.5392 11.7816 17.4549 10.2189" fill="#175DA8"/>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M6.11322 5.97453V4.81333C6.11334 4.47071 6.23637 4.13951 6.45996 3.87991C6.68354 3.6203 6.99285 3.44953 7.33166 3.39863L8.32931 3.24898C9.4353 3.08367 10.5597 3.08367 11.6657 3.24898L12.6634 3.39863C13.0023 3.44955 13.3117 3.62044 13.5353 3.88022C13.7589 4.13999 13.8819 4.47139 13.8818 4.81415V5.97534L15.2671 6.08737C15.743 6.12568 16.1923 6.32244 16.5432 6.6462C16.8941 6.96997 17.1263 7.40204 17.2027 7.87333C17.2365 8.08412 17.2673 8.29539 17.2951 8.50708C17.3015 8.55817 17.2917 8.60999 17.267 8.65519C17.2424 8.70039 17.2041 8.73669 17.1577 8.75895L17.0947 8.78838C12.656 10.89 7.33984 10.89 2.90031 8.78838L2.83734 8.75895C2.79075 8.73682 2.75232 8.70057 2.72752 8.65536C2.70271 8.61014 2.69278 8.55826 2.69914 8.50708C2.72749 8.29556 2.75856 8.08431 2.79236 7.87333C2.86875 7.40204 3.10096 6.96997 3.45185 6.6462C3.80274 6.32244 4.25206 6.12568 4.72797 6.08737L6.11322 5.97453ZM8.51167 4.4617C9.49677 4.31456 10.4983 4.31456 11.4834 4.4617L12.481 4.61135C12.5294 4.61859 12.5736 4.64296 12.6056 4.68002C12.6375 4.71709 12.6551 4.76439 12.6552 4.81333V5.88866C10.8848 5.78756 9.11018 5.78756 7.33984 5.88866V4.81333C7.33989 4.76439 7.3575 4.71709 7.38946 4.68002C7.42142 4.64296 7.46562 4.61859 7.51402 4.61135L8.51167 4.4617ZM9.99546 9.42595C10.418 9.42595 10.7605 9.08345 10.7605 8.66096C10.7605 8.23847 10.418 7.89597 9.99546 7.89597C9.57297 7.89597 9.23047 8.23847 9.23047 8.66096C9.23047 9.08345 9.57297 9.42595 9.99546 9.42595Z" fill="#175DA8"/>
                     </svg> 
                     <span class="work-pop"> <?php echo htmlspecialchars($workExp); ?> </span>
                    </div>
                    <div>
                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.03516 14.9433C3.03516 16.1276 3.94082 17.0333 5.12515 17.0333H14.8785C16.0628 17.0333 16.9685 16.1276 16.9685 14.9433V9.36996H3.03516V14.9433ZM14.8785 4.49331H13.4851V3.79664C13.4851 3.37864 13.2065 3.09998 12.7885 3.09998C12.3705 3.09998 12.0918 3.37864 12.0918 3.79664V4.49331H7.91181V3.79664C7.91181 3.37864 7.63314 3.09998 7.21515 3.09998C6.79715 3.09998 6.51848 3.37864 6.51848 3.79664V4.49331H5.12515C3.94082 4.49331 3.03516 5.39897 3.03516 6.5833V7.97663H16.9685V6.5833C16.9685 5.39897 16.0628 4.49331 14.8785 4.49331Z" fill="#175DA8"/>
                       </svg> 
                       <span class="salary-pop"> <?php echo htmlspecialchars($salary); ?> Lakhs Per Annum </span>
                        <span class="mx-2">|</span>
                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <path fill-rule="evenodd" clip-rule="evenodd" d="M9.3837 18.4451C9.3837 18.4451 3.33203 13.3484 3.33203 8.33342C3.33203 6.5653 4.03441 4.86961 5.28465 3.61937C6.5349 2.36913 8.23059 1.66675 9.9987 1.66675C11.7668 1.66675 13.4625 2.36913 14.7127 3.61937C15.963 4.86961 16.6654 6.5653 16.6654 8.33342C16.6654 13.3484 10.6137 18.4451 10.6137 18.4451C10.277 18.7551 9.72287 18.7517 9.3837 18.4451ZM9.9987 11.2501C10.3817 11.2501 10.761 11.1746 11.1149 11.0281C11.4687 10.8815 11.7903 10.6666 12.0611 10.3958C12.3319 10.125 12.5468 9.80344 12.6933 9.44957C12.8399 9.09571 12.9154 8.71644 12.9154 8.33342C12.9154 7.95039 12.8399 7.57112 12.6933 7.21726C12.5468 6.86339 12.3319 6.54186 12.0611 6.27102C11.7903 6.00018 11.4687 5.78534 11.1149 5.63877C10.761 5.49219 10.3817 5.41675 9.9987 5.41675C9.22515 5.41675 8.48328 5.72404 7.9363 6.27102C7.38932 6.818 7.08203 7.55987 7.08203 8.33342C7.08203 9.10696 7.38932 9.84883 7.9363 10.3958C8.48328 10.9428 9.22515 11.2501 9.9987 11.2501Z" fill="#175DA8"/>
                       </svg> 
                       <span class="location-pop"> <?php echo htmlspecialchars($location); ?> </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mb-3">
                    <button class="btn btn-outline-danger btn-sm me-1 reject-pop-up">
                     <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <path d="M7 6.52002L15 14.52M7 14.52L15 6.52002" stroke="#ED4C5C" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                     </svg> Reject
                    </button>
                    <button class="btn btn-outline-success btn-sm select-pop-up">
                       <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.50018 11.72L9.17518 15.395L16.5252 7.52002" stroke="#4EA647" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"/>
                     </svg>
                     Shortlist
                        </button>
                </div>

                <!-- Candidate Description Section -->
                <h6 class="mb-2 mt-3 pop-subhead-candidate">Candidate Description</h6>
                <ul class="list-unstyled mb-1">
                    <li class="mb-2">
                        <strong class="pop-strong">
                      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path d="M17.1951 16.2427H15.6094V3.55713H16.1379C16.2781 3.55713 16.4126 3.50144 16.5117 3.40232C16.6108 3.30319 16.6665 3.16875 16.6665 3.02856C16.6665 2.88838 16.6108 2.75394 16.5117 2.65481C16.4126 2.55569 16.2781 2.5 16.1379 2.5H4.50952C4.36934 2.5 4.2349 2.55569 4.13577 2.65481C4.03665 2.75394 3.98096 2.88838 3.98096 3.02856C3.98096 3.16875 4.03665 3.30319 4.13577 3.40232C4.2349 3.50144 4.36934 3.55713 4.50952 3.55713H5.03809V16.2427H3.45239C3.31221 16.2427 3.17777 16.2984 3.07864 16.3975C2.97952 16.4966 2.92383 16.6311 2.92383 16.7712C2.92383 16.9114 2.97952 17.0459 3.07864 17.145C3.17777 17.2441 3.31221 17.2998 3.45239 17.2998H17.1951C17.3353 17.2998 17.4697 17.2441 17.5688 17.145C17.6679 17.0459 17.7236 16.9114 17.7236 16.7712C17.7236 16.6311 17.6679 16.4966 17.5688 16.3975C17.4697 16.2984 17.3353 16.2427 17.1951 16.2427ZM7.68091 5.14282H9.2666C9.40679 5.14282 9.54123 5.19851 9.64035 5.29764C9.73948 5.39676 9.79517 5.5312 9.79517 5.67139C9.79517 5.81157 9.73948 5.94601 9.64035 6.04514C9.54123 6.14426 9.40679 6.19995 9.2666 6.19995H7.68091C7.54072 6.19995 7.40628 6.14426 7.30716 6.04514C7.20803 5.94601 7.15234 5.81157 7.15234 5.67139C7.15234 5.5312 7.20803 5.39676 7.30716 5.29764C7.40628 5.19851 7.54072 5.14282 7.68091 5.14282ZM7.68091 7.78564H9.2666C9.40679 7.78564 9.54123 7.84133 9.64035 7.94046C9.73948 8.03958 9.79517 8.17403 9.79517 8.31421C9.79517 8.45439 9.73948 8.58884 9.64035 8.68796C9.54123 8.78709 9.40679 8.84277 9.2666 8.84277H7.68091C7.54072 8.84277 7.40628 8.78709 7.30716 8.68796C7.20803 8.58884 7.15234 8.45439 7.15234 8.31421C7.15234 8.17403 7.20803 8.03958 7.30716 7.94046C7.40628 7.84133 7.54072 7.78564 7.68091 7.78564ZM7.15234 10.957C7.15234 10.8168 7.20803 10.6824 7.30716 10.5833C7.40628 10.4842 7.54072 10.4285 7.68091 10.4285H9.2666C9.40679 10.4285 9.54123 10.4842 9.64035 10.5833C9.73948 10.6824 9.79517 10.8168 9.79517 10.957C9.79517 11.0972 9.73948 11.2317 9.64035 11.3308C9.54123 11.4299 9.40679 11.4856 9.2666 11.4856H7.68091C7.54072 11.4856 7.40628 11.4299 7.30716 11.3308C7.20803 11.2317 7.15234 11.0972 7.15234 10.957ZM11.9094 16.2427H8.73804V13.5999H11.9094V16.2427ZM12.9666 11.4856H11.3809C11.2407 11.4856 11.1062 11.4299 11.0071 11.3308C10.908 11.2317 10.8523 11.0972 10.8523 10.957C10.8523 10.8168 10.908 10.6824 11.0071 10.5833C11.1062 10.4842 11.2407 10.4285 11.3809 10.4285H12.9666C13.1067 10.4285 13.2412 10.4842 13.3403 10.5833C13.4394 10.6824 13.4951 10.8168 13.4951 10.957C13.4951 11.0972 13.4394 11.2317 13.3403 11.3308C13.2412 11.4299 13.1067 11.4856 12.9666 11.4856ZM12.9666 8.84277H11.3809C11.2407 8.84277 11.1062 8.78709 11.0071 8.68796C10.908 8.58884 10.8523 8.45439 10.8523 8.31421C10.8523 8.17403 10.908 8.03958 11.0071 7.94046C11.1062 7.84133 11.2407 7.78564 11.3809 7.78564H12.9666C13.1067 7.78564 13.2412 7.84133 13.3403 7.94046C13.4394 8.03958 13.4951 8.17403 13.4951 8.31421C13.4951 8.45439 13.4394 8.58884 13.3403 8.68796C13.2412 8.78709 13.1067 8.84277 12.9666 8.84277ZM12.9666 6.19995H11.3809C11.2407 6.19995 11.1062 6.14426 11.0071 6.04514C10.908 5.94601 10.8523 5.81157 10.8523 5.67139C10.8523 5.5312 10.908 5.39676 11.0071 5.29764C11.1062 5.19851 11.2407 5.14282 11.3809 5.14282H12.9666C13.1067 5.14282 13.2412 5.19851 13.3403 5.29764C13.4394 5.39676 13.4951 5.5312 13.4951 5.67139C13.4951 5.81157 13.4394 5.94601 13.3403 6.04514C13.2412 6.14426 13.1067 6.19995 12.9666 6.19995Z" fill="#175DA8"/>
                      </svg>  Company:
                        </strong> 
                        <span class="company-text-pop"> <?php echo htmlspecialchars($companyName); ?></span>
                    </li>
                    <li  class="mb-2">
                        <strong class="pop-strong">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path d="M17.4549 10.2181C17.4533 10.1917 17.4453 10.166 17.4316 10.1434C17.4178 10.1207 17.3988 10.1017 17.3761 10.0881C17.3535 10.0744 17.3278 10.0664 17.3013 10.0649C17.2749 10.0633 17.2485 10.0682 17.2243 10.0791C12.6687 12.0965 7.32881 12.0965 2.77316 10.0791C2.74902 10.0682 2.72259 10.0633 2.69615 10.0649C2.66971 10.0664 2.64404 10.0744 2.62136 10.0881C2.59867 10.1017 2.57965 10.1207 2.56592 10.1434C2.55219 10.166 2.54417 10.1917 2.54255 10.2181C2.45973 11.7837 2.54388 13.3536 2.7936 14.9013C2.86982 15.3728 3.10195 15.805 3.45286 16.129C3.80377 16.4529 4.25318 16.6498 4.7292 16.6881L6.26002 16.8108C8.74842 17.0119 11.2483 17.0119 13.7375 16.8108L15.2683 16.6881C15.7443 16.6498 16.1937 16.4529 16.5446 16.129C16.8955 15.805 17.1277 15.3728 17.2039 14.9013C17.4541 13.3517 17.5392 11.7816 17.4549 10.2189" fill="#175DA8"/>
                             <path fill-rule="evenodd" clip-rule="evenodd" d="M6.11322 5.97453V4.81333C6.11334 4.47071 6.23637 4.13951 6.45996 3.87991C6.68354 3.6203 6.99285 3.44953 7.33166 3.39863L8.32931 3.24898C9.4353 3.08367 10.5597 3.08367 11.6657 3.24898L12.6634 3.39863C13.0023 3.44955 13.3117 3.62044 13.5353 3.88022C13.7589 4.13999 13.8819 4.47139 13.8818 4.81415V5.97534L15.2671 6.08737C15.743 6.12568 16.1923 6.32244 16.5432 6.6462C16.8941 6.96997 17.1263 7.40204 17.2027 7.87333C17.2365 8.08412 17.2673 8.29539 17.2951 8.50708C17.3015 8.55817 17.2917 8.60999 17.267 8.65519C17.2424 8.70039 17.2041 8.73669 17.1577 8.75895L17.0947 8.78838C12.656 10.89 7.33984 10.89 2.90031 8.78838L2.83734 8.75895C2.79075 8.73682 2.75232 8.70057 2.72752 8.65536C2.70271 8.61014 2.69278 8.55826 2.69914 8.50708C2.72749 8.29556 2.75856 8.08431 2.79236 7.87333C2.86875 7.40204 3.10096 6.96997 3.45185 6.6462C3.80274 6.32244 4.25206 6.12568 4.72797 6.08737L6.11322 5.97453ZM8.51167 4.4617C9.49677 4.31456 10.4983 4.31456 11.4834 4.4617L12.481 4.61135C12.5294 4.61859 12.5736 4.64296 12.6056 4.68002C12.6375 4.71709 12.6551 4.76439 12.6552 4.81333V5.88866C10.8848 5.78756 9.11018 5.78756 7.33984 5.88866V4.81333C7.33989 4.76439 7.3575 4.71709 7.38946 4.68002C7.42142 4.64296 7.46562 4.61859 7.51402 4.61135L8.51167 4.4617ZM9.99546 9.42595C10.418 9.42595 10.7605 9.08345 10.7605 8.66096C10.7605 8.23847 10.418 7.89597 9.99546 7.89597C9.57297 7.89597 9.23047 8.23847 9.23047 8.66096C9.23047 9.08345 9.57297 9.42595 9.99546 9.42595Z" fill="#175DA8"/>
                           </svg>Role:
                        </strong>
                        <span class="role-text-pop"><?php echo htmlspecialchars($jobRole); ?> </sapn> 
                    </li>
                </ul>

                <!-- Additional Details -->
                <h6 class="fw-bold mb-2 pop-subhead-add">
                 <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5 4.5H12C12 3.39688 11.1031 2.5 10 2.5C8.89687 2.5 8 3.39688 8 4.5H5.5C4.67188 4.5 4 5.17188 4 6V17C4 17.8281 4.67188 18.5 5.5 18.5H14.5C15.3281 18.5 16 17.8281 16 17V6C16 5.17188 15.3281 4.5 14.5 4.5ZM10 3.75C10.4156 3.75 10.75 4.08438 10.75 4.5C10.75 4.91563 10.4156 5.25 10 5.25C9.58437 5.25 9.25 4.91563 9.25 4.5C9.25 4.08438 9.58437 3.75 10 3.75ZM13 7.25C13 7.3875 12.8875 7.5 12.75 7.5H7.25C7.1125 7.5 7 7.3875 7 7.25V6.75C7 6.6125 7.1125 6.5 7.25 6.5H12.75C12.8875 6.5 13 6.6125 13 6.75V7.25ZM9.91222 12.5325C10.772 12.5325 11.469 11.8355 11.469 10.9757C11.469 10.1159 10.772 9.41895 9.91222 9.41895C9.05245 9.41895 8.35547 10.1159 8.35547 10.9757C8.35547 11.8355 9.05245 12.5325 9.91222 12.5325ZM12.6379 14.6729C12.6379 15.5326 11.4182 16.2296 9.91356 16.2296C8.40896 16.2296 7.18924 15.5326 7.18924 14.6729C7.18924 13.8131 8.40896 13.1161 9.91356 13.1161C11.4182 13.1161 12.6379 13.8131 12.6379 14.6729Z" fill="#175DA8"/>
                </svg>  
                Additional Details
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="add-text-subhead-pop ">
                         <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="10" cy="10" r="4" fill="#175DA8"/>
</svg>   Current Employment Status:
                        </span>
                        <span class="pop-add-text"><?php echo $employed; ?></span>
                    </li>
                    <li class="mb-2">
                    <span class="add-text-subhead-pop ">
                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="10" cy="10" r="4" fill="#175DA8"/>
</svg> Experience in Bank / NBFC:
                        </span>
                    <span class="pop-add-text"> <?php echo $bankNbfcExp; ?></span>
                    </li>
                    <li class="mb-2">
                        <span class="add-text-subhead-pop ">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="10" cy="10" r="4" fill="#175DA8"/>
</svg>    Experience in Banking Product:
                        </span>
                        <span class="pop-add-text"><?php echo $products; ?></span> 
                    </li>
                    <li class="mb-2">
                        <span class="add-text-subhead-pop ">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="10" cy="10" r="4" fill="#175DA8"/>
</svg>    Experience in Department:
                        </span>
                        <span class="pop-add-text"><?php echo $departments; ?></span>
                    </li>
                </ul>

                <!-- Resume Preview -->
               <!-- Resume Preview -->
<div class="mt-3">
    <h6 class="fw-bold pop-resume">Resume</h6>
    <?php
    if (!empty($resume) && file_exists(__DIR__ . '/../uploads/resumes/' . $resume)) {
        $pdfPath = 'uploads/resumes/' . htmlspecialchars($resume, ENT_QUOTES, 'UTF-8');
        echo '<div class="resume-preview-container position-relative" style="overflow:hidden;">
                <iframe 
                    class="blurred-resume" 
                    src="/../uploads/resumes/' . $resume . '" 
                    width="100%" 
                    height="300px" 
                    frameborder="0">
                </iframe>
                <div class="lock-overlay">
                    <i class="fas fa-lock"></i>
                </div>
              </div>';
    } else {
        echo '<div class="resume-placeholder bg-light p-4 text-center">
                <i class="fas fa-file-pdf fa-3x  mb-2"></i>
                <p class="">No resume available</p>
              </div>';
    }
    ?>
</div>


                <!-- Add to Cart Button -->
        <div class="add-to-cart-section mt-3">
        
   <?php if ($cart_exists): ?>
    <button class="btn btn-sm add-to-cart-btn btn-success add-to-cart-btn-pop " 
        data-candidate-id="<?php echo $candidate_id; ?>" 
        data-price="50" 
        onclick="goToCart()">
   <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z" fill="white"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z" fill="white"/>
        <path d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z" fill="white"/>
        <path d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z" fill="white"/>
      </svg>
        Go to Cart
    </button>
<?php else: ?>
    <button class="btn btn-sm add-to-cart-btn btn-success add-to-cart-btn-pop" 
        data-candidate-id="<?php echo $candidate_id; ?>" 
        data-price="50" 
        onclick="addToCart(<?php echo $candidate_id; ?>, 50)">
           <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z" fill="white"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z" fill="white"/>
        <path d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z" fill="white"/>
        <path d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z" fill="white"/>
      </svg>
        Add to Cart
    </button>
<?php endif; ?>
                </div>
                <div class="mt-3 p-2 main-div-active">
                <?php if (!empty($lastActive)): ?>
                    <span class="text-pop-active">
                 <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <g clip-path="url(#clip0_2288_7080)">
                     <path d="M8.00065 14.6667C4.31865 14.6667 1.33398 11.682 1.33398 8.00004C1.33398 4.31804 4.31865 1.33337 8.00065 1.33337C10.986 1.33337 13.4847 3.29537 14.334 6.00004H12.6673" stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                     <path d="M8 5.33337V8.00004L9.33333 9.33337M14.6367 8.66671C14.6567 8.44671 14.6667 8.22448 14.6667 8.00004M10 14.6667C10.2267 14.5916 10.4493 14.5044 10.6667 14.4054M13.86 11.3334C13.9893 11.0854 14.1038 10.8289 14.2033 10.564M12.128 13.486C12.3582 13.2958 12.5753 13.0909 12.7793 12.8714" stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                   </g>
                 <defs>
                 <clipPath id="clip0_2288_7080">
                   <rect width="16" height="16" fill="white"/>
                 </clipPath>
                </defs>
                </svg>
                        <?php echo $lastActive; ?>
                    </span>
                <?php endif; ?>
            </div>

            </div>
        </div>

        <?php
    } else {
        echo 'Candidate not found.';
    }
} else {
    echo 'Invalid candidate ID.';
}
?>
<script>

function goToCart() {
    window.location.href = "cart_page.php"; // Redirect to cart page
}
</script>
