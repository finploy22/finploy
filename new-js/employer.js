// Show pagination after a delay
window.addEventListener("load", function () {
    setTimeout(function () {
        document.querySelector(".pagination-div").style.display = "block";
    }, 900);
});


// for while clik input open dropdown 
function activateSearch(inputId, listDivId, arrowSelector) {
    $(`#${inputId}`).on('focus', function () {
        const content = $(`#${listDivId}`);
        content.addClass('active').css('max-height', '200px');

        const arrowImg = $(arrowSelector);
        arrowImg.attr('src', '../assets/upward-arrow.svg');
    });
}
// Apply to all search boxes
activateSearch('departmentsearch', 'department-list-div', '#department-list-head .accordion-arrow');
activateSearch('subdepartmentsearch', 'subdepartment-list-div', '.filter-accordion.subdepart-head .accordion-arrow');
activateSearch('categorysearch', 'category-list-div', '.filter-accordion.cate-head .accordion-arrow');
activateSearch('productsearch', 'product-list-div', '.filter-accordion.product-head .accordion-arrow');
activateSearch('subproductsearch', 'subproduct-list-div', '.filter-accordion.subpro-head .accordion-arrow');
activateSearch('specializationsearch', 'specialization-list-div', '.filter-accordion.specia-head .accordion-arrow');

// checkbox need to work like radio 
document.querySelectorAll('input[name="sort"]').forEach((checkbox) => {
    checkbox.addEventListener('change', function () {
        if (this.checked) {
            document.querySelectorAll('input[name="sort"]').forEach((other) => {
                if (other !== this) other.checked = false;
            });
        }
    });
});

// ----------- Filter Toggle ------------
$(document).ready(function () {
    // Ensure all accordions start closed
    $('.filter-accordion').removeClass('active');
    $('.filter-content').removeClass('active').css('max-height', 0);
    $('.filter-accordion .accordion-arrow').attr('src', '../assets/downward-arrow.svg');
    // Toggle filter accordion sections
    $('.filter-accordion').on('click', function () {
        $(this).toggleClass('active');
        const content = $(this).next('.filter-content');
        const arrowImg = $(this).find('.accordion-arrow');
        content.toggleClass('active');
        if (content.hasClass('active')) {
            content.css('max-height', "200px");
            arrowImg.attr('src', '../assets/upward-arrow.svg');
        } else {
            content.css('max-height', 0);
            arrowImg.attr('src', '../assets/downward-arrow.svg');
        }
    });
});


// /////////// Copy text Functions //////////
window.onload = function () {
    setTimeout(() => {  // Give the DOM a little extra time to load
        let buttons = document.querySelectorAll(".copy-btn");

        if (buttons.length === 0) {
            //   console.warn("No buttons found. Ensure HTML is loaded properly.");
            return;
        }

        buttons.forEach(button => {
            button.addEventListener("click", async function () {
                let textToCopy = this.getAttribute("data-value");
                try {
                    await navigator.clipboard.writeText(textToCopy);
                    alert("Copied: " + textToCopy);
                } catch (err) {
                    console.error("Copy failed:", err);
                    alert("Failed to copy!");
                }
            });
        });
    }, 500); // Small delay to ensure elements exist
};