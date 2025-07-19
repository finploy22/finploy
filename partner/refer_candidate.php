<?php
include 'partner_header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partner Refer Form</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /*body {*/
    /*    display: flex;*/
    /*    justify-content: center;*/
    /*    align-items: center;*/
    /*    height: 100vh;*/
    /*    background-color: #f8f9fa;*/
    /*    font-family: ;*/
    /*}*/
    .form-container {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      width: 400px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin: 2rem auto;
    }

    .form-step {
      display: none;
    }

    .form-step.active {
      display: block;
    }

    .login-logo {
      width: 140px;
      margin-bottom: 15px;
    }

    .form-control {
      border-radius: 6px;
      font-size: 14px;
      padding: 10px;
    }

    .btn-success {
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 6px;
    }

    .error-message {
      font-size: 13px;
      color: red;
      margin-top: 4px;
    }

    .congrats-logo {
      width: 80px;
      margin-bottom: 10px;
    }

    @media (max-width: 768px) {
      .form-container {
        width: 100%;
      }
    }

    .index-body {
      background-image: url('../employer_flow/image/bg-image.png') !important;
      /* Replace with your image path */
      background-size: cover !important;
      /* Adjusts the image to cover the entire container */
      background-position: 0% 100% !important;
      background-repeat: no-repeat !important;
      /* Prevents repetition */

    }

    .text-formheader {
      color: #4EA647 !important;
      text-align: center !important;
      font-family: Poppins !important;
      font-size: 20px !important;
      font-style: normal !important;
      font-weight: 700 !important;
      line-height: normal !important;
      text-transform: capitalize !important;
    }

    .text-header {
      color: var(--Deeesha-Blue, #175DA8) !important;
      font-family: 'Poppins' !important;
      font-size: 62px !important;
      font-style: normal !important;
      font-weight: 600 !important;
      line-height: 89.461px !important;
      text-align: left !important;
      padding: 20px !important;
    }

    .text-start {
      color: #4EA647;
      font-size: 22px;
      font-family: 'Poppins' !important;
    }
  </style>
</head>

<body>
  <div class="index-body">

    <div class="container index-container">
      <div class="row">
        <div class="col-12 col-md-7 d-md-block left-poster-label">
          <div class="container text-center align-items-center py-5">
            <p class="text-success fw-bold text-start ps-4">Finploy (Finance + Employ) - Built for Global Financial
              Talent</p>
            <h1 class="fw-bold text-header">Know someone perfect for the role? Refer now</h1>
            <p class="para-employer">Access 100k+ Qualified Professionals - Hire <span
                class="responsive-br"><br></span>Top Talent Today!</p>
          </div>

        </div>

        <div class="col-12 col-md-5 form-detials">
          <div class="form-container">
            <div class="text-center mb-3">
              <img src="../employer_flow/image/logo.png" alt="Finploy Logo"
                style="width: 140px; height: 48.768px; margin-left: -22px;">

            </div>
            <h5 class="text-center text-formheader">Refer Candidate Details</h5>
            <form id="referForm">
             

              <!-- Step 1: Mobile Number Input -->
              <div class="form-step active" id="step-1">
                <div class="mb-3">
                  <label for="referMobile" class="form-label text-success text-start">Referred Candidate Mobile
                    Number:</label>
                  <input type="tel" class="form-control" maxlength="10" id="referMobile"
                    placeholder="Enter 10 Digit Number" required>
                  <div id="mobileError" class="error-message"></div>
                </div>
                <div class="mb-3">
                  <label for="referName" class="form-label text-success text-start">Referred Candidate Name:</label>
                  <input type="text" class="form-control" id="referName" placeholder="Enter Candidate Name" required>
                  <div id="nameError" class="error-message"></div>
                </div>
                <div class="mb-3">
                  <label for="search_location" class="form-label text-success text-start">Referred Candidate
                    Location:</label>
                  <input type="text" class="form-control" id="search_location" placeholder="Enter Candidate Location"
                    required>
                     <div id="select-div" style="display: none;"></div>
                  <div id="locationError" class="error-message"></div>
                </div>
                <button type="button" class="btn btn-success w-100" id="nextStep">Submit</button>
              </div>

              <!-- Step 2: Congratulations -->
              <div class="form-step" id="step-2">
                <div class="m-4 text-center">
                  <img class="congrats-logo mb-3" src="../assets/party.svg" alt="Congratulations">
                  <h4 class="step-title mb-4">✨ Congratulations ✨</h4>
                  <p class="text-success mb-4">You have successfully referred a candidate.</p>
                  <a class="text-decoration-none" href="candidate_listing.php"><button type="button"
                      class="btn btn-success w-100">Continue</button></a>
                </div>
              </div>
            </form>

          </div>


        </div>

      </div>

    </div>
  </div>



 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const jobId = urlParams.get("jobid");

    $('#nextStep').click(function () {
      const mobile = $('#referMobile').val().trim();
      const name = $('#referName').val().trim();
      const location = $('#search_location').val().trim();

      let isValid = true;

      // Validate Mobile
      if (mobile.length !== 10 || isNaN(mobile)) {
        $('#mobileError').text("Please enter a valid mobile number.");
        isValid = false;
      } else {
        $('#mobileError').text("");
      }

      // Validate Name
      if (name === "") {
        $('#nameError').text("Please enter candidate name.");
        isValid = false;
      } else {
        $('#nameError').text("");
      }

      // Validate Location
      if (location === "") {
        $('#locationError').text("Please enter candidate location.");
        isValid = false;
      } else {
        $('#locationError').text("");
      }

      // Stop if any field is invalid
      if (!isValid) return;

      // Check for job ID
      if (!jobId) {
        alert("Job ID is missing from the URL.");
        return;
      }

      // Send data via AJAX
      $.ajax({
        type: 'POST',
        url: 'insert_referred_user.php',
        data: {
          mobile: mobile,
          name: name,
          location: location,
          jobid: jobId
        },
        success: function (response) {
          if (response.trim() === "success") {
            $('#step-1').removeClass('active');
            $('#step-2').addClass('active');
          } else {
            alert(response);
          }
        },
        error: function () {
          alert("An error occurred. Please try again.");
        }
      });
    });

    // Real-time validation (optional)
    $('#referMobile').on('input', function () {
      if ($(this).val().length === 10 && !isNaN($(this).val())) {
        $('#mobileError').text("");
      }
    });

    $('#referName').on('input', function () {
      if ($(this).val().trim() !== "") {
        $('#nameError').text("");
      }
    });

    $('#search_location').on('input', function () {
      if ($(this).val().trim() !== "") {
        $('#locationError').text("");
      }
    });
  });







// For location 
  
        // When input is clicked, fetch locations all location
        $(document).ready(function () {
            $(document).on("click", '#search_location', function () {
                const allLocation = $(this).val();
                $.ajax({
                    url: '../candidate/search_location.php',
                    type: 'POST',
                    data: { allLocation: allLocation },
                    success: function (response) {
                        if ($.trim(response)) {
                            $("#select-div").show().html(response);
                        } else {
                            $("#select-div").hide().html("");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", status, error);
                    }
                });
            });

            // Hide suggestion list when clicking outside
            $(document).on("click", function (e) {
                const $target = $(e.target);
                if (
                    !$target.closest('#search_location').length &&
                    !$target.closest('#select-div').length
                ) {
                    $("#select-div").hide().html("");
                }
            });
        });

        // fetch selctecd location
        $(document).on("input", '#search_location', function () {
            const query = $(this).val();
            if (query.length >= 1) {
                $.ajax({
                    url: '../candidate/search_location.php',
                    type: 'POST',
                    data: { query: query },
                    success: function (response) {
                        if ($.trim(response)) {
                            $("#select-div").show().html(response);
                        } else {
                            $("#select-div").hide().html("");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error:", status, error);
                    }
                });
            } else {
                $("#select-div").hide().html("");
            }
        });


        let locationSelected = false;
        
                // Set location input
        $(document).on("click", '#list_location', function () {
            const location = $(this).text();
            const location_id = $(this).data('id');
            $('#search_location').val(location);
            $('#search_location').data('location-id', location_id);
            $('#location_id').val(location_id);
            locationSelected = true; 
            $('#select-div').hide();
            $('#search_location').css({
                'border-radius': '8px',
                'border-bottom': '1px solid #ccc'
            });
        
            const validator = $("#multiStepForm").validate();
            validator.element("#search_location");
        
            // Optional: Backend tracking
            $.ajax({
                url: '../candidate/search_location.php',
                type: 'POST',
                data: { location: location, location_id: location_id }
            });
        });
        $('#search_location').on('input', function () {
            $('#location_id').val('');
            locationSelected = false; 
        });
        $('#search_location').on('blur', function () {
                if (!locationSelected) {
                    $('#search_location').val('');
                    $('#location_id').val('');        }
        });

</script>




  <?php include '../footer.php'; ?>
</body>

</html>