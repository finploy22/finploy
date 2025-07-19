<?php
include '../db/connection.php';
session_start();
if (!isset($_SESSION['employer_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first',
        'cartCount' => 0
    ]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id'])) {
    $userId = $_SESSION['employer_id'];
    $candidateId = intval($_POST['candidate_id']);
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Remove from cart
        $deleteQuery = $conn->prepare("DELETE FROM user_cart WHERE user_id = ? AND candidate_id = ?");
        $deleteQuery->bind_param("ii", $userId, $candidateId);
        $deleteQuery->execute();
        
        // Get updated cart count
        $countQuery = $conn->prepare("SELECT COUNT(*) as count FROM user_cart WHERE user_id = ?");
        $countQuery->bind_param("i", $userId);
        $countQuery->execute();
        $result = $countQuery->get_result();
        $cartCount = $result->fetch_assoc()['count'];
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart',
            'cartCount' => $cartCount
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'cartCount' => 0
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request',
        'cartCount' => 0
    ]);
}

$conn->close();
?>