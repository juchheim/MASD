// Wait until the entire document is fully loaded
document.addEventListener('DOMContentLoaded', function() {
  const sliderContainer = document.querySelector('.slider-container'); // Get the container of the slider
  const slider = document.querySelector('.slider'); // Get the slider element

  // Check if the slider container and slider elements exist
  if (!sliderContainer || !slider) {
    console.log('Slider elements not found on this page.');
    return; // Exit the script if the slider is not present
  }

  const slides = document.querySelectorAll('.slider .slider-image'); // Get all the slide elements
  const slideCount = slides.length; // Get the number of slides
  const dotsContainer = document.querySelector('.slider-dots'); // Get the container for the dots
  let currentIndex = 1; // Start at the first slide (index 1 because of the cloned slides)
  let intervalId; // This will hold our interval ID for automatic sliding
  let isPlaying = true; // Flag to track whether the slideshow is playing or paused

  console.log('DOMContentLoaded: Slider initialized with', slideCount, 'slides');

  // Clone the first and last slides for a seamless transition
  const firstSlideClone = slides[0].cloneNode(true);
  const lastSlideClone = slides[slideCount - 1].cloneNode(true);
  slider.appendChild(firstSlideClone); // Add clone of the first slide at the end
  slider.insertBefore(lastSlideClone, slides[0]); // Add clone of the last slide at the beginning

  // Update the list of slides to include the cloned slides
  const allSlides = document.querySelectorAll('.slider .slider-image');
  const totalSlides = allSlides.length; // Total number of slides including clones

  // Set the width of the slider to accommodate all slides
  slider.style.width = `${totalSlides * 100}%`;
  allSlides.forEach(slide => {
      slide.style.width = `${100 / totalSlides}%`; // Set each slide's width. 100 represents 100% of the slider's width.
  });

  // Set the initial position of the slider without a transition
  slider.style.transition = 'none';
  slider.style.transform = `translateX(-${100 / totalSlides}%)`;

  // Function to update the slider position and handle video playback
  function updateSlider() {
      console.log('updateSlider: Updating slider position to index', currentIndex);
      const translateValue = -currentIndex * (100 / totalSlides);
      slider.style.transform = `translateX(${translateValue}%)`;

      const currentSlide = allSlides[currentIndex]; // Get the current slide based on the currentIndex
      const currentVideo = currentSlide.querySelector('video'); // Check if the current slide contains a video element
      const playPauseButton = document.querySelector('.play-pause'); // Get the play/pause button from the document

      console.log('updateSlider: Current slide', currentIndex, 'has video:', !!currentVideo);

      // Pause and reset all videos and remove event listeners
      allSlides.forEach((slide, index) => {
          const video = slide.querySelector('video');
          if (video) {
              video.pause();
              // video.currentTime = 0; // Reset video time to 0
              video.removeEventListener('ended', handleVideoEnded);
              console.log('updateSlider: Paused and reset video on slide', index);
          }
      });

      // If the current slide has a video, play it and hide the play/pause button
      if (currentVideo) {
        clearInterval(intervalId);  // Clear the interval to stop automatic slide transitions
        if (playPauseButton) {
            playPauseButton.style.display = 'none'; // Hide the play/pause button when a video is playing
        }
      /*  currentVideo.muted = false; */
        currentVideo.play(); // Play the video on the current slide
        console.log('updateSlider: Playing video on slide', currentIndex);
        currentVideo.addEventListener('ended', handleVideoEnded, { once: true }); // When the video ends, transition to the next slide
        currentVideo.addEventListener('click', toggleVideoPlayPause); // Toggle play/pause video on click
      } else {
        // If no video is present, reset the interval for automatic sliding
        if (playPauseButton) {
            playPauseButton.style.display = 'block';  // Show the play/pause button when no video is playing
        }
        resetInterval();  // Restart the interval for automatic slide transitions
        console.log('updateSlider: No video on slide', currentIndex, 'resetting interval');
      }
  }

  // Function to reset the video
  function resetVideo(slide) {
      const video = slide.querySelector('video');
      if (video) {
          video.pause();
          video.currentTime = 0; // Reset video time to 0
          console.log('resetVideo: Reset video on slide');
      }
  }

  // Function to handle when a video ends
  function handleVideoEnded() {
    console.log('handleVideoEnded: Video ended on slide', currentIndex);

    // Move to the next slide after the video ends
    nextSlide(true);

    // Update the play/pause button to show the "pause" icon
    const playPauseButton = document.querySelector('.play-pause');
    if (playPauseButton) {
        playPauseButton.innerHTML = "&#10074;&#10074;"; // Display pause icon (||)
    }

    // Set the slideshow state to "playing"
    isPlaying = true;
  }

  // Function to move to the next slide
  function nextSlide(fromVideoEnded = false) {
    console.log('nextSlide: Moving to next slide from index', currentIndex);

    if (slideCount <= 1) {
        console.log('Only one slide, not advancing.');
        return;
    }

    // Increment the current index to move to the next slide
    currentIndex++;

    // Update the slider's position to show the next slide
    updateSlider();

    // Update the navigation dots to highlight the current slide
    updateDots();

    // If we reach the end of the slider, reset to the first slide
    if (currentIndex === totalSlides - 1) {
        // After a short delay, reset the slider position to show the first actual slide
        setTimeout(() => {
            slider.style.transition = 'none'; // Temporarily disable transitions for the reset. Otherwise jankiness happens.
            currentIndex = 1; // Reset the index to the first actual slide (after the cloned last slide)
            updateSlider(); // Update the slider position immediately
            setTimeout(() => {
                slider.style.transition = ''; // Re-enable transitions
                updateDots(); // Update dots to reflect the current slide after reset
            }, 20); // Small delay to ensure the slider update takes effect before re-enabling transitions
        }, 500); // Delay the reset to allow the slide transition to complete smoothly
    }

    // If the transition to the next slide was not triggered by a video ending
    if (!fromVideoEnded) {
      // Reset the interval for automatic sliding
      resetInterval(); 
      
      // Log that the interval has been reset after moving to the next slide
      console.log('nextSlide: Interval reset after moving to slide', currentIndex);
    }
  }

  // Function to move to the previous slide.
  function prevSlide() {
      console.log('prevSlide: Moving to previous slide from index', currentIndex);

      if (slideCount <= 1) {
          console.log('Only one slide, not advancing.');
          return;
      }

      currentIndex--;
      updateSlider();
      updateDots();

      // If we reach the beginning of the slider, reset to the last slide
      if (currentIndex === 0) {
          setTimeout(() => {
              slider.style.transition = 'none';
              currentIndex = slideCount;
              updateSlider();
              setTimeout(() => {
                  slider.style.transition = '';
                  updateDots(); // Update dots after resetting the slider
              }, 20);
          }, 500);
      }

      resetInterval();
      console.log('prevSlide: Interval reset after moving to slide', currentIndex);
  }

  // Function to create dots for the slider navigation
  function createDots() {
      if (slideCount > 1) {
          for (let i = 0; i < slides.length; i++) {
              const dot = document.createElement('span');
              dot.classList.add('slider-dot');   // add slider-dot to the list of classes for the dots
              dot.dataset.index = i;
              dot.addEventListener('click', function() {
                  currentIndex = parseInt(this.dataset.index) + 1;
                  updateSlider();
                  updateDots();
                  resetInterval();
                  console.log('createDots: Dot clicked, moving to slide', currentIndex);
              });
              dotsContainer.appendChild(dot);
          }
          updateDots();
      } else {
          dotsContainer.style.display = 'none'; // Hide dots if there's only one slide
      }
  }

  // Function to update the active dot. Ensures that the correct navigation dot is highlighted (made active) based on the currently displayed slide.
  function updateDots() {
      const dots = document.querySelectorAll('.slider-dot');
      dots.forEach((dot, index) => {
          dot.classList.toggle('active', index === (currentIndex - 1) % slideCount);
      });
      console.log('updateDots: Dots updated, current active dot', currentIndex);
  }

  // Function to toggle play/pause state of the slideshow
  function togglePlayPause() {
      const playPauseButton = document.querySelector('.play-pause');
      if (isPlaying) { // isPlaying indicates that the slideshow is currently actively transitioning between slides automatically
          console.log('togglePlayPause: Pausing slideshow');
          clearInterval(intervalId);
          if (playPauseButton) {
              playPauseButton.innerHTML = "&#9658;"; // Change to play icon
          }
      } else {
          console.log('togglePlayPause: Resuming slideshow');
          resetInterval();
          if (playPauseButton) {
              playPauseButton.innerHTML = "&#10074;&#10074;"; // Change to pause icon
          }
      }
      isPlaying = !isPlaying; // Toggle the playing state
  }

  // Function to reset the interval for automatic sliding
  function resetInterval() {
      console.log('resetInterval: Resetting interval');
      clearInterval(intervalId);
      const currentSlide = allSlides[currentIndex];
      const currentVideo = currentSlide.querySelector('video');

      if (currentVideo) {
          console.log('resetInterval: Video detected on slide', currentIndex, 'waiting for it to end before moving to the next slide');
          currentVideo.addEventListener('ended', handleVideoEnded, { once: true }); // handles what happens when a video ends
      } else {
          console.log('resetInterval: No video detected on slide', currentIndex, 'setting interval for automatic sliding');
          intervalId = setInterval(() => nextSlide(), 8000); // Slide every 8 seconds
      }
  }

  // Function to play/pause video on click
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

  createDots(); // Create navigation dots

  const nextButton = document.querySelector('.next'); // Get the next button
  const prevButton = document.querySelector('.prev'); // Get the previous button
  const playPauseButton = document.querySelector('.play-pause'); // Get the play/pause button

  // Check if the next, previous, and play/pause buttons exist before adding event listeners
  if (nextButton && prevButton && playPauseButton) {
    // Add a click event listener to the "next" button to move to the next slide
    nextButton.addEventListener('click', () => {
        console.log('Next button clicked');
        nextSlide(); // Call the function to move to the next slide
    });

    // Add a click event listener to the "previous" button to move to the previous slide
    prevButton.addEventListener('click', () => {
        console.log('Previous button clicked');
        prevSlide(); // Call the function to move to the previous slide
    });

    // Add a click event listener to the play/pause button to toggle the slideshow's play/pause state
    playPauseButton.addEventListener('click', togglePlayPause); // Call the function to toggle play/pause state
  }

  slider.addEventListener('transitionend', () => {
    // Reset the video on the previous slide
    const previousSlideIndex = (currentIndex - 1 < 0) ? totalSlides - 2 : currentIndex - 1;
    const previousSlide = allSlides[previousSlideIndex];
    resetVideo(previousSlide);

    // Handle the case where we are transitioning to the cloned first slide from the real last slide
    if (currentIndex === 0) {
        resetVideo(allSlides[totalSlides - 2]);
    }

    // Handle the case where we are transitioning to the cloned last slide from the real first slide
    if (currentIndex === totalSlides - 1) {
        resetVideo(allSlides[1]);
    }
  });

  sliderContainer.classList.add('ready'); // Mark the slider as ready
  setTimeout(() => {
      slider.style.transition = '';
      updateSlider(); // Initial update of the slider
      updateDots(); // Initial update of the dots
      resetInterval(); // Start the automatic sliding
  }, 20); // Delay to ensure initial position is set without transition
});
