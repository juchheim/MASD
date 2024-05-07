// Ensure jQuery is ready after the document has loaded
jQuery(document).ready(function($) {
    // Check if we are on the teachers page
    if (window.location.pathname.includes('/teachers')) {
        // Initialize variables for managing the state of the application
        var podData = []; // Array to store teacher data
        var teachersPerPage = 20; // Number of teachers to fetch per page
        var currentTeachersDisplayed = teachersPerPage; // Counter for displayed teachers
        var currentPage = 1; // Start fetching from the first page

        // Fetch the total count of teachers to initialize the loading process
        function fetchTotalCount(searchQuery = '') {
            $.ajax({
                url: teacherData.ajax_url, // Endpoint to fetch teacher data
                type: 'GET', // HTTP method
                headers: {
                    'X-WP-Nonce': teacherData.nonce // Security nonce for WordPress REST API
                },
                dataType: 'json', // Expected data type of the response
                data: {
                    per_page: 1, // Fetch only headers to get the total count
                    search: searchQuery // Optional search query
                },
                success: function(data, status, xhr) {
                    // Parse total count from response headers
                    var totalCount = parseInt(xhr.getResponseHeader('X-WP-Total'));
                    console.log("Total number of teachers:", totalCount);
                    // Fetch the first page of teachers
                    fetchPage(totalCount, searchQuery);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error fetching total count:', textStatus + ': ' + errorThrown);
                }
            });
        }

        // Fetch a single page of teachers
        function fetchPage(totalCount, searchQuery = '') {
            console.log(`Attempting to fetch page ${currentPage} with ${teachersPerPage} teachers per page.`);
            // Check if all pages have been fetched
            if (currentPage > Math.ceil(totalCount / teachersPerPage)) {
                console.log("All pages fetched.");
                return;
            }

            $.ajax({
                url: teacherData.ajax_url,
                type: 'GET',
                headers: {
                    'X-WP-Nonce': teacherData.nonce
                },
                dataType: 'json',
                data: {
                    page: currentPage,
                    per_page: teachersPerPage,
                    search: searchQuery
                },
                success: function(data) {
                    console.log(`Successfully fetched ${data.length} teachers for page ${currentPage}.`);
                    podData = podData.concat(data); // Store fetched data
                    displayPodData(data.length, totalCount); // Display fetched teachers
                    $('#loadingMessage').hide(); // Hide the loading message
                    $('.loadMoreContainer').remove(); // Remove existing Load More button
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(`AJAX Error on page ${currentPage}: ${textStatus}: ${errorThrown}`);
                    $('#loadingMessage').hide(); // Hide loading message on error
                }
            });
        }
        
        // Display fetched teacher data on the page
        function displayPodData(numNewTeachers, totalCount) {
            $('.loadMoreContainer').remove(); // Remove existing Load More button to prevent duplicates

            var startIndex = podData.length - numNewTeachers; // Calculate starting index for new data
            var podDataHtml = '';

            // Generate HTML for each new teacher
            for (var i = startIndex; i < podData.length; i++) {
                var object = podData[i];
                var imageUrl = object.image && object.image.guid ? object.image.guid : 'http://masd.local/wp-content/uploads/2024/04/no_image_available-1.jpeg';
                var firstName = object.first_name || 'N/A';
                var lastName = object.last_name || 'N/A';
                var email = object.email || '';
                var grades = Array.isArray(object.grade) ? object.grade.join(', ') : object.grade || '';
                var subjects = Array.isArray(object.subject) ? object.subject.join(', ') : object.subject || '';

                podDataHtml += '<div class="threeColumnSingle">';
                podDataHtml += '<img class=\'teacherPhoto\' loading=\'lazy\' src="' + imageUrl + '" alt="' + firstName + ' ' + lastName + ' Image"><br>';
                podDataHtml += '<h2>' + firstName + ' ' + lastName + '</h2>';
                podDataHtml += '<a href=\'mailto:' + email + '\'>' + email + '</a><br>';
                podDataHtml += '<p>' + grades + ' ' + subjects + '</p>';
                podDataHtml += '</div>';
            }

            $('.threeColumn').append(podDataHtml); // Append the new HTML to the page

            // Append Load More button if there are more pages to fetch
            if (currentPage * teachersPerPage < totalCount) {
                $('.threeColumn').append('<div class="loadMoreContainer"><button id="loadMoreButton">Load More Teachers</button></div>');
                console.log("New teachers added to the page.");
            }
        }

        // Event handlers for interactive elements
        $('#teacherSearchInput').keypress(function(event) {
            if (event.which == 13) { // Enter key pressed
                event.preventDefault();
                searchTeacher();
            }
        });

        $(document).on('click', '#searchButton', function() {
            searchTeacher();
        });

        $(document).on('click', '#resetButton', function() {
            resetPage();
        });

        $(document).on('click', '#loadMoreButton', function() {
            currentPage++; // Increment the page count
            fetchPage(); // Fetch the next page
        });

        // Function to handle search queries
        function searchTeacher() {
            var searchQuery = $('#teacherSearchInput').val().trim();
            currentPage = 1; // Reset to the first page
            currentTeachersDisplayed = teachersPerPage;
            podData = []; // Clear existing data
            fetchTotalCount(searchQuery);
        }

        // Function to reset the page and fetch all teachers again
        function resetPage() {
            $('#teacherSearchInput').val('');
            currentPage = 1; // Reset to the first page
            currentTeachersDisplayed = teachersPerPage;
            podData = []; // Clear existing data
            fetchTotalCount();
        }

        fetchTotalCount(); // Initial fetch to load data
    }
});
