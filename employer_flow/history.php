<?php
include '../db/connection.php';
session_start(); // Start the session

$Downloaded_CV_count = isset($_SESSION['Downloaded_CV']) ? $_SESSION['Downloaded_CV'] : 0;
$accessedCandidateCount = isset($_SESSION['accessed_candidate_count']) ? $_SESSION['accessed_candidate_count'] : 0;
$downloadCandidateCount = isset($_SESSION['candidateCount_download']) ? $_SESSION['candidateCount_download'] : 0;
// echo $downloadCandidateCount;



// Resume download handler
if (isset($_GET['download']) && isset($_GET['filename'])) {
    // Create connection
    // $conn = new mysqli($host, $dbusername, $password_db, $database);

    // // Check connection
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    // Sanitize the filename
    $filename = $conn->real_escape_string($_GET['filename']);

    // Define the directory where resumes are stored
    // Try multiple potential paths
    // Try multiple potential paths
       $possible_paths = [
        'uploads/',
        $_SERVER['DOCUMENT_ROOT'] . '/uploads/',
        realpath($_SERVER['DOCUMENT_ROOT'] . '/../uploads/'),
    ];

    $filepath = '';
    foreach ($possible_paths as $path) {
        $full_path = $path . $filename;
        
        // Use forward slashes for cross-platform compatibility
        $full_path = str_replace('\\', '/', $full_path);
        
        if (file_exists($full_path)) {
            $filepath = $full_path;
            break;
        }
    }

    // If no file found
    if (empty($filepath)) {
        // Debugging output
        echo "Attempted paths:<br>";
        foreach ($possible_paths as $path) {
            echo htmlspecialchars($path . $filename) . "<br>";
        }
        
        // Check if filename exists in database
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

    // Verify the filename exists in the database
    $query = "SELECT resume FROM candidate_details WHERE resume = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Clear output buffer
        ob_clean();
        flush();
        
        // Read and output file
        readfile($filepath);
        exit;
    } else {
        die('Invalid file reference.');
    }
}


// // Main database connection
// $conn = new mysqli($host, $dbusername, $password_db, $database);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
   // Modified query to order candidates by payment date, showing newest first
   $query = "SELECT DISTINCT cd.*, p.created_at as purchase_date, p.status as payment_status
   FROM candidate_details cd 
   INNER JOIN order_items oi ON cd.id = oi.candidate_id 
   INNER JOIN payments p ON oi.payment_id = p.id
   WHERE p.employer_id = ? 
   AND p.status IN ('completed', 'failed') 
   ORDER BY 
     CASE WHEN p.id = ? THEN 0 ELSE 1 END,  -- Show latest payment's candidates first
     p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $_SESSION['employer_id'], $payment['id']);
$stmt->execute();
$candidates = $stmt->get_result();


// Fetch all candidate details with resume path

// Check if query was successful
// if (!$candidates) {
//     die("Query failed: " . mysqli_error($conn));
// }
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
        <!-- Left side hamburger -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Centered logo -->
        <a class="navbar-brand mx-auto" href="#">
            <img src="image/logo.png" alt="Finploy" height="50">
        </a>

        <!-- Right side icons for mobile -->
        <div class="d-flex d-lg-none align-items-center">
            <a class="nav-link position-relative me-2" href="cart_page.php">
                <i class="fa fa-shopping-cart"></i>
                <span id="cart-count-mobile" class="badge position-absolute top-0 start-100 translate-middle">0</span>
            </a>
            <a class="nav-link" href="#">
                <i class="fas fa-user-circle"></i>
            </a>
        </div>

        <!-- Offcanvas menu for mobile -->
        <!-- <div class="offcanvas offcanvas-start" id="navbarOffcanvas" aria-labelledby="navbarOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="navbarOffcanvasLabel">Menu</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="navbar-nav">
                    <a class="nav-link" href="#">
                        <i class="fa fa-home" aria-hidden="true"></i> Home
                    </a>
                    <a class="nav-link" href="cart_page.php">
                        <i class="fa fa-shopping-cart"></i> Cart
                        <span id="cart-count-dropdown" class="badge">0</span>
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-user-circle"></i> Hi Finploy
                    </a>
                </div>
            </div>
        </div> -->

        <!-- Desktop menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="#">
                    <i class="fa fa-home" aria-hidden="true"></i> Home
                </a>
                <a class="nav-link position-relative d-none d-lg-block" href="cart_page.php">
                    Cart <i class="fa fa-shopping-cart"></i>
                    <span id="cart-count" class="badge position-absolute top-10 start-100 translate-middle">0</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-user-circle"></i> Hi Finploy
                </a>
            </div>
        </div>
    </div>
</nav>


        <span> <h4 class="heading-dashboard"> History</h4></span>
        <div class="filter-buttons-container">
        <div class="filter-buttons-wrapper">
        <button class="filter-btn" onclick="window.location.href='payment_success.php'">
        Accessed Candidates(<?= $accessedCandidateCount ?>)
        </button>
        <button class="filter-btn" onclick="window.location.href='download.php'">
         Downloaded CV(<?= $downloadCandidateCount?>)
            </button>
            <button class="filter-btn" onclick="window.location.href='history.php'">
            <?php 
          $candidate_count_history = 0;
          if (isset($candidates)) {
              // Use $candidates instead of $candidates_history
              $candidate_count_history = $candidates->num_rows;
          }
          $_SESSION['history'] = $candidate_count_history; 
          echo "History (" . $candidate_count_history . ")";
          ?>

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
                            <th>Gender</th>
                            <th>Employed</th>
                            <th>Current Company</th>
                            <th>Destination</th>
                            <th>Sales Experience</th>
                            <th>Work Experience</th>
                            <th>Current Location</th>
                            <th>Current Salary</th>
                            <!-- <th>Resume</th> -->
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
                            <th>Add to Cart</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        function maskHalf($str) {
                            $length = strlen($str);
                            $halfLength = floor($length / 2);
                            $firstHalf = substr($str, 0, $halfLength);
                            $secondHalf = str_repeat('x', $length - $halfLength);
                            return $firstHalf . $secondHalf;
                        }
                        $cartCandidateIds = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
                        if ($candidates && $candidates->num_rows > 0) {
                            while ($candidate = $candidates->fetch_assoc()) {
                                echo "<tr>";
                                $maskedMobileNumber = maskHalf($candidate['mobile_number']);
                                $maskedUsername = maskHalf($candidate['username']);
                              
                                echo "<td>" . htmlspecialchars($candidate['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($candidate['user_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($maskedUsername) . "</td>";
                                echo "<td>" . htmlspecialchars($maskedMobileNumber) . "</td>";
                                echo "<td><button class='btn btn-secondary btn-sm' disabled>View CV</button></td>";
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
                                echo "<td>";
                                if (in_array($candidate['id'], $cartCandidateIds)) {
                                    echo "<button class='btn btn-success btn-sm cart-btn' onclick='window.location.href=\"cart_page.php\"'>
                                            Go to Cart <i class='fa fa-shopping-cart'></i>
                                          </button>";
                                } else {
                                    echo "<button class='btn btn-success btn-sm cart-btn' onclick='addToCart(" . $candidate['id'] . ", 50)'>
                                            Add to Cart <i class='fa fa-shopping-cart'></i>
                                          </button>";
                                }
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

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js'></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
$(document).ready(function() {
    $('#candidate_table').DataTable({
        dom: 'Btrip',  // Changed from 'Bfrtip' to 'Btrip'
        scrollX: true,
        scrollY: '60vh',
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 1
        },
        columnDefs: [
            { targets: '_all', width: '120px' }
        ]
    });

    // Handle resume download button clicks
    $('.get-score-btn').on('click', function(e) {
        console.log('Resume download initiated for: ' + $(this).attr('title'));
    });

    // Handle copy link functionality
    $('.copy-link-btn').on('click', function() {
        const link = $(this).data('link');
        
        const tempInput = $('<textarea>');
        $('body').append(tempInput);
        tempInput.val(link).select();
        
        document.execCommand('copy');
        tempInput.remove();
        
        $(this).tooltip({
            title: 'Copied!',
            trigger: 'manual'
        }).tooltip('show');
        
        setTimeout(() => {
            $(this).tooltip('hide');
        }, 2000);
    });
});

function addToCart(candidateId, price) {
    $.ajax({
        url: 'add_to_cart.php',
        type: 'POST',
        data: {
            candidate_id: candidateId,
            price: price
        },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success) {
                    // Update cart count in navbar
                    $('#cart-count').text(data.cartCount);
                    
                    // Change the button to "Go to Cart"
                    const button = $(`button[onclick="addToCart(${candidateId}, ${price})"]`);
                    button.html('Go to Cart <i class="fa fa-shopping-cart"></i>')
                           .attr('onclick', 'window.location.href = "cart_page.php"')
                           .removeClass('btn-primary')
                           .addClass('btn-success');
                    
                    alert('Candidate added to cart successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert('Error processing response');
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            alert('Error adding candidate to cart');
        }
    });
}
// Update the cart count when the page loads
function updateAllCartCounts() {
    $.ajax({
        url: 'get_cart_count.php',
        type: 'GET',
        success: function(response) {
            try {
                const data = JSON.parse(response);
                const count = data.cartCount;
                // Update all cart count displays
                $('#cart-count').text(count);
                $('#cart-count-mobile').text(count);
                $('#cart-count-dropdown').text(count);
            } catch (e) {
                console.error('Error parsing cart count:', e);
            }
        }
    });
}

// Initialize when document is ready
$(document).ready(function() {
    updateAllCartCounts();
    
    // Ensure mobile menu closes when clicking outside
    $(document).click(function(event) {
        const $navbar = $('.navbar');
        const $navbarToggler = $('.navbar-toggler');
        
        if (!$navbar.is(event.target) 
            && $navbar.has(event.target).length === 0 
            && !$navbarToggler.is(event.target)
            && $('.navbar-collapse').hasClass('show')) {
            $navbarToggler.click();
        }
    });
});
    </script>
    <?php
  
    require_once '..\footer.php';
    ?>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>