<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package MASD_TESTING
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="single_wrapper">
        <?php
        // Accessing Pods information
        global $post;
        $post_id = $post->ID;
        $pods_type = get_post_type($post_id);
        $pods = pods($pods_type, $post_id);

        // Ensure Pods object is correctly created
        if ($pods && !is_null($pods->id)) {
            if ($pods_type == 'news') {
                echo '<h1>' . esc_html($pods->field('title')) . '</h1>';
                echo '<p>' . esc_html($pods->field('news_item_content')) . '</p>';
            } elseif ($pods_type == 'teacher') {
				$image_url = $pods->field('image.guid');
				echo "<img style='width:300px' src='" . esc_url($image_url) . "'>";
                echo '<h2>' . esc_html($pods->field('title')) . '</h2>';
				echo "<a href='mailto:" . esc_html($pods->field('email')) . "'>" . esc_html($pods->field('email')) . "</a>";
                echo '<p>' . esc_html($pods->field('school')) . '</p>';
				
				/* pods relationship fields require this logic */
				/* if field is an array and not empty */
				function display_field_data($field_data) {
					if (is_array($field_data) && !empty($field_data)) {
						echo '<p>' . esc_html(implode(', ', $field_data)) . '</p>';
						/* it must be a string or empty, so if is string and not empty */
					} elseif (is_string($field_data) && !empty($field_data)) {
						echo '<p>' . esc_html($field_data) . '</p>';
					}
				}
				/* output grade and subject if they exist */
				$grades = $pods->field('grade');
				display_field_data($grades);

				$subjects = $pods->field('subject');
				display_field_data($subjects);
				
					
            } elseif ($pods_type == 'staff') {
				$image_url = $pods->field('image.guid');
				echo "<img style='width:300px' src='" . esc_url($image_url) . "' alt='" . esc_attr($pods->field('title')) . "'>";
				echo '<h2>' . esc_html($pods->field('title')) . '</h2>';
				echo '<p>' . esc_html($pods->field('staff_title')) . '</p>';
				echo "<a href='mailto:" . esc_html($pods->field('email')) . "'>" . esc_html($pods->field('email')) . "</a>";
				echo '<p>' . esc_html($pods->field('phone_number')) . '</p>';
			}
			
        } else {
            // Handle the case where Pods couldn't be loaded
            echo '<p>Error loading information.</p>';
        }

        // Start the main loop for post content and navigation
        while ( have_posts() ) :
            the_post();
            get_template_part( 'template-parts/content', get_post_type() );
            the_post_navigation(
                array(
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'masd-testing' ) . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'masd-testing' ) . '</span> <span class="nav-title">%title</span>',
                )
            );

            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile; // End of the loop.
        ?>
    </div>
</main><!-- #main -->


	

<?php
get_footer();
