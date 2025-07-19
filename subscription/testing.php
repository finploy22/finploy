<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION['name'])) {

    $name = $_SESSION['name'];
    $mobile = $_SESSION['mobile'];
} else {

    // header("Location: ../index.php");
    // die();  
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans</title>
    <link href="../css/style.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
 
    <style>
        body {
            font-family: 'poppins';
            background-color: #f8f9fa;
        }
        
        .pricing-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 15px;
        }
        
        .pricing-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .pricing-header h1 {
            color: #175DA8;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .pricing-header p {
            color: #6c757d;
            font-size: 14px;
            cursor: pointer;
        }
        
        .card {
            border-radius: 12px;
            border: none !important;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 5px 0px, rgba(0, 0, 0, 0.1) 0px 0px 1px 0px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .pricing-card {
            padding: 20px;
            background-color: white;
        }
        
        .plan-title {
            font-weight: bold;
            color: #175DA8;
            margin-bottom: 5px;
            font-size: 18px;
        }
        
        .plan-subtitle {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .plan-feature {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #000;
            font-family: Poppins;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }
        
        .plan-feature i, .plan-feature img {
            margin-right: 10px;
            color: #212529;
            width: 20px;
            text-align: center;
        }
        
        .plan-price {
            font-size: 24px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        
        .original-price {
            text-decoration: line-through;
            font-family: Arial, sans-serif;
            color: #6c757d;
            font-weight: normal;
            font-size: 18px;
            margin-left: 5px;
        }
        
        .discount-badge {
            color: #9B0000;
            background: #FBECED;
            font-family: Poppins;
            padding: 3px 7px;
            border-radius: 5px;
            font-size: 12px;
            font-style: normal;
            font-weight: 600;
            line-height: 24.597px; /* 204.975% */
            letter-spacing: 0.12px;
        }
        
        .btn-action {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px;
            font-weight: bold;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
        }
        
        .custom-plan {
            padding: 20px;
        }
        
        .custom-plan-title {
            color: #175DA8;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .custom-plan-desc {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .custom-feature {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
            color: #000;
            font-family: Poppins;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }
        
        .custom-feature i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .support-image {
            max-width: 100%;
        }
        
        .trusted-section {
            margin-top: 30px;
        }
        
        .trusted-title {
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .company-logos {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .company-logo {
            height: 40px;
            margin: 0 10px 10px 0;
        }
        
        .rupee-symbol {
            font-family: Arial, sans-serif;
        }
        .finploy-contact{
            display: none;
        }
        
        /* Media queries for responsiveness */
        @media (max-width: 768px) {
            .company-logos {
                justify-content: center;
            }
            
            .company-logo {
                margin: 0 15px 15px;
            }
            .support-image{
                margin-top: 1.5rem;
            }
        }
        
    </style>
</head>
<body>
    <?php include'../header.php'; ?>
    <div class="pricing-container">
        <!-- Pricing Header -->
        <div class="pricing-header">
            <h1>Choose from our tailored plans</h1>
            <p class="text-muted">View how it works?</p>
        </div>
        
        <!-- Pricing Plans -->
        <div class="row">
            <!-- Pay as you go plan -->
            <div class="col-md-4">
                <div class="card pricing-card">
                    <div class="plan-title">Pay-as-you-go</div>
                    <div class="plan-subtitle">Ideal for small teams</div>
                    
                    <div class="plan-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M12.1081 11H11.8851C10.277 10.9459 9 9.62683 9 8.00338C9 6.34611 10.3378 5 12 5C13.6554 5 15 6.34611 15 8.00338C14.9932 9.62683 13.7333 10.9396 12.1081 11Z" stroke="black" stroke-width="1.21154" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M7.59677 13.9551C5.46774 15.0823 5.46774 16.9194 7.59677 18.0397C10.0161 19.3201 13.9839 19.3201 16.4032 18.0397C18.5323 16.9124 18.5323 15.0754 16.4032 13.9551C13.9927 12.6816 10.0249 12.6816 7.59677 13.9551Z" stroke="black" stroke-width="1.21154" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Rs.25/Per Candidate Profile</span>
                    </div>
                    
                    <div class="plan-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M7.8002 14.9998C7.64107 14.9998 7.48845 14.9366 7.37593 14.8241C7.26341 14.7115 7.2002 14.5589 7.2002 14.3998C7.2002 14.2407 7.26341 14.0881 7.37593 13.9755C7.48845 13.863 7.64107 13.7998 7.8002 13.7998H16.2002C16.3593 13.7998 16.5119 13.863 16.6245 13.9755C16.737 14.0881 16.8002 14.2407 16.8002 14.3998C16.8002 14.5589 16.737 14.7115 16.6245 14.8241C16.5119 14.9366 16.3593 14.9998 16.2002 14.9998H7.8002ZM7.8002 17.9998C7.64107 17.9998 7.48845 17.9366 7.37593 17.8241C7.26341 17.7115 7.2002 17.5589 7.2002 17.3998C7.2002 17.2407 7.26341 17.0881 7.37593 16.9755C7.48845 16.863 7.64107 16.7998 7.8002 16.7998H16.2002C16.3593 16.7998 16.5119 16.863 16.6245 16.9755C16.737 17.0881 16.8002 17.2407 16.8002 17.3998C16.8002 17.5589 16.737 17.7115 16.6245 17.8241C16.5119 17.9366 16.3593 17.9998 16.2002 17.9998H7.8002Z" fill="black"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4226 1.19971H5.40059C4.9232 1.19971 4.46536 1.38935 4.12779 1.72692C3.79023 2.06448 3.60059 2.52232 3.60059 2.99971V20.9997C3.60059 21.4771 3.79023 21.9349 4.12779 22.2725C4.46536 22.6101 4.9232 22.7997 5.40059 22.7997H18.6006C19.078 22.7997 19.5358 22.6101 19.8734 22.2725C20.2109 21.9349 20.4006 21.4771 20.4006 20.9997V8.64211C20.4005 8.19147 20.2313 7.75727 19.9266 7.42531L14.7498 1.78291C14.5811 1.599 14.376 1.45219 14.1475 1.35179C13.919 1.25139 13.6722 1.1996 13.4226 1.19971ZM4.80059 2.99971C4.80059 2.84058 4.8638 2.68797 4.97632 2.57544C5.08884 2.46292 5.24146 2.39971 5.40059 2.39971H13.4226C13.5058 2.39962 13.5882 2.41685 13.6644 2.45032C13.7407 2.48379 13.8091 2.53276 13.8654 2.59411L19.0422 8.23651C19.1439 8.3471 19.2004 8.49184 19.2006 8.64211V20.9997C19.2006 21.1588 19.1374 21.3115 19.0248 21.424C18.9123 21.5365 18.7597 21.5997 18.6006 21.5997H5.40059C5.24146 21.5997 5.08884 21.5365 4.97632 21.424C4.8638 21.3115 4.80059 21.1588 4.80059 20.9997V2.99971Z" fill="black"/>
                          <path d="M13.8008 2.51929V8.15929H19.4408" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M9.76171 7.35878C9.93308 7.36403 10.1037 7.33481 10.2636 7.27286C10.4235 7.2109 10.5693 7.11748 10.6923 6.99812C10.8154 6.87876 10.9132 6.7359 10.9801 6.57801C11.0469 6.42013 11.0813 6.25043 11.0813 6.07898C11.0813 5.90754 11.0469 5.73784 10.9801 5.57996C10.9132 5.42207 10.8154 5.27921 10.6923 5.15985C10.5693 5.04049 10.4235 4.94707 10.2636 4.88511C10.1037 4.82316 9.93308 4.79394 9.76171 4.79918C9.42907 4.80937 9.11347 4.94867 8.88178 5.18757C8.65008 5.42647 8.52051 5.74619 8.52051 6.07898C8.52051 6.41178 8.65008 6.7315 8.88178 6.9704C9.11347 7.2093 9.42907 7.3486 9.76171 7.35878Z" fill="black"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.3194 10.1329C12.3194 8.77207 11.1734 7.78687 9.7598 7.78687C8.3462 7.78687 7.2002 8.77087 7.2002 10.1329V10.7737C7.20051 10.8868 7.24566 10.9951 7.32574 11.075C7.40582 11.1548 7.5143 11.1997 7.6274 11.1997H11.8934C12.0063 11.1993 12.1145 11.1544 12.1943 11.0745C12.2741 10.9947 12.3191 10.8865 12.3194 10.7737V10.1329Z" fill="black"/>
                        </svg>
                        <span>Free 5 Profile Access</span>
                    </div>
                    
                    <div class="plan-price">
                        <span class="rupee-symbol">₹</span> 25
                        <span class="original-price">₹ 50</span>
                        <span class="discount-badge">50% OFF</span>
                    </div>
                    
                    <button class="btn-action" onclick="payWithRazorpay('Pay-as-you-go Plan', 25, 'PAYG')">Get Started Now</button>
                </div>
            </div>
            
            <!-- 1 Month plan -->
            <div class="col-md-4">
                <div class="card pricing-card">
                    <div class="plan-title">1 Month</div>
                    <div class="plan-subtitle">Perfect for growing businesses</div>
                    
                    <div class="plan-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M18.5331 5.86669H18V7.73338H14.3498V5.86669H9.6093V7.73338H6V5.86669H5.4663C4.43869 5.86669 3.59961 6.7067 3.59961 7.73338V18.9335C3.59961 19.9602 4.43869 20.8002 5.4663 20.8002H18.5331C19.5598 20.8002 20.3998 19.9602 20.3998 18.9335V7.73338C20.3998 6.7067 19.5598 5.86669 18.5331 5.86669ZM18.5331 18.9335H5.4663V11.4668H18.5331V18.9335ZM8.733 4H6.86631V7H8.733V4ZM17.1331 4H15.2664V7H17.1331V4Z" fill="black"/>
                        </svg>
                        <span> Use these credits in 30 days</span>
                    </div>
                    
                    <div class="plan-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M7.8002 14.9998C7.64107 14.9998 7.48845 14.9366 7.37593 14.8241C7.26341 14.7115 7.2002 14.5589 7.2002 14.3998C7.2002 14.2407 7.26341 14.0881 7.37593 13.9755C7.48845 13.863 7.64107 13.7998 7.8002 13.7998H16.2002C16.3593 13.7998 16.5119 13.863 16.6245 13.9755C16.737 14.0881 16.8002 14.2407 16.8002 14.3998C16.8002 14.5589 16.737 14.7115 16.6245 14.8241C16.5119 14.9366 16.3593 14.9998 16.2002 14.9998H7.8002ZM7.8002 17.9998C7.64107 17.9998 7.48845 17.9366 7.37593 17.8241C7.26341 17.7115 7.2002 17.5589 7.2002 17.3998C7.2002 17.2407 7.26341 17.0881 7.37593 16.9755C7.48845 16.863 7.64107 16.7998 7.8002 16.7998H16.2002C16.3593 16.7998 16.5119 16.863 16.6245 16.9755C16.737 17.0881 16.8002 17.2407 16.8002 17.3998C16.8002 17.5589 16.737 17.7115 16.6245 17.8241C16.5119 17.9366 16.3593 17.9998 16.2002 17.9998H7.8002Z" fill="black"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4226 1.19971H5.40059C4.9232 1.19971 4.46536 1.38935 4.12779 1.72692C3.79023 2.06448 3.60059 2.52232 3.60059 2.99971V20.9997C3.60059 21.4771 3.79023 21.9349 4.12779 22.2725C4.46536 22.6101 4.9232 22.7997 5.40059 22.7997H18.6006C19.078 22.7997 19.5358 22.6101 19.8734 22.2725C20.2109 21.9349 20.4006 21.4771 20.4006 20.9997V8.64211C20.4005 8.19147 20.2313 7.75727 19.9266 7.42531L14.7498 1.78291C14.5811 1.599 14.376 1.45219 14.1475 1.35179C13.919 1.25139 13.6722 1.1996 13.4226 1.19971ZM4.80059 2.99971C4.80059 2.84058 4.8638 2.68797 4.97632 2.57544C5.08884 2.46292 5.24146 2.39971 5.40059 2.39971H13.4226C13.5058 2.39962 13.5882 2.41685 13.6644 2.45032C13.7407 2.48379 13.8091 2.53276 13.8654 2.59411L19.0422 8.23651C19.1439 8.3471 19.2004 8.49184 19.2006 8.64211V20.9997C19.2006 21.1588 19.1374 21.3115 19.0248 21.424C18.9123 21.5365 18.7597 21.5997 18.6006 21.5997H5.40059C5.24146 21.5997 5.08884 21.5365 4.97632 21.424C4.8638 21.3115 4.80059 21.1588 4.80059 20.9997V2.99971Z" fill="black"/>
                          <path d="M13.8008 2.51929V8.15929H19.4408" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M9.76171 7.35878C9.93308 7.36403 10.1037 7.33481 10.2636 7.27286C10.4235 7.2109 10.5693 7.11748 10.6923 6.99812C10.8154 6.87876 10.9132 6.7359 10.9801 6.57801C11.0469 6.42013 11.0813 6.25043 11.0813 6.07898C11.0813 5.90754 11.0469 5.73784 10.9801 5.57996C10.9132 5.42207 10.8154 5.27921 10.6923 5.15985C10.5693 5.04049 10.4235 4.94707 10.2636 4.88511C10.1037 4.82316 9.93308 4.79394 9.76171 4.79918C9.42907 4.80937 9.11347 4.94867 8.88178 5.18757C8.65008 5.42647 8.52051 5.74619 8.52051 6.07898C8.52051 6.41178 8.65008 6.7315 8.88178 6.9704C9.11347 7.2093 9.42907 7.3486 9.76171 7.35878Z" fill="black"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.3194 10.1329C12.3194 8.77207 11.1734 7.78687 9.7598 7.78687C8.3462 7.78687 7.2002 8.77087 7.2002 10.1329V10.7737C7.20051 10.8868 7.24566 10.9951 7.32574 11.075C7.40582 11.1548 7.5143 11.1997 7.6274 11.1997H11.8934C12.0063 11.1993 12.1145 11.1544 12.1943 11.0745C12.2741 10.9947 12.3191 10.8865 12.3194 10.7737V10.1329Z" fill="black"/>
                        </svg>
                        <span> 200 Profile/CV Credits</span>
                    </div>
                    
                    <div class="plan-price">
                        <span class="rupee-symbol">₹</span> 1799
                        <span class="original-price">₹ 3600</span>
                        <span class="discount-badge">50% OFF</span>
                    </div>
                    
                    <button class="btn-action" onclick="payWithRazorpay('1 Month Plan', 1799, '1M')">Buy Now</button>
                </div>
            </div>
            
            <!-- 6 Months plan -->
            <div class="col-md-4">
                <div class="card pricing-card">
                    <div class="plan-title">6 Months</div>
                    <div class="plan-subtitle">Best fit for larger hiring needs</div>
                    
                    <div class="plan-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M18.5331 5.86669H18V7.73338H14.3498V5.86669H9.6093V7.73338H6V5.86669H5.4663C4.43869 5.86669 3.59961 6.7067 3.59961 7.73338V18.9335C3.59961 19.9602 4.43869 20.8002 5.4663 20.8002H18.5331C19.5598 20.8002 20.3998 19.9602 20.3998 18.9335V7.73338C20.3998 6.7067 19.5598 5.86669 18.5331 5.86669ZM18.5331 18.9335H5.4663V11.4668H18.5331V18.9335ZM8.733 4H6.86631V7H8.733V4ZM17.1331 4H15.2664V7H17.1331V4Z" fill="black"/>
                        </svg>
                        <span>Use these credits in 180 days</span>
                    </div>
                    
                    <div class="plan-feature">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M7.8002 14.9998C7.64107 14.9998 7.48845 14.9366 7.37593 14.8241C7.26341 14.7115 7.2002 14.5589 7.2002 14.3998C7.2002 14.2407 7.26341 14.0881 7.37593 13.9755C7.48845 13.863 7.64107 13.7998 7.8002 13.7998H16.2002C16.3593 13.7998 16.5119 13.863 16.6245 13.9755C16.737 14.0881 16.8002 14.2407 16.8002 14.3998C16.8002 14.5589 16.737 14.7115 16.6245 14.8241C16.5119 14.9366 16.3593 14.9998 16.2002 14.9998H7.8002ZM7.8002 17.9998C7.64107 17.9998 7.48845 17.9366 7.37593 17.8241C7.26341 17.7115 7.2002 17.5589 7.2002 17.3998C7.2002 17.2407 7.26341 17.0881 7.37593 16.9755C7.48845 16.863 7.64107 16.7998 7.8002 16.7998H16.2002C16.3593 16.7998 16.5119 16.863 16.6245 16.9755C16.737 17.0881 16.8002 17.2407 16.8002 17.3998C16.8002 17.5589 16.737 17.7115 16.6245 17.8241C16.5119 17.9366 16.3593 17.9998 16.2002 17.9998H7.8002Z" fill="black"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M13.4226 1.19971H5.40059C4.9232 1.19971 4.46536 1.38935 4.12779 1.72692C3.79023 2.06448 3.60059 2.52232 3.60059 2.99971V20.9997C3.60059 21.4771 3.79023 21.9349 4.12779 22.2725C4.46536 22.6101 4.9232 22.7997 5.40059 22.7997H18.6006C19.078 22.7997 19.5358 22.6101 19.8734 22.2725C20.2109 21.9349 20.4006 21.4771 20.4006 20.9997V8.64211C20.4005 8.19147 20.2313 7.75727 19.9266 7.42531L14.7498 1.78291C14.5811 1.599 14.376 1.45219 14.1475 1.35179C13.919 1.25139 13.6722 1.1996 13.4226 1.19971ZM4.80059 2.99971C4.80059 2.84058 4.8638 2.68797 4.97632 2.57544C5.08884 2.46292 5.24146 2.39971 5.40059 2.39971H13.4226C13.5058 2.39962 13.5882 2.41685 13.6644 2.45032C13.7407 2.48379 13.8091 2.53276 13.8654 2.59411L19.0422 8.23651C19.1439 8.3471 19.2004 8.49184 19.2006 8.64211V20.9997C19.2006 21.1588 19.1374 21.3115 19.0248 21.424C18.9123 21.5365 18.7597 21.5997 18.6006 21.5997H5.40059C5.24146 21.5997 5.08884 21.5365 4.97632 21.424C4.8638 21.3115 4.80059 21.1588 4.80059 20.9997V2.99971Z" fill="black"/>
                          <path d="M13.8008 2.51929V8.15929H19.4408" stroke="black" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M9.76171 7.35878C9.93308 7.36403 10.1037 7.33481 10.2636 7.27286C10.4235 7.2109 10.5693 7.11748 10.6923 6.99812C10.8154 6.87876 10.9132 6.7359 10.9801 6.57801C11.0469 6.42013 11.0813 6.25043 11.0813 6.07898C11.0813 5.90754 11.0469 5.73784 10.9801 5.57996C10.9132 5.42207 10.8154 5.27921 10.6923 5.15985C10.5693 5.04049 10.4235 4.94707 10.2636 4.88511C10.1037 4.82316 9.93308 4.79394 9.76171 4.79918C9.42907 4.80937 9.11347 4.94867 8.88178 5.18757C8.65008 5.42647 8.52051 5.74619 8.52051 6.07898C8.52051 6.41178 8.65008 6.7315 8.88178 6.9704C9.11347 7.2093 9.42907 7.3486 9.76171 7.35878Z" fill="black"/>
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M12.3194 10.1329C12.3194 8.77207 11.1734 7.78687 9.7598 7.78687C8.3462 7.78687 7.2002 8.77087 7.2002 10.1329V10.7737C7.20051 10.8868 7.24566 10.9951 7.32574 11.075C7.40582 11.1548 7.5143 11.1997 7.6274 11.1997H11.8934C12.0063 11.1993 12.1145 11.1544 12.1943 11.0745C12.2741 10.9947 12.3191 10.8865 12.3194 10.7737V10.1329Z" fill="black"/>
                        </svg>
                        <span>450 Profile/CV Credits</span>
                    </div>
                    
                    <div class="plan-price">
                        <span class="rupee-symbol">₹</span> 6599
                        <span class="original-price">₹ 13200</span>
                        <span class="discount-badge">50% OFF</span>
                    </div>
                    
                    <button class="btn-action" onclick="payWithRazorpay('6 Months Plan', 6599, '6M')">Buy Now</button>
                </div>
            </div>
        </div>
        
        <!-- Custom Plan -->
        <div class="card custom-plan mt-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="custom-plan-title">Want a personalised plan?</div>
                    <div class="custom-plan-desc">Unlock unlimited growth with advanced features and support. Contact sales for custom pricing.</div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="custom-feature">
                                <i class="fab fa-whatsapp"></i>
                                <span>Multimedia WhatsApp invites</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-feature">
                                <i class="fas fa-cogs"></i>
                                <span>ATS Integration</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-feature">
                                <i class="far fa-clock"></i>
                                <span>Valid up to 360 days</span>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn-action mt-3" style="max-width: 200px;">Contact 8169449669</button>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-center">
                    <img src="assets/customer-support.svg" alt="Customer Support" class="support-image">
                </div>
            </div>
        </div>
        
        <!-- Trusted By Section -->
        <!--<div class="trusted-section">-->
        <!--    <div class="trusted-title">Trusted by 10,000+ Company's...</div>-->
        <!--    <div class="company-logos">-->
        <!--        <img src="../assets/Aditya Birla Capital.png" alt="Aditya Birla Capital" class="company-logo">-->
        <!--        <img src="../assets/Hinduja Housing Finance.png" alt="Hinduja Housing Finance" class="company-logo">-->
        <!--        <img src="../assets/Sammaan Capital.png" alt="Sammaan Capital" class="company-logo">-->
        <!--        <img src="../assets/Shubham Housing Finance.png" alt="Shubham Housing Finance" class="company-logo">-->
        <!--        <img src="../assets/Niwas Housing Finance.png" alt="Niwas Housing Finance" class="company-logo">-->
        <!--    </div>-->
        <!--</div>-->
    </div>
 
 <!-- Add this in your <head> or before your script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Razorpay JavaScript SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    // JavaScript for any interactive elements
    document.addEventListener('DOMContentLoaded', function() {
        // You can add any JavaScript functionality here if needed
        // For example, handling the "View how it works?" click
        document.querySelector('.pricing-header p').addEventListener('click', function() {
            alert('This would show how the pricing works');
        });
    });

   function payWithRazorpay(planName, amount, planCode) {
    $.ajax({
        url: "create_order.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ amount: amount, plan: planName, planCode: planCode }),
        success: function (response) {
            console.log("Order Created:", response);
            if (!response.id) {
                alert("Error creating order! Try again.");
                return;
            }

            var options = {
                "key": "rzp_test_rtxMNYHT7oPGHY",
                "amount": amount * 100,
                "currency": "INR",
                "name": "Finploy",
                "description": planName,
                "order_id": response.id, // Pass order ID
                "handler": function (paymentResponse) {
                    // Send payment details to server
                    $.ajax({
                        url: "success.php",
                        type: "POST",
                        data: {
                            razorpay_payment_id: paymentResponse.razorpay_payment_id,
                            razorpay_order_id: response.id, // Now we have the order ID
                            status: "success"
                        },
                        success: function (msg) {
                            console.log("Server Response:", msg);
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            alert("AJAX Error: " + error);
                        }
                    });
                },
                "prefill": {
                    "name": <?php echo json_encode($_SESSION['name']); ?>,
                    "email": "employer@gmail.com",
                    "contact": <?php echo json_encode($_SESSION['mobile']); ?>,
                },
                "theme": {
                    "color": "#175DA8"
                }
            };

            var rzp = new Razorpay(options);
            rzp.open();
        },
        error: function (xhr, status, error) {
            console.error("Error Creating Order:", xhr.responseText);
            alert("Error creating order. Please try again.");
        }
    });
}




</script>


    
</body>
<footer><?php include'../footer.php'; ?></footer>

</html>