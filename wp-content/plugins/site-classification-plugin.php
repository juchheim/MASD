<?php
/*
Plugin Name: Site Classifications Manager for Multisite Dashboard
Description: Adds a top-level menu item in the network admin dashboard to manage site classifications for a WordPress multisite network with dropdown options.
Version: 1.0
Author: Ernest Juchheim
License: GPL2
*/

// Add a top-level menu item to the Network Admin dashboard
add_action('network_admin_menu', 'add_site_classification_dashboard_menu');
function add_site_classification_dashboard_menu() {
    add_menu_page(
        'Site Classifications',          // Page title
        'Site Classifications',          // Menu title
        'manage_network_options',        // Capability
        'site-classifications',          // Menu slug
        'site_classification_page',      // Callback function
        'dashicons-networking',          // Icon
        6                                // Position
    );
}

// Render the site classification settings page
function site_classification_page() {
    ?>
    <div class="wrap">
        <h1>Manage Site Classifications</h1>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="save_site_classifications">
            <?php wp_nonce_field('site_classifications_nonce'); ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Site ID</th>
                        <th>Site Name</th>
                        <th>Classification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sites = get_sites();
                    foreach ($sites as $site) {
                        $site_id = $site->blog_id;
                        switch_to_blog($site_id);
                        $current_classification = get_option('site_classification', 'None'); // Default to 'None' if not set
                        restore_current_blog();
                        ?>
                        <tr>
                            <td><?php echo esc_html($site_id); ?></td>
                            <td><?php echo esc_html(get_blog_details($site_id)->blogname); ?></td>
                            <td>
                                <select name="site_classification[<?php echo $site_id; ?>]" class="regular-text">
                                    <option value="None" <?php echo $current_classification === 'None' ? 'selected' : ''; ?>>None</option>
                                    <option value="Humphreys" <?php echo $current_classification === 'Humphreys' ? 'selected' : ''; ?>>Humphreys</option>
                                    <option value="Yazoo" <?php echo $current_classification === 'Yazoo' ? 'selected' : ''; ?>>Yazoo</option>
                                </select>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php submit_button('Save Classifications'); ?>
        </form>
    </div>
    <?php
}

// Handle form submission
add_action('admin_post_save_site_classifications', 'handle_save_site_classifications');
function handle_save_site_classifications() {
    if (!current_user_can('manage_network_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    check_admin_referer('site_classifications_nonce');

    if (isset($_POST['site_classification']) && is_array($_POST['site_classification'])) {
        foreach ($_POST['site_classification'] as $site_id => $classification) {
            if (is_numeric($site_id)) {
                update_blog_option((int)$site_id, 'site_classification', sanitize_text_field($classification));
            }
        }
    }

    // Redirect back to the dashboard page with a success message
    wp_redirect(add_query_arg(['page' => 'site-classifications', 'updated' => 'true'], network_admin_url('admin.php')));
    exit;
}
