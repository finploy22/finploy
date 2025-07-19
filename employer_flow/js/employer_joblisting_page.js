  // Global variables
        let currentPage = 1;
        let currentFilter = 'active';
        let currentSearch = '';
        let currentLocation = '';
        let perPage = 20;
        
        // Initialize when document is ready
        $(document).ready(function() {
            // Load jobs
            loadJobs();
            
            // Event listeners
           // Replace your current searchInput event listener with this improved version
  $('#searchInput').on('input', function() {
        // Get the current search value
        currentSearch = $(this).val();
        
        // Add a small delay to prevent searching on every keystroke
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(function() {
            // console.log("Searching for:", currentSearch);
            currentPage = 1;
            loadJobs();
        }, 500); // 500ms delay
    })
    // Close the location filter popup when the "x" is clicked
$('#locationFilterClose').on('click', function() {
    $('#locationFilterPopup').hide();
});

        
         $('.all-filter').on('click', function(e) {
        e.preventDefault();
        // Toggle location filter popup
        $('#locationFilterPopup').toggle();
        
        // If popup is visible, populate the dropdown
        if ($('#locationFilterPopup').is(':visible')) {
            populateLocationDropdown();
        }
    });
    
    // Event listener for location select
    $('#locationSelect').on('change', function() {
        currentLocation = $(this).val();
        currentPage = 1;
        loadJobs();
        
        // Hide the popup after selection
        $('#locationFilterPopup').hide();
    });    
            $('.job-filter').on('click', function() {
                $('.job-filter').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                currentPage = 1;
                loadJobs();
            });
            
            $('#perPageSelect').on('change', function() {
                perPage = $(this).val();
                currentPage = 1;
                loadJobs();
            });
            
            // Delegated event handler for pagination
            $(document).on('click', '.page-link:not(.disabled)', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if(page) {
                    currentPage = page;
                    loadJobs();
                }
            });
        });
        
        function populateLocationDropdown() {
    // Clear existing options except the first one
    $('#locationSelect').find('option:not(:first)').remove();
    
    // Fetch available locations
    $.ajax({
        url: 'employer_joblisting_location.php', // Create this endpoint to fetch unique locations
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.locations && response.locations.length > 0) {
                // Add locations to dropdown
                response.locations.forEach(location => {
                    $('#locationSelect').append(`<option value="${location}">${location}</option>`);
                });
                
                // If there's a currently selected location, set it
                if (currentLocation) {
                    $('#locationSelect').val(currentLocation);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching locations:', error);
        }
    });
}
        // Function to load jobs via AJAX
        function loadJobs() {
            $('#jobsContainer').html(`
                <div class="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading jobs...</p>
                </div>
            `);
            
            $.ajax({
                url: 'employer_joblisting_endpoint.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    search: currentSearch,
                    filter: currentFilter,
                    location: currentLocation,
                    page: currentPage,
                    per_page: perPage
                },
                success: function(response) {
                    // console.log('Response:', response);
                    displayJobs(response.jobs);
                    updatePagination(response.pagination);
                    updateCounters(response.pagination.total_records);
                },
                error: function(xhr, status, error) {
                    $('#jobsContainer').html(`
                        <div class="alert alert-danger">
                            Error loading jobs: ${error}
                        </div>
                    `);
                }
            });
        }
        // Add this to your $(document).ready function
$(document).ready(function() {
    // ... your existing code ...
    
    // Delegated event handler for the action dropdown toggle
    $(document).on('click', '.icon-danger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Close all other open dropdowns first
        $('.action-dropdown').not($(this).siblings('.action-dropdown')).hide();
        
        // Toggle this dropdown
        $(this).siblings('.action-dropdown').toggle();
    });
    
    // Close dropdown when clicking elsewhere on the page
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.action-dropdown').hide();
        }
    });
    
    // Handle delete button click
    $(document).on('click', '.delete-job', function(e) {
  e.preventDefault();
  const jobId = $(this).data('job-id');

  if (!confirm('Are you sure you want to delete this job?')) return;

  $.ajax({
    url: 'delete_job.php',
    type: 'POST',
    data: { job_id: jobId },
    dataType: 'json',                // ← tell jQuery "expect JSON"
    success: function(result) {
      if (result.success) {
        loadJobs();
      } else {
        alert('Error: ' + result.message);
      }
    },
    error: function(xhr, status, error) {
      alert('AJAX error: ' + error);
    }
  });
});

});
        // Function to display jobs
        function displayJobs(jobs) {
            if(jobs.length === 0) {
                $('#jobsContainer').html('<div class="alert alert-info">No jobs found matching your criteria.</div>');
                return;
            }
            
            let html = '';
            
            jobs.forEach(job => {
                // Format date
                const created = new Date(job.created);
                const formattedDate = formatDate(created);
                
             // Normalize the status: remove extra spaces and convert to lowercase
        const status = job.job_status.trim().toLowerCase();
        
        // Set the class based on the normalized status
        let statusClass = 'badge-inactive'; // Default class
        if (status === 'active') {
            statusClass = 'badge-active'; // Class for 'active' or 'Active'
        } else if (status === 'expired') {
            statusClass = 'badge-expired'; // Example for another status
        }
            function capitalizeFirstLetter(str) {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1);
}
    
                html += `
            <div class="job-card-emp-job ">
    <div class="job-body row g-1">
        <div class="col-12 col-sm-5">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2 active-div ">
  <p class="mb-0 job-list-titel-text flex-shrink-1 text-truncate">
    ${capitalizeFirstLetter(job.jobrole)}
  </p>
  <span class="main-custom-badge ${statusClass}">
    ${capitalizeFirstLetter(job.job_status)}
  </span>
</div>


                <div class="job-info d-flex flex-wrap">
    <div class="info-item d-flex align-items-center me-4 mb-2">
        <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7.50735 15.2562C7.50735 15.2562 2.66602 11.1788 2.66602 7.16683C2.66602 5.75234 3.22792 4.39579 4.22811 3.39559C5.22831 2.3954 6.58486 1.8335 7.99935 1.8335C9.41384 1.8335 10.7704 2.3954 11.7706 3.39559C12.7708 4.39579 13.3327 5.75234 13.3327 7.16683C13.3327 11.1788 8.49135 15.2562 8.49135 15.2562C8.22202 15.5042 7.77868 15.5015 7.50735 15.2562ZM7.99935 9.50016C8.30577 9.50016 8.60918 9.43981 8.89228 9.32255C9.17537 9.20529 9.43259 9.03342 9.64926 8.81675C9.86593 8.60008 10.0378 8.34285 10.1551 8.05976C10.2723 7.77666 10.3327 7.47325 10.3327 7.16683C10.3327 6.86041 10.2723 6.55699 10.1551 6.2739C10.0378 5.99081 9.86593 5.73358 9.64926 5.51691C9.43259 5.30024 9.17537 5.12837 8.89228 5.01111C8.60918 4.89385 8.30577 4.8335 7.99935 4.8335C7.38051 4.8335 6.78702 5.07933 6.34943 5.51691C5.91185 5.9545 5.66602 6.54799 5.66602 7.16683C5.66602 7.78567 5.91185 8.37916 6.34943 8.81675C6.78702 9.25433 7.38051 9.50016 7.99935 9.50016Z"
                fill="#175DA8" />
        </svg>
        <span class="location-joblist ml-1">${capitalizeFirstLetter(job.location)}</span>
    </div>
    <div class="info-item d-flex align-items-center me-4 mb-2">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M12.1631 2.72676C13.1686 3.30733 14.005 4.14044 14.5896 5.14362C15.1742 6.14681 15.4867 7.28527 15.496 8.44632C15.5054 9.60736 15.2114 10.7507 14.6431 11.7632C14.0748 12.7757 13.2519 13.6222 12.2559 14.219C11.2599 14.8157 10.1253 15.142 8.96448 15.1655C7.80364 15.189 6.65679 14.9089 5.63746 14.3529C4.61813 13.797 3.76167 12.9844 3.15286 11.9958C2.54406 11.0071 2.20403 9.87656 2.16642 8.71609L2.16309 8.50009L2.16642 8.28409C2.20375 7.13275 2.53878 6.01073 3.13885 5.02742C3.73891 4.04411 4.58352 3.23306 5.59035 2.67335C6.59717 2.11363 7.73185 1.82436 8.88376 1.83372C10.0357 1.84308 11.1655 2.15076 12.1631 2.72676ZM8.82975 4.50009C8.66646 4.50011 8.50886 4.56006 8.38684 4.66857C8.26481 4.77707 8.18686 4.92659 8.16775 5.08876L8.16309 5.16676V8.50009L8.16909 8.58742C8.18429 8.70309 8.22957 8.81274 8.30042 8.90542L8.35842 8.97209L10.3584 10.9721L10.4211 11.0268C10.538 11.1175 10.6818 11.1667 10.8298 11.1667C10.9777 11.1667 11.1215 11.1175 11.2384 11.0268L11.3011 10.9714L11.3564 10.9088C11.4471 10.7918 11.4964 10.6481 11.4964 10.5001C11.4964 10.3521 11.4471 10.2083 11.3564 10.0914L11.3011 10.0288L9.49642 8.22342V5.16676L9.49175 5.08876C9.47265 4.92659 9.39469 4.77707 9.27267 4.66857C9.15064 4.56006 8.99304 4.50011 8.82975 4.50009Z"
                fill="#175DA8" />
        </svg>
        <span class="date-badge-text mr-1 ml-1">Posted on</span> <span class="date-badge-text">:</span><span
            class="date-badge ml-1"> ${capitalizeFirstLetter(formattedDate)}</span>
    </div>
    <div class="info-item d-flex align-items-center mb-2">
        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M8.48828 9.1665C10.0856 9.1665 11.5383 9.62917 12.6069 10.2805C13.1403 10.6072 13.5963 10.9905 13.9256 11.4072C14.2496 11.8178 14.4883 12.3085 14.4883 12.8332C14.4883 13.3965 14.2143 13.8405 13.8196 14.1572C13.4463 14.4572 12.9536 14.6558 12.4303 14.7945C11.3783 15.0725 9.97428 15.1665 8.48828 15.1665C7.00228 15.1665 5.59828 15.0732 4.54628 14.7945C4.02295 14.6558 3.53028 14.4572 3.15695 14.1572C2.76161 13.8398 2.48828 13.3965 2.48828 12.8332C2.48828 12.3085 2.72695 11.8178 3.05095 11.4072C3.38028 10.9905 3.83561 10.6072 4.36961 10.2805C5.43828 9.62917 6.89161 9.1665 8.48828 9.1665Z"
                fill="#175DA8" />
            <path
                d="M8.48876 1.8335C11.0548 1.8335 12.6588 4.6115 11.3754 6.8335C11.0829 7.34021 10.6621 7.761 10.1554 8.05356C9.64867 8.34612 9.07387 8.50015 8.48876 8.50016C5.92276 8.50016 4.31876 5.72216 5.6021 3.50016C5.89465 2.99344 6.31543 2.57266 6.82214 2.2801C7.32886 1.98754 7.90366 1.83351 8.48876 1.8335Z"
                fill="#175DA8" />
        </svg>
        <span class="bage-contact-name-joblist ml-1">${capitalizeFirstLetter(job.contact_person_name)}</span>
    </div>
    <div class="info-item d-flex align-items-center mb-2">
        <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M3 2C2.86739 2 2.74021 2.05268 2.64645 2.14645C2.55268 2.24021 2.5 2.36739 2.5 2.5C2.5 2.63261 2.55268 2.75979 2.64645 2.85355C2.74021 2.94732 2.86739 3 3 3V14H2.5C2.36739 14 2.24021 14.0527 2.14645 14.1464C2.05268 14.2402 2 14.3674 2 14.5C2 14.6326 2.05268 14.7598 2.14645 14.8536C2.24021 14.9473 2.36739 15 2.5 15H13.5C13.6326 15 13.7598 14.9473 13.8536 14.8536C13.9473 14.7598 14 14.6326 14 14.5C14 14.3674 13.9473 14.2402 13.8536 14.1464C13.7598 14.0527 13.6326 14 13.5 14H13V3C13.1326 3 13.2598 2.94732 13.3536 2.85355C13.4473 2.75979 13.5 2.63261 13.5 2.5C13.5 2.36739 13.4473 2.24021 13.3536 2.14645C13.2598 2.05268 13.1326 2 13 2H3ZM6 4.5C5.86739 4.5 5.74022 4.55268 5.64645 4.64645C5.55268 4.74021 5.5 4.86739 5.5 5C5.5 5.13261 5.55268 5.25979 5.64645 5.35355C5.74022 5.44732 5.86739 5.5 6 5.5H7C7.13261 5.5 7.25979 5.44732 7.35355 5.35355C7.44732 5.25979 7.5 5.13261 7.5 5C7.5 4.86739 7.44732 4.74021 7.35355 4.64645C7.25979 4.55268 7.13261 4.5 7 4.5H6ZM5.5 7C5.5 6.86739 5.55268 6.74022 5.64645 6.64645C5.74022 6.55268 5.86739 6.5 6 6.5H7C7.13261 6.5 7.25979 6.55268 7.35355 6.64645C7.44732 6.74022 7.5 6.86739 7.5 7C7.5 7.13261 7.44732 7.25979 7.35355 7.35355C7.25979 7.44732 7.13261 7.5 7 7.5H6C5.86739 7.5 5.74022 7.44732 5.64645 7.35355C5.55268 7.25979 5.5 7.13261 5.5 7ZM6 8.5C5.86739 8.5 5.74022 8.55268 5.64645 8.64645C5.55268 8.74022 5.5 8.86739 5.5 9C5.5 9.13261 5.55268 9.25979 5.64645 9.35355C5.74022 9.44732 5.86739 9.5 6 9.5H7C7.13261 9.5 7.25979 9.44732 7.35355 9.35355C7.44732 9.25979 7.5 9.13261 7.5 9C7.5 8.86739 7.44732 8.74022 7.35355 8.64645C7.25979 8.55268 7.13261 8.5 7 8.5H6ZM8.5 5C8.5 4.86739 8.55268 4.74021 8.64645 4.64645C8.74022 4.55268 8.86739 4.5 9 4.5H10C10.1326 4.5 10.2598 4.55268 10.3536 4.64645C10.4473 4.74021 10.5 4.86739 10.5 5C10.5 5.13261 10.4473 5.25979 10.3536 5.35355C10.2598 5.44732 10.1326 5.5 10 5.5H9C8.86739 5.5 8.74022 5.44732 8.64645 5.35355C8.55268 5.25979 8.5 5.13261 8.5 5ZM9 6.5C8.86739 6.5 8.74022 6.55268 8.64645 6.64645C8.55268 6.74022 8.5 6.86739 8.5 7C8.5 7.13261 8.55268 7.25979 8.64645 7.35355C8.74022 7.44732 8.86739 7.5 9 7.5H10C10.1326 7.5 10.2598 7.44732 10.3536 7.35355C10.4473 7.25979 10.5 7.13261 10.5 7C10.5 6.86739 10.4473 6.74022 10.3536 6.64645C10.2598 6.55268 10.1326 6.5 10 6.5H9ZM8.5 9C8.5 8.86739 8.55268 8.74022 8.64645 8.64645C8.74022 8.55268 8.86739 8.5 9 8.5H10C10.1326 8.5 10.2598 8.55268 10.3536 8.64645C10.4473 8.74022 10.5 8.86739 10.5 9C10.5 9.13261 10.4473 9.25979 10.3536 9.35355C10.2598 9.44732 10.1326 9.5 10 9.5H9C8.86739 9.5 8.74022 9.44732 8.64645 9.35355C8.55268 9.25979 8.5 9.13261 8.5 9ZM6 13.5V12C6 11.8674 6.05268 11.7402 6.14645 11.6464C6.24022 11.5527 6.36739 11.5 6.5 11.5H9.5C9.63261 11.5 9.75979 11.5527 9.85355 11.6464C9.94732 11.7402 10 11.8674 10 12V13.5C10 13.6326 9.94732 13.7598 9.85355 13.8536C9.75979 13.9473 9.63261 14 9.5 14H6.5C6.36739 14 6.24022 13.9473 6.14645 13.8536C6.05268 13.7598 6 13.6326 6 13.5Z"
                fill="#175DA8" />
        </svg>
        <span class="date-badge-text mr-1 ml-1">Company</span> <span class="date-badge-text">:</span><span
            class="date-badge ml-1"> ${capitalizeFirstLetter(job.companyname)}</span>
    </div>
</div>



               
            </div>
        </div>

        <div class="col-12 col-sm-7">
            <div class="stats-container">
               
                 <div class="stats-card text-center applied" id="applied-candidates" data-job-id="${job.id}">
                    <span class="stats-value">${job.applied_count} <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M8.7424 14.7803L13.0107 10.512L8.7424 6.24365" stroke="black" stroke-width="1.2805" stroke-linecap="round" stroke-linejoin="round"/>
</svg></span><br>
                    <span>Applied to job</span>
                </div>
            
                <div class="stats-card match text-center match-job">
                    <span class="stats-value"> <a href="" class=" mr-1"><svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M4.47117 5.09148C4.39992 5.85148 4.34992 7.19648 4.79867 7.76898C4.79867 7.76898 4.58742 6.29148 6.48117 4.43773C7.24367 3.69148 7.41992 2.67648 7.15367 1.91523C7.00242 1.48398 6.72617 1.12773 6.48617 0.878982C6.34617 0.732732 6.45367 0.491482 6.65742 0.500232C7.88992 0.555232 9.88742 0.897731 10.7362 3.02773C11.1087 3.96273 11.1362 4.92898 10.9587 5.91148C10.8462 6.53898 10.4462 7.93398 11.3587 8.10523C12.0099 8.22773 12.3249 7.71023 12.4662 7.33773C12.5249 7.18273 12.7287 7.14398 12.8387 7.26773C13.9387 8.51898 14.0324 9.99273 13.8049 11.2615C13.3649 13.714 10.8812 15.499 8.41367 15.499C5.33117 15.499 2.87742 13.7352 2.24117 10.5427C1.98492 9.25398 2.11492 6.70398 4.10242 4.90398C4.24992 4.76898 4.49117 4.88898 4.47117 5.09148Z" fill="url(#paint0_radial_4210_1718)"/>
  <path d="M9.5402 9.67764C8.40395 8.21514 8.9127 6.54639 9.19145 5.88139C9.22895 5.79389 9.12895 5.71139 9.0502 5.76514C8.56145 6.09764 7.5602 6.88014 7.09395 7.98139C6.4627 9.47014 6.5077 10.1989 6.88145 11.0889C7.10645 11.6251 6.8452 11.7389 6.71395 11.7589C6.58645 11.7789 6.46895 11.6939 6.3752 11.6051C6.10555 11.3462 5.91337 11.0172 5.8202 10.6551C5.8002 10.5776 5.69895 10.5564 5.6527 10.6201C5.3027 11.1039 5.12145 11.8801 5.1127 12.4289C5.0852 14.1251 6.48645 15.5001 8.18145 15.5001C10.3177 15.5001 11.874 13.1376 10.6465 11.1626C10.2902 10.5876 9.9552 10.2114 9.5402 9.67764Z" fill="white"/>
  <defs>
    <radialGradient id="paint0_radial_4210_1718" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(7.80314 15.5378) rotate(-179.751) scale(8.82346 14.4775)">
      <stop offset="0.314" stop-color="#FF9800"/>
      <stop offset="0.662" stop-color="#FF6D00"/>
      <stop offset="0.972" stop-color="#F44336"/>
    </radialGradient>
  </defs>
</svg></a>
0 <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M8.64876 13.7498L12.8154 9.58317L8.64876 5.4165" stroke="black" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
</svg></span><br>
                    <span>Match your job post</span>
                </div>
            
                <div class="stats-card text-center database-match">
                    <span class="stats-value">0 <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M8.38108 14.6343L12.6494 10.366L8.38108 6.09766" stroke="black" stroke-width="1.2805" stroke-linecap="round" stroke-linejoin="round"/>
</svg></span><br>
                    <span>Database Matches</span>
                </div>
            
                <div class="stats-actions">
                    <a href="#" class="icon-primary">
                        <!-- Your first SVG icon -->
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.8993 4.68701H7.1818C6.52389 4.68701 5.89294 4.94836 5.42773 5.41357C4.96252 5.87878 4.70117 6.50973 4.70117 7.16764V14.8851C4.70117 15.543 4.96252 16.174 5.42773 16.6392C5.89294 17.1044 6.52389 17.3658 7.1818 17.3658H14.8993C15.5572 17.3658 16.1882 17.1044 16.6534 16.6392C17.1186 16.174 17.3799 15.543 17.3799 14.8851V7.16764C17.3799 6.50973 17.1186 5.87878 16.6534 5.41357C16.1882 4.94836 15.5572 4.68701 14.8993 4.68701ZM13.7813 11.5776H11.5918V13.7671C11.5918 14.0638 11.3637 14.3184 11.0671 14.3332C10.9925 14.3368 10.9181 14.3252 10.8481 14.2992C10.7782 14.2731 10.7143 14.2331 10.6603 14.1817C10.6062 14.1302 10.5632 14.0683 10.5339 13.9997C10.5045 13.9311 10.4893 13.8573 10.4893 13.7826V11.5776H8.2998C8.00316 11.5776 7.74855 11.3496 7.73374 11.0529C7.73015 10.9784 7.74172 10.9039 7.76777 10.834C7.79382 10.764 7.83379 10.7001 7.88527 10.6461C7.93675 10.5921 7.99866 10.5491 8.06724 10.5197C8.13583 10.4903 8.20968 10.4751 8.2843 10.4751H10.4893V8.28564C10.4893 7.989 10.7174 7.73439 11.014 7.71958C11.0886 7.71599 11.163 7.72756 11.233 7.75361C11.3029 7.77966 11.3668 7.81963 11.4208 7.87111C11.4748 7.92259 11.5179 7.98449 11.5472 8.05308C11.5766 8.12167 11.5918 8.19552 11.5918 8.27014V10.4751H13.7968C13.8715 10.4751 13.9454 10.4901 14.0141 10.5195C14.0827 10.5488 14.1447 10.5918 14.1963 10.6459C14.2478 10.6999 14.2879 10.7638 14.314 10.8338C14.34 10.9038 14.3516 10.9783 14.348 11.0529C14.3336 11.3496 14.0779 11.5776 13.7813 11.5776Z" fill="white"/>
                            <path d="M14.4802 3.58441C14.3087 3.10112 13.9918 2.68273 13.5731 2.38665C13.1544 2.09056 12.6543 1.93128 12.1415 1.93066H4.42398C3.76608 1.93066 3.13512 2.19201 2.66992 2.65722C2.20471 3.12243 1.94336 3.75339 1.94336 4.41129V12.1288C1.94398 12.6416 2.10326 13.1417 2.39934 13.5604C2.69543 13.9791 3.11382 14.296 3.59711 14.4675V6.34066C3.59711 5.60966 3.8875 4.9086 4.4044 4.3917C4.92129 3.8748 5.62236 3.58441 6.35336 3.58441H14.4802Z" fill="white"/>
                        </svg>
                    </a>
                    <div class="dropdown position-relative">
                                <a href="#" class="icon-danger dropdown-toggle" data-job-id="${job.id}">
                                    <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.94743 4.80924C9.94749 4.97789 9.91432 5.1449 9.84983 5.30074C9.78534 5.45657 9.69079 5.59818 9.57157 5.71747C9.45236 5.83677 9.31081 5.93141 9.15502 5.996C8.99922 6.06059 8.83223 6.09386 8.66358 6.09392C8.49493 6.09397 8.32792 6.06081 8.17208 5.99632C8.01625 5.93183 7.87464 5.83727 7.75535 5.71806C7.63606 5.59884 7.54141 5.45729 7.47682 5.3015C7.41223 5.14571 7.37896 4.97872 7.37891 4.81007C7.3788 4.46946 7.514 4.14276 7.75477 3.90183C7.99554 3.66091 8.32215 3.5255 8.66276 3.52539C9.00336 3.52528 9.33007 3.66048 9.57099 3.90125C9.81191 4.14202 9.94732 4.46863 9.94743 4.80924Z" fill="white"/>
                                        <path d="M8.66471 10.5008C9.37376 10.5008 9.94856 9.92601 9.94856 9.21696C9.94856 8.50791 9.37376 7.93311 8.66471 7.93311C7.95566 7.93311 7.38086 8.50791 7.38086 9.21696C7.38086 9.92601 7.95566 10.5008 8.66471 10.5008Z" fill="white"/>
                                        <path d="M8.66471 14.909C9.37376 14.909 9.94856 14.3342 9.94856 13.6252C9.94856 12.9161 9.37376 12.3413 8.66471 12.3413C7.95566 12.3413 7.38086 12.9161 7.38086 13.6252C7.38086 14.3342 7.95566 14.909 8.66471 14.909Z" fill="white"/>
                                    </svg>
                                </a>
                                <div class="action-dropdown" style="display: none; position: absolute; right: 0; z-index: 1000; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 4px; width: 120px;">
                                    <ul class="list-unstyled m-0 p-0">
                                        <li><a href="update_posting_job.php?id=${job.id}" class="dropdown-item py-2 px-3">Edit</a></li>
                                        <li><a href="#" class="dropdown-item py-2 px-3 delete-job" data-job-id="${job.id}">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
                `;
            });
            
            $('#jobsContainer').html(html);
        }
        
        // Function to update pagination
        function updatePagination(pagination) {
            const { current_page, total_pages } = pagination;
            
            // Update page info text
            $('#pageInfo').text(`Page ${current_page} of ${total_pages}`);
            
            let paginationHtml = '';
            
            // Previous button
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link ${current_page <= 1 ? 'disabled' : ''}" href="#" ${current_page > 1 ? 'data-page="' + (current_page - 1) + '"' : ''}>
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;
            
            // Page numbers
            const startPage = Math.max(1, current_page - 2);
            const endPage = Math.min(total_pages, current_page + 2);
            
            if (startPage > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="1">1</a>
                    </li>
                `;
                
                if (startPage > 2) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#">...</a>
                        </li>
                    `;
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `
                    <li class="page-item ">
                        <a class="page-link  ${i === current_page ? 'active' : ''}" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }
            
            if (endPage < total_pages) {
                if (endPage < total_pages - 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#">...</a>
                        </li>
                    `;
                }
                
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${total_pages}">${total_pages}</a>
                    </li>
                `;
            }
            
            // Next button
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link ${current_page >= total_pages ? 'disabled' : ''}" href="#" ${current_page < total_pages ? 'data-page="' + (current_page + 1) + '"' : ''}>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;
            
            $('#pagination').html(paginationHtml);
        }
        
        // Function to update counters
        function updateCounters(totalRecords) {
            // $('#jobCount').text(totalRecords);
          
            
            // For the filter counts, you might want to make separate AJAX calls
            // to get the actual counts per status, or include them in the main response
            // For now, we'll just set placeholders
            $('#activeCount').text('0');
            $('#inactiveCount').text('0');
            $('#expiredCount').text('0');
            
            // You can implement a separate function to fetch these counts if needed
            fetchStatusCounts();
        }
        
        // Helper function to fetch status counts
        function fetchStatusCounts() {
            $.ajax({
                url: 'employer_listing_counts.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#activeCount').text(response.active || 0);
                    $('#inactiveCount').text(response.inactive || 0);
                    $('#expiredCount').text(response.expired || 0);
                    $('#jobCount').text(response.total);
                }
            });
        }
        
        // // Helper function to format date
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = date.toLocaleString('default', { month: 'short' });
            const year = date.getFullYear();
            return `${day} ${month} ${year}`;
        }
        
        // Helper function to capitalize first letter
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        