<?php
include 'db/connection.php';
include 'header.php'; 
error_reporting(E_ALL);
ini_set('display_errors', 1);
// API URL to fetch video list
$apiUrl = "https://deeesha.co.in/list_videos.php";

// Use cURL to get JSON response
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Convert JSON response to PHP array
$videos = json_decode($response, true);
$testimonialCount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Testimonials</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background: linear-gradient(180deg, #EEF5FA 0%, #D9E8F6 89.18%, #DEEBF7 100%); }
        .testimonial-card { 
            background: white; 
            border-radius: 10px; 
            padding: 15px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            text-align: center; 
            position: relative; 
            overflow: hidden; 
            cursor: pointer;
        }
        .testimonial-card video { 
            width: 100%; 
            border-radius: 10px; 
            max-height: 200px; 
            pointer-events: none;
            object-fit: cover;
        }
        .testimonial-description { border-radius: 8px; background: #FFF; }
        .testimonial-description h5 { color: #175DA8; font-size: 14px; font-weight: 600; margin: 0; }
        .testimonial-description p { margin: 0; }
        .company-logo { width: 45px; height: 30px; }
        .star-rating { color: #FFD700; font-size: 16px; }
        .feedback{
            color: #747474;
            font-family: Poppins;
            font-size: 11px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }
        .feedback-title {
            color: #175DA8;
            font-family: Poppins;
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }
        .close-video{
            color: #fff;
            font-size: 40px;
            font-weight: 700;
        }
         @media (min-width: 769px) {
            .finploy-contact {
                display: none !important;
            }
            
        }
    </style>
</head>
<body>
<?php //include 'header.php'; ?>

<div class="container">
    <h2 class="feedback-title mb-4 mt-3">Our User Feedback</h2>
    <div class="row">
        <?php if (!empty($videos)) : ?>
        
            <?php foreach ($videos as $video) : 
                $videoFilename = pathinfo($video, PATHINFO_FILENAME);
                
                // Extract mobile number using regex (Assuming it's a 10-digit number)
                preg_match('/\b\d{10}\b/', $videoFilename, $matches);
                $mobileNumber = $matches[0] ?? '';
                
                // Fetch details from database
                $candidateName = "Unknown";
                $companyName = "Company Name";
                $testimonialData = "No testimonial available.";
                $location = "Unknown Location";
                
                if (!empty($mobileNumber)) {
                    $stmt = $conn->prepare("SELECT candidatename, company, candidatelocation, thumbnail, feedback FROM testimonials WHERE candidatemobile = ?");
                    $stmt->bind_param("s", $mobileNumber);
                    $stmt->execute();
                    $result = $stmt->get_result();
                     
                    if ($row = $result->fetch_assoc()) {
                        $candidateName = $row['candidatename'];
                        $companyName = $row['company'];
                        $location = $row['candidatelocation'];
                        $testimonialData = $row['feedback'];
                        $logo = $row['thumbnail'];
                         $testimonialCount++;
                    }
                    $stmt->close();
                }
            ?>
                <div class="col-md-3 mb-4">
                    <div class="text-end mb-3">
    <strong>Total Testimonials: <?= $testimonialCount ?></strong>
</div>
                    <div class="testimonial-card" data-bs-toggle="modal" data-bs-target="#videoModal" data-video="<?= htmlspecialchars($video) ?>">
                        <span class="mb-1"><img class="company-logo mb-1" src="assets/<?= htmlspecialchars($companyName) ?>.png" /></span>
                        <video poster="https://finploy.co.uk/<?= htmlspecialchars($logo) ?>">
                            <source src="<?= htmlspecialchars($video) ?>" type="video/mp4">
                        </video>
                        <div class="testimonial-description">
                            <h5><?= htmlspecialchars($candidateName) ?></h5>
                            <p class="feedback"><span class="star-rating">★★★★★</span> | <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                              <path d="M10.2682 7.745C10.5953 7.131 10.7658 6.44571 10.7644 5.75C10.7644 3.40275 8.86166 1.5 6.51441 1.5C4.16716 1.5 2.26441 3.40275 2.26441 5.75C2.26265 6.75258 2.61705 7.72319 3.26441 8.48875L3.26941 8.495L3.27391 8.5H3.26441L5.78641 11.1775C5.8799 11.2767 5.9927 11.3558 6.11787 11.4099C6.24304 11.4639 6.37794 11.4918 6.51429 11.4918C6.65063 11.4918 6.78553 11.4639 6.9107 11.4099C7.03587 11.3558 7.14867 11.2767 7.24216 11.1775L9.76441 8.5H9.75491L9.75891 8.49525L9.75941 8.49475C9.77741 8.47325 9.79533 8.45158 9.81316 8.42975C9.98661 8.21666 10.139 7.9875 10.2682 7.745ZM6.51566 7.37475C6.11784 7.37475 5.73631 7.21671 5.455 6.93541C5.1737 6.65411 5.01566 6.27257 5.01566 5.87475C5.01566 5.47693 5.1737 5.09539 5.455 4.81409C5.73631 4.53279 6.11784 4.37475 6.51566 4.37475C6.91349 4.37475 7.29502 4.53279 7.57632 4.81409C7.85763 5.09539 8.01566 5.47693 8.01566 5.87475C8.01566 6.27257 7.85763 6.65411 7.57632 6.93541C7.29502 7.21671 6.91349 7.37475 6.51566 7.37475Z" fill="#747474"/>
                            </svg><?= htmlspecialchars($location) ?></p>
                            <p class="feedback">"<?= htmlspecialchars($testimonialData) ?>"</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-center">No videos found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Video Popup Modal -->
<!-- Video Popup Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true" style=" background: transparent;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center" style=" background: none; padding: 0; margin: 0;">
            <div class="modal-body text-center">
                <div class="text-end">
                    <span class="close-video" data-bs-dismiss="modal" style="cursor:pointer; font-size:24px;">&times;</span>
                </div>
                <video id="popupVideo" controls autoplay muted style="width: 100%; max-height: 80vh; border-radius: 10px;">
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const videoModal = document.getElementById('videoModal');
        const popupVideo = document.getElementById('popupVideo');

        videoModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const videoSrc = button.getAttribute('data-video');
            popupVideo.src = videoSrc;
        });

        videoModal.addEventListener('hidden.bs.modal', function () {
            popupVideo.pause();
            popupVideo.src = ""; 
        });

        // Prevent modal from closing when clicking on the video itself
        popupVideo.addEventListener('click', function (event) {
            event.stopPropagation(); 
        });
    });
</script>


<?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>
