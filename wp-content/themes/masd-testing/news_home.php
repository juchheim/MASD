<?php

function removeAfterLastSpace($str) {
    $lastSpacePos = strrpos($str, ' ');
    if ($lastSpacePos !== false) {
        return substr($str, 0, $lastSpacePos);
    } else {
        return $str; // No space found, return the original string
    }
}

$pod = pods( 'news' );
$params = array(
    'limit' => 6,
    'orderby' => 'date_published DESC'
);

$pod->find( $params );

if ($pod->total() > 0) {
    ?>
    <div class="headline"><h1>News</h1></div>
    <div class="threeColumn">
    <?php
}
        
if ($pod->total() > 0) {
    while ($pod->fetch()) {
        $title = $pod->display('title');
        $permalink = $pod->display('permalink');
        $date_published = $pod->display('date_published');
        $date_published = removeAfterLastSpace($date_published);
        $leading_image = $pod->display('leading_image');
        $news_item_excerpt = $pod->display('news_excerpt');
?>

<div class="threeColumnSingle"><?php
        
        echo "<h3 class='date_published'>" .esc_html($date_published). "</h3>";
        
        if ( !empty($leading_image) ) {
            ?>
            <div class="image-container"><?php
            echo '<a href="' . esc_url($permalink) . '">' .'<img src="' . $leading_image . '" alt="News Image"></a><br>';
            ?>
            </div>
            <?php
            
        } else
        echo "<a href='". esc_url($permalink) ."'><img src='/wp-content/uploads/2024/04/no_image_available_news-1.jpg' /></a>";
        echo "<div class='news_home_title'><a href='" . esc_url($permalink) . "'><h3>" . esc_html($title) . "</h3></a></div>";
        echo "<div class='news_home_excerpt'>" .$news_item_excerpt. "</div>";
        echo "<div class='news_home_read_more'><a href='" . esc_url($permalink) . "'><h4>[ read more ]</h4></a></div>";
        echo "</div>";
    }
    echo "</div>";
}

?>