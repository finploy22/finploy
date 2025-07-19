<?php
// Secure PDF Viewer

if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Prevent directory traversal
    $filePath = realpath(__DIR__ . '/../uploads/resumes/' . $file);

    // Ensure file exists and is inside the allowed directory
    if ($filePath && file_exists($filePath) && strpos($filePath, realpath(__DIR__ . '/../uploads/resumes/')) === 0) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $file . '"');
        readfile($filePath);
        exit;
    } else {
        die('Invalid file request.');
    }
} else {
    die('No file specified.');
}
