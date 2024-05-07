<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package MASD_TESTING
 */

get_header();
?>



<!-- HOME PAGE SLIDER -->
<?php 
	if ( is_front_page() ) {
?>

<div class="slider-container">
  <div class="slider">
	
			<?php
				$pod = pods( 'flyer' );
				$params = array(
					'limit' => -1,
					'orderby' => 'publish_date DESC'
				);
				
				$pod->find( $params );
				if ($pod->total() > 0) {
					$i = 0;
					while ($pod->fetch()) {
						$i++;
						$image = $pod->display('image');
						$title = $pod->display('title');
						echo "<img src='".$pod->display('image')."' alt='".$title."' />";
					}
				}
			?>
  </div>
</div>

<?php
	}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const slider = document.querySelector('.slider');
  const images = document.querySelectorAll('.slider img');
  const slideCount = images.length;
  let currentIndex = 0;
  let startX = 0;
  let isDragging = false;

  function slide() {
    currentIndex = (currentIndex + 1) % slideCount;
    const translateValue = -currentIndex * 100;
    slider.style.transform = `translateX(${translateValue}%)`;
  }

  function handleTouchStart(event) {
    isDragging = true;
    startX = event.touches[0].clientX;
  }

  function handleTouchMove(event) {
    if (!isDragging) return;
    const currentX = event.touches[0].clientX;
    const diff = currentX - startX;
    slider.style.transform = `translateX(calc(-${currentIndex * 100}% + ${diff}px))`;
  }

  function handleTouchEnd() {
    isDragging = false;
    const threshold = slider.offsetWidth / 4;
    const movedPercent = (startX - event.changedTouches[0].clientX) / slider.offsetWidth * 100;
    if (movedPercent > 25 || (movedPercent < -25 && currentIndex !== slideCount - 1)) {
      currentIndex = (currentIndex + 1) % slideCount;
    }
    slide();
  }

  slider.addEventListener('touchstart', handleTouchStart);
  slider.addEventListener('touchmove', handleTouchMove);
  slider.addEventListener('touchend', handleTouchEnd);

  setInterval(slide, 8000);
});

</script>




	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->
	

	<?php 
		// Get the post object of the current page or post
		global $post;

		// Check if the $post object is not empty
		if ($post) {
			// Get the slug of the current page or post
			$slug = get_post_field('post_name', $post->ID);
			
			/* if page is home, output news items */
			if ($slug == 'home') {
				require 'news_home.php'; 
			}
			
			if ($slug == 'teachers') {
				require 'teachers.php'; 
			}

			if ($slug == 'news') {
				require 'news.php'; 
			}

			if ($slug == 'schools') {
				require 'schools.php'; 
			}

			require 'staff.php'; 

		}
	?>
	

<?php
get_footer();
