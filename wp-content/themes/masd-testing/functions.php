<?php
/**
 * MASD TESTING functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package MASD_TESTING
 */

if ( ! defined( '_S_VERSION' ) ) {
    // Replace the version number of the theme on each release.
    define( '_S_VERSION', '1.0.2' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function masd_testing_setup() {
    load_theme_textdomain( 'masd-testing', get_template_directory() . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );

    register_nav_menus(
        array(
            'menu-1' => esc_html__( 'Primary', 'masd-testing' ),
        )
    );

    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    add_theme_support(
        'custom-background',
        apply_filters(
            'masd_testing_custom_background_args',
            array(
                'default-color' => 'ffffff',
                'default-image' => '',
            )
        )
    );

    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );
}
add_action( 'after_setup_theme', 'masd_testing_setup' );

function masd_testing_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'masd_testing_content_width', 640 );
}
add_action( 'after_setup_theme', 'masd_testing_content_width', 0 );

function masd_testing_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'Sidebar', 'masd-testing' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Add widgets here.', 'masd-testing' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'masd_testing_widgets_init' );

function masd_testing_scripts() {
    wp_enqueue_style( 'masd-testing-style', get_stylesheet_uri(), array(), _S_VERSION );
    wp_style_add_data( 'masd-testing-style', 'rtl', 'replace' );

    wp_enqueue_script( 'masd-testing-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'masd_testing_scripts' );

require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

if ( defined( 'JETPACK__VERSION' ) ) {
    require get_template_directory() . '/inc/jetpack.php';
}

function add_dropdown_toggle_class($items, $args) {
    if ($args->theme_location == 'menu-1') {
        foreach ($items as &$item) {
            if (in_array('menu-item-has-children', $item->classes)) {
                $item->classes[] = 'dropdown-toggle';
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'add_dropdown_toggle_class', 10, 2);

add_action('wpmu_options', 'add_custom_site_option');
function add_custom_site_option() {
    ?>
    <h2>Site Classification</h2>
    <table class="form-table">
        <tr>
            <th scope="row">Site Classification</th>
            <td>
                <input type="text" name="site_classification" id="site_classification" value="<?php echo esc_attr(get_site_option('site_classification')); ?>" class="regular-text" />
                <p class="description">Enter the classification for this site (e.g., Humphreys, Yazoo).</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action('update_wpmu_options', 'update_custom_site_option');
function update_custom_site_option() {
    update_site_option('site_classification', $_POST['site_classification']);
}

add_filter('rest_teacher_query', function($args, $request) {
    if (!empty($request['search'])) {
        $args['s'] = $request['search'];
    }
    return $args;
}, 10, 2);

function add_custom_body_class($classes) {
    $parent_id = 310;
    $current_page_id = get_the_ID();

    if ($current_page_id && (wp_get_post_parent_id($current_page_id) == $parent_id)) {
        $classes[] = 'child-of-news';
    }
    return $classes;
}
add_filter('body_class', 'add_custom_body_class');

function custom_enqueue_scripts() {
    // Enqueue jQuery
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');


// hide certain post type fields from the admin
function hide_editor_custom_post_type() {
    remove_post_type_support('slider', 'editor');    // removes text editor
    remove_post_type_support('slider', 'comments'); // Removes comments
    remove_post_type_support('slider', 'author');   // Removes author
    remove_post_type_support('slider', 'excerpt');  // Removes excerpt
    remove_post_type_support('slider', 'trackbacks'); // Removes trackbacks
    
}

add_action('init', 'hide_editor_custom_post_type');

add_post_type_support( 'slider', 'page-attributes' );

// hide custom post types from child sites
function hide_post_type_from_child_sites() {
    if ( is_multisite() && ! is_main_site() ) {
        // If it's a multisite and not the main site
        unregister_post_type( 'teacher' );
        unregister_post_type( 'school_staff' );
        unregister_post_type( 'school_leadership' );
        unregister_post_type( 'request_for_proposal' );
    }
}

add_action('init', 'hide_post_type_from_child_sites');


// hide custom post types from main site
function hide_post_type_from_main_site() {
    if ( is_main_site() ) {
        unregister_post_type( 'football_schedule' );
    }
}

add_action('init', 'hide_post_type_from_main_site');





// Enable manual sorting for the 'slider' custom post type and set initial sorting by menu_order
function enable_manual_ordering_for_slider($query) {
    global $pagenow;

    // Ensure we are in the admin area and on the edit screen for the 'slider' post type
    if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'slider') {
        // Check if the orderby parameter is not already set (to avoid overriding if user chooses a different order)
        if (!isset($_GET['orderby'])) {
            $query->set('orderby', 'menu_order');
            // Check if the order parameter is not already set
            if (!isset($_GET['order'])) {
                // Set the default order to ASC
                $query->set('order', 'ASC');
            }
        }
    }
}
add_action('pre_get_posts', 'enable_manual_ordering_for_slider');

wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array(), '1.0.5', 'all' );



function custom_breadcrumbs() {
    $delimiter = ' &raquo; ';
    $home = 'Home'; // text for the 'Home' link
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb

    if ( !is_home() && !is_front_page() || is_paged() ) {
        echo '<nav class="breadcrumbs">';
        global $post;
        $homeLink = get_bloginfo('url');
        echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

        if ( is_category() ) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if ($thisCat->parent != 0) echo get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');
            echo $before . single_cat_title('', false) . $after;

        } elseif ( is_day() ) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;

        } elseif ( is_month() ) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;

        } elseif ( is_year() ) {
            echo $before . get_the_time('Y') . $after;

        } elseif ( is_single() && !is_attachment() ) {
            if ( get_post_type() != 'post' ) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
                echo $before . get_the_title() . $after;
            } else {
                $cat = get_the_category(); $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                echo $cats;
                if ($showCurrent == 1) echo $before . get_the_title() . $after;
            }

        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;

        } elseif ( is_attachment() ) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID); $cat = $cat[0];
            echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
            echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
            echo $before . get_the_title() . $after;

        } elseif ( is_page() && !$post->post_parent ) {
            if ($showCurrent == 1) echo $before . get_the_title() . $after;

        } elseif ( is_page() && $post->post_parent ) {
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo $breadcrumbs[$i];
                if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
            }
            if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

        } elseif ( is_tag() ) {
            echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

        } elseif ( is_author() ) {
            global $author;
            $userdata = get_userdata($author);
            echo $before . 'Articles posted by ' . $userdata->display_name . $after;

        } elseif ( is_404() ) {
            echo $before . 'Error 404' . $after;
        }

        if ( get_query_var('paged') ) {
            echo ' (Page ' . get_query_var('paged') . ')';
        }

        echo '</nav>';
    }
}
