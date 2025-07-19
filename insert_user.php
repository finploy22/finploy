<?php
//-----------------------------------------------------------------
// CONFIG & BOOTSTRAP
//-----------------------------------------------------------------
session_start();
date_default_timezone_set('Asia/Kolkata');

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log');
error_reporting(E_ALL);

include 'db/connection.php';

//-----------------------------------------------------------------
// HELPERS
//-----------------------------------------------------------------
function generateDefaultPassword(): string
{
    // fin@ + zero‑padded 4‑digit random number (0000‑9999)
    return 'fin@' . str_pad(strval(mt_rand(0, 9999)), 4, '0', STR_PAD_LEFT);
}

//-----------------------------------------------------------------
// HANDLE POST
//-----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'error';
    exit;
}

$logintype   = $_POST['logintype']   ?? '';
$name        = $_POST['name']        ?? null;
$mobile      = $_POST['mobile']      ?? '';
$auth_method = $_POST['auth_method'] ?? 'password';

$created = $updated = date('Y-m-d H:i:s');

//-----------------------------------------------------------------
// CHOOSE TABLE
//-----------------------------------------------------------------
switch ($logintype) {
    case 'candidate': $table = 'candidates'; break;
    case 'partner'  : $table = 'associate';  break;
    case 'employer' : $table = 'employers';  break;
    default:
        echo 'error';  // invalid logintype
        exit;
}

//-----------------------------------------------------------------
// LOOK UP USER
//-----------------------------------------------------------------
$stmt = $conn->prepare(
    "SELECT `username`, `mobile_number`, `password`, `otp`
     FROM `$table`
     WHERE `mobile_number` = ?"
);
$stmt->bind_param('s', $mobile);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    //---------------- EXISTING USER ----------------
    $stmt->bind_result($existingUsername, $existingMobile,
                       $existingPassword, $existingOtp);
    $stmt->fetch();
    $stmt->close();

    // update last‑login timestamp
    $updateStmt = $conn->prepare(
        "UPDATE `$table` SET `updated` = ? WHERE `mobile_number` = ?"
    );
    $updateStmt->bind_param('ss', $updated, $mobile);
    $updateStmt->execute();
    $updateStmt->close();

    //---------------- OTP LOGIN --------------------
    if ($auth_method === 'otp') {
        // store placeholder 'otp' only once
        if (empty($existingOtp)) {
            $saveOtp = $conn->prepare(
                "UPDATE `$table` SET `otp` = 'otp' WHERE `mobile_number` = ?"
            );
            $saveOtp->bind_param('s', $mobile);
            $saveOtp->execute();
            $saveOtp->close();
        }

        $_SESSION['name']      = $existingUsername;
        $_SESSION['mobile']    = $existingMobile;
        $_SESSION['user_type'] = $logintype;

        echo 'matching';       // FRONT‑END: case 'matching'
        $conn->close();
        exit;
    }

    //---------------- PASSWORD LOGIN ---------------
    $password = $_POST['password'] ?? '';

    if (empty($existingPassword)) {
        // first‑time password set
        $updatePass = $conn->prepare(
            "UPDATE `$table` SET `password` = ? WHERE `mobile_number` = ?"
        );
        $updatePass->bind_param('ss', $password, $mobile);
        $updatePass->execute();
        $updatePass->close();

        $_SESSION['name']      = $existingUsername;
        $_SESSION['mobile']    = $existingMobile;
        $_SESSION['user_type'] = $logintype;
        echo 'matching';
    } elseif ($existingPassword === $password) {
        $_SESSION['name']      = $existingUsername;
        $_SESSION['mobile']    = $existingMobile;
        $_SESSION['user_type'] = $logintype;
        echo 'matching';
    } else {
         $existingPassword;
        echo 'notmatching';    // FRONT‑END: case 'notmatching'
    }

} else {
    //---------------- NEW USER ----------------
    $stmt->close();

    if ($auth_method === 'password') {
        // PASSWORD REGISTRATION
        $password  = $_POST['password'] ?? '';
        $otp       = 'otp';            // placeholder
        $generated = null;
    } else { // === OTP REGISTRATION ===
        $password  = generateDefaultPassword(); // fin@####
        $otp       = 'otp';
        $generated = $password;
    }

    $insert = $conn->prepare(
        "INSERT INTO `$table`
         (`username`, `mobile_number`, `password`, `otp`, `created`)
         VALUES (?, ?, ?, ?, ?)"
    );
    $insert->bind_param('sssss', $name, $mobile, $password, $otp, $created);

    if ($insert->execute()) {
        $_SESSION['name']      = $name;
        $_SESSION['mobile']    = $mobile;
        $_SESSION['user_type'] = $logintype;

        if ($generated) {
            // OTP sign‑up: include generated password after a pipe
            echo 'success';   // FRONT‑END sees "success" first
        } else {
            echo 'success';                // FRONT‑END: case 'success'
        }
    } else {
        echo 'error';
    }

    $insert->close();
}

$conn->close();
?>
