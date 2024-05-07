<?php
$slug = get_post_field('post_name', get_the_ID());
$pod = pods('request_for_proposal');
$params = array(
    'limit' => -1,
    'orderby' => 'date DESC'
);

$pod->find($params);
    
if ($pod->total() > 0) {
    while ($pod->fetch()) {
        $date = $pod->display('publish_date');
        $title = $pod->display('title');
        $file = $pod->display('file');
            
        echo "<p>" . esc_html($date) . "</p>";
        echo "<h3><a href='". $file ."' target='_blank'>" . esc_html($title) . "</a></h3>";

            
            
    }
}
?>