<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../db/connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set default values
$accessCredits = 0;
$accessCreditsAvailable = 0;
$accessCreditsUsed = 0;
$percentageUsed = 0;
$expiryDate = "N/A";
$jobPostCredits = 0;
$jobPostCreditsAvailable = 0;

// Get subscription data if user is logged in
if (isset($_SESSION['mobile'])) {
    $employer_mobile = $_SESSION['mobile'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM `subscription_payments` 
                           WHERE employer_mobile = ? AND plan_status = 'ACTIVE' AND status = 'success' 
                           ORDER BY created DESC");

    if ($stmt) {
        $stmt->bind_param("s", $employer_mobile);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($subscription = $result->fetch_assoc()) {
                $accessCredits += intval($subscription['total_profile_credits']);
                $accessCreditsAvailable += intval($subscription['profile_credits_available']);

                $jobPostCredits += intval($subscription['total_jobpost_credits']);
                $jobPostCreditsAvailable += intval($subscription['jobpost_credits_available']);
            }


            $subscription = $result->fetch_assoc();

            // Calculate values if subscription exists
            // $accessCredits = intval($subscription['total_credits']);
            // $accessCreditsAvailable = intval($subscription['credits_available']);
            $accessCreditsUsed = $accessCredits - $accessCreditsAvailable;

            // Prevent division by zero
            if ($accessCredits > 0) {
                $perceCtageUsed = ($accessCreditsUsed / $accessCredits) * 100;
            }

            // Calculate expiry date based on plan
            // $created = new DateTime($subscription['updated_at']);

            // if (strtoupper($subscription['sub_plan']) == '1M') {
            //     $created->add(new DateInterval('P30D')); // Add 30 days
            // } elseif (strtoupper($subscription['sub_plan']) == '6M') {
            //     $created->add(new DateInterval('P180D')); // Add 180 days
            // } else {
            //     // Default to 30 days if plan is not recognized
            //     $created->add(new DateInterval('P30D'));
            // }

            // $expiryDate = $created->format('M d, Y'); // Returns format like "Mar 15, 2025"
        }
        $stmt->close();
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<style>
    .credits-popup {
        position: absolute;
        background: white;
        box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        padding: 15px;
        width: 323px;
        display: none;
        /* Hidden by default */
        z-index: 1000;
    }

    /* Close Button */
    .credits-popup .close-btn {
        position: absolute;
        top: 8px;
        right: 12px;
        cursor: pointer;
        font-size: 26px;
    }

    /* Progress Bar */
    .progress-bar {
        width: 100%;
        background: #e0e0e0;
        height: 6px;
        border-radius: 3px;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: #4CAF50;
        border-radius: 3px;
    }

    /* Button */
    .buy-credits-btn {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 8px 12px;
        width: 100%;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
    }

    .available-credit-title {
        color: #175DA8;
        font-family: Poppins;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
    }

    .bag-icon {
        border-radius: 8.662px;
        background: #FFF;
        box-shadow: 0px 0.722px 5.775px 0px rgba(0, 0, 0, 0.24);
    }

    .progress-bar {
        background: #DEFFDB !important;
    }

    .credits-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        color: #000;
        font-family: Poppins;
        font-size: 12px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .close-btn {
        font-size: 24px;
    }

    .credits-note {
        color: #747474;
        font-family: Poppins;
        font-size: 12px;
        font-style: italic;
        font-weight: 500;
        line-height: normal;
        letter-spacing: -0.12px;
        margin-bottom: 0;
    }

    .progress-bar {
        height: 8px;
    }

    .horizontal-line {
        border: 1px solid #747474;
        font-family: Poppins;
        font-size: 12px;
        font-style: italic;
        font-weight: 500;
        line-height: normal;
        letter-spacing: -0.24px;
    }
</style>

<body>
    <div id="credits-dropdown" class="credits-popup">
        <div class="credits-content">
            <span class="close-btn"
                onclick="document.getElementById('credits-dropdown').style.display='none'">&times;</span>
            <h4 class="available-credit-title mb-3">Available Credits</h4>
            <p><img class="p-1 bag-icon me-2"
                    src="image/bag.svg"><strong>&nbsp;&nbsp;<?php echo $accessCreditsAvailable; ?></strong> CV Access Credits
            </p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $percentageUsed; ?>%;"></div>
            </div>
            <p class="credits-info">
                <span class="used-credit"><?php echo $accessCreditsUsed; ?> Used</span>
                <span class="remaining-credit"><?php echo $accessCreditsAvailable; ?> Left</span>
            </p>

            <p class="credits-note mt-3">Note : <?php echo $accessCreditsAvailable; ?> CV credits will expire on
                <?php echo $expiryDate; ?></p>
            <hr class="horizontal-line">
            <p><img class="p-1 bag-icon me-2"
                    src="image/post-job.svg"><strong>&nbsp;&nbsp;<?php echo $jobPostCreditsAvailable; ?></strong> Job Post
                Credits</p>
            <p class="credits-note mt-3" style="    margin-bottom: 15px;">Note : <?php echo $jobPostCreditsAvailable; ?> job post credits expire on
                <?php echo $expiryDate; ?>.</p>
            <button class="buy-credits-btn" onclick="window.location.href='../../subscription/plans.php'">Buy Credits</button>

        </div>
    </div>


</body>

</html>