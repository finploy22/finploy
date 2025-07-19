<?php
include '../db/connection.php';
session_start();
// $host = 'localhost';
// $dbusername = 'root';
// $password_db = '';
// $database = 'finployin_dev_user';
// $conn = new mysqli($host, $dbusername, $password_db, $database);
$history_count = isset($_SESSION['history']) ? $_SESSION['history'] : 0;
$accessedCandidateCount = isset($_SESSION['accessed_candidate_count']) ? $_SESSION['accessed_candidate_count'] : 0;

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// // Add this at the top of payment_success.php

if (isset($_SESSION['employer_id'])) {
    $userId = $_SESSION['employer_id'];
    $clearCartQuery = "DELETE FROM user_cart WHERE user_id = ?";
    $clearCartStmt = $conn->prepare($clearCartQuery);
    $clearCartStmt->bind_param("i", $userId);
    $clearCartStmt->execute();
}

// Clear any remaining cart session data
unset($_SESSION['cart']);
unset($_SESSION['payment_amount']);


if (isset($_GET['download']) && isset($_GET['filename'])) {
    // Create connection (or use your existing connection)
    $conn = new mysqli($host, $dbusername, $password_db, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Sanitize the filename
    $filename = $conn->real_escape_string($_GET['filename']);
    
    // Define possible paths where the resume might be stored
    $possible_paths = [
        'D:/xampp/htdocs/finploy/uploads/resumes/',
        $_SERVER['DOCUMENT_ROOT'] . '/finploy/uploads/resumes/',
        realpath($_SERVER['DOCUMENT_ROOT'] . '/../uploads/resumes/'),
    ];
    
    $filepath = '';
    foreach ($possible_paths as $path) {
        $full_path = str_replace('\\', '/', $path . $filename);
        if (file_exists($full_path)) {
            $filepath = $full_path;
            break;
        }
    }
    
    if (empty($filepath)) {
        // Debug output if file not found
        echo "Attempted paths:<br>";
        foreach ($possible_paths as $path) {
            echo htmlspecialchars($path . $filename) . "<br>";
        }
        // Check database record (optional)
        $check_query = "SELECT resume FROM candidate_details WHERE resume = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            die("File not found on server, but exists in database record.");
        } else {
            die("File not found and no database record exists.");
        }
    }
    
    // Verify the resume exists in the candidate_details table
    $query = "SELECT resume FROM candidate_details WHERE resume = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Record the download event in the downloads table
        if (isset($_SESSION['employer_id']) && isset($_GET['candidate_id'])) {
            $employer_id = (int) $_SESSION['employer_id'];
            $candidate_id = (int) $_GET['candidate_id'];
        
            $insert_query = "INSERT INTO downloads (employer_id, candidate_id, resume, download_date) VALUES (?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iis", $employer_id, $candidate_id, $filename);
            
            if (!$insert_stmt->execute()) {
                error_log("Insert failed: " . $conn->error);
            }
        }
        
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Clear output buffer and flush
        ob_clean();
        flush();
        
        // Read and output the file
        readfile($filepath);
        exit;
    } else {
        die('Invalid file reference.');
    }
}

// Get the latest payment ID for the current session
$payment_check_query = "SELECT p.id, p.created_at, p.amount 
                       FROM payments p 
                       WHERE p.status = 'completed' 
                       AND p.employer_id = ?
                       ORDER BY p.created_at DESC 
                       LIMIT 1";

$stmt = $conn->prepare($payment_check_query);
$stmt->bind_param("i", $_SESSION['employer_id']);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment = $payment_result->fetch_assoc();

if ($payment) {
    // Query to get all candidates with their access times
    $query = "SELECT DISTINCT 
                cd.*, 
                p.created_at as purchase_date,
                p.id as payment_id,
                CASE 
                    WHEN p.id = ? THEN 'new'
                    ELSE 'old'
                END as purchase_status
              FROM candidate_details cd 
              INNER JOIN order_items oi ON cd.id = oi.candidate_id 
              INNER JOIN payments p ON oi.payment_id = p.id
              INNER JOIN downloads d ON cd.id = d.candidate_id
              WHERE p.employer_id = ? 
              AND p.status = 'completed'
              ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $payment['id'], $_SESSION['employer_id']);
    $stmt->execute();
    $candidates = $stmt->get_result();
}
error_log("Session data: " . print_r($_SESSION, true));
error_log("Payment data: " . print_r($payment, true));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finploy Job Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicons/favicon.ico">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
    <link rel="stylesheet" href="css/deeesha_style.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Latest Font Awesome CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="./css/employer.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom header">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="image/logo.png" alt="Finploy" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="employer.php">Home <i class="fa fa-home" aria-hidden="true"></i></a>
                    <a class="nav-link position-relative" href="cart_page.php">Cart 
                    <span id="cart-count" class="badge  position-absolute top-10 start-100 translate-middle">0</span><i class="fa fa-shopping-cart"></i>
                </a>
                    <a class="nav-link" href="#">Hi Finploy <i class="fas fa-user-circle"></i></a>
                </div>
            </div>
        </div>
    </nav>
   
        <?php if (isset($payment)): ?>
        <span> <h4 class="heading-dashboard"> Access Candidates</h4></span>
        <div class="filter-buttons-container">
        <div class="filter-buttons-wrapper">
        <button class="filter-btn"  onclick="window.location.href='payment_success.php'">
        Accessed Candidates(<?= $accessedCandidateCount ?>)
        </button>
        <button class="filter-btn" id="candidate-count-btn" onclick="window.location.href='download.php'"></button>
            <button class="filter-btn" onclick="window.location.href='history.php'">
            History(<?= $history_count ?>)
        </button>
        </div>
    </div>
          <div class="section-banner1" align="center">
                <table border="0" id="candidate_table" class="display">
                    <thead>
                        <tr style="background-color:#D2D7D6">
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Mobile Number</th>
                            <th>Resume</th>
                            <th>Exp.Date <i class="fa fa-info-circle" aria-hidden="true"></i></th> 
                            <th>Gender</th>
                            <th>Employed</th>
                            <th>Current Company</th>
                            <th>Destination</th>
                            <th>Sales Experience</th>
                            <th>Work Experience</th>
                            <th>Current Location</th>
                            <th>Current Salary</th>
                            <th>HL/LAP</th>
                            <th>Personal Loan</th>
                            <th>Business Loan</th>
                            <th>Education Loan</th>
                            <th>Gold Loan</th>
                            <th>Credit Cards</th>
                            <th>CASA</th>
                            <th>Others</th>
                            <th>Sales</th>
                            <th>Credit Dept</th>
                            <th>HR/Training</th>
                            <th>Legal/Compliance/Risk</th>
                            <th>Operations</th>
                            <th>Others</th>
                            <th>Created</th>
                            <th>Modified</th>
                        
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($candidates && $candidates->num_rows > 0) {
                            while ($candidate = $candidates->fetch_assoc()) {
                                $startTime = time();
                                echo "<tr class='candidate-row' data-id='" . $candidate['id'] . "'>";
                                echo "<td>" . htmlspecialchars($candidate['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['user_id']) . "</td>";
                                echo "<td data-original='" . htmlspecialchars($candidate['username']) . "' data-start-time='" . $startTime . "'>";
                                echo htmlspecialchars($candidate['username']);
                                echo "</td>";
                                
                                echo "<td data-original='" . htmlspecialchars($candidate['mobile_number']) . "' data-start-time='" . $startTime . "'>";
                                echo htmlspecialchars($candidate['mobile_number']);
                                echo "</td>";
                                if (!empty($candidate['resume'])) {
                                    $safe_filename = htmlspecialchars($candidate['resume']);
                                    echo "<td><a href='?download=true&filename=" . urlencode($safe_filename) . "&candidate_id=" . urlencode($candidate['id']) . "' class='btn btn-primary btn-sm get-score-btn' title='Download Resume'>View CV</a></td>";
                                } else {
                                    echo "<td>No Resume</td>";
                                }
                                echo "<td class='access-timer' 
                                data-start-time='" . strtotime($candidate['purchase_date']) . "'
                                data-status='" . $candidate['purchase_status'] . "'>
                                " . ($candidate['purchase_status'] === 'new' ? '60s' : 'Old Purchase') . "
                            </td>";
                                echo "<td>" . htmlspecialchars($candidate['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['employed']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['current_company']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['destination']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['sales_experience']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['work_experience']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['current_location']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['current_salary']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['hl/lap']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['personal_loan']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['business_loan']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['education_loan']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['gold_loan']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['credit_cards']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['casa']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['others']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['Sales']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['Credit_dept']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['HR/Training']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['Legal/compliance/Risk']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['Operations']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['Others1']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['created']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['modified']) . "</td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='30'>No candidates found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
    </div>
        <?php endif; ?>
    
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js'></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
if ( ! $.fn.DataTable.isDataTable('#candidate_table') ) {
    $('#candidate_table').DataTable({
        dom: 'Btrip',
        scrollX: true,
        scrollY: '60vh',
        scrollCollapse: true,
        fixedColumns: { leftColumns: 1 },
        columnDefs: [{ targets: '_all', width: '120px' }]
    });
}

    </script>
    <!-- Footer -->
    <?php
  
  require_once '..\footer.php';
  ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// Update time remaining every second
// Constants

// Constants

// Constants
const ACCESS_DURATION = 172800; // 2 days in seconds
const WARNING_THRESHOLD = 10; // Adjust if needed

// Helper function to format remaining time:
// - If one day or more remains, show only days count.
// - Otherwise, show hours, minutes, and seconds.
// Format remaining time into days/hours/minutes/seconds
function formatDuration(totalSeconds) {
    if (totalSeconds >= 86400) {
        const days = Math.floor(totalSeconds / 86400);
        return days + "d";
    } else {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return hours + "h " + minutes + "m " + seconds + "s";
    }
}

// Update timers and remove expired candidates using the DataTables API
function updateTimers() {
    const currentTime = Math.floor(Date.now() / 1000);
    const ACCESS_DURATION = 172800; // 2 days in seconds
    const WARNING_THRESHOLD = 10;   // seconds remaining to add a style warning
    const ONE_DAY = 86400;          // one day in seconds

    // Get the DataTable instance
    const table = $('#candidate_table').DataTable();

    // Loop through each row (all pages) using the DataTables API
    table.rows().every(function () {
        const rowNode = $(this.node());
        const timer = rowNode.find('.access-timer');
        const startTime = parseInt(timer.data('startTime'));
        const paymentId = rowNode.data('payment-id'); // retrieve the payment ID

        if (isNaN(startTime)) {
            return; // Skip if no valid start time is set
        }

        const timeElapsed = currentTime - startTime;
        const timeRemaining = ACCESS_DURATION - timeElapsed;

        if (timeRemaining <= ONE_DAY && timeRemaining > 0 && !rowNode.data('notificationSent')) {
            rowNode.data('notificationSent', true);
            $.ajax({
                url: 'notify_employer.php',
                type: 'POST',
                data: { 
                    candidate_id: rowNode.data('id'),
                    employer_id: employerId,
                    message: 'Access to candidate details will expire in one day.'
                },
                success: function(response) {
                    console.log("Notification sent: ", response);
                },
                error: function(xhr, status, error) {
                    console.error("Error sending notification:", error);
                }
            });
        }

        if (timeRemaining <= 0) {
            if (paymentId) {
                $.ajax({
                    url: 'update_purchase_status.php',
                    type: 'POST',
                    data: { payment_id: paymentId },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Status update response:", response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error updating payment status:", error);
                    }
                });
            }
            this.remove();
        } else {
            if (timeRemaining <= WARNING_THRESHOLD) {
                timer.addClass('warning');
            } else {
                timer.removeClass('warning');
            }
            timer.text(formatDuration(timeRemaining));
        }
    });
//     $(document).ready(function() {
//     var table = $('#candidate_table').DataTable();
//     console.log("Initial row count: " + table.rows().count());
// });

    // Update the candidate count button text:
    var candidateCount_download = table.rows().count();
    // console.log("Candidate count to send: " + candidateCount);
    $("#candidate-count-btn").text("Downloaded CV(" + candidateCount_download + ")");

    // Send the candidate count to the server via AJAX
    $.ajax({
        url: 'updatedownloadcount.php',
        type: 'POST',
        data: { candidateCount_download: candidateCount_download },
        success: function(response) {
            console.log("Candidate count updated in session.");
        },
        error: function(xhr, status, error) {
            console.error("Error updating candidate count:", error);
        }
    });

    // Redraw the table (without resetting pagination) after removals
    table.draw(false);
}
// Call updateTimers every second
setInterval(updateTimers, 1000);
updateTimers(); // initial update




</script>
</body>
</html>