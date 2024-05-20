<?php
// Get the slug (post name) of the current page
$slug = get_post_field('post_name', get_the_ID());

// Create a new Pods object to interact with the 'request_for_proposal' custom post type
$pod = pods('request_for_proposal');

// Set the query parameters
$params = array(
    'limit' => -1, // No limit on the number of items to fetch
    'orderby' => 'date DESC' // Order by the 'date' field in descending order
);

// Execute the query with the specified parameters
$pod->find($params);

// Check if there are any items found
if ($pod->total() > 0) {
    // Loop through each item
    while ($pod->fetch()) {
        // Fetch the fields for each item
        $date = $pod->display('publish_date'); // Get the publish date
        $title = $pod->display('title'); // Get the title
        $file = $pod->display('file'); // Get the file URL

        // Display the publish date
        // esc_html() is used to escape the date to prevent XSS attacks
        echo "<p>" . esc_html($date) . "</p>";
        // Display the title with a link to the file
        // esc_url() is used to escape the URL to ensure it is safe
        // esc_html() is used to escape the title to prevent XSS attacks
        echo "<h3><a href='" . esc_url($file) . "' target='_blank'>" . esc_html($title) . "</a></h3>";
    }
}
?>
