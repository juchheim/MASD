<?php

// Include WordPress load file if this script runs outside of WordPress environment
require_once('wp-load.php');

// Get current blog ID and site name
$current_blog_id = get_current_blog_id();
$current_site_name = get_bloginfo('name');

// Optionally switch to a specific blog if necessary, e.g., main site
switch_to_blog(1);

// Prepare the Pods finder parameters
$params = array(
    'limit' => -1, // No limit, fetch all entries
    'where' => array(
        'relation' => 'AND',
        array(
            'key'     => 'school',
            'value'   => $current_site_name,
            'compare' => '='
        )
    ),
    'orderby' => array(
        'priority.meta_value' => 'DESC', // Order by the priority field, ascending
        'last_name.meta_value' => 'ASC'  // Then order by the last_name field, ascending
    )
);

// Fetch the leadership data
$pod = pods('school_leadership', $params);

// Check if we have entries
if ($pod->total() > 0) {
    ?>
    <div class="threeColumn">
    <?php
    while ($pod->fetch()) {
        // Accessing the fields of the leadership Pods
        $title = $pod->display('title');
        $leadership_title = $pod->display('leadership_title');
        $email = $pod->display('email');
        $phone = $pod->display('phone_number');
        $image = $pod->display('image');
        $priority = $pod->display('priority');

        // Determine the CSS classes based on priority
        $priority99 = ($priority == 99) ? 'darkblue-div' : '';
        $priority98 = ($priority == 98) ? 'gray-div' : '';
        ?>
        <div class="threeColumnSingle <?php echo esc_attr($priority99); ?> <?php echo esc_attr($priority98); ?>">
        <?php

        // Output the data
        if (!empty($image)) {
            echo "<img class='staffPhoto' src='" . esc_url($image) . "' alt='Photo of $title'><br><br>";
        } else {
            echo "<img class='staffPhoto' src='http://masd.local/wp-content/uploads/2024/04/no_image_available-1.jpeg' alt='No image available'><br><br>";
        }
        echo "<h2>" . esc_html($title) . "</h2>";
        echo "<p>" . esc_html($leadership_title) . "</p>";
        echo "<p><a href='mailto:" . esc_attr($email) . "'>" . esc_html($email) . "</a></p>";
        echo "<p>" . esc_html($phone) . "</p>";

        echo "</div>"; // threeColumnSingle
    }
    ?>
    </div><!-- threeColumn -->
    <?php
} else {
    echo "No leadership data found for the current site.";
}
// Restore original blog, if switched
restore_current_blog();
?>
