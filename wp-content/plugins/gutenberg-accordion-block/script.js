document.addEventListener('DOMContentLoaded', function() {
    var accordions = document.querySelectorAll('.wp-block-gutenberg-accordion-block-main .accordion-item h3');

    accordions.forEach(function(accordion) {
        accordion.addEventListener('click', function() {
            // Close all other open accordions
            accordions.forEach(function(otherAccordion) {
                if (otherAccordion !== accordion) {
                    var otherContent = otherAccordion.nextElementSibling;
                    if (otherContent.style.display === "block") {
                        otherContent.style.display = "none";
                    }
                }
            });

            // Toggle the clicked accordion
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    });
});
