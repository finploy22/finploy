<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db/connection.php';

if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    die();
}
$isLoggedIn = isset($_SESSION['name']);
$loggedInName = $isLoggedIn ? $_SESSION['name'] : null;
$mobile = $isLoggedIn ? $_SESSION['mobile'] : null;
$firstLetter = $isLoggedIn ? strtoupper($loggedInName[0]) : null;
$dynamicColor = $isLoggedIn ? '#' . substr(md5($loggedInName), 0, 6) : null;

$query = "SELECT partner_image FROM associate WHERE username='$loggedInName' AND mobile_number='$mobile'";
$result = mysqli_query($conn, $query);

$candidateImage = '';
if ($row = mysqli_fetch_assoc($result)) {
    $candidateImage = $row['partner_image'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_update'])) {

    $updatedName = trim($_POST['username']);
    $updatedMobile = preg_replace('/[^0-9]/', '', $_POST['mobile_number']);
    $safeName = $conn->real_escape_string($updatedName);
    $safeMobile = $conn->real_escape_string($updatedMobile);

    // Optional password update
    $passwordQuery = "";
    if (!empty($_POST['password']) && !empty($_POST['repassword'])) {
        $updatedPassword = $_POST['password'];
        $rePassword = $_POST['repassword'];

        if ($updatedPassword !== $rePassword) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
            exit;
        }

        $safePassword = $conn->real_escape_string($updatedPassword);
        $passwordQuery = ", password='$safePassword'";
    }

    $userQuery = "SELECT user_id FROM candidates WHERE username='$loggedInName' AND mobile_number='$mobile'";
    $userResult = $conn->query($userQuery);

    if ($userResult && $userResult->num_rows > 0) {
        $row = $userResult->fetch_assoc();
        $userId = $row['user_id'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }

    // Perform update
    $updateCandidates = "UPDATE candidates 
    SET username='$safeName', mobile_number='$safeMobile' $passwordQuery 
    WHERE user_id='$userId'";

    $updateDetails = "UPDATE candidate_details 
 SET username='$safeName', mobile_number='$safeMobile' 
 WHERE user_id='$userId'";

    $updateAssociates = "UPDATE associate 
                         SET username='$safeName', mobile_number='$safeMobile' $passwordQuery 
                         WHERE username='$loggedInName' AND mobile_number='$mobile'";

    $candidatesUpdated = $conn->query($updateCandidates);
    $associatesUpdated = $conn->query($updateAssociates);
    $detailUpdated = $conn->query($updateDetails);

    if ($candidatesUpdated && $associatesUpdated & $detailUpdated) {
        $_SESSION['name'] = $safeName;
        $_SESSION['mobile'] = $safeMobile;
        echo json_encode(['status' => 'success', 'message' => 'Profile Updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update Failed']);
    }
    exit;

}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $filename = time() . '_' . basename($file['name']);
    $upload_dir = '../assets/candidate_partner_images/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $upload_dir)) {

        $getUserId = "SELECT user_id FROM candidates WHERE username='$loggedInName' AND mobile_number='$mobile'";
        $result = $conn->query($getUserId);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['user_id'];

            // Update candidate_details table
            $updateCandidateDetails = "UPDATE candidate_details 
                                       SET candidate_image='$filename' 
                                       WHERE user_id='$userId'";
            $candidateDetailsUpdated = $conn->query($updateCandidateDetails);

            // Update associate table
            $updateAssociates = "UPDATE associate 
                                 SET partner_image='$filename'
                                 WHERE username='$loggedInName' AND mobile_number='$mobile'";
            $associatesUpdated = $conn->query($updateAssociates);

            if ($candidateDetailsUpdated && $associatesUpdated) {
                echo json_encode(['status' => 'success', 'message' => 'Profile Updated', 'filename' => $filename]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Update Failed']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
    }
    exit;
}
// Fetch current payment method
$res = $conn->query("SELECT payment_method FROM associate WHERE username='$loggedInName' AND mobile_number='$mobile'");
if ($res && $row = $res->fetch_assoc()) {
    $paymentMethod = $row['payment_method'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_payment_update'])) {
    $newPayment = $conn->real_escape_string($_POST['payment_method']);
    $update = $conn->query("UPDATE associate SET payment_method='$newPayment' WHERE username='$loggedInName' AND mobile_number='$mobile'");
    echo json_encode(['status' => $update ? 'success' : 'error', 'message' => 'Payment Option Updated Successfully']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="./css/partner.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .error-message {
            color: red;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <?php include 'partner_header.php'; ?>
    <div class="main-content">
    <div id="toast" style="
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #1DB954 ;
    color: white;
    padding: 10px 16px;
    border-radius: 6px;
    font-family: Poppins, sans-serif;
    font-size: 14px;
    font-weight: 500;
    display: none;
    z-index: 9999;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
">Payment Method Updated</div>

        <div class="container">
            <div class="profmain">
                <div class="profile-header">
                    <div class="profile-title">My Profile</div>
                </div>
                <div class="flexprofcard">
                    <div class="profile-card">
                        <div class="profile-avatar" style="background-color: <?php echo $dynamicColor; ?>"
                            id="avatarBox">
                            <?php if (!empty($candidateImage)): ?>
                                <img src="../assets/candidate_partner_images/<?php echo $candidateImage; ?>">
                            <?php else: ?>
                                <?php echo strtoupper($firstLetter); ?>
                            <?php endif; ?>
                            <div class="avaedit" onclick="enableImageEditing()">
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none"
                                    onchange="uploadImage()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M4 15.9997L3 19.9997L7 18.9997L18.586 7.4137C18.9609 7.03864 19.1716 6.53003 19.1716 5.9997C19.1716 5.46937 18.9609 4.96075 18.586 4.5857L18.414 4.4137C18.0389 4.03876 17.5303 3.82812 17 3.82812C16.4697 3.82813 15.9611 4.03876 15.586 4.4137L4 15.9997Z"
                                        stroke="#175DA8" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M4 15.9995L3 19.9995L7 18.9995L17 8.99951L14 5.99951L4 15.9995Z"
                                        fill="#175DA8" />
                                    <path d="M14 5.99951L17 8.99951M12 19.9995H20" stroke="#175DA8" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>

                        <div class="profile-name"><?php echo ucfirst($loggedInName); ?></div>
                        <div class="profile-contact">
                            <div class="phicon">
                                <img src="../assets/ic_baseline-phone-in-talk.svg">
                            </div>
                            <div class="profile-mobile">+91 <?php echo $mobile; ?></div>
                        </div>
                        <div class="profile-payment-method custom-dropdown">
                            <div class="selected-option" onclick="toggleDropdown()">
                                <i id="selectedIcon" class="fa-solid fa-credit-card"></i>
                                <span id="selectedText">Select Payment</span>
                                <span class="dropdown-arrow">&#9662;</span>
                            </div>
                            <ul class="dropdown-options" id="paymentOptions">
                                <li onclick="selectPayment('Google Pay', 'fa-brands fa-google-pay')">
                                    <i class="fa-brands fa-google-pay"></i> Google Pay
                                </li>
                                <li onclick="selectPayment('PhonePe', 'fa-solid fa-mobile-screen')">
                                    <i class="fa-solid fa-mobile-screen"></i> PhonePe
                                </li>
                                <li onclick="selectPayment('UPI ID', 'fa-solid fa-id-card')">
                                    <i class="fa-solid fa-id-card"></i> UPI ID
                                </li>
                                <li onclick="selectPayment('QR Code', 'fa-solid fa-qrcode')">
                                    <i class="fa-solid fa-qrcode"></i> QR Code
                                </li>
                                <li onclick="selectPayment('Bank Transfer', 'fa-solid fa-building-columns')">
                                    <i class="fa-solid fa-building-columns"></i> Bank Transfer
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php
                    $query = "SELECT password FROM associate WHERE username = '$loggedInName' AND mobile_number = '$mobile'";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $passwordField = $row['password'];
                        if ($passwordField === 'otp_verified' || $passwordField === '') { ?>
                            <div class="profile-detail-card">
                                <form id="profileForm">
                                    <div class="profile-detail-header">
                                        <div class="profile-detail-title">Profile Details:</div>
                                        <div class="edit-icon" onclick="enableEditing()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path
                                                    d="M4 15.9997L3 19.9997L7 18.9997L18.586 7.4137C18.9609 7.03864 19.1716 6.53003 19.1716 5.9997C19.1716 5.46937 18.9609 4.96075 18.586 4.5857L18.414 4.4137C18.0389 4.03876 17.5303 3.82812 17 3.82812C16.4697 3.82813 15.9611 4.03876 15.586 4.4137L4 15.9997Z"
                                                    stroke="#175DA8" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M4 15.9995L3 19.9995L7 18.9995L17 8.99951L14 5.99951L4 15.9995Z"
                                                    fill="#175DA8" />
                                                <path d="M14 5.99951L17 8.99951M12 19.9995H20" stroke="#175DA8" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Name:</div>
                                            <div class="field-value">
                                                <input type="text" class="inputfield" name="username"
                                                    value="<?php echo ucfirst($loggedInName); ?>" readonly />
                                            </div>
                                            <span id="nameError" class="error-message"></span>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Contact Number:</div>
                                            <div class="field-value"><span>+91</span><input type="text" name="mobile_number"
                                                    class="inputfield" value="<?php echo $mobile; ?> " readonly /></div>
                                            <span id="mobileError" class="error-message"></span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php } else { ?>
                            <div class="profile-detail-card">
                                <form id="profileForm">
                                    <div class="profile-detail-header">
                                        <div class="profile-detail-title">Profile Details:</div>
                                        <div class="edit-icon" onclick="enableEditing()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path
                                                    d="M4 15.9997L3 19.9997L7 18.9997L18.586 7.4137C18.9609 7.03864 19.1716 6.53003 19.1716 5.9997C19.1716 5.46937 18.9609 4.96075 18.586 4.5857L18.414 4.4137C18.0389 4.03876 17.5303 3.82812 17 3.82812C16.4697 3.82813 15.9611 4.03876 15.586 4.4137L4 15.9997Z"
                                                    stroke="#175DA8" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M4 15.9995L3 19.9995L7 18.9995L17 8.99951L14 5.99951L4 15.9995Z"
                                                    fill="#175DA8" />
                                                <path d="M14 5.99951L17 8.99951M12 19.9995H20" stroke="#175DA8" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Name:</div>
                                            <div class="field-value">
                                                <input type="text" class="inputfield" name="username"
                                                    value="<?php echo ucfirst($loggedInName); ?>" readonly />

                                            </div>
                                            <span id="nameError" class="error-message"></span>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Contact Number:</div>
                                            <div class="field-value"><span>+91</span><input type="text" name="mobile_number"
                                                    class="inputfield" value="<?php echo $mobile; ?> " readonly /></div>
                                            <span id="mobileError" class="error-message"></span>
                                        </div>
                                    </div>
                                    <div class="fieldlabels">
                                        <div class="field">
                                            <div class="field-label">Password:</div>
                                            <div class="field-value passfie">
                                                <input type="password" id="password" name="password" class="inputfield"
                                                    value="<?php echo htmlspecialchars($passwordField); ?>" readonly />
                                                <img id="toggle-password" class="toggle-icon" src="./assets/visibility_off.svg"
                                                    onclick="togglePassword('password', 'toggle-password')">
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="field-label">Re-Enter Password:</div>
                                            <div class="field-value passfie">
                                                <input type="password" name="repassword" id="repassword" class="inputfield"
                                                    value="<?php echo htmlspecialchars($passwordField); ?>" readonly />
                                                <img id="toggle-repassword" class="toggle-icon"
                                                    src="./assets/visibility_off.svg"
                                                    onclick="togglePassword('repassword', 'toggle-repassword')">
                                            </div>
                                        </div>
                                        <div class="subbtn" id="saveButton" onclick="sendProfileUpdate()">
                                            <div class="submitwrapper">
                                                <img src="../assets/fluent_save-24-filled.svg">
                                                <span>Save Details</span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php }
                    } else {
                        echo "<p>User not found</p>";
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function validateName() {
            const nameInput = document.querySelector("input[name='username']");
            const nameError = document.getElementById('nameError');
            const name = nameInput.value.trim();
            const nameRegex = /^[a-zA-Z\s]+$/;

            if (!nameRegex.test(name)) {
                nameError.textContent = "Name can only contain letters and spaces.";
                return false;
            } else {
                nameError.textContent = '';
                return true;
            }
        }

        function validateMobile() {
            const mobileInput = document.querySelector("input[name='mobile_number']");
            const mobileError = document.getElementById('mobileError');
            const mobile = mobileInput.value.trim();
            const mobileRegex = /^[0-9]{10}$/;

            if (!mobileRegex.test(mobile)) {
                mobileError.textContent = "Mobile number must be exactly 10 digits.";
                return false;
            } else {
                mobileError.textContent = '';
                return true;
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('profileForm');

            form.addEventListener('focusout', (event) => {
                const target = event.target;

                if (target.name === 'username') {
                    validateName();
                } else if (target.name === 'mobile_number') {
                    validateMobile();
                }
            });
        });

    </script>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            icon.src = isPassword ? "./assets/visibility.svg" : "./assets/visibility_off.svg";
        }
        function enableEditing() {
            const inputs = document.querySelectorAll('.inputfield');
            inputs.forEach(input => input.removeAttribute('readonly'));
            const saveButton = document.getElementById('saveButton');
            saveButton.style.display = 'inline-flex';
            const addeditDiv = document.querySelector('.edit-icon');
            if (addeditDiv) {
                addeditDiv.style.border = '0.1px solid var(--Deeesha-Blue, #175DA8)';
            }
            const addeditDivFields = document.querySelectorAll('.profile-detail-card .field-value');
            addeditDivFields.forEach(field => {
                field.style.border = '0.1px solid var(--Deeesha-Blue, #175DA8)';
            });
            const fieldLabels = document.querySelectorAll('.fieldlabels');
            if (fieldLabels.length > 0) {
                const lastFieldLabel = fieldLabels[fieldLabels.length - 1];

                function updateFieldLabelMargin() {
                    const width = window.innerWidth;
                    if (width <= 454) {
                        lastFieldLabel.style.marginBottom = '15%';
                    } else if (width <= 760) {
                        lastFieldLabel.style.marginBottom = '10%';
                    } else {
                        lastFieldLabel.style.marginBottom = '6%';
                    }
                }
                updateFieldLabelMargin();
                window.addEventListener('resize', updateFieldLabelMargin);
            }
        }

        function sendProfileUpdate() {
            const isNameValid = validateName();
            const isMobileValid = validateMobile();

            if (!isNameValid || !isMobileValid) return;

            const formData = new FormData(document.getElementById('profileForm'));
            formData.append('ajax_update', true);

            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.querySelectorAll('.inputfield').forEach(input => input.setAttribute('readonly', true));
                        document.getElementById('saveButton').style.display = 'none';
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('AJAX error: ' + error);
                });
        }
    </script>


    <script>
        function enableImageEditing() {
            document.getElementById('avatarInput').click();
        }
        function uploadImage() {
            var input = document.getElementById('avatarInput');
            var file = input.files[0];

            if (file) {
                var formData = new FormData();
                formData.append("avatar", file);

                fetch(window.location.href, {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())  
                    .then(result => {
                        if (result.status === "success") {
                            // Change avatar immediately after upload
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var avatarBox = document.getElementById('avatarBox');
                                avatarBox.innerHTML = `
                        <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <div class="avaedit" onclick="enableImageEditing()" style="position: absolute; bottom: 10px; right: 10px; background: #fff; border-radius: 50%; padding: 8px; cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
                                <path d="M4 16L3 20L7 19L18.586 7.414C18.961 7.039 19.172 6.53 19.172 6C19.172 5.469 18.961 4.961 18.586 4.586L18.414 4.414C18.039 4.039 17.53 3.828 17 3.828C16.47 3.828 15.961 4.039 15.586 4.414L4 16Z" stroke="#175DA8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 16L3 20L7 19L17 9L14 6L4 16Z" fill="#175DA8"/>
                                <path d="M14 6L17 9M12 20H20" stroke="#175DA8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    `;
                                location.reload();
                            }
                            reader.readAsDataURL(file);
                        } else {
                            alert('Upload Failed: ' + result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Upload error: Server sent invalid response (maybe PHP error)');
                    });
            }
        }
    </script>

    <script>
        function toggleDropdown() {
            document.getElementById("paymentOptions").classList.toggle("show");
        }
        window.onclick = function (e) {
            if (!e.target.closest('.custom-dropdown')) {
                document.getElementById("paymentOptions").classList.remove("show");
            }
        }
    </script>

    <script>
        function selectPayment(name, iconClass) {
            document.getElementById("selectedText").innerText = name;
            document.getElementById("selectedIcon").className = iconClass;
            document.getElementById("paymentOptions").classList.remove("show");
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Same file
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const res = JSON.parse(xhr.responseText);
                    if (res.status === 'success') {
                        showToast("✨ Payment Method Updated");
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    }
                }
            };
            xhr.send("ajax_payment_update=1&payment_method=" + encodeURIComponent(name));
        }
        document.addEventListener("DOMContentLoaded", function () {
            const savedPayment = "<?php echo $paymentMethod; ?>";
            let iconClass = "fa-solid fa-credit-card";
            switch (savedPayment) {
                case "Google Pay": iconClass = "fa-brands fa-google-pay"; break;
                case "PhonePe": iconClass = "fa-solid fa-mobile-screen"; break;
                case "UPI ID": iconClass = "fa-solid fa-id-card"; break;
                case "QR Code": iconClass = "fa-solid fa-qrcode"; break;
                case "Bank Transfer": iconClass = "fa-solid fa-building-columns"; break;
            }
            if (savedPayment) {
                document.getElementById("selectedText").innerText = savedPayment;
                document.getElementById("selectedIcon").className = iconClass;
            }
        });

        // Toast function
        function showToast(message) {
            const toast = document.getElementById("toast");
            toast.innerHTML = message;
            toast.style.display = "block";
            setTimeout(() => {
                toast.style.display = "none";
            }, 5000);
        }
    </script>
</body>

</html>