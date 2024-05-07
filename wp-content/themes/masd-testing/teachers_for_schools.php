<?php
global $blog_id;
$current_blog_details = get_blog_details($blog_id);
$main_site_name = $current_blog_details->blogname;
$blog_id = get_current_blog_id();
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    var nonce = '<?php echo wp_create_nonce("wp_rest"); ?>'; // Ensure nonce is declared in this scope
    var ajaxUrl = "<?php echo rest_url("pods/teacher/"); ?>"; // Consistently use ajaxUrl
    var podData = []; // Define podData at a higher scope
    var teachersPerPage = 22; // Define this variable at a higher scope to ensure it's accessible
    var currentTeachersDisplayed = teachersPerPage; // Track the number of teachers currently displayed

    function fetchPodData(page, searchQuery = '') {
        $('#loadingMessage').show();
        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                page: page,
                per_page: 100,
                nonce: nonce,   // Include the nonce in the data object
                search: searchQuery
            },
            success: function(data) {
                console.log("Data received:", data);  // Log the received data
                podData = data;
                displayPodData(currentTeachersDisplayed);
                $('#loadingMessage').hide();
                $('#searchContainer').css('display', 'block');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('AJAX Error: ' + textStatus + ': ' + errorThrown);
                $('#loadingMessage').hide();
                $('.threeColumn').html('<p>Error fetching teacher data.</p>');
            }
        });
    }

    function displayPodData(numTeachers) {
        $('.threeColumn').empty();
        var podDataHtml = '';
        podData.sort(function(a, b) {
            var nameA = a.last_name || ''; // Provide an empty string as a fallback
            var nameB = b.last_name || ''; // Provide an empty string as a fallback
            return nameA.localeCompare(nameB);
        });

        for (var i = 0; i < numTeachers && i < podData.length; i++) {
            var object = podData[i];
            var imageUrl = object.image && object.image.guid ? object.image.guid : 'http://masd.local/wp-content/uploads/2024/04/no_image_available-1.jpeg';
            var firstName = object.first_name || 'N/A';
            var lastName = object.last_name || 'N/A';
            var email = object.email || 'no-email@example.com';
            var grades = Array.isArray(object.grade) ? object.grade.join(', ') : object.grade || ''; // Provide an empty string as a fallback;
            var subjects = Array.isArray(object.subject) ? object.subject.join(', ') : object.subject || ''; // Provide an empty string as a fallback

            podDataHtml += '<div class="threeColumnSingle">';
            podDataHtml += '<img class=\'teacherPhoto\' loading=\'lazy\' src="' + imageUrl + '" alt="' + firstName + ' ' + lastName + ' Image"><br>';
            podDataHtml += '<h2>' + firstName + ' ' + lastName + '</h2>';
            podDataHtml += '<a href=\'mailto:' + email + '\'>' + email + '</a><br>';
            podDataHtml += '<p>' + grades + ' ' + subjects + '</p>';
            podDataHtml += '</div>';
        }

        $('.threeColumn').html(podDataHtml);
        if (podData.length > numTeachers) {
            $('.threeColumn').append('<div class="loadMoreContainer"><button id="loadMoreButton">Load More Teachers</button></div>');
        }
    }

    fetchPodData(1); // Fetch data initially on page load

    $(document).on('click', '#loadMoreButton', function() {
        currentTeachersDisplayed += teachersPerPage;
        displayPodData(currentTeachersDisplayed);
    });

    $('#teacherSearchInput').keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            searchTeacher();
        }
    });

    function searchTeacher() {
        var searchQuery = $('#teacherSearchInput').val().trim();
        currentTeachersDisplayed = teachersPerPage; // Reset the number of teachers displayed
        fetchPodData(1, searchQuery); // Always start from the first page when searching
    }

    function resetPage() {
        $('#teacherSearchInput').val('');
        currentTeachersDisplayed = teachersPerPage; // Reset the number of teachers displayed
        fetchPodData(1); // Reset and fetch initial data again
    }

    // Bind click events for search and reset buttons using jQuery
    $(document).on('click', '#searchButton', function() {
        searchTeacher();
    });

    $(document).on('click', '#resetButton', function() {
        resetPage();
    });

});

var mainSiteName = "<?php echo $main_site_name; ?>"; // Define this outside the jQuery ready function if it doesn't depend on DOM elements

</script>

<div id="loadingMessage" style="text-align:center">
    <img src="http://masd.local/wp-content/uploads/2024/04/loading.gif" />
</div>

<div id="searchContainer" style="text-align:center; margin-bottom:20px; display: none;">
    <input type="text" id="teacherSearchInput" placeholder="Search by name..." />
    <button id="searchButton" class="searchButton">Search</button>
    <button id="resetButton" class="resetButton">Reset</button>
</div>

<!-- <div class="threeColumn"></div> -->

<?php restore_current_blog(); // Ensure you switch back after your operations are done ?>
