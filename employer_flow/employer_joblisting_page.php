<?php
// Start session and check login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
    header("Location: ../index.php");
    exit();
}
$employer_name = $_SESSION['name'] ?? '';
$employer_mobile = $_SESSION['mobile'] ?? '';

include 'posting_header.php';
include '../db/connection.php';

?>

<link rel="stylesheet" href="./css/employer_joblisting_page.css">

<body>

    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center job-header-row">
                    <div class="job-title-container mb-3 mb-md-0">
                        <div class="main-all-job-text">All Jobs (<span id="jobCount">0</span>)</div>
                    </div>
                    <div class="search-container" id="search-container-web">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                            <path
                                d="M16.5674 16.1255L20.4141 20M18.6363 11.1111C18.6363 15.0385 15.4526 18.2222 11.5252 18.2222C7.59781 18.2222 4.41406 15.0385 4.41406 11.1111C4.41406 7.18375 7.59781 4 11.5252 4C15.4526 4 18.6363 7.18375 18.6363 11.1111Z"
                                stroke="#888888" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <input type="text" id="search" class="form-control" placeholder="Search here'"
                            autocomplete="off">
                    </div>
                    <div class="search-container p-2" id="search-icon-mobile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                            <path
                                d="M16.5674 16.1255L20.4141 20M18.6363 11.1111C18.6363 15.0385 15.4526 18.2222 11.5252 18.2222C7.59781 18.2222 4.41406 15.0385 4.41406 11.1111C4.41406 7.18375 7.59781 4 11.5252 4C15.4526 4 18.6363 7.18375 18.6363 11.1111Z"
                                stroke="#888888" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="location-filter-container">
                    <button class="all-filter active" id="allFilter">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.44334 4.45801H11.2233C11.6042 4.45801 11.935 4.45801 12.1942 4.48301C12.45 4.50634 12.765 4.56217 13.0217 4.77301C13.3525 5.04467 13.5392 5.45134 13.5417 5.87467C13.5433 6.19967 13.3983 6.47967 13.2583 6.70134C13.1183 6.92634 12.9158 7.19301 12.6792 7.50467L10.5158 10.3563C10.3058 10.633 10.2533 10.7063 10.2167 10.7813C10.1785 10.8599 10.1507 10.9431 10.1342 11.0288C10.1175 11.1122 10.115 11.2055 10.115 11.5572V15.093C10.115 15.2663 10.115 15.4363 10.1033 15.5747C10.0908 15.7113 10.06 15.9305 9.91667 16.1272C9.74167 16.3663 9.46917 16.5213 9.16667 16.5397C8.915 16.5555 8.71084 16.4538 8.59167 16.3872C8.45601 16.3065 8.32368 16.2203 8.195 16.1288L7.37084 15.5597L7.33084 15.5322C7.17167 15.423 6.995 15.3022 6.8625 15.1347C6.74735 14.9902 6.66158 14.8246 6.61 14.6472C6.55084 14.4438 6.55167 14.2305 6.55167 14.0322V11.5572C6.55167 11.2055 6.54834 11.1122 6.5325 11.0288C6.51566 10.9431 6.48765 10.8598 6.44917 10.7813C6.41334 10.7063 6.36084 10.633 6.15084 10.3563L3.9875 7.50467C3.75084 7.19301 3.54834 6.92634 3.4075 6.70134C3.26834 6.47967 3.12417 6.19967 3.125 5.87467C3.12544 5.66412 3.17226 5.45625 3.26214 5.26585C3.35201 5.07544 3.48273 4.90717 3.645 4.77301C3.90167 4.56217 4.21667 4.50634 4.4725 4.48217C4.73167 4.45801 5.06167 4.45801 5.44334 4.45801ZM4.42334 5.75384C4.39529 5.78459 4.37854 5.82397 4.37584 5.86551C4.38084 5.88134 4.40084 5.93301 4.4675 6.03801C4.57417 6.20884 4.7425 6.43134 5.00084 6.77217L7.14667 9.60051L7.17584 9.63884C7.34334 9.85884 7.47667 10.0347 7.57417 10.2347C7.65973 10.4108 7.72139 10.5955 7.75917 10.7888C7.80167 11.0063 7.80084 11.228 7.80084 11.5088V13.9822C7.80084 14.1138 7.80167 14.188 7.805 14.2438L7.81 14.2955C7.81573 14.3176 7.82594 14.3383 7.84 14.3563L7.87417 14.3847C7.91584 14.418 7.97417 14.458 8.08084 14.5313L8.865 15.073V11.508C8.865 11.2272 8.865 11.0055 8.9075 10.788C8.94528 10.5952 9.00695 10.4105 9.0925 10.2338C9.19 10.0338 9.32334 9.85884 9.49084 9.63801L9.52 9.59967L11.6658 6.77134C11.9242 6.42967 12.0925 6.20801 12.1992 6.03717C12.2658 5.93217 12.2858 5.88051 12.2908 5.86467C12.2881 5.82314 12.2714 5.78376 12.2433 5.75301C12.1888 5.7385 12.133 5.72957 12.0767 5.72634C11.8833 5.70801 11.6133 5.70717 11.1933 5.70717H5.47334C5.05334 5.70717 4.78334 5.70717 4.59 5.72634C4.53369 5.72957 4.47785 5.73933 4.42334 5.75384ZM13.125 9.24967C13.125 9.08391 13.1909 8.92494 13.3081 8.80773C13.4253 8.69052 13.5842 8.62467 13.75 8.62467H16.25C16.4158 8.62467 16.5747 8.69052 16.6919 8.80773C16.8092 8.92494 16.875 9.08391 16.875 9.24967C16.875 9.41543 16.8092 9.57441 16.6919 9.69162C16.5747 9.80883 16.4158 9.87467 16.25 9.87467H13.75C13.5842 9.87467 13.4253 9.80883 13.3081 9.69162C13.1909 9.57441 13.125 9.41543 13.125 9.24967ZM11.875 11.333C11.875 11.1672 11.9409 11.0083 12.0581 10.8911C12.1753 10.7739 12.3342 10.708 12.5 10.708H16.25C16.4158 10.708 16.5747 10.7739 16.6919 10.8911C16.8092 11.0083 16.875 11.1672 16.875 11.333C16.875 11.4988 16.8092 11.6577 16.6919 11.7749C16.5747 11.8922 16.4158 11.958 16.25 11.958H12.5C12.3342 11.958 12.1753 11.8922 12.0581 11.7749C11.9409 11.6577 11.875 11.4988 11.875 11.333ZM11.4583 13.4163C11.4583 13.2506 11.5242 13.0916 11.6414 12.9744C11.7586 12.8572 11.9176 12.7913 12.0833 12.7913H16.25C16.4158 12.7913 16.5747 12.8572 16.6919 12.9744C16.8092 13.0916 16.875 13.2506 16.875 13.4163C16.875 13.5821 16.8092 13.7411 16.6919 13.8583C16.5747 13.9755 16.4158 14.0413 16.25 14.0413H12.0833C11.9176 14.0413 11.7586 13.9755 11.6414 13.8583C11.5242 13.7411 11.4583 13.5821 11.4583 13.4163ZM11.4583 15.4997C11.4583 15.3339 11.5242 15.1749 11.6414 15.0577C11.7586 14.9405 11.9176 14.8747 12.0833 14.8747H14.1667C14.3324 14.8747 14.4914 14.9405 14.6086 15.0577C14.7258 15.1749 14.7917 15.3339 14.7917 15.4997C14.7917 15.6654 14.7258 15.8244 14.6086 15.9416C14.4914 16.0588 14.3324 16.1247 14.1667 16.1247H12.0833C11.9176 16.1247 11.7586 16.0588 11.6414 15.9416C11.5242 15.8244 11.4583 15.6654 11.4583 15.4997Z"
                                fill="#175DA8" />
                        </svg> All Filter(<span id="allCount">0</span>)
                    </button>

                    <!-- Location Filter Popup -->
                    <div class="location-filter-popup" id="locationFilterPopup">
                        <button type="button" id="locationFilterClose" class="btn-close" aria-label="Close"></button>
                        <div class="mb-3">
                            <label for="locationSelect" class="form-label">Filter by Location</label>
                            <select class="form-select" id="locationSelect">
                                <option value="">All Locations</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>

                    </div>
                </div>
                <span class="separator"></span>
                <button class="job-filter ml-2" data-filter="active">
                    Active (<span id="activeCount">0</span>)
                </button>

                <button class="job-filter" data-filter="inactive">
                    Inactive (<span id="inactiveCount">0</span>)
                </button>
                <button class="job-filter" data-filter="expired">
                    Expired (<span id="expiredCount">0</span>)
                </button>
            </div>
        </div>

        <!-- Job Cards -->
        <div class="row">
            <div class="col-12">
                <div id="jobsContainer">
                    <!-- Jobs will be loaded here via AJAX -->
                    <div class="loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Loading jobs...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="pagination-custom">
                    <div class="showing">
                        <span class="text-muted">Showing</span>
                        <select class="form-select form-select-sm d-inline-block ms-2" id="perPageSelect"
                            style="width: auto;">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-muted ms-2">per page</span>
                    </div>

                    <div class="page-nav">
                        <span class="text-muted me-2" id="pageInfo">Page 1 of 1</span>
                        <ul class="list-unstyled d-flex mb-0 " id="pagination">
                            <!-- Pagination will be generated dynamically -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Bootstrap 5 JS Bundle with Popper -->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>-->
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/employer_joblisting_page.js"></script>

    <script>
        // delegate so it works even after AJAX reloads
        $(document).on('click', '#applied-candidates', function () {
            const jobId = $(this).data('job-id');
            // navigate, passing the job ID
            window.location.href = `applied_candidate.php?job_id=${jobId}`;
        });



        //////////////////////////////// Available Credits //////////////////////
        // document.getElementById("available-credits").addEventListener("click", function (event) {
        //     event.stopPropagation(); // Prevent closing immediately

        //     let dropdown = document.getElementById("credits-dropdown"); // Check if modal already exists

        //     if (!dropdown) {
        //         // Fetch the modal content from external file
        //         fetch("available_credits.php")
        //             .then(response => response.text())
        //             .then(html => {
        //                 document.getElementById("credits-container").innerHTML = html; // Load the popup
        //                 showDropdown();
        //             });
        //     } else {
        //         showDropdown();
        //     }
        // });

        function showDropdown() {
            let dropdown = document.getElementById("credits-dropdown");

            // Positioning near button
            let button = document.getElementById("available-credits");
            let rect = button.getBoundingClientRect();
            dropdown.style.top = 82 + "px";
            dropdown.style.left = 550 + "px";
            dropdown.style.display = "block";

            // Close dropdown when clicking outside
            document.addEventListener("click", function closeDropdown(event) {
                if (!dropdown.contains(event.target) && event.target.id !== "available-credits") {
                    dropdown.style.display = "none";
                    document.removeEventListener("click", closeDropdown);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('dropdownMenuLink');
            const profileDropdown = document.getElementById('custom-dropdown');

            profileToggle.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent default link behavior
                e.stopPropagation(); // Stop the event from bubbling up
                profileDropdown.classList.toggle('show'); // Toggle visibility
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        });


    </script>
    <?php include('../footer.php'); ?>
</body>

</html>