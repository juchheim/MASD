jQuery(document).ready(function($) {
    if (window.location.pathname.includes('/teachers')) {
        var podData = [];
        var teachersPerPage = 20; // Adjusted from 50 for demonstration
        var currentPage = 1;
        var totalCount = 0;
        var isInitialLoad = true; // Flag to check if it's initial load or a search operation

        // Fetch the total count of teachers to determine how many pages are needed
        function fetchTotalCount(searchQuery = '') {
            isInitialLoad = !searchQuery; // Set to false if there is a search query, true otherwise
            $.ajax({
                url: teacherData.ajax_url,
                type: 'GET',
                headers: {
                    'X-WP-Nonce': teacherData.nonce
                },
                dataType: 'json',
                data: {
                    per_page: 1,
                    search: searchQuery
                },
                success: function(data, status, xhr) {
                    totalCount = parseInt(xhr.getResponseHeader('X-WP-Total'));
                    console.log("Total number of teachers:", totalCount);
                    fetchPage(searchQuery); // Start fetching pages
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error fetching total count:', textStatus + ': ' + errorThrown);
                    $('#loadingMessage').hide();
                }
            });
        }

        // Fetches a page of teachers based on current page and total count
        function fetchPage(searchQuery = '') {
            console.log("Fetching page:", currentPage);
            console.log("Pages remaining:", Math.ceil(totalCount / teachersPerPage) - currentPage);

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
                    podData = podData.concat(data);
                    console.log("Page loaded:", currentPage);
                    displayPodData(data.length);
                    $('#loadingMessage').hide();

                    if (currentPage * teachersPerPage < totalCount && isInitialLoad) {
                        currentPage++; // Increment after successful fetch
                        fetchPage(searchQuery); // Continue fetching if it's initial load
                    } else {
                        // No more pages to fetch or search mode, make search and reset visible
                        $('#searchContainer').show();
                        $('#resetButton').show();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(`AJAX Error on page ${currentPage}: ${textStatus}: ${errorThrown}`);
                    $('#loadingMessage').hide();
                }
            });
        }

        // Display newly fetched teacher data
        function displayPodData(numNewTeachers) {
            var startIndex = podData.length - numNewTeachers;
            var podDataHtml = '';
            for (var i = startIndex; i < podData.length; i++) {
                var object = podData[i];
                var imageUrl = object.image || 'http://masd.local/wp-content/uploads/2024/04/no_image_available-1.jpeg';
                var firstName = object.first_name || 'N/A';
                var lastName = object.last_name || 'N/A';
                var email = object.email || '';
                var grades = Array.isArray(object.grade) ? object.grade.join(', ') : object.grade || '';
                var subjects = Array.isArray(object.subject) ? object.subject.join(', ') : object.subject || '';
                var school = object.school || '';

                podDataHtml += `<div class="threeColumnSingle">
                                    <img class='teacherPhoto' loading='lazy' src="${imageUrl}" alt="${firstName} ${lastName} Image"><br>
                                    <h2>${firstName} ${lastName}</h2>
                                    <a href='mailto:${email}'>${email}</a><br>
                                    <p>${school}</p>
                                    <p>${grades} ${subjects}</p>
                                </div>`;
            }
            $('.threeColumn').append(podDataHtml);
        }

        $('#teacherSearchInput').on('keypress', function(event) {
            if (event.which == 13) {  // Enter key pressed
                event.preventDefault();  // Prevent default action (form submission)
                var searchQuery = $(this).val().trim().toLowerCase();  // Get the trimmed and lowercased search query
                if (searchQuery) {  // Only proceed if the query is not empty
                    currentPage = 1;
                    podData = [];
                    $('.threeColumn').empty();
                    fetchTotalCount(searchQuery);  // Fetch the total count with the new search query
                }
            }
        });

        $('#searchButton').on('click', function() {
            var searchQuery = $('#teacherSearchInput').val().trim().toLowerCase();  // Get the trimmed and lowercased search query
            if (searchQuery) {  // Only proceed if the query is not empty
                currentPage = 1;
                podData = [];
                $('.threeColumn').empty();
                fetchTotalCount(searchQuery);  // Fetch the total count with the new search query
            }
        });

        $('#resetButton').on('click', function() {
            currentPage = 1;
            podData = [];
            $('.threeColumn').empty();
            fetchTotalCount();  // Reset page and fetch without search query
        });

        fetchTotalCount(); // Initial data fetch

        $('#scrollToTopButton').on('click', function() {
            $('html, body').animate({scrollTop: 0}, 'slow');
        });
    }
});
