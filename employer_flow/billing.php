<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    die();
}

$mobile = $_SESSION['mobile'] ?? '';
include '../db/connection.php';

function getReadableSubPlan($subPlanCode) {
    switch (strtoupper($subPlanCode)) {
        case '1M':
        case '1MS':
            return '1 Month';
        case '3M':
            return '3 Months';
        case 'PAYG':
            return 'Pay-as-you-go';
        default:
            return $subPlanCode; // fallback to original if not matched
    }
}

$employer = $conn->query("SELECT id FROM employers WHERE mobile_number = '$mobile'")->fetch_assoc();
$employer_id = $employer['id'];
$accessCredits = 0;
$accessCreditsAvailable = 0;
$accessCreditsUsed = 0;
$percentageUsed = 0;
$expiryDate = "N/A";
$jobPostCredits = 0;
$jobPostCreditsAvailable = 0;

// Fix for Active Subscription
// Get all active subscription plans
if (isset($_SESSION['mobile'])) {
    $employer_mobile = $_SESSION['mobile'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, plan, plan_status, total_profile_credits, profile_credits_available, 
                          total_jobpost_credits, jobpost_credits_available, sub_plan, amount, expires_at, created
                           FROM `subscription_payments` 
                           WHERE employer_mobile = ? AND plan_status = 'ACTIVE' AND status = 'success' 
                           ORDER BY created DESC");

    if ($stmt) {
        $stmt->bind_param("s", $employer_mobile);
        $stmt->execute();
        $activePlans = $stmt->get_result();
        $stmt->close();
        
        // Get the count of active plans
        $activePlanCount = $activePlans->num_rows;
    }
}

// Check if this is an AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Pagination setup for billing history
$itemsPerPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 20; // Default 20 per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Apply filter if set
$filterStatus = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filterClause = "";
if ($filterStatus !== 'all') {
    $filterClause = " AND status = '$filterStatus'";
}

// Get total number of records for pagination
$totalCountQuery = "SELECT COUNT(*) as total FROM subscription_payments WHERE employer_id = '$employer_id' AND employer_mobile = '$mobile'$filterClause";
$totalCountResult = $conn->query($totalCountQuery);
$totalCount = $totalCountResult->fetch_assoc()['total'];
$totalPages = ceil($totalCount / $itemsPerPage);

// Make sure current page is valid
if ($currentPage < 1) $currentPage = 1;
if ($currentPage > $totalPages && $totalPages > 0) $currentPage = $totalPages;

// If this is an AJAX request, return only the table content
if ($isAjax) {
    // Get the filtered transactions
    $query = "SELECT * FROM subscription_payments 
            WHERE employer_id = '$employer_id' AND employer_mobile = '$mobile'
            $filterClause
            ORDER BY created DESC 
            LIMIT $offset, $itemsPerPage";
    
    $history = $conn->query($query);
    
    $tableContent = '';
    
    if ($history->num_rows > 0) {
        while ($row = $history->fetch_assoc()) {
          $status = strtolower($row['status']);
          
          $statusClasses = [
            'success' => 'badge-success',
            'failed' => 'badge-failed',
            'pending' => 'badge-pending'
          ];
          $badgeClass = $statusClasses[$status] ?? 'badge-secondary';
          
          $action = '';
          if ($status === 'success') {
            $action = '<a href="#" class="text-success text-decoration-none"><i class="fa fa-download me-1"></i> View Invoice</a>';
          } else {
            $action = '<a href="../contact_us.php" class="text-success text-decoration-none"><i class="fa fa-phone me-1"></i> Contact us</a>';
          }
          
          $tableContent .= "<tr>
            <td>" . date('d M Y, h:i A', strtotime($row['created'])) . "</td>
            <td>1 Month Plan</td>
            <td>" . date('d M Y', strtotime($row['expires_at'])) . "</td>
            <td>₹ " . number_format($row['amount']) . "</td>
            <td><span class='$badgeClass status-badge rounded'>" . ucfirst($status) . "</span></td>
            <td>$action</td>
          </tr>";
        }
    } else {
        $tableContent = "<tr><td colspan='6' class='text-center'>No billing history found</td></tr>";
    }
    
    // Create the pagination HTML
    $paginationHtml = '
    <div class="pagination-container">
      <div class="per-page-select">
        <span>Showing</span>
        <select id="perPageSelect" onchange="changePerPage(this.value)">
          <option value="10" ' . ($itemsPerPage == 10 ? 'selected' : '') . '>10</option>
          <option value="20" ' . ($itemsPerPage == 20 ? 'selected' : '') . '>20</option>
          <option value="50" ' . ($itemsPerPage == 50 ? 'selected' : '') . '>50</option>
          <option value="100" ' . ($itemsPerPage == 100 ? 'selected' : '') . '>100</option>
        </select>
        <span>per page</span>
      </div>
      
      <div class="pagination-controls">
        <button onclick="goToPage(' . ($currentPage - 1) . ')" ' . ($currentPage <= 1 ? 'disabled' : '') . '>
          <i class="fas fa-chevron-left"></i>
        </button>
        
        <span class="pagination-info">Page ' . $currentPage . ' of ' . ($totalPages > 0 ? $totalPages : 1) . '</span>
        
        <button onclick="goToPage(' . ($currentPage + 1) . ')" ' . ($currentPage >= $totalPages ? 'disabled' : '') . '>
          <i class="fas fa-chevron-right"></i>
        </button>
        
        <button onclick="goToPage(' . $totalPages . ')" ' . ($currentPage >= $totalPages ? 'disabled' : '') . '>
          <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>';
    
    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'tableContent' => $tableContent,
        'pagination' => $paginationHtml,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'filter' => $filterStatus
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Billing & Subscriptions</title>
  <link rel="stylesheet" href="css/billing.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .gstform-title{
        color: #175DA8;
        font-family: 'Poppins';
        font-size: 20px;
        font-weight: 600;
        text-align:center;
    }
    .gstform-text{
        color: #232323;
        font-family: 'Poppins';
        font-size: 13px;
        font-weight: 500;
    }
    .gstForm label{
        color: #232323;
        font-family: 'Poppins';
        font-size: 15px;
        font-weight: 600;
    }
    
    /* Updated styles for progress bar and carousel */
    .progress-bar {
        transition: width 0.6s ease;
    }
    
    /* Carousel Controls - Make them visible and position them */
    #subscriptionCarousel .carousel-control-prev,
    #subscriptionCarousel .carousel-control-next {
        width: 10%;
        opacity: 0.8;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        height: 40px;
        width: 40px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    #subscriptionCarousel .carousel-control-prev {
        left: auto;
        right: 50px;
    }
    
    #subscriptionCarousel .carousel-control-next {
        right: 5px;
    }
    
    /* Make the carousel control icons more visible */
    #subscriptionCarousel .carousel-control-prev-icon,
    #subscriptionCarousel .carousel-control-next-icon {
        background-color: #175DA8;
        border-radius: 50%;
        padding: 10px;
    }
    
    /* Container for carousel controls */
    .carousel-controls-container {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }
    
    /* Credits display styling */
    .credits-container {
        margin-bottom: 15px;
    }
    
    .progress {
        height: 8px !important;
        border-radius: 5px;
        background-color: #e9ecef;
    }
    
    .used-credit, .remaining-credit {
        font-size: 12px !important;
        color: #6c757d;
    }
    .text-muted {
        color: #232323;
        font-family: 'Poppins';
        font-size: 15px;
        font-weight: 500;
    }
    .carousel-btns:hover{
        background: #fff !important;
        color: #175DA8 !important;
        border-radius: 50% !important;
    }
    .carousel-btns:hover{
        background: #CCCCCC !important;
        color: #175DA8 !important;
        border-radius: 50% !important;
    }
    .carousel-btns{
        padding: 10px !important;
    }
    #billingCardContainer {
    display: none;
    }
    .mobile-billing-card {
        border-radius: 12px;
        background: #FFF;
        box-shadow: 0px 1px 8px 0px rgba(0, 0, 0, 0.20);
      }
      
    @media (max-width: 768px) {
      #billingTable {
        display: none !important;
      }
      #billingCardContainer {
        display: block;
      }
      .plan-value{
          float: right;
          color: #000;
      }
      .billing-action-box{
          border-top: 1px solid #888;
          padding: 5px;
      }
      .per-page-select {
          display: none;
      }
      .pagination-controls{
          text-align: center;
      }
      .filter-btn-wrapper {
        display: flex;
        flex-wrap: nowrap !important;
        overflow-x: auto;
        gap: 0.5rem;
      }
      .filter-btn-wrapper .btn {
        flex-shrink: 0;
      }
    }
  </style>
</head>
<body class="bg-light">
<?php include 'posting_header.php'; ?>
<div class="container my-4">

  <h4 class="mb-4 fw-bold page-title">Billing & Subscriptions</h4>

  <!-- Billing Profile -->
  <div class="row mb-3">
    <div class="col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="billing-card-title mb-0">Billing Profile:</h5>
            <button class="btn btn-outline-primary btn-sm upgrade-details" id="upgradeGstBtn">Upgrade GSTIN Number</button>
          </div>
          <?php
            $query = "SELECT * FROM employer_add_details WHERE employer_mobile_number = '$mobile' LIMIT 1";
            // Execute query and check for errors
            $result = $conn->query($query);
            $profile = $result->fetch_assoc();
          ?>
          <?php if ($profile):?>
            
            <p><strong>GSTIN:</strong> <?php echo !empty($profile['gst']) ? $profile['gst'] : 'N/A'; ?> <span class="badge not-verified"><img src="assets/not-verified-icon.svg"> Not Verified</span></p>
            <p><strong>Company name:</strong> <?php echo ucfirst(htmlspecialchars($profile['employer_company_name'])); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($profile['address']); ?></p>
          <?php else: ?>
            <p class="text-danger">Billing profile not found. Please check your employer ID or mobile number.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Active Subscription (Dynamic Carousel) - UPDATED -->
    <div class="col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body position-relative">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="billing-card-title mb-0">Active Subscription</h5>
            <div>
              <button class="btn btn-outline-primary btn-sm upgrade-details"><a href="../subscription/plans.php" class="text-decoration-none upgrade-plan">Upgrade plan</a></button>
            </div>
          </div>
    
          <?php if ($activePlanCount > 0): ?>
            <div id="subscriptionCarousel" class="carousel slide" data-bs-ride="carousel">
              <!-- Carousel navigation controls positioned to the right -->
              <?php if ($activePlanCount > 1): ?>
              <div class="carousel-controls-container">
                <button class="btn btn-sm btn-outline-primary me-1 carousel-btns" type="button" data-bs-target="#subscriptionCarousel" data-bs-slide="prev">
                  <i class="fas fa-chevron-left carousel-icons"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary carousel-btns" type="button" data-bs-target="#subscriptionCarousel" data-bs-slide="next">
                  <i class="fas fa-chevron-right carousel-icons"></i>
                </button>
              </div>
              <?php endif; ?>
              
              <div class="carousel-inner">
                <?php 
                $activePlans->data_seek(0); // Reset result pointer
                $counter = 0;
                while ($plan = $activePlans->fetch_assoc()): 
                    // Calculate usage percentages
                    $profilePercentage = 0;
                    $profileUsed = 0;
                    if ($plan['total_profile_credits'] > 0) {
                        $profileUsed = $plan['total_profile_credits'] - $plan['profile_credits_available'];
                        $profilePercentage = ($profileUsed / $plan['total_profile_credits']) * 100;
                    }
                    
                    $jobPercentage = 0;
                    $jobUsed = 0;
                    if ($plan['total_jobpost_credits'] > 0) {
                        $jobUsed = $plan['total_jobpost_credits'] - $plan['jobpost_credits_available'];
                        $jobPercentage = ($jobUsed / $plan['total_jobpost_credits']) * 100;
                    }
                    
                    // Determine plan type for badge
                    $planType = "";
                    if ($plan['total_profile_credits'] > 0) {
                        $planType = "CV Access";
                    }
                    if ($plan['total_jobpost_credits'] > 0) {
                        $planType = empty($planType) ? "Job Post" : "Combined";
                    }
                ?>
                    <div class="carousel-item <?php echo ($counter == 0) ? 'active' : ''; ?>">
                        <div class="position-relative">
                            <!-- Plan Type Badge -->
                            <div class="position-absolute top-0 end-0">
                                <!--<span class="badge plan-badge"><?php echo $planType; ?> Plan</span>-->
                            </div>
                            
                            <p class="mb-2">
                                <strong><?php echo getReadableSubPlan($plan['sub_plan']) ?? '1 Month'; ?> Plan</strong> 
                                <span class="badge-success status-badge rounded"><?php echo getReadableSubPlan($plan['plan']) ?? 'Monthly'; ?></span>
                            </p>
                            
                            <?php if ($plan['total_profile_credits'] > 0): ?>
                            <div class="credits-container">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <img class="bag-icon" src="image/bag.svg">
                                        <strong>&nbsp;&nbsp;<?php echo $plan['profile_credits_available']; ?></strong> CV Access Credits
                                    </div>
                                    <span class="ms-2 text-end next-upgrade small">Next Billing: <?php echo date('d M, Y', strtotime($plan['expires_at'])); ?></span>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                                  <span class="used-credit text-muted small">
                                    <strong><?php echo $profileUsed; ?></strong> Used
                                  </span>
                                  <div class="flex-grow-1 mx-2">
                                    <div class="progress">
                                      <div class="progress-bar bg-success" role="progressbar" 
                                           style="width: <?php echo $profilePercentage; ?>%;" 
                                           aria-valuenow="<?php echo $profilePercentage; ?>" 
                                           aria-valuemin="0" 
                                           aria-valuemax="100"></div>
                                    </div>
                                  </div>
                                  <span class="remaining-credit text-muted small">
                                    <strong><?php echo $plan['profile_credits_available']; ?></strong> left
                                  </span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($plan['total_jobpost_credits'] > 0): ?>
                            <div class="credits-container">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <img class="bag-icon" src="image/bag.svg">
                                        <strong>&nbsp;&nbsp;<?php echo $plan['jobpost_credits_available']; ?></strong> Job Post Credits
                                    </div>
                                    <span class="ms-2 text-end next-upgrade small">Next Billing: <?php echo date('d M, Y', strtotime($plan['expires_at'])); ?></span>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                                  <span class="used-credit text-muted small">
                                    <strong><?php echo $jobUsed; ?></strong> Used
                                  </span>
                                  <div class="flex-grow-1 mx-2">
                                    <div class="progress">
                                      <div class="progress-bar bg-success" role="progressbar" 
                                           style="width: <?php echo $jobPercentage; ?>%;" 
                                           aria-valuenow="<?php echo $jobPercentage; ?>" 
                                           aria-valuemin="0" 
                                           aria-valuemax="100"></div>
                                    </div>
                                  </div>
                                  <span class="remaining-credit text-muted small">
                                    <strong><?php echo $plan['jobpost_credits_available']; ?></strong> left
                                  </span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    $counter++;
                    endwhile; 
                ?>
              </div>
            </div>
          <?php else: ?>
            <div class="text-center py-4">
              <p>No active subscriptions found.</p>
              <button class="btn btn-primary btn-sm">Subscribe Now</button>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Billing History -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="billing-card-title mb-3">Billing History:</h5>
       <div class="mb-3 d-flex flex-nowrap overflow-auto gap-2 filter-btn-wrapper">
          <a data-filter="all" class="btn btn-outline-secondary btn-sm filter-btn action-btns flex-shrink-0 <?php echo $filterStatus == 'all' ? 'active' : ''; ?>">All</a>
          <a data-filter="success" class="btn btn-outline-success btn-sm filter-btn action-btns flex-shrink-0 <?php echo $filterStatus == 'success' ? 'active' : ''; ?>">Success</a>
          <a data-filter="pending" class="btn btn-outline-warning btn-sm filter-btn action-btns flex-shrink-0 <?php echo $filterStatus == 'pending' ? 'active' : ''; ?>">Pending</a>
          <a data-filter="failed" class="btn btn-outline-danger btn-sm filter-btn action-btns flex-shrink-0 <?php echo $filterStatus == 'failed' ? 'active' : ''; ?>">Failed</a>
        </div>
      <div id="tableContainer" class="table-responsive">
        <table class="table table-hover" id="billingTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Plan Details</th>
              <th>Expires on</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="billingTableBody">
            <?php
            // Updated query with filter
            $query = "SELECT * FROM subscription_payments 
                    WHERE employer_id = '$employer_id' AND employer_mobile = '$mobile'
                    $filterClause
                    ORDER BY created DESC 
                    LIMIT $offset, $itemsPerPage";
            
            $history = $conn->query($query);
            
            if ($history->num_rows > 0) {
                while ($row = $history->fetch_assoc()) {
                  $status = strtolower($row['status']);
                  
                  $statusClasses = [
                    'success' => 'badge-success',
                    'failed' => 'badge-failed',
                    'pending' => 'badge-pending'
                  ];
                  $badgeClass = $statusClasses[$status] ?? 'badge-secondary';
                  
                  $action = '';
                  if ($status === 'success') {
                    $action = '<a href="#" class="text-success text-decoration-none"><i class="fa fa-download me-1"></i> View Invoice</a>';
                  } else {
                    $action = '<a href="#" class="text-success text-decoration-none"><i class="fa fa-phone me-1"></i> Contact us</a>';
                  }
                  
                  echo "<tr>
                    <td>" . date('d M Y, h:i A', strtotime($row['created'])) . "</td>
                    <td>" .getReadableSubPlan($row['sub_plan'])." </td>
                    <td>" . date('d M Y', strtotime($row['expires_at'])) . "</td>
                    <td>₹ " . number_format($row['amount']) . "</td>
                    <td><span class='$badgeClass status-badge rounded'>" . ucfirst($status) . "</span></td>
                    <td>$action</td>
                  </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No billing history found</td></tr>";
            }
            ?>
          </tbody>
        </table>
        <!-- Billing History For Mobile View -->
        <div id="billingCardContainer" class="d-md-none">
          <?php
          $history->data_seek(0); // Reset pointer if already iterated
          if ($history->num_rows > 0) {
              while ($row = $history->fetch_assoc()) {
                $status = strtolower($row['status']);
                $statusText = ucfirst($status);
                $statusColor = [
                  'success' => 'bg-success text-white',
                  'pending' => 'bg-warning text-dark',
                  'failed'  => 'bg-danger text-white'
                ][$status] ?? 'bg-secondary text-white';
        
                $action = '';
                if ($status === 'success') {
                  $action = '<center><a href="#" class="text-success fw-semibold d-inline-block mt-2 billing-action-btn"><i class="fa fa-download me-1"></i> View Invoice</a></center>';
                } else {
                  $action = '<center><a href="#" class="text-success fw-semibold d-inline-block mt-2 billing-action-btn"><i class="fa fa-phone me-1"></i> Contact us</a></center>';
                }
        
                echo '<div class="border rounded shadow-md p-3 mb-3 bg-white mobile-billing-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div><i class="fa fa-calendar me-2 text-primary"></i>' . date('d M Y, h:i A', strtotime($row['created'])) . '</div>
                          <span class="rounded p-1 m-0 ' . $statusColor . '">' . $statusText . '</span>
                        </div>
                        <div class="billing-action-box"></div>
                        <div class="mb-1 text-muted">Plan Details: <span class="text-align-end plan-value"> ' . getReadableSubPlan($row['sub_plan']) . '</span></div>
                        <div class="mb-1 text-muted">Expires on: <span class="text-align-end plan-value">' . date('d M Y', strtotime($row['expires_at'])) . '</span></div>
                        <div class="mb-1 text-muted">Amount: <span class="text-align-end plan-value">₹ ' . number_format($row['amount']) . '</span></div>
                        <div class="billing-action-box"></div>
                        ' . $action . '
                      </div>';
              }
          } else {
              echo "<div class='text-center'>No billing history found</div>";
          }
          ?>
        </div>

        <!-- Loading spinner -->
        <div id="loadingSpinner" class="loading-spinner">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <!-- Pagination Interface -->
        <div id="paginationContainer" class="pagination-container">
          <div class="per-page-select">
            <span>Showing</span>
            <select id="perPageSelect">
              <option value="10" <?php echo $itemsPerPage == 10 ? 'selected' : ''; ?>>10</option>
              <option value="20" <?php echo $itemsPerPage == 20 ? 'selected' : ''; ?>>20</option>
              <option value="50" <?php echo $itemsPerPage == 50 ? 'selected' : ''; ?>>50</option>
              <option value="100" <?php echo $itemsPerPage == 100 ? 'selected' : ''; ?>>100</option>
            </select>
            <span>per page</span>
          </div>
          
          <div class="pagination-controls">
            <button id="prevPageBtn" <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>>
              <i class="fas fa-chevron-left"></i>
            </button>
            
            <span class="pagination-info">Page <span id="currentPageNum"><?php echo $currentPage; ?></span> of <span id="totalPagesNum"><?php echo $totalPages > 0 ? $totalPages : 1; ?></span></span>
            
            <button id="nextPageBtn" <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>>
              <i class="fas fa-chevron-right"></i>
            </button>
            
            <button id="lastPageBtn" <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>>
              <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Trigger Button -->

<div id="gstPopup" class="gst-modal p-4 rounded bg-white shadow-lg" style="max-width: 500px; margin: auto;">
  <h3 class="gstform-title mb-3 fw-bold text-primary">Upgrade GSTIN Details</h3>
  
  <form id="gstForm" class="gstForm needs-validation" novalidate>
    <p class="gstform-text">The tax id would appear on your future invoices.</p>  
    
    <div class="mb-3">
      <label for="gst_number" class="form-label fw-semibold">GST Number</label>
      <input type="text" class="form-control" id="gst_number" name="gst_number" required />
      <div class="invalid-feedback">Please enter GST number.</div>
    </div>

    <div class="mb-3">
      <label for="gst_address" class="form-label fw-semibold">Address</label>
      <textarea class="form-control" id="gst_address" name="gst_address" rows="3" required></textarea>
      <div class="invalid-feedback">Please enter address.</div>
    </div>
    <p class="gstform-text">We found following company details</p>
    <p class="gstform-text">Company Name: <span class="gstform-text company-name" id="companyName"></span></p>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="verifyDetails" required />
      <label class="form-check-label" for="verifyDetails">
        <span class="gstform-text">I verify my company details and understand that the invoices would be generated using the same information.</span>
      </label>
      <div class="invalid-feedback">You must verify your company details.</div>
    </div>

    <input type="hidden" id="employer_id" name="employer_id" value="<?php echo $profile['employer_id'] ?>" />

    <div class="gstform-btns d-flex justify-content-end gap-2">
      <button type="button" class="btn btn-outline-secondary" onclick="closePopup()">Cancel</button>
      <button type="submit" class="btn btn-success">Save</button>
    </div>
  </form>
</div>



<!-- Background overlay -->
<div id="overlay" class="gst-overlay" onclick="closePopup()"></div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });
  
  // Global variables to track current state
  let currentFilter = '<?php echo $filterStatus; ?>';
  let currentPage = <?php echo $currentPage; ?>;
  let totalPages = <?php echo $totalPages > 0 ? $totalPages : 1; ?>;
  let itemsPerPage = <?php echo $itemsPerPage; ?>;
  
  // Function to load data via AJAX
  function loadData(filter = currentFilter, page = currentPage, perPage = itemsPerPage) {
    // Show loading spinner
    document.getElementById('billingTableBody').style.display = 'none';
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('paginationContainer').style.display = 'none';
    
    // Create URL with query parameters
    const url = `?filter=${filter}&page=${page}&perPage=${perPage}`;
    
    // Make AJAX request
    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      // Update table content
      document.getElementById('billingTableBody').innerHTML = data.tableContent;
      
      // Update pagination
      document.getElementById('paginationContainer').innerHTML = data.pagination;
      
      // Update global variables
      currentFilter = data.filter;
      currentPage = data.currentPage;
      totalPages = data.totalPages;
      
      // Update UI active state for filter buttons
      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-filter') === currentFilter) {
          btn.classList.add('active');
        }
      });
      
      // Update URL without refreshing the page
      window.history.pushState({}, '', url);
      
      // Hide loading spinner, show table
      document.getElementById('billingTableBody').style.display = '';
      document.getElementById('loadingSpinner').style.display = 'none';
      document.getElementById('paginationContainer').style.display = '';
      
      // Reattach event listeners to the new pagination elements
      attachPaginationEventListeners();
    })
    .catch(error => {
      console.error('Error loading data:', error);
      document.getElementById('loadingSpinner').style.display = 'none';
      document.getElementById('billingTableBody').style.display = '';
      document.getElementById('paginationContainer').style.display = '';
      
      // Show error message in table
      document.getElementById('billingTableBody').innerHTML = 
        '<tr><td colspan="6" class="text-center text-danger">Error loading data. Please try again.</td></tr>';
    });
  }
  
  // Function to navigate to a specific page
  function goToPage(page) {
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    loadData(currentFilter, page, itemsPerPage);
  }
  
  // Function to change items per page
  function changePerPage(perPage) {
    itemsPerPage = perPage;
    loadData(currentFilter, 1, perPage); // Reset to page 1 when changing items per page
  }
  
  // Attach event listeners to filter buttons
  document.addEventListener('DOMContentLoaded', function() {
    // Add click event to filter buttons
    document.querySelectorAll('.filter-btn').forEach(button => {
      button.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        loadData(filter, 1, itemsPerPage); // Reset to page 1 when changing filter
      });
    });
    
    // Add event listener to per page select
    document.getElementById('perPageSelect').addEventListener('change', function() {
      changePerPage(this.value);
    });
    
    // Initialize pagination event listeners
    attachPaginationEventListeners();
  });
  
  // Function to attach event listeners to pagination controls
  function attachPaginationEventListeners() {
    // Previous page button
    const prevPageBtn = document.getElementById('prevPageBtn');
    if (prevPageBtn) {
      prevPageBtn.addEventListener('click', function() {
        goToPage(currentPage - 1);
      });
    }
    
    // Next page button
    const nextPageBtn = document.getElementById('nextPageBtn');
    if (nextPageBtn) {
      nextPageBtn.addEventListener('click', function() {
        goToPage(currentPage + 1);
      });
    }
    
    // Last page button
    const lastPageBtn = document.getElementById('lastPageBtn');
    if (lastPageBtn) {
      lastPageBtn.addEventListener('click', function() {
        goToPage(totalPages);
      });
    }
  }
  
// Gst Popup Script

// When Upgrade GST button is clicked
document.getElementById("upgradeGstBtn").addEventListener("click", function () {
    const employerId = document.getElementById("employer_id").value;

    fetch("get_gst_details.php?employer_id=" + encodeURIComponent(employerId))
        .then(res => res.text()) // use text to debug response
        .then(text => {
            console.log("Raw response from get_gst_details.php:", text);
            try {
                const data = JSON.parse(text);

                if (data.success) {
                    document.getElementById("companyName").innerText = (data.company || "").toUpperCase();
                    document.getElementById("gst_number").value = data.gst_number || "";
                    document.getElementById("gst_address").value = data.gst_address || "";
                }

                // Show popup in any case
                document.getElementById("overlay").style.display = "block";
                document.getElementById("gstPopup").style.display = "block";
            } catch (err) {
                console.error("JSON parse error:", err);
                alert("Failed to load GST data. Please try again.");
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert("Network error while fetching GST details.");
        });
});

// Close popup
function closePopup() {
    document.getElementById("gstPopup").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

// Save GST form
document.getElementById("gstForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
console.log(formData);
    fetch("save_gst.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        console.log("Raw response from save_gst.php:", text);
        try {
            const data = JSON.parse(text);
            alert(data.message);
            if (data.success) {
                closePopup();
            }
        } catch (err) {
            console.error("Error parsing JSON:", err);
            alert("Invalid response from server. Please try again.");
        }
    })
    .catch(error => {
        console.error("Error saving GST:", error);
        alert("Something went wrong while saving GST.");
    });
});


</script>
</body>
</html>