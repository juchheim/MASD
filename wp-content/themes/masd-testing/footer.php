<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package MASD_TESTING
 */
?>

<footer id="colophon" class="site-footer" style="padding: 20px;">
    <div class="footer-row">
        <div class="footer-column footer-column-left">
            <div class="footer-contact">
                <strong>Mississippi Achievement School District</strong><br>
                1133 Calhoun Ave<br>
                Yazoo City, MS 39194<br>
                Tel: 662.746.2125
            </div>
            <div class="social-icons">
                <a href="https://facebook.com" target="_blank" class="social-icon"><i class="fa fa-facebook"></i></a>
            </div>
        </div>
        <div class="footer-column footer-column-center">
            <?php get_search_form(); ?>
            <div class="mobile-app">
                <a href="#"><img src="/wp-content/uploads/2024/05/app_store.png" /></a>
                <a href="#"><img src="/wp-content/uploads/2024/05/google_play.png" /></a>
            </div>
        </div>
        <div class="footer-column footer-column-right">
            <div class="footer-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="/wp-content/uploads/2024/05/logo_reversed_smallv3.png" alt="Site Logo" style="height: 100px;">
                </a>
            </div>
        </div>
    </div>
    <div class="site-info">
        &copy; <?php echo date('Y'); ?> Mississippi Achievement School District. All rights reserved.
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>
