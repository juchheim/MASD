document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.slider');
    const images = document.querySelectorAll('.slider img');
    const slideCount = images.length;
    const dotsContainer = document.querySelector('.slider-dots');
    let currentIndex = 0;
    let intervalId; // Declare intervalId variable to store setInterval ID
  
    function updateSlider() {
      const translateValue = -currentIndex * 100;
      slider.style.transform = `translateX(${translateValue}%)`;
    }
  
    function nextSlide() {
      currentIndex = (currentIndex + 1) % slideCount;
      updateSlider();
      updateDots();
    }
  
    function prevSlide() {
      currentIndex = (currentIndex - 1 + slideCount) % slideCount;
      updateSlider();
      updateDots();
    }
  
    function createDots() {
      for (let i = 0; i < images.length; i++) {
        const dot = document.createElement('span');
        dot.classList.add('slider-dot');
        dot.dataset.index = i;
        dot.addEventListener('click', function() {
          currentIndex = parseInt(this.dataset.index);
          updateSlider();
          updateDots();
          resetInterval(); // Reset interval when dot is clicked
        });
        dotsContainer.appendChild(dot);
      }
      updateDots();
    }
  
    function updateDots() {
      const dots = document.querySelectorAll('.slider-dot');
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentIndex);
      });
    }
  
    function resetInterval() {
      clearInterval(intervalId); // Clear existing interval
      intervalId = setInterval(nextSlide, 8000); // Start a new interval
    }
  
    createDots();
  
    const nextButton = document.querySelector('.next');
    const prevButton = document.querySelector('.prev');
    nextButton.addEventListener('click', nextSlide);
    prevButton.addEventListener('click', prevSlide);
  
    resetInterval(); // Start the initial interval
  });