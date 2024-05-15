<?php
/**
 * Template Name: Multisite Search
 */

get_header();

global $wpdb;
$s = get_search_query();
$search_results = [];

if (!empty($s)) {
    // Get all sites in the network
    $sites = get_sites();

    foreach ($sites as $site) {
        $site_id = $site->blog_id;

        // Switch to the blog
        switch_to_blog($site_id);

        // Perform search query
        $results = new WP_Query([
            's' => $s,
            'posts_per_page' => -1
        ]);

        // Add site information to the results
        while ($results->have_posts()) {
            $results->the_post();
            $search_results[] = [
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'blog_id' => $site_id,
                'blog_name' => get_bloginfo('name')
            ];
        }

        // Restore the original blog
        restore_current_blog();
    }
}
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <h1><?php echo sprintf(__('Search Results for: %s'), $s); ?></h1>

        <?php if (!empty($search_results)) : ?>
            <ul>
                <?php foreach ($search_results as $result) : ?>
                    <li>
                        <a href="<?php echo esc_url($result['permalink']); ?>">
                            <?php echo esc_html($result['title']); ?>
                        </a>
                        <span> - <?php echo esc_html($result['blog_name']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><?php _e('No results found.'); ?></p>
        <?php endif; ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
