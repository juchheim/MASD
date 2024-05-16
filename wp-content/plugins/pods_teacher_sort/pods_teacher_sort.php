<?php
/*
Plugin Name: Pods Teacher Sort
Description: Server-side sorting of teachers by last name using Pods.
Version: 1.0
Author: Ernest Juchheim
*/

// Get the current site ID and name
$site_id = get_current_blog_id();
$site_name = get_bloginfo('name');

// Register a custom REST API route for fetching sorted teachers
add_action('rest_api_init', function () use ($site_name) {
    register_rest_route('pods_teacher_sort/v1', '/teachers/', array(
        'methods' => 'GET',  // Only allow GET requests
        'callback' => function ($data) use ($site_name) {
            return get_sorted_teachers($data, $site_name);
        },
        'permission_callback' => '__return_true'  // Allow all users to access this route
    ));
});

/**
 * Function to get sorted teachers
 * 
 * @param object $data The request data
 * @param string $site_name The name of the current site
 * @return WP_REST_Response The response containing sorted teachers
 */
function get_sorted_teachers($data, $site_name) {
    // Switch to the main site (ID 1)
    switch_to_blog(1);

    // Get the 'per_page' parameter from the request, default to 50 if not provided
    $per_page = $data->get_param('per_page') ? intval($data->get_param('per_page')) : 50;
    // Get the 'page' parameter from the request, default to 1 if not provided
    $page = $data->get_param('page') ? intval($data->get_param('page')) : 1;

    // Calculate the offset for pagination
    $offset = ($page - 1) * $per_page;

    // Set the query parameters based on the site name
    if ($site_name != "Mississippi Achievement School District") {
        $params = array(
            'limit' => $per_page,
            'offset' => $offset,
            'orderby' => 'last_name.meta_value',  // Sort by last name
            'order' => 'ASC',  // Sort in ascending order
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

    // Fetch the teachers from the 'teacher' pod using the parameters
    $pods = pods('teacher', $params);
    $teachers = array();

    // If there are teachers, fetch them and add to the teachers array
    if ($pods->total() > 0) {
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
        // Manually set the X-WP-Total header to indicate the total number of teachers found
        header('X-WP-Total: ' . $pods->total_found());
    }

    // Return the teachers array as a REST API response
    return new WP_REST_Response($teachers, 200);
}

/**
 * Enqueue the JavaScript file for handling the teacher directory on the front end
 */
function enqueue_teacher_directory_script() {
    wp_enqueue_script('teacher-directory', get_template_directory_uri() . '/js/teacher-directory.js', array('jquery'), null, true);
    wp_localize_script('teacher-directory', 'teacherData', array(
        'ajax_url' => rest_url('pods_teacher_sort/v1/teachers/'),
        'nonce' => wp_create_nonce('wp_rest'),
        'current_site_id' => get_current_blog_id()
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_teacher_directory_script', 1);

// Restore the current blog to the original site
restore_current_blog();

?>