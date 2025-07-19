<?php
include '../db/connection.php';
session_start();

if (!isset($_SESSION['employer_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first',
        'inCart' => []
    ]);
    exit;
}

$userId = $_SESSION['employer_id'];

// Get all candidate IDs in cart for this user
$query = $conn->prepare("SELECT candidate_id FROM user_cart WHERE user_id = ?");
$query->bind_param("i", $userId);
$query->execute();
$result = $query->get_result();

$inCartItems = [];
while ($row = $result->fetch_assoc()) {
    $inCartItems[] = $row['candidate_id'];
}

echo json_encode([
    'success' => true,
    'inCart' => $inCartItems
]);

$conn->close();
?>