document.addEventListener('DOMContentLoaded', function() {
  const sliderContainer = document.querySelector('.slider-container');
  const slider = document.querySelector('.slider');
  const slides = document.querySelectorAll('.slider .slider-image');
  const slideCount = slides.length; 
  const dotsContainer = document.querySelector('.slider-dots');
  let currentIndex = 1; // Start at the first slide (which is now index 1 due to the clone)
  let intervalId;
  let isPlaying = true;

  console.log('DOMContentLoaded: Slider initialized with', slideCount, 'slides');

  // Clone the first and last slides
  const firstSlideClone = slides[0].cloneNode(true);
  const lastSlideClone = slides[slideCount - 1].cloneNode(true);
  slider.appendChild(firstSlideClone);
  slider.insertBefore(lastSlideClone, slides[0]);

  const allSlides = document.querySelectorAll('.slider .slider-image'); // Update slides to include clones
  const totalSlides = allSlides.length; // Total number of slides including clones

  slider.style.width = `${totalSlides * 100}%`;
  allSlides.forEach(slide => {
      slide.style.width = `${100 / totalSlides}%`;
  });

  // Set the initial position of the slider without transition
  slider.style.transition = 'none';
  slider.style.transform = `translateX(-${100 / totalSlides}%)`;

  function updateSlider() {
      console.log('updateSlider: Updating slider position to index', currentIndex);
      const translateValue = -currentIndex * (100 / totalSlides);
      slider.style.transform = `translateX(${translateValue}%)`;

      const currentSlide = allSlides[currentIndex];
      const currentVideo = currentSlide.querySelector('video');
      const playPauseButton = document.querySelector('.play-pause');

      console.log('updateSlider: Current slide', currentIndex, 'has video:', !!currentVideo);

      allSlides.forEach((slide, index) => {
          const video = slide.querySelector('video');
          if (video) {
              video.pause();
              video.removeEventListener('ended', handleVideoEnded);
              console.log('updateSlider: Paused video on slide', index);
          }
      });

      if (currentVideo) {
          clearInterval(intervalId);
          if (playPauseButton) {
              playPauseButton.style.display = 'none';
          }
          currentVideo.play();
          console.log('updateSlider: Playing video on slide', currentIndex);
          currentVideo.addEventListener('ended', handleVideoEnded, { once: true });

          currentVideo.addEventListener('click', toggleVideoPlayPause);
      } else {
          if (playPauseButton) {
              playPauseButton.style.display = 'block';
          }
          resetInterval();
          console.log('updateSlider: No video on slide', currentIndex, 'resetting interval');
      }
  }

  function handleVideoEnded() {
      console.log('handleVideoEnded: Video ended on slide', currentIndex);
      nextSlide(true);
      const playPauseButton = document.querySelector('.play-pause');
      if (playPauseButton) {
          playPauseButton.innerHTML = "&#10074;&#10074;";
      }
      isPlaying = true;
  }

  function nextSlide(fromVideoEnded = false) {
      console.log('nextSlide: Moving to next slide from index', currentIndex);
      currentIndex++;
      updateSlider();
      updateDots();

      if (currentIndex === totalSlides - 1) {
          setTimeout(() => {
              slider.style.transition = 'none';
              currentIndex = 1;
              updateSlider();
              setTimeout(() => {
                  slider.style.transition = '';
                  updateDots(); // Update dots after resetting the slider
              }, 20);
          }, 500);
      }

      if (!fromVideoEnded) {
          resetInterval();
          console.log('nextSlide: Interval reset after moving to slide', currentIndex);
      }
  }

  function prevSlide() {
      console.log('prevSlide: Moving to previous slide from index', currentIndex);
      currentIndex--;
      updateSlider();
      updateDots();

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

  function createDots() {
      if (slideCount > 1) {
          for (let i = 0; i < slides.length; i++) {
              const dot = document.createElement('span');
              dot.classList.add('slider-dot');
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
          dotsContainer.style.display = 'none';
      }
  }

  function updateDots() {
      const dots = document.querySelectorAll('.slider-dot');
      dots.forEach((dot, index) => {
          dot.classList.toggle('active', index === (currentIndex - 1) % slideCount);
      });
      console.log('updateDots: Dots updated, current active dot', currentIndex);
  }

  function togglePlayPause() {
      const playPauseButton = document.querySelector('.play-pause');
      if (isPlaying) {
          console.log('togglePlayPause: Pausing slideshow');
          clearInterval(intervalId);
          if (playPauseButton) {
              playPauseButton.innerHTML = "&#9658;";
          }
      } else {
          console.log('togglePlayPause: Resuming slideshow');
          resetInterval();
          if (playPauseButton) {
              playPauseButton.innerHTML = "&#10074;&#10074;";
          }
      }
      isPlaying = !isPlaying;
  }

  function resetInterval() {
      console.log('resetInterval: Resetting interval');
      clearInterval(intervalId);
      const currentSlide = allSlides[currentIndex];
      const currentVideo = currentSlide.querySelector('video');

      if (currentVideo) {
          console.log('resetInterval: Video detected on slide', currentIndex, 'waiting for it to end before moving to the next slide');
          currentVideo.addEventListener('ended', handleVideoEnded, { once: true });
      } else {
          console.log('resetInterval: No video detected on slide', currentIndex, 'setting interval for automatic sliding');
          intervalId = setInterval(() => nextSlide(), 8000);
      }
  }

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

  sliderContainer.classList.add('ready');
  setTimeout(() => {
      slider.style.transition = '';
      updateSlider(); // Initial update of the slider
      updateDots(); // Initial update of the dots
      resetInterval(); // Start the automatic sliding
  }, 20); // Delay to ensure initial position is set without transition
});
