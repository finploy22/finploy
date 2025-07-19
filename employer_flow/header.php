
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finploy Job Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicons/favicon.ico">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
    <link rel="stylesheet" href="./css/employer.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="employer.js"></script>

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Latest Font Awesome CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicons/favicon.ico">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
   
    <link rel="stylesheet" href="./css/employer.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Latest Font Awesome CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
 <style>
/* Candidate Popup Styles */
.candidate-popup {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    padding: 0;
}

.candidate-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #9C4D4D;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 10px;
    text-transform: uppercase;
}

.candidate-name-rating h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
}



.candidate-meta {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 15px;
    padding-bottom: 15px;
}

.meta-item {
    flex: 1 0 50%;
    margin-bottom: 10px;
    font-size: 14px;
    color: #444;
    display: flex;
    align-items: center;
}

.meta-item i {
    margin-right: 8px;
    color: #4a6da7;
    font-size: 14px;
    width: 16px;
}

.candidate-description h5 {
    font-size: 16px;
    margin-bottom: 12px;
    color: #333;
    font-weight: 500;
}

.description-item {
    margin-bottom: 10px;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.description-item i {
    margin-right: 8px;
    color: #4a6da7;
    width: 16px;
}

.additional-details h6 {
    font-size: 15px;
    margin: 15px 0 10px;
    font-weight: 500;
    color: #333;
}

.additional-details ul {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 15px;
}

.additional-details ul li {
    margin-bottom: 5px;
    font-size: 14px;
    color: #444;
    position: relative;
    padding-left: 15px;
}

.additional-details ul li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #666;
}

.text-primary {
    color: #007bff;
}

.candidate-resume {
    margin-bottom: 15px;
}

.candidate-resume h5 {
    font-size: 16px;
    margin-bottom: 10px;
    color: #333;
    font-weight: 500;
}

.resume-preview {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    text-align: center;
    background-color: #f9f9f9;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.resume-preview img {
    max-width: 100%;
    max-height: 100%;
}

.add-to-cart-section {
    margin-top: 20px;
}

.add-to-cart-btn-pop {
    padding: 10px;
    font-size: 16px;
    border-radius: 4px;
    background-color: #4CAF50;
    border-color: #4CAF50;
    width: 100%;
}

.add-to-cart-btn i {
    margin-right: 8px;
}

/* Modal adjustments */
#candidateModal .modal-dialog {
    max-width: 400px;
    margin: 1.75rem auto;
}

#candidateModal .modal-content {
    border-radius: 8px;
    box-shadow: 0 3px 7px rgba(0,0,0,0.2);
}

#candidateModal .modal-header {
    border-bottom: none;
    padding: 10px 15px 0;
}

#candidateModal .modal-body {
    padding: 15px;
}

#candidateModal .close {
    font-size: 1.5rem;
    opacity: 0.7;
}

#candidateModal .close:hover {
    opacity: 1;
}

.reject-pop-up {
   padding: 10px 30px 10px 22px !important;
  border-radius: 8px !important;
    border: 1px solid #ED4C5C !important;
    background: #FFF !important;
    color: #ED4C5C !important;
    font-family: Poppins !important;
    font-size: 12px !important;
    font-style: normal !important;
    font-weight: 600 !important;
    line-height: normal !important;
}


.select-pop-up {
 padding: 10px 30px 10px 22px !important;
  border-radius: 8px !important;
  border: 1px solid #4EA647 !important;
  background: #FFF !important;
  color: #4EA647 !important;
  font-family: Poppins !important;
  font-size: 12px !important;
  font-style: normal !important;
  font-weight: 600 !important;
  line-height: normal !important;
    
}
.close-pop-up {
    top: -20px;
  left: -71px;
  background: white !important;
padding: 6px 11px !important;
}

.close-pop-up i {
    font-size:28px;
    color:#FF0015;
    
}


.resume-preview-container {
    position: relative;
}

.blurred-resume {
    filter: blur(5px);
}

.lock-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 3rem;
    color: rgba(0, 0, 0, 0.7);
    pointer-events: none; /* so clicks pass through if needed */
}

.work-pop { 
	color: #000;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}
.gender-pop {
	color: #000;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.salary-pop {

	color: #000;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.location-pop {
	color: #000;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.pop-subhead-candidate {
	color: #000;
	font-family: Poppins;
	font-size: 18px;
	font-style: normal;
	font-weight: 600;
	line-height: normal;
}

.pop-strong {
	color: #000;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.company-text-pop {
	color: #175DA7;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.role-text-pop {
	color: #175DA7;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.pop-subhead-add {
	color: #000;
	font-family: Poppins;
	font-size: 18px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.pop-add-text{
	color: #175DA7;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}
.pop-resume {
	color: #000;
	font-family: Poppins;
	font-size: 18px;
	font-style: normal;
	font-weight: 600;
	line-height: normal;
}
.add-text-subhead-pop {
	color: #000;
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.pop-fullname {
	color: #000;
	font-family: Poppins;
	font-size: 20px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.pop-mobile {
	color: #000;
	font-family: Poppins;
	font-size: 20px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
}

.pop-add-cart-btn {
    border-radius: 10px !important;
    background: var(--Deeesha-Green, #4EA647) !important;
    color: #FFF !important;
    font-family: Poppins !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    line-height: normal !important;
}

.text-pop-active{
    float: right;
  color: #888;
  font-family: Poppins;
  font-size: 12px;
  font-style: normal;
  font-weight: 500;
  line-height: normal;
}

.main-div-active {
  border-top:  1px solid #888;  
}
.avatar-circle-pop {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #9C4D4D;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 10px;
    text-transform: uppercase;
}

@media (max-width: 576px) {

.avatar-circle-pop {
	width: 74px !important;
	height: 49px !important;
}
.pop-mobile {
	color: #000;
	font-family: Poppins;
	font-size: 20px;
	font-style: normal;
	font-weight: 500;
	line-height: normal;
	padding-left: 115px;
}
 .pop-span-line {
      display:none;
      margin-right:90px !important ;
  }
  .company-text-pop {
	font-size: 14px;
}

#candidateModal .modal-dialog {
    max-width: 350px !important;
   margin: 1.75rem auto;
}
.close-pop-up {
	left: 289px;
}

}

@media screen and (min-width: 768px) {
  .modal-dialog {
    margin-right: 20px !important;
    margin-top: 40px !important;
  }
  
 
}

.tempary-number {
font-size:12px;
}

/* Hide the dropdown arrow */
  .dropdown-toggle::after {
    display: none !important;
  }

  .dropdown-toggle::after {
    display: none !important;
  }

  /* Custom dropdown menu styling */
  .custom-dropdown {
    position: relative;
    background-color: #fff;
    border: none;
  border-radius: 8px;
background: #FFF;
box-shadow: 0px -4px 14px 0px rgba(0, 0, 0, 0.10), 0px 4px 14px 0px rgba(0, 0, 0, 0.15);
  }

  /* Create the speech-bubble arrow */
  .custom-dropdown::before {
    content: "";
    position: absolute;
    top: -10px;         /* Move it above the menu */
    right: 20px;        /* Adjust horizontal position */
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 10px solid #fff; /* same color as dropdown */
  }

  /* Style dropdown items with icons */
.custom-dropdown .dropdown-item {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	color: var(--Black-font, #313739);
	font-family: Poppins;
	font-size: 16px;
	font-style: normal;
	font-weight: 400;
	line-height: normal;
	padding: 10px 52px;
	margin-left: -32px;
}
  /* Hover effect for dropdown items */
 .custom-dropdown .dropdown-item:hover {
  background-color: transparent !important;
  /* Optional: Also reset text color if it changes on hover */
  color: inherit !important;
}

.dropdown-item i {
    color:#175DA8;
}

.tempary-number {
font-size:11px;
}

.mobile-dropdown{
  display: none;
  position: absolute;
  right: 0 !important;
  top: 100%;
  background-color: white;
  /*box-shadow: 0 4px 8px rgba(0,0,0,0.1);*/
  border-radius: 4px;
  z-index: 1000;
  left: auto !important;
  width: 180px;
  float:right !important;
  margin-right:10px !important;
}

.mobile-dropdown.show {
  display: block;
}

.nav-link.profile {
  position: relative;
}

@media (max-width: 576px) {
    .custom-dropdown::before {
	content: "";
	position: absolute;
	top: -10px;
	right: 20px;
	width: 0;
	height: 0;
	border-left: 10px solid transparent;
	border-right: 10px solid transparent;
	border-bottom: 10px solid #fff;
}
.custom-dropdown-showing, /* Hide "Showing" dropdown */
    .page-count-pagination, /* Hide "Page X of Y" */
    .main-pagination-top { /* Hide pagination controls */
        display: none !important;
    }
   .showing-pag::after {
        display:none;
    }
    /*.pag-toatal-bottom {*/
    /*    padding: 10px;*/
    /*}*/
.custom-dropdown .dropdown-item {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	color: var(--Black-font, #313739);
	font-family: Poppins;
	font-size: 14px;
	font-style: normal;
	font-weight: 400;
	line-height: normal;
	padding: 9px 52px;
	margin-left: -32px;
}
.dropdown-item svg {
  width: 17px;
  height: 17px;
  flex-shrink: 0; /* Prevents the SVG from shrinking */
}

.pop-fullname {
	font-size: 18px;
}
.pop-mobile {
font-size: 18px;
}
.gender-pop {
	font-size: 14px;
}
.salary-pop {
	font-size: 14px;
}
.location-pop {
	font-size: 14px;
}
.pop-subhead-candidate {
	font-size: 16px;
}
.pop-strong {
	font-size: 14px;
}
.role-text-pop {
	font-size: 14px;

}
.pop-subhead-add {
	font-size: 16px;
}

.add-text-subhead-pop {
	font-size: 14px;
}
.pop-add-text {
	font-size: 14px;
}

.pop-resume {
	font-size: 16px;
}
.close-pop-up i {
	font-size: 24px;
}
}

.dropdown-item svg {
  width: 20px;
  height: 20px;
  flex-shrink: 0; /* Prevents the SVG from shrinking */
}
/* For Bootstrap, we need to make sure to override with high specificity */
.dropdown-menu .dropdown-item:active,
.dropdown-menu .dropdown-item.active {
  background-color: transparent !important;
  color: inherit !important;
}

.dropdown-item:focus,
.dropdown-item:active,
.dropdown-item.active {
  background-color: transparent !important;
  outline: none !important;
}

.page-item.active .page-link {
	z-index: 1;
  background-color: #d6d6d6; /* Cement color */
        color: black; /* Text color */
        border-radius: 4px;
border: 0.5px solid var(--Deeesha-Blue, #175DA8) !important;
background: #FFF;
	/* height: ; */
	/* padding-top: 2px; */
	padding-bottom: 1px;
	margin-top: 3px;
}
 .pagination .page-link {
        border: none; /* Remove border from all buttons */
        background: #F6F8F9;
        color: black; /* Text color */
    }
    
    .pagination .page-item.active .page-link {
        border: 1px solid #333; /* Add border for active page */
        background-color: #F6F8F9; /* White background for active */
        color: #175DA8;
        padding: 4px 11px;
        
    }

    /*.pagination .page-link:focus, */
    /*.pagination .page-link:hover {*/
        background-color: #bcbcbc; /* Slightly darker on hover */
    /*}*/
   
 .pagination {
      
        background-color: #d6d6d6; /* Cement color */
        color: black; /* Text color */
        border-radius: 4px;

background: #F6F8F9;
 }
 .page-item  {
      border: none;
     background: #F6F8F9 !important;
        color: #175DA8  !important; 
        margin-top: 2px;
 }
    
.page-item.disabled .page-link {
	color: #6c757d;
	pointer-events: none;
	cursor: auto;
    background: #F6F8F9 !important;
	border-color: #dee2e6;
}

.page-count-pagination{
    color: #747474;
font-family: Poppins;
font-size: 13px;
font-style: normal;
font-weight: 500;
line-height: normal;
}
.page-count-pagination-bottom {
      color: #747474;
font-family: Poppins;
font-size: 13px;
font-style: normal;
font-weight: 500;
line-height: normal;
}
.page-act-pagination {
color: #747474;
font-family: Poppins;
font-size: 13px;
font-style: normal;
font-weight: 500;
line-height: normal;
}
#recordsPerPageSelect {
    border-radius: 4px;
border: 0.5px solid var(--Deeesha-Blue, #175DA8);

}
#dateFilter{
     border-radius: 4px;
border: 0.5px solid var(--Deeesha-Blue, #175DA8); 
}

/* Style the dropdown */
.custom-dropdown-pagination {
    appearance: none; /* Removes default dropdown icon */
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: white;
    border: 1px solid #ccc;
   padding: 6px 30px 6px 10px;
    border-radius: 5px;
    cursor: pointer;
    position: relative;
      color: #747474;
font-family: Poppins;
font-size: 13px;
font-style: normal;
font-weight: 500;
line-height: normal
}

/* Custom dropdown arrow */
.custom-dropdown-wrapper {
    position: relative;
    display: inline-block;
}

.custom-dropdown-wrapper::after {
    content: "";
    position: absolute;
    top: 50%;
    right: 10px;
    width: 17px;
    height: 16px;
    background: url("data:image/svg+xml,%3Csvg width='17' height='16' viewBox='0 0 17 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.5 6.25024L8.6665 10.4167L12.833 6.25024H4.5Z' fill='%23175DA8'/%3E%3C/svg%3E") no-repeat center;
    background-size: contain;
    transform: translateY(-50%);
    pointer-events: none; /* Prevents the icon from blocking clicks */
}

.showing-pag::after {
     content: "";
    position: absolute;
    top: 50%;
    right: 10px;
    width: 17px;
    height: 16px;
    background: url("data:image/svg+xml,%3Csvg width='17' height='16' viewBox='0 0 17 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.5 6.25024L8.6665 10.4167L12.833 6.25024H4.5Z' fill='%23175DA8'/%3E%3C/svg%3E") no-repeat center;
    background-size: contain;
    transform: translateY(-50%);
    pointer-events: none; /* Prevents the icon from blocking clicks */
}
    
}
/* Custom Dropdown Container */
.custom-dropdown-wrapper {
    position: relative;
    display: inline-block;
}

/* Hide default select appearance */
.custom-dropdown-showing{
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: white;
    border: 1px solid #ccc;
  padding: 6px 15px;
    border-radius: 5px;
    cursor: pointer;
    width: auto;
          color: #747474;
font-family: Poppins;
font-size: 13px;
font-style: normal;
font-weight: 500;
line-height: normal
}

/* Custom Dropdown Arrow */
.custom-dropdown-wrapper::after {
    content: "";
    position: absolute;
    top: 50%;
    right: 10px;
    width: 16px;
    height: 16px;
    background: url("data:image/svg+xml,%3Csvg width='17' height='16' viewBox='0 0 17 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.5 6.25024L8.6665 10.4167L12.833 6.25024H4.5Z' fill='%23175DA8'/%3E%3C/svg%3E") no-repeat center;
    background-size: contain;
    transform: translateY(-50%);
    pointer-events: none;
}

/* Dropdown Hover & Focus */
.custom-dropdown:focus {
    border-color: #175DA8;
    outline: none;
    box-shadow: 0 0 5px rgba(23, 93, 168, 0.5);
}
.pagination .page-item .page-link {
    color: inherit;
}

.page-link {
  transition: none !important;
  transform: none !important;
  box-shadow: none !important;
}

/* Prevent zoom/scale on hover */
.page-link:hover {
  transform: none !important;
  box-shadow: none !important;
}

/* Prevent zoom/scale on focus */
.page-link:focus {
  outline: none !important;
  transform: none !important;
  box-shadow: none !important;
}

/* Prevent zoom/scale on active (when clicking) */
.page-link:active {
  transform: none !important;
  box-shadow: none !important;
}
/* Change icon color on click */
.page-link:active svg path {
  stroke: #007bff !important; /* Bootstrap primary blue */
}

/* Optionally: change on hover too */
.page-link:hover svg path {
  stroke: #007bff;
}
.bottom-page:hover {
background-color: #175DA8 !important;
}
.bottom-page {
    color: white !important;
  background-color: #175DA8 !important;
}
.page-other:hover {
background-color: transparent !important; /* <- make background none */
}

/*.main-div-posting-job {*/
/*  background-image: url('image/bg-image.png') !important;*/
/*  background-size: cover;*/
/*  background-position: center;*/
/*  background-repeat: no-repeat;*/
/*}*/
/*.form-posting {*/
/*opacity: 0.7;*/
/*  background: #FFF;*/
/*box-shadow: 0px 1px 8px 0px rgba(0, 0, 0, 0.24);*/
/*}*/

/*.main-div-posting-job {*/
/*    padding-top:1px;*/
/*}*/

 </style>
</head>
<body>
     
    <!-- Header -->
   <!-- Desktop Navbar: Visible on lg (desktop) screens and above -->
<nav class="navbar navbar-expand-lg navbar-light bg-white  header d-none d-lg-flex ">
  <div class="container-fluid">
    <!-- Your original desktop navbar content -->
     
      <!--<div class="tempary-number">  <?php echo $employer_mobile; ?></div>-->
    <a class="navbar-brand" href="#">
      <img src="image/logo.png" alt="Finploy" height="50">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#desktopNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="desktopNavbar">
      <ul class="navbar-nav ms-auto">
           <li class="nav-item">
          <a class="nav-link" href="accessed_candidates.php">Seen<i class="fa fa-eye" aria-hidden="true"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="employer.php">Home <i class="fa fa-home"></i></a>
        </li>
        <!-- Other desktop nav items -->
        <li class="nav-item">
          <a class="nav-link position-relative" href="cart_page.php">
         Cart  <svg width="24" height="25" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1106 6.66675H9.33333V5.33341C9.33333 4.62617 9.05238 3.94789 8.55229 3.4478C8.05219 2.9477 7.37391 2.66675 6.66667 2.66675H4V5.33341H6.66667V20.0001C6.66667 20.7073 6.94762 21.3856 7.44772 21.8857C7.94781 22.3858 8.62609 22.6667 9.33333 22.6667H25.3333C25.3333 21.9595 25.0524 21.2812 24.5523 20.7811C24.0522 20.281 23.3739 20.0001 22.6667 20.0001H9.33333V17.3334H23.1947C23.8013 17.3333 24.3897 17.1264 24.8629 16.7468C25.1699 16.5005 25.4164 16.1912 25.5877 15.8425C25.0745 15.9458 24.5436 16.0001 24 16.0001C19.5817 16.0001 16 12.4183 16 8.00006C16 7.54578 16.0379 7.10035 16.1106 6.66675ZM9.33333 29.3334C10.8 29.3334 12 28.1334 12 26.6667C12 25.2001 10.8 24.0001 9.33333 24.0001C7.86667 24.0001 6.68 25.2001 6.68 26.6667C6.68 28.1334 7.86667 29.3334 9.33333 29.3334ZM20.0133 26.6667C20.0133 25.2001 21.2 24.0001 22.6667 24.0001C24.1333 24.0001 25.3333 25.2001 25.3333 26.6667C25.3333 28.1334 24.1333 29.3334 22.6667 29.3334C21.2 29.3334 20.0133 28.1334 20.0133 26.6667Z" fill="#175DA8"/>
</svg>
            <span id="cart-count" class="badge position-absolute top-10 start-100 translate-middle">0</span>
          </a>
        </li>
        
        <li class="nav-item" id="available-credits" >
        <a class="nav-link d-flex align-items-center" href="#">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
              <path d="M28.0005 10.666C28.0005 12.026 26.4192 13.226 24.0005 13.9487C22.4885 14.402 20.6499 14.666 18.6672 14.666C16.6845 14.666 14.8459 14.4007 13.3339 13.9487C10.9165 13.226 9.33386 12.026 9.33386 10.666C9.33386 8.45668 13.5125 6.66602 18.6672 6.66602C23.8219 6.66602 28.0005 8.45668 28.0005 10.666Z" fill="#175DA8"/>
              <path d="M28.0005 10.666C28.0005 8.45668 23.8219 6.66602 18.6672 6.66602C13.5125 6.66602 9.33386 8.45668 9.33386 10.666M28.0005 10.666V15.9993C28.0005 17.3593 26.4192 18.5593 24.0005 19.282C22.4885 19.7353 20.6499 19.9993 18.6672 19.9993C16.6845 19.9993 14.8459 19.734 13.3339 19.282C10.9165 18.5593 9.33386 17.3593 9.33386 15.9993V10.666M28.0005 10.666C28.0005 12.026 26.4192 13.226 24.0005 13.9487C22.4885 14.402 20.6499 14.666 18.6672 14.666C16.6845 14.666 14.8459 14.4007 13.3339 13.9487C10.9165 13.226 9.33386 12.026 9.33386 10.666" stroke="#175DA8" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 16.0004V21.3337C4 22.6937 5.58267 23.8937 8 24.6164C9.512 25.0697 11.3507 25.3337 13.3333 25.3337C15.316 25.3337 17.1547 25.0684 18.6667 24.6164C21.084 23.8937 22.6667 22.6937 22.6667 21.3337V20.0004M4 16.0004C4 14.4044 6.18 13.0271 9.33333 12.3857M4 16.0004C4 17.3604 5.58267 18.5604 8 19.2831C9.512 19.7364 11.3507 20.0004 13.3333 20.0004C14.26 20.0004 15.1547 19.9431 16 19.8351" stroke="#175DA8" stroke-width="2.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          <span>Available Credits</span>
        </a>
      </li>
      <div id="credits-container"></div>
        
<li class="nav-item dropdown bell-container">
  <a class="nav-link position-relative bell-svg" 
     href="#" 
     role="button" 
     data-bs-toggle="dropdown" 
     aria-expanded="false" 
     onclick="toggleNotifications('desktop', event); return false;">
      
    <!-- SVG icon code here -->
     <svg width="40" height="40" viewBox="0 0 52 65" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g filter="url(#filter0_d_2644_948)">
        <rect x="6" y="6" width="40" height="40" rx="8.66071" fill="white"/>
        <path d="M34.3084 35.8887L34.0099 36.9909L13.9622 31.6746L14.2606 30.5723L17.085 28.9584L18.8756 22.3447C19.8008 18.9277 22.8764 16.518 26.4469 16.4252L26.5335 16.1055C26.6918 15.5209 27.0783 15.0224 27.6079 14.7197C28.1376 14.417 28.7671 14.335 29.3579 14.4917C29.9486 14.6484 30.4523 15.0309 30.7581 15.5551C31.0639 16.0793 31.1468 16.7023 30.9885 17.287L30.902 17.6066C33.9472 19.4538 35.3934 23.0626 34.4683 26.4797L32.6777 33.0934L34.3084 35.8887ZM25.9152 36.0257C25.7569 36.6104 25.3704 37.1089 24.8407 37.4116C24.311 37.7142 23.6816 37.7963 23.0908 37.6396C22.5 37.4829 21.9963 37.1004 21.6905 36.5762C21.3847 36.052 21.3018 35.429 21.4601 34.8443" fill="#175DA8"/>
      </g>
      <defs>
        <filter id="filter0_d_2644_948" x="0.226191" y="0.947917" width="51.5476" height="51.5476" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
          <feFlood flood-opacity="0" result="BackgroundImageFix"/>
          <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
          <feOffset dy="0.721726"/>
          <feGaussianBlur stdDeviation="2.8869"/>
          <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.24 0"/>
          <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2644_948"/>
          <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2644_948" result="shape"/>
        </filter>
      </defs>
    </svg>
    <?php if ($notificationCount > 0): ?>
      <span id="notification-count" class="desktop-badge bg-danger position-absolute top-0 start-100 translate-middle">
        <?php echo $notificationCount; ?>
      </span>
    <?php endif; ?>
  </a>
  <!-- Custom Notification Container -->
  <div id="notifications-desktop" class="notification-container">
    <div class="notification-header">
      <h3>Notifications (<?php echo $notificationCount; ?>)</h3>
    </div>
    <div class="notification-body">
      <?php if (count($expiredNotifications) > 0): ?>
        <?php foreach ($expiredNotifications as $notification): ?>
          <div class="notification-item">
            <?php echo $notification['message']; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="notification-item">No notifications</div>
      <?php endif; ?>
    </div>
    <div class="notification-footer">
      <a href="notifications_dashboard.php" class="see-all">See All Notifications</a>
    </div>
  </div>
</li>
     <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdownMenuLink" role="button"
           data-bs-toggle="dropdown" aria-expanded="false">
          <?php echo $employer_name; ?>
          <span class="profile-desk"><?php echo strtoupper(substr($employer_name, 0, 1)); ?></span>
        </a>
        <ul class="dropdown-menu custom-dropdown" aria-labelledby="dropdownMenuLink">
          <li>
            <a class="dropdown-item" href="#">
              <svg width="53" height="25" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
             <path fill-rule="evenodd" clip-rule="evenodd" d="M7.935 9.8432C5.07807 10.7252 3 13.3885 3 16.5333C3 16.6571 3.04917 16.7758 3.13668 16.8633C3.2242 16.9508 3.3429 17 3.46667 17C3.59043 17 3.70913 16.9508 3.79665 16.8633C3.88417 16.7758 3.93333 16.6571 3.93333 16.5333C3.93333 13.185 6.65167 10.4667 10 10.4667C13.3483 10.4667 16.0667 13.185 16.0667 16.5333C16.0667 16.6571 16.1158 16.7758 16.2034 16.8633C16.2909 16.9508 16.4096 17 16.5333 17C16.6571 17 16.7758 16.9508 16.8633 16.8633C16.9508 16.7758 17 16.6571 17 16.5333C17 13.3885 14.9219 10.7252 12.065 9.8432C12.5782 9.5028 12.9991 9.04066 13.2902 8.49802C13.5813 7.95539 13.7335 7.34912 13.7333 6.73333C13.7333 4.673 12.0603 3 10 3C7.93967 3 6.26667 4.673 6.26667 6.73333C6.26648 7.34912 6.41873 7.95539 6.70984 8.49802C7.00094 9.04066 7.42185 9.5028 7.935 9.8432ZM10 3.93333C11.5451 3.93333 12.8 5.1882 12.8 6.73333C12.8 8.27847 11.5451 9.53333 10 9.53333C8.45487 9.53333 7.2 8.27847 7.2 6.73333C7.2 5.1882 8.45487 3.93333 10 3.93333Z" fill="#175DA8" stroke="#175DA8" stroke-width="0.5"/>
             </svg>My Profile
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="accessed_candidates.php">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M5.51343 7.38807L8.65021 4.25011V6.24987V6.34987H8.75021H10.0002H10.1002V6.24987V4.25004L13.2382 7.38808L13.309 7.45884L13.3797 7.38803L14.2635 6.50303L14.3341 6.43232L14.2634 6.36166L9.88842 1.98666L9.85913 1.95737H9.81771H8.93396H8.89254L8.86325 1.98666L4.48825 6.36166L4.41759 6.43232L4.4882 6.50303L5.37195 7.38803L5.44267 7.45886L5.51343 7.38807Z" fill="#175DA8" stroke="#175DA8" stroke-width="0.2"/>
  <path d="M1.875 11.15H1.83358L1.80429 11.1793L1.17929 11.8043L1.15 11.8336V11.875V18.125V18.1664L1.17929 18.1957L1.80429 18.8207L1.83358 18.85H1.875H16.875H16.9164L16.9457 18.8207L17.5707 18.1957L17.6 18.1664V18.125V11.875V11.8336L17.5707 11.8043L16.9457 11.1793L16.9164 11.15H16.875H12.4375H12.3558L12.3395 11.2301C12.2006 11.9139 11.8296 12.5287 11.2894 12.9703C10.7491 13.4119 10.0728 13.6531 9.375 13.6531C8.67722 13.6531 8.0009 13.4119 7.46062 12.9703C6.92035 12.5287 6.54936 11.9139 6.4105 11.2301L6.39424 11.15H6.3125H1.875ZM8.75 7.4H8.65V7.5V8.75V8.85H8.75H10H10.1V8.75V7.5V7.4H10H8.75ZM8.75 9.9H8.65V10V11.25V11.35H8.75H10H10.1V11.25V10V9.9H10H8.75ZM11.7679 14.4084C12.4659 13.9667 13.0277 13.3407 13.3915 12.6H16.15V17.4H2.6V12.6H5.35847C5.72231 13.3407 6.28409 13.9667 6.9821 14.4084C7.69801 14.8614 8.5278 15.1019 9.375 15.1019C10.2222 15.1019 11.052 14.8614 11.7679 14.4084Z" fill="#175DA8" stroke="#175DA8" stroke-width="0.2"/>
</svg> Accessed Candidates
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#">
       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M11.876 1.81785C11.3327 1.6742 10.7777 1.9971 10.634 2.54044L10.4782 3.12982C8.11719 2.99165 5.90455 4.52828 5.27636 6.90423L5.0702 7.68393C4.70173 9.07758 3.868 10.3055 2.71196 11.1625L2.1028 11.6156C1.86632 11.7895 1.7522 12.0845 1.80763 12.3718C1.86305 12.659 2.07789 12.8899 2.3603 12.9646L14.6391 16.211C14.9215 16.2857 15.2224 16.1912 15.4125 15.9689C15.6027 15.7466 15.6493 15.4337 15.5297 15.1656L15.2232 14.4737C14.6426 13.1543 14.5248 11.6748 14.8932 10.2811L15.0994 9.50142C15.7276 7.12547 14.5636 4.69606 12.4428 3.64926L12.5986 3.05988C12.7423 2.51654 12.4194 1.96151 11.876 1.81785ZM11.0969 4.76476C12.997 5.26716 14.1283 7.2117 13.6259 9.11184L13.4198 9.89155C13.031 11.3619 13.0787 12.9083 13.546 14.3456L4.23255 11.8831C5.34918 10.8646 6.15489 9.5439 6.54366 8.07351L6.74981 7.29381C7.25221 5.39366 9.19675 4.26237 11.0969 4.76476ZM10.2046 16.0895L6.27537 15.0507C6.1374 15.5725 6.21077 16.1273 6.48174 16.593C6.75271 17.0588 7.19869 17.3967 7.72054 17.5347C8.24239 17.6727 8.79713 17.5993 9.26289 17.3283C9.72865 17.0574 10.0666 16.6114 10.2046 16.0895Z" fill="#175DA8"/>
</svg>Notification
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#">
             <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <g clip-path="url(#clip0_1816_1884)">
               <path d="M10.0001 18.7619C14.8391 18.7619 18.7619 14.8391 18.7619 10.0001C18.7619 5.16107 14.8391 1.23828 10.0001 1.23828C5.16107 1.23828 1.23828 5.16107 1.23828 10.0001C1.23828 14.8391 5.16107 18.7619 10.0001 18.7619Z" stroke="#175DA8" stroke-width="1.31458" stroke-linecap="round" stroke-linejoin="round"/>
               <path d="M7.97803 7.97801C7.97803 7.5781 8.09661 7.18718 8.31879 6.85467C8.54096 6.52216 8.85675 6.263 9.22621 6.10997C9.59567 5.95693 10.0022 5.91689 10.3944 5.99491C10.7867 6.07292 11.1469 6.2655 11.4297 6.54827C11.7125 6.83105 11.9051 7.19132 11.9831 7.58354C12.0611 7.97576 12.0211 8.38231 11.868 8.75177C11.715 9.12124 11.4558 9.43702 11.1233 9.6592C10.7908 9.88137 10.3999 9.99996 9.99998 9.99996V11.3479" stroke="#175DA8" stroke-width="1.31458" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9.99975 13.3699C9.79979 13.3699 9.60433 13.4292 9.43808 13.5403C9.27182 13.6513 9.14224 13.8092 9.06573 13.994C8.98921 14.1787 8.96919 14.382 9.0082 14.5781C9.0472 14.7742 9.14349 14.9543 9.28488 15.0957C9.42627 15.2371 9.6064 15.3334 9.80251 15.3724C9.99862 15.4114 10.2019 15.3914 10.3866 15.3149C10.5714 15.2383 10.7293 15.1088 10.8403 14.9425C10.9514 14.7763 11.0107 14.5808 11.0107 14.3808C11.0072 14.1138 10.8996 13.8587 10.7108 13.6698C10.5219 13.481 10.2668 13.3734 9.99975 13.3699Z" fill="#175DA8"/>
  </g>
  <defs>
    <clipPath id="clip0_1816_1884">
      <rect width="20" height="20" fill="white"/>
    </clipPath>
  </defs>
</svg>FAQ
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <path d="M9.99996 17.5C9.76385 17.5 9.56579 17.42 9.40579 17.26C9.24579 17.1 9.16607 16.9022 9.16663 16.6667C9.16663 16.4306 9.24663 16.2325 9.40663 16.0725C9.56663 15.9125 9.76441 15.8328 9.99996 15.8333H15.8333V9.91667C15.8333 9.11111 15.6805 8.35417 15.375 7.64583C15.0694 6.9375 14.6527 6.31945 14.125 5.79167C13.5972 5.26389 12.9791 4.84722 12.2708 4.54167C11.5625 4.23611 10.8055 4.08333 9.99996 4.08333C9.19441 4.08333 8.43746 4.23611 7.72913 4.54167C7.02079 4.84722 6.40274 5.26389 5.87496 5.79167C5.34718 6.31945 4.93052 6.9375 4.62496 7.64583C4.3194 8.35417 4.16663 9.11111 4.16663 9.91667V14.1667C4.16663 14.4028 4.08691 14.6008 3.92746 14.7608C3.76802 14.9208 3.56996 15.0006 3.33329 15C2.87496 15 2.48246 14.8367 2.15579 14.51C1.82913 14.1833 1.66607 13.7911 1.66663 13.3333V11.6667C1.66663 11.3472 1.74302 11.0661 1.89579 10.8233C2.04857 10.5806 2.24996 10.3825 2.49996 10.2292L2.56246 9.125C2.68746 8.11111 2.97579 7.19445 3.42746 6.375C3.87913 5.55556 4.44163 4.86111 5.11496 4.29167C5.78829 3.72222 6.54524 3.28111 7.38579 2.96833C8.22635 2.65556 9.09774 2.49945 9.99996 2.5C10.9166 2.5 11.7952 2.65639 12.6358 2.96917C13.4764 3.28195 14.2297 3.72639 14.8958 4.3025C15.5625 4.87861 16.1216 5.57306 16.5733 6.38583C17.025 7.19861 17.313 8.10472 17.4375 9.10417L17.5 10.1875C17.75 10.3125 17.9513 10.4967 18.1041 10.74C18.2569 10.9833 18.3333 11.2506 18.3333 11.5417V13.4583C18.3333 13.7639 18.2569 14.0347 18.1041 14.2708C17.9513 14.5069 17.75 14.6875 17.5 14.8125V15.8333C17.5 16.2917 17.3366 16.6842 17.01 17.0108C16.6833 17.3375 16.2911 17.5006 15.8333 17.5H9.99996Z" fill="#175DA8"/>
              </svg>Support
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="logout.php">
             <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
             <path d="M9.16663 5.83333L7.99996 7L10.1666 9.16667H1.66663V10.8333H10.1666L7.99996 13L9.16663 14.1667L13.3333 10L9.16663 5.83333ZM16.6666 15.8333H9.99996V17.5H16.6666C17.5833 17.5 18.3333 16.75 18.3333 15.8333V4.16667C18.3333 3.25 17.5833 2.5 16.6666 2.5H9.99996V4.16667H16.6666V15.8333Z" fill="#FF3333"/>
            </svg>Log out
            </a>
          </li>
        </ul>
      </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Mobile Navbar: Visible only on screens smaller than lg -->
<nav class="navbar navbar-light bg-white header d-lg-none ">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Left Group: Offcanvas Toggle and Logo -->
    <div class="d-flex align-items-center">
      <!-- Offcanvas Toggle Button -->
      <button class="btn hamberg me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileOffcanvas" aria-controls="mobileOffcanvas">
        <i class="fas fa-bars"></i>
      </button>
      <!-- Logo -->
      <a class="navbar-brand" href="#">
        <img src="image/logo.png" alt="Finploy" height="50">
      </a>
    </div>
    
    <!-- Right Group: Cart and Profile Icons -->
<div class="d-flex align-items-center">
      <!-- Cart SVG Icon -->
    <a class="nav-link position-relative" href="cart_page.php">
     <svg width="20" height="20" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
         <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1106 6.66675H9.33333V5.33341C9.33333 4.62617 9.05238 3.94789 8.55229 3.4478C8.05219 2.9477 7.37391 2.66675 6.66667 2.66675H4V5.33341H6.66667V20.0001C6.66667 20.7073 6.94762 21.3856 7.44772 21.8857C7.94781 22.3858 8.62609 22.6667 9.33333 22.6667H25.3333C25.3333 21.9595 25.0524 21.2812 24.5523 20.7811C24.0522 20.281 23.3739 20.0001 22.6667 20.0001H9.33333V17.3334H23.1947C23.8013 17.3333 24.3897 17.1264 24.8629 16.7468C25.1699 16.5005 25.4164 16.1912 25.5877 15.8425C25.0745 15.9458 24.5436 16.0001 24 16.0001C19.5817 16.0001 16 12.4183 16 8.00006C16 7.54578 16.0379 7.10035 16.1106 6.66675ZM9.33333 29.3334C10.8 29.3334 12 28.1334 12 26.6667C12 25.2001 10.8 24.0001 9.33333 24.0001C7.86667 24.0001 6.68 25.2001 6.68 26.6667C6.68 28.1334 7.86667 29.3334 9.33333 29.3334ZM20.0133 26.6667C20.0133 25.2001 21.2 24.0001 22.6667 24.0001C24.1333 24.0001 25.3333 25.2001 25.3333 26.6667C25.3333 28.1334 24.1333 29.3334 22.6667 29.3334C21.2 29.3334 20.0133 28.1334 20.0133 26.6667Z" fill="#175DA8"/>
    </svg>
    <span id="cart-count-mobile" class="badge position-absolute top-0 start-100 translate-middle">0</span>
   </a>
      
      <!-- Notification (Bell) SVG Icon -->
<!-- In your mobile navbar -->
<a class="nav-link position-relative" 
   href="#" 
   role="button" 
   data-bs-toggle="dropdown" 
   aria-expanded="false" 
   onclick="toggleNotifications('mobile', event); return false;">
 
  <!-- Your SVG icon remains the same -->
  <svg width="40" height="40" viewBox="0 0 52 53" fill="none" xmlns="http://www.w3.org/2000/svg">
    <g filter="url(#filter0_d_2644_948)">
      <rect x="6" y="6" width="40" height="40" rx="8.66071" fill="white"/>
      <path d="M34.3084 35.8887L34.0099 36.9909L13.9622 31.6746L14.2606 30.5723L17.085 28.9584L18.8756 22.3447C19.8008 18.9277 22.8764 16.518 26.4469 16.4252L26.5335 16.1055C26.6918 15.5209 27.0783 15.0224 27.6079 14.7197C28.1376 14.417 28.7671 14.335 29.3579 14.4917C29.9486 14.6484 30.4523 15.0309 30.7581 15.5551C31.0639 16.0793 31.1468 16.7023 30.9885 17.287L30.902 17.6066C33.9472 19.4538 35.3934 23.0626 34.4683 26.4797L32.6777 33.0934L34.3084 35.8887ZM25.9152 36.0257C25.7569 36.6104 25.3704 37.1089 24.8407 37.4116C24.311 37.7142 23.6816 37.7963 23.0908 37.6396C22.5 37.4829 21.9963 37.1004 21.6905 36.5762C21.3847 36.052 21.3018 35.429 21.4601 34.8443" fill="#175DA8"/>
    </g>
    <defs>
      <filter id="filter0_d_2644_948" x="0.226191" y="0.947917" width="51.5476" height="51.5476" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
        <feOffset dy="0.721726"/>
        <feGaussianBlur stdDeviation="2.8869"/>
        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.24 0"/>
        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2644_948"/>
        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2644_948" result="shape"/>
      </filter>
    </defs>
  </svg>
  
  <?php if ($notificationCount > 0): ?>
    <span id="notification-count-mobile" class="mobile-badge bg-danger position-absolute top-0 start-100 translate-middle">
      <?php echo $notificationCount; ?>
    </span>
  <?php endif; ?>
  
  <!-- Create a separate mobile notification container -->
  <div id="notifications-mobile" class="notification-container">
    <div class="notification-header">
      <h3>Notifications (<?php echo $notificationCount; ?>)</h3>
    </div>
    <div class="notification-body">
      <?php if (count($expiredNotifications) > 0): ?>
        <?php foreach ($expiredNotifications as $notification): ?>
          <div class="notification-item">
            <?php echo $notification['message']; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="notification-item">No notifications</div>
      <?php endif; ?>
    </div>
    <div class="notification-footer">
      <a href="notifications_dashboard.php" class="see-all">See All Notifications</a>
    </div>
  </div>
</a>
      
      <!-- Profile Icon -->
     <a class="nav-link profile" href="#" id="profileToggle">
  <span class="profile-desk"><?php echo strtoupper(substr($employer_name, 0, 1)); ?></span>
</a>

<div class="dropdown-menu mobile-dropdown custom-dropdown " id="profileDropdown">
  <a href="#" class="dropdown-item">
      <svg width="53" height="25" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
             <path fill-rule="evenodd" clip-rule="evenodd" d="M7.935 9.8432C5.07807 10.7252 3 13.3885 3 16.5333C3 16.6571 3.04917 16.7758 3.13668 16.8633C3.2242 16.9508 3.3429 17 3.46667 17C3.59043 17 3.70913 16.9508 3.79665 16.8633C3.88417 16.7758 3.93333 16.6571 3.93333 16.5333C3.93333 13.185 6.65167 10.4667 10 10.4667C13.3483 10.4667 16.0667 13.185 16.0667 16.5333C16.0667 16.6571 16.1158 16.7758 16.2034 16.8633C16.2909 16.9508 16.4096 17 16.5333 17C16.6571 17 16.7758 16.9508 16.8633 16.8633C16.9508 16.7758 17 16.6571 17 16.5333C17 13.3885 14.9219 10.7252 12.065 9.8432C12.5782 9.5028 12.9991 9.04066 13.2902 8.49802C13.5813 7.95539 13.7335 7.34912 13.7333 6.73333C13.7333 4.673 12.0603 3 10 3C7.93967 3 6.26667 4.673 6.26667 6.73333C6.26648 7.34912 6.41873 7.95539 6.70984 8.49802C7.00094 9.04066 7.42185 9.5028 7.935 9.8432ZM10 3.93333C11.5451 3.93333 12.8 5.1882 12.8 6.73333C12.8 8.27847 11.5451 9.53333 10 9.53333C8.45487 9.53333 7.2 8.27847 7.2 6.73333C7.2 5.1882 8.45487 3.93333 10 3.93333Z" fill="#175DA8" stroke="#175DA8" stroke-width="0.5"/>
             </svg> My Profile
  </a>
  <a href="accessed_candidates.php" class="dropdown-item">
 <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M5.51343 7.38807L8.65021 4.25011V6.24987V6.34987H8.75021H10.0002H10.1002V6.24987V4.25004L13.2382 7.38808L13.309 7.45884L13.3797 7.38803L14.2635 6.50303L14.3341 6.43232L14.2634 6.36166L9.88842 1.98666L9.85913 1.95737H9.81771H8.93396H8.89254L8.86325 1.98666L4.48825 6.36166L4.41759 6.43232L4.4882 6.50303L5.37195 7.38803L5.44267 7.45886L5.51343 7.38807Z" fill="#175DA8" stroke="#175DA8" stroke-width="0.2"/>
  <path d="M1.875 11.15H1.83358L1.80429 11.1793L1.17929 11.8043L1.15 11.8336V11.875V18.125V18.1664L1.17929 18.1957L1.80429 18.8207L1.83358 18.85H1.875H16.875H16.9164L16.9457 18.8207L17.5707 18.1957L17.6 18.1664V18.125V11.875V11.8336L17.5707 11.8043L16.9457 11.1793L16.9164 11.15H16.875H12.4375H12.3558L12.3395 11.2301C12.2006 11.9139 11.8296 12.5287 11.2894 12.9703C10.7491 13.4119 10.0728 13.6531 9.375 13.6531C8.67722 13.6531 8.0009 13.4119 7.46062 12.9703C6.92035 12.5287 6.54936 11.9139 6.4105 11.2301L6.39424 11.15H6.3125H1.875ZM8.75 7.4H8.65V7.5V8.75V8.85H8.75H10H10.1V8.75V7.5V7.4H10H8.75ZM8.75 9.9H8.65V10V11.25V11.35H8.75H10H10.1V11.25V10V9.9H10H8.75ZM11.7679 14.4084C12.4659 13.9667 13.0277 13.3407 13.3915 12.6H16.15V17.4H2.6V12.6H5.35847C5.72231 13.3407 6.28409 13.9667 6.9821 14.4084C7.69801 14.8614 8.5278 15.1019 9.375 15.1019C10.2222 15.1019 11.052 14.8614 11.7679 14.4084Z" fill="#175DA8" stroke="#175DA8" stroke-width="0.2"/>
</svg> Accessed Candidates
  </a>
  <a href="#" class="dropdown-item">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M11.876 1.81785C11.3327 1.6742 10.7777 1.9971 10.634 2.54044L10.4782 3.12982C8.11719 2.99165 5.90455 4.52828 5.27636 6.90423L5.0702 7.68393C4.70173 9.07758 3.868 10.3055 2.71196 11.1625L2.1028 11.6156C1.86632 11.7895 1.7522 12.0845 1.80763 12.3718C1.86305 12.659 2.07789 12.8899 2.3603 12.9646L14.6391 16.211C14.9215 16.2857 15.2224 16.1912 15.4125 15.9689C15.6027 15.7466 15.6493 15.4337 15.5297 15.1656L15.2232 14.4737C14.6426 13.1543 14.5248 11.6748 14.8932 10.2811L15.0994 9.50142C15.7276 7.12547 14.5636 4.69606 12.4428 3.64926L12.5986 3.05988C12.7423 2.51654 12.4194 1.96151 11.876 1.81785ZM11.0969 4.76476C12.997 5.26716 14.1283 7.2117 13.6259 9.11184L13.4198 9.89155C13.031 11.3619 13.0787 12.9083 13.546 14.3456L4.23255 11.8831C5.34918 10.8646 6.15489 9.5439 6.54366 8.07351L6.74981 7.29381C7.25221 5.39366 9.19675 4.26237 11.0969 4.76476ZM10.2046 16.0895L6.27537 15.0507C6.1374 15.5725 6.21077 16.1273 6.48174 16.593C6.75271 17.0588 7.19869 17.3967 7.72054 17.5347C8.24239 17.6727 8.79713 17.5993 9.26289 17.3283C9.72865 17.0574 10.0666 16.6114 10.2046 16.0895Z" fill="#175DA8"/>
</svg> Notification
  </a>
  <a href="#" class="dropdown-item">
 <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <g clip-path="url(#clip0_1816_1884)">
    <path d="M10.0001 18.7619C14.8391 18.7619 18.7619 14.8391 18.7619 10.0001C18.7619 5.16107 14.8391 1.23828 10.0001 1.23828C5.16107 1.23828 1.23828 5.16107 1.23828 10.0001C1.23828 14.8391 5.16107 18.7619 10.0001 18.7619Z" stroke="#175DA8" stroke-width="1.31458" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M7.97803 7.97801C7.97803 7.5781 8.09661 7.18718 8.31879 6.85467C8.54096 6.52216 8.85675 6.263 9.22621 6.10997C9.59567 5.95693 10.0022 5.91689 10.3944 5.99491C10.7867 6.07292 11.1469 6.2655 11.4297 6.54827C11.7125 6.83105 11.9051 7.19132 11.9831 7.58354C12.0611 7.97576 12.0211 8.38231 11.868 8.75177C11.715 9.12124 11.4558 9.43702 11.1233 9.6592C10.7908 9.88137 10.3999 9.99996 9.99998 9.99996V11.3479" stroke="#175DA8" stroke-width="1.31458" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M9.99975 13.3699C9.79979 13.3699 9.60433 13.4292 9.43808 13.5403C9.27182 13.6513 9.14224 13.8092 9.06573 13.994C8.98921 14.1787 8.96919 14.382 9.0082 14.5781C9.0472 14.7742 9.14349 14.9543 9.28488 15.0957C9.42627 15.2371 9.6064 15.3334 9.80251 15.3724C9.99862 15.4114 10.2019 15.3914 10.3866 15.3149C10.5714 15.2383 10.7293 15.1088 10.8403 14.9425C10.9514 14.7763 11.0107 14.5808 11.0107 14.3808C11.0072 14.1138 10.8996 13.8587 10.7108 13.6698C10.5219 13.481 10.2668 13.3734 9.99975 13.3699Z" fill="#175DA8"/>
  </g>
  <defs>
    <clipPath id="clip0_1816_1884">
      <rect width="20" height="20" fill="white"/>
    </clipPath>
  </defs>
</svg> FAQ
  </a>
  <a href="#" class="dropdown-item">
   <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M9.99996 17.5C9.76385 17.5 9.56579 17.42 9.40579 17.26C9.24579 17.1 9.16607 16.9022 9.16663 16.6667C9.16663 16.4306 9.24663 16.2325 9.40663 16.0725C9.56663 15.9125 9.76441 15.8328 9.99996 15.8333H15.8333V9.91667C15.8333 9.11111 15.6805 8.35417 15.375 7.64583C15.0694 6.9375 14.6527 6.31945 14.125 5.79167C13.5972 5.26389 12.9791 4.84722 12.2708 4.54167C11.5625 4.23611 10.8055 4.08333 9.99996 4.08333C9.19441 4.08333 8.43746 4.23611 7.72913 4.54167C7.02079 4.84722 6.40274 5.26389 5.87496 5.79167C5.34718 6.31945 4.93052 6.9375 4.62496 7.64583C4.3194 8.35417 4.16663 9.11111 4.16663 9.91667V14.1667C4.16663 14.4028 4.08691 14.6008 3.92746 14.7608C3.76802 14.9208 3.56996 15.0006 3.33329 15C2.87496 15 2.48246 14.8367 2.15579 14.51C1.82913 14.1833 1.66607 13.7911 1.66663 13.3333V11.6667C1.66663 11.3472 1.74302 11.0661 1.89579 10.8233C2.04857 10.5806 2.24996 10.3825 2.49996 10.2292L2.56246 9.125C2.68746 8.11111 2.97579 7.19445 3.42746 6.375C3.87913 5.55556 4.44163 4.86111 5.11496 4.29167C5.78829 3.72222 6.54524 3.28111 7.38579 2.96833C8.22635 2.65556 9.09774 2.49945 9.99996 2.5C10.9166 2.5 11.7952 2.65639 12.6358 2.96917C13.4764 3.28195 14.2297 3.72639 14.8958 4.3025C15.5625 4.87861 16.1216 5.57306 16.5733 6.38583C17.025 7.19861 17.313 8.10472 17.4375 9.10417L17.5 10.1875C17.75 10.3125 17.9513 10.4967 18.1041 10.74C18.2569 10.9833 18.3333 11.2506 18.3333 11.5417V13.4583C18.3333 13.7639 18.2569 14.0347 18.1041 14.2708C17.9513 14.5069 17.75 14.6875 17.5 14.8125V15.8333C17.5 16.2917 17.3366 16.6842 17.01 17.0108C16.6833 17.3375 16.2911 17.5006 15.8333 17.5H9.99996Z" fill="#175DA8"/>
</svg> Support
  </a>
  <a  href="logout.php" class="dropdown-item">
   <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M9.16663 5.83333L7.99996 7L10.1666 9.16667H1.66663V10.8333H10.1666L7.99996 13L9.16663 14.1667L13.3333 10L9.16663 5.83333ZM16.6666 15.8333H9.99996V17.5H16.6666C17.5833 17.5 18.3333 16.75 18.3333 15.8333V4.16667C18.3333 3.25 17.5833 2.5 16.6666 2.5H9.99996V4.16667H16.6666V15.8333Z" fill="#FF3333"/>
</svg>Log out
  </a>
</div>
    </div>
  </div>
</nav>

<!-- Offcanvas Mobile Menu -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileOffcanvas" aria-labelledby="mobileOffcanvasLabel">
  <div class="offcanvas-header">
    <a class="navbar-brand-drop" href="#">
      <img src="image/logo.png" alt="Finploy" height="50">
    </a>
    <button 
    type="button" 
    class="btn-close text-reset offcanvas-close-btn" 
    data-bs-dismiss="offcanvas" 
    aria-label="Close">
  </button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav flex-column">
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="employer.php">
          <i class="fa fa-home me-3"></i>
          <span>Home</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="cart_page.php">
          <i class="fa fa-shopping-cart me-3"></i>
          <span>Cart</span>
        </a>
      </li>
      
      
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="#">
          <sapn class="me-3">
              <svg width="24" height="24" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M8.35205 6.58627V7.67728L7.05053 7.78331C6.60339 7.81929 6.18123 8.00416 5.85155 8.30836C5.52187 8.61255 5.3037 9.0185 5.23193 9.46131C5.20017 9.65953 5.17097 9.85801 5.14434 10.0568C5.13836 10.1048 5.14769 10.1536 5.171 10.1961C5.19431 10.2385 5.23041 10.2726 5.27418 10.2934L5.33334 10.3211C9.50452 12.2956 14.4993 12.2956 18.6698 10.3211L18.7289 10.2934C18.7725 10.2725 18.8085 10.2384 18.8316 10.1959C18.8548 10.1534 18.864 10.1048 18.858 10.0568C18.8319 9.85786 18.803 9.65936 18.7712 9.46131C18.6994 9.0185 18.4812 8.61255 18.1515 8.30836C17.8219 8.00416 17.3997 7.81929 16.9526 7.78331L15.651 7.67805V6.58704C15.6511 6.265 15.5356 5.95364 15.3255 5.70957C15.1154 5.46549 14.8247 5.30494 14.5063 5.25709L13.5689 5.11649C12.5298 4.96117 11.4733 4.96117 10.4342 5.11649L9.49684 5.25709C9.17851 5.30492 8.8879 5.46536 8.67783 5.70927C8.46775 5.95318 8.35216 6.26437 8.35205 6.58627ZM13.3976 6.2559C12.472 6.11766 11.5311 6.11766 10.6055 6.2559L9.66818 6.3965C9.6227 6.40331 9.58117 6.4262 9.55114 6.46102C9.52111 6.49585 9.50457 6.54029 9.50452 6.58627V7.59661C11.1679 7.50161 12.8352 7.50161 14.4986 7.59661V6.58627C14.4985 6.54029 14.482 6.49585 14.452 6.46102C14.4219 6.4262 14.3804 6.40331 14.3349 6.3965L13.3976 6.2559Z" fill="#175DA8"/>
  <path d="M19.0068 11.6638C19.0052 11.6389 18.9977 11.6148 18.9848 11.5936C18.9719 11.5723 18.954 11.5544 18.9327 11.5416C18.9114 11.5287 18.8873 11.5213 18.8624 11.5198C18.8376 11.5183 18.8128 11.5229 18.7901 11.5332C14.5098 13.4286 9.49271 13.4286 5.21243 11.5332C5.18975 11.5229 5.16492 11.5183 5.14008 11.5198C5.11524 11.5213 5.09112 11.5287 5.06981 11.5416C5.04849 11.5544 5.03062 11.5723 5.01772 11.5936C5.00482 11.6148 4.99729 11.6389 4.99577 11.6638C4.91795 13.1347 4.99702 14.6097 5.23164 16.0639C5.30325 16.5069 5.52135 16.913 5.85105 17.2173C6.18075 17.5217 6.60299 17.7067 7.05024 17.7427L8.48852 17.8579C10.8265 18.0469 13.1752 18.0469 15.514 17.8579L16.9523 17.7427C17.3995 17.7067 17.8218 17.5217 18.1515 17.2173C18.4812 16.913 18.6993 16.5069 18.7709 16.0639C19.006 14.608 19.0859 13.1328 19.0068 11.6646" fill="#175DA8"/>
</svg>
          </sapn>
          <span>Accessed Candidates</span>
        </a>
      </li>
      <li class="nav-item history ">
        <a class="nav-link d-flex align-items-center" href="#">
          <i class="fa fa-history me-3"></i>
          <span>History</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="#">
          <i class="fa fa-user me-3"></i>
          <span>My Account</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="#">
          <i class="fa fa-headphones me-3"></i>
          <span>Support</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="#">
         <sapn class="me-3">
             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 6px;"> 
  <g clip-path="url(#clip0_1754_5789)">
    <path d="M12.0005 22.5146C17.8073 22.5146 22.5146 17.8073 22.5146 12.0005C22.5146 6.19367 17.8073 1.48633 12.0005 1.48633C6.19367 1.48633 1.48633 6.19367 1.48633 12.0005C1.48633 17.8073 6.19367 22.5146 12.0005 22.5146Z" stroke="#175DA8" stroke-width="1.57749" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M9.57422 9.5738C9.57422 9.09392 9.71652 8.62481 9.98313 8.2258C10.2497 7.82679 10.6287 7.5158 11.072 7.33216C11.5154 7.14851 12.0033 7.10046 12.4739 7.19408C12.9446 7.2877 13.3769 7.51879 13.7162 7.85812C14.0556 8.19745 14.2867 8.62978 14.3803 9.10045C14.4739 9.57111 14.4258 10.059 14.2422 10.5023C14.0586 10.9457 13.7476 11.3246 13.3486 11.5912C12.9496 11.8578 12.4804 12.0001 12.0006 12.0001V13.6177" stroke="#175DA8" stroke-width="1.57749" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M11.9983 16.0439C11.7584 16.0439 11.5238 16.1151 11.3243 16.2484C11.1248 16.3817 10.9693 16.5712 10.8775 16.7929C10.7857 17.0145 10.7617 17.2585 10.8085 17.4938C10.8553 17.7291 10.9708 17.9453 11.1405 18.115C11.3102 18.2846 11.5263 18.4002 11.7616 18.447C11.997 18.4938 12.2409 18.4698 12.4626 18.3779C12.6843 18.2861 12.8737 18.1306 13.007 17.9311C13.1403 17.7316 13.2115 17.4971 13.2115 17.2571C13.2073 16.9367 13.0781 16.6305 12.8515 16.4039C12.6249 16.1773 12.3188 16.0481 11.9983 16.0439Z" fill="#175DA8"/>
  </g>
  <defs>
    <clipPath id="clip0_1754_5789">
      <rect width="24" height="24" fill="white"/>
    </clipPath>
  </defs>
</svg>
         </sapn>
          <span>FAQ</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="#">
          <sapn class="me-3">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M11 7L9.6 8.4L12.2 11H2V13H12.2L9.6 15.6L11 17L16 12L11 7ZM20 19H12V21H20C21.1 21 22 20.1 22 19V5C22 3.9 21.1 3 20 3H12V5H20V19Z" fill="#FF3333"/>
</svg>
          </sapn>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </div>
</div>
</body>
</html>

