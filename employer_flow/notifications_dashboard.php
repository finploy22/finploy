<?php

include '../db/connection.php';
session_start();

$employer_id = isset($_SESSION['employer_id']) ? $_SESSION['employer_id'] : 0;

// Pagination variables
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$limit  = 10;
$offset = ($page - 1) * $limit;

// Count total notifications for pagination
$countQuery = "SELECT COUNT(*) as total FROM (
    SELECT p.id 
    FROM candidate_details cd
    INNER JOIN order_items oi ON cd.id = oi.candidate_id
    INNER JOIN payments p ON oi.payment_id = p.id
    WHERE p.employer_id = {$employer_id}
) as notifications";
$countResult = $conn->query($countQuery);
$totalNotifications = ($countResult && $row = $countResult->fetch_assoc()) ? $row['total'] : 0;
$totalPages = ceil($totalNotifications / $limit);

// Fetch notifications with pagination
$query = "SELECT p.id, cd.username AS candidate_name, 
                 p.employer_username AS employer_name, 
                 p.created_at AS purchase_date, 
                 p.expired
          FROM candidate_details cd
          INNER JOIN order_items oi ON cd.id = oi.candidate_id
          INNER JOIN payments p ON oi.payment_id = p.id
          WHERE p.employer_id = {$employer_id}
          ORDER BY p.created_at DESC
          LIMIT {$limit} OFFSET {$offset}";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Custom CSS -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    /* Table header styling */
    .table-bordered thead th {
      background: #175DA8; 
      color: #fff;
      text-align: center;
    }
    /* Table borders with opacity */
    .table-bordered th,
    {
      border: 1px solid rgba(0, 0, 145, 0.12);
      text-align: center;
      vertical-align: middle;
    }
    /* Alternating row background colors */
    .table-striped tbody tr:nth-child(odd) {
      background-color: #fff;
    }
    .table-striped tbody tr:nth-child(even) {
      background-color: #f2f2f2; /* light gray */
    }
    .pagination {
      margin-top: 20px;
    }
    /* Custom header container styling (for both the arrow icon and text) */
    .header-container {
      color: #175DA8; /* Replace with your desired color */
    }
    .arrow{
        text-decoration:none;
        color: #175DA8; 
        font-size: 20px;
    }
    
  </style>
</head>
<body>
  <div class="container dashboard-container">
    <div class="d-flex align-items-center mb-4 header-container">
      <a href="employer.php" class="arrow me-3">
      <i class="fas fa-arrow-left"></i>
      </a>
      <h3 class="m-0">Notifications</h3>
    </div>

    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>S.No</th>
          <th>Message</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): 
          $sn = $offset + 1;
          while ($row = $result->fetch_assoc()):
              // Format the purchase date for the message
              $formattedDate = date("M d, Y, h:i:s A", strtotime($row['purchase_date']));
              $expiredMinutes = (int)$row['expired'];
              $message = "{$row['employer_name']}, your selected candidate {$row['candidate_name']} will expire in {$expiredMinutes} minutes on {$formattedDate}.";
      ?>
          <tr>
              <td><?php echo $sn++; ?></td>
              <td><?php echo htmlspecialchars($message); ?></td>
              <td>
                  <!-- Display plain text for "Mark as read" -->
                  <span class="text-primary">Mark as read</span>
              </td>
          </tr>
      <?php endwhile; else: ?>
          <tr>
              <td colspan="3" class="text-center">No notifications available.</td>
          </tr>
      <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination with page numbers and "Next" button only (no Previous button) -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center">
        <?php
        $range = 2; // number of pages to show on either side of the current page
        if ($totalPages <= (4 + ($range * 2))) {
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == $page) {
                    echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }
            }
        } else {
            // Always show the first page
            if ($page == 1) {
                echo '<li class="page-item active"><span class="page-link">1</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
            }
            if ($page - $range > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $start = max(2, $page - $range);
            $end   = min($totalPages - 1, $page + $range);
            for ($i = $start; $i <= $end; $i++) {
                if ($i == $page) {
                    echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }
            }
            if ($page + $range < $totalPages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            if ($page == $totalPages) {
                echo '<li class="page-item active"><span class="page-link">' . $totalPages . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
            }
        }
        ?>
        <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo ($page + 1); ?>">Next</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled"><span class="page-link">Next</span></li>
        <?php endif; ?>
      </ul>
    </nav>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
