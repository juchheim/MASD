<div class="media-gallery">
    <?php
    // Initialize the Pods object for the custom post type 'media_gallery' with ordering by date
    $params = array(
        'limit' => -1,
        'orderby' => 'menu_order ASC'
    );
    $pods = pods('media_gallery', $params);
    $videos_list = [];

    // Loop through the Pods object
    if ($pods->total() > 0) :
        ?>
        <div class="headline"><h1>Photos</h1></div>
        <?php

        while ($pods->fetch()) :
            // Retrieve post data
            $title = $pods->display('title');
            $images = $pods->field('image');
            $videos = $pods->field('video'); // Assume 'video' is the field name for videos
            $caption = $pods->field('caption');
            $menu_order = $pods->field('menu_order');

            // Display images
            if (!empty($images)) :
                // Check if there is more than one image
                if (count($images) > 1) : ?>
                    <div class="media-item-group" data-menu-order="<?php echo esc_attr($menu_order); ?>">
                        <h3><?php echo esc_html($title); ?></h3>
                        <?php foreach ($images as $image) :
                            $thumbnail_url = wp_get_attachment_image_url($image['ID'], 'thumbnail');
                            $full_size_url = wp_get_attachment_image_url($image['ID'], 'full');
                            $lightbox_caption = $title . ' - ' . $caption;
                            ?>
                            <div class="media-item grouped-media-item">
                                <a href="<?php echo esc_url($full_size_url); ?>" data-fancybox="gallery" data-caption="<?php echo esc_attr($lightbox_caption); ?>">
                                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : 
                    // If only one image, do not group
                    $image = $images[0];
                    $thumbnail_url = wp_get_attachment_image_url($image['ID'], 'thumbnail');
                    $full_size_url = wp_get_attachment_image_url($image['ID'], 'full');
                    $lightbox_caption = $title . ' - ' . $caption;
                    ?>
                    <div class="media-item" data-menu-order="<?php echo esc_attr($menu_order); ?>">
                        <a href="<?php echo esc_url($full_size_url); ?>" data-fancybox="gallery" data-caption="<?php echo esc_attr($lightbox_caption); ?>">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>">
                        </a>
                        <h4><?php echo esc_html($title); ?></h4>
                        <p><?php echo esc_html($caption); ?></p>
                    </div>
                <?php endif;
            endif;

            // Collect videos to display separately, with menu_order
            if (!empty($videos)) :
                foreach ($videos as $video) :
                    $videos_list[] = array(
                        'title' => $title,
                        'caption' => $caption,
                        'url' => wp_get_attachment_url($video['ID']),
                        'menu_order' => $menu_order
                    );
                endforeach;
            endif;
        endwhile;
    else :
        echo '<p>No media items found.</p>';
    endif;

    // Sort videos by menu_order ASC
    usort($videos_list, function($a, $b) {
        return $a['menu_order'] <=> $b['menu_order'];
    });
    ?>
</div>

<?php if (!empty($videos_list)) : ?>
    <div class="headline"><h1>Videos</h1></div>
    <div class="media-gallery-videos">
        <div class="video-items">
            <?php foreach ($videos_list as $video) : ?>
                <div class="media-item video-item" data-menu-order="<?php echo esc_attr($video['menu_order']); ?>">
                    <h4><?php echo esc_html($video['title']); ?></h4>
                    <video controls style="width:100%; max-width:600px; display:block; margin: 0 auto;">
                        <source src="<?php echo esc_url($video['url']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p><?php echo esc_html($video['caption']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Initialize FancyBox -->
<script>
jQuery(document).ready(function($) {
    $('[data-fancybox="gallery"]').fancybox({
        buttons: [
            "zoom",
            "slideShow",
            "thumbs",
            "close"
        ]
    });

    // Log menu_order values to the console
    $('.media-item, .media-item-group').each(function() {
        console.log('menu_order:', $(this).data('menu-order'));
    });
});
</script>
