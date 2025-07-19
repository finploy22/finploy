<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db/connection.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $candidate_id = isset($_POST['candidate_id']) ? intval($_POST['candidate_id']) : 0;
    $mobile = isset($_SESSION['mobile']) ? $_SESSION['mobile'] : '';

    if ($candidate_id <= 0 || empty($mobile)) {
        echo json_encode(["status" => "error", "message" => "Invalid Candidate ID or Mobile number"]);
        exit;
    }

    // Fetch employer
    $employerQuery = "SELECT id, username FROM employers WHERE mobile_number = ?";
    $employerStmt = $conn->prepare($employerQuery);
    $employerStmt->bind_param("s", $mobile);
    $employerStmt->execute();
    $employerResult = $employerStmt->get_result();
    $employer = $employerResult->fetch_assoc();
    $employerStmt->close();

    if (!$employer) {
        echo json_encode(["status" => "error", "message" => "Employer not found"]);
        exit;
    }

    $employer_id = $employer['id'];
    $employer_username = $employer['username'];

    // Fetch candidate
    $candidateQuery = "SELECT id, username, mobile_number FROM candidate_details WHERE id = ?";
    $candidateStmt = $conn->prepare($candidateQuery);
    $candidateStmt->bind_param("i", $candidate_id);
    $candidateStmt->execute();
    $candidateResult = $candidateStmt->get_result();
    $candidate = $candidateResult->fetch_assoc();
    $candidateStmt->close();

    if (!$candidate) {
        echo json_encode(["status" => "error", "message" => "Candidate not found"]);
        exit;
    }

    // Fetch subscription
    $creditQuery = "SELECT id, profile_credits_available, amount, razorpay_payment_id, sub_plan
                    FROM subscription_payments 
                    WHERE employer_mobile = ? 
                    AND profile_credits_available >=1
                    AND plan_status = 'ACTIVE' 
                    AND status = 'success'
                    ORDER BY created ASC LIMIT 1";

    $creditStmt = $conn->prepare($creditQuery);
    $creditStmt->bind_param("s", $mobile);
    $creditStmt->execute();
    $creditResult = $creditStmt->get_result();
    $subscription = $creditResult->fetch_assoc();
    $creditStmt->close();
    if (!$subscription || $subscription['profile_credits_available'] <= 0) {
        echo json_encode(["status" => "error", "message" => "Insufficient credits"]);
        exit;
    }


        $days = 0;

        switch ($subscription['sub_plan']) {
            case '1M':
                $days = 30;
                break;
            case '1MS':
                $days = 30;
                break;
            case '3M':
                $days = 90;
                break;
            case '6M':
                $days = 180;
                break;
            case 'PAYG':
                $days = 90;
                break;
            default:
                $days = 0;
                break;
        }
        $insertQuery = "INSERT INTO order_items 
            (payment_id, candidate_id, price, employer_username, employer_id, purchased, expired, created_at, candidate_name, candidate_mobile, employer_mobile, expires_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, DATE_ADD(NOW(), INTERVAL ? DAY))";

        $insertStmt = $conn->prepare($insertQuery);
        $purchased = 1;
        $expired = 0; 
        $candidate_name = $candidate['username'];
        $candidate_mobile = $candidate['mobile_number'];
        $employer_mobile = $mobile;

        $insertStmt->bind_param(
            "sisssiisssi",
            $subscription['razorpay_payment_id'],
            $candidate_id,
            $subscription['amount'],
            $employer_username,
            $employer_id,
            $purchased,
            $expired,
            $candidate_name,
            $candidate_mobile,
            $employer_mobile,
            $days
        );


    if ($insertStmt->execute()) {
        $new_credit = max(0, $subscription['profile_credits_available'] - 1);
        if($new_credit ==0){
            $expired =1;
        }else{
            $expired = 0;
        }
        $subscription_id = $subscription['id'];

        $updateCreditQuery ="UPDATE subscription_payments SET profile_credits_available = ?, expired = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateCreditQuery);
        $updateStmt->bind_param("iii", $new_credit,$expired, $subscription_id);
        $updateStmt->execute();
        $updateStmt->close();

        echo json_encode([
            "status" => "success",
            "message" => "Order item inserted successfully, Credit updated",
            "credit_available" => $new_credit,
            "candidate_name" => $candidate_name,
            "candidate_mobile" => $candidate_mobile,
            "candidate_id" => $candidate_id,
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to insert order item"]);
    }

    $insertStmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
