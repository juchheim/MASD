jQuery(document).ready(function($) {
    if (window.location.pathname.includes('/teachers')) {
        var nonce = "<?php echo wp_create_nonce('wp_rest'); ?>";
        var ajaxUrl = "<?php echo rest_url('pods_teacher_sort/v1/teachers'); ?>"; 
        var podData = []; 
        var teachersPerPage = 50; 
        var currentTeachersDisplayed = teachersPerPage;
        var currentPage = 1; // Start with the first page

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
                    var totalCount = parseInt(xhr.getResponseHeader('X-WP-Total'));
                    console.log("Total number of teachers:", totalCount);
                    fetchPage(totalCount, searchQuery); // Fetch only the first page
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error fetching total count:', textStatus + ': ' + errorThrown);
                }
            });
        }

        function fetchPage(totalCount, searchQuery = '') {
            console.log(`Attempting to fetch page ${currentPage} with ${teachersPerPage} teachers per page.`);
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
                    podData = podData.concat(data); // Concatenate new data with existing podData
                    displayPodData(data.length, totalCount); // Display only the new data
                    $('#loadingMessage').hide();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(`AJAX Error on page ${currentPage}: ${textStatus}: ${errorThrown}`);
                    $('#loadingMessage').hide();
                }
            });
        }
        
        function displayPodData(numNewTeachers, totalCount) {
            $('.loadMoreContainer').remove(); // Remove existing Load More button to prevent duplicates
        
            var startIndex = podData.length - numNewTeachers; // Calculate starting index for new teachers
            var podDataHtml = '';
        
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
        
            $('.threeColumn').append(podDataHtml);
        
            // Append Load More button if there are more pages to fetch
            if (currentPage * teachersPerPage < totalCount) {
                $('.threeColumn').append('<div class="loadMoreContainer"><button id="loadMoreButton">Load More Teachers</button></div>');
                console.log("New teachers added to the page.");
            }
        }
        

        fetchTotalCount(); // Initial fetch

        $('#teacherSearchInput').keypress(function(event) {
            if (event.which == 13) {
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

        function searchTeacher() {
            var searchQuery = $('#teacherSearchInput').val().trim();
            currentPage = 1; // Reset to the first page
            currentTeachersDisplayed = teachersPerPage;
            podData = []; // Clear existing data
            fetchTotalCount(searchQuery);
        }

        function resetPage() {
            $('#teacherSearchInput').val('');
            currentPage = 1; // Reset to the first page
            currentTeachersDisplayed = teachersPerPage;
            podData = []; // Clear existing data
            fetchTotalCount();
        }
    }
});
