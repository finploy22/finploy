<?php
session_start(); // Start the session on every page where session data is needed

// Uncomment if session check is required
// if (!isset($_SESSION['name'])) {
//     header("Location: ../index.php");
//     die();
// }

// Pagination variables
include '../db/connection.php';

$total_entries = isset($total_entries) ? $total_entries : 0;

// Define pagination variables
$limit = 10; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$total_pages = ($total_entries > 0) ? ceil($total_entries / $limit) : 1;
// Fetch total number of rows
$total_sql = "SELECT COUNT(*) AS total FROM candidates";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch count of selected candidates
$selected_sql = "
    SELECT 
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
        $selected_count = $row['selected_count']; // Total count of selected candidates
        $selected_candidates[] = [
            'name' => $row['username'],
            'updated_at' => $row['updated']
        ];
    }
}

// Fetch data for the current page
$sql = "SELECT * FROM candidates LIMIT $start, $limit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling for the scrollable table */
        .table-responsive-wrapper {
            overflow-x: auto; /* Add horizontal scrolling */
        }
        th {
            background-color: #175DA8 !important;
            color:  #fff !important;
        } 
        /* Ensure the table doesn't break on smaller screens */
        .table {
            width: 100%; /* Ensure the table takes the full width */
            min-width: 900px; /* Set a minimum width for the table */
            border: none;
        }

        tbody{
            font-size: 15px !important;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
        }

        .status-inprogress {
            background-color: #888888;
            color: #fff;
        }
        .notification-list {
            border-bottom: 1px solid #e3e2e2;
        }
        .notification-header{
            color: #000;
            font-family: Poppins;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            font-size: 15px;
            border-bottom: 1px solid #e3e2e2;
        }
        .notification-date{
            color: #737373;
            font-family: Poppins;
            font-size: 12px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }
        .status-selected {
            background-color: #28a745;
            color: #fff;
        }

        .partner-table-title {
            color: #175DA8;
            font-family: Poppins, sans-serif;
            font-size: 24px;
            font-weight: 600;
        }
        .bell-icon {
            box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
            
        }
        tr {
            text-align: center;
        }
        .tooltip-text {
            position: absolute;
            background-color: rgba(0, 0, 0, 0.75);
            color: #fff;
            font-size: 12px;
            text-align: center;
            border-radius: 4px;
            padding: 5px;
            display: none;
            z-index: 100;
        }
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            margin-top: 15px;
            color: #777;
        }
        .entries-info {
            font-size: 14px;
        }
        .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .page-link {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            background-color: #f1f5f9;
            color: #333;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .page-link:hover {
            background-color: #dce5f1;
        }
        .page-link.active {
            background-color: #dce5f1;
            font-weight: bold;
        }
        .ellipsis {
            padding: 8px;
            color: #999;
        }
        @media screen and (max-width: 600px) {
        .tooltip-text {
        font-size: 12px; /* Smaller font size on smaller screens */
        padding: 4px; /* Adjust padding for smaller screens */
        }
        .partner-table-title {
            font-size: 24px;
        }
        .pagination-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 5px;
        }

        .entries-info {
            font-size: 12px;
        }

        .pagination {
            justify-content: center;
            gap: 5px;
        }

        .page-link {
            padding: 6px 10px;
            font-size: 12px;
        }

        .ellipsis {
            padding: 4px;
        }
        }

        @media screen and (max-width: 400px) {
            .tooltip-text {
            font-size: 10px; /* Even smaller font size on very small screens */
            padding: 3px; /* Adjust padding further */
            }
            .partner-table-title {
                font-size: 18px;
            }
            tbody{
                font-size: 14px !important;
            }
        }

    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <!-- Hero Section -->
    <div id="refer-popup"></div>
    <div class="container">
    <div class="row">
    <div class="mt-4 mb-4 d-flex align-items-center gap-2">
        <a href="./"><i class="fa fa-arrow-left me-2" aria-hidden="true"></i></a>
        <h2 class="partner-table-title mb-0">Notifications</h2>
    </div>
    </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive-wrapper">
                <table class="table rounded table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = $start + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$sno}</td>
                                    <td>{$row['updated']}</td>
                                    <td class='text-start'>
                                        You Referred a Candidate Secured a Job - <strong>" . ucfirst($row['username']) . "</strong>
                                    </td>
                                    <td>
                                        <a id='mark-as-read' href='' class='text-decoration-none'>Mark as Read </a>                                                                            
                                    </td>
                                </tr>";
                            $sno++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="pagination-container mb-4 pe-2">
            <!-- <span class="entries-info">
                Showing <?php echo ($total_entries > 0) ? (($page - 1) * $limit + 1) : 0; ?> to 
                <?php echo min($page * $limit, $total_entries); ?> of <?php echo $total_entries; ?> entries
            </span> -->

            <div class="pagination">
                <?php if ($page > 1) { ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="page-link prev">Previous</a>
                <?php } ?>

                <!-- Always Show First Page -->
                <a href="?page=1" class="page-link <?php echo ($page == 1) ? 'active' : ''; ?>">1</a>

                <!-- Show Ellipsis if needed before current page -->
                <?php if ($page > 3) { ?>
                    <span class="ellipsis">...</span>
                <?php } ?>

                <!-- Show Current Page -->
                <?php if ($page > 1 && $page < $total_pages) { ?>
                    <a href="?page=<?php echo $page; ?>" class="page-link active"><?php echo $page; ?></a>
                <?php } ?>

                <!-- Show Ellipsis if needed after current page -->
                <?php if ($page < $total_pages - 2) { ?>
                    <span class="ellipsis">...</span>
                <?php } ?>

                <!-- Always Show Last Page -->
                <?php if ($total_pages > 1) { ?>
                    <a href="?page=<?php echo $total_pages; ?>" class="page-link <?php echo ($page == $total_pages) ? 'active' : ''; ?>"><?php echo $total_pages; ?></a>
                <?php } ?>

                <?php if ($page < $total_pages) { ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="page-link next">Next</a>
                <?php } ?>
            </div>
        </div>
        <?php else: ?>
            <!-- Empty Listing -->
            <div class="empty-listing text-center">
                <img src="../assets/empty-listing.png" alt="No Records Found" width="400px">
                <p></p>
                <a id="refer-candidate" class="mb-4 btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.71213 4.87237V6.30355L5.00479 6.44264C4.41824 6.48984 3.86446 6.73235 3.43198 7.13139C2.9995 7.53044 2.71331 8.06296 2.61916 8.64383C2.5775 8.90386 2.5392 9.16423 2.50426 9.42493C2.49642 9.48801 2.50866 9.55196 2.53924 9.60768C2.56981 9.66341 2.61718 9.70808 2.67459 9.73535L2.7522 9.77164C8.22394 12.3619 14.7761 12.3619 20.2468 9.77164L20.3245 9.73535C20.3817 9.70793 20.4288 9.66319 20.4592 9.60747C20.4896 9.55176 20.5017 9.4879 20.4938 9.42493C20.4596 9.16403 20.4216 8.90363 20.3799 8.64383C20.2857 8.06296 19.9995 7.53044 19.5671 7.13139C19.1346 6.73235 18.5808 6.48984 17.9943 6.44264L16.2869 6.30456V4.87338C16.287 4.45093 16.1355 4.04248 15.8599 3.72231C15.5843 3.40214 15.2029 3.19152 14.7852 3.12875L13.5556 2.94431C12.1924 2.74057 10.8066 2.74057 9.44346 2.94431L8.21386 3.12875C7.79627 3.19149 7.41505 3.40197 7.13948 3.72193C6.8639 4.04189 6.71227 4.4501 6.71213 4.87237ZM13.3308 4.43899C12.1167 4.25764 10.8824 4.25764 9.66822 4.43899L8.43862 4.62343C8.37896 4.63236 8.32448 4.66239 8.28509 4.70807C8.2457 4.75375 8.224 4.81205 8.22394 4.87237V6.19772C10.4059 6.07311 12.5932 6.07311 14.7751 6.19772V4.87237C14.775 4.81205 14.7533 4.75375 14.714 4.70807C14.6746 4.66239 14.6201 4.63236 14.5604 4.62343L13.3308 4.43899Z" fill="white"/>
                    <path d="M20.689 11.5335C20.687 11.5009 20.6772 11.4693 20.6602 11.4413C20.6433 11.4134 20.6199 11.39 20.5919 11.3732C20.564 11.3563 20.5323 11.3465 20.4997 11.3446C20.4671 11.3427 20.4346 11.3487 20.4048 11.3621C14.79 13.8485 8.20856 13.8485 2.59371 11.3621C2.56396 11.3487 2.53139 11.3427 2.4988 11.3446C2.46621 11.3465 2.43457 11.3563 2.40661 11.3732C2.37865 11.39 2.35521 11.4134 2.33829 11.4413C2.32137 11.4693 2.31148 11.5009 2.30949 11.5335C2.20741 13.463 2.31113 15.398 2.6189 17.3056C2.71285 17.8866 2.99895 18.4194 3.43145 18.8186C3.86394 19.2178 4.41784 19.4605 5.00454 19.5078L6.89127 19.6589C9.95823 19.9069 13.0393 19.9069 16.1073 19.6589L17.994 19.5078C18.5807 19.4605 19.1346 19.2178 19.5671 18.8186C19.9996 18.4194 20.2857 17.8866 20.3796 17.3056C20.688 15.3956 20.7929 13.4605 20.689 11.5345" fill="white"/>
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
        
    </script>
</body>
</html>
