<?php
include '../db/connection.php';

session_start();


// Get candidate id from POST
$candidate_id = isset($_POST['candidate_id']) ? (int) $_POST['candidate_id'] : 0;

// Ensure employer is logged in
$employer_id = isset($_SESSION['employer_id']) ? (int) $_SESSION['employer_id'] : 0;

if ($candidate_id <= 0 || $employer_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid candidate or employer']);
    exit;
}

/*
  Adjust the query based on your schema. In this example, we assume:
  
  - An "order_items" table links candidate IDs with payment IDs.
  - The payments table contains "employer_id", "status", and "buyed_status".
  - A completed payment with buyed_status=0 means the candidate access is still active.
*/
$query = "SELECT p.buyed_status, p.created_at 
          FROM payments p
          INNER JOIN order_items oi ON p.id = oi.payment_id
          WHERE p.employer_id = ?
            AND oi.candidate_id = ?
            AND p.status = 'completed'
            AND p.created_at >= DATE_SUB(NOW(), INTERVAL 2 DAY)
          ORDER BY p.created_at DESC
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $employer_id, $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Calculate if the purchase is still active
    $created_time = strtotime($row['created_at']);
    $current_time = time();
    $is_active = ($current_time - $created_time) < (2 * 24 * 60 * 60); // 2 days in seconds
    
    echo json_encode([
        'success' => true, 
        'buyed_status' => (int)$row['buyed_status'],
        'is_active' => $is_active
    ]);
} else {
    echo json_encode(['success' => true, 'buyed_status' => null, 'is_active' => false]);
}
?>
