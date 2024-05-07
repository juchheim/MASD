<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once('../../../../wp-load.php'); 
$id = get_current_blog_id();
$data = [
    'name' => $id
];
echo json_encode($data);
?>