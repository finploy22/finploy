
if (jobIdUrl > 0) {
    var fetchJobUrl = "fetch_jobs.php?jobidurl=" + jobIdUrl;
} else {
    var fetchJobUrl = "fetch_jobs.php";
}

// ----------- Loader Show and Hide -----------
function showLoader() {
    $('.ajax-loader').fadeIn(200);
}
function hideLoader() {
    $('.ajax-loader').fadeOut(200);
}

// ------------ Toggle Notificaiton Dropdown --------------        
function toggleDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// ----------------- fetch Full Jobs -------------
$(document).ready(function () {
    $(document).on("click", "#loadMoreBtn", function () {
        $.ajax({
            url: fetchJobUrl,
            type: "POST",
            data: { load_more: true },
            beforeSend: function () {
                showLoader();
            },
            // beforeSend: function() {
            //     $("#loadMoreBtn").text("Loading...").prop("disabled", true);
            // },
            success: function (response) {
                $("#job-list").html(response);
                hideLoader();
            }
        });
    });
});

// ----------------- Job details Popup --------------- 
$(document).on("click", ".job-grid, .job-card-container, .job-bio", function () {
    let jobId = $(this).data("id");
    $("#jobDetailsPopup").fadeIn();
    $.ajax({
        url: "candidate/get_job_details.php",
        type: "POST",
        data: { id: jobId },
        beforeSend: function () {
            showLoader();
        },
        success: function (response) {

            hideLoader();
            $("#popupContent").html(response);
        }
    });
});

$(".close-popup").on("click", function () {
    $("#jobDetailsPopup").fadeOut();
});

// ------------- Multi-step navigation ------------
$(".next-step").on("click", function () {
    $(".step").hide();
    $("#step-2").show();
});

$(".prev-step").on("click", function () {
    $(".step").hide();
    $("#step-1").show();
});

// -------------- Pagination -------------
// $(document).ready(function () {
//     function loadJobs(page) {
//         $.ajax({
//             url: fetchJobUrl,
//             type: 'POST',
//             data: {
//                 page: page,
//                 // filters:filters
//             },
//             beforeSend: function () {
//                 showLoader();
//                 // $('#job-container').html('<p>Loading...</p>');
//             },
//             success: function (response) {
//                 hideLoader();
//                 $('#job-list').html(response);
//             }
//         });
//     }
//     loadJobs(1);
// });

// ----------- Filter Active and  Inactive  ------------
$(document).ready(function () {
    // Ensure all accordions start closed
    $('.filter-accordion').removeClass('active');
    $('.filter-content').removeClass('active').css('max-height', 0);
    $('.filter-accordion .accordion-arrow').attr('src', 'assets/downward-arrow.svg');
    // Toggle filter accordion sections
    $('.filter-accordion').on('click', function () {
        $(this).toggleClass('active');
        const content = $(this).next('.filter-content');
        const arrowImg = $(this).find('.accordion-arrow');
        content.toggleClass('active');
        if (content.hasClass('active')) {
            content.css('max-height', "200px");
            arrowImg.attr('src', 'assets/upward-arrow.svg');
        } else {
            content.css('max-height', 0);
            arrowImg.attr('src', 'assets/downward-arrow.svg');
        }
    });
});

// ------------ Set fillter for Seo  ----------------
if (seoCity != null) {
    addBadge(seolocationId, "location", seocityLabel);
}
if (seoDesignation != null) {
    addBadge(seoDesignation, "designation", seoDesignation);
}
if (seoCompanyname != null) {
    addBadge(seoCompanyname, "companyname", seoCompanyname);
}
if (seoProduct != null) {
    addBadge(seoProductId, "product", seoProduct);
}
if (seoSubProduct != null) {
    addBadge(seoSubProductId, "subproduct", seoSubProduct);
}
if (seoSpecialization != null) {
    addBadge(seoSpecializationId, "specialization", seoSpecialization);
}
if (seoDepartment != null) {
    addBadge(seoDepartmentId, "department", seoDepartment);
}
if (seoSubDepartment != null) {
    addBadge(seoSubDepartmentId, "subdepartment", seoSubDepartment);
}
if (seoCategory != null) {
    addBadge(seoCategoryId, "category", seoCategory);
}


// ------------ End Set fillter for Seo  -------------


// ------------ Search Input Placeholder auto change ----------------
const searchValues = ["jobs", "departments", "companies", "locations"];
let index = 0;
function changePlaceholder() {
    const searchInputs = document.querySelectorAll("#search, #searchjob");
    searchInputs.forEach(input => {
        input.setAttribute("placeholder", `Search for '${searchValues[index]}'`);
    });
    index = (index + 1) % searchValues.length;
}
// Change placeholder every 2.5 seconds
setInterval(changePlaceholder, 2500);
// Show search container when search icon is clicked
document.getElementById("search-icon-mobile").addEventListener("click", function () {
    document.getElementById("search-container-mobile").style.display = "block";
});


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
// ------------ New Added Filters -----------

// ------------ Function to add a keyword badge And Remove bage ------------------
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
// Trigger removeBadge
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
    fetchFilteredJobs(1);
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
    fetchFilteredJobs(1);
});
// Target remover
$(document).on("click", function (event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div').length && !$target.is('#search_location')) {
        $('#select-div').hide();
    }
});
//------------ End Fetch searched location and Filter useing location -----------------


//------------ Fetch searched location and Filter useing location For mobile-----------------
$(document).on("input", '#search_location-m', function () {
    const query = $(this).val();
    if (query.length >= 1) {
        $('#select-div-m').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
        $.ajax({
            url: '../candidate/search_location.php',
            type: 'POST',
            data: { query: query },
            success: function (response) {
                if ($.trim(response)) {
                    $("#select-div-m").show().html(response);
                } else {
                    $("#select-div-m").hide().html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
            }
        });
    } else {
        $("#select-div-m").hide().html("");
    }
});
// Set input value and trigger function 
// $(document).on("click", '#list_location', function () {
//     const label = $(this).text();
//     const location_id = $(this).data('id');
//     $('#search_location-m').val(label);
//     addBadge(location_id, "location", label);
//     $('#select-div-m').hide();
//     $('#search_location-m').val('');
    // fetchFilteredJobs(1);
// });
// Target remover
$(document).on("click", function (event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div-m').length && !$target.is('#search_location-m')) {
        $('#select-div-m').hide();
    }
});
//------------ End Fetch searched location and Filter useing location For mobile -----------------

//------------ Search product and department and subproduct and subdepartment ---------------
function setupSearch(inputSelector, dataKey, resultDivSelector) {
    let debounceTimer;
    $(inputSelector).on('input', function () {
        const input = $(this).val();
        const $resultDiv = $(resultDivSelector);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            $resultDiv.html('<p style="padding-top: 10px;text-align: center;color: #999;">Searching...</p>').show();

            const ajaxStartTime = Date.now();

            $.ajax({
                url: '../employer_flow/serach_sub_data.php',
                type: 'POST',
                data: { [dataKey]: input },
                success: function (response) {
                    const elapsed = Date.now() - ajaxStartTime;
                    const remainingTime = 400 - elapsed;
                    if (remainingTime > 0) {
                        setTimeout(() => {
                            $resultDiv.html(response);
                        }, remainingTime);
                    } else {
                        $resultDiv.html(response);
                    }
                },
                error: function () {
                    $resultDiv.html('<p style="padding: 10px;text-align: center;color: red;">Search failed. Please try again.</p>');
                }
            });
        }, 500);
    });
}
setupSearch('#departmentsearch', 'departmentsearch', '#department-list-div');
setupSearch('#subdepartmentsearch', 'subdepartmentsearch', '#subdepartment-list-div');
setupSearch('#categorysearch', 'categorysearch', '#category-list-div');
setupSearch('#productsearch', 'productsearch', '#product-list-div');
setupSearch('#subproductsearch', 'subproductsearch', '#subproduct-list-div');
setupSearch('#specializationsearch', 'specializationsearch', '#specialization-list-div');
//------------ End Search product and department and subproduct and subdepartment ---------------

//------------ Fetch Fetch searched designation -----------------
$(document).on("input", '#search_designation', function () {
    const query = $(this).val();
    if (query.length >= 1) {
        $('#select-designation-div').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
        $.ajax({
            url: '../employer_flow/search_designation.php',
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
    fetchFilteredJobs(1);
});
// Target remover
$(document).on("click", function (event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div').length && !$target.is('#search_designation')) {
        $('#select-designation-div').hide();
    }
});
//------------ End Fetch searched designation -----------------

//------------ Fetch searched designation For Mobile -----------------
$(document).on("input", '#search_designation-m', function () {
    const query = $(this).val();
    if (query.length >= 1) {
        $('#select-designation-div-m').html('<ul id="searching-item"><li style="text-align:center;">Searching...</li></ul>').show();
        $.ajax({
            url: '../employer_flow/search_designation.php',
            type: 'POST',
            data: { query: query },
            success: function (response) {
                if ($.trim(response)) {
                    $("#select-designation-div-m").show().html(response);
                } else {
                    $("#select-designation-div-m").hide().html("");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
            }
        });
    } else {
        $("#select-designation-div-m").hide().html("");
    }
});
// Set input value and trigger function 
$(document).on("click", '#list_designation', function () {
    const label = $(this).text();
    $('#search_designation-m').val(label);
    addBadge(label, "designation", label);
    $('#select-designation-div-m').hide();
    $('#search_designation-m').val('');
    fetchFilteredJobs(1);
});
// Target remover
$(document).on("click", function (event) {
    const $target = $(event.target);
    if (!$target.closest('#select-div').length && !$target.is('#search_designation-m')) {
        $('#select-designation-div-m').hide();
    }
});
//------------ End Fetch searched designation For Mobile -----------------



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

    //    document.querySelector('.clear-all-filter').style.display = 'block';
}
// ------------ End Fetch selected values -----------

// ------------- Sort by filter --------------
$(document).on('change', 'input[name="sort"]', function () {
    const selected = this;
    const label = $(selected).data('name');

    // Uncheck all except the current one
    $('input[name="sort"]').each(function () {
        if (this !== selected) {
            this.checked = false;
            const otherLabel = $(this).data('name');
            removeBadge(this.value, "sort", otherLabel);
        }
    });

    // Add badge for the selected one
    if (selected.checked) {
        addBadge(selected.value, "sort", label);
    } else {
        removeBadge(selected.value, "sort", label);
    }

    fetchFilteredJobs(1);
});

// ------------- Sort by filter end --------------


// ----------- Deparments Products Sub Products,Departments Filter -------
$(document).on('change', 'input[name="department"]', function () {
    const label = $(this).data('name');
    if (this.checked) {
        addBadge(this.value, "department", label);
    } else {
        removeBadge(this.value, "department", label)
    }
    fetchFilteredJobs(1);
});
$(document).on('change', 'input[name="subdepartment"]', function () {
    const label = $(this).data('name');
    if (this.checked) {
        addBadge(this.value, "subdepartment", label);
    } else {
        removeBadge(this.value, "subdepartment", label)
    }
    fetchFilteredJobs(1);
});
$(document).on('change', 'input[name="category"]', function () {
    const label = $(this).data('name');
    if (this.checked) {
        addBadge(this.value, "category", label);
    } else {
        removeBadge(this.value, "category", label)
    }
    fetchFilteredJobs(1);
});
$(document).on('change', 'input[name="product"]', function () {
    const label = $(this).data('name');
    if (this.checked) {
        addBadge(this.value, "product", label);
    } else {
        removeBadge(this.value, "product", label)
    }
    fetchFilteredJobs(1);
});
$(document).on('change', 'input[name="subproduct"]', function () {
    const label = $(this).data('name');
    if (this.checked) {
        addBadge(this.value, "subproduct", label);
    } else {
        removeBadge(this.value, "subproduct", label)
    }
    fetchFilteredJobs(1);
});
$(document).on('change', 'input[name="specialization"]', function () {
    const label = $(this).data('name');
    if (this.checked) {
        addBadge(this.value, "specialization", label);
    } else {
        removeBadge(this.value, "specialization", label)
    }
    fetchFilteredJobs(1);
});

// Function to fetch jobs with all filters
function fetchFilteredJobs(page = 1) {
    const filters = collectFilters();
    const values = getSelectedKeysValues();
    values.forEach(({ key, value }) => {
        if (!filters[`${key}[]`]) {
            filters[`${key}[]`] = [];
        }
        filters[`${key}[]`].push(value);
    });
    filters.page = page;
    // console.log("Sending filters:", filters);
    showLoader();
    $.ajax({
        url: fetchJobUrl,
        type: 'POST',
        data: filters,
        success: function (response) {
            $('#job-list').html(response);
            hideLoader();
        },
        error: function (xhr, status, error) {
            console.error("Filter request failed:", error);
            $('#job-list').html('<p class="text-danger">Failed to load jobs. Please try again later.</p>');
            hideLoader();
        }
    });
}

// Event bindings
$(document).ready(function () {
    fetchFilteredJobs();

    // Web filters
    $(document).on('change', 'input[type="checkbox"]', function () {
        fetchFilteredJobs(1);
    });

    $('#other-department, #other-sub-department, #other-product, #other-sub-product, #other-designation').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchFilteredJobs(1);
        }, 500));
    });

    $('#jobsort, #jobdepartment, #job-sub-department,#job-category, #job-product,#job-sub-product,#job-specialization, #location, #joblocation, #per-page-list').on('change', function () {
        fetchFilteredJobs(1);
    });

    $('#min_salary, #max_salary, #jobmin_salary, #jobmax_salary').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchFilteredJobs(1);
        }, 500));
    });

    $('#search, #searchjob').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(function () {
            fetchFilteredJobs(1);
        }, 300));
    });

    // Pagination
    $(document).on('click', '.linkForPage', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        fetchFilteredJobs(page);
    });

    // Mobile filter checkboxes
    $('#mobileFilterPopup input[type="checkbox"]').on('change', function () {
        fetchFilteredJobs(1);
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

// --------------- Mobile Filter Popup ---------------
// document.addEventListener('DOMContentLoaded', function () {
//     const popup = document.getElementById('mobileFilterPopup');
//     const filterBtn = document.querySelector('#filters-section-mobile button');
//     const closeBtn = document.getElementById('closeFilterPopup');
//     const clearBtn = document.getElementById('clearFiltersBtn');
//     const applyBtn = document.getElementById('applyFiltersBtn');
//     // Show popup
//     filterBtn.addEventListener('click', () => popup.classList.remove('d-none'));
//     // Close popup
//     closeBtn.addEventListener('click', () => popup.classList.add('d-none'));
//     // Clear filters
//     clearBtn.addEventListener('click', () => {
//         popup.querySelectorAll('input[type=checkbox]').forEach(chk => chk.checked = false);
//     });
//     // Apply filters
//     applyBtn.addEventListener('click', () => {
//         // Trigger your filter logic here...
//         popup.classList.add('d-none');
//     });
//     // Handle category tab switching
//     document.querySelectorAll('.filter-categories .list-group-item').forEach(item => {
//         item.addEventListener('click', function () {
//             document.querySelectorAll('.filter-categories .list-group-item').forEach(li => li.classList.remove('active'));
//             this.classList.add('active');

//             const category = this.dataset.category;
//             document.querySelectorAll('#filterOptionsContent > div').forEach(content => {
//                 content.classList.toggle('d-none', content.dataset.content !== category);
//             });
//         });
//     });
// });