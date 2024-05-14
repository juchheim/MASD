<?php
/*
Plugin Name: Custom User Roles
Description: Creates user roles and custom post types to go along with them.
Version: 1.01
Author: Ernest Juchheim
*/

/*
function add_teacher_role() {
    if (get_role('teacher')) {
        remove_role('teacher');
    }

    add_role('teacher', 'Teacher', array(
        'read' => true,
        'upload_files' => true,
        'publish_teachers' => true,
        'edit_teachers' => true,
        'edit_others_teachers' => true,
        'delete_teachers' => true,
        'delete_others_teachers' => true,
        'edit_published_teachers' => true,
        'delete_published_teachers' => true,
    ));
}

add_action('init', 'add_teacher_role', 11); // Ensuring it runs after the CPT is registered

*/

function add_slider_role() {
    if (get_role('slider')) {
        remove_role('slider');
    }

    add_role('slider', 'Sliders', array(
        'read' => true,
        'upload_files' => true,
        'publish_sliders' => true,
        'edit_sliders' => true,
        'edit_others_sliders' => true,
        'delete_sliders' => true,
        'delete_others_sliders' => true,
        'edit_published_sliders' => true,
        'delete_published_slider' => true,
    ));
}

add_action('init', 'add_slider_role', 11); // Ensuring it runs after the CPT is registered

function create_slider_post_type() {
    $labels = array(
        'name'                  => _x('Sliders', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Slider', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Sliders', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Slider', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'add_new_item'          => __('Add New Slider', 'textdomain'),
        'new_item'              => __('New Slider', 'textdomain'),
        'edit_item'             => __('Edit Slider', 'textdomain'),
        'view_item'             => __('View Slider', 'textdomain'),
        'all_items'             => __('All Sliders', 'textdomain'),
        'search_items'          => __('Search Sliders', 'textdomain'),
        'parent_item_colon'     => __('Parent Sliders:', 'textdomain'),
        'not_found'             => __('No sliders found.', 'textdomain'),
        'not_found_in_trash'    => __('No sliders found in Trash.', 'textdomain'),
        'featured_image'        => _x('Slider Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
        'set_featured_image'    => _x('Set slider image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'remove_featured_image' => _x('Remove slider image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'use_featured_image'    => _x('Use as slider image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'archives'              => _x('Slider archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
        'insert_into_item'      => _x('Insert into slider', 'Overrides the “Insert into post”/“Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
        'uploaded_to_this_item' => _x('Uploaded to this slider', 'Overrides the “Uploaded to this post”/“Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
        'filter_items_list'     => _x('Filter sliders list', 'Screen reader text for the filter links heading on the admin screen. Default “Filter posts list”/“Filter pages list”. Added in 4.4', 'textdomain'),
        'items_list_navigation' => _x('Sliders list navigation', 'Screen reader text for the pagination heading on the admin screen. Default “Posts list navigation”/“Pages list navigation”. Added in 4.4', 'textdomain'),
        'items_list'            => _x('Sliders list', 'Screen reader text for the items list heading on the admin screen. Default “Posts list”/“Pages list”. Added in 4.4', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'slider'),
        'capability_type'    => 'slider',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'map_meta_cap'       => true, // Important for correct mapping of custom capabilities
        'capabilities'       => array(
            'publish_posts'       => 'publish_sliders',
            'edit_posts'          => 'edit_sliders',
            'edit_others_posts'   => 'edit_others_sliders',
            'delete_posts'        => 'delete_sliders',
            'delete_others_posts' => 'delete_others_sliders',
            'read_private_posts'  => 'read_private_sliders',
            'edit_post'           => 'edit_slider',
            'delete_post'         => 'delete_slider',
            'read_post'           => 'read_slider',
        ),
    );

    register_post_type('slider', $args);
}

add_action('init', 'create_slider_post_type');




function create_teacher_post_type() {
    $labels = array(
        'name'                  => _x('Teachers', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Teacher', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Teachers', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Teacher', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'add_new_item'          => __('Add New Teacher', 'textdomain'),
        'new_item'              => __('New Teacher', 'textdomain'),
        'edit_item'             => __('Edit Teacher', 'textdomain'),
        'view_item'             => __('View Teacher', 'textdomain'),
        'all_items'             => __('All Teachers', 'textdomain'),
        'search_items'          => __('Search Teachers', 'textdomain'),
        'parent_item_colon'     => __('Parent Teachers:', 'textdomain'),
        'not_found'             => __('No teachers found.', 'textdomain'),
        'not_found_in_trash'    => __('No teachers found in Trash.', 'textdomain'),
        'featured_image'        => _x('Teacher Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'archives'              => _x('Teacher archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
        'insert_into_item'      => _x('Insert into teacher', 'Overrides the “Insert into post”/“Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
        'uploaded_to_this_item' => _x('Uploaded to this teacher', 'Overrides the “Uploaded to this post”/“Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
        'filter_items_list'     => _x('Filter teachers list', 'Screen reader text for the filter links heading on the admin screen. Default “Filter posts list”/“Filter pages list”. Added in 4.4', 'textdomain'),
        'items_list_navigation' => _x('Teachers list navigation', 'Screen reader text for the pagination heading on the admin screen. Default “Posts list navigation”/“Pages list navigation”. Added in 4.4', 'textdomain'),
        'items_list'            => _x('Teachers list', 'Screen reader text for the items list heading on the admin screen. Default “Posts list”/“Pages list”. Added in 4.4', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'teacher'),
        'capability_type'    => 'teacher',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap'       => true, // Important for correct mapping of custom capabilities
        'capabilities'       => array(
            'publish_posts'       => 'publish_teachers',
            'edit_posts'          => 'edit_teachers',
            'edit_others_posts'   => 'edit_others_teachers',
            'delete_posts'        => 'delete_teachers',
            'delete_others_posts' => 'delete_others_teachers',
            'read_private_posts'  => 'read_private_teachers',
            'edit_post'           => 'edit_teacher',
            'delete_post'         => 'delete_teacher',
            'read_post'           => 'read_teacher',
        ),
    );

    register_post_type('teacher', $args);
}

add_action('init', 'create_teacher_post_type');



// request for proposals


function add_request_for_proposal_role() {
    if (get_role('request_for_proposal')) {
        remove_role('request_for_proposal');
    }

    add_role('request_for_proposal', 'Request for Proposals', array(
        'read' => true,
        'upload_files' => true,
        'publish_proposals' => true,
        'edit_proposals' => true,
        'edit_others_proposals' => true,
        'delete_proposals' => true,
        'delete_others_proposals' => true,
        'edit_published_proposals' => true,
        'delete_published_proposals' => true,
    ));
}


// Ensure the role addition function runs after the CPTs are registered
add_action('init', 'add_request_for_proposal_role', 11);



function register_custom_post_type_request_for_proposals() {
    // Register the 'Request for Proposal' custom post type with specific capabilities
    register_post_type('request_for_proposal', array(
        'labels' => array(
            'name' => 'Request for Proposals',
            'singular_name' => 'Request for Proposals',
            'menu_name' => 'Request for Proposals',
            'name_admin_bar' => 'Request for Proposals',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Request',
            'new_item' => 'New Request',
            'edit_item' => 'Edit Request',
            'view_item' => 'View Request',
            'all_items' => 'All Requests',
            'search_items' => 'Search Requests',
            'parent_item_colon' => 'Parent Request:',
            'not_found' => 'No requests found.',
            'not_found_in_trash' => 'No requests found in trash.'
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'request_for_proposal'),
        'capability_type' => 'proposal',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true,
        'capabilities' => array(
            'publish_posts' => 'publish_proposals',
            'edit_posts' => 'edit_proposals',
            'edit_others_posts' => 'edit_others_proposals',
            'delete_posts' => 'delete_proposals',
            'delete_others_posts' => 'delete_others_proposals',
            'read_private_posts' => 'read_private_proposals',
            'edit_post' => 'edit_proposal',
            'delete_post' => 'delete_proposal',
            'read_post' => 'read_proposal',
        ),
    ));
}

add_action('init', 'register_custom_post_type_request_for_proposals');


// school leadership

function add_school_leadership_role() {
    if (get_role('school_leadership')) {
        remove_role('school_leadership');
    }

    add_role('school_leadership', 'School Leadership', array(
        'read' => true,
        'upload_files' => true,
        'publish_school_leaderships' => true,
        'edit_school_leaderships' => true,
        'edit_others_school_leaderships' => true,
        'delete_school_leaderships' => true,
        'delete_others_school_leaderships' => true,
        'read_private_school_leaderships' => true,
        'edit_published_school_leaderships' => true,
        'delete_published_school_leaderships' => true
    ));
}

add_action('init', 'add_school_leadership_role', 11);



function register_custom_post_type_school_leadership() {
    $labels = array(
        'name' => 'School Leadership',
        'singular_name' => 'School Leadership Entry',
        'menu_name' => 'School Leadership',
        'name_admin_bar' => 'School Leadership Entry',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Leadership Entry',
        'new_item' => 'New Leadership Entry',
        'edit_item' => 'Edit Leadership Entry',
        'view_item' => 'View Leadership Entry',
        'all_items' => 'All Leadership Entries',
        'search_items' => 'Search Leadership Entries',
        'parent_item_colon' => 'Parent Leadership Entry:',
        'not_found' => 'No leadership entries found.',
        'not_found_in_trash' => 'No leadership entries found in trash.'
    );

    $capabilities = array(
        'publish_posts' => 'publish_school_leaderships',
        'edit_posts' => 'edit_school_leaderships',
        'edit_others_posts' => 'edit_others_school_leaderships',
        'delete_posts' => 'delete_school_leaderships',
        'delete_others_posts' => 'delete_others_school_leaderships',
        'read_private_posts' => 'read_private_school_leaderships',
        'edit_post' => 'edit_school_leadership',
        'delete_post' => 'delete_school_leadership',
        'read_post' => 'read_school_leadership',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'school_leadership'),
        'capability_type' => 'school_leadership',
        'capabilities' => $capabilities,
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true
    );

    register_post_type('school_leadership', $args);
}

add_action('init', 'register_custom_post_type_school_leadership');


// alerts
function add_alert_role() {
    if (get_role('alert')) {
        remove_role('alert');
    }

    add_role('alert', 'Alerts', array(
        'read' => true,
        'upload_files' => true,
        'publish_alerts' => true,
        'edit_alerts' => true,
        'edit_others_alerts' => true,
        'delete_alerts' => true,
        'delete_others_alerts' => true,
        'read_private_alerts' => true,
        'edit_published_alerts' => true,
        'delete_published_alerts' => true
    ));
}

add_action('init', 'add_alert_role', 11);

function register_custom_post_type_alert() {
    $labels = array(
        'name' => 'Alerts',
        'singular_name' => 'Alert',
        'menu_name' => 'Alerts',
        'name_admin_bar' => 'Alert',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Alert',
        'new_item' => 'New Alert',
        'edit_item' => 'Edit Alert',
        'view_item' => 'View Alert',
        'all_items' => 'All Alerts',
        'search_items' => 'Search Alerts',
        'parent_item_colon' => 'Parent Alert:',
        'not_found' => 'No alerts found.',
        'not_found_in_trash' => 'No alerts found in trash.'
    );

    $capabilities = array(
        'publish_posts' => 'publish_alerts',
        'edit_posts' => 'edit_alerts',
        'edit_others_posts' => 'edit_others_alerts',
        'delete_posts' => 'delete_alerts',
        'delete_others_posts' => 'delete_others_alerts',
        'read_private_posts' => 'read_private_alerts',
        'edit_post' => 'edit_alert',
        'delete_post' => 'delete_alert',
        'read_post' => 'read_alert',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'alert'),
        'capability_type' => 'alert',
        'capabilities' => $capabilities,
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true
    );

    register_post_type('alert', $args);
}

add_action('init', 'register_custom_post_type_alert');


// school staff

function add_school_staff_role() {
    if (get_role('school_staff')) {
        remove_role('school_staff');
    }

    add_role('school_staff', 'School Staff', array(
        'read' => true,
        'upload_files' => true,
        'publish_posts' => true,  // Changed from 'publish_school_staff' to 'publish_posts'
        'edit_posts' => true,     // Changed from 'edit_school_staff' to 'edit_posts'
        'edit_others_posts' => true,  // Changed from 'edit_others_school_staff' to 'edit_others_posts'
        'delete_posts' => true,   // Changed from 'delete_school_staff' to 'delete_posts'
        'delete_others_posts' => true, // Changed from 'delete_others_school_staff' to 'delete_others_posts'
        'read_private_posts' => true, // Changed from 'read_private_school_staff' to 'read_private_posts'
        'edit_published_posts' => true,  // Changed from 'edit_published_school_staff' to 'edit_published_posts'
        'delete_published_posts' => true // Changed from 'delete_published_school_staff' to 'delete_published_posts'
    ));
}

add_action('init', 'add_school_staff_role', 11);


function register_custom_post_type_school_staff() {
    $labels = array(
        'name' => 'School Staff',
        'singular_name' => 'School Staff Entry',
        'menu_name' => 'School Staff',
        'name_admin_bar' => 'School Staff Entry',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Staff Entry',
        'new_item' => 'New Staff Entry',
        'edit_item' => 'Edit Staff Entry',
        'view_item' => 'View Staff Entry',
        'all_items' => 'All Staff Entries',
        'search_items' => 'Search Staff Entries',
        'parent_item_colon' => 'Parent Staff Entry:',
        'not_found' => 'No staff entries found.',
        'not_found_in_trash' => 'No staff entries found in trash.'
    );

    $capabilities = array(
        'publish_posts' => 'publish_posts',
        'edit_posts' => 'edit_posts',
        'edit_others_posts' => 'edit_others_posts',
        'delete_posts' => 'delete_posts',
        'delete_others_posts' => 'delete_others_posts',
        'read_private_posts' => 'read_private_posts',
        'edit_post' => 'edit_post',
        'delete_post' => 'delete_post',
        'read_post' => 'read_post',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'school_staff'),
        'capability_type' => 'post',
        'capabilities' => $capabilities,
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true
    );

    register_post_type('school_staff', $args);
}

add_action('init', 'register_custom_post_type_school_staff');


add_action('init', 'create_teacher_post_type');

// Add Football Schedule Role
function add_football_schedule_role() {
    if (get_role('football_schedule')) {
        remove_role('football_schedule');
    }

    add_role('football_schedule', 'Football Schedule Manager', array(
        'read' => true,
        'publish_football_schedules' => true,
        'edit_football_schedules' => true,
        'edit_others_football_schedules' => true,
        'delete_football_schedules' => true,
        'delete_others_football_schedules' => true,
        'edit_published_football_schedules' => true,
        'delete_published_football_schedules' => true,
    ));
}

add_action('init', 'add_football_schedule_role', 10); // Ensuring it runs before the CPT is registered

// Create Football Schedule Post Type
function create_football_schedule_post_type() {
    $labels = array(
        'name'                  => _x('Football Schedules', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Football Schedule', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Football Schedules', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Football Schedule', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'add_new_item'          => __('Add New Football Schedule', 'textdomain'),
        'new_item'              => __('New Football Schedule', 'textdomain'),
        'edit_item'             => __('Edit Football Schedule', 'textdomain'),
        'view_item'             => __('View Football Schedule', 'textdomain'),
        'all_items'             => __('All Football Schedules', 'textdomain'),
        'search_items'          => __('Search Football Schedules', 'textdomain'),
        'parent_item_colon'     => __('Parent Football Schedules:', 'textdomain'),
        'not_found'             => __('No football schedules found.', 'textdomain'),
        'not_found_in_trash'    => __('No football schedules found in Trash.', 'textdomain'),
        'featured_image'        => _x('Football Schedule Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
        'set_featured_image'    => _x('Set football schedule image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'remove_featured_image' => _x('Remove football schedule image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'use_featured_image'    => _x('Use as football schedule image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'archives'              => _x('Football Schedule archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
        'insert_into_item'      => _x('Insert into football schedule', 'Overrides the “Insert into post”/“Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
        'uploaded_to_this_item' => _x('Uploaded to this football schedule', 'Overrides the “Uploaded to this post”/“Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
        'filter_items_list'     => _x('Filter football schedules list', 'Screen reader text for the filter links heading on the admin screen. Default “Filter posts list”/“Filter pages list”. Added in 4.4', 'textdomain'),
        'items_list_navigation' => _x('Football schedules list navigation', 'Screen reader text for the pagination heading on the admin screen. Default “Posts list navigation”/“Pages list navigation”. Added in 4.4', 'textdomain'),
        'items_list'            => _x('Football schedules list', 'Screen reader text for the items list heading on the admin screen. Default “Posts list”/“Pages list”. Added in 4.4', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'football_schedule'),
        'capability_type'    => 'football_schedule',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'map_meta_cap'       => true, // Important for correct mapping of custom capabilities
        'capabilities'       => array(
            'publish_posts'       => 'publish_football_schedules',
            'edit_posts'          => 'edit_football_schedules',
            'edit_others_posts'   => 'edit_others_football_schedules',
            'delete_posts'        => 'delete_football_schedules',
            'delete_others_posts' => 'delete_others_football_schedules',
            'read_private_posts'  => 'read_private_football_schedules',
            'edit_post'           => 'edit_football_schedule',
            'delete_post'         => 'delete_football_schedule',
            'read_post'           => 'read_football_schedule',
        ),
    );

    register_post_type('football_schedule', $args);
}

add_action('init', 'create_football_schedule_post_type');





function remove_unwanted_menu_items() {
    $user = wp_get_current_user();

    if (in_array('school_staff', $user->roles)) {
        // Remove the 'Posts' menu
        remove_menu_page('edit.php'); // Posts

        // Remove the 'Comments' menu
        remove_menu_page('edit-comments.php'); // Comments

        // Remove the 'Templates' menu, typically used by page builders like Elementor
        remove_menu_page('edit.php?post_type=elementor_library'); // Templates

        // Remove the 'Tools' menu
        remove_menu_page('tools.php'); // Tools

        // Assuming 'calendars' is a custom post type
        remove_menu_page('edit.php?post_type=calendar'); // Calendars

        // Removing 'District Staff'
        remove_menu_page('edit.php?post_type=staff'); // District Staff
    }
}

add_action('admin_menu', 'remove_unwanted_menu_items', 999);


