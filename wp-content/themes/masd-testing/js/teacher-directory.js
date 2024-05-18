jQuery(document).ready(function($) {
    // Check if the current page URL includes '/teachers'
    if (window.location.pathname.includes('/teachers')) {
        console.log('This is a Teachers page.');

        // Variables to store data about teachers and state management
        let podData = []; // Array to store fetched teacher data
        let currentSiteId = teacherData.current_site_id; // Get the current site ID from global teacherData
        console.log('Current Site ID retrieved:', currentSiteId);

        // Add a check to log the type and value of currentSiteId
        console.log('Type of currentSiteId:', typeof currentSiteId);

        // Ensure currentSiteId is an integer
        currentSiteId = parseInt(currentSiteId, 10);
        console.log('Parsed Current Site ID as integer:', currentSiteId);

        // Set the number of teachers to display per page based on the current site ID.
        // If the current site ID is 1, set teachersPerPage to 80. Otherwise, set it to 50.
        let teachersPerPage = currentSiteId === 1 ? 80 : 50;
        console.log('Teachers to display per page:', teachersPerPage);

        // State object for managing the pagination and initial load state
        let state = {
            currentPage: 1, // Track the current page being displayed
            totalCount: 0, // Total number of teachers available
            isInitialLoad: true, // Flag to indicate if it's the initial load
            searchQuery: '' // Store the current search query
        };
        console.log('Initial state set:', state);

        // UI utility functions to manage display elements
        const ui = {
            showLoading: () => $('#loadingMessage').show(), // Show loading message
            hideLoading: () => $('#loadingMessage').hide(), // Hide loading message
            showLoadingSearch: () => $('#loadingSearch').show(), // Show search loading message
            hideLoadingSearch: () => $('#loadingSearch').hide(), // Hide search loading message
            updateDisplay: (content) => $('.threeColumn').append(content), // Add content to the display area
            clearDisplay: () => $('.threeColumn').empty(), // Clear the display area
            showNoResultsMessage: () => $('.threeColumn').before('<div class="no-results">No teachers found.</div>'), // Show no results message
            hideNoResultsMessage: () => $('.no-results').remove(), // Hide no results message
            appendReturnTopButton: () => {
                // Add a "Return to Top" button if it doesn't exist
                if ($('#scrollToTopButton').length === 0) {
                    $('.threeColumn').after('<button id="scrollToTopButton" class="to-top-button" style="display: none;">Return to Top</button>');
                }
            },
            showReturnTopButton: () => $('#scrollToTopButton').css('display', 'block'), // Show "Return to Top" button
            appendLoadMoreButton: () => {
                // Add a "Load More Teachers" button if it doesn't exist
                if ($('#loadMoreTeachersButton').length === 0) {
                    $('.threeColumn').after('<button id="loadMoreTeachersButton" class="load-more-button">Load More Teachers</button>');
                }
            },
            showLoadMoreButton: () => $('#loadMoreTeachersButton').show(), // Show "Load More Teachers" button
            hideLoadMoreButton: () => $('#loadMoreTeachersButton').hide(), // Hide "Load More Teachers" button
            appendLoadingMessage: () => {
                // Add a loading message if it doesn't exist
                if ($('#loadingMessage').length === 0) {
                    $('.threeColumn').after('<div id="loadingMessage" style="text-align:center"><img src="/wp-content/uploads/2024/04/loading.gif" /></div>');
                }
            },
            showLoadingMessage: () => $('#loadingMessage').show(), // Show loading message
            hideLoadingMessage: () => $('#loadingMessage').hide() // Hide loading message
        };

        // Generate HTML for each teacher entry
        function generatePodHtml(teachers) {
            // Create HTML for each teacher in the array
            return teachers.map(teacher => `
                <div class="threeColumnSingle">
                    <img class='teacherPhoto' loading='lazy' src="${teacher.image || '/wp-content/uploads/2024/04/no_image_available-1.jpeg'}" alt="${teacher.first_name} ${teacher.last_name} Image"><br>
                    <h2>${teacher.first_name || 'N/A'} ${teacher.last_name || 'N/A'}</h2>
                    <a href='mailto:${teacher.email}'>${teacher.email}</a><br>
                    <p class="teacher-school">${teacher.school}</p>
                    <p>${formatList(teacher.grade)} ${formatList(teacher.subject)}</p>
                </div>
            `).join('');
        }

        // Helper function to format arrays as comma-separated strings
        function formatList(items) {
            // Join array items into a comma-separated string, handle null or empty arrays
            return items && Array.isArray(items) ? items.join(', ') : items || '';
        }

        // Fetch the total count of teachers
        function fetchTotalCount(searchQuery = '') {
            console.log('Fetching total number of teachers for query:', searchQuery);
            ui.showLoading();
            state.isInitialLoad = !searchQuery; // Update initial load flag based on query presence
            console.log('Updated state for total count fetch:', state);
            $.ajax({
                url: teacherData.ajax_url, // URL to fetch the data from
                type: 'GET', // Request type
                headers: {'X-WP-Nonce': teacherData.nonce}, // WordPress security nonce for AJAX
                dataType: 'json', // Expected data type
                data: { per_page: 1, search: searchQuery }, // Query parameters to get total count
                success: handleTotalCountResponse, // Function to handle success response
                error: handleAjaxError // Function to handle error response
            });
        }

        // Handle the response for the total count fetch
        function handleTotalCountResponse(data, status, xhr) {
            state.totalCount = parseInt(xhr.getResponseHeader('X-WP-Total')); // Parse total count from response headers
            console.log('Total number of teachers fetched:', state.totalCount);
            if (state.totalCount > 0) {
                fetchPage(state.searchQuery); // Fetch the first page of teachers
            } else {
                console.log('No teachers found for the given query.');
                ui.showNoResultsMessage(); // Show no results message if count is 0
                ui.hideLoadingSearch(); // Hide the search loading indicator
                $('#searchContainer, #resetButton').show(); // Show search and reset buttons
            }
        }

        // Fetch a specific page of teachers
        function fetchPage(searchQuery = '') {
            console.log('Fetching page:', state.currentPage, 'for query:', searchQuery);
            ui.showLoadingMessage(); // Show loading message when fetching a page
            $.ajax({
                url: teacherData.ajax_url, // URL to fetch the data from
                type: 'GET', // Request type
                headers: {'X-WP-Nonce': teacherData.nonce}, // WordPress security nonce for AJAX
                dataType: 'json', // Expected data type
                data: { page: state.currentPage, per_page: teachersPerPage, search: searchQuery }, // Query parameters to get teachers data
                success: handlePageResponse, // Function to handle success response
                error: handleAjaxError // Function to handle error response
            });
        }

        // Handle the response for the page fetch
        function handlePageResponse(data) {
            console.log('Page data received:', data);
            if (data.length > 0) {
                podData = podData.concat(data); // Add new data to podData array
                displayPodData(data); // Display the new teachers
                if (state.currentPage * teachersPerPage < state.totalCount) {
                    state.currentPage++;
                    console.log('More teachers available, showing Load More button.');
                    ui.showLoadMoreButton(); // Show the Load More button if more teachers are available
                } else {
                    console.log('All teachers loaded, hiding Load More button.');
                    ui.hideLoadMoreButton(); // Hide the Load More button if all teachers are loaded
                }
                ui.hideLoadingSearch(); // Hide the search loading indicator once the page data is loaded
                ui.hideLoadingMessage(); // Hide the loading message
                $('#searchContainer, #resetButton').show();
            } else {
                console.log('No more teachers found.');
                ui.showNoResultsMessage(); // Show no results message if no teachers are found
                ui.hideLoadingSearch(); // Hide the search loading indicator
                ui.hideLoadingMessage(); // Hide the loading message
                $('#searchContainer, #resetButton').show(); // Show search and reset buttons
            }
        }

        // Handle AJAX errors
        function handleAjaxError(jqXHR, textStatus, errorThrown) {
            console.log(`AJAX Error occurred: ${textStatus}: ${errorThrown}`); // Log error details
            ui.hideLoadingSearch(); // Hide the search loading indicator
            ui.hideLoadingMessage(); // Hide the loading message
            ui.showNoResultsMessage(); // Show no results message in case of error
            $('#searchContainer, #resetButton').show(); // Show search and reset buttons
        }

        // Display the fetched teacher data
        function displayPodData(newTeachers) {
            console.log('Displaying new teachers:', newTeachers);
            let podDataHtml = generatePodHtml(newTeachers); // Generate HTML for new teachers
            ui.updateDisplay(podDataHtml); // Update the display with new HTML
            ui.appendReturnTopButton(); // Check and append 'Return to Top' button if not already present
            ui.showReturnTopButton(); // Ensure 'Return to Top' button is visible
            ui.appendLoadMoreButton(); // Check and append 'Load More Teachers' button if not already present
        }

        // Reset the search and fetch new data
        function resetAndFetch(searchQuery = '') {
            console.log('Resetting and fetching new data for query:', searchQuery);
            state.currentPage = 1; // Reset to the first page
            podData = []; // Clear the podData array
            ui.hideNoResultsMessage(); // Hide any existing no results message
            ui.clearDisplay(); // Clear the current display
            state.searchQuery = searchQuery; // Update current search query
            fetchTotalCount(searchQuery); // Fetch the total count for the new query
            ui.hideLoading(); // Hide the initial loading indicator
            ui.showLoadingSearch(); // Show loading indicator for search
            ui.hideLoadMoreButton(); // Hide the Load More button when reset
            $('#searchContainer, #resetButton').hide(); // Hide search and reset buttons
        }

        // Event Handlers

        // Handle click on the "Return to Top" button
        $(document).on('click', '#scrollToTopButton', function() {
            $('html, body').animate({scrollTop: 0}, 'slow'); // Smooth scroll to top of page
        });

        // Handle click on the "Load More Teachers" button
        $(document).on('click', '#loadMoreTeachersButton', function() {
            console.log('Load More Teachers button clicked.');
            fetchPage(state.searchQuery); // Fetch the next page of teachers when 'Load More Teachers' button is clicked
        });

        // Handle Enter key press in the search input field
        $(document).on('keypress', '#teacherSearchInput', (event) => {
            if (event.which == 13) { // Detect 'Enter' keypress
                event.preventDefault();
                var searchQuery = $('#teacherSearchInput').val().trim().toLowerCase();
                console.log('Search query entered:', searchQuery);
                resetAndFetch(searchQuery); // Reset and fetch new data based on the search query
            }
        });

        // Handle click on the search button
        $('#searchButton').click(() => {
            var searchQuery = $('#teacherSearchInput').val().trim().toLowerCase();
            console.log('Search button clicked, query:', searchQuery);
            resetAndFetch(searchQuery); // Reset and fetch new data based on the search query
        });

        // Handle click on the reset button
        $('#resetButton').click(() => {
            console.log('Reset button clicked.');
            resetAndFetch(); // Reset without a search query
        });

        console.log('Initiating data fetch on page load.');
        fetchTotalCount(); // Initiate data fetch on page load
    }
});
