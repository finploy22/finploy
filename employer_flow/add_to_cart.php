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



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id']) && isset($_POST['price'])) {
    $userId = $_SESSION['employer_id'];
    $candidateId = intval($_POST['candidate_id']);
    $price = floatval($_POST['price']);
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Check if candidate exists
        $checkCandidate = $conn->prepare("SELECT id FROM candidate_details WHERE id = ?");
        $checkCandidate->bind_param("i", $candidateId);
        $checkCandidate->execute();
        $candidateResult = $checkCandidate->get_result();
        
        if ($candidateResult->num_rows === 0) {
            throw new Exception("Invalid candidate ID");
        }
        
        // Check if already in cart
        $checkCart = $conn->prepare("SELECT id FROM user_cart WHERE user_id = ? AND candidate_id = ?");
        $checkCart->bind_param("ii", $userId, $candidateId);
        $checkCart->execute();
        $cartResult = $checkCart->get_result();
        
        if ($cartResult->num_rows > 0) {
            throw new Exception("Candidate already in cart");
        }
        
        // Add to cart
        $addToCart = $conn->prepare("INSERT INTO user_cart (user_id, candidate_id, price) VALUES (?, ?, ?)");
        $addToCart->bind_param("iid", $userId, $candidateId, $price);
        $addToCart->execute();
        
        // Get cart count
        $countCart = $conn->prepare("SELECT COUNT(*) as count FROM user_cart WHERE user_id = ?");
        $countCart->bind_param("i", $userId);
        $countCart->execute();
        $countResult = $countCart->get_result();
        $cartCount = $countResult->fetch_assoc()['count'];
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Added to cart successfully',
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