window.addEventListener('DOMContentLoaded', () => {
  // Initialize GSAP animations
  gsap.registerPlugin(ScrollTrigger);

  // Initialize the date and time display
  initDateTime();

  // Add section one animations
  addSectionOneAnimation();
});

function initDateTime() {
  const dateTimeElement = document.querySelector('.date-time');

  if (!dateTimeElement) {
    throw new Error('Date-time element not found');
  }

  // Add an interval to update the date and time every second
  // Date must be in the format "DHH:MM:SS"
  setInterval(() => {
    const now = new Date();
    const formattedDate = now.toLocaleDateString('en-US', {
      weekday: 'short'
    });
    const formattedTime = now.toLocaleTimeString('it-IT', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });

    dateTimeElement.textContent = `${formattedDate}${formattedTime}`;
  }, 1000);
}

function addSectionOneAnimation() {
  const sectionOne = document.querySelector('.section-1');
  if (!sectionOne) {
    throw new Error('Section 1 element not found');
  }

  const heroBackground = sectionOne.querySelector('.hero-background');
  const heroImage = sectionOne.querySelector('.hero-image');
  const titleContainer = sectionOne.querySelector('.hero-title-container');
  const titleElement = sectionOne.querySelector('.hero-title-container h1');

  if (!heroBackground || !heroImage || !titleContainer || !titleElement) {
    throw new Error('Required elements for animation not found');
  }

  const split = new SplitType(titleElement, { types: 'chars',
    classNames: 'hero-title-split-chars' });

  gsap.set([heroBackground, heroImage], { opacity: 0 });
  gsap.set(titleContainer, { '--scroll-indicator-scale': 0 });
  gsap.set(split.chars, { opacity: 0, y: '100%' });

  // Timeline
  const tl = gsap.timeline();

  tl.to(heroBackground, {
    opacity: 1,
    duration: 0.5,
    ease: 'power2.out'
  });

  tl.to(titleContainer, {
    '--scroll-indicator-scale': 1,
    duration: 0.5,
    ease: 'power2.out'
  }, '+=0.3'); 

  tl.to(split.chars, {
    opacity: 1,
    y: 0,
    duration: 0.8,
    stagger: 0.05,
    delay: () => Math.random() * 0.5,
    ease: 'power2.out'
  }, '+=0.1');

  tl.to(titleContainer, {
    '--scroll-indicator-origin': 'right',
    '--scroll-indicator-scale': 0,
    duration: 0.5,
    ease: 'power2.out'
  }, '+=0.2');

  tl.to(heroImage, {
    opacity: 1,
    duration: 0.5,
    ease: 'power2.out'
  }, '+=0.3');
}