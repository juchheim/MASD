jQuery(document).ready(function($) {
    // Check if the current page URL includes '/teachers'
    if (window.location.pathname.includes('/teachers')) {
        // Variables to store data about teachers and state management
        let podData = []; // Array to store fetched teacher data
        let currentSiteId = teacherData.current_site_id; // Get the current site ID from global teacherData
        let teachersPerPage = currentSiteId === 1 ? 100 : 20; // Number of teachers per page, varies by site ID

        // State object for managing the pagination and initial load state
        let state = {
            currentPage: 1,
            totalCount: 0,
            isInitialLoad: true,
            searchQuery: '' // Empty string to store current search query
        };

        // UI utility functions to manage display elements
        const ui = {
            showLoading: () => $('#loadingMessage').show(),
            hideLoading: () => $('#loadingMessage').hide(),
            showLoadingSearch: () => $('#loadingSearch').show(),
            hideLoadingSearch: () => $('#loadingSearch').hide(),
            updateDisplay: (content) => $('.threeColumn').append(content),
            clearDisplay: () => $('.threeColumn').empty(),
            showNoResultsMessage: () => $('.threeColumn').before('<div class="no-results">No teachers found.</div>'),
            hideNoResultsMessage: () => $('.no-results').remove(),
            appendReturnTopButton: () => {
                if ($('#scrollToTopButton').length === 0) { // Check if the button doesn't already exist
                    $('.threeColumn').after('<button id="scrollToTopButton" class="to-top-button" style="display: none;">Return to Top</button>');
                }
            },
            showReturnTopButton: () => $('#scrollToTopButton').show()
        };
        

        // Fetch total count of teachers matching the search query
        function fetchTotalCount(searchQuery = '') {
            ui.showLoading();
            state.isInitialLoad = !searchQuery; // Update initial load flag based on query presence
            $.ajax({
                url: teacherData.ajax_url,
                type: 'GET',
                headers: {'X-WP-Nonce': teacherData.nonce}, // WordPress security nonce for AJAX
                dataType: 'json',
                data: { per_page: 1, search: searchQuery }, // Query data with minimal per_page to get total count
                success: handleTotalCountResponse,
                error: handleAjaxError
            });
        }

        // Handle response from total count fetch
        function handleTotalCountResponse(data, status, xhr) {
            state.totalCount = parseInt(xhr.getResponseHeader('X-WP-Total')); // Parse total count from response headers
            if (state.totalCount > 0) {
                fetchPage(state.searchQuery); // If teachers exist, fetch first page
            } else {
                ui.showNoResultsMessage(); // Show no results message if count is 0
                ui.hideLoading(); // Hide loading message
            }
        }

        // Fetch a page of teacher data
        function fetchPage(searchQuery = '') {
            $.ajax({
                url: teacherData.ajax_url,
                type: 'GET',
                headers: {'X-WP-Nonce': teacherData.nonce}, // WordPress security nonce for AJAX
                dataType: 'json',
                data: { page: state.currentPage, per_page: teachersPerPage, search: searchQuery },
                success: handlePageResponse,
                error: handleAjaxError
            });
        }

        function handlePageResponse(data) {
            if (data.length > 0) {
                podData = podData.concat(data);
                displayPodData(data.length);
                if (state.currentPage * teachersPerPage < state.totalCount) {
                    state.currentPage++;
                    fetchPage(state.searchQuery);
                    ui.hideLoading();
                } else {
                    ui.hideLoadingSearch(); // Hide the search loading indicator once all pages are loaded
                    $('#searchContainer, #resetButton').show();
                }
            } else {
                ui.showNoResultsMessage();
                ui.hideLoading();
                ui.hideLoadingSearch();
            }
        }

        // Display fetched teacher data
        function displayPodData(numNewTeachers) {
            let startIndex = podData.length - numNewTeachers; // Determine start index for new teachers
            let podDataHtml = generatePodHtml(startIndex); // Generate HTML for new teachers
            ui.updateDisplay(podDataHtml); // Update the display with new HTML
            if ($('#scrollToTopButton').length === 0) { // Append 'Return to Top' button if not already present
                $('.threeColumn').after('<button id="scrollToTopButton" class="to-top-button">Return to Top</button>');
            }
            $('#scrollToTopButton').show();
        }

        // Generate HTML for each teacher entry
        function generatePodHtml(startIndex) {
            return podData.slice(startIndex).map(teacher => `
                <div class="threeColumnSingle">
                    <img class='teacherPhoto' loading='lazy' src="${teacher.image || 'http://masd.local/wp-content/uploads/2024/04/no_image_available-1.jpeg'}" alt="${teacher.first_name} ${teacher.last_name} Image"><br>
                    <h2>${teacher.first_name || 'N/A'} ${teacher.last_name || 'N/A'}</h2>
                    <a href='mailto:${teacher.email}'>${teacher.email}</a><br>
                    <p class="teacher-school">${teacher.school}</p>
                    <p>${formatList(teacher.grade)} ${formatList(teacher.subject)}</p>
                </div>
            `).join(''); // Join all entries to form a single HTML string
        }

        // Helper function to format arrays as comma-separated strings
        function formatList(items) {
            return items && Array.isArray(items) ? items.join(', ') : items || '';
        }

        // Handle AJAX errors
        function handleAjaxError(jqXHR, textStatus, errorThrown) {
            console.log(`AJAX Error: ${textStatus}: ${errorThrown}`); // Log error details
            ui.hideLoading();
            ui.showNoResultsMessage(); // Show no results message in case of error
        }

        // Reset and fetch data based on new or updated search query
        function resetAndFetch(searchQuery = '') {
            state.currentPage = 1;
            podData = [];
            ui.hideNoResultsMessage(); // Hide any existing no results message
            ui.clearDisplay();
            state.searchQuery = searchQuery; // Update current search query
            fetchTotalCount(searchQuery);
            ui.hideLoading();
            ui.showLoadingSearch(); // Show loading indicator for search when reset
            $('#searchContainer, #resetButton').hide(); // Hide search and reset buttons
        }
        
        // Set up event handlers
        $(document).on('click', '#scrollToTopButton', function() {
            $('html, body').animate({scrollTop: 0}, 'slow'); // Smooth scroll to top of page
        });

        $(document).on('keypress', '#teacherSearchInput', (event) => {
            if (event.which == 13) { // Detect 'Enter' keypress
                event.preventDefault();
                var searchQuery = $('#teacherSearchInput').val().trim().toLowerCase();
                resetAndFetch(searchQuery);
            }
        });

        $('#searchButton').click(() => {
            var searchQuery = $('#teacherSearchInput').val().trim().toLowerCase();
            resetAndFetch(searchQuery);
        });

        $('#resetButton').click(() => resetAndFetch()); // Reset without a search query

        fetchTotalCount(); // Initiate data fetch on page load
    }
});
