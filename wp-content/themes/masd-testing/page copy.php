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
// Check if this is the front page
if (is_front_page()) {
?>

<div class="slider-container">
  <div class="slider-dots"></div>
  <div class="slider">
    <?php
    // Fetch all slider items using Pods
    $pod = pods('slider');
    $params = array(
      'limit' => -1, // No limit on the number of items fetched
      'orderby' => 'menu_order ASC' // Sort by the order set in the admin
    );
    
    $pod->find($params);
    if ($pod->total() > 0) {
      // Loop through each slider item
      while ($pod->fetch()) {
        $image = $pod->display('image'); // Get the image URL
        $title = $pod->display('title'); // Get the title
        $link = $pod->display('link'); // Get the link

        // Display each slider image, wrapping it in a link if a link is provided
        if (!empty($link)) {
          echo "<div class='slider-image'><a href='".$link."' target='_blank'><img src='".$image."' alt='".$title."' /></a></div>";
        } else {
          echo "<div class='slider-image'><img src='".$image."' alt='".$title."' /></div>";
        }
      }
    }
    ?>
  </div>
  <!-- Navigation and Control buttons, shown only if there is more than one slide -->
  <?php if ($pod->total() > 1) : ?>
    <button class="prev">&#10094;</button> <!-- HTML entity for left arrow -->
    <button class="next">&#10095;</button> <!-- HTML entity for right arrow -->
    <div id="play-pause-wrapper"><button class="play-pause">&#10074;&#10074;</button></div> <!-- HTML entity for play/pause icons -->
  <?php endif; ?>
</div>

<script>
// JavaScript to handle the slider functionality
document.addEventListener('DOMContentLoaded', function() {
  // Initialize variables and select DOM elements for the slider functionality.
  // This includes the container, individual slides, and navigation dots.
  // It also sets up variables to keep track of the current slide index, the interval ID for automatic slide transitions, and the play/pause state.

  const sliderContainer = document.querySelector('.slider-container'); // Get the slider container element
  const slider = document.querySelector('.slider'); // Get the slider element that contains all the slides
  const slides = document.querySelectorAll('.slider .slider-image'); // Get all individual slide elements
  const slideCount = slides.length; // Get the total number of slides
  const dotsContainer = document.querySelector('.slider-dots'); // Get the container element for the slider dots
  let currentIndex = 0; // Initialize the current index to 0, representing the first slide
  let intervalId; // Declare a variable to hold the ID of the interval for automatic slide transitions
  let isPlaying = true; // Keep track of whether the slider is playing

  // Set the width of the slider to be the number of slides times 100%
  slider.style.width = `${slideCount * 100}%`;

  // Set the width of each slide to be 100% divided by the number of slides
  slides.forEach(slide => {
    slide.style.width = `${100 / slideCount}%`;
  });

  // Function to update the slider position
  function updateSlider() {
    const translateValue = -currentIndex * (100 / slideCount);
    slider.style.transform = `translateX(${translateValue}%)`;
  }

  // Function to show the next slide
  function nextSlide() {
    currentIndex = (currentIndex + 1) % slideCount;
    updateSlider();
    updateDots();
    resetInterval();
  }

  // Function to show the previous slide
  function prevSlide() {
    currentIndex = (currentIndex - 1 + slideCount) % slideCount;
    updateSlider();
    updateDots();
    resetInterval();
  }

  // Function to create dots for the slider
function createDots() {
  // Check if there are more than one slide
  if (slideCount > 1) {
    // Loop through each slide to create a corresponding dot
    for (let i = 0; i < slides.length; i++) {
      const dot = document.createElement('span'); // Create a new span element for the dot
      dot.classList.add('slider-dot'); // Add the 'slider-dot' class to the dot
      dot.dataset.index = i; // Set a data attribute with the index of the dot
      // Add a click event listener to the dot
      dot.addEventListener('click', function() {
        currentIndex = parseInt(this.dataset.index); // Update the currentIndex to the clicked dot's index
        updateSlider(); // Update the slider to show the corresponding slide
        updateDots(); // Update the dots to reflect the current slide
        resetInterval(); // Reset the interval for automatic sliding
      });
      dotsContainer.appendChild(dot); // Append the dot to the dots container
    }
    updateDots(); // Initial call to update the dots to reflect the current slide
  } else {
    dotsContainer.style.display = 'none'; // Hide the dots container if there is only one slide
  }
}

  // Function to update the active dot
  function updateDots() {
    const dots = document.querySelectorAll('.slider-dot'); // Select all dot elements
    // Loop through each dot and update its 'active' class
    dots.forEach((dot, index) => {
      // Add or remove the 'active' class based on whether the dot's index matches the current slide index
      dot.classList.toggle('active', index === currentIndex);
    });
  }

  // Function to toggle play/pause
  function togglePlayPause() {
    // Check if the slider is currently playing
    if (isPlaying) {
      clearInterval(intervalId);
      playPauseButton.innerHTML = "&#9658;"; // Change to play icon
    } else {
      resetInterval(); // Restart the automatic slide transition
      playPauseButton.innerHTML = "&#10074;&#10074;"; // Change to pause icon
    }
    isPlaying = !isPlaying;
  }

  // Function to reset the slide interval
  function resetInterval() {
    clearInterval(intervalId); // Clear the existing interval that controls the automatic slide transition
    intervalId = setInterval(nextSlide, 8000); // Start a new interval to automatically move to the next slide every 8 seconds
  }

  createDots(); // Create navigation dots for the slider

  const nextButton = document.querySelector('.next'); // Select the "next" button element
  const prevButton = document.querySelector('.prev'); // Select the "previous" button element
  const playPauseButton = document.querySelector('.play-pause'); // Select the "play/pause" button element

  // Add event listeners for navigation buttons if they exist
  if (nextButton && prevButton && playPauseButton) {
    nextButton.addEventListener('click', nextSlide); // Move to the next slide when the "next" button is clicked
    prevButton.addEventListener('click', prevSlide); // Move to the previous slide when the "prev" button is clicked
    playPauseButton.addEventListener('click', togglePlayPause); // Toggle play/pause when the "play/pause" button is clicked
  }


  resetInterval(); // Start the initial interval

  // Make the slider visible
  sliderContainer.classList.add('ready');
}); // End of the DOMContentLoaded event listener callback function
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
