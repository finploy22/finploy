<?php
session_start();
$input = json_decode(file_get_contents('php://input'), true);

if ($input) {
    unset($_SESSION['planDetails']);
    $_SESSION['planDetails'] = [
        'subPlanName' => $input['subPlanName'],
        'amount' => $input['amount'],
        'subPlanCode' => $input['subPlanCode'],
        'planName' => $input['planName'],
        'jobPostingCredit' => $input['jobPostingCredit'],
        'profileAccess' => $input['profileAccess'],
        'planCode' => $input['planCode'],
    ];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>