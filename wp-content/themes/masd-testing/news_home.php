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
    'limit' => 6, // Limit the number of news items to 6
    'orderby' => 'date_published DESC' // Order by the date_published field in descending order
);

// Execute the query with the specified parameters
$pod->find($params);

// Check if there are any news items found
if ($pod->total() > 0) {
    ?>
    <!-- Display the headline for the news section -->
    <div class="headline"><h1>News</h1></div>
    <!-- Start a container for the three-column layout -->
    <div class="threeColumn">
    <?php

    // Loop through each news item
    while ($pod->fetch()) {
        // Fetch the fields for each news item
        $title = $pod->display('title');
        $permalink = $pod->display('permalink');
        $date_published = $pod->display('date_published');
        // Remove the time part from the date_published field
        $date_published = removeAfterLastSpace($date_published);
        $leading_image = $pod->display('leading_image');
        $news_item_excerpt = $pod->display('news_excerpt');
    ?>

    <!-- Start a container for a single column item -->
    <div class="threeColumnSingle">
        <?php
        // Display the date published
        // esc_html() is used to escape the date to prevent XSS attacks
        echo "<h3 class='date_published'>" . esc_html($date_published) . "</h3>";
        
        // Check if there is a leading image
        if (!empty($leading_image)) {
            ?>
            <!-- Container for the leading image -->
            <div class="image-container">
            <?php
            // Display the leading image with a link to the full news item
            // esc_url() is used to escape the URL to ensure it is safe
            echo '<a href="' . esc_url($permalink) . '">' . '<img class="news_home_image" src="' . esc_url($leading_image) . '" alt="News Image"></a><br>';
            ?>
            </div>
            <?php
            
        } else {
            // If no leading image, display a placeholder image
            echo "<a href='". esc_url($permalink) ."'><img class='news_home_image' src='/wp-content/uploads/2024/05/no_image_available1.jpg' /></a>";
        }
        // Display the news item title with a link to the full news item
        // esc_html() is used to escape the title to prevent XSS attacks
        echo "<div class='news_home_title'><a href='" . esc_url($permalink) . "'><h3>" . esc_html($title) . "</h3></a></div>";
        // Display the news item excerpt without visible <p></p> tags
        // wp_kses_post() is used to allow safe HTML tags
        echo "<div class='news_home_excerpt'>" . wp_kses_post($news_item_excerpt) . "</div>";
        // Display a 'read more' link
        // esc_url() is used to escape the URL to ensure it is safe
        echo "<div class='news_home_read_more'><a href='" . esc_url($permalink) . "'><h4>[ read more ]</h4></a></div>";
        ?>
    </div> <!-- End of single column item container -->
    <?php
    }
    echo "</div>"; // End of the three-column layout container
}

?>
