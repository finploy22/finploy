<?php
include '../db/connection.php';

if (isset($_POST['id'])) {
    $user_id = intval($_POST['id']);

    // Prepare and execute query
    $query = "SELECT 
    candidate_details.*, 
    locations.area, 
    locations.city, 
    locations.state 
    FROM candidate_details 
    LEFT JOIN locations 
        ON candidate_details.current_location = locations.id 
    WHERE user_id = ?
    ";
                  
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Extract candidate details
        $id = htmlspecialchars($row['id']);
        $username = htmlspecialchars($row['username']);
        $mobile_number = htmlspecialchars($row['mobile_number']);
        $gender = htmlspecialchars($row['gender']);
        $work_experience = htmlspecialchars($row['work_experience']);
        $current_salary = htmlspecialchars($row['current_salary']);
        $current_location = htmlspecialchars($row['current_location']);
        $current_company = htmlspecialchars($row['current_company']);
        $jobrole = htmlspecialchars($row['destination']);
        $employed = htmlspecialchars($row['employed']);
        $personal_loan = htmlspecialchars($row['personal_loan']);
        $Sales = htmlspecialchars($row['Sales']);
        $resume = htmlspecialchars($row['resume']);






   
        
            $dept_ids = $row['departments'];
            $sub_department_ids = $row['sub_departments'];
            $product_ids = $row['products'];
            $sub_product_ids = $row['sub_products'];
            
            $specialization_ids = $row['specialization'];
            $category_ids = $row['category'];
            
            // Fetch departments
            $department_query = "SELECT department_name FROM departments WHERE department_id IN ($dept_ids)";
            $dept_result = mysqli_query($conn, $department_query);
            $departments = [];
            while ($dept_row = mysqli_fetch_assoc($dept_result)) {
                $departments[] = $dept_row['department_name'];
            }
            
            $sub_department_query = "SELECT sub_department_name FROM sub_departments WHERE sub_department_id IN ($sub_department_ids)";
            $sub_dept_result = mysqli_query($conn, $sub_department_query);
            $sub_departments = [];
            while ($sub_dept_row = mysqli_fetch_assoc($sub_dept_result)) {
                $sub_departments[] = $sub_dept_row['sub_department_name'];
            }
            
            // Fetch products
            $product_query = "SELECT product_name FROM products WHERE product_id IN ($product_ids)";
            $product_result = mysqli_query($conn, $product_query);
            $products = [];
            while ($prod_row = mysqli_fetch_assoc($product_result)) {
                $products[] = $prod_row['product_name'];
            }
            
            $sub_product_query = "SELECT sub_product_name FROM sub_products WHERE sub_product_id IN ($sub_product_ids)";
            $sub_product_result = mysqli_query($conn, $sub_product_query);
            $sub_products = [];
            while ($sub_prod_row = mysqli_fetch_assoc($sub_product_result)) {
                $sub_products[] = $sub_prod_row['sub_product_name'];
            }
            
            
            $departmentStr =!empty($departments) ? implode(", ", $departments) : "N/A";
            $sub_departmentStr =!empty($sub_departments) ? implode(", ", $sub_departments) : "N/A";
            $productStr =!empty($products) ? implode(", ", $products) : "N/A";
            $sub_productStr =!empty($sub_products) ? implode(", ", $sub_products) : "N/A";
            
            
            
            
            
            $specialization_query = "SELECT specialization FROM products_specialization WHERE specialization_id IN ($specialization_ids)";
            $specialization_result = mysqli_query($conn, $specialization_query);
            $specialization = [];
            while ($specialization_row = mysqli_fetch_assoc($specialization_result)) {
                $specialization[] = $specialization_row['specialization'];
            }
            
            $category_query = "SELECT category FROM departments_category WHERE category_id IN ($category_ids)";
            $category_result = mysqli_query($conn, $category_query);
            $categorys = [];
            while ($category_row = mysqli_fetch_assoc($category_result)) {
                $categorys[] = $category_row['category'];
            }
            
            $specializationStr =!empty($specialization) ? implode(", ", $specialization) : "N/A";
            $categoryStr =!empty($categorys) ? implode(", ", $categorys) : "N/A";
                        
                








        // Get initials for the avatar
        $initials = '';
        $name_parts = explode(' ', $username);
        foreach ($name_parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        
        // Format age from username (assuming age is embedded or available)
        // For demonstration, using a placeholder - replace with actual logic if needed
        $age = 23; // Example: This should be extracted or calculated as needed
        
        // Output the candidate profile UI
        echo '
        <div class="candidate-profile">
            <div class="profile-header d-flex align-items-center">
                <div class="avatar-circle">
                    <span class="initials">' . $initials . '</span>
                </div>
                
                <p class="mb-1 ms-2">
                    <button class="dotted-border-btn copy-btn" data-value="' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '" title="Copy">
                        <span>' . htmlspecialchars(ucfirst($username), ENT_QUOTES, 'UTF-8') . '</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                          <path d="M9.48829 15.417C9.32485 15.417 8.41338 15.417 7.73794 15.417C7.32372 15.417 6.98829 15.0812 6.98829 14.667V13.542V6.66699H4.98829C4.85568 6.66699 4.72851 6.71638 4.63474 6.80429C4.54097 6.89219 4.48829 7.01142 4.48829 7.13574L4.48828 15.917C4.48828 17.0216 5.38371 17.917 6.48828 17.917H13.4883C13.7535 17.917 14.0079 17.8182 14.1954 17.6424C14.3829 17.4666 14.4883 17.2281 14.4883 16.9795V15.417H9.48829Z" fill="#175DA8"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9683 1.66846H9.20177C8.67073 1.66846 8.24023 2.09895 8.24023 2.63V13.2069C8.24023 13.738 8.67073 14.1685 9.20177 14.1685H16.4133C16.9444 14.1685 17.3748 13.738 17.3748 13.2069V6.07504C17.3748 6.02403 17.3546 5.97512 17.3185 5.93905L13.1043 1.72478C13.0682 1.68872 13.0193 1.66846 12.9683 1.66846ZM16.8919 6.47677H13.0457V2.63062L16.8919 6.47677Z" fill="#175DA8"/>
                        </svg>
                    </button>
                    &nbsp; <span class="separator">|</span> &nbsp;
                
                    <button class="dotted-border-btn copy-btn" data-value="' . htmlspecialchars($mobile_number, ENT_QUOTES, 'UTF-8') . '" title="Copy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                          <path d="M17.4069 12.9167C16.3652 12.9167 15.3652 12.75 14.4319 12.4417C14.2854 12.395 14.1289 12.389 13.9793 12.4242C13.8296 12.4594 13.6922 12.5346 13.5819 12.6417L11.7486 14.475C9.38306 13.2716 7.46029 11.3488 6.2569 8.98333L8.09023 7.14167C8.20061 7.03695 8.27883 6.90293 8.31573 6.75533C8.35263 6.60773 8.34668 6.45267 8.29857 6.30833C7.983 5.34824 7.82265 4.34395 7.82357 3.33333C7.82357 2.875 7.44857 2.5 6.99023 2.5H4.07357C3.61523 2.5 3.24023 2.875 3.24023 3.33333C3.24023 11.1583 9.5819 17.5 17.4069 17.5C17.8652 17.5 18.2402 17.125 18.2402 16.6667V13.75C18.2402 13.2917 17.8652 12.9167 17.4069 12.9167ZM16.5736 10H18.2402C18.2402 8.01088 17.4501 6.10322 16.0435 4.6967C14.637 3.29018 12.7294 2.5 10.7402 2.5V4.16667C13.9652 4.16667 16.5736 6.775 16.5736 10ZM13.2402 10H14.9069C14.9069 7.7 13.0402 5.83333 10.7402 5.83333V7.5C12.1236 7.5 13.2402 8.61667 13.2402 10Z" fill="#175DA8"/>
                        </svg> <span>' . htmlspecialchars($mobile_number, ENT_QUOTES, 'UTF-8') . '</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                          <path d="M9.48829 15.417C9.32485 15.417 8.41338 15.417 7.73794 15.417C7.32372 15.417 6.98829 15.0812 6.98829 14.667V13.542V6.66699H4.98829C4.85568 6.66699 4.72851 6.71638 4.63474 6.80429C4.54097 6.89219 4.48829 7.01142 4.48829 7.13574L4.48828 15.917C4.48828 17.0216 5.38371 17.917 6.48828 17.917H13.4883C13.7535 17.917 14.0079 17.8182 14.1954 17.6424C14.3829 17.4666 14.4883 17.2281 14.4883 16.9795V15.417H9.48829Z" fill="#175DA8"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9683 1.66846H9.20177C8.67073 1.66846 8.24023 2.09895 8.24023 2.63V13.2069C8.24023 13.738 8.67073 14.1685 9.20177 14.1685H16.4133C16.9444 14.1685 17.3748 13.738 17.3748 13.2069V6.07504C17.3748 6.02403 17.3546 5.97512 17.3185 5.93905L13.1043 1.72478C13.0682 1.68872 13.0193 1.66846 12.9683 1.66846ZM16.8919 6.47677H13.0457V2.63062L16.8919 6.47677Z" fill="#175DA8"/>
                        </svg>
                    </button>
                </p>
            </div>
            
            <div class="candidate-details mt-3">
                
                <p class="mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                      <path d="M8.739 7.94285C10.3801 7.94285 11.7104 6.6125 11.7104 4.97143C11.7104 3.33035 10.3801 2 8.739 2C7.09793 2 5.76758 3.33035 5.76758 4.97143C5.76758 6.6125 7.09793 7.94285 8.739 7.94285Z" fill="#175DA8"/>
                      <path d="M8.73906 15.0002C11.6109 15.0002 13.9391 13.6699 13.9391 12.0288C13.9391 10.3877 11.6109 9.05737 8.73906 9.05737C5.86718 9.05737 3.53906 10.3877 3.53906 12.0288C3.53906 13.6699 5.86718 15.0002 8.73906 15.0002Z" fill="#175DA8"/>
                    </svg> <span>' . htmlspecialchars(ucfirst($gender), ENT_QUOTES, 'UTF-8') . '</span>
                    &nbsp; <span class="separator">|</span> &nbsp; 
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                      <path d="M14.7046 8.67446C14.7033 8.65331 14.6969 8.63279 14.6859 8.61467C14.6749 8.59655 14.6597 8.58136 14.6415 8.57042C14.6234 8.55948 14.6029 8.55311 14.5817 8.55186C14.5605 8.55061 14.5394 8.55452 14.5201 8.56325C10.8756 10.1771 6.60367 10.1771 2.95915 8.56325C2.93984 8.55452 2.9187 8.55061 2.89755 8.55186C2.87639 8.55311 2.85586 8.55948 2.83771 8.57042C2.81956 8.58136 2.80434 8.59655 2.79336 8.61467C2.78238 8.63279 2.77596 8.65331 2.77467 8.67446C2.70841 9.92691 2.77573 11.1829 2.9755 12.421C3.03648 12.7982 3.22219 13.144 3.50291 13.4031C3.78364 13.6623 4.14317 13.8198 4.52399 13.8505L5.74864 13.9486C7.73936 14.1095 9.73923 14.1095 11.7306 13.9486L12.9553 13.8505C13.3361 13.8198 13.6956 13.6623 13.9763 13.4031C14.2571 13.144 14.4428 12.7982 14.5037 12.421C14.7039 11.1813 14.772 9.92528 14.7046 8.67512" fill="#175DA8"/>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M5.6312 5.27962V4.35066C5.6313 4.07657 5.72972 3.81161 5.90859 3.60392C6.08746 3.39624 6.33491 3.25963 6.60596 3.2189L7.40408 3.09919C8.28887 2.96694 9.1884 2.96694 10.0732 3.09919L10.8713 3.2189C11.1425 3.25964 11.39 3.39635 11.5689 3.60417C11.7478 3.81199 11.8461 4.07711 11.8461 4.35132V5.28027L12.9543 5.3699C13.335 5.40054 13.6944 5.55795 13.9752 5.81696C14.2559 6.07597 14.4416 6.42163 14.5028 6.79866C14.5298 6.9673 14.5545 7.13632 14.5767 7.30566C14.5818 7.34654 14.574 7.38799 14.5543 7.42415C14.5345 7.46031 14.5039 7.48935 14.4668 7.50716L14.4164 7.53071C10.8654 9.21199 6.6125 9.21199 3.06087 7.53071L3.0105 7.50716C2.97323 7.48946 2.94248 7.46046 2.92264 7.42429C2.90279 7.38812 2.89485 7.34661 2.89994 7.30566C2.92262 7.13645 2.94748 6.96745 2.97452 6.79866C3.03563 6.42163 3.22139 6.07597 3.5021 5.81696C3.78282 5.55795 4.14227 5.40054 4.523 5.3699L5.6312 5.27962ZM7.54996 4.06936C8.33804 3.95165 9.13923 3.95165 9.92731 4.06936L10.7254 4.18908C10.7642 4.19487 10.7995 4.21437 10.8251 4.24402C10.8506 4.27367 10.8647 4.31151 10.8648 4.35066V5.21093C9.4485 5.13005 8.02877 5.13005 6.6125 5.21093V4.35066C6.61254 4.31151 6.62662 4.27367 6.65219 4.24402C6.67776 4.21437 6.71312 4.19487 6.75184 4.18908L7.54996 4.06936ZM8.73688 8.04071C9.07488 8.04071 9.34888 7.76671 9.34888 7.42871C9.34888 7.09071 9.07488 6.81671 8.73688 6.81671C8.39889 6.81671 8.12489 7.09071 8.12489 7.42871C8.12489 7.76671 8.39889 8.04071 8.73688 8.04071Z" fill="#175DA8"/>
                    </svg> 
                    <span>' . htmlspecialchars($work_experience, ENT_QUOTES, 'UTF-8') . '</span>
                    &nbsp; <span class="separator">|</span> &nbsp; 
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                      <path d="M8.74121 15.4467C12.692 15.4467 15.8945 12.2441 15.8945 8.29333C15.8945 4.34256 12.692 1.14001 8.74121 1.14001C4.79043 1.14001 1.58789 4.34256 1.58789 8.29333C1.58789 12.2441 4.79043 15.4467 8.74121 15.4467ZM9.81421 5.43201C10.0488 5.74389 10.2169 6.10871 10.2971 6.505H11.6025V7.578H10.2971C10.1734 8.18391 9.84416 8.72847 9.36511 9.11953C8.88606 9.51059 8.28661 9.72412 7.66821 9.724H7.5323L10.1933 12.385L9.43508 13.1433L5.87988 9.58809V8.651H7.66821C8.00113 8.6511 8.32589 8.54796 8.59774 8.35579C8.8696 8.16362 9.07517 7.89188 9.18615 7.578H5.87988V6.505H9.18615C9.07517 6.19113 8.8696 5.91938 8.59774 5.72721C8.32589 5.53505 8.00113 5.43191 7.66821 5.43201H5.87988V4.35901H11.6025V5.43201H9.81421Z" fill="#175DA8"/>
                    </svg> <span>₹ ' . htmlspecialchars($current_salary, ENT_QUOTES, 'UTF-8') . '</span> LPA
                    &nbsp; <span class="separator">|</span> &nbsp; 
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M8.24758 15.256C8.24758 15.256 3.40625 11.1787 3.40625 7.16671C3.40625 5.75222 3.96815 4.39567 4.96835 3.39547C5.96854 2.39528 7.3251 1.83337 8.73958 1.83337C10.1541 1.83337 11.5106 2.39528 12.5108 3.39547C13.511 4.39567 14.0729 5.75222 14.0729 7.16671C14.0729 11.1787 9.23158 15.256 9.23158 15.256C8.96225 15.504 8.51892 15.5014 8.24758 15.256ZM8.73958 9.50004C9.046 9.50004 9.34942 9.43969 9.63251 9.32243C9.9156 9.20517 10.1728 9.03329 10.3895 8.81662C10.6062 8.59995 10.778 8.34273 10.8953 8.05964C11.0126 7.77654 11.0729 7.47313 11.0729 7.16671C11.0729 6.86029 11.0126 6.55687 10.8953 6.27378C10.778 5.99069 10.6062 5.73346 10.3895 5.51679C10.1728 5.30012 9.9156 5.12825 9.63251 5.01099C9.34942 4.89373 9.046 4.83337 8.73958 4.83337C8.12075 4.83337 7.52725 5.07921 7.08967 5.51679C6.65208 5.95438 6.40625 6.54787 6.40625 7.16671C6.40625 7.78555 6.65208 8.37904 7.08967 8.81662C7.52725 9.25421 8.12075 9.50004 8.73958 9.50004Z" fill="#175DA8"/>
                    </svg> 
                    <span>' . htmlspecialchars(ucfirst($row['area']." ,".$row['city']), ENT_QUOTES, 'UTF-8') . '</span>
                </p>
            </div>
            
            <div class="action-button my-3">
                <button class="btn btn-outline-danger reject-btn">
                    <i class="fas fa-times"></i> Reject
                </button>
                <button class="btn btn-outline-success shortlist-btn">
                    <i class="fas fa-check"></i> Shortlist
                </button>
            </div>
            
            <div class="candidate-description">
                <h5>Candidate Description</h5>
                
                <div class="description-item">
                    <i class="fas fa-building"></i>
                    <span>Company: </span>
                    <a href="#" class="company-link">' . htmlspecialchars(ucfirst($current_company), ENT_QUOTES, 'UTF-8') . '</a>
                </div>
                
                <div class="description-item">
                    <i class="fas fa-id-badge"></i>
                    <span>Role: </span>
                    <span class="role-text">' . htmlspecialchars(ucfirst($jobrole), ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                
                <div class="description-item">
                    <i class="fas fa-info-circle"></i>
                    <span>Additional Details:</span>
                </div>
                
                <div class="additional-details-list">
                    <p>
                        <span>Current Employment Status: </span>
                        <span class="status-value">' . ($employed == 'Yes' ? 'Yes' : 'No') . '</span>
                    </p>
                    <p>
                        <span>Experience in Bank / NBFC: </span>
                        <span class="status-value">No</span>
                    </p>
                    <p>
                        <span>Experience in Banking Product: </span>
                        <span class="status-value">' . ($productStr) . '</span>
                    </p>
                    <p>
                        <span>Experience in Department: </span>
                        <span class="status-value">' . ($departmentStr) . '</span>
                    </p>
                </div>
            </div>';
            
            if (!empty($resume) && file_exists(__DIR__ . '/../uploads/resumes/' . $resume)) {
                
                $pdfPath = 'uploads/resumes/' . htmlspecialchars($resume, ENT_QUOTES, 'UTF-8');
            
                echo '<div class="">
                            <iframe 
                                src="/../uploads/resumes/'. $resume .'" 
                                width="100%" 
                                height="300px" 
                                frameborder="0">
                            </iframe>
                          </div>';
            }elseif(!empty($resume)){
                        echo '<div class="resume-preview-container">
                            <iframe 
                                src="' . $resume . '" 
                                width="100%" 
                                height="300px" 
                                frameborder="0">
                            </iframe>
                        </div>';
                
            }else {
                echo '<div class="resume-placeholder bg-light p-4 text-center">
                            <i class="fas fa-file-pdf fa-3x text-muted mb-2"></i>
                            <p class="text-muted">No resume available</p>
                          </div>';
            }
            
            echo '</div> ';
    } else {
        echo "<p class='text-danger'>No candidate found.</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p class='text-danger'>Invalid request.</p>";
}
?>