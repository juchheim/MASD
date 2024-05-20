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

// Include the header template part
get_header();
?>

<?php 
if (is_front_page()) {
?>

<div class="slider-container">
  <div class="slider-dots"></div>
  <div class="slider">
    <?php
    $pod = pods('slider');
    $params = array(
      'limit' => -1, // Fetch all items without limit
      'orderby' => 'menu_order ASC' // Order items by the menu order in ascending order
    );
    
    // Fetch the items based on the parameters
    $pod->find($params);
    if ($pod->total() > 0) { 
      while ($pod->fetch()) { 
        $image = $pod->display('image'); 
        $title = $pod->display('title');
        $link = $pod->display('link'); 
        $video = $pod->field('video'); 

        // Initialize video URL as an empty string
        $video_url = '';
        
        // Determine video URL based on the type of data in the video field
        if ($video) {
          if (is_array($video) && isset($video[0])) {
            // Case where video is an array of IDs (local setup)
            $video_id = $video[0];
            $video_url = wp_get_attachment_url($video_id);
          } elseif (is_array($video) && isset($video['guid'])) {
            // Case where video is an array containing a guid (server setup)
            $video_url = $video['guid'];
          } 
        }

        echo "<script>console.log('Video field raw value: " . json_encode($video) . "');</script>";
        echo "<script>console.log('Video URL: " . $video_url . "');</script>";

        // Display the video if a video URL is found
        if (!empty($video_url)) {
          echo "<div class='slider-image slider-video-slide'><video src='".$video_url."' autoplay muted playsinline></video></div>";
        // Otherwise, display the image with or without a link
        } elseif (!empty($link)) {
          echo "<div class='slider-image'><a href='".$link."' target='_blank'><img src='".$image."' alt='".$title."' /></a></div>";
        } else {
          echo "<div class='slider-image'><img src='".$image."' alt='".$title."' /></div>";
        }
      }
    }
    ?>
  </div>
  <?php if ($pod->total() > 1) : // Display navigation controls if there's more than one slide ?>
    <button class="prev">&#10094;</button> <!-- Previous slide button -->
    <button class="next">&#10095;</button> <!-- Next slide button -->
    <div id="play-pause-wrapper"><button class="play-pause">&#10074;&#10074;</button></div> <!-- Play/pause button -->
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sliderContainer = document.querySelector('.slider-container');
  const slider = document.querySelector('.slider');
  const slides = document.querySelectorAll('.slider .slider-image');
  const slideCount = slides.length;
  const dotsContainer = document.querySelector('.slider-dots');
  let currentIndex = 0;
  let intervalId;
  let isPlaying = true;

  console.log('DOMContentLoaded: Slider initialized with', slideCount, 'slides');

  slider.style.width = `${slideCount * 100}%`;
  slides.forEach(slide => {
    slide.style.width = `${100 / slideCount}%`;
  });

  function updateSlider() {
    console.log('updateSlider: Updating slider position to index', currentIndex);
    const translateValue = -currentIndex * (100 / slideCount);
    slider.style.transform = `translateX(${translateValue}%)`;

    const currentSlide = slides[currentIndex];
    const currentVideo = currentSlide.querySelector('video');
    const playPauseButton = document.querySelector('.play-pause');

    console.log('updateSlider: Current slide', currentIndex, 'has video:', !!currentVideo);

    slides.forEach((slide, index) => {
      const video = slide.querySelector('video');
      if (video) {
        video.pause();
        video.removeEventListener('ended', handleVideoEnded);
        console.log('updateSlider: Paused video on slide', index);
      }
    });

    if (currentVideo) {
      clearInterval(intervalId);
      playPauseButton.style.display = 'none';
      currentVideo.play();
      console.log('updateSlider: Playing video on slide', currentIndex);
      currentVideo.addEventListener('ended', handleVideoEnded, { once: true });
    } else {
      playPauseButton.style.display = 'block';
      resetInterval();
      console.log('updateSlider: No video on slide', currentIndex, 'resetting interval');
    }
  }

  function handleVideoEnded() {
    console.log('handleVideoEnded: Video ended on slide', currentIndex);
    nextSlide(true);
    // Reset the play/pause button to show the pause icon after a video ends
    const playPauseButton = document.querySelector('.play-pause');
    playPauseButton.innerHTML = "&#10074;&#10074;";
    isPlaying = true;
  }

  function nextSlide(fromVideoEnded = false) {
    console.log('nextSlide: Moving to next slide from index', currentIndex);
    currentIndex = (currentIndex + 1) % slideCount;
    updateSlider();
    updateDots();
    if (!fromVideoEnded) {
      resetInterval();
      console.log('nextSlide: Interval reset after moving to slide', currentIndex);
    }
  }

  function prevSlide() {
    console.log('prevSlide: Moving to previous slide from index', currentIndex);
    currentIndex = (currentIndex - 1 + slideCount) % slideCount;
    updateSlider();
    updateDots();
    resetInterval();
    console.log('prevSlide: Interval reset after moving to slide', currentIndex);
  }

  function createDots() {
    if (slideCount > 1) {
      for (let i = 0; i < slides.length; i++) {
        const dot = document.createElement('span');
        dot.classList.add('slider-dot');
        dot.dataset.index = i;
        dot.addEventListener('click', function() {
          currentIndex = parseInt(this.dataset.index);
          updateSlider();
          updateDots();
          resetInterval();
          console.log('createDots: Dot clicked, moving to slide', currentIndex);
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
    console.log('updateDots: Dots updated, current active dot', currentIndex);
  }

  function togglePlayPause() {
    const playPauseButton = document.querySelector('.play-pause');
    if (isPlaying) {
      console.log('togglePlayPause: Pausing slideshow');
      clearInterval(intervalId);
      playPauseButton.innerHTML = "&#9658;";
    } else {
      console.log('togglePlayPause: Resuming slideshow');
      resetInterval();
      playPauseButton.innerHTML = "&#10074;&#10074;";
    }
    isPlaying = !isPlaying;
  }

  function resetInterval() {
    console.log('resetInterval: Resetting interval');
    clearInterval(intervalId);
    const currentSlide = slides[currentIndex];
    const currentVideo = currentSlide.querySelector('video');

    if (currentVideo) {
      console.log('resetInterval: Video detected on slide', currentIndex, 'waiting for it to end before moving to the next slide');
      currentVideo.addEventListener('ended', handleVideoEnded, { once: true });
    } else {
      console.log('resetInterval: No video detected on slide', currentIndex, 'setting interval for automatic sliding');
      intervalId = setInterval(nextSlide, 8000);
    }
  }

  createDots();

  const nextButton = document.querySelector('.next');
  const prevButton = document.querySelector('.prev');
  const playPauseButton = document.querySelector('.play-pause');

  if (nextButton && prevButton && playPauseButton) {
    nextButton.addEventListener('click', () => {
      console.log('Next button clicked');
      nextSlide();
    });
    prevButton.addEventListener('click', () => {
      console.log('Previous button clicked');
      prevSlide();
    });
    playPauseButton.addEventListener('click', togglePlayPause);
  }

  updateSlider();
  sliderContainer.classList.add('ready');
});


</script>

<?php
} // End of the if (is_front_page()) block
?>

<main id="primary" class="site-main">

<?php // if (!is_front_page()) : ?>
<div class="breadcrumbs-container">
  <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
    <?php if (function_exists('bcn_display')) {
      bcn_display(); // Display breadcrumb navigation if the function exists
    } ?>
  </div>
</div>
<?php // endif; // End of the if (!is_front_page()) block ?>

<?php
// Start the loop to display the content of the page
while (have_posts()) :
  the_post();
  get_template_part('template-parts/content', 'page'); // Include the content template part for the page

  // If comments are open or there is at least one comment, load the comment template
  if (comments_open() || get_comments_number()) :
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
  
  // Display parallax effect if the page slug is 'home' and it is the main site (blog ID 1)
  if ($slug == 'home' && get_current_blog_id() == 1) {
?>
    <div id="parallax">
      <div id="foreground"></div>
      <div id="blue"></div>
      <div id="red"></div>
    </div>

    <script>
    // JavaScript to handle the parallax scrolling effect
    window.addEventListener('scroll', function() {
      // Get the current scroll position of the window along the Y-axis (vertical scroll position)
      var scrollPosition = window.pageYOffset;

      // Existing functionality for foreground
      var slowScroll = scrollPosition * 0.5; // The image moves at half the scroll speed
      document.getElementById('foreground').style.top = (-200 + slowScroll) + 'px'; // Adjust starting point dynamically

      // Functionality for blue and red images
      var blue = document.getElementById('blue');
      var red = document.getElementById('red');

      // Keep blue and red in the middle until scrollPosition reaches 500px
      var startTransformPoint = 500; // Adjust this value based on when you want the images to start moving
      // Initialize transformation values for the blue and red elements
      var blueTransform = 0;
      var redTransform = 0;

      // Check if the scroll position is greater than the starting transform point
      if (scrollPosition > startTransformPoint) {
        // Calculate the transformation value for the red and blue elements, increasing the factor for faster movement
        // The transformation value is limited to a maximum of 1000 pixels to ensure that the elements (blue and red) do not move too far off-screen
        blueTransform = Math.min((scrollPosition - startTransformPoint) * 1.5, 1000);
        redTransform = Math.min((scrollPosition - startTransformPoint) * 1.5, 1000);
      }

      // Apply the calculated transformation to the blue element, moving it to the left
      blue.style.transform = `translateX(-${blueTransform}px)`;

      // Apply the calculated transformation to the red element, moving it to the right
      // An additional offset of 200 pixels is added to the red element to adjust positioning
      red.style.transform = `translateX(${redTransform + 200}px)`;

    });
    </script>

<?php
  }

  // Include different content based on the page slug
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

  require 'staff.php'; // Include staff content in every page because slugs vary. Staff.php immediately detects and pulls content (if content exits).

  if ($slug == 'leadership') {
    require 'school_leadership.php';
  }

  if ($slug == 'staff') {
    require 'school_staff.php';
  }

  // Display google calendar events based on the site and page slug. 
  // Uses Simple Google Calendar plugin. Edit it's files to alter output (/plugins/simple-google-calendar/simple_google_calendar.php) 
  if ($slug == 'home' && get_current_blog_id() == 1) {
    echo do_shortcode('[google_calendar_events calendar_id="c_46f1e96c91dde30d948251d704ac1f5ba7e5f104d86eb6e7b254c15e9f093fe7@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 2) {
    echo do_shortcode('[google_calendar_events calendar_id="c_34ab838945ee6c9b192f8f914750d328755eaff90c20ed71682dc11e7eb53682@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 3) {
    echo do_shortcode('[google_calendar_events calendar_id="c_780db5863d569f8a66f5a7123880304c2530a4fe26512efb8ef8b3a91aa3f63f@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 4) {
    echo do_shortcode('[google_calendar_events calendar_id="c_8771017d68265998645f0dc093e011e4a0cf0ec2f1628bf1556a540ccb0d7273@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 5) {
    echo do_shortcode('[google_calendar_events calendar_id="c_f2214d22850b10b7927b8a570ea3aed9bd6e773390ddfeb46da9f402797d4d09@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 6) {
    echo do_shortcode('[google_calendar_events calendar_id="c_e298f06037cdcf4a011a26747bc71565b8bd425d81f1e75872ca1b228d1d94a0@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 7) {
    echo do_shortcode('[google_calendar_events calendar_id="c_e298f06037cdcf4a011a26747bc71565b8bd425d81f1e75872ca1b228d1d94a0@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 8) {
    echo do_shortcode('[google_calendar_events calendar_id="c_cd9e943f248dcc0b8ca71c751ac1abe627d8bb4a9d57a2ca2baef6c55f11606c@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 9) {
    echo do_shortcode('[google_calendar_events calendar_id="c_88e94a664577eac86cf036470c2fe5d719e715246dc1975264a3d1ce73d6ddfb@group.calendar.google.com" max_results="5"]');
  }

  if ($slug == 'home' && get_current_blog_id() == 10) {
    echo do_shortcode('[google_calendar_events calendar_id="c_6be2f00db5e2d286182eae5fef2798eee664610dba66318f409263b5d97cc0ad@group.calendar.google.com" max_results="5"]');
  }

  // Include athletics content based on the page slug
  if ($slug == 'varsity-football') {
    require 'athletics_football.php';
  }

  if ($slug == 'volleyball') {
    require 'athletics_volleyball.php';
  }
?>

  <!-- Close tags from staff (staff won't work otherwise, am troubleshooting) -->
  </div>
  
  <!-- End tags from staff -->

  <?php
  // Fetch and display the 'below the content' field if it is not empty
  $pod = pods('page', get_the_id());
  if (!empty($pod->field('below_the_content'))) {
    echo "<div class='below_the_content'>" . $pod->field('below_the_content') . "</div>";
  }
}
?>

<?php
get_footer(); // Include the footer template part
?>
