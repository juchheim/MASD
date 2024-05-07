<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
    <label>
        <span class="screen-reader-text"><?php echo _x('Search for:', 'label', 'your-theme-text-domain'); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('Search the site', 'placeholder', 'your-theme-text-domain'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <input type="submit" class="search-submit" value="<?php echo esc_attr_x('Search', 'submit button', 'your-theme-text-domain'); ?>" />
</form>
