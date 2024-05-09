<?php
/*
Plugin Name: Pods Teacher Sort
Description: Server-side sorting of teachers by last name using Pods.
Version: 1.0
Author: Ernest Juchheim
*/


$site_id = get_current_blog_id();
$site_name = get_bloginfo('name');

// Register REST API route
add_action('rest_api_init', function () use ($site_name) {
    register_rest_route('pods_teacher_sort/v1', '/teachers/', array(
        'methods' => 'GET',
        'callback' => function ($data) use ($site_name) {
            return get_sorted_teachers($data, $site_name);
        },
        'permission_callback' => '__return_true'
    ));
});

function get_sorted_teachers($data, $site_name) {
    switch_to_blog( 1 );
    $per_page = $data->get_param('per_page') ? intval($data->get_param('per_page')) : 50;  // Default to 50 if not specified
    $page = $data->get_param('page') ? intval($data->get_param('page')) : 1;  // Default to page 1 if not specified

    $offset = ($page - 1) * $per_page;  // Calculate the offset

    if ($site_name != "Mississippi Achievement School District") {
        $params = array(
            'limit' => $per_page,
            'offset' => $offset,
            'orderby' => 'last_name.meta_value',
            'order' => 'ASC',
            'where' => array(
                array(
                    'key' => 'school.meta_value',
                    'value' => $site_name,
                    'compare' => '='
                )
            )
        );
    } else {
        $params = array(
            'limit' => $per_page,
            'offset' => $offset,
            'orderby' => 'last_name.meta_value',
            'order' => 'ASC',
        );
    }

    $pods = pods('teacher', $params);
    $teachers = array();
    if (0 < $pods->total() /* && $site_name != "Mississippi Achievement School District" */) {
        while ($pods->fetch()) {
            $teachers[] = array(
                'first_name' => $pods->display('first_name'),
                'last_name' => $pods->display('last_name'),
                'email' => $pods->display('email'),
                'image' => $pods->display('image'),
                'grade' => $pods->display('grade'),
                'subject' => $pods->display('subject'),
                'school' => $pods->display('school')
            );
        }
        // Manually set the X-WP-Total header
        header('X-WP-Total: ' . $pods->total_found());  // Use total_found() if available, or total() if not
    }/* else {
        while ($pods->fetch()) {
            $teachers[] = array(
                'first_name' => $pods->display('first_name'),
                'last_name' => $pods->display('last_name'),
                'email' => $pods->display('email'),
                'image' => $pods->display('image'),
                'grade' => $pods->display('grade'),
                'subject' => $pods->display('subject'),
                'school' => $pods->display('school')
            );
        }
        // Manually set the X-WP-Total header
        header('X-WP-Total: ' . $pods->total_found());  // Use total_found() if available, or total() if not
    } */

    return new WP_REST_Response($teachers, 200);
}


function enqueue_teacher_directory_script() {
    wp_enqueue_script('teacher-directory', get_template_directory_uri() . '/js/teacher-directory.js', array('jquery'), null, true);
    wp_localize_script('teacher-directory', 'teacherData', array(
        'ajax_url' => rest_url('pods_teacher_sort/v1/teachers/'),
        'nonce' => wp_create_nonce('wp_rest'),
        'current_site_id' => get_current_blog_id()
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_teacher_directory_script', 1);

restore_current_blog();	

?>
