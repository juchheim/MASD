<div class="schools-wrapper">

<?php

function display_unique_classified_sites_columns() {
    global $wpdb;

    // Arrays to hold sites by classification
    $humphreys_sites = [];
    $yazoo_sites = [];

    // Retrieve all sites in the multisite network
    $sites = $wpdb->get_results("SELECT blog_id FROM {$wpdb->prefix}blogs");

    if (empty($sites)) {
        return '<p>No sites found or error retrieving sites.</p>';
    }

    foreach ($sites as $site) {
        switch_to_blog($site->blog_id); // Switch to the blog to access its options

        // Get classification and site info
        $classification = get_option('site_classification', 'Unclassified');
        $site_name = get_bloginfo('name');
        $site_url = get_bloginfo('url');

        restore_current_blog(); // Restore original blog

        // Sort sites into their respective arrays
        if ($classification === 'Humphreys') {
            $humphreys_sites[] = ['name' => $site_name, 'url' => $site_url];
        } elseif ($classification === 'Yazoo') {
            $yazoo_sites[] = ['name' => $site_name, 'url' => $site_url];
        }
    }

    // HTML Output
    $output = '<div class="site-columns">';
    $output .= '<div class="schools-column humphreys"><h2>Humphreys County Region</h2><ul>';
    foreach ($humphreys_sites as $site) {
        $output .= "<li><a href='{$site['url']}'>{$site['name']}</a></li>";
    }
    $output .= '</ul></div>';
    $output .= '<div class="schools-column yazoo"><h2>Yazoo City Region</h2><ul>';
    foreach ($yazoo_sites as $site) {
        $output .= "<li><a href='{$site['url']}'>{$site['name']}</a></li>";
    }
    $output .= '</ul></div>';
    $output .= '</div>';

    return $output;
}

echo display_unique_classified_sites_columns();

?>

</div>
