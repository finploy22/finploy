<?php
// Start session and check login
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// include '../redirect_check.php';

include '../db/connection.php';


// Check if session mobile exists
if (isset($_SESSION['mobile'])) {
  $employer_mobile = $_SESSION['mobile'];


  // Check if user is already registered in employer_add_details table
  $checkSql = "SELECT * FROM employer_add_details WHERE employer_mobile_number = ?";
  $checkStmt = $conn->prepare($checkSql);
  $checkStmt->bind_param("s", $employer_mobile);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  // If user exists, redirect to employer.php
  if ($checkResult->num_rows > 0) {
    $checkStmt->close();
    $conn->close();
    header("Location: employer.php");
    exit;
  }
  $checkStmt->close();
} else {
  // If mobile session is not set, redirect to login page or handle accordingly
  // echo "Session variable 'mobile' is not set!";
  // Uncomment the following line if you want to redirect to login
  // header("Location: login.php");
  // exit;
}

// Continue with the rest of your page code
// Query to check if employer exists in associate table (as in your original code)
$name = "";
$mobile = "";

if (isset($_SESSION['mobile'])) {
  $employer_mobile = $_SESSION['mobile'];

  // Query to check if employer is already registered in associate table
  $sql = "SELECT username, mobile_number FROM employers WHERE mobile_number = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $employer_mobile);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['username'];
    $mobile = $row['mobile_number'];
  }

  $stmt->close();
}
// Don't close the connection here if you need it for the POST request
include '../header.php';
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Finploy Form</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<style>
  body {
    font-family: 'Poppins';
  }

  .form-container-employer {
    max-width: 460px !important;
    background: white !important;
    padding: 20px !important;
    border-radius: 8px !important;  /* sakthi*/
    background: #FFF !important;
    box-shadow: 0px 0.933px 7.467px 0px rgba(0, 0, 0, 0.24) !important;
    margin: 10px 10px !important;
    width: 100% !important;
    border: none !important;
  }

  .btn-continue {
    padding: 14px 134.5px !important;
    background-color: #28a745 !important;
    color: white !important;
    width: 100% !important;
  }

  .btn-continue:hover {
    background-color: #218838 !important !important;
  }

  .index-body {
    background-image: url('image/bg-image.png') !important;
    /* Replace with your image path */
    background-size: cover !important;
    /* Adjusts the image to cover the entire container */
    background-position: 0% 100% !important;
    background-repeat: no-repeat !important;
    /* Prevents repetition */

  }

  .text-header {

    color: var(--Deeesha-Blue, #175DA8) !important;
    /*font-family: 'Poppins' !important;*/
    font-size: 60px !important;
    font-style: normal !important;
    font-weight: 600 !important;
    line-height: 70.461px !important;
    text-align: left !important;
    padding: 20px !important;

  }

  .para-employer {
    color: #232323 !important;
    text-align: center !important;
    font-family: 'Poppins' !important;
    font-size: 22px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: 48px !important;
    text-align: left !important;
    padding: 0 20px !important;
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

  .form-label {
    color: #4EA647 !important;
    font-family: Poppins !important;
    font-size: 16px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: normal !important;

  }

  .form-control::placeholder {
    color: #888 !important;
    font-family: Poppins !important;
    font-size: 13px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: normal !important;
  }

  /* .btn-continue {
    padding: 14px 134.5px !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    border-radius: 11.2px !important;
    background: #4EA647 !important;
    border: none !important;
    color: #FFF !important;
font-family: Poppins !important;
font-size: 18.667px !important;
font-style: normal !important;
font-weight: 600 !important;
line-height: normal !important;
 margin-bottom: 70px !important;
} */
  .additional-form {
    padding: 0px 18px !important;
  }

  .companey-drop {
    color: #888 !important;
    font-family: Poppins !important;
    font-size: 15px !important;
    font-style: normal !important;
    font-weight: 400 !important;
    line-height: normal !important;
  }

  .form-check {
    color: #4EA647 !important;
    leading-trim: both !important;
    text-edge: cap !important;
    font-family: Poppins !important;
    font-size: 16px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: normal !important;
  }

  .form-control {
    border-radius: 11.2px;
    border: 0.373px solid #C6C6C6;
    background: #FFF;
    box-shadow: 0px 0px 7.467px 0px rgba(99, 99, 99, 0.20);
  }

  .success-container {
    max-width: 460px;
    background: white;
    padding: 40px 0px;
    border-radius: 15px;
    box-shadow: 0px 1px 7px rgba(0, 0, 0, 0.2);
    text-align: center;
    margin-top: 60px;
    margin-bottom: 60px;
  }

  .success-container h3 {
    color: #333;
    font-size: 20px;
    font-weight: bold;
    margin: 10px 0;
  }

  .succ-con-btn {
    border-radius: 11.2px !important;
    background: #4EA647 !important;
    color: white !important;
    border: none !important;
    padding: 7px 80px !important;
    font-size: 16px !important;
    cursor: pointer !important;
    transition: 0.3s !important;
  }

  .btn-success:hover {
    background-color: #1E8C50;
  }

  .terms {

    color: #888;
    font-family: Poppins;
    font-size: 13px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    letter-spacing: 0.13px;
    margin-top: 100px;
    padding: 0px 40px;

  }

  .terms a {
    color: var(--Deeesha-Green, #4EA647);
    font-family: Poppins;
    font-size: 13px;
    font-style: normal;
    font-weight: 500;
    line-height: normal;
    letter-spacing: 0.13px;
    text-decoration: none;
  }

  .succ-msg {
    color: var(--Deeesha-Green, #4EA647);
    text-align: center;
    font-family: Poppins;
    font-size: 20px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    font-size: 16px;
    margin-bottom: 20px;
  }

  .cong-tex {
    color: #E59900;
    font-family: Poppins;
    font-size: 24px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    font-family: Poppins;
  }

  .main-div {
    margin-top: 80px;
  }

  .main-div img {
    display: block;
    margin: 0 auto 20px auto;
  }

    /* sakthi */
    .res-file-infos{
    display:none !important; 
  }

  /* sakthi conatiner changes added classes */
  @media (min-width:768px) {
    .def-bs-container-w{
      max-width:720px !important;
    }
  }
  @media (min-width:992px) {
    .def-bs-container-w{
      max-width:960px !important;
    }
  }

  @media (min-width:1200px) {
    .def-bs-container-w{
      max-width:1140px !important;
    }
    .left-block-container{
      position: sticky;
      margin-bottom:  20px;
      top: 100px;
    }
  }

  @media (min-width:1400px) {
    .def-bs-container-w{
      max-width:1340px !important;
    }
    .left-block-container{
      position: sticky;
      margin-bottom: 190px;
      top: 100px;
    }
  }

  @media (max-width:1200px) and (min-width:1000px){
    .form-container-employer{
      padding: 12px !important;
    }
    .custom-file-upload span{
      margin-left:8px !important;
    }
    .file-info-chosen-btn{
      font-size:11px !important;
    }
    .file-info{
      font-size:11px !important;
    }
  }
  @media (max-width:992px) and (min-width:768px){
    .custom-file-upload .file-info-chosen-btn,
    .custom-file-upload .file-info{
      display:none;
    }
    .res-file-infos{
     display: flex !important;
     flex-direction:column;
    }
    .res-file-infos .file-info-chosen-btn,
    .res-file-infos .file-info{
      display:inline !important;
      font-size: 10px !important;
      margin: 0px !important;
    }
    .custom-file-upload{
      padding:8px !important;
    }
    .custom-file-upload span{
      margin-left:4px !important; 
    }
    .form-detials{
      align-items:flex-start !important;
    }
    

  }
  @media (max-width:768px){
    .left-poster-label{
      display:none !important;
    }
  }

  @media (max-width:992px){
    button.btn-continue{
      padding:10px 113px !important;
    }
    .left-block-container{
      padding-top:3em;
    }
    
  }
    /* changes sakthi ends here */
  @media (max-width: 576px) {

    .form-container-employer {
      border-radius: 0px !important;
      margin: 0px auto !important;
    }

    .success-container {

      border-radius: 0px !important;

      margin-top: 0px !important;
      margin-bottom: 0px !important;
    }

    .custom-file-upload span {
      margin-left: 8px;
    }

    .custom-file-upload {
      font-size: 8px !important;
      padding: 1px !important;
    }

    .file-info {
      font-size: 9px !important;


    }

    .file-info-chosen-btn {
      color: #6c757d;
      font-size: 9px !important;
    }

    .choose_file {
      font-size: 9px !important;
    }
  }

  .custom-file-upload {
    display: flex;
    align-items: center;
    border: 2px dotted #007bff;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    width: 100%;

  }

  .custom-file-upload input {
    display: none;
  }

  .custom-file-upload span {
    color: #007bff;
    margin-left: 10px;
    cursor: pointer;
    font-weight: bold;
  }

  .file-info {
    font-size: 12px;
    margin-left: -2px;
    color: #6c757d;
  }

  .note {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
  }

  .btn-primary.choose_file {
    font-size: 12px;
  }

  .file-info-chosen-btn {
    color: #6c757d !important;
    font-size: 12px;
  }

  label.error {
    color: red;
    font-size: 0.875em;
    margin-top: 0.25rem;
    display: block;
  }

  input.error,
  select.error,
  textarea.error {
    border-color: red;
  }

  .form-detials {
    display: flex;
    align-items: center;
    align-content: stretch;
    flex-direction: row-reverse;
    padding: 20px 0px;
    /* position: relative;
          left: 130px; */
  }

  .left-poster-label {
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    justify-content: center;
    align-content: center;
    align-items: center;
  }

  .btn-continue {
    padding: 11px 140px !important;/* sakthi*/
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    border-radius: 6px !important;/* sakthi*/
    background: #4EA647 !important;
    border: none !important;
    color: #FFF !important;
    font-family: Poppins !important;
    font-size: 16px !important;/* sakthi*/
    font-style: normal !important;
    font-weight: 600 !important;
    line-height: normal !important;
    margin-top: 30px !important;/* sakthi*/
    margin-bottom: 10px !important;/* sakthi*/
    height: 48px !important;/* sakthi*/
  }

  .form-control {
    border-radius: 6px;
    border: 0.373px solid #C6C6C6;
    background: #FFF;
    box-shadow: 0px 0px 7.467px 0px rgba(99, 99, 99, 0.20);
    height: 48px;
  }

  .choose_file {
    background: #175DA8;
  }

  .custom-file-upload {
    display: flex;
    align-items: center;
    border: 2px dotted #007bff;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    width: 100%;
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

  .form-control::placeholder {
    color: #888888 !important;
    font-family: Poppins !important;
    font-size: 13px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: normal !important;
  }

  .para-employer {
    color: #232323 !important;
    text-align: center !important;
    font-family: 'Poppins' !important;
    font-size: 22px !important;
    font-style: normal !important;
    font-weight: 600 !important;
    line-height: 48px !important;
    text-align: left !important;
    padding: 0 20px !important;
  }



  @media (max-width: 760px) {
    .form-detials {
      display: block;
      width: 100%;
      margin: 0px;
    }

    .responsive-br {
      display: none;
    }

    .para-employer {
      line-height: 40px !important;
    }

    .text-header {
      line-height: 80.461px !important;
    }

    .form-container-employer {
      max-width: 100% !important;
    }
  }
</style>
</head>

<body>

  <div class="index-body">

    <div class="container def-bs-container-w index-container">
      <div class="row">
        <div class="col-12 col-md-7 d-md-block left-poster-label">
          <div class="container left-block-container text-center align-items-center pb-5">
            <p class="text-success fw-bold text-start ps-4">Finploy (Finance + Employ) - Built for Global Financial
              Talent</p>
            <h1 class="fw-bold text-header">Find the Perfect Candidate for Your Team - Start Here!</h1>
            <!-- <p class="para-employer">Access 100k+ Qualified Professionals - Hire <span
                class="responsive-br"><br></span>Top Talent Today!</p> -->
                <p class="para-employer">Access 100k+ Qualified Professionals - Hire Top Talent Today!
          </div>

        </div>

        <div class="col-12 col-md-5 form-detials">
          <div class="form-container-employer">
            <div class="text-center mb-3">
              <img src="image/logo.png" alt="Finploy Logo" style="width: 140px; height: 48.768px; margin-left: -22px;">

            </div>
            <h5 class="text-center text-formheader">Enter Additional Details</h5>
            <form id="employerForm" action="employer_add_details.php" method="POST" enctype="multipart/form-data"
              class="additional-form">

              <div class="mb-4 mt-4">
                <label class="form-label">Name:</label>
                <input type="text" class="form-control" name="name" placeholder="Enter your name"
                  value="<?php echo htmlspecialchars($name); ?>">
              </div>
              <div class="mb-4">
                <label class="form-label">Mobile No:</label>
                <input type="text" class="form-control" name="mobile" placeholder="Enter 10 Digit Number" maxlength="10"
                  value="<?php echo htmlspecialchars($mobile); ?>">
              </div>
              <div class="mb-4">
                <label class="form-label">Gender:</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" id="male" value="male" checked>
                  <label class="form-check-label" for="male">Male</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                  <label class="form-check-label" for="female">Female</label>
                </div>
              </div>
              <div class="mb-4">
                <label class="form-label">Company Name:</label>
                <input type="text" name="company_name" class="form-control" placeholder="Enter your Company Name">
              </div>
              <div class="mb-4">
                <label class="form-label">Official Email verification:</label>
                <input type="email" name="document_name" class="form-control" placeholder="Enter your Email">
              </div>
              <div class="mb-4">
                <label class="custom-file-upload">
                  <input type="file" id="uploadFile" name="upload_file" accept=".pdf,.docx">
                  <button type="button" class="btn btn-primary choose_file">Choose File</button>
                    <span class="file-info-chosen-btn">-No File Chosen / </span>
                    <span> <a class="file-info" href="#" style="color: #175DA8; text-decoration: none;">Upload .pdf or
                        .docx</a></span>
                    <!-- sakthi btn changes res -->
                    <div class="res-file-infos">
                      <span class="file-info-chosen-btn">-No File Chosen / </span>
                      <span> <a class="file-info" href="#" style="color: #175DA8; text-decoration: none;">Upload .pdf or
                          .docx</a></span>
                    </div>
                </label>
                <div class="note">Note: Max file size - 5 MB</div>
              </div>
              <button type="submit" class=" btn-continue">Continue</button>
            </form>

          </div>


        </div>

      </div>

    </div>
  </div>
  <script>


    //   When the custom button is clicked, trigger a click on the hidden file input.
    document.querySelector('.choose_file').addEventListener('click', function () {
      document.getElementById('uploadFile').click();
    });

    document.getElementById('uploadFile').addEventListener('change', function () {
      const file = this.files[0];
      const maxSize = 5 * 1024 * 1024; // 5 MB

      const chosenBtn = document.querySelector('.file-info-chosen-btn');
      const fileInfo = document.querySelector('.file-info');

      if (file) {
        if (file.size > maxSize) {
          alert("File is too large. Maximum size allowed is 5 MB.");
          this.value = ""; // Clear the input
          chosenBtn.style.display = 'inline'; // Show "-No File Chosen / "
          fileInfo.textContent = 'Upload .pdf or .docx'; // Reset text
          return;
        }

        chosenBtn.style.display = 'none'; // Hide "-No File Chosen / "

        let fileName = file.name;
        const maxLength = 15;

        if (fileName.length > maxLength) {
          const extensionIndex = fileName.lastIndexOf('.');
          const extension = extensionIndex !== -1 ? fileName.slice(extensionIndex) : '';
          const baseName = fileName.slice(0, maxLength);
          fileName = baseName + '...' + extension;
        }

        fileInfo.textContent = fileName;
      } else {
        chosenBtn.style.display = 'inline'; // Show "-No File Chosen / "
        fileInfo.textContent = 'Upload .pdf or .docx';
      }
    });


    $(document).ready(function () {
      //   Custom file size validation
      //   $.validator.addMethod("filesize", function (value, element, param) {
      //     return this.optional(element) || (element.files[0].size <= param);
      //   }, "File size must be less than 5 MB.");

      //   Apply validation to form
      $("#employerForm").validate({
        rules: {
          name: {
            required: true,
            minlength: 3
          },
          mobile: {
            required: true,
            digits: true,
            minlength: 10,
            maxlength: 10
          },
          gender: {
            required: true
          },
          company_name: {
            required: true
          },
          document_name: {
            required: true
          },
        },
        messages: {
          name: "Please enter your full name",
          mobile: {
            required: "Please enter your mobile number",
            digits: "Only digits allowed",
            minlength: "Mobile number must be 10 digits",
            maxlength: "Mobile number must be 10 digits"
          },
          gender: "Please select your gender",
          company_name: "Please enter your company name",
          document_name: "Please enter your email",
        },
        errorElement: "label",
        errorPlacement: function (error, element) {
          error.insertAfter(element);
        },

        // Only if the form is valid, perform AJAX
        submitHandler: function (form) {
          var formData = new FormData(form);

          $.ajax({
            type: "POST",
            url: "employer_add_details.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
              if (response.trim() === "success") {
                $(".form-container-employer").hide();
                var successHtml = `
            <div class="success-container">
                <div>
                <div class="main-div">
                    <img src="image/logo.png" alt="Finploy Logo">
                    <h3 class="cong-tex">✨ Congratulations ✨</h3>
                    <img src="../images/emojione_party-popper.svg" alt="Congratulations Illustration" width="140" height="139">
                </div>
                <p class="succ-msg">Your Profile is Successfully Created</p>
                <button onclick="window.location.href='employer.php'" class="btn-success succ-con-btn">Continue</button>
                <p class="terms">
                    By Continuing, you agree to the Finploy’s 
                    <a href="#">Terms of Service</a> and 
                    <a href="#">Privacy Policy</a>
                </p>
                </div>
            </div>
            `;

                $(".col-12.col-md-5").append(successHtml);
              } else {
                alert("Error: " + response);
              }
            }
          });
        }
      });
    });

    $(document).ready(function () {
      function adjustContainer() {
        if ($(window).width() < 576) {
          // Remove the 'container' class from the targeted element on mobile
          $('.index-container').removeClass('container');
        } else {
          // Re-add the 'container' class when the screen is wider
          $('.index-container').addClass('container');
        }
      }

      // Run the check on page load
      adjustContainer();

      // Run the check on window resize
      $(window).resize(adjustContainer);
    });


  </script>
  <?php include '../footer.php'; ?>
</body>

</html>