<?php

class Custom_Nav_Walker extends Walker_Nav_Menu {
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        // Add a class for parent items
        $is_parent = ! empty( $args->has_children ) ? 'parent-item' : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Add the parent-item class if it's a parent item
        $classes[] = $is_parent;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        // Opening tag
        $output .= $indent;
        if ( $depth === 0 ) {
            $output .= '<div class="menu-column">';
        } else {
            $output .= '<div>';
        }

        // Add <h1> or <h2> based on depth
        if ( $depth === 0 ) {
            $output .= '<h1' . $id . $value . $class_names .'>';
        } else {
            $output .= '<h2' . $id . $value . $class_names .'>';
        }

        $attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
        $attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
        $attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        // Output the item
        $output .= $item_output;
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        // Closing tags
        if ($depth === 0) {
            $output .= "</h1>\n";
            $output .= "</div>\n"; // Close menu-column for top-level items
        } else {
            $output .= "</h2>\n";
            $output .= "</div>\n"; // Close div for sub-menu items
        }
    }
    
}

wp_nav_menu( array(
    'theme_location' => 'menu-1',
    'container' => 'nav',
    'container_class' => 'main-nav',
    'walker' => new Custom_Nav_Walker()
) );


?>