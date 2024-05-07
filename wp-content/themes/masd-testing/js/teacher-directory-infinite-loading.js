jQuery(document).ready(function($) {
    if (window.location.pathname.includes('/teachers')) {
        var podData = []; 
        var teachersPerPage = 20;
        var currentPage = 1; 
        var totalCount = 0;

        // Fetch the total count of teachers to determine how many pages are needed
        function fetchTotalCount(searchQuery = '') {
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
                    fetchPage(searchQuery); 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error fetching total count:', textStatus + ': ' + errorThrown);
                }
            });
        }

        // Fetches a page of teachers based on current page and total count
        function fetchPage(searchQuery = '') {
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
                    podData = podData.concat(data);
                    displayPodData(data.length); 
                    $('#loadingMessage').hide();
                    currentPage++; // Increment after successful fetch
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(`AJAX Error on page ${currentPage}: ${textStatus}: ${errorThrown}`);
                    $('#loadingMessage').hide();
                }
            });
        }

        // Display newly fetched teacher data and manages the Load More button
        function displayPodData(numNewTeachers) {
            var startIndex = podData.length - numNewTeachers;
            var podDataHtml = '';

            for (var i = startIndex; i < podData.length; i++) {
                var object = podData[i];
                var imageUrl = object.image && object.image.guid ? object.image.guid : 'http://masd.local/wp-content/uploads/2024/04/no_image_available-1.jpeg';
                var firstName = object.first_name || 'N/A';
                var lastName = object.last_name || 'N/A';
                var email = object.email || '';
                var grades = Array.isArray(object.grade) ? object.grade.join(', ') : object.grade || '';
                var subjects = Array.isArray(object.subject) ? object.subject.join(', ') : object.subject || '';

                podDataHtml += `<div class="threeColumnSingle">
                                    <img class='teacherPhoto' loading='lazy' src="${imageUrl}" alt="${firstName} ${lastName} Image"><br>
                                    <h2>${firstName} ${lastName}</h2>
                                    <a href='mailto:${email}'>${email}</a><br>
                                    <p>${grades} ${subjects}</p>
                                </div>`;
            }

            $('.threeColumn').append(podDataHtml);

            // Check if more pages are needed, then append or remove the Load More button
            if (currentPage * teachersPerPage < totalCount) {
                $('.threeColumn').append('<div class="loadMoreContainer"><button id="loadMoreButton">Load More Teachers</button></div>');
            }
        }

        // Event Handlers for user interactions
        $('#teacherSearchInput').keypress(function(event) {
            if (event.which == 13) {  // Enter key pressed
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
            fetchPage();  // Fetch the next page
        });

        // Handles search query changes
        function searchTeacher() {
            var searchQuery = $('#teacherSearchInput').val().trim();
            currentPage = 1;
            podData = []; 
            fetchTotalCount(searchQuery);
        }

        // Resets the page content and reloads the data
        function resetPage() {
            $('#teacherSearchInput').val('');
            currentPage = 1;
            podData = []; 
            fetchTotalCount();
        }

        fetchTotalCount();  // Initial data fetch
    }
});
