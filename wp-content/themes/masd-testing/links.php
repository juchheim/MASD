<?php

// Custom Walker Class for Navigation Menu
class Custom_Nav_Walker extends Walker_Nav_Menu {
    // Method to start an element
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        // Generate indentation based on depth
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        // Check if the current item is a parent item
        $is_parent = ! empty( $args->has_children ) ? 'parent-item' : '';

        // Initialize class names and values
        $class_names = $value = '';

        // Get the item classes and add a unique class for each item
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Add the parent-item class if it's a parent item
        $classes[] = $is_parent;

        // Join the classes into a string
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        // Generate the ID for the item
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        // Opening tag based on depth
        $output .= $indent;
        if ( $depth === 0 ) {
            $output .= '<div class="menu-column">';
        } else {
            $output .= '<div>';
        }

        // Add <h1> or <h2> tags based on depth
        if ( $depth === 0 ) {
            $output .= '<h1' . $id . $value . $class_names .'>';
        } else {
            $output .= '<h2' . $id . $value . $class_names .'>';
        }

        // Build the attributes string for the <a> tag
        $attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
        $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
        $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

        // Build the item output string
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        // Add the item output to the overall output
        $output .= $item_output;
    }

    // Method to end an element
    function end_el( &$output, $item, $depth = 0, $args = null ) {
        // Closing tags based on depth
        if ($depth === 0) {
            $output .= "</h1>\n"; // Close the <h1> tag for top-level items
            $output .= "</div>\n"; // Close the menu-column div for top-level items
        } else {
            $output .= "</h2>\n"; // Close the <h2> tag for sub-menu items
            $output .= "</div>\n"; // Close the div for sub-menu items
        }
    }
}

// Call wp_nav_menu with our custom walker
wp_nav_menu( array(
    'theme_location' => 'menu-1', // Define the location of the menu
    'container' => 'nav', // Wrap the menu in a <nav> container
    'container_class' => 'main-nav', // Add a class to the container
    'walker' => new Custom_Nav_Walker() // Use the custom walker
) );

?>
