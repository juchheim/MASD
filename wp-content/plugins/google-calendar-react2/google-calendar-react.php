<?php
/*
Plugin Name: Google Calendar React
Description: A plugin to display Google Calendar using React.
Version: 1.0
Author: Ernest Juchheim
*/

function enqueue_react_app() {
    $plugin_dir = plugin_dir_path(__FILE__);
    $manifest_path = $plugin_dir . 'build/asset-manifest.json';

    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);

        if (isset($manifest['files']['main.js'])) {
            $main_js = ltrim($manifest['files']['main.js'], '/');
            $main_css = isset($manifest['files']['main.css']) ? ltrim($manifest['files']['main.css'], '/') : '';

            wp_enqueue_script(
                'google-calendar-react',
                plugins_url('build/' . $main_js, __FILE__),
                array(), // Add any script dependencies if needed
                '1.0',
                true
            );

            if ($main_css) {
                wp_enqueue_style(
                    'google-calendar-react',
                    plugins_url('build/' . $main_css, __FILE__),
                    array(),
                    '1.0'
                );
            }

            echo '<div id="root"></div>';
        }
    }
}

function render_react_app() {
    enqueue_react_app();
    return '<div id="root"></div>';
}

add_shortcode('google_calendar_react', 'render_react_app');
