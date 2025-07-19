<?php
include '../db/connection.php';
session_start();

$history_count = isset($_SESSION['history']) ? $_SESSION['history'] : 0;
$downloadCandidateCount = isset($_SESSION['candidateCount_download']) ? $_SESSION['candidateCount_download'] : 0;

// Clear the employer's cart if logged in
if (isset($_SESSION['employer_id'])) {
    $userId = $_SESSION['employer_id'];
    $clearCartQuery = "DELETE FROM user_cart WHERE user_id = ?";
    $clearCartStmt = $conn->prepare($clearCartQuery);
    $clearCartStmt->bind_param("i", $userId);
    $clearCartStmt->execute();
}
unset($_SESSION['cart']);
unset($_SESSION['payment_amount']);

// Download candidate resume if requested
if (isset($_GET['download']) && isset($_GET['filename'])) {
    $conn = new mysqli($host, $dbusername, $password_db, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $filename = $conn->real_escape_string($_GET['filename']);
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
        echo "Attempted paths:<br>";
        foreach ($possible_paths as $path) {
            echo htmlspecialchars($path . $filename) . "<br>";
        }
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
    
    $query = "SELECT resume FROM candidate_details WHERE resume = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
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
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Pragma: no-cache');
        header('Expires: 0');
        
        ob_clean();
        flush();
        readfile($filepath);
        exit;
    } else {
        die('Invalid file reference.');
    }
}

// Get the latest payment details for the current employer
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
    // Query to fetch candidate details
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
              WHERE p.employer_id = ? 
              AND p.status = 'completed'
              ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $payment['id'], $_SESSION['employer_id']);
    $stmt->execute();
    $candidates = $stmt->get_result();
    
 
    
  // Define access durations (in seconds)
// Total access duration in seconds (10 minutes)

}
   // Convert results to an array so we can update and then display them.
   $candidatesArray = [];
   if ($candidates && $candidates->num_rows > 0) {
       while ($row = $candidates->fetch_assoc()) {
           $candidatesArray[] = $row;
       }
   }


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Finploy Job Board</title>
  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png" />
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicons/favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/deeesha_style.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="./css/employer.css" />
  <style>
    /* Basic table styling */
    #candidate_table {
      width: 100%;
      border-collapse: collapse;
    }
    #candidate_table th,
    #candidate_table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
      min-width: 120px;
    }
    /* Sticky header */
    #candidate_table thead th {
      position: sticky;
      top: 0;
      background: #D2D7D6;
      z-index: 2;
    }
    /* Sticky first column */
    #candidate_table tbody td:first-child,
    #candidate_table thead th:first-child {
      position: sticky;
      left: 0;
      background: #fff;
      z-index: 1;
    }
    /* For the header cell at the top left */
    #candidate_table thead th:first-child {
      z-index: 3;
    }
    /* Optional warning style for timers */
    .warning {
      background-color: #ffcccc;
    }
    /* Container for horizontal scrolling */
    .table-container {
      overflow: auto;
      max-height: 90vh;
      margin-bottom: 20px;
    }
    /* Pagination controls styling */
    #pagination-controls {
      text-align: right;
      margin-top: 10px;
    }
    #pagination-controls button {
      margin: 0 5px;
      padding: 5px 10px;
      border: 1px solid #ccc;
      background-color: #f8f8f8;
      cursor: pointer;
    }
    #pagination-controls button:hover {
      background-color: #ddd;
    }
    /* Filter button styling */
    .filter-buttons-container {
      margin: 20px 0;
      text-align: center;
    }
    .filter-buttons-wrapper .filter-btn {
      margin: 0 5px;
    }
    /* Dashboard first row (header) color #E9F1FB */
/* Dashboard header row: Entire row should be #E9F1FB */
#candidate_table thead tr {
    background-color: #E9F1FB !important;
    color: inherit !important;
}

/* Dashboard first tbody row: Entire row should be #FFFFFF */
#candidate_table tbody tr:first-child {
    background-color: #FFFFFF !important;
    color: inherit !important;
}

/* Ensure that individual cells in the first tbody row inherit the row's background */
#candidate_table tbody tr:first-child td {
    background-color: inherit !important;
}
.navbar-brand {

	margin-left: 50px;
}
.navbar-expand-lg .navbar-nav {
	flex-direction: row;
	padding-right: 91px;
}

  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom header">
      <div class="container-fluid">
          <a class="navbar-brand" href="#">
              <img src="image/logo.png" alt="Finploy" height="50" />
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
              <div class="navbar-nav ms-auto">
                  <a class="nav-link" href="employer.php">Home <i class="fa fa-home" aria-hidden="true"></i></a>
                  <a class="nav-link position-relative" href="cart_page.php">
                    Cart 
                    <span id="cart-count" class="badge position-absolute top-10 start-100 translate-middle">0</span>
                    <i class="fa fa-shopping-cart"></i>
                  </a>
                  <a class="nav-link" href="#">Hi Finploy <i class="fas fa-user-circle"></i></a>
              </div>
          </div>
      </div>
  </nav>
   
  <?php if (isset($payment)): ?>
    <div class="container">
    <div class=" my-4">
  <!-- Row 1: Heading -->
  <div class="row">
    <div class="col-12">
      <h4 class="heading-dashboard text-center">Access Candidates</h4>
    </div>
  </div>

  <!-- Row 2: Filter Buttons -->
  <div class="row">
    <div class="col-12">
      <div class="filter-buttons-container">
        <div class="filter-buttons-wrapper">
          <button class="filter-btn" id="candidate-count-btn" onclick="window.location.href='payment_success.php'"></button>
          <button class="filter-btn" onclick="window.location.href='download.php'">
              Downloaded CV(<?= $downloadCandidateCount ?>)
          </button>
          <button class="filter-btn" onclick="window.location.href='history.php'">
              History(<?= $history_count ?>)
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
      <!-- Table Container for horizontal scrolling -->
      <div class="table-container">
        <table id="candidate_table">
          <thead>
            <tr>
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
            if (!empty($candidatesArray)) {
              foreach ($candidatesArray as $candidate) {
                    // Use the current time as the starting point for the timer
                    $startTime = time();
                    $paymentId = htmlspecialchars($candidate['payment_id']);
                    echo "<tr class='candidate-row' data-id='" . htmlspecialchars($candidate['id']) . "' data-payment-id='" . $paymentId . "'>";
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
                        echo "<td><a href='?download=true&filename=" . urlencode($safe_filename) . "&candidate_id=" . urlencode($candidate['id']) . "' class='btn btn-success btn-sm' title='Download Resume'>View CV</a></td>";
                    } else {
                        echo "<td>No Resume</td>";
                    }
                    echo "<td class='access-timer' data-starttime='" . strtotime($candidate['purchase_date']) . "' data-status='" . $candidate['purchase_status'] . "'>";
                    echo ($candidate['purchase_status'] === 'new' ? '60s' : 'Old Purchase');
                    echo "</td>";
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
      <!-- Pagination Controls -->
      <div id="pagination-controls"></div>
    </div>
    </div>
  <?php endif; ?>

  <!-- jQuery and Bootstrap Bundle -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // --- Timer and Notification Logic ---
    const employerId = <?= json_encode($_SESSION['employer_id']); ?>;
    const ACCESS_DURATION = 172800; // 2 days in seconds
    const WARNING_THRESHOLD = 10;   // seconds before expiry to highlight timer
    const ONE_DAY = 86400;          // one day in seconds

    // --- Pagination Variables ---
    let rowsPerPage = 10;
    let currentPage = 1;
    let lastRowCount = $("#candidate_table tbody tr").length;

    // Show only the rows for the current page
    function showPage(page) {
      currentPage = page;
      const rows = $("#candidate_table tbody tr");
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      rows.hide();
      rows.slice(start, end).show();
    }

    // Setup pagination controls based on the current number of rows
    function setupPagination() {
      const rows = $("#candidate_table tbody tr");
      const rowsCount = rows.length;
      const pageCount = Math.ceil(rowsCount / rowsPerPage);
      const pagination = $("#pagination-controls");
      pagination.empty();
      if (pageCount > 1) {
        pagination.append("<button id='prev-btn'>Previous</button>");
        for (let i = 1; i <= pageCount; i++) {
          pagination.append("<button class='page-btn' data-page='" + i + "'>" + i + "</button>");
        }
        pagination.append("<button id='next-btn'>Next</button>");
      }
      if (currentPage > pageCount) {
        currentPage = pageCount;
      }
      showPage(currentPage);

      $("#prev-btn").click(function(){
        if(currentPage > 1){
          showPage(currentPage - 1);
        }
      });
      $("#next-btn").click(function(){
        if(currentPage < pageCount){
          showPage(currentPage + 1);
        }
      });
      $(".page-btn").click(function(){
        const page = $(this).data("page");
        showPage(page);
      });
    }

    // --- Timer Update Logic ---
    function updateTimers() {
      const currentTime = Math.floor(Date.now() / 1000);
      $("#candidate_table tbody tr").each(function() {
        const $row = $(this);
        const timer = $row.find(".access-timer");
        const startTime = parseInt(timer.data("starttime")); // data attribute (all lowercase)
        if (isNaN(startTime)) return;
        const timeElapsed = currentTime - startTime;
        const timeRemaining = ACCESS_DURATION - timeElapsed;
        const paymentId = $row.data("payment-id");
       

        // Notify employer when less than one day remains (once per row)
        if (timeRemaining <= ONE_DAY && timeRemaining > 0 && !$row.data("notificationSent")) {
          $row.data("notificationSent", true);
          $.ajax({
            url: 'notify_employer.php',
            type: 'POST',
            data: { 
              candidate_id: $row.data("id"),
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
        // If time is up, update status and remove row
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
          $row.remove();
        } else {
          // Add or remove warning class based on time remaining
          if (timeRemaining <= WARNING_THRESHOLD) {
            timer.addClass("warning");
          } else {
            timer.removeClass("warning");
          }
          timer.text(formatDuration(timeRemaining));
        }
      });
      // Update candidate count button text (shows total rows in the table)
      const candidateCount = $("#candidate_table tbody tr").length;
      $("#candidate-count-btn").text("Accessed Candidates(" + candidateCount + ")");

      // Only update pagination if the number of rows has changed
      const newRowCount = $("#candidate_table tbody tr").length;
      if(newRowCount !== lastRowCount) {
        lastRowCount = newRowCount;
        setupPagination();
      }
      
      // Optionally, update candidate count in session via AJAX
      $.ajax({
        url: 'updateCandidateCount.php',
        type: 'POST',
        data: { candidate_count: candidateCount },
        success: function(response) {
          console.log("Candidate count updated in session.");
        },
        error: function(xhr, status, error) {
          console.error("Error updating candidate count:", error);
        }
      });
    }

    // Format seconds into days or h m s
    function formatDuration(totalSeconds) {
      if (totalSeconds >= ONE_DAY) {
        const days = Math.floor(totalSeconds / ONE_DAY);
        return days + "d";
      } else {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return hours + "h " + minutes + "m " + seconds + "s";
      }
    }

    // Initialize pagination on page load
    $(document).ready(function(){
      setupPagination();
      // Update timers every second
      setInterval(updateTimers, 1000);
      updateTimers();
    });

    function updateExpiredStatus() {
    $.ajax({
      url: 'update_expired_status.php',
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        if(response.status === 'success') {
          console.log("Expired statuses updated:", response.updates);
          // Optionally, update your UI based on the returned data.
        } else {
          console.error("Error:", response.message);
        }
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
      }
    });
  }

  // Call the function immediately and then every 30 seconds.
  $(document).ready(function(){
    updateExpiredStatus();
    setInterval(updateExpiredStatus, 30000); // 30000 ms = 30 seconds.
  });


  </script>

  <!-- Footer -->
  <?php require_once '..\footer.php'; ?>
</body>
</html>
