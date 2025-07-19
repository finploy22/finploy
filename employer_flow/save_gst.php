<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../db/connection.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employer_id = intval($_POST['employer_id'] ?? 0);
    $gst_number = trim($_POST['gst_number'] ?? '');
    $gst_address = trim($_POST['gst_address'] ?? '');

    if ($employer_id > 0 && $gst_number !== "" && $gst_address !== "") {

        // Step 1: Check if record exists
        $check = $conn->prepare("SELECT gst FROM employer_add_details WHERE employer_id = ?");
        if (!$check) {
            $response['message'] = "Prepare failed (SELECT): " . $conn->error;
            echo json_encode($response);
            exit;
        }

        $check->bind_param("i", $employer_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Step 2: Update
            $stmt = $conn->prepare("UPDATE employer_add_details SET gst = ?, address = ? WHERE employer_id = ?");
            if (!$stmt) {
                $response['message'] = "Prepare failed (UPDATE): " . $conn->error;
                echo json_encode($response);
                exit;
            }
            $stmt->bind_param("ssi", $gst_number, $gst_address, $employer_id);
        } else {
            // Step 3: Insert
            $stmt = $conn->prepare("INSERT INTO employer_add_details (employer_id, gst, address) VALUES (?, ?, ?)");
            if (!$stmt) {
                $response['message'] = "Prepare failed (INSERT): " . $conn->error;
                echo json_encode($response);
                exit;
            }
            $stmt->bind_param("iss", $employer_id, $gst_number, $gst_address);
        }

        // Step 4: Execute and respond
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "GST details saved successfully.";
        } else {
            $response['message'] = "Execution failed: " . $stmt->error;
        }

        // Step 5: Clean up
        if ($stmt) $stmt->close();
        if ($check) $check->close();

    } else {
        $response['message'] = "All fields are required.";
    }
}

$conn->close();
echo json_encode($response);
exit;
