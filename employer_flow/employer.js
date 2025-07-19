// For Add to cart 
function addToCart(candidateId, price) {
    $.ajax({
        url: 'check_candidate_purchase.php',
        type: 'POST',
        data: { candidate_id: candidateId },
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                if (data.is_active && data.buyed_status === 0) {
                    alert("You have already selected this candidate and their access is still active. Please select another candidate.");
                    return;
                }
                // Candidate allowed: proceed with adding to cart
                $.ajax({
                    url: 'add_to_cart.php',
                    type: 'POST',
                    data: {
                        candidate_id: candidateId,
                        price: price
                    },
                    success: function (response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                // Update cart count in navbar elements
                                $('#cart-count').text(data.cartCount);
                                $('#cart-count-mobile').text(data.cartCount);
                                $('#cart-count-dropdown').text(data.cartCount);
                                // Update the button for this candidate using its data attribute
                                $("button.add-to-cart-btn").each(function () {
                                    if ($(this).data('candidate-id') == candidateId) {
                                        $(this)
                                            .html('<i class="fa fa-shopping-cart"></i> Go to Cart')
                                            .attr('onclick', 'window.location.href = "cart_page.php"')
                                            .removeClass('btn-primary')
                                            .addClass('btn-success');
                                    }
                                });
                                alert('Candidate added to cart successfully!');
                            } else {
                                alert('Error: ' + data.message);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error processing response');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Ajax error:', error);
                        alert('Error adding candidate to cart');
                    }
                });
            } else {
                alert("Error checking candidate purchase status: " + data.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error in AJAX check:", error);
            alert("Error in checking candidate purchase status.");
        }
    });
}

// Update the cart count on page load
function updateAllCartCounts() {
    $.ajax({
        url: 'get_cart_count.php',
        type: 'GET',
        success: function (response) {
            try {
                const data = JSON.parse(response);
                const count = data.cartCount;
                $('#cart-count').text(count);
                $('#cart-count-mobile').text(count);
                $('#cart-count-dropdown').text(count);
            } catch (e) {
                console.error('Error parsing cart count:', e);
            }
        }
    });
}

// Update button states based on cart status using data attributes
function updateButtonStates() {
    $.ajax({
        url: 'check_cart_status.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                $.each(data.inCart, function (index, candidateId) {
                    // More specific and robust selector
                    $('button.add-to-cart-btn').each(function () {
                        var btnCandidateId = $(this).data('candidate-id');
                        if (btnCandidateId == candidateId) {
                            $(this)
                                .html('<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.57326 2.30253C1.68241 1.97506 2.03636 1.79809 2.36382 1.90724L2.58459 1.98083C2.59559 1.98449 2.60655 1.98815 2.61748 1.99179C3.13946 2.16577 3.58056 2.31279 3.92742 2.47414C4.29612 2.64567 4.61603 2.85824 4.85861 3.1948C5.10119 3.53137 5.2017 3.90209 5.24782 4.3061C5.29122 4.68619 5.29121 5.15115 5.29118 5.70136V7.91684C5.29118 9.11301 5.29251 9.94726 5.37714 10.5768C5.45935 11.1882 5.60972 11.512 5.84036 11.7427C6.071 11.9733 6.39481 12.1237 7.00628 12.2058C7.63574 12.2905 8.47001 12.2918 9.66618 12.2918H15.4995C15.8447 12.2918 16.1245 12.5717 16.1245 12.9168C16.1245 13.262 15.8447 13.5418 15.4995 13.5418H9.62043C8.4808 13.5418 7.5622 13.5418 6.83972 13.4448C6.08963 13.3439 5.45807 13.1282 4.95647 12.6265C4.45488 12.1249 4.23913 11.4933 4.13829 10.7433C4.04116 10.0208 4.04116 9.10226 4.04118 7.96257V5.73604C4.04118 5.14187 4.04024 4.74873 4.00589 4.44789C3.97337 4.16302 3.91658 4.02561 3.84456 3.92569C3.77255 3.82578 3.66015 3.72844 3.40018 3.60751C3.12564 3.47979 2.75297 3.35457 2.1893 3.16669L1.96854 3.09309C1.64108 2.98394 1.46411 2.62999 1.57326 2.30253Z" fill="white"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.29123 5.70117C5.29124 5.44964 5.29124 5.21593 5.28711 5H14.2084C15.9208 5 16.7771 5 17.1476 5.56188C17.5181 6.12378 17.1808 6.91078 16.5063 8.48477L16.1491 9.31811C15.8342 10.053 15.6767 10.4204 15.3636 10.6269C15.0505 10.8334 14.6508 10.8334 13.8513 10.8334H5.41889C5.40343 10.7538 5.38953 10.6684 5.37718 10.5765C5.29256 9.94711 5.29123 9.11286 5.29123 7.91667L5.29123 5.70117ZM7.16504 6.875C6.81986 6.875 6.54004 7.15482 6.54004 7.5C6.54004 7.84518 6.81986 8.125 7.16504 8.125H9.66504C10.0102 8.125 10.29 7.84518 10.29 7.5C10.29 7.15482 10.0102 6.875 9.66504 6.875H7.16504Z" fill="white"/><path d="M6.75 15C7.44036 15 8 15.5597 8 16.25C8 16.9403 7.44036 17.5 6.75 17.5C6.05964 17.5 5.5 16.9403 5.5 16.25C5.5 15.5597 6.05964 15 6.75 15Z" fill="white"/><path d="M15.5 16.25C15.5 15.5596 14.9403 15 14.25 15C13.5597 15 13 15.5596 13 16.25C13 16.9403 13.5597 17.5 14.25 17.5C14.9403 17.5 15.5 16.9403 15.5 16.25Z" fill="white"/></svg> Go to Cart')
                                .attr('onclick', 'window.location.href = "cart_page.php"')
                                .removeClass('btn-primary')
                                .addClass('btn-success');
                        }
                    });
                });
            } else {
                console.log("Failed to get cart status:", data.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error checking cart status:', error);
        }
    });
}

// On document ready, execute both functions
$(document).ready(function () {
    updateAllCartCounts();
    // Add a delay to ensure DOM is fully loaded
    setTimeout(function () {
        updateButtonStates();
    }, 500);
});

// Also update on window load as a backup
$(window).on('load', function () {
    updateButtonStates();
});

// Initialize when document is ready
$(document).ready(function () {
    // Ensure mobile menu closes when clicking outside
    $(document).click(function (event) {
        const $navbar = $('.navbar');
        const $navbarToggler = $('.navbar-toggler');

        if (!$navbar.is(event.target) &&
            $navbar.has(event.target).length === 0 &&
            !$navbarToggler.is(event.target) &&
            $('.navbar-collapse').hasClass('show')) {
            $navbarToggler.click();
        }
    });
});

function toggleNotifications(device, event) {
    // Prevent default behavior and stop propagation
    // Get the specific notification container using a template literal
    const containerId = `notifications-${device}`;
    const container = document.getElementById(containerId);
    if (container) {
        // Get current display state
        const currentDisplay = window.getComputedStyle(container).display;
        if (currentDisplay === 'none') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    } else {
        console.error(`Notification container #${containerId} not found`);
    }
}

// (Optional) Your existing DOMContentLoaded code for filter groups, etc.
document.addEventListener('DOMContentLoaded', function () {
    const filterGroups = document.querySelectorAll('.filter-group');
    filterGroups.forEach(group => {
        const heading = group.querySelector('.d-flex');
        const content = group.querySelector('.filter-content');
        const icon = heading.querySelector('i');
        // Ensure filter content is hidden on initial load
        content.style.display = 'none';
        icon.classList.replace('fa-chevron-down', 'fa-chevron-right'); // Ensure correct icon state
        heading.addEventListener('click', function () {
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.replace('fa-chevron-right', 'fa-chevron-down');
            } else {
                content.style.display = 'none';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-right');
            }
        });
    });
});

function showLoader() {
    $('.ajax-loader').fadeIn(200);
}

function hideLoader() {
    $('.ajax-loader').fadeOut(200);
}

//------------ Function to add a keyword badge And Remove bage ------------------
function addBadge(value, type, label) {
    const badgeClass = 'badge-keyword-' + type;
    const badgeContainer = $('#keyword-badges-' + type);
    // Avoid duplicate badges
    if (badgeContainer.find(`[data-check="${label}"]`).length > 0) {
        return;
    }
    // Create badge
    const badge = $('<span></span>')
        .addClass(badgeClass)
        .addClass('filter-badge keyword-filter-badge')
        .attr('data-check', label)
        .attr('data-value', value)
        .attr('data-key', type)
        .html(`${label} <span class="remove-badge">x</span>`);
    badgeContainer.append(badge);
    badgeContainer.show();
}

function removeBadge(value, type, label) {
    const badgeContainer = $('#keyword-badges-' + type);
    // Find and remove the badge
    const badge = badgeContainer.find(`[data-check="${label}"]`);
    if (badge.length > 0) {
        badge.remove();
    }
    // Hide the container if no badges remain
    if (badgeContainer.children().length === 0) {
        badgeContainer.hide();
    }
}
$(document).on('click', '.remove-badge', function () {
    const clickedSpan = $(this);
    // Get the parent span of the clicked span
    const parentSpan = clickedSpan.parent('span');
    const label = parentSpan.data('check');
    const type = parentSpan.data('key');
    const value = parentSpan.data('value');
    // Uncheck the corresponding checkbox
    const checkbox = $(`input[type="checkbox"][name="${type}"][value="${value}"]`);
    if (checkbox.length) {
        checkbox.prop('checked', false);
    }
    // Remove the badge using data-check
    $(`.filter-badge[data-check="${label}"][data-key="${type}"]`).remove();
    // Hide container if empty
    const badgeContainer = $(`.keyword-badge-container[data-key="${type}"]`);
    if (badgeContainer.length && badgeContainer.children().length === 0) {
        badgeContainer.hide();
    }
    fetchCandidates(1);
});
// ------------End Function to add a keyword badge And Remove bage ----------------

//------------ Fetch searched location and Filter useing location -----------------
$(document).on("input", '#search_location', function () {
    const query = $(this).val();
    if (query.length >= 1) {
        $('#select-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
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
// Set input value and trigger function 
$(document).on("click", '#list_location', function () {
    const label = $(this).text();
    const location_id = $(this).data('id');
    $('#search_location').val(label);
    addBadge(location_id, "location", label);
    $('#select-div').hide();
    $('#search_location').val('');
    fetchCandidates(1);
});
// Target remover
$(document).on("click", function (event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div').length && !$target.is('#search_location')) {
        $('#select-div').hide();
    }
});
//------------ End Fetch searched location and Filter useing location -----------------
//------------ Fetch havekeyword ------------

// Format field names for display
function formatFieldName(fieldName) {
    const fieldDisplayNames = {
        'username': 'Name',
        'mobile_number': 'Mobile Number',
        'current_company': 'Current Company',
        'companyname': 'Company Name',
        'sales_experience': 'Sales Experience',
        'work_experience': 'Work Experience',
        'current_location': 'Current Location',
        'location': 'Location',
        'current_salary': 'Current Salary',
        'salary': 'Salary',
        'jobrole': 'Job Role',
        'gender': 'Gender',
        'employed': 'Employment Status',
        'destination': 'Destination',
        'hl_lap': 'HL/LAP',
        'personal_loan': 'Personal Loan',
        'business_loan': 'Business Loan',
        'education_loan': 'Education Loan',
        'credit_cards': 'Credit Cards',
        'gold_loan': 'Gold Loan',
        'casa': 'CASA',
        'others': 'Others',
        'Credit_dept': 'Credit Department',
        'HR_Training': 'HR Training',
        'Legal_compliance_Risk': 'Legal Compliance/Risk',
        'Operations': 'Operations',
        'Others1': 'Other Details',
        'Sales': 'Sales',
        'associate_name': 'Associate Name',
        'associate_mobile': 'Associate Mobile'
    };

    return fieldDisplayNames[fieldName] || fieldName.split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

$('#Havekeyword').on('input', function () {
    const keyword = $(this).val().trim();
    if (keyword.length === 0) {
        $('#keywordSuggestions').hide().empty();
        return;
    }
    // Add loading indicator
    $('#keywordSuggestions').html('<li style="text-align:center;padding:10px;">Searching...</li>').show();
    $.ajax({
        url: 'candidate_search.php',
        type: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function (response) {
            const $suggestions = $('#keywordSuggestions').empty();
            if (response && response.length > 0) {
                // Group results by field type
                const fieldGroups = {};
                response.forEach(function (item) {
                    if (!fieldGroups[item.field]) {
                        fieldGroups[item.field] = [];
                    }
                    // Check if this exact value already exists in this field group
                    const exists = fieldGroups[item.field].some(function (existingItem) {
                        return existingItem.value === item.value;
                    });
                    if (!exists) {
                        fieldGroups[item.field].push(item);
                    }
                });
                // Loop through each field group and add its items to the dropdown
                Object.keys(fieldGroups).forEach(function (fieldName) {
                    // Convert field name to display name
                    const displayFieldName = formatFieldName(fieldName);
                    // Add each value for this field
                    fieldGroups[fieldName].forEach(function (item) {
                        const $item = $(
                            `<li class="suggestion-item" style="padding:8px 15px;cursor:pointer;border-bottom:1px solid #eee;display:flex;justify-content:space-between;">
                                            ${item.value}
                                        </li>`
                        ).data('item', item);

                        // New click handler
                        $item.on('click', function () {
                            const selectedItem = $(this).data('item');
                            const selectedValue = selectedItem.value;

                            // Add a badge for the selected keyword
                            addBadge(selectedValue, "havekeyword", selectedValue);

                            // Clear the input field
                            $('#Havekeyword').val('');
                            $suggestions.hide().empty();

                            // Trigger the search with all selected keywords
                            fetchCandidates(1);
                        });
                        $suggestions.append($item);
                    });
                });
                $suggestions.css({
                    'max-height': '400px',
                    'overflow-y': 'auto',
                    'border': '1px solid #ddd',
                    'border-radius': '4px',
                    'box-shadow': '0 2px 5px rgba(0,0,0,0.15)'
                }).show();
            } else {
                $suggestions.html(
                    '<li style="text-align:center;padding:10px;color:#777;">No matching results found</li>'
                ).show();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching suggestions:', error);
            $('#keywordSuggestions').html(
                '<li style="text-align:center;padding:10px;color:#d9534f;">Error loading suggestions</li>'
            );
        }
    });
});
// Hide suggestions when clicking outside
$(document).on('click', function (e) {
    if (!$(e.target).closest('#Havekeyword, #keywordSuggestions').length) {
        $('#keywordSuggestions').hide().empty();
    }
});
// -------------- End Fecth haveKeyword --------------
//------------ Search product and department and subproduct and subdepartment ---------------
$('#departmentsearch').on('input', function () {
    const input = $(this).val();
    $('#department-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { departmentsearch: input },
        success: function (response) {
            $('#department-list-div').html(response);
        },
        error: function () {
            console.error('AJAX request failed.');
        }
    });
});
$('#subdepartmentsearch').on('input', function () {
    const input = $(this).val();
    $('#subdepartment-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { subdepartmentsearch: input },
        success: function (response) {
            $('#subdepartment-list-div').html(response);
        },
        error: function () {
            console.error('AJAX request failed.');
        }
    });
});
$('#categorysearch').on('input', function () {
    const input = $(this).val();
    $('#category-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { categorysearch: input },
        success: function (response) {
            $('#category-list-div').html(response);
        },
        error: function () {
            console.error('AJAX request failed.');
        }
    });
});
$('#productsearch').on('input', function () {
    const input = $(this).val();
    $('#product-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { productsearch: input },
        success: function (response) {
            $('#product-list-div').html(response);
        },
        error: function () {
            console.error('AJAX request failed.');
        }
    });
});
$('#subproductsearch').on('input', function () {
    const input = $(this).val();
    $('#subproduct-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { subproductsearch: input },
        success: function (response) {
            $('#subproduct-list-div').html(response);
        },
        error: function () {
            console.error('AJAX request failed.');
        }
    });
});
$('#specializationsearch').on('input', function () {
    const input = $(this).val();
    $('#specialization-list-div').html('<p style="padding: 10px; color: #999;">Searching...</p>').show();
    $.ajax({
        url: 'serach_sub_data.php',
        type: 'POST',
        data: { specializationsearch: input },
        success: function (response) {
            $('#specialization-list-div').html(response);
        },
        error: function () {
            console.error('AJAX request failed.');
        }
    });
});
//------------ End Search product and department and subproduct and subdepartment ---------------

//------------ Fetch Fetch searched designation -----------------
$(document).on("input", '#search_designation', function () {
    const query = $(this).val();
    if (query.length >= 1) {
        $('#select-designation-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
        $.ajax({
            url: 'search_designation.php',
            type: 'POST',
            data: { query: query },
            success: function (response) {
                if ($.trim(response)) {
                    $("#select-designation-div").show().html(response);
                } else {
                    $("#select-designation-div").hide().html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
            }
        });
    } else {
        $("#select-designation-div").hide().html("");
    }
});
// Set input value and trigger function 
$(document).on("click", '#list_designation', function () {
    const label = $(this).text();
    $('#search_designation').val(label);
    addBadge(label, "designation", label);
    $('#select-designation-div').hide();
    $('#search_designation').val('');
    fetchCandidates(1);
});
// Target remover
$(document).on("click", function (event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div').length && !$target.is('#search_designation')) {
        $('#select-designation-div').hide();
    }
});
//------------ End Fetch searched designation -----------------

// ------------Fetch selected values -----------
function getSelectedKeysValues() {
    const KeysValues = [];
    $('.keyword-filter-badge').each(function () {
        const key = $(this).data('key');
        const value = $(this).data('value');
        KeysValues.push({ key: key, value: value });
    });
    // Show or hide the clear-all button
    if (KeysValues.length > 0) {
        document.querySelector('.clear-all-filter').style.display = 'block';
    } else {
        document.querySelector('.clear-all-filter').style.display = 'none';
    }
    return KeysValues;
}
// ------------ End Fetch selected values -----------


// ----------- Old filters ------------
function collectFilters() {
    const filters = {};
    // if ($('input[name="sort"]:checked').length) {
    //     filters.sort = $('input[name="sort"]:checked').val();
    // } else if ($('#jobsort').val()) {
    //     filters.sort = $('#jobsort').val();
    // } else if ($('#sort-relevance').is(':checked')) {
    //     filters.sort = 'relevance';
    // } else if ($('#sort-salary').is(':checked')) {
    //     filters.sort = 'salary';
    // } else if ($('#sort-date').is(':checked')) {
    //     filters.sort = 'date';
    // }

    // For Mobile extra details
    filters.department_m = $('#jobdepartment').val();
    filters.subdepartment_m = $('#job-sub-department').val();
    filters.category_m = $('#job-category').val();
    filters.product_m = $('#job-product').val();
    filters.subproduct_m = $('#job-sub-product').val();
    filters.specialization_m = $('#job-specialization').val();
    filters.location_m = $('#joblocation').val();

    // Salary range
    const minSalaryValue = $('#min_salary').val() || $('#jobmin_salary').val();
    const maxSalaryValue = $('#max_salary').val() || $('#jobmax_salary').val();
    if (minSalaryValue) filters.min_salary = minSalaryValue;
    if (maxSalaryValue) filters.max_salary = maxSalaryValue;

    // Search term
    const searchValue = $('#search').val() || $('#searchjob').val();
    if (searchValue) filters.search = searchValue;

    // Pagination
    const limitValue = $('#per-page-list').val();
    if (limitValue) filters.limit = limitValue;

    return filters;
}

// Ensure jQuery is loaded
const urlParams = new URLSearchParams(window.location.search);
const jobId = urlParams.get('job_id');


function fetchCandidates(page = 1) {

    const filters = collectFilters();
    const values = getSelectedKeysValues();
    values.forEach(({ key, value }) => {
        if (!filters[`${key}[]`]) {
            filters[`${key}[]`] = [];
        }
        filters[`${key}[]`].push(value);
    });
    filters.page = page;
    showLoader();
    $.ajax({
        url: 'candidate_filter.php',
        type: 'GET',
        data: filters,
        success: function (response) {
            $('#candidate-list').html(response);
            hideLoader();
        },
        error: function (xhr, status, error) {
            console.error('Error fetching candidates:', error);
            hideLoader();
        }
    });
    if (jobId) {
        filters.job_id = jobId;
    }
}

$(document).ready(function () {

    // Apply job filter 
    $(document).on('change', '.applied-input', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "applied", label);
        } else {
            removeBadge(this.value, "applied", label);
        }
        fetchCandidates(1);
    });

    // Location filter mention in top 

    // Haveword filter mention in top 

    // Gender filter
    $(document).on('change', 'input[name="gender"]', function () {
        if (this.checked) {
            addBadge(this.value, "gender", this.value);
        } else {
            removeBadge(this.value, "gender", this.value)
        }
        fetchCandidates(1);

    });

    // Deparments Products Sub Products,Departments Filter
    $(document).on('change', 'input[name="department"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "department", label);
        } else {
            removeBadge(this.value, "department", label)
        }
        fetchCandidates(1);
    });

    $(document).on('change', 'input[name="subdepartment"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "subdepartment", label);
        } else {
            removeBadge(this.value, "subdepartment", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="category"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "category", label);
        } else {
            removeBadge(this.value, "category", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="product"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "product", label);
        } else {
            removeBadge(this.value, "product", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="subproduct"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "subproduct", label);
        } else {
            removeBadge(this.value, "subproduct", label)
        }
        fetchCandidates(1);
    });
    $(document).on('change', 'input[name="specialization"]', function () {
        const label = $(this).data('name');
        if (this.checked) {
            addBadge(this.value, "specialization", label);
        } else {
            removeBadge(this.value, "specialization", label)
        }
        fetchCandidates(1);
    });

    // Designation filter mention in top

});

// Event bindings
$(document).ready(function () {
    fetchCandidates();

    // Web filters
    $(document).on('change', 'input[type="checkbox"]', function () {
        fetchCandidates(1);
    });

    $('#other-department, #other-sub-department, #other-product, #other-sub-product, #other-designation').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchCandidates(1);
        }, 500));
    });

    $('#jobsort, #jobdepartment, #job-sub-department,#job-category, #job-product,#job-sub-product,#job-specialization, #location, #joblocation, #per-page-list').on('change', function () {
        fetchCandidates(1);
    });

    $('#min_salary, #max_salary, #jobmin_salary, #jobmax_salary').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchCandidates(1);
        }, 500));
    });

    $('#search, #searchjob').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchCandidates(1);
        }, 300));
    });

    // Pagination
    $(document).on('click', '.linkForPage', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        fetchCandidates(page);
    });

    // Mobile filter checkboxes
    $('#mobileFilterPopup input[type="checkbox"]').on('change', function () {
        fetchCandidates(1);
    });

    // Mobile clear filters
    $('#clearFiltersBtn').on('click', function () {
        window.location.reload(); // Reloads the current page
    });


    // Optional: Close filter popup button
    $('#closeFilterPopup').on('click', function () {
        $('#mobileFilterPopup').addClass('d-none');
    });
});
