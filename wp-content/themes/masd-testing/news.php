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
    'limit' => -1,
    'orderby' => 'date_published DESC'
);

$pod->find( $params );
        
if ($pod->total() > 0) {
    while ($pod->fetch()) {
        $title = $pod->display('title');
        $permalink = $pod->display('permalink');
        $date_published = $pod->display('date_published');
        $date_published = removeAfterLastSpace($date_published);


    /*  $leading_image = $pod->display('leading_image'); */
        $news_item_content = $pod->display('news_item_content');
        $news_item_excerpt = $pod->display('news_excerpt');
?>

<div class="threeColumnSingle"><?php
        
/*      if ( !empty($leading_image) ) {
            echo '<a href="' . esc_url($permalink) . '">' .'<img src="' . $leading_image . '" alt="Leading Image"></a>';
        }
*/
        echo '<a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a><br>'. $date_published .'<br>';
        echo $news_item_excerpt;
        echo "<hr></div>";
    }
   
}

?>