<?php
// Database configuration
$host = 'localhost';       // Hostname or IP address
$db_name = 'finployuk_dev_users'; // Database name
$username = 'root'; // MySQL username
$password = ''; // MySQL password

// $host = 'localhost';       // Hostname or IP address
// $db_name = 'harsh875_finploy_com'; // Database name
// $username = 'harsh875_finploy_user1'; // MySQL username
// $password = 'Make*45@23+67'; // MySQL password

// $host = 'localhost';       // Hostname or IP address
// $db_name = 'finploy_finploy_sg'; // Database name
// $username = 'finploy_user_sg'; // MySQL username
// $password = 'Finsg@2356'; // MySQL password

// $host = 'localhost';       // Hostname or IP address
// $db_name = 'intern25_finternhub_com'; // Database name
// $username = 'intern25_intern25'; // MySQL username
// $password = 'Mower*45#34&57'; // MySQL password


// $host = 'localhost';       // Hostname or IP address
// $db_name = 'finployuk_dev_users'; // Database name
// $username = 'root'; // MySQL username
// $password = ''; // MySQL password

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
