<?php
session_start(); 
// Uncomment if session check is required
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="/css/candidate.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
          .job-card {
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: white;
            width: 100%;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            align-items: center;
            border: none;
            margin-bottom: 10px;
        }

        .job-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 0;
        }

        .job-logo {
            width: 50px;
            height: 50px;
            background: #eef2ff;
            color: #1a3ca6;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            margin-right: 10px;
        }

        .job-role {
            flex-grow: 1;
            font-size: 16px;
            font-weight: bold;
        }

        .job-matched-btn {
            background: #e7f3ff;
            color: #0066cc;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }

        .job-details {
            width: 100%;
        }

        .job-meta {
            display: flex;
            gap: 15px;
            font-size: 14px;
        }

        .job-meta img {
            width: 14px;
            height: 14px;
        }

        .job-role {
            color: #000;
            font-family: Poppins;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }
        .filter-header {
            color: var(--Deeesha-Blue, #175DA8);
            font-family: Poppins;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }
        .jobs-page-title{
            color: var(--Deeesha-Blue, #175DA8);
            font-family: Poppins;
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }
        .filters-section{
            padding: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .filters-section h6{
            color: #000;
            font-family: 'Poppins';
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }
        hr{
            background: #B2B2B2;
        }
        .no-jobs-image{
            height: 200px;
            width: 300px;
        }


        .filters-section {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto; /* Enable horizontal scrolling */
        -ms-overflow-style: none; /* Hide scrollbar in IE/Edge */
        scrollbar-width: none; 
    }

    .filters-section select,
    .filters-section input,
    .filters-section button {
        flex-shrink: 0; /* Prevent items from shrinking */
        font-size: 12px; /* Larger font size for readability */
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }

    /* Customize horizontal scrollbar */
    .filters-section::-webkit-scrollbar {
        height: 8px; /* Slightly larger scrollbar */
    }

    .filters-section::-webkit-scrollbar-thumb {
        background-color: #aaa;
        border-radius: 4px;
    }

    .filters-section::-webkit-scrollbar-track {
        background-color: #f5f5f5;
    }

    /* Add spacing between dropdown text and the dropdown arrow */
    .form-select {
        padding-right: 30px; 
    }
    .notify-btn{
        margin-right: 10px;
    }

    .popup-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .popup-content {
        background: white;
        padding: 20px;
        width: 400px;
        border-radius: 10px;
        text-align: center;
        position: relative;
    }

    .close-popup {
        cursor: pointer;
        font-size: 20px;
        position: absolute;
        top: 10px; left: 15px;
    }
    .job-description-title{
        color: #000;
        font-family: 'Poppins';
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }
    .job-description-helptext{
        color: var(--Deeesha-Blue, #175DA8);
        font-family: 'Poppins';
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }
        
        @media screen and (max-width: 768px) {
            .job-logo {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            #job-listing-web {
                display: none !important;
            }
            #job-listing-mobile {
                display: block !important;
            }
            .job-matched-btn{
                padding: 5px 5px;
            }
            .col-md-3{
                padding: 0;
            }
            .col-md-9{
                padding: 0;
            }
            .job-card{
                margin: 10px 0px;
            }
            #filters-section-mobile{
                display: block ;
            }
            #filters-section-web{
                display: none !important;
            }
            .jobs-page-title{
                font-size: 20px;
            }
            .job-description-title{
                font-size: 13px;
            }
            .job-description-helptext{
                font-size: 12px;
            }
        }
        @media screen and (min-width: 769px) {
            #job-listing-web {
                display: block !important;
            }
            #job-listing-mobile {
                display: none !important;
            }
            #filters-section-mobile{
                display: none !important;
            }
            #filters-section-web{
                display: block !important;
            }
        }
        @media screen and (max-width: 400px) {
            .job-logo {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }
            .job-role {
                font-size: 12px;
            }
            .job-matched-btn{
                font-size: 0;
            }
            .company-name {
                font-size: 12px;
                padding: 0 10px;
            }
            .salary-icon{
                font-size: 12px;
                padding: 0 10px;
            }
            .location-icon{
                font-size: 12px;
            }
        } 
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4 mb-5">
        <div class="job-liting-page">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="jobs-page-title">Showing All Jobs</h5>
                <div class="notify-btn text-end position-relative mt-2 mt-md-0" onclick="toggleDropdown()">
                    <svg class="bell-icon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 52 53" fill="none">
                        <g filter="url(#filter0_d_1800_1522)">
                            <rect x="6" y="6" width="40" height="40" rx="8.66071" fill="white"/>
                            <path d="M34.3084 35.8887L34.0099 36.9909L13.9622 31.6746L14.2606 30.5723L17.085 28.9584L18.8756 22.3447C19.8008 18.9277 22.8764 16.518 26.4469 16.4252L26.5335 16.1055C26.6918 15.5209 27.0783 15.0224 27.6079 14.7197C28.1376 14.417 28.7671 14.335 29.3579 14.4917C29.9486 14.6484 30.4523 15.0309 30.7581 15.5551C31.0639 16.0793 31.1468 16.7023 30.9885 17.287L30.902 17.6066C33.9472 19.4538 35.3934 23.0626 34.4683 26.4797L32.6777 33.0934L34.3084 35.8887ZM25.9152 36.0257C25.7569 36.6104 25.3704 37.1089 24.8407 37.4116C24.311 37.7142 23.6816 37.7963 23.0908 37.6396C22.5 37.4829 21.9963 37.1004 21.6905 36.5762C21.3847 36.052 21.3018 35.429 21.4601 34.8443" fill="#175DA8"/>
                        </g>
                        <defs>
                            <filter id="filter0_d_1800_1522" x="0.226191" y="0.947917" width="51.5476" height="51.5476" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                            <feOffset dy="0.721726"/>
                            <feGaussianBlur stdDeviation="2.8869"/>
                            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.24 0"/>
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1800_1522"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1800_1522" result="shape"/>
                            </filter>
                        </defs>
                    </svg>
                    <span id="notification-count" class="notification-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;" onclick="toggleDropdown()">
                        0<?php //echo $selected_count; ?>
                    </span>
                    <div id="notificationDropdown" class="dropdown-content position-absolute bg-white shadow p-3" style="display: none; width: 328px; right: 0; top: 50px; border-radius: 8px; text-align: start; font-size: 13px;">
                        <h4 class="notification-header text-primary fw-bold p-2">Notifications (0<?php //echo $selected_count; ?>)</h4>
                        <ul class="list-unstyled mb-0">
                            <?php if ($selected_count > 0): ?>
                                <?php 
                                // Display only the first 4 notifications
                                $counter = 0;
                                foreach ($selected_candidates as $candidate): 
                                    if ($counter >= 3) break;
                                    $counter++;
                                ?>
                                    <li class="py-2 notification-list">
                                        You Referred a Candidate Secured a job - <strong>4<?php //echo ucfirst($candidate['name']); ?></strong>
                                        <br>
                                        <span class="notification-date">
                                            <?php echo date('M d, Y, h:i:s A', strtotime($candidate['updated_at'])); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="py-2 text-center">No Data Found</li>
                            <?php endif; ?>
                        </ul>
                        <!-- See All button -->
                        <div class="text-center mt-3">
                            <a href="notification_list.php" class="text-primary fw-bold text-decoration-none">See All Notification</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Filters Section -->
            <div class="col-md-3 filters-section" id="filters-section-web">
                <h5 class="filter-header"><img src="assets/filter-icon.svg" alt=""> All Filters</h5>

                <div class="mb-3">
                    <h6>Sort By</h6>
                    <select id="sort" class="form-select">
                        <option value="">Relevance</option>
                        <option value="salary_desc">Salary - High to Low</option>
                        <option value="date_desc">Date Posted - New to Old</option>
                    </select>
                </div>
                <hr>
                <div class="mb-3">
                    <h6>Department</h6>
                    <select id="department" class="form-select">
                        <option value="">All Departments</option>
                        <option value="Sales">Sales</option>
                        <option value="Credit">Credit</option>
                        <option value="Legal">Legal</option>
                        <option value="HR">HR</option>
                    </select>
                </div>
                <hr>
                <div class="mb-3">
                    <h6>Location</h6>
                    <select id="location" class="form-select">
                        <!-- <option value="">Select city</option>
                        <option value="Mumbai">Mumbai</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Bangalore">Bangalore</option> -->
                    </select>
                </div>
                <hr>
                <div class="mb-3">
                    <h6>Monthly Salary</h6>
                    <div class="input-group">
                        <input type="number" id="min_salary" class="form-control" placeholder="Min">
                        <input type="number" id="max_salary" class="form-control" placeholder="Max">
                    </div>
                </div>
            </div>

            <!-- FIlters for Mobile View -->
            <div class="filters-section d-flex align-items-center overflow-auto" style="white-space: nowrap; gap: 1.5rem;" id="filters-section-mobile">
                <!-- Filter Button -->
                <button class="btn btn-outline-primary d-flex align-items-center" style="width: 80px; height: 35px;">
                    <img src="assets/filter-icon.svg" alt="Filter Icon" class="me-2" style="width: 20px;"> Filter
                </button>

                <!-- Sort By Dropdown -->
                <select id="jobsort" class="form-select" style="width: 120px; height: 35px;">
                    <option value="">Sort By</option>
                    <option value="relevance">Relevance</option>
                    <option value="salary_desc">Salary - High to Low</option>
                    <option value="date_desc">Date Posted - New to Old</option>
                </select>

                <!-- Department Dropdown -->
                <select id="jobdepartment" class="form-select" style="width: 122px; height: 35px;">
                    <option value="">Department</option>
                    <option value="Sales">Sales</option>
                    <option value="Credit">Credit</option>
                    <option value="Legal">Legal</option>
                    <option value="HR">HR</option>
                </select>

                <!-- Location Dropdown -->
                <select id="joblocation" class="form-select" style="width: 120px; height: 35px;">
                    <!-- <option value="">Location</option>
                    <option value="Mumbai">Mumbai</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Bangalore">Bangalore</option> -->
                </select>

                <!-- Min Salary Input -->
                <input type="number" id="jobmin_salary" class="form-control" placeholder="Min Salary" style="width: 120px; height: 35px;">

                <!-- Max Salary Input -->
                <input type="number" id="jobmax_salary" class="form-control" placeholder="Max Salary" style="width: 120px; height: 35px;">
            </div>

            <!-- Job Listings Section -->
            <div class="col-md-9">
                <div id="job-list">
                    <!-- Job cards will be dynamically populated here -->
                </div>
            </div>
        </div>
    </div>

    <div id="jobDetailsPopup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            
            <div class="step" id="step-1">
                <div id="popupContent">Loading...</div>
                <button class="next-step btn btn-success w-100 mb-4">Apply for Job</button>
                <button class="btn btn-pimary border border-primary text-primary w-100 mb-4">Refer a candidate</button>
            </div>

            <div class="step" id="step-2" style="display: none;">
                <div class="mb-3 justify-center text-center">
                    <img class="login-logo" src="assets/finploy-logo.png" alt="FINPLOY" height="40">
                </div>
                <div class="text-center mt-5 mb-4">
                    <img src="assets/party.svg" alt="">
                    <h4 class="step-title mt-3">✨ Congratulations ✨</h4>
                    <p class="text-success mt-3">You have successfully applied for the job</p>
                </div>
                <a class="text-decoration-none text-light" href="landingpage.php"><button type="submit" class="btn btn-success w-100 mt-5 mb-5">Continue</button></a>
                <!-- <button class="prev-step">Back</button> -->
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Function to fetch jobs based on selected filters
        function fetchJobs() {
    // Create an empty object to store filters dynamically
    const filters = {};

    // Fetch values from both sets of filter elements
    const sortValue = $('#sort').val() || $('#jobsort').val();
    const locationValue = $('#location').val() || $('#joblocation').val();
    const minSalaryValue = $('#min_salary').val() || $('#jobmin_salary').val();
    const maxSalaryValue = $('#max_salary').val() || $('#jobmax_salary').val();
    const departmentValue = $('#department').val() || $('#jobdepartment').val();

    // Add values to the filters object only if they exist (not empty)
    if (sortValue) filters.sort = sortValue;
    if (locationValue) filters.location = locationValue;
    if (minSalaryValue) filters.min_salary = minSalaryValue;
    if (maxSalaryValue) filters.max_salary = maxSalaryValue;
    if (departmentValue) filters.department = departmentValue;

    console.log(filters); // Debugging: Check what filters are being sent

    // AJAX request to fetch jobs
    $.ajax({
        url: 'fetch_jobs.php',
        type: 'POST',
        data: filters, // Send only active filters
        success: function (response) {
            $('#job-list').html(response);
        },
        error: function () {
            $('#job-list').html('<p class="text-danger">Failed to load jobs. Please try again later.</p>');
        },
    });
}

// Fetch jobs on page load and on filter change
$(document).ready(function () {
    fetchJobs(); // Initial fetch

    // Update job list whenever a filter is changed
    $('#sort, #jobsort, #location, #joblocation, #min_salary, #jobmin_salary, #max_salary, #jobmax_salary, #department, #jobdepartment').on('change', fetchJobs);

    // Call AJAX when the user enters salary values
    $('#min_salary, #jobmin_salary, #max_salary, #jobmax_salary').on('input', function () {
        fetchJobs();
    });
});


/////////////////////////////////////// Toggle Notificaiton Dropdown ///////////////////////////////////////        
        function toggleDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

/////////////////////////////////////// fetch Location ///////////////////////////////////////
        $.ajax({
            url: 'fetch_locations.php',
            type: 'GET',
            success: function (locations) {
                console.log(locations);
                if (typeof locations === 'string') {
                    try {
                        locations = JSON.parse(locations);
                    } catch (e) {
                        console.error('Invalid JSON response:', locations);
                        return;
                    }
                }

                if (!Array.isArray(locations)) {
                    console.error('Response is not an array:', locations);
                    return;
                }

                // Sort locations alphabetically
                locations.sort();

                // Select both dropdowns
                const locationDropdown = $('#location');
                const jobLocationDropdown = $('#joblocation');

                // Empty existing options
                locationDropdown.empty();
                jobLocationDropdown.empty();

                // Add default option
                const defaultOption = '<option value="">Select city</option>';
                locationDropdown.append(defaultOption);
                jobLocationDropdown.append(defaultOption);

                // Populate both dropdowns
                locations.forEach(location => {
                    const option = `<option value="${location}">${location}</option>`;
                    locationDropdown.append(option);
                    jobLocationDropdown.append(option);
                });
            },
            error: function (xhr, status, error) {
                console.error('Failed to fetch locations:', error);
            },
        });


/////////////////////////////////////// fetch Full Jobs ////////////
        $(document).ready(function() {
            $(document).on("click", "#loadMoreBtn", function() {
                $.ajax({
                    url: "fetch_jobs.php",
                    type: "POST",
                    data: { load_more: true },
                    beforeSend: function() {
                        $("#loadMoreBtn").text("Loading...").prop("disabled", true);
                    },
                    success: function(response) {
                        $("#job-list").html(response); // Replace content with full job list
                    }
                });
            });
        });

        $(document).on("click", ".job-card", function () {
            // alert("Job card clicked!");
            let jobId = $(this).data("id"); // Get job ID
            $("#jobDetailsPopup").fadeIn(); // Show popup
console.log(jobId);
            // Fetch job details via AJAX if needed
            $.ajax({
                url: "get_job_details.php",
                type: "POST",
                data: { id: jobId },
                success: function (response) {
                    $("#popupContent").html(response); // Update popup content
                }
            });
        });
        $(".close-popup").on("click", function () {
                $("#jobDetailsPopup").fadeOut();
            });

            // Multi-step navigation
            $(".next-step").on("click", function () {
                $(".step").hide();
                $("#step-2").show();
            });

            $(".prev-step").on("click", function () {
                $(".step").hide();
                $("#step-1").show();
            });

    </script>
</body>
<?php include '../footer.php'; ?>
</html>
