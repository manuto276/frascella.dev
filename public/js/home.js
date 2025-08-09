// js/home.js
(() => {
  const on = (t, s, h, opts) => t.addEventListener(s, h, opts || { passive: true });

  /* ---------- Live Clock ---------- */
  const initLiveClock = () => {
    const el = document.querySelector('.date-time');
    if (!el) return; // graceful: non bloccare se manca

    const tick = () => {
      const now = new Date();
      const day = now.toLocaleDateString('en-US', { weekday: 'short' }); // e.g. Mon
      const time = now.toLocaleTimeString('it-IT', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      el.textContent = `${day} ${time}`; // "Mon 18:23:01"
    };

    tick(); // render immediato
    const id = setInterval(tick, 1000);

    // opzionale: pausa quando tab hidden
    on(document, 'visibilitychange', () => {
      if (document.hidden) { clearInterval(id); }
    });
  };

  /* ---------- Fitty (hero title) ---------- */
  const initHeroFit = () => {
    const el = document.getElementById('hero-title');
    if (!el) return;

    // evita doppie inizializzazioni
    if (el._fitted) return;

    if (typeof window.fitty === 'function') {
      window.fitty(el, { minSize: 20, maxSize: 640, multiLine: false });
      el._fitted = true;
    }
  };

  /* ---------- Resize (debounced) ---------- */
  const initResizeHandler = () => {
    let raf = null;
    on(window, 'resize', () => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        raf = null;
        initHeroFit();
      });
    });
  };

  /* ---------- GSAP plugin (safe) ---------- */
  const registerGsapPlugins = () => {
    if (window.gsap && window.ScrollTrigger && typeof gsap.registerPlugin === 'function') {
      gsap.registerPlugin(ScrollTrigger);
    }
  };

  /* ---------- DOM Ready ---------- */
  on(window, 'DOMContentLoaded', () => {
    registerGsapPlugins();
    initLiveClock();
    initHeroFit();
    initResizeHandler();
  });
})();
