<?php
/*
Plugin Name: Pods Teacher Sort
Description: Server-side sorting of teachers by last name using Pods.
Version: 1.0
Author: Ernest Juchheim
*/

// Get the current site ID and name
// These values are used to customize the behavior of the plugin based on the specific site within a multisite network.
// The site ID and name help determine if we need to apply site-specific conditions or if we are working with the main site.
$site_id = get_current_blog_id(); // Retrieves the ID of the current site in a multisite network.
$site_name = get_bloginfo('name'); // Retrieves the name of the current site.


// Register a custom REST API route for fetching sorted teachers
// This action hook runs when the REST API is initialized, allowing us to register custom routes.
add_action('rest_api_init', function () use ($site_name) {
    
    // register_rest_route registers a new route for the REST API.
    // The first parameter is the namespace for our custom route. This helps avoid conflicts with other plugins.
    // 'pods_teacher_sort/v1' indicates that this route is part of our "Pods Teacher Sort" plugin and that it's version 1.
    register_rest_route('pods_teacher_sort/v1', '/teachers/', array(
        
        // 'methods' => 'GET' specifies that this route only accepts GET requests.
        'methods' => 'GET',
        
        // 'callback' defines the function that will be called when this route is accessed.
        // Here, we use an anonymous function that takes the request data as a parameter.
        // The function calls 'get_sorted_teachers' with the request data and the current site name.
        'callback' => function ($data) use ($site_name) {
            return get_sorted_teachers($data, $site_name);
        },
        
        // 'permission_callback' defines a function that checks whether the current user has permission to access this route.
        // '__return_true' is a WordPress function that always returns true, effectively allowing all users to access this route.
        'permission_callback' => '__return_true'
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
    // Switch to the main site (ID 1) to fetch teachers
    switch_to_blog(1);

    // Get the 'per_page' parameter from the request, default to 50 if not provided
    $per_page = $data->get_param('per_page') ? intval($data->get_param('per_page')) : 50;
    // Get the 'page' parameter from the request, default to 1 if not provided
    $page = $data->get_param('page') ? intval($data->get_param('page')) : 1;

    // Calculate the offset for pagination
    // Offset determines the starting point for the data to be returned by the query.
    // For example, if 'page' is 3 and 'per_page' is 10, the offset will be 20,
    // meaning the query will skip the first 20 records and start returning from the 21st record.
    $offset = ($page - 1) * $per_page;

    // Set the query parameters based on the site name. 
    // If site name isn't the primary site's name, then it must be a school site. Only get teachers from that particular school.
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
        // Query parameters for the main site. Get all teachers.
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
            // Add each teacher's details to the array
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
        // X-WP-Total is a custom header used to inform the client about the total number of items available in the dataset.
        // X: Indicates a custom, non-standard HTTP header. 
        // WP: Specifies that the header is related to WordPress.
        // Total: Indicates the total count of items in the response.
        // This is particularly useful for paginated responses where the client needs to know the total count to manage navigation and display.
        header('X-WP-Total: ' . $pods->total_found());
    }

    // Return the teachers array as a REST API response
    return new WP_REST_Response($teachers, 200);
}

/**
 * Enqueue (add to Wordpress) the JavaScript file for handling the teacher directory on the front end
 */
function enqueue_teacher_directory_script() {
    // Enqueue the custom JavaScript file for the teacher directory
    wp_enqueue_script('teacher-directory', get_template_directory_uri() . '/js/teacher-directory.js', array('jquery'), null, true);
    
    // Localize the script to pass data from PHP to JavaScript
    // This function creates a global JavaScript object that contains the data we need in our JavaScript code.
    // - 'teacher-directory' is the handle of the script we're localizing.
    // - 'teacherData' is the JavaScript object name that will contain the data.
    // - The third parameter is an array of data to be passed to the script.
    wp_localize_script('teacher-directory', 'teacherData', array(
        'ajax_url' => rest_url('pods_teacher_sort/v1/teachers/'), // REST API endpoint URL
        'nonce' => wp_create_nonce('wp_rest'), // Security nonce for REST API requests
        'current_site_id' => get_current_blog_id() // Current site ID
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_teacher_directory_script', 1); // Hook to enqueue scripts

// Restore the current blog to the original site
restore_current_blog();

?>
