<?php
/*
Plugin Name: Gutenberg Accordion Block
Description: A custom Gutenberg block for an accordion.
Version: 1.0
Author: Ernest Juchheim
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function gutenberg_accordion_block_init() {
    // Register block editor script.
    wp_register_script(
        'gutenberg-accordion-block-editor',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
    );

    // Register block editor styles.
    wp_register_style(
        'gutenberg-accordion-block-editor',
        plugins_url( 'editor.css', __FILE__ ),
        array( 'wp-edit-blocks' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' )
    );

    // Register front-end styles.
    wp_register_style(
        'gutenberg-accordion-block',
        plugins_url( 'style.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'style.css' )
    );

    // Register block.
    register_block_type( 'gutenberg-accordion-block/main', array(
        'editor_script' => 'gutenberg-accordion-block-editor',
        'editor_style'  => 'gutenberg-accordion-block-editor',
        'style'         => 'gutenberg-accordion-block',
    ) );
}
add_action( 'init', 'gutenberg_accordion_block_init' );

// Enqueue front-end script.
function gutenberg_accordion_block_enqueue_script() {
    if ( ! is_admin() ) {
        wp_enqueue_script(
            'gutenberg-accordion-block-script',
            plugins_url( 'script.js', __FILE__ ),
            array(),
            filemtime( plugin_dir_path( __FILE__ ) . 'script.js' ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'gutenberg_accordion_block_enqueue_script' );
