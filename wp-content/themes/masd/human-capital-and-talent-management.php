<div class="threeColumn">

<?php

$pod = pods( 'staff' );
$params = array(
    'limit' => -1,
    'orderby' => 'priority.meta_value DESC',
    'where' => 'department.meta_value = "Human Capital and Talent Management"'
);

$pod->find( $params );
        
if ($pod->total() > 0) {
    while ($pod->fetch()) {
        $image = $pod->display('image');
        $first_name = $pod->display('first_name');
        $last_name = $pod->display('last_name');
        $title = $pod->display('staff_title');
        $email = $pod->display('email');
        $phone_number = $pod->display('phone_number');
?>

<div class="threeColumnSingle"><?php
        
        echo "<img src='" .$image. "' />";
        echo $first_name. " " .$last_name. "<br>" ;
        echo $title. "<br>";
        echo "<a href='mailto:" .$email. "'> " .$email. "</a><br>";
        echo $phone_number;

        echo "<hr></div>";
    }
   
}

?>

</div>