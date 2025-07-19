<?php
include '../db/connection.php';

if (isset($_POST['id'])) {
    $job_id = intval($_POST['id']);
    $mobile_number = isset($_POST['mobile_number']) ? $_POST['mobile_number'] : "";

    // Fetch candidate_id and partner_id
    $sql = "SELECT user_id AS candidate_id, associate_id FROM candidates WHERE mobile_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mobile_number);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $candidateId = $row["candidate_id"];
    $partnerId = $row["associate_id"];
    // Check if the user has already applied for this job
    $sql = "SELECT id FROM jobs_applied WHERE job_id = ? AND candidate_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $job_id, $candidateId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $job_apllyed = 1;
    } else {
        $job_apllyed = 0;
    }
    // Prepare and execute query
    $query = "
    SELECT 
    job_id.id, 
    job_id.jobrole, 
    job_id.companyname,
    job_id.age,
    job_id.gender,
    job_id.experience,
    job_id.salary,
    
    departments.department_name,
    sub_departments.sub_department_name,
    departments_category.category,
    products.product_name,
    sub_products.sub_product_name,
    products_specialization.specialization,

    job_id.role_overview,

    locations.area, 
    locations.city, 
    locations.state
FROM 
    job_id
LEFT JOIN departments ON job_id.department = departments.department_id
LEFT JOIN sub_departments ON job_id.sub_department = sub_departments.sub_department_id
LEFT JOIN departments_category ON job_id.category = departments_category.category_id
LEFT JOIN products ON job_id.product = products.product_id
LEFT JOIN sub_products ON job_id.sub_product = sub_products.sub_product_id
LEFT JOIN products_specialization ON job_id.specialization = products_specialization.specialization_id
LEFT JOIN locations ON job_id.location_code = locations.id
WHERE 
    job_id.id = ?

";


    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $jobs = '';

        while ($row = $result->fetch_assoc()) {

            $jobId = htmlspecialchars($row['id']);
            $companyName = htmlspecialchars($row['companyname']);

            $age = $row['age'];
            $gender = $row['gender'];
            $experience = $row['experience'];
            $department = $row['department_name'];
            $product = $row['product_name'];
            $sub_department = $row['sub_department_name'];
            $sub_product = $row['sub_product_name'];
            $specialization = $row['specialization'];
            $category = $row['category'];

            $role_overview = $row['role_overview'];
            $words = explode(" ", $companyName);
            $initials = strtoupper(substr($words[0], 0, 1));
            if (count($words) > 1) {
                $initials .= strtoupper(substr($words[1], 0, 1));
            }
            function truncateName($name)
            {
                return (strlen($name) > 15) ? substr($name, 0, 12) . '...' : $name;
            }

            function truncateText($text)
            {
                return (strlen($text) > 15) ? substr($text, 0, 12) . '...' : $text;
            }

            $salary = $row['salary'] . " LPA";
            $experience = $row['experience'] . " yrs";
            $location = $row['area'] . ", " . $row['city'];

            // Append job details
            $jobs .= '
            <input id="JobID" type="hidden" value="' . $job_id . '">
            <div class="job-card shadow-none" data-id="' . $jobId . '" style="padding:0 15px;">
                <div class="job-card-container" data-id="' . $jobId . '">
                    <div class="job-card-logo col-2">
                        <div class="job-logo">
                            <span class="company-initials">' . $initials . '</span>
                        </div>
                    </div>
                    <div class="job-card-details text-start col-9">
                        <h5 class="job-role pt-3 ps-2" style="    color: #175DA8;">' . htmlspecialchars($row['jobrole']) . '</h5>
                    </div>
                    <div class="job-card-matched col-1">
                    </div>
                </div>
                <div class="job-additional-description">
                    <div class="job-details mt-2">
                         <p class="more-details"><img src="/images/ph_building-fill.svg" alt="Employer"> ' . $companyName . '</p>
                        
                        <div class="job-meta">
                             <span class="requirement-details"><img src=/images/uis_calender.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong>Age:</strong> ' . htmlspecialchars($row['age']) . ' yrs</span> <span class="devider-line">|</span>
                            <span class="requirement-details"><img src=/images/icons8_gender.svg  width="16" height="16" style="margin: 0 4px 4px 0;"><strong>Gender:</strong> ' . htmlspecialchars($row['gender']) . '</span> </span> 
                           
                        </div>
                        <div class="job-meta">
                           <span class="requirement-details"><img src=/images/basil_bag-solid-c.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> <strong>Experience:</strong> ' . htmlspecialchars($row['experience']) . ' yrs</span> <span class="devider-line">|</span>
                            <span class="requirement-details"><img src=/images/ri_money-rupee-circle-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"><strong>Salary:</strong> ' . htmlspecialchars($row['salary']) . '</span> </span> 
                           
                   
                        </div>
                        
                        
                     
                <p class="requirement-details mb-2 popup-location"><strong class="pe-2"><img src=/images/weui_location-filled.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Loc:</strong>' . htmlspecialchars($location) . '</p>

                      


                <p class="requirement-details mb-2 popup-p"><strong class="pe-2"><img src=/images/mingcute_department-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Department: </strong>' . htmlspecialchars($department) . '</p>
                <p class="requirement-details mb-2 popup-p"><strong class="pe-2"><img src=/images/mingcute_department-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Sub Department :</strong>  ' . htmlspecialchars($sub_department) . '</p>
                <p class="requirement-details mb-2 popup-p"><strong class="pe-2"><img src=/images/mingcute_department-fill.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Category :</strong>  ' . htmlspecialchars($category) . '</p>
                <p class="requirement-details mb-2 popup-p"><strong class="pe-2"><img src=/images/mdi_cash.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Product :</strong>  ' . htmlspecialchars($product) . '</p>
                <p class="requirement-details mb-2 popup-p"><strong class="pe-2"><img src=/images/mdi_cash.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Sub product :</strong>  ' . htmlspecialchars($sub_product) . '</p>
                <p class="requirement-details"><strong class="pe-2"><img src=/images/mdi_cash.svg  width="16" height="16" style="margin: 0 4px 4px 0;"> Specialization: </strong> ' . htmlspecialchars($specialization) . '</p>
                        


                      
                    </div>
                    <div class="job-description-container text-start mt-3">
                        <h4 class="job-description-title"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M13.9643 8.17458C13.963 8.15343 13.9566 8.13291 13.9457 8.11479C13.9347 8.09667 13.9194 8.08148 13.9013 8.07054C13.8832 8.0596 13.8626 8.05323 13.8415 8.05199C13.8203 8.05074 13.7992 8.05464 13.7799 8.06337C10.1353 9.67727 5.86344 9.67727 2.21891 8.06337C2.19961 8.05464 2.17846 8.05074 2.15731 8.05199C2.13616 8.05323 2.11562 8.0596 2.09748 8.07054C2.07933 8.08148 2.06411 8.09667 2.05313 8.11479C2.04214 8.13291 2.03573 8.15343 2.03443 8.17458C1.96817 9.42703 2.0355 10.683 2.23527 11.9212C2.29625 12.2983 2.48195 12.6441 2.76268 12.9033C3.04341 13.1624 3.40293 13.3199 3.78375 13.3506L5.00841 13.4487C6.99912 13.6096 8.999 13.6096 10.9904 13.4487L12.215 13.3506C12.5958 13.3199 12.9554 13.1624 13.2361 12.9033C13.5168 12.6441 13.7025 12.2983 13.7635 11.9212C13.9637 10.6815 14.0317 9.42541 13.9643 8.17524" fill="#175DA8"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.89097 4.77962V3.85066C4.89106 3.57657 4.98949 3.31161 5.16836 3.10392C5.34723 2.89624 5.59467 2.75963 5.86572 2.7189L6.66384 2.59919C7.54863 2.46694 8.44817 2.46694 9.33296 2.59919L10.1311 2.7189C10.4022 2.75964 10.6498 2.89635 10.8287 3.10417C11.0075 3.31199 11.1059 3.57711 11.1058 3.85132V4.78027L12.214 4.8699C12.5948 4.90054 12.9542 5.05795 13.2349 5.31696C13.5156 5.57597 13.7014 5.92163 13.7625 6.29866C13.7896 6.4673 13.8143 6.63632 13.8364 6.80566C13.8416 6.84654 13.8337 6.88799 13.814 6.92415C13.7943 6.96031 13.7637 6.98935 13.7265 7.00716L13.6762 7.03071C10.1252 8.71199 5.87226 8.71199 2.32063 7.03071L2.27026 7.00716C2.23299 6.98946 2.20225 6.96046 2.1824 6.92429C2.16256 6.88812 2.15461 6.84661 2.1597 6.80566C2.18238 6.63645 2.20724 6.46745 2.23428 6.29866C2.29539 5.92163 2.48116 5.57597 2.76187 5.31696C3.04259 5.05795 3.40204 4.90054 3.78276 4.8699L4.89097 4.77962ZM6.80973 3.56936C7.5978 3.45165 8.399 3.45165 9.18707 3.56936L9.98519 3.68908C10.0239 3.69487 10.0593 3.71437 10.0848 3.74402C10.1104 3.77367 10.1245 3.81151 10.1245 3.85066V4.71093C8.70827 4.63005 7.28853 4.63005 5.87226 4.71093V3.85066C5.8723 3.81151 5.88639 3.77367 5.91196 3.74402C5.93753 3.71437 5.97289 3.69487 6.01161 3.68908L6.80973 3.56936ZM7.99665 7.54071C8.33465 7.54071 8.60864 7.26671 8.60864 6.92871C8.60864 6.59071 8.33465 6.31671 7.99665 6.31671C7.65865 6.31671 7.38465 6.59071 7.38465 6.92871C7.38465 7.26671 7.65865 7.54071 7.99665 7.54071Z" fill="#175DA8"/>
                        </svg> Job Description</h4>
                        <p class="company-name text-start"> ' . $role_overview . '</p>

                    </div>
                </div>    
            </div>';
        }
        echo $jobs; // Return all job cards

        if ($job_apllyed == 1) {
            echo '<style>
        .not_apply_btn{
            display: none
        }
      </style>';
        } elseif ($job_apllyed == 0) {
            echo '<style>
            .apply_btn{
                display: none
            }
          </style>';
        }
        ;
    } else {
        echo "<p class='text-danger'>No job found.</p>";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "<p class='text-danger'>Invalid request.</p>";
}
?>