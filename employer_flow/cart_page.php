<?php
// Start session and check login
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['name']) || !isset($_SESSION['mobile'])) {
  header("Location: ../index.php");
  exit();
}
$employer_name = $_SESSION['name'] ?? '';
$employer_mobile = $_SESSION['mobile'] ?? '';
$employer_username = $_SESSION['employer_username'] ?? 'Not There';


include 'posting_header.php';
include '../db/connection.php';

if (isset($_SESSION['planDetails'])) {
  header("Location: ../subscription/plans.php");
  exit();
}

$total = 0;
$candidates = [];

function generateRandomColor($string) {
    $hash = md5($string);
    $r = hexdec(substr($hash, 0, 2));
    $g = hexdec(substr($hash, 2, 2));
    $b = hexdec(substr($hash, 4, 2));
    
    $r = (int)(($r + 255) / 2);
    $g = (int)(($g + 255) / 2);
    $b = (int)(($b + 255) / 2);
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

// Get cart items from database
 $userId = $_SESSION['employer_id'];
$query = "SELECT c.id, c.username, c.mobile_number, uc.price 
          FROM user_cart uc 
          JOIN candidate_details c ON uc.candidate_id = c.id 
          WHERE uc.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $name = $row['username'];
    $maskedName = substr($name, 0, 1) . (strlen($name) > 8 ? str_repeat('x', 8) : str_repeat('x', strlen($name) - 1));

    $mobile = $row['mobile_number'];
    $maskedMobile = substr($mobile, 0, 2) . 'xxxxxx' . substr($mobile, -2);
    
    $candidate = [
        'id' => $row['id'],
        'username' => $maskedName,
        'mobile_number' => $maskedMobile,
        'price' => $row['price'],
        'background_color' => generateRandomColor($name)
    ];
    
    $candidates[] = $candidate;
    $total += $row['price'];
}

?>


<body>


    <div class="container">
    <div class="page-header">
      <h3 class="cart-count">My Cart</h3>
      <!-- <a href="employer.php" class="btn btn-outline-primary">Back to Search</a> -->
    </div>
    <?php if (empty($candidates)): ?>
      <div class="empty-cart text-center p-4 bg-white rounded">
        <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #dee2e6;"></i>
        <h4>Your cart is empty</h4>
        <p>Go back to the dashboard to add candidates</p>
        <a href="employer.php" class="btn btn-primary">Return to Dashboard</a>
      </div>
    <?php else: ?>
      <div class="row">
        <!-- Candidate List -->
        <div class="col-12 col-md-8">
          <?php foreach ($candidates as $candidate): ?>
            <div class="candidate-card">
              <div class="top-remove">
                  <span onclick="removeFromCart(<?= $candidate['id'] ?>)">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="btn-remove" >
                         <path fill-rule="evenodd" clip-rule="evenodd" d="M12 22C7.286 22 4.929 22 3.464 20.535C2 19.072 2 16.714 2 12C2 7.286 2 4.929 3.464 3.464C4.93 2 7.286 2 12 2C16.714 2 19.071 2 20.535 3.464C22 4.93 22 7.286 22 12C22 16.714 22 19.071 20.535 20.535C19.072 22 16.714 22 12 22ZM8.97 8.97C9.11063 8.82955 9.30125 8.75066 9.5 8.75066C9.69875 8.75066 9.88937 8.82955 10.03 8.97L12 10.94L13.97 8.97C14.1122 8.83752 14.3002 8.7654 14.4945 8.76882C14.6888 8.77225 14.8742 8.85097 15.0116 8.98838C15.149 9.12579 15.2277 9.31118 15.2312 9.50548C15.2346 9.69978 15.1625 9.88783 15.03 10.03L13.06 12L15.03 13.97C15.1037 14.0387 15.1628 14.1215 15.2038 14.2135C15.2448 14.3055 15.2668 14.4048 15.2686 14.5055C15.2704 14.6062 15.2518 14.7062 15.2141 14.7996C15.1764 14.893 15.1203 14.9778 15.049 15.049C14.9778 15.1203 14.893 15.1764 14.7996 15.2141C14.7062 15.2518 14.6062 15.2704 14.5055 15.2686C14.4048 15.2668 14.3055 15.2448 14.2135 15.2038C14.1215 15.1628 14.0387 15.1037 13.97 15.03L12 13.06L10.03 15.03C9.96134 15.1037 9.87854 15.1628 9.78654 15.2038C9.69454 15.2448 9.59522 15.2668 9.49452 15.2686C9.39382 15.2704 9.29379 15.2518 9.2004 15.2141C9.10701 15.1764 9.02218 15.1203 8.95096 15.049C8.87974 14.9778 8.8236 14.893 8.78588 14.7996C8.74816 14.7062 8.72963 14.6062 8.73141 14.5055C8.73318 14.4048 8.75523 14.3055 8.79622 14.2135C8.83721 14.1215 8.89631 14.0387 8.97 13.97L10.94 12L8.97 10.03C8.82955 9.88937 8.75066 9.69875 8.75066 9.5C8.75066 9.30125 8.82955 9.11063 8.97 8.97Z" fill="#ED4C5C"/>
                      </svg>
                  </span>
              </div>
              <div class="row card-content">
                <!-- Candidate Header: full width on mobile, 4 columns on md+ -->
                <div class="col-12 col-md-3 candidate-header">
                  <div class="candidate-info-wrapper">
                    <div class="profile-avatar" style="background-color: <?= $candidate['background_color'] ?>">
                      <?= strtoupper(substr($candidate['username'], 0, 1)) ?>
                    </div>
                    <h5 class="candidate-name"><?= htmlspecialchars($candidate['username']) ?></h5>
                  </div>
                </div>
                <!-- Candidate Info: 6 columns on mobile, 4 columns on md+ -->
                <div class="col-6 col-md-3">
                 <div class="candidate-info">
                      <svg width="19" height="19" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M19.9983 10.999H21.9983C21.9983 5.869 18.1253 2 12.9883 2V4C17.0503 4 19.9983 6.943 19.9983 10.999Z" fill="#4EA647"/>
  <path d="M12.9983 8.00024C15.1013 8.00024 15.9983 8.89724 15.9983 11.0002H17.9983C17.9983 7.77524 16.2233 6.00024 12.9983 6.00024V8.00024ZM16.4203 13.4432C16.2283 13.2684 15.9757 13.1751 15.7161 13.1831C15.4565 13.1911 15.2102 13.2998 15.0293 13.4862L12.6363 15.9472C12.0603 15.8372 10.9023 15.4762 9.71033 14.2872C8.51833 13.0942 8.15733 11.9332 8.05033 11.3612L10.5093 8.96724C10.6957 8.78637 10.8045 8.54006 10.8125 8.28045C10.8205 8.02083 10.7272 7.76828 10.5523 7.57624L6.85733 3.51324C6.68237 3.3206 6.43921 3.20374 6.17948 3.1875C5.91976 3.17125 5.66393 3.2569 5.46633 3.42624L3.29633 5.28724C3.12344 5.46075 3.02025 5.69169 3.00633 5.93624C2.99133 6.18624 2.70533 12.1082 7.29733 16.7022C11.3033 20.7072 16.3213 21.0002 17.7033 21.0002C17.9053 21.0002 18.0293 20.9942 18.0623 20.9922C18.3067 20.9778 18.5372 20.8743 18.7103 20.7012L20.5703 18.5302C20.7398 18.3328 20.8256 18.077 20.8096 17.8173C20.7935 17.5576 20.6768 17.3143 20.4843 17.1392L16.4203 13.4432Z" fill="#4EA647"/>
</svg>
                  <?= htmlspecialchars($candidate['mobile_number']) ?>
                 </div>
                </div>
                <!-- View CV: 6 columns on mobile, 4 columns on md+ -->
                <div class="col-6 col-md-3 ">
                    <div class=" view-cv">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M17.1403 9H18.8546C19.082 9 19.3 9.09031 19.4607 9.25105C19.6215 9.4118 19.7118 9.62981 19.7118 9.85714V20.1429C19.7118 20.3702 19.6215 20.5882 19.4607 20.7489C19.3 20.9097 19.082 21 18.8546 21H5.14035C4.91302 21 4.695 20.9097 4.53425 20.7489C4.37351 20.5882 4.2832 20.3702 4.2832 20.1429V9.85714C4.2832 9.62981 4.37351 9.4118 4.53425 9.25105C4.695 9.09031 4.91302 9 5.14035 9H6.85463V8.14286C6.85463 6.77889 7.39647 5.47078 8.36094 4.50631C9.32541 3.54184 10.6335 3 11.9975 3C13.3615 3 14.6696 3.54184 15.634 4.50631C16.5985 5.47078 17.1403 6.77889 17.1403 8.14286V9ZM11.1403 15.6274V17.5714H12.8546V15.6274C13.1814 15.4387 13.4368 15.1475 13.5812 14.7988C13.7256 14.4502 13.751 14.0637 13.6533 13.6992C13.5556 13.3347 13.3404 13.0126 13.041 12.7828C12.7417 12.5531 12.3748 12.4286 11.9975 12.4286C11.6201 12.4286 11.2533 12.5531 10.9539 12.7828C10.6546 13.0126 10.4394 13.3347 10.3417 13.6992C10.244 14.0637 10.2694 14.4502 10.4138 14.7988C10.5582 15.1475 10.8136 15.4387 11.1403 15.6274ZM15.4261 9V8.14286C15.4261 7.23354 15.0648 6.36147 14.4219 5.71849C13.7789 5.07551 12.9068 4.71429 11.9975 4.71429C11.0882 4.71429 10.2161 5.07551 9.57312 5.71849C8.93014 6.36147 8.56892 7.23354 8.56892 8.14286V9H15.4261Z" fill="#4EA647"/>
</svg>
                  <span>view cv</span>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <!-- Summary Card -->
        <div class="col-12 col-md-4">
          <div class="summary-card">
            <h5 class="mb-4 Pricing">Pricing Details</h5>
            <div class="d-flex justify-content-between mb-3">
              <span>Per Candidates</span>
              <strong><svg width="15" height="16" viewBox="2 1 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M13.725 21.5L7 14.5V12.5H10.5C11.3833 12.5 12.146 12.2123 12.788 11.637C13.43 11.0617 13.8173 10.3493 13.95 9.5H6V7.5H13.65C13.3667 6.91667 12.9457 6.43733 12.387 6.062C11.8283 5.68667 11.1993 5.49933 10.5 5.5H6V3.5H18V5.5H14.75C14.9833 5.78333 15.1917 6.09167 15.375 6.425C15.5583 6.75833 15.7 7.11667 15.8 7.5H18V9.5H15.975C15.8417 10.9167 15.2583 12.1043 14.225 13.063C13.1917 14.0217 11.95 14.5007 10.5 14.5H9.775L16.5 21.5H13.725Z" fill="black"/>
</svg>25</strong>
            </div>
            <div class="d-flex justify-content-between ">
              <span>Total Amount(Include Tax) <span class="textx">x</span> <span class="text-x"><?= count($candidates) ?></span></span>
              <strong><svg width="15" height="16" viewBox="2 1 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M13.725 21.5L7 14.5V12.5H10.5C11.3833 12.5 12.146 12.2123 12.788 11.637C13.43 11.0617 13.8173 10.3493 13.95 9.5H6V7.5H13.65C13.3667 6.91667 12.9457 6.43733 12.387 6.062C11.8283 5.68667 11.1993 5.49933 10.5 5.5H6V3.5H18V5.5H14.75C14.9833 5.78333 15.1917 6.09167 15.375 6.425C15.5583 6.75833 15.7 7.11667 15.8 7.5H18V9.5H15.975C15.8417 10.9167 15.2583 12.1043 14.225 13.063C13.1917 14.0217 11.95 14.5007 10.5 14.5H9.775L16.5 21.5H13.725Z" fill="black"/>
</svg><?= number_format($total) ?></strong>
            </div>
            <hr class="blue-line">
            <div class="d-flex justify-content-between mb-3">
              <span class="blue-text">Total Amount to Pay </span>
              <strong class="blue-text"><svg class="rupee-icon" width="15" height="16" viewBox="2 1 26 28" xmlns="http://www.w3.org/2000/svg">
  <path d="M13.725 21.5L7 14.5V12.5H10.5C11.3833 12.5 12.146 12.2123 12.788 11.637C13.43 11.0617 13.8173 10.3493 13.95 9.5H6V7.5H13.65C13.3667 6.91667 12.9457 6.43733 12.387 6.062C11.8283 5.68667 11.1993 5.49933 10.5 5.5H6V3.5H18V5.5H14.75C14.9833 5.78333 15.1917 6.09167 15.375 6.425C15.5583 6.75833 15.7 7.11667 15.8 7.5H18V9.5H15.975C15.8417 10.9167 15.2583 12.1043 14.225 13.063C13.1917 14.0217 11.95 14.5007 10.5 14.5H9.775L16.5 21.5H13.725Z"/>
</svg>
<?= number_format($total) ?></strong>
            </div>
            <button class="btn-payment" onclick="proceedToPayment()">Proceed to Payment</button>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
      function removeFromCart(candidateId) {
    if (confirm("Are you sure you want to remove this item from the cart?")) {
        $.ajax({
            url: 'remove_from_cart.php',
            type: 'POST',
            data: { candidate_id: candidateId },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        // alert("Item removed from cart successfully!");
                        $('#cart-count, #cart-count-mobile').text(data.cartCount);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('Error removing item from cart');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
                alert('Error removing item from cart');
            }
        });
    }
}


// Function to verify payment with Razorpay
function verifyPayment(response) {
    console.log('Verifying payment:', response);
    $.ajax({
        url: 'verify_payment.php',
        type: 'POST',
        data: response,
        dataType: 'json',
        success: function(result) {
            console.log('Verification response:', result);
            try {
                if (result.success) {
                    window.location.href = 'accessed_candidates.php';
                } else {
                    alert('Payment verification failed: ' + (result.message || 'Unknown error'));
                }
            } catch (e) {
                console.error('Error processing result:', e);
                alert('Error processing payment response');
            }
        },
        error: function(xhr, status, error) {
            console.error('Payment verification error:', error);
            console.log('Response text:', xhr.responseText);
            
            // Try to parse the response text for any JSON that might be after PHP warnings
            try {
                const responseText = xhr.responseText;
                const jsonStart = responseText.indexOf('{');
                if (jsonStart !== -1) {
                    const jsonString = responseText.substring(jsonStart);
                    const result = JSON.parse(jsonString);
                    if (result.success) {
                        window.location.href = 'accessed_candidates.php';
                        return;
                    }
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
            
            alert('Error processing payment. Please contact support.');
        }
    });
}
var employer_mobile = <?php echo json_encode($_SESSION['mobile']); ?>;
  console.log(employer_mobile);

// Function to handle Razorpay payment failure
function proceedToPayment() {
    $.ajax({
        url: 'create_order.php',
        type: 'POST',
        data: { amount: <?= $total * 100 ?> },
        dataType: 'json',
        success: function(response) {
            console.log('Create order response:', response);
            
            if (response.success && response.order_id) {
                const options = {
                    key: 'rzp_test_rtxMNYHT7oPGHY',
                    // key: 'rzp_live_ceAIhcdU5YtpSc',                    
                    amount: response.amount,
                    currency: 'INR',
                    name: '<?php echo $employer_username; ?>',
                    description: 'Candidate Access Purchase',
                    order_id: response.order_id,
                    handler: function(paymentResponse) {
                        verifyPayment(paymentResponse);
                    },
                    theme: { color: '#175DA8' },
                    modal: {
                        ondismiss: function() {
                            console.log('Payment cancelled by user');
                        }
                    },
                    prefill: {
                        name: '', 
                        email: '', 
                        contact: employer_mobile 
                    }
                };
                
                try {
                    const rzp = new Razorpay(options);
                    
                    // Enhanced payment failure handling
                    rzp.on('payment.failed', function (response) {
                        console.log('Payment failed:', response);
                        
                        // Send detailed error information to server
                        $.ajax({
                            url: 'verify_payment.php',
                            type: 'POST',
                            data: { 
                                error: {
                                    payment_id: response.error.metadata?.payment_id || null,
                                    order_id: response.error.metadata?.order_id || null,
                                    code: response.error.code || 'UNKNOWN',
                                    description: response.error.description || 'No description',
                                    source: response.error.source || 'Unknown',
                                    step: response.error.step || 'Unknown',
                                    reason: response.error.reason || 'Unknown'
                                }
                            },
                            dataType: 'json',
                            success: function(result) {
                                console.log('Payment failure logged:', result);
                                
                                // Display user-friendly error message
                                let errorMessage = 'Payment failed. ';
                                switch(response.error.code) {
                                    case 'BAD_REQUEST_ERROR':
                                        errorMessage += 'There was an issue with the payment request.';
                                        break;
                                    case 'GATEWAY_ERROR':
                                        errorMessage += 'Payment gateway encountered an error.';
                                        break;
                                    case 'PAYMENT_VALIDATION_ERROR':
                                        errorMessage += 'Payment details could not be validated.';
                                        break;
                                    default:
                                        errorMessage += response.error.description || 'Please try again.';
                                }
                                
                                alert(errorMessage);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error logging payment failure:', error);
                                alert('Payment failed. Please contact support.');
                            }
                        });
                    });
                    
                    rzp.open();
                } catch (error) {
                    console.error('Razorpay initialization error:', error);
                    alert('Error initializing payment. Please try again.');
                }
            } else {
                alert('Error: ' + (response.message || 'Failed to create order'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            console.log('Response text:', xhr.responseText);
            alert('Error creating order. Please try again.');
        }
    });
}
function updateCartCount() {
    $.ajax({
        url: 'get_cart_count.php',
        type: 'GET',
        success: function(response) {
            try {
                const data = JSON.parse(response);
                $('#cart-count, #cart-count-mobile').text(data.cartCount);

            } catch (e) {
                console.error('Error parsing cart count:', e);
            }
        }
    });
}

// Call updateCartCount when the page loads
$(document).ready(function() {
    updateCartCount();
});
var offcanvasEl = document.getElementById('mobileOffcanvas');
var hambergBtn = document.querySelector('.hamberg');

offcanvasEl.addEventListener('show.bs.offcanvas', function () {
    hambergBtn.style.display = 'none';
});
offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
    hambergBtn.style.display = 'block';
});


document.addEventListener('DOMContentLoaded', function() {
  // Get the profile toggle button and dropdown
  const profileToggle = document.getElementById('profileToggle');
  const profileDropdown = document.getElementById('profileDropdown');
  
  // Add click event to toggle profile dropdown
  profileToggle.addEventListener('click', function(e) {
    e.preventDefault();
    profileDropdown.classList.toggle('show');
  });
  
  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
      profileDropdown.classList.remove('show');
    }
  });
});


    //         //////////////////////////////// Available Credits //////////////////////
    // document.getElementById("available-credits").addEventListener("click", function (event) {
    //   event.stopPropagation(); // Prevent closing immediately

    //   let dropdown = document.getElementById("credits-dropdown"); // Check if modal already exists

    //   if (!dropdown) {
    //     // Fetch the modal content from external file
    //     fetch("available_credits.php")
    //       .then(response => response.text())
    //       .then(html => {
    //         document.getElementById("credits-container").innerHTML = html; // Load the popup
    //         showDropdown();
    //       });
    //   } else {
    //     showDropdown();
    //   }
    // });

    // function showDropdown() {
    //   let dropdown = document.getElementById("credits-dropdown");

    //   // Positioning near button
    //   let button = document.getElementById("available-credits");
    //   let rect = button.getBoundingClientRect();
    //   dropdown.style.top = 82 + "px";
    //   dropdown.style.left = 550 + "px";
    //   dropdown.style.display = "block";

    //   // Close dropdown when clicking outside
    //   document.addEventListener("click", function closeDropdown(event) {
    //     if (!dropdown.contains(event.target) && event.target.id !== "available-credits") {
    //       dropdown.style.display = "none";
    //       document.removeEventListener("click", closeDropdown);
    //     }
    //   });
    // }


document.addEventListener('DOMContentLoaded', function () {
    const profileToggle = document.getElementById('dropdownMenuLink');
    const profileDropdown = document.getElementById('custom-dropdown');

    profileToggle.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default link behavior
        e.stopPropagation(); // Stop the event from bubbling up
        profileDropdown.classList.toggle('show'); // Toggle visibility
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('show');
        }
    });
});

    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>


 <?php include('../footer.php'); ?>
</body>

</html>