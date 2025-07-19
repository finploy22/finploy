<?php
include '../db/connection.php';
session_start();
if (!isset($_SESSION['employer_id'])) {
    echo json_encode([
        'success' => false,
        'cartCount' => 0
    ]);
    exit;
}



$userId = $_SESSION['employer_id'];
$query = $conn->prepare("SELECT COUNT(*) as count FROM user_cart WHERE user_id = ?");
$query->bind_param("i", $userId);
$query->execute();
$result = $query->get_result();
$count = $result->fetch_assoc()['count'];

echo json_encode([
    'success' => true,
    'cartCount' => $count
]);

$conn->close();
?>