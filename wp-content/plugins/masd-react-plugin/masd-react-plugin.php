<?php
/*
Plugin Name: MASD React Plugin
Description: A plugin to integrate a React app into WordPress.
Version: 1.0
Author: Ernest Juchheim
*/

// Enqueue the React app script and styles with cache busting
function masd_react_enqueue_scripts() {
    $react_app_dir = plugin_dir_path(__FILE__) . 'build/static/js/';
    $react_css_dir = plugin_dir_path(__FILE__) . 'build/static/css/';

    // Get the main.js file name (it includes a unique hash for cache busting)
    $react_app_js = glob($react_app_dir . 'main*.js')[0];
    $react_app_js_url = plugins_url('build/static/js/' . basename($react_app_js), __FILE__);

    // Get the main.css file name (it includes a unique hash for cache busting)
    $react_app_css = glob($react_css_dir . 'main*.css')[0];
    $react_app_css_url = plugins_url('build/static/css/' . basename($react_app_css), __FILE__);

    // Enqueue the React app's main JavaScript file
    wp_enqueue_script(
        'masd-react-app',
        $react_app_js_url,
        array(),
        filemtime($react_app_js),
        true
    );

    // Enqueue the React app's main CSS file
    wp_enqueue_style(
        'masd-react-app',
        $react_app_css_url,
        array(),
        filemtime($react_app_css)
    );
}
add_action('wp_enqueue_scripts', 'masd_react_enqueue_scripts');

// Shortcode to display the React app
function masd_react_app_shortcode() {
    return '<div id="root"></div>';
}
add_shortcode('masd_react_app', 'masd_react_app_shortcode');
