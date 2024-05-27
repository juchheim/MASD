<?php
/*
Plugin Name: Custom Facebook Feed
Description: A custom plugin to display a Facebook feed using the Facebook Page Plugin.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue Facebook SDK script
function cff_enqueue_facebook_sdk() {
    ?>
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v13.0"></script>
    <?php
}
add_action('wp_footer', 'cff_enqueue_facebook_sdk');

// Enqueue custom CSS for full-width styling
function cff_enqueue_custom_styles() {
    ?>
    <style>
        .cff-full-width {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            margin: 0 auto;
        }
    </style>
    <?php
}
add_action('wp_head', 'cff_enqueue_custom_styles');

// Shortcode to display the Facebook feed
function cff_facebook_feed_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'url' => 'https://www.facebook.com/MASDonline',
            'tabs' => 'timeline',
            'width' => '500',
            'height' => '700',
            'small_header' => 'false',
            'adapt_container_width' => 'true',
            'hide_cover' => 'false',
            'show_facepile' => 'true',
        ),
        $atts,
        'facebook_feed'
    );

    $output = '<div class="cff-full-width">
        <div class="fb-page" 
            data-href="' . esc_url($atts['url']) . '"
            data-tabs="' . esc_attr($atts['tabs']) . '"
            data-width="' . esc_attr($atts['width']) . '"
            data-height="' . esc_attr($atts['height']) . '"
            data-small-header="' . esc_attr($atts['small_header']) . '"
            data-adapt-container-width="' . esc_attr($atts['adapt_container_width']) . '"
            data-hide-cover="' . esc_attr($atts['hide_cover']) . '"
            data-show-facepile="' . esc_attr($atts['show_facepile']) . '">
            <blockquote cite="' . esc_url($atts['url']) . '" class="fb-xfbml-parse-ignore"><a href="' . esc_url($atts['url']) . '">MASDonline</a></blockquote>
        </div>
    </div>';

    return $output;
}
add_shortcode('facebook_feed', 'cff_facebook_feed_shortcode');
