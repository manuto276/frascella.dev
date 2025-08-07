// Initialize the date and time display
initDateTime();

// Fit text in the hero section
fitText();

window.addEventListener('DOMContentLoaded', () => {
  // Initialize GSAP animations
  gsap.registerPlugin(ScrollTrigger);
});

window.addEventListener('resize', () => {
  // Refit text on window resize
  fitText();
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

function fitText() {
  const heroTitleEl = document.getElementById('hero-title');
  if (!heroTitleEl) {
    throw new Error('Hero title element not found');
  }
  fitText(heroTitleEl, {
    minFontSize: 20,
    maxFontSize: 640,
  });
}