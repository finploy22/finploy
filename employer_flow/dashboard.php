<?php
session_start(); // Start the session

// // Check if the username is set in the session
// if (!isset($_SESSION['username'])) {
//     // If the username is not set, redirect to the login page
//     header('Location: login.html');
//     exit;
// }

// Database connection details
// $host = 'localhost';
// $dbusername = "finployin_dev_user";
// $password_db = "Finindia@23";
// $database = "finployin_dev_user";
$host = 'localhost';
$dbusername = 'root';
$password_db = '';
$database = 'finployin_dev_user';


// Resume download handler
if (isset($_GET['download']) && isset($_GET['filename'])) {
    // Create connection
    $conn = new mysqli($host, $dbusername, $password_db, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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


// Main database connection
$conn = new mysqli($host, $dbusername, $password_db, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all candidate details with resume path
$query = "SELECT id, user_id, username, mobile_number, gender, employed, current_company, destination, 
          sales_experience, work_experience, current_location, current_salary, 
          `hl/lap`, personal_loan, business_loan, education_loan, gold_loan, 
          credit_cards, casa, others, unique_link, associate_id, associate_name, 
          associate_mobile, associate_link, jobrole, companyname, location, salary, 
          Sales, `Credit_dept`, `HR/Training`, `Legal/compliance/Risk`, Operations, 
          Others1, created, modified,resume 
          FROM candidate_details";
$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Details Dashboard</title>
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicons/favicon.ico">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
    <link rel="stylesheet" href="css/deeesha_style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Latest Font Awesome CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        <?php include 'styles.php'; ?>
       /* General table styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
/*.btn-group-sm>.btn, .btn-sm {*/
/*     padding:0 !impartant; */
/*     font-size: .875rem; */
/*    line-height: 1.5;*/
/*    border-radius: .2rem;*/
/*}*/

th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #f4f4f4;
    color: #333;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

/* Button styles */
.get-score-btn {
    background-color: #4EA647;
    color: white;
    padding: 5px 15px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 12px;
    transition: background-color 0.3s ease;
    border:none;
}

.get-score-btn:hover {
    background-color: #4EA647;
    cursor: pointer;
}


.navbar-expand-md{
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px !important;
    background: white;
}    
/* Table responsive styles */
@media (max-width: 768px) {
    table {
        font-size: 12px;
        margin: 0;
        overflow-x: auto;
        display: block;
    }

    th, td {
        padding: 8px;
    }
}

/* DataTable enhancements */
.dataTables_wrapper {
    padding: 20px;
}

.dataTables_wrapper .dataTables_filter {
    margin-bottom: 10px;
}

.dataTables_wrapper .dataTables_length select {
    padding: 5px;
}

.dt-buttons {
    padding: 10px;
}

/* Alternate row styling */
tbody tr:nth-child(odd) {
    background-color: #f4f4f4;
}
.logout {
    margin-top: auto; /* Automatically push the logout button to the bottom */
}
.logout-btn {
	box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px !important;
	background: white;
	border: none;
	padding: 4px 10px;
	border: ;
	border-radius: 10px;
    font-family: "Poppins", serif;
    font-weight: bold;
    font-style: normal;
}
.logout-btn i {
    font-size: 16px; /* Change the size of the icon */
    color: #ff0000; /* Set the color to red */
    margin-right: 5px; /* Add space between the icon and text */
    transition: color 0.3s ease; /* Add a hover transition effect */
}
#candidate_table {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
}

#candidate_table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Only the first column (ID) becomes sticky */
#candidate_table thead tr th:first-child,
#candidate_table tbody tr td:first-child {
    position: sticky;
    left: 0;
    z-index: 30;
    background-color: #D2D7D6 !important;
}

/* Ensure other columns scroll normally */
#candidate_table thead tr th:not(:first-child) {
    position: static;
}

#candidate_table tbody tr td:not(:first-child) {
    position: static;
}

/* Alternate row coloring for the first column */
#candidate_table tbody tr:nth-child(even) td:first-child {
    background-color: #f9f9f9 !important;
}

#candidate_table tbody tr td:first-child {
    background-color: white !important;
}
.dataTables_wrapper .dataTables_filter input {
	border:none;
	border-radius: 50px !important;
	padding: 5px;
	background-color: transparent;
	margin-bottom: 66px !important;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px !important;
    margin-top: -15px !important;
}
div.dt-buttons {
	float: left;
	margin-top: 51px !important;
}
button.dt-button, div.dt-button, a.dt-button, input.dt-button {
	border-radius: 10px !important;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px !important;
    border:none !important;
    background: none !important;
}
/* Excel button icon color */
.buttons-excel i {
    color:#175DA8;
    padding: 0px 6px;

}

/* PDF button icon color */
.buttons-pdf i {
    color:#175DA8; /* Orange */
    padding: 0px 6px;
}

/* Print button icon color */
.buttons-print i {
    color:#175DA8; /* Blue */
    padding: 0px 6px;
}

/* Copy button icon color */
.buttons-copy i {
    color: #175DA8; /* Purple */
    padding: 0px 6px;
}

/* CSV button icon color */
.buttons-csv i {
    color: #175DA8; /* Yellow */
    padding: 0px 6px;
}
.dataTables_scroll{
    background: none !important;
}
table.dataTable.hover > tbody > tr:hover > *, table.dataTable.display > tbody > tr:hover > * {
	box-shadow: none !important;
}
table.dataTable.stripe > tbody > tr.odd > *, table.dataTable.display > tbody > tr.odd > * {
	/* box-shadow: inset 0 0 0 9999px rgba(0, 0, 0, 0.023); */
    box-shadow: none !important;
    /* background-color: #E9F1FB !important; */
}
table.dataTable.stripe > tbody > tr.odd > *, table.dataTable.display > tbody > tr.even > * {
	/* box-shadow: inset 0 0 0 9999px rgba(0, 0, 0, 0.023); */
    box-shadow: none !important;
    background-color: #E9F1FB !important;
}
.sorting{
    background: #175DA8 !important;
    color:white !important;
}
#candidate_table tbody tr:nth-child(2n) td:first-child {
	background-color: #E9F1FB !important;
}
#candidate_table tbody tr td:first-child {
	background-color: white !important;
}
.heading-dashboard {
	color: #175DA8;
	margin-bottom: -44px;
	margin-left: 33px;
    font-family: "Poppins", serif;
    font-weight: 400;
    font-style: normal;
	margin-top: 40px;
}
#candidate_table tbody tr td:not(:first-child) {
	font-family: "Poppins", serif;
}
.dataTables_wrapper .dataTables_info {
    font-family: "Poppins", serif;
    font-weight: 400;
    font-style: normal;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
	color: #333 !important;
	border:none !important;
	background: #E9F1FB;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
font-family: "Poppins", serif;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
	font-family: "Poppins", serif;
}
    </style>
</head>
<body class="hero-anime">
<div class="navigation-wrap bg-light start-header start-style">
    <div class="container1">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-md navbar-light">
                    <a class="navbar-brand" href="index.php" target="_blank">
                        <img src="image/logo.png" alt="Deeesha Logo">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Add Logout Button -->
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <form action="logout.php" method="POST">
                                    <button class=" logout-btn nav-link" type="submit" name="logout"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>


   
    <!-- <div class="section full-height">
    <div class="topping">
        Logout button
        <div class="logout">
            <form action="logout.php" method="POST">
                <button class="logout-btn" type="submit" name="logout">Logout</button>
            </form>
        </div>
    </div>
</div> -->
<span> <h4 class="heading-dashboard">Candidates Table</h4></span>
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
                            <th>Unique Link</th>
                            <th>Open Link</th>
                            <th>Closedlink25</th>
                            <th>Closedlink50</th>
                            <th>Associate ID</th>
                            <th>Associate Name</th>
                            <th>Associate Mobile</th>
                            <th>Associate Link</th>
                            <th>Job Role</th>
                            <th>Company Name</th>
                            <th>Location</th>
                            <th>Salary</th>
                            <th>Sales</th>
                            <th>Credit Dept</th>
                            <th>HR/Training</th>
                            <th>Legal/Compliance/Risk</th>
                            <th>Operations</th>
                            <th>Others</th>
                            <th>Created</th>
                            <th>Modified</th>
                            <th>Add to Cart</th>
                            <!-- <th>Resume</th> -->
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Populate table rows
                        // $row = mysqli_fetch_assoc($result);
                        // // print_r(  $row);
                        while ($row = mysqli_fetch_assoc($result)) {
                           
                            // echo "<tr>";
                            // // echo "<td></td>";
                            echo "<tr>";
                            // echo "<td></td>";
                          
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['mobile_number']) . "</td>";
                            if (!empty($row['resume'])) {
                                $safe_filename = htmlspecialchars($row['resume']);
                                echo "<td><a href='?download=true&filename=" . urlencode($safe_filename) . "' class='btn btn-primary btn-sm get-score-btn' title='Download Resume'>Veiw CV</a></td>";
                            } else {
                                echo "<td>No Resume</td>";
                            }
                            echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['employed']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['current_company']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sales_experience']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['work_experience']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['current_location']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['current_salary']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['hl/lap']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['personal_loan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['business_loan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['education_loan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['gold_loan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['credit_cards']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['casa']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['others']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['unique_link']) . "</td>";
                            // if (!empty($row['unique_link'])) {
                                $unique_link = "https://finploy.in/admin/fetchdetails.php?unique_link=" . htmlspecialchars(base64_encode($row['mobile_number']));
                                echo "<td>
                                    $unique_link 
                                    <button class='btn btn-sm btn-secondary copy-link-btn' data-link='" . htmlspecialchars($unique_link) . "'>
                                      copy
                                    </button>
                                </td>";
                                $unique_link1 = "https://finploy.in/sample/closedlink.php?unique_link=" . htmlspecialchars(base64_encode($row['mobile_number']));
                                echo "<td>
                                    $unique_link1 
                                    <button class='btn btn-sm btn-secondary copy-link-btn' data-link='" . htmlspecialchars($unique_link1) . "'>
                                      copy
                                    </button>
                                </td>";
                                $unique_link2 = "https://finploy.in/sample/closedlink50.php?unique_link=" . htmlspecialchars(base64_encode($row['mobile_number']));
                                echo "<td>
                                    $unique_link2 
                                    <button class='btn btn-sm btn-secondary copy-link-btn' data-link='" . htmlspecialchars($unique_link2) . "'>
                                      copy
                                    </button>
                                </td>";
                            // } else {
                            //     echo "<td>No Unique Link</td>";
                            // }
                            echo "<td>" . htmlspecialchars($row['associate_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['associate_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['associate_mobile']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['associate_link']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['jobrole']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['companyname']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['salary']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Sales']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Credit_dept']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['HR/Training']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Legal/compliance/Risk']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Operations']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Others1']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['modified']) . "</td>";
                            echo "<td><button class='btn btn-primary btn-sm'>Add to Cart</button></td>";;
                            // if (!empty($row['resume'])) {
                            //     $safe_filename = htmlspecialchars($row['resume']);
                            //     echo "<td><a href='?download=true&filename=" . urlencode($safe_filename) . "' class='btn btn-primary btn-sm get-score-btn' title='Download Resume'>Get Score</a></td>";
                            // } else {
                            //     echo "<td>No Resume</td>";
                            // }
                           
                            
                            echo "</tr>";
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
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
$(document).ready(function() {
    $('#candidate_table').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollX: true,
        scrollY: '60vh',
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 1 // This will help with the first column
        },
        columnDefs: [
            { targets: '_all', width: '120px' }
        ],  // Added missing comma here
        language: {
            search: ''
        }
    });  // Added missing curly brace here

    // Rest of the code...;

    const searchContainer = document.querySelector('#candidate_table_filter');
    const label = searchContainer.querySelector('label');
    const input = label.querySelector('input');
    searchContainer.removeChild(label);
    
    const searchWrapper = document.createElement('div');
    searchWrapper.className = 'custom-search-wrapper';
    searchWrapper.style.position = 'relative';
    searchWrapper.style.display = 'inline-block';
    
    const searchIcon = document.createElement('i');
    searchIcon.className = 'fa fa-search';
    searchIcon.style.position = 'absolute';
    searchIcon.style.left = '16px';
    searchIcon.style.top = '13%';
    searchIcon.style.transform = 'translateY(-50%)';
    searchIcon.style.color = '#666';
    
    input.setAttribute('placeholder', 'Search here...');
    input.style.paddingLeft = '35px';
    input.style.width = '250px';
    input.style.height = '38px';
    
    searchWrapper.appendChild(searchIcon);
    searchWrapper.appendChild(input);
    searchContainer.appendChild(searchWrapper);

    // Select all buttons
const excelButton = document.querySelector('.buttons-excel');
const pdfButton = document.querySelector('.buttons-pdf');
const printButton = document.querySelector('.buttons-print');
const copyButton = document.querySelector('.buttons-copy');
const csvButton = document.querySelector('.buttons-csv');

// Create <i> elements for each button
const excelIcon = document.createElement('i');
const pdfIcon = document.createElement('i');
const printIcon = document.createElement('i');
const copyIcon = document.createElement('i');
const csvIcon = document.createElement('i');

// Add classes for Font Awesome icons or your preferred icon set
excelIcon.classList.add('fa', 'fa-file-excel'); // Font Awesome Excel icon
pdfIcon.classList.add('fa', 'fa-file-pdf');    // Font Awesome PDF icon
printIcon.classList.add('fa', 'fa-print');      // Font Awesome Print icon
copyIcon.classList.add('fa', 'fa-copy');        // Font Awesome Copy icon
csvIcon.classList.add('fa', 'fa-file-csv');     // Font Awesome CSV icon

// Insert icons inside the buttons
excelButton.insertBefore(excelIcon, excelButton.firstChild);
pdfButton.insertBefore(pdfIcon, pdfButton.firstChild);
printButton.insertBefore(printIcon, printButton.firstChild);
copyButton.insertBefore(copyIcon, copyButton.firstChild);
csvButton.insertBefore(csvIcon, csvButton.firstChild);


    // Add the CSS styles
    const style = document.createElement('style');
    style.textContent = `
        #candidate_table_filter {
            margin-bottom: 20px;
        }
        
        #candidate_table_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px 8px 35px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        #candidate_table_filter input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
    `;
    document.head.appendChild(style);

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

    </script>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>