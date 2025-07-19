<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db/connection.php';

$response = ['success' => false];

// Ensure employer_id is present and valid
if (isset($_GET['employer_id']) && intval($_GET['employer_id']) > 0) {
    $employer_id = intval($_GET['employer_id']);

    $stmt = $conn->prepare("SELECT employer_company_name, gst, address FROM employer_add_details WHERE employer_id = ?");
    $stmt->bind_param("i", $employer_id);
    $stmt->execute();
    $stmt->bind_result($company, $gst_number, $gst_address);

    if ($stmt->fetch()) {
        $response = [
            'success' => true,
            'company' => $company,
            'gst_number' => $gst_number,
            'gst_address' => $gst_address
        ];
    }

    $stmt->close();
}

$conn->close();

// Return only JSON (no whitespace, no warnings/errors before or after)
echo json_encode($response);
exit;
