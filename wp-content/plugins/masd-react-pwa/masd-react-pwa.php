<?php
/**
 * Plugin Name: MASD React PWA
 * Description: A React-based PWA for the Mississippi Achievement School District.
 * Version: 1.0.0
 * Author: Ernest Juchheim
 */

// Enqueue the React app
function masd_enqueue_react_app() {
    $plugin_dir = plugin_dir_path(__FILE__);
    $build_path = $plugin_dir . 'masd-react-app/build/';
    $manifest_path = $build_path . 'asset-manifest.json';

    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);

        if (isset($manifest['files']['main.js']) && isset($manifest['files']['main.css'])) {
            $main_js = $manifest['files']['main.js'];
            $main_css = $manifest['files']['main.css'];

            wp_enqueue_script(
                'masd-react-app-js',
                plugin_dir_url(__FILE__) . 'masd-react-app/build/' . $main_js,
                array(), // Dependencies, if any
                null,
                true
            );

            wp_enqueue_style(
                'masd-react-app-css',
                plugin_dir_url(__FILE__) . 'masd-react-app/build/' . $main_css,
                array(), // Dependencies, if any
                null
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'masd_enqueue_react_app');

// Shortcode to display the React app
function masd_react_app_shortcode() {
    return '<div id="root"></div>';
}
add_shortcode('masd-react-app', 'masd_react_app_shortcode');
