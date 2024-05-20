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
  
    console.log('DOMContentLoaded: Slider initialized with', slideCount, 'slides');
  
    // Set the width of the slider to be equal to the number of slides times 100%
    slider.style.width = `${slideCount * 100}%`;
    slides.forEach(slide => {
      // Set the width of each slide to be a fraction of the total width (100% divided by the number of slides)
      slide.style.width = `${100 / slideCount}%`;
    });
  
    // Function to update the slider position
    function updateSlider() {
      console.log('updateSlider: Updating slider position to index', currentIndex);
      const translateValue = -currentIndex * (100 / slideCount); // Calculate the translation value based on the current index
      slider.style.transform = `translateX(${translateValue}%)`; // Apply the translation to the slider
  
      const currentSlide = slides[currentIndex]; // Get the current slide element
      const currentVideo = currentSlide.querySelector('video'); // Get the video element in the current slide (if any)
      const playPauseButton = document.querySelector('.play-pause'); // Get the play/pause button
  
      console.log('updateSlider: Current slide', currentIndex, 'has video:', !!currentVideo);
  
      // Pause all videos
      slides.forEach((slide, index) => {
        const video = slide.querySelector('video'); // Get the video element in each slide
        if (video) {
          video.pause(); // Pause the video if it exists
          video.removeEventListener('ended', handleVideoEnded); // Remove the ended event listener
          console.log('updateSlider: Paused video on slide', index);
        }
      });
  
      // Play the current video if it exists
      if (currentVideo) {
        clearInterval(intervalId); // Clear the automatic slide interval
        if (playPauseButton) {
          playPauseButton.style.display = 'none'; // Hide the play/pause button
        }
        currentVideo.play(); // Play the video
        console.log('updateSlider: Playing video on slide', currentIndex);
        currentVideo.addEventListener('ended', handleVideoEnded, { once: true }); // Add an event listener to move to the next slide when the video ends
  
        // Add event listener to pause/play the video on click
        currentVideo.addEventListener('click', toggleVideoPlayPause);
      } else {
        if (playPauseButton) {
          playPauseButton.style.display = 'block'; // Show the play/pause button if there's no video
        }
        resetInterval(); // Reset the automatic slide interval
        console.log('updateSlider: No video on slide', currentIndex, 'resetting interval');
      }
    }
  
    // Function to handle the end of the video
    function handleVideoEnded() {
      console.log('handleVideoEnded: Video ended on slide', currentIndex);
      nextSlide(true); // Move to the next slide
      const playPauseButton = document.querySelector('.play-pause');
      if (playPauseButton) {
        playPauseButton.innerHTML = "&#10074;&#10074;"; // Ensure the play/pause button is in the pause state
      }
      isPlaying = true; // Set the play state to true
    }
  
    // Function to move to the next slide
    function nextSlide(fromVideoEnded = false) {
      console.log('nextSlide: Moving to next slide from index', currentIndex);
      currentIndex = (currentIndex + 1) % slideCount; // Increment the current index, wrapping around to the start if necessary
      updateSlider(); // Update the slider position
      updateDots(); // Update the navigation dots
      if (!fromVideoEnded) {
        resetInterval(); // Reset the automatic slide interval
        console.log('nextSlide: Interval reset after moving to slide', currentIndex);
      }
    }
  
    // Function to move to the previous slide
    function prevSlide() {
      console.log('prevSlide: Moving to previous slide from index', currentIndex);
      currentIndex = (currentIndex - 1 + slideCount) % slideCount; // Decrement the current index, wrapping around to the end if necessary
      updateSlider(); // Update the slider position
      updateDots(); // Update the navigation dots
      resetInterval(); // Reset the automatic slide interval
      console.log('prevSlide: Interval reset after moving to slide', currentIndex);
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
            console.log('createDots: Dot clicked, moving to slide', currentIndex);
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
      console.log('updateDots: Dots updated, current active dot', currentIndex);
    }
  
    // Function to toggle the play/pause state
    function togglePlayPause() {
      const playPauseButton = document.querySelector('.play-pause'); // Get the play/pause button
      if (isPlaying) {
        console.log('togglePlayPause: Pausing slideshow');
        clearInterval(intervalId); // Pause the automatic slide interval
        if (playPauseButton) {
          playPauseButton.innerHTML = "&#9658;"; // Change to play icon
        }
      } else {
        console.log('togglePlayPause: Resuming slideshow');
        resetInterval(); // Resume the automatic slide interval
        if (playPauseButton) {
          playPauseButton.innerHTML = "&#10074;&#10074;"; // Change to pause icon
        }
      }
      isPlaying = !isPlaying; // Toggle the play/pause flag
    }
  
    // Function to reset the automatic slide interval
    function resetInterval() {
      console.log('resetInterval: Resetting interval');
      clearInterval(intervalId); // Clear the existing interval
      const currentSlide = slides[currentIndex]; // Get the current slide
      const currentVideo = currentSlide.querySelector('video'); // Get the video element in the current slide
      
      // If there is a video on the current slide, wait for the video to end before moving to the next slide
      if (currentVideo) {
        console.log('resetInterval: Video detected on slide', currentIndex, 'waiting for it to end before moving to the next slide');
        currentVideo.addEventListener('ended', handleVideoEnded, { once: true });
      } else {
        console.log('resetInterval: No video detected on slide', currentIndex, 'setting interval for automatic sliding');
        intervalId = setInterval(() => nextSlide(), 8000); // Set a new interval for automatic sliding
      }
    }
  
    // Function to toggle video play/pause on click
    function toggleVideoPlayPause(event) {
      const video = event.target;
      if (video.paused) {
        video.play();
        console.log('toggleVideoPlayPause: Playing video');
      } else {
        video.pause();
        console.log('toggleVideoPlayPause: Pausing video');
      }
    }
  
    createDots(); // Create the navigation dots
  
    // Get references to the navigation buttons and play/pause button
    const nextButton = document.querySelector('.next');
    const prevButton = document.querySelector('.prev');
    const playPauseButton = document.querySelector('.play-pause');
  
    // Add event listeners to the navigation buttons
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
  
    updateSlider(); // Initial update of the slider
    sliderContainer.classList.add('ready'); // Indicate that the slider is ready
  });