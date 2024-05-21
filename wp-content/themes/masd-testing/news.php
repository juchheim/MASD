<?php

// Function to remove the substring after the last space in a string
function removeAfterLastSpace($str) {
    $lastSpacePos = strrpos($str, ' ');
    if ($lastSpacePos !== false) {
        return substr($str, 0, $lastSpacePos);
    } else {
        return $str;
    }
}

// Create a new Pods object to interact with the 'news' custom post type
$pod = pods('news');

// Set the query parameters
$params = array(
    'limit' => -1,
    'orderby' => 'date_published DESC'
);

// Execute the query with the specified parameters
$pod->find($params);

// Check if there are any news items found
if ($pod->total() > 0) {
    echo '<div class="news-container">'; // Add container to center the news items
    while ($pod->fetch()) {
        $image = $pod->display('leading_image');
        $title = $pod->display('title');
        $permalink = $pod->display('permalink');
        $date_published = $pod->display('date_published');
        $date_published = removeAfterLastSpace($date_published);
        $news_item_content = $pod->display('news_item_content');
        $news_item_excerpt = $pod->display('news_excerpt');

        if (empty($image)) {
            $image = '/wp-content/uploads/2024/05/no_image_available_news.jpeg';
        }
    ?>

    <div class="threeColumnSingle">
        <div class="news-page-image-container">
            <a href="<?php echo esc_url($permalink); ?>"><img class="news-page-image" src="<?php echo esc_url($image); ?>" /></a>
        </div>
        <div class="news-content">
            <h3><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h3>
            <p class="news-content-date-published"><?php echo esc_html($date_published); ?></p>
            <?php echo wp_kses_post($news_item_excerpt); ?>
        </div>
    </div>

    <?php
    }
    echo '</div>'; // Close the container
}

?>
