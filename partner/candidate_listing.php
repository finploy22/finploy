<?php
session_start();

if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    die();
}
$name = $_SESSION['name'];
$mobile = $_SESSION['mobile'];

include '../db/connection.php';

$total_entries = isset($total_entries) ? $total_entries : 0;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;
$total_pages = ($total_entries > 0) ? ceil($total_entries / $limit) : 1;
// Fetch total number of rows
//$total_sql = "SELECT COUNT(*) AS total FROM candidates";
$total_sql = "SELECT COUNT(*) AS total FROM candidates WHERE associate_mobile = $mobile";
$total_result = $conn->query($total_sql);
if ($total_result && $row = $total_result->fetch_assoc()) {
    $total_entries = $row['total'];
    $total_rows = $row['total'];
} else {
    $total_entries = 0;
    $total_rows = 0;
    error_log("Error fetching total rows: " . $conn->error);
}
$total_pages = ($total_rows > 0) ? ceil($total_rows / $limit) : 1;
// Fetch count of selected candidates
$selected_sql = "SELECT 
            username, 
            updated, 
            (SELECT COUNT(*) FROM candidates WHERE status = 'Selected') AS selected_count 
          FROM candidates 
          WHERE status = 'Selected'";

$selected_result = $conn->query($selected_sql);
$selected_count = 0;
$selected_candidates = [];
if ($selected_result->num_rows > 0) {
    while ($row = $selected_result->fetch_assoc()) {
        $selected_count = $row['selected_count'];
        $selected_candidates[] = [
            'name' => $row['username'],
            'updated_at' => $row['updated']
        ];
    }
}
// Fetch data for the current page
$sql = "SELECT * FROM candidates WHERE associate_mobile = $mobile LIMIT $start, $limit";
//$sql = "SELECT * FROM candidates LIMIT $start, $limit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/partner.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'partner_header.php'; ?>
    <div id="modalContainer"></div>
    <div class="container">
        <div class="referhd">
            <div class="retext">Referred Candidates Details</div>
            <div class="renotifi">
                <div class="icon-box">
                    <img src="assets/bell-icon.svg" alt="Bell Icon">
                    <span class="notification-badge"><?= count($selected_candidates) ?></span>
                </div>
                <div class="notification-container">
                    <div id="notificationDropdown" class="notification-dropdown">
                        <h4>Notification (<?= count($selected_candidates) ?>)</h4>
                        <ul>
                            <?php if (count($selected_candidates) > 0): ?>
                                <?php for ($i = 0; $i < min(3, count($selected_candidates)); $i++): ?>
                                    <li>
                                        You Referred a New Candidate -
                                        <strong><?= htmlspecialchars($selected_candidates[$i]['name']) ?></strong><br>
                                        <small><?= date("M j, Y, g:i:s A", strtotime($selected_candidates[$i]['updated_at'])) ?></small>
                                    </li>
                                <?php endfor; ?>
                            <?php else: ?>
                                <li>No notifications found.</li>
                            <?php endif; ?>
                        </ul>
                        <a href="notification_list.php" class="see-all">See All Notification</a>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive-wrapper">
                <table class="table rounded table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Referred Candidate Name</th>
                            <th>Referred Candidate Mobile No</th>
                            <th>Status <svg class="tooltip-svg" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 14 14" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M13 7C13 10.3137 10.3137 13 7 13C3.68629 13 1 10.3137 1 7C1 3.68629 3.68629 1 7 1C10.3137 1 13 3.68629 13 7ZM7 10.45C7.24852 10.45 7.45 10.2485 7.45 10V6.4C7.45 6.15148 7.24852 5.95 7 5.95C6.75148 5.95 6.55 6.15148 6.55 6.4V10C6.55 10.2485 6.75148 10.45 7 10.45ZM7 4C7.33138 4 7.6 4.26863 7.6 4.6C7.6 4.93137 7.33138 5.2 7 5.2C6.66862 5.2 6.4 4.93137 6.4 4.6C6.4 4.26863 6.66862 4 7 4Z"
                                        fill="white" />
                                </svg></th>
                            <div id="tooltip" class="tooltip-text" style="display: none;"><strong>InProgress:</strong> The
                                candidate is currently in the hiring process.<br>
                                <strong>Selected:</strong> The candidate has been successfully secured the job.
                            </div>
                            <th>Referred Candidate Current Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = $start + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$sno}</td>
                                <td>" . ucfirst($row['username']) . "</td>
                                <td>{$row['mobile_number']}</td>
                                <td>
                                    <span class='status-badge " .
                                ($row['status'] === 'Inprogress' ? 'status-inprogress' : 'status-selected') .
                                "'>{$row['status']}</span>
                                </td>
                                <td>{$row['current_location']}</td>
                            </tr>";
                            $sno++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <?php if ($total_entries > 1): ?>

                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <span>Showing</span>
                        <form method="GET" id="limitForm" style="display: inline-block;">
                            <select name="limit" onchange="document.getElementById('limitForm').submit()"
                                class="limit-dropdown">
                                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                            </select>
                            <input type="hidden" name="page" value="1">
                        </form>
                        <span>Per page</span>
                    </div>

                    <div class="pagination-box">
                        <!-- Previous Button -->
                        <a href="?page=<?= max($page - 1, 1) ?>&limit=<?= $limit ?>"
                            class="pagination-btn <?= $page <= 1 ? 'disabled' : '' ?>">
                            <img src="assets/iconamoon_arrow-left-2.svg" alt="Previous">
                        </a>

                        <!-- Page Indicator -->
                        <div class="page-count">
                            Page <?= $page ?> of <?= $total_pages ?>
                        </div>

                        <!-- Next Button -->
                        <a href="?page=<?= min($page + 1, $total_pages) ?>&limit=<?= $limit ?>"
                            class="pagination-btn <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <img src="assets/iconamoon_arrow-left-2 (1).svg" alt="Next">
                        </a>

                        <a href="?page=<?= $total_pages ?>&limit=<?= $limit ?>"
                            class="pagination-btn <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <img src="assets/iconamoon_arrow-left-2 (2).svg" alt="LAST">
                        </a>



                    </div>
                </div>

            <?php endif; ?>
        <?php else: ?>
            <!-- Empty Listing -->
            <div class="empty-listing text-center">
                <img src="../assets/empty-listing.png" alt="No Records Found" width="330px">
                <p></p>
                <a id="refer-candidate" class="mb-4 btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M6.71213 4.87237V6.30355L5.00479 6.44264C4.41824 6.48984 3.86446 6.73235 3.43198 7.13139C2.9995 7.53044 2.71331 8.06296 2.61916 8.64383C2.5775 8.90386 2.5392 9.16423 2.50426 9.42493C2.49642 9.48801 2.50866 9.55196 2.53924 9.60768C2.56981 9.66341 2.61718 9.70808 2.67459 9.73535L2.7522 9.77164C8.22394 12.3619 14.7761 12.3619 20.2468 9.77164L20.3245 9.73535C20.3817 9.70793 20.4288 9.66319 20.4592 9.60747C20.4896 9.55176 20.5017 9.4879 20.4938 9.42493C20.4596 9.16403 20.4216 8.90363 20.3799 8.64383C20.2857 8.06296 19.9995 7.53044 19.5671 7.13139C19.1346 6.73235 18.5808 6.48984 17.9943 6.44264L16.2869 6.30456V4.87338C16.287 4.45093 16.1355 4.04248 15.8599 3.72231C15.5843 3.40214 15.2029 3.19152 14.7852 3.12875L13.5556 2.94431C12.1924 2.74057 10.8066 2.74057 9.44346 2.94431L8.21386 3.12875C7.79627 3.19149 7.41505 3.40197 7.13948 3.72193C6.8639 4.04189 6.71227 4.4501 6.71213 4.87237ZM13.3308 4.43899C12.1167 4.25764 10.8824 4.25764 9.66822 4.43899L8.43862 4.62343C8.37896 4.63236 8.32448 4.66239 8.28509 4.70807C8.2457 4.75375 8.224 4.81205 8.22394 4.87237V6.19772C10.4059 6.07311 12.5932 6.07311 14.7751 6.19772V4.87237C14.775 4.81205 14.7533 4.75375 14.714 4.70807C14.6746 4.66239 14.6201 4.63236 14.5604 4.62343L13.3308 4.43899Z"
                            fill="white" />
                        <path
                            d="M20.689 11.5335C20.687 11.5009 20.6772 11.4693 20.6602 11.4413C20.6433 11.4134 20.6199 11.39 20.5919 11.3732C20.564 11.3563 20.5323 11.3465 20.4997 11.3446C20.4671 11.3427 20.4346 11.3487 20.4048 11.3621C14.79 13.8485 8.20856 13.8485 2.59371 11.3621C2.56396 11.3487 2.53139 11.3427 2.4988 11.3446C2.46621 11.3465 2.43457 11.3563 2.40661 11.3732C2.37865 11.39 2.35521 11.4134 2.33829 11.4413C2.32137 11.4693 2.31148 11.5009 2.30949 11.5335C2.20741 13.463 2.31113 15.398 2.6189 17.3056C2.71285 17.8866 2.99895 18.4194 3.43145 18.8186C3.86394 19.2178 4.41784 19.4605 5.00454 19.5078L6.89127 19.6589C9.95823 19.9069 13.0393 19.9069 16.1073 19.6589L17.994 19.5078C18.5807 19.4605 19.1346 19.2178 19.5671 18.8186C19.9996 18.4194 20.2857 17.8866 20.3796 17.3056C20.688 15.3956 20.7929 13.4605 20.689 11.5345"
                            fill="white" />
                    </svg> Refer Candidate Now
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../footer.php'; ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/index.js"></script>
    <script>
        $(document).ready(function () {
            // Global function to show steps
            function showStep(step) {
                $('.form-step').removeClass('active');
                $(`#step-${step}`).addClass('active');
            }

            function openReferModal(options = {}) {
                const defaults = {
                    resetForm: true,
                    showStep: 1,
                    callback: null
                };

                const settings = { ...defaults, ...options };

                $('#referModal').fadeIn();

                if (settings.resetForm) {
                    $('#referMobile').val('');
                    $('#referName').val('');
                    $('#referLocation').val('');
                }

                if (settings.showStep) {
                    showStep(settings.showStep);
                }

                if (settings.callback && typeof settings.callback === 'function') {
                    settings.callback();
                }
            }

            // Event listener for the Refer button
            $('#refer-cand-btn').on('click', function () {
                openReferModal();
            });

            // You can also trigger it from any other element using a class
            $('.refer-trigger').on('click', function () {
                openReferModal();
            });

            // Close button functionality
            $('#closeReferBtn').on('click', function () {
                $('#referModal').fadeOut();
            });

            // Close on clicking outside the modal
            $(window).on('click', function (event) {
                if ($(event.target).is('#referModal')) {
                    $('#referModal').fadeOut();
                }
            });

            // Make openReferModal available globally if needed
            window.openReferModal = openReferModal;
        });
        const svgElement = document.querySelector('.tooltip-svg');
        const tooltip = document.getElementById('tooltip');

        // svgElement.addEventListener('mouseenter', function() {
        //     const rect = svgElement.getBoundingClientRect();
        //     tooltip.style.display = 'block';
        //     tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`; // Center the tooltip horizontally
        //     tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5}px`; // Position above the SVG
        // });

        // svgElement.addEventListener('mouseleave', function() {
        //     tooltip.style.display = 'none';
        // });

        function toggleDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Close the dropdown if clicked outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bellIcon = document.querySelector('.bell-icon');
            if (!dropdown.contains(event.target) && !bellIcon.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
        $(document).ready(function () {
            const referBtn = $('#refer-candidate');
            const modalContainer = $('#modalContainer');

            // Event listener for Refer Candidate button
            referBtn.on('click', function () {
                console.log('clicked');
                // Load the modal content from refer_Candidate.php
                $.ajax({
                    url: 'refer_Candidate.php',
                    method: 'GET',
                    success: function (response) {
                        console.log('entered');
                        modalContainer.html(response); // Load the modal content
                        $('#referModal').fadeIn(); // Show the modal
                    },
                    error: function () {
                        alert('Failed to load the modal. Please try again.');
                    }
                });
            });

            // Event delegation for close button inside modal
            modalContainer.on('click', '#closeReferBtn', function () {
                $('#referModal').fadeOut(); // Hide the modal
            });

            // Close the modal when clicking outside of the modal content
            $(document).on('click', function (event) {
                if ($(event.target).is('#referModal')) {
                    $('#referModal').fadeOut();
                }
            });
        });
    </script>

    <script>
        function togglenotiDropdown(event) {
            event.stopPropagation(); // Prevent click from bubbling up
            var dropdown = document.getElementById("notificationDropdown");
            dropdown.style.display = (dropdown.style.display === "none" || dropdown.style.display === "") ? "block" : "none";
        }

        // Close dropdown if clicked outside
        document.addEventListener("click", function (event) {
            var dropdown = document.getElementById("notificationDropdown");
            var iconBox = document.querySelector(".icon-box");

            if (!iconBox.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });

        // Attach the toggle function properly
        document.querySelector(".icon-box").addEventListener("click", togglenotiDropdown);
    </script>

</body>

</html>