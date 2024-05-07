<?php
    $main_site_name = $current_blog_details->blogname;
?>

<script>
    var mainSiteName = "<?php echo $main_site_name; ?>";
</script>

<div id="loadingMessage" style="text-align:center"><img src="http://masd.local/wp-content/uploads/2024/04/loading.gif" /></div>

<div id="searchContainer" style="text-align:center; margin-bottom:20px; display: none;">
    <input type="text" id="teacherSearchInput" placeholder="Search by name..." />
    <button onclick="searchTeacher()" class="searchButton">Search</button>
    <button onclick="resetPage()" class="resetButton">Reset</button>
</div>

<div class="threeColumn">

<?php
    switch_to_blog( 1 );
?>

<script>
$(document).ready(function() {
    var currentPage = 1;
    var podData = [];
    var schoolName = "<?php echo $current_blog_details->blogname; ?>";
    var siteName = "Mississippi Achievement School District";

    fetchPodData(currentPage); // Initial fetch

    $('#teacherSearchInput').keypress(function(event) {
        if (event.which == 13) {  // 13 is the keycode for Enter
            event.preventDefault();  // Prevent the default form submit behavior
            searchTeacher();
        }
    });

    function fetchPodData(page, searchQuery = '') {
        $('#loadingMessage').show();
        $.ajax({
            url: '/wp-json/pods/teacher',
            type: 'GET',
            dataType: 'json',
            data: {
                page: page,
                per_page: 100,
                search: searchQuery
            },
            success: function(data, textStatus, jqXHR) {
                var totalPages = parseInt(jqXHR.getResponseHeader('X-WP-TotalPages'));
                if (page === 1) {
                    podData = [];  // Clear existing data
                    $('.threeColumn').empty(); // Clear previous results
                }
                podData = podData.concat(data);
                currentPage++;
                if (currentPage <= totalPages) {
                    fetchPodData(currentPage, searchQuery);
                } else {
                    podData.sort(function(a, b) {
                        return a.last_name.toUpperCase().localeCompare(b.last_name.toUpperCase());
                    });
                    displayPodData();
                    $('#loadingMessage').hide();
                    $('#searchContainer').css('display', 'block');  // Show search container after data is loaded
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#loadingMessage').hide();
                $('.threeColumn').html('<p>Error fetching teacher data.</p>');
            }
        });
    }

    function displayPodData() {
        $('.threeColumn').empty();
        var podDataHtml = '';
        $.each(podData, function(index, object) {
            var firstName = object.first_name;
            var lastName = object.last_name;
            var school = object.school;
            var image = object.image;
            var email = object.email;
            var grade = object.grade ? object.grade.join(', ') : ''; // Format grade with comma and space
            var subject = object.subject ? object.subject.join(', ') : ''; // Format subject with comma and space


            if (school == schoolName || siteName == mainSiteName) {
                var imageUrl = (image && image.guid) ? image.guid : 'http://masd.local/wp-content/uploads/2024/04/no_image_available-1.jpeg';
                podDataHtml += '<div class="threeColumnSingle">';
                podDataHtml += '<img class=\'teacherPhoto\' loading=\'lazy\' src="' + imageUrl + '" alt="' + firstName + ' ' + lastName + ' Image"><br>';
                podDataHtml += '<h2>' + firstName + ' ' + lastName + '</h2>';
                podDataHtml += '<a href=\'mailto:' + email + '\'>' + email + '</a></br>';
                podDataHtml += '<p>' + school + '</p>';
                podDataHtml += '<p>' + grade + ' ' + subject + '</p>';
                podDataHtml += '</div>';
            }
        });
        $('.threeColumn').html(podDataHtml);
    }

    function searchTeacher() {
        var searchQuery = $('#teacherSearchInput').val().trim();
        currentPage = 1;  // Reset to first page for a new search
        fetchPodData(currentPage, searchQuery);
    }

    function resetPage() {
        $('#teacherSearchInput').val(''); // Clear search input
        currentPage = 1; // Reset to the first page
        fetchPodData(currentPage); // Fetch all data again
    }

    window.searchTeacher = searchTeacher;
    window.resetPage = resetPage; // Expose function to global scope if needed
});
</script>

<?php
    restore_current_blog();	 
?>
