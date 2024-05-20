<?php

// Function to remove the substring after the last space in a string
// This is useful for truncating a date string to remove the time part, for example
function removeAfterLastSpace($str) {
    $lastSpacePos = strrpos($str, ' ');
    if ($lastSpacePos !== false) {
        // If a space is found, return the substring up to the last space
        return substr($str, 0, $lastSpacePos);
    } else {
        // If no space is found, return the original string
        return $str;
    }
}

// Create a new Pods object to interact with the 'news' custom post type
$pod = pods('news');

// Set the query parameters
$params = array(
    'limit' => -1, // No limit on the number of news items to fetch
    'orderby' => 'date_published DESC' // Order by the date_published field in descending order
);

// Execute the query with the specified parameters
$pod->find($params);

// Check if there are any news items found
if ($pod->total() > 0) {
    // Loop through each news item
    while ($pod->fetch()) {
        // Fetch the fields for each news item
        $image = $pod->display('leading_image'); // Get the leading image of the news item
        $title = $pod->display('title'); // Get the title of the news item
        $permalink = $pod->display('permalink'); // Get the permalink (URL) of the news item
        $date_published = $pod->display('date_published'); // Get the date published
        $date_published = removeAfterLastSpace($date_published); // Remove the time part from the date_published field

        // Get the content and excerpt of the news item
        $news_item_content = $pod->display('news_item_content');
        $news_item_excerpt = $pod->display('news_excerpt');

        // Set a fallback image if the leading image is empty
        if (empty($image)) {
            $image = '/wp-content/uploads/2024/05/no_image_available_news.jpeg';
        }
    ?>

    <!-- Start a container for a single news item -->
    <div class="threeColumnSingle">
        <?php
        // Display the leading image with a fallback if empty
        echo "<a href='" . esc_url($permalink) . "'><img class='news-page-image' src='" . esc_url($image) . "' /></a>";
        // Display the news item title with a link to the full news item
        // esc_url() is used to escape the URL to ensure it is safe
        // esc_html() is used to escape the title to prevent XSS attacks
        echo '<h3><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h3>' . esc_html($date_published);
        // Display the news item excerpt
        // wp_kses_post() is used to allow safe HTML tags
        echo wp_kses_post($news_item_excerpt);
        // Display a horizontal rule to separate news items
        echo "<hr></div>";
    }
}

?>
