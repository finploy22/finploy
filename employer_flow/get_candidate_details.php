<?php
include '../db/connection.php';
session_start();

// Use a function to mask phone
function maskPhone($str)
{
    if (strlen($str) <= 2) {
        return $str;
    }
    // Return the first two characters and exactly four asterisks
    return substr($str, 0, 2) . '*******';
}


if (isset($_GET['id']) && isset($_GET['employerId'])) {
    $candidateId = intval($_GET['id']);
    $employerId = intval($_GET['employerId']);


    // fetch candidates data if alreay employer buy it in orser_items table
    $stmt = $conn->prepare("SELECT * FROM `order_items` WHERE `employer_id` = ? AND `candidate_id` = ? AND `expired` = 0 AND `purchased` = 1");
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $employerId, $candidateId);
    $stmt->execute();
    $buy_candidate = $stmt->get_result();
    $buycandidate_ids = [];

    while ($row_buycandidate = $buy_candidate->fetch_assoc()) {
        $buycandidate_ids[] = $row_buycandidate['candidate_id'];
    }
    $unique_ids = array_unique($buycandidate_ids);



    // Fetch candidate details
    // $query = "SELECT * FROM candidate_details WHERE id = ?";

    $query = "SELECT 
    candidate_details.*, 
    departments.department_name, 
    sub_departments.sub_department_name, 
    products.product_name, 
    sub_products.sub_product_name,
    products_specialization.specialization,
    departments_category.category,
    locations.area,
    locations.city,
    locations.state
FROM candidate_details
LEFT JOIN departments 
    ON candidate_details.departments = departments.department_id
LEFT JOIN sub_departments 
    ON candidate_details.sub_departments = sub_departments.sub_department_id
LEFT JOIN products 
    ON candidate_details.products = products.product_id
LEFT JOIN sub_products 
    ON candidate_details.sub_products = sub_products.sub_product_id
LEFT JOIN products_specialization 
    ON candidate_details.specialization = products_specialization.specialization_id
LEFT JOIN departments_category 
    ON candidate_details.category = departments_category.category_id
LEFT JOIN locations 
    ON candidate_details.location_code = locations.id
WHERE candidate_details.id = ?";



    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $candidateId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Basic fields
        $username = $row['username'] ?? '';
        $initials = strtoupper(substr($username, 0, 2));
        $fullName = ucfirst($username);
        $gender = ucfirst($row['gender'] ?? 'Male');



        // Check if the candidate has not been purchased yet
        if (!in_array($row['id'], $unique_ids)) {
            // Mask sensitive info
            $maskedMobile = maskPhone($row['mobile_number'] ?? '');
            $maskedFullName = maskPhone($fullName ?? '');
        
            // Show placeholder or "locked" resume
            $resume = $row['resume'] ? 'not_buy' : '';
        
            // Disable buy button or show label accordingly
            $button = "already_buy";
        } else {
            // Candidate is already purchased, show full info
            $maskedMobile = $row['mobile_number'] ?? '';
            $maskedFullName = $fullName ?? '';
            $resume = $row['resume'] ?? '';
            $button = ""; // Show active buy/download button
        }






        // Age display (if you want to hardcode months, you can do so)
        // Example: "23 years, 7 months" => you could store months in DB or add logic
        // For now, we'll just do "23 years" if `age` is available
        $ageDisplay = isset($row['age']) ? $row['age'] . ' yrs' : '';

        $workExp = $row['work_experience'] ?? 'N/A';
        $salary = $row['current_salary'] ?? 'N/A';
        $location = ucfirst($row['area'] . " ," . $row['city'] ?? 'N/A');

        // Current company & role
        $companyName = !empty($row['companyname'])
            ? $row['companyname']
            : ($row['current_company'] ?? 'N/A');

        $jobRole = !empty($row['jobrole'])
            ? $row['jobrole']
            : 'Senior Human Resource';

        // Booleans
        $employed = (isset($row['employed']) && strtolower($row['employed']) === 'yes') ? 'Yes' : 'No';
        $bankNbfcExp = (isset($row['sales_experience']) && strtolower($row['sales_experience']) === 'yes') ? 'Yes' : 'No';

        // Product list
        $array_productIds = explode(',', $row['products']);
        $productIds = implode(',', array_map('intval', $array_productIds));
        $product_sql = "SELECT product_name FROM products WHERE product_id IN ($productIds)";
        $product_result = $conn->query($product_sql);
        $productNames = [];
        while ($product = $product_result->fetch_assoc()) {
            $productNames[] = $product["product_name"];
        }
        $products = !empty($productNames) ? implode(", ", $productNames) : "N/A";


        // Department list

        $array_departmentIds = explode(',', $row['departments']);
        $departmentIds = implode(',', array_map('intval', $array_departmentIds));
        $department_sql = "SELECT department_name FROM departments WHERE department_id IN ($departmentIds)";
        $department_result = $conn->query($department_sql);
        $departmentNames = [];
        while ($department = $department_result->fetch_assoc()) {
            $departmentNames[] = $department["department_name"];
        }
        $departments = !empty($departmentNames) ? implode(", ", $departmentNames) : "N/A";


        // Query last active time (from 'candidates' table, based on mobile)
        $mobile = $row['mobile_number'];
        $active_sql = "SELECT updated FROM candidates WHERE mobile_number = '$mobile'";
        $active_result = $conn->query($active_sql);

        $lastActive = '';  // default
        if ($active_result && $active_result->num_rows > 0) {
            $active_data = $active_result->fetch_assoc();
            $active_date = $active_data['updated'] ?? null;

            if ($active_date) {
                $timezone = new DateTimeZone('Asia/Kolkata');
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
            $row_of_emp_id = $result->fetch_assoc();

            // Store the employer id in the session
            $_SESSION['employer_id'] = $row_of_emp_id['id'];

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
                    <div
                        class="avatar-circle-pop d-flex align-items-center justify-content-center bg-primary text-white fw-bold rounded-circle">
                        <?php echo htmlspecialchars($initials); ?>
                    </div>
                    <!-- Name + Masked Phone -->
                    <div class="ms-3 flex-grow-1 ">
                        <span class="fw-semibold pop-fullname "><?php echo htmlspecialchars($maskedFullName); ?></span>
                        <div class="pop-mobile"> <img src=../images/ic_baseline-phone-in-talk-blk.svg width="20" height="20"
                                style="margin: 0 4px 4px 0;"><?php echo htmlspecialchars($maskedMobile); ?></div>
                        <!-- <span class="mx-2 pop-span-line">|</span> -->
                        <!-- <span class="pop-mobile">
                            <img src=../images/ic_baseline-phone-in-talk-blk.svg width="20" height="20"
                                style="margin: 0 4px 4px 0;"><?php echo htmlspecialchars($maskedMobile); ?>
                        </span> -->
                    </div>
                </div>

                <!-- Candidate Meta Info -->
                <div class="candidate-meta  mb-3">



                    <div class="job-meta">
                        <span class="requirement-details"><img src=../images/uis_calender.svg width="16" height="16"
                                style="margin: 0 4px 4px 0;"> <strong>Age:</strong> <?php echo $ageDisplay; ?></span> <span
                            class="devider-line">|</span>
                        <span class="requirement-details"><img src=../images/icons8_gender.svg width="16" height="16"
                                style="margin: 0 4px 4px 0;"><strong>Gender:</strong><?php echo $gender; ?></span> </span>

                    </div>
                    <div class="job-meta">
                        <span class="requirement-details"><img src=../images/basil_bag-solid-c.svg width="16" height="16"
                                style="margin: 0 4px 4px 0;"><strong>Experience:</strong>
                            <?php echo $workExp . ' yrs'; ?></span>
                        <span class="devider-line">|</span>
                        <span class="requirement-details"><img src=../images/ri_money-rupee-circle-fill.svg width="16"
                                height="16" style="margin: 0 4px 4px 0;"><strong>Salary:</strong>
                            <?php echo $salary . ' LPA'; ?></span> </span>


                    </div>

                    <p class="requirement-details mb-2 popup-location"><strong class="pe-2"><img
                                src=../images/weui_location-filled.svg width="16" height="16" style="margin: 0 4px 4px 0;">
                            Loc:</strong><?php echo $location; ?></p>


                </div>

                <!-- Action Buttons -->
                <div class="mb-3">
                    <button class="btn btn-outline-danger btn-sm me-1 reject-pop-up">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 6.52002L15 14.52M7 14.52L15 6.52002" stroke="#ED4C5C" stroke-width="2.2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg> Reject
                    </button>
                    <button class="btn btn-outline-success btn-sm select-pop-up">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.50018 11.72L9.17518 15.395L16.5252 7.52002" stroke="#4EA647" stroke-width="2.1"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Shortlist
                    </button>
                </div>

                <!-- Candidate Description Section -->
                <h6 class="mb-2 mt-3 pop-subhead-candidate">Candidate Description</h6>
                <ul class="list-unstyled mb-1">
                    <li class="mb-2">
                        <strong class="pop-strong">
                            <img src="../images/ph_building-fill.svg" alt="Employer"> Company:
                        </strong>
                        <span class="company-text-pop"> <?php echo htmlspecialchars($companyName); ?></span>
                    </li>
                    <li class="mb-2">
                        <strong class="pop-strong">
                            <img src=../images/basil_bag-solid-c.svg width="16" height="16" style="margin: 0 4px 4px 0;"> Role:
                        </strong>
                        <span class="role-text-pop"><?php echo htmlspecialchars($jobRole); ?> </sapn>
                    </li>
                </ul>

                <!-- Additional Details -->
                <h6 class="fw-bold mb-2 pop-subhead-add">
                    <img src="../images/notes-medical_svgrepo.com-blk.svg" alt="">
                    Additional Details
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="add-text-subhead-pop ">
                            <img src="../images/ph_building-fill-blk.svg" alt=""> Current Employment Status:
                        </span>
                        <span class="pop-add-text"><?php echo $employed; ?></span>
                    </li>
                    <li class="mb-2">
                        <span class="add-text-subhead-pop ">
                            <img src="../images/ph_building-fill-blk.svg" alt=""> Experience in Bank / NBFC:
                        </span>
                        <span class="pop-add-text"> <?php echo $bankNbfcExp; ?></span>
                    </li>
                    <li class="mb-2 d-flex">
                        <span class="add-text-subhead-pop me-1" style="white-space: nowrap;">
                            <img src="../images/ph_building-fill-blk.svg" alt=""> Product Data:
                        </span>
                        <span class="pop-add-text text-wrap">
                            <?php echo htmlspecialchars($products); ?>
                        </span>
                    </li>

                    <li class="mb-2 d-flex">
                        <span class="add-text-subhead-pop me-1" style="white-space: nowrap;">
                            <img src="../images/ph_building-fill-blk.svg" alt=""> Departments:
                        </span>

                        <span class="pop-add-text text-wrap">
                            <?php echo htmlspecialchars($departments); ?>
                        </span>
                    </li>
                </ul>



                <!-- Resume Preview -->
                <div class="mt-3">


                    <h6 class="fw-bold pop-resume">Resume</h6>
                    <?php
                    // echo "<pre>";
                    // print_r($resume);
                    // echo "</pre>";
            
                    $resumeDirectory = __DIR__ . '/../uploads/resumes/';
                    $resumeUrlPath = '../uploads/resumes/';

                    if (!empty($resume) && $resume !== 'not_buy' && file_exists($resumeDirectory . $resume)) {
                        $safeResumeName = htmlspecialchars($resume, ENT_QUOTES, 'UTF-8');
                        echo '<div class="resume-preview-container">
                            <iframe 
                                src="' . $resumeUrlPath . $safeResumeName . '" 
                                width="100%" 
                                height="300px" 
                                frameborder="0" 
                                title="Resume Preview" 
                                aria-label="Resume Preview">
                            </iframe>
                        </div>';
                    } elseif (!empty($resume) && $resume !== 'not_buy') {
                        $safeResumeName = htmlspecialchars($resume, ENT_QUOTES, 'UTF-8');
                        echo '<div class="resume-preview-container">
                            <iframe 
                                src="../images/image-404-url.png" 
                                width="100%" 
                                height="300px" 
                                frameborder="0" 
                                title="Resume Preview" 
                                aria-label="Resume Preview">
                            </iframe>
                        </div>';

                    } elseif ($resume === 'not_buy') {
                        echo '<div class="resume-preview-container position-relative" style="overflow:hidden;">
                            <iframe 
                                class="blurred-resume" 
                                src="../images/dummy-image.png" 
                                width="100%" 
                                height="300px" 
                                frameborder="0" 
                                title="Resume Preview (Locked)" 
                                aria-label="Resume Preview (Locked)">
                            </iframe>
                            <div class="lock-overlay">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>';
                    } else {
                        echo '<div class="resume-placeholder bg-light p-4 text-center">
                            <i class="fas fa-file-pdf fa-3x mb-2"></i>
                            <p>No resume available</p>
                        </div>';
                    }
                    ?>
                </div>

                <?php if ($button === 'already_buy') { ?>
                    <!-- Add to Cart Button -->
                    <div class="add-to-cart-section mt-3">
                        <?php if ($cart_exists): ?>
                            <button class="btn btn-sm add-to-cart-btn btn-success add-to-cart-btn-pop "
                                data-candidate-id="<?php echo $candidate_id; ?>" data-price="50" onclick="goToCart()">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z"
                                        fill="white" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z"
                                        fill="white" />
                                    <path
                                        d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z"
                                        fill="white" />
                                    <path
                                        d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z"
                                        fill="white" />
                                </svg>
                                Go to Cart
                            </button>
                        <?php else: ?>
                            <button class="btn btn-sm add-to-cart-btn btn-success add-to-cart-btn-pop"
                                data-candidate-id="<?php echo $candidate_id; ?>" data-price="50"
                                onclick="addToCart(<?php echo $candidate_id; ?>, 50)">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z"
                                        fill="white" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z"
                                        fill="white" />
                                    <path
                                        d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z"
                                        fill="white" />
                                    <path
                                        d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z"
                                        fill="white" />
                                </svg>
                                Add to Cart
                            </button>
                        <?php endif; ?>
                    </div>

                <?php } ?>
                <div class="mt-3 p-2 main-div-active">
                    <?php if (!empty($lastActive)): ?>
                        <span class="text-pop-active">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_2288_7080)">
                                    <path
                                        d="M8.00065 14.6667C4.31865 14.6667 1.33398 11.682 1.33398 8.00004C1.33398 4.31804 4.31865 1.33337 8.00065 1.33337C10.986 1.33337 13.4847 3.29537 14.334 6.00004H12.6673"
                                        stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M8 5.33337V8.00004L9.33333 9.33337M14.6367 8.66671C14.6567 8.44671 14.6667 8.22448 14.6667 8.00004M10 14.6667C10.2267 14.5916 10.4493 14.5044 10.6667 14.4054M13.86 11.3334C13.9893 11.0854 14.1038 10.8289 14.2033 10.564M12.128 13.486C12.3582 13.2958 12.5753 13.0909 12.7793 12.8714"
                                        stroke="#888888" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_2288_7080">
                                        <rect width="16" height="16" fill="white" />
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