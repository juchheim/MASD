<div class="headline"><h1 id="photos">Photos</h1></div>
<div class="media-gallery">
    <?php
    // Initialize the Pods object for the custom post type 'media_gallery' with ordering by date
    $params = array(
        'limit' => -1, // No limit on the number of items
        'orderby' => 'menu_order ASC' // Order by the 'menu_order' field in ascending order
    );
    $pods = pods('media_gallery', $params);
    $videos_list = []; // Array to store video items separately

    // Loop through the Pods object
    if ($pods->total() > 0) : // Check if there are any media_gallery items
        while ($pods->fetch()) : // Fetch each item
            // Retrieve post data
            $title = $pods->display('title'); // Get the title of the media item
            $images = $pods->field('image'); // Get the images field
            $videos = $pods->field('video'); // Get the videos field (assuming 'video' is the field name for videos)
            $caption = $pods->field('caption'); // Get the caption field
            $menu_order = $pods->field('menu_order'); // Get the menu_order field

            // Display images
            if (!empty($images)) : // Check if there are any images
                // Check if there is more than one image
                if (count($images) > 1) : ?>
                    <div class="media-item-group" data-menu-order="<?php echo esc_attr($menu_order); ?>">
                        <h3><?php echo esc_html($title); ?></h3> <!-- Display the title of the media group -->
                        <?php foreach ($images as $image) :
                            $thumbnail_url = wp_get_attachment_image_url($image['ID'], 'thumbnail'); // Get the URL of the thumbnail image
                            $full_size_url = wp_get_attachment_image_url($image['ID'], 'full'); // Get the URL of the full-size image
                            $lightbox_caption = $title . ' - ' . $caption; // Create the lightbox caption
                            ?>
                            <div class="grouped-media-item">
                                <a href="<?php echo esc_url($full_size_url); ?>" data-fancybox="gallery" data-caption="<?php echo esc_attr($lightbox_caption); ?>">
                                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>"> <!-- Display the image with a link to the full-size image -->
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : 
                    // If only one image, do not group
                    $image = $images[0]; // Get the single image
                    $thumbnail_url = wp_get_attachment_image_url($image['ID'], 'thumbnail'); // Get the URL of the thumbnail image
                    $full_size_url = wp_get_attachment_image_url($image['ID'], 'full'); // Get the URL of the full-size image
                    $lightbox_caption = $title . ' - ' . $caption; // Create the lightbox caption
                    ?>
                    <div class="media-item" data-menu-order="<?php echo esc_attr($menu_order); ?>">
                        <a href="<?php echo esc_url($full_size_url); ?>" data-fancybox="gallery" data-caption="<?php echo esc_attr($lightbox_caption); ?>">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>"> <!-- Display the image with a link to the full-size image -->
                        </a>
                        <h4><?php echo esc_html($title); ?></h4> <!-- Display the title of the media item -->
                    </div>
                <?php endif;
            endif;

            // Collect videos to display separately, with menu_order
            if (!empty($videos)) : // Check if there are any videos
                foreach ($videos as $video) : // Loop through each video
                    $videos_list[] = array(
                        'title' => $title, // Store the title
                        'caption' => $caption, // Store the caption
                        'url' => wp_get_attachment_url($video['ID']), // Store the URL of the video
                        'menu_order' => $menu_order // Store the menu_order
                    );
                endforeach;
            endif;
        endwhile;
    else :
        echo '<p>No media items found.</p>'; // Display a message if no media items are found
    endif;

    // Sort videos by menu_order ASC
    usort($videos_list, function($a, $b) {
        return $a['menu_order'] <=> $b['menu_order']; // Sort the videos array by menu_order in ascending order
    });
    ?>
</div>

<?php if (!empty($videos_list)) : ?>
    <div class="headline"><h1 id="videos">Videos</h1></div>
    <div class="media-gallery-videos">
        <div class="video-items">
            <?php foreach ($videos_list as $video) : ?>
                <div class="media-item video-item" data-menu-order="<?php echo esc_attr($video['menu_order']); ?>">
                    <h4><?php echo esc_html($video['title']); ?></h4> <!-- Display the title of the video -->
                    <video controls style="width:100%; max-width:600px; display:block; margin: 0 auto;">
                        <source src="<?php echo esc_url($video['url']); ?>" type="video/mp4"> <!-- Display the video -->
                        Your browser does not support the video tag.
                    </video>
                    <p><?php echo esc_html($video['caption']); ?></p> <!-- Display the caption of the video -->
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
