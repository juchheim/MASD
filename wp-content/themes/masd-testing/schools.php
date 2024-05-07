<div class="schools-wrapper">

<?php

function display_classified_sites_columns() {
    // Arrays to hold sites by classification
    $humphreys_sites = [];
    $yazoo_sites = [];

    // Retrieve all sites in the multisite network
    $sites = get_sites();
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id); // Switch to the blog to access its options
        $classification = get_option('site_classification', 'Unclassified'); // Default to 'Unclassified' if none is set
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
    echo '<div class="site-columns">';
    echo '<div class="schools-column humphreys"><h2>Humphreys County Region</h2><ul>';
    foreach ($humphreys_sites as $site) {
        echo "<li><a href='{$site['url']}'>{$site['name']}</a></li>";
    }
    echo '</ul></div>';
    echo '<div class="schools-column yazoo"><h2>Yazoo City Region</h2><ul>';
    foreach ($yazoo_sites as $site) {
        echo "<li><a href='{$site['url']}'>{$site['name']}</a></li>";
    }
    echo '</ul></div>';
    echo '</div>';
}

?>

<?php display_classified_sites_columns(); ?>

</div>