<?php
/*
Template Name: Custom News Item Template
Template Post Type: news
*/
?>

<?php
	/**
	 * The template for displaying all single posts
	 *
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
	 *
	 * @package MASD
	 */

	get_header();
	?>

<?php if (!is_front_page()): ?>
<div class="breadcrumbs-container">
  <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
      <?php if(function_exists('bcn_display'))
      {
          bcn_display();
      }?>
  </div>
</div>
<?php endif; ?>

	<?php
		// Start the loop
		while (have_posts()) :
			the_post();

			// Display the title of the news item
			the_title('<h1 class="entry-title">', '</h1>');

			// Display the content of the news item
			the_content();

			// Get specific Pod fields
			$leading_image = pods_field('leading_image.guid');
			$news_item_content = pods_field('news_item_content');

			// Output the specific Pod fields
			if ($leading_image) {
				echo '<div class="image-container">';
				echo "<img class='news_image' src='".$leading_image."' />";
				echo '</div>';
			}
			if ($news_item_content) {
				foreach ($news_item_content as $the_content) {
					echo "<div class='news_content'>" .wpautop( $the_content ). "</div>";
				}
			}

		endwhile; // End of the loop.
		?>

	<?php
	get_footer();
