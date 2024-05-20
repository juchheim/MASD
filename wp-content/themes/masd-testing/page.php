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
// Run as soon as the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  // Get references to the slider container, slider, and individual slides
  const sliderContainer = document.querySelector('.slider-container');
  const slider = document.querySelector('.slider');
  const slides = document.querySelectorAll('.slider .slider-image');
  const slideCount = slides.length; // Total number of slides
  const dotsContainer = document.querySelector('.slider-dots'); // Container for navigation dots
  let currentIndex = 0; // Index of the current slide
  let intervalId; // ID of the interval for automatic slide transitions
  let isPlaying = true; // Flag to track the play/pause state

  // Set the width of the slider to be equal to the number of slides times 100%
  slider.style.width = `${slideCount * 100}%`;
  slides.forEach(slide => {
    // Set the width of each slide to be a fraction of the total width (100% divided by the number of slides)
    slide.style.width = `${100 / slideCount}%`;
  });

  // Function to update the slider position
  function updateSlider() {
    // Calculate the translation value based on the current index
    const translateValue = -currentIndex * (100 / slideCount);
    // Apply the translation to the slider
    slider.style.transform = `translateX(${translateValue}%)`;

    const currentSlide = slides[currentIndex]; // Get the current slide element
    const currentVideo = currentSlide.querySelector('video'); // Get the video element in the current slide (if any)
    const playPauseButton = document.querySelector('.play-pause'); // Get the play/pause button

    // Pause all videos
    slides.forEach(slide => {
      const video = slide.querySelector('video'); // Get the video element in each slide
      if (video) {
        video.pause(); // Pause the video if it exists
      }
    });

    // Play the current video if it exists
    if (currentVideo) {
      clearInterval(intervalId); // Clear the automatic slide interval
      playPauseButton.style.display = 'none'; // Hide the play/pause button
      currentVideo.play(); // Play the video
      // Add an event listener to move to the next slide when the video ends
      currentVideo.addEventListener('ended', function() {
        nextSlide(); // Move to the next slide
        resetInterval(); // Reset the automatic slide interval
      }, { once: true }); // Ensure the event listener is called only once
    } else {
      playPauseButton.style.display = 'block'; // Show the play/pause button if there's no video
      resetInterval(); // Reset the automatic slide interval
    }
  }

  // Function to move to the next slide
  function nextSlide() {
    // Increment the current index, wrapping around to the start if necessary
    currentIndex = (currentIndex + 1) % slideCount;
    updateSlider(); // Update the slider position
    updateDots(); // Update the navigation dots
  }

  // Function to move to the previous slide
  function prevSlide() {
    // Decrement the current index, wrapping around to the end if necessary
    currentIndex = (currentIndex - 1 + slideCount) % slideCount;
    updateSlider(); // Update the slider position
    updateDots(); // Update the navigation dots
    resetInterval(); // Reset the automatic slide interval
  }

  // Function to create navigation dots
  function createDots() {
    if (slideCount > 1) {
      // Create a dot for each slide
      for (let i = 0; i < slides.length; i++) {
        const dot = document.createElement('span'); // Create a new span element for the dot
        dot.classList.add('slider-dot'); // Add the 'slider-dot' class to the dot
        dot.dataset.index = i; // Store the slide index in the dot's dataset
        // Add an event listener to move to the corresponding slide when the dot is clicked
        dot.addEventListener('click', function() {
          currentIndex = parseInt(this.dataset.index); // Update the current index to the clicked dot's index
          updateSlider(); // Update the slider position
          updateDots(); // Update the navigation dots
          resetInterval(); // Reset the automatic slide interval
        });
        dotsContainer.appendChild(dot); // Add the dot to the container
      }
      updateDots(); // Initialize the dots
    } else {
      dotsContainer.style.display = 'none'; // Hide the dots container if there's only one slide
    }
  }

  // Function to update the active state of the navigation dots
  function updateDots() {
    const dots = document.querySelectorAll('.slider-dot'); // Get all dot elements
    dots.forEach((dot, index) => {
      // Toggle the 'active' class based on the current slide index
      dot.classList.toggle('active', index === currentIndex);
    });
  }

  // Function to toggle the play/pause state
  function togglePlayPause() {
    const playPauseButton = document.querySelector('.play-pause'); // Get the play/pause button
    if (isPlaying) {
      clearInterval(intervalId); // Pause the automatic slide interval
      playPauseButton.innerHTML = "&#9658;"; // Change to play icon
    } else {
      resetInterval(); // Resume the automatic slide interval
      playPauseButton.innerHTML = "&#10074;&#10074;"; // Change to pause icon
    }
    isPlaying = !isPlaying; // Toggle the play/pause flag
  }

  // Function to reset the automatic slide interval
  function resetInterval() {
    clearInterval(intervalId); // Clear the existing interval
    const currentSlide = slides[currentIndex]; // Get the current slide
    const currentVideo = currentSlide.querySelector('video'); // Get the video element in the current slide
    
    // If there is a video on the current slide, wait for the video to end before moving to the next slide
    if (currentVideo) {
      currentVideo.addEventListener('ended', nextSlide, { once: true });
    } else {
      intervalId = setInterval(nextSlide, 8000); // Set a new interval for automatic sliding
    }
  }

  createDots(); // Create the navigation dots

  // Get references to the navigation buttons and play/pause button
  const nextButton = document.querySelector('.next');
  const prevButton = document.querySelector('.prev');
  const playPauseButton = document.querySelector('.play-pause');

  // Add event listeners to the navigation buttons
  if (nextButton && prevButton && playPauseButton) {
    nextButton.addEventListener('click', nextSlide); // Move to the next slide when clicked
    prevButton.addEventListener('click', prevSlide); // Move to the previous slide when clicked
    playPauseButton.addEventListener('click', togglePlayPause); // Toggle play/pause when clicked
  }

  updateSlider(); // Call updateSlider to initialize the slider state and check the first slide
  sliderContainer.classList.add('ready'); // Indicate that the slider is ready
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
