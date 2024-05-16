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

<?php 
  if ( is_front_page() ) {
?>

<div class="slider-container">
  <div class="slider-dots"></div>
  <div class="slider">
    <?php
      $pod = pods( 'slider' );
      $params = array(
        'limit' => -1,
        'orderby' => 'menu_order ASC' // sort by order as displayed in the admin
      );
      
      $pod->find( $params );
      if ($pod->total() > 0) {
        while ($pod->fetch()) {
          $image = $pod->display('image');
          $title = $pod->display('title');
          echo "<img class='slider-image' src='".$image."' alt='".$title."' />";
        }
      }
    ?>
  </div>
  <!-- Navigation and Control buttons -->
  <?php if ($pod->total() > 1) : ?>
    <button class="prev">&#10094;</button> <!-- HTML entity for left arrow -->
    <button class="next">&#10095;</button> <!-- HTML entity for right arrow -->
    <div id="play-pause-wrapper"><button class="play-pause">&#10074;&#10074;</button></div> <!-- HTML entity for pause icon -->
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const slider = document.querySelector('.slider');
  const images = document.querySelectorAll('.slider img');
  const slideCount = images.length;
  const dotsContainer = document.querySelector('.slider-dots');
  let currentIndex = 0;
  let intervalId;
  let isPlaying = true; // Keep track of whether the slider is playing

  function updateSlider() {
    const translateValue = -currentIndex * 100;
    slider.style.transform = `translateX(${translateValue}%)`;
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % slideCount;
    updateSlider();
    updateDots();
    resetInterval();
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + slideCount) % slideCount;
    updateSlider();
    updateDots();
    resetInterval();
  }

  function createDots() {
    if (slideCount > 1) {
      for (let i = 0; i < images.length; i++) {
        const dot = document.createElement('span');
        dot.classList.add('slider-dot');
        dot.dataset.index = i;
        dot.addEventListener('click', function() {
          currentIndex = parseInt(this.dataset.index);
          updateSlider();
          updateDots();
          resetInterval();
        });
        dotsContainer.appendChild(dot);
      }
      updateDots();
    } else {
      dotsContainer.style.display = 'none';
    }
  }

  function updateDots() {
    const dots = document.querySelectorAll('.slider-dot');
    dots.forEach((dot, index) => {
      dot.classList.toggle('active', index === currentIndex);
    });
  }

  function togglePlayPause() {
    if (isPlaying) {
      clearInterval(intervalId);
      playPauseButton.innerHTML = "&#9658;"; // Change to play icon
    } else {
      resetInterval();
      playPauseButton.innerHTML = "&#10074;&#10074;"; // Change to pause icon
    }
    isPlaying = !isPlaying;
  }

  function resetInterval() {
    clearInterval(intervalId);
    intervalId = setInterval(nextSlide, 8000);
  }

  createDots();
  const nextButton = document.querySelector('.next');
  const prevButton = document.querySelector('.prev');
  const playPauseButton = document.querySelector('.play-pause');

  if (nextButton && prevButton && playPauseButton) {
    nextButton.addEventListener('click', nextSlide);
    prevButton.addEventListener('click', prevSlide);
    playPauseButton.addEventListener('click', togglePlayPause);
  }

  resetInterval(); // Start the initial interval
});
</script>

<?php
  }
?>

<main id="primary" class="site-main">

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
    
    if ($slug == 'home' && get_current_blog_id() == 1) {
      ?>
      <div id="parallax">
        <div id="foreground"></div>
        <div id="blue"></div>
        <div id="red"></div>
      </div>

      <script>

      window.addEventListener('scroll', function() {
          var scrollPosition = window.pageYOffset;

          // Existing functionality for foreground
          var slowScroll = scrollPosition * 0.5; // The image moves at half the scroll speed
          document.getElementById('foreground').style.top = (-200 + slowScroll) + 'px'; // Adjust starting point dynamically

          // New functionality for blue and red images
          var blue = document.getElementById('blue');
          var red = document.getElementById('red');

          // Keep blue and red in the middle until scrollPosition reaches 500px
          var startTransformPoint = 500; // Adjust this value based on when you want the images to start moving
          var blueTransform = 0;
          var redTransform = 0;

          if (scrollPosition > startTransformPoint) {
              blueTransform = Math.min((scrollPosition - startTransformPoint) * 1.5, 1000); // Increase the factor for faster movement
              redTransform = Math.min((scrollPosition - startTransformPoint) * 1.5, 1000);
          }

          blue.style.transform = `translateX(-${blueTransform}px)`;
          red.style.transform = `translateX(${redTransform + 200}px)`; // Adjust the initial position offset

      });




      </script>

      <?php
    }

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
  
    if ($slug == 'request-for-proposals') {
      require 'request-for-proposals.php';
    }

    require 'staff.php'; 

  }

  if ($slug == 'leadership') {
    require 'school_leadership.php';
  }

  if ($slug == 'staff') {
    require 'school_staff.php';
  }

  if ($slug == 'home' && get_current_blog_id() == 1) {
    echo "<div class='headline'><h1>Calendar of Events</h1></div>";
    echo do_shortcode('[calendar id="318"]');
  }

  // athletics
  if ($slug == 'varsity-football') {
    require 'athletics_football.php';
  }

  // athletics
  if ($slug == 'volleyball') {
    require 'athletics_volleyball.php';
  }

  ?>


  <!-- closes tags from staff (staff won't work otherwise, am troubleshooting) -->
  </div>
  
  <!-- end tags from staff -->

  <?php
  $pod = pods('page', get_the_id());
  if(!empty($pod->field('below_the_content'))) {
      
        echo  "<div class='below_the_content'>" .$pod->field('below_the_content'). "</div>";
    
  }
  ?>

<?php
get_footer();
?>
