<?php
/*
Plugin Name: Custom Flyers and Alerts Post Types
Description: Declares a plugin that creates custom post types for Flyers and Alerts.
Version: 1.0
Author: Ernest Juchheim
*/

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


/* flyers and alerts */

function create_custom_post_types() {
    // Register the Flyers post type
    $labels_flyers = array(
        'name' => 'Flyers',
        'singular_name' => 'Flyer',
        'menu_name' => 'Flyers',
        'name_admin_bar' => 'Flyer',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Flyer',
        'new_item' => 'New Flyer',
        'edit_item' => 'Edit Flyer',
        'view_item' => 'View Flyer',
        'all_items' => 'All Flyers',
        'search_items' => 'Search Flyers',
        'parent_item_colon' => 'Parent Flyer:',
        'not_found' => 'No flyers found.',
        'not_found_in_trash' => 'No flyers found in trash.'
    );

    $args_flyers = array(
        'labels' => $labels_flyers,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'flyer'),
        'capability_type' => 'flyer',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true,
        'capabilities' => array(
            'publish_posts' => 'publish_flyers',
            'edit_posts' => 'edit_flyers',
            'edit_others_posts' => 'edit_others_flyers',
            'delete_posts' => 'delete_flyers',
            'delete_others_posts' => 'delete_others_flyers',
            'read_private_posts' => 'read_private_flyers',
            'edit_post' => 'edit_flyer',
            'delete_post' => 'delete_flyer',
            'read_post' => 'read_flyer',
        )
    );

    // Register the Alerts post type
    $labels_alerts = array(
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

    $args_alerts = array(
        'labels' => $labels_alerts,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'alert'),
        'capability_type' => 'alert',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true,
        'capabilities' => array(
            'publish_posts' => 'publish_alerts',
            'edit_posts' => 'edit_alerts',
            'edit_others_posts' => 'edit_others_alerts',
            'delete_posts' => 'delete_alerts',
            'delete_others_posts' => 'delete_others_alerts',
            'read_private_posts' => 'read_private_alerts',
            'edit_post' => 'edit_alert',
            'delete_post' => 'delete_alert',
            'read_post' => 'read_alert',
        )
    );

    register_post_type('flyer', $args_flyers);
    register_post_type('alert', $args_alerts);
}

add_action('init', 'create_custom_post_types');


// flyer and alerts

function add_flyers_and_alerts_role() {
    // Remove the role first to reset the capabilities if the role already exists
    if (get_role('flyers_and_alerts')) {
        remove_role('flyers_and_alerts');
    }

    // Add the Flyers and Alerts role with specific capabilities
    add_role('flyers_and_alerts', 'Flyers and Alerts', array(
        'read' => true,  // basic capability to access the admin dashboard
        'upload_files' => true,

        // Capabilities for Flyers
        'publish_flyers' => true,
        'edit_flyers' => true,
        'edit_others_flyers' => true,
        'delete_flyers' => true,
        'delete_others_flyers' => true,
        'edit_published_flyers' => true,
        'delete_published_flyers' => true,

        // Capabilities for Alerts
        'publish_alerts' => true,
        'edit_alerts' => true,
        'edit_others_alerts' => true,
        'delete_alerts' => true,
        'delete_others_alerts' => true,
        'edit_published_alerts' => true,
        'delete_published_alerts' => true,
    ));
}

add_action('init', 'add_flyers_and_alerts_role');


function register_custom_post_types() {
    // Register the 'Flyer' custom post type with specific capabilities
    register_post_type('flyer', array(
        'labels' => array(
            'name' => 'Flyers',
            'singular_name' => 'Flyer',
            'menu_name' => 'Flyers',
            'name_admin_bar' => 'Flyer',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Flyer',
            'new_item' => 'New Flyer',
            'edit_item' => 'Edit Flyer',
            'view_item' => 'View Flyer',
            'all_items' => 'All Flyers',
            'search_items' => 'Search Flyers',
            'parent_item_colon' => 'Parent Flyer:',
            'not_found' => 'No flyers found.',
            'not_found_in_trash' => 'No flyers found in trash.'
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'flyer'),
        'capability_type' => 'flyer',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true,
        'capabilities' => array(
            'publish_posts' => 'publish_flyers',
            'edit_posts' => 'edit_flyers',
            'edit_others_posts' => 'edit_others_flyers',
            'delete_posts' => 'delete_flyers',
            'delete_others_posts' => 'delete_others_flyers',
            'read_private_posts' => 'read_private_flyers',
            'edit_post' => 'edit_flyer',
            'delete_post' => 'delete_flyer',
            'read_post' => 'read_flyer',
        ),
    ));

    // Register the 'Alert' custom post type with specific capabilities
    register_post_type('alert', array(
        'labels' => array(
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
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'alert'),
        'capability_type' => 'alert',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'map_meta_cap' => true,
        'capabilities' => array(
            'publish_posts' => 'publish_alerts',
            'edit_posts' => 'edit_alerts',
            'edit_others_posts' => 'edit_others_alerts',
            'delete_posts' => 'delete_alerts',
            'delete_others_posts' => 'delete_others_alerts',
            'read_private_posts' => 'read_private_alerts',
            'edit_post' => 'edit_alert',
            'delete_post' => 'delete_alert',
            'read_post' => 'read_alert',
        ),
    ));
}

add_action('init', 'register_custom_post_types');


// how to remove roles: 
/*
function remove_custom_roles() {
    remove_role('flyers_manager');
    remove_role('alerts_manager');
}

add_action('init', 'remove_custom_roles');
*/