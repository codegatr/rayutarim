/**
 * RAYU TARIM MAKİNELERİ — Public site JS
 * v0.2.0 — Vanilla, dependency-free
 *
 * Modules:
 *  - Mobile nav toggle
 *  - Hero slider (autoplay, pause on hover, keyboard, swipe)
 *  - Scroll behavior (header shrink)
 */

(function () {
  'use strict';

  // ────────────────────────────────────────────────────────────────────────
  // Mobile Nav
  // ────────────────────────────────────────────────────────────────────────
  const navToggle = document.getElementById('navToggle');
  const mainNav   = document.getElementById('mainNav');
  if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
      const open = mainNav.classList.toggle('is-open');
      navToggle.classList.toggle('is-open', open);
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.style.overflow = open ? 'hidden' : '';
    });

    // Sayfa disina tiklamada kapat
    document.addEventListener('click', (e) => {
      if (!mainNav.classList.contains('is-open')) return;
      if (mainNav.contains(e.target) || navToggle.contains(e.target)) return;
      mainNav.classList.remove('is-open');
      navToggle.classList.remove('is-open');
      navToggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    });

    // Esc tusu
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mainNav.classList.contains('is-open')) {
        mainNav.classList.remove('is-open');
        navToggle.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      }
    });
  }

  // ────────────────────────────────────────────────────────────────────────
  // Hero Slider
  // ────────────────────────────────────────────────────────────────────────
  const slider = document.getElementById('heroSlider');
  if (slider) {
    const track    = document.getElementById('heroTrack');
    const slides   = slider.querySelectorAll('.hero__slide');
    const dots     = slider.querySelectorAll('.hero__dot');
    const prevBtn  = document.getElementById('heroPrev');
    const nextBtn  = document.getElementById('heroNext');
    const progress = document.getElementById('heroProgressBar');

    if (slides.length > 1 && track) {
      let current = 0;
      let timer   = null;
      let progTimer = null;
      const AUTOPLAY_MS = 6500;
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      const goTo = (index) => {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        current = index;
        track.style.transform = `translateX(-${index * 100}%)`;
        slides.forEach((s, i) => s.classList.toggle('is-active', i === index));
        dots.forEach((d, i) => {
          d.classList.toggle('is-active', i === index);
          d.setAttribute('aria-selected', i === index ? 'true' : 'false');
        });
        resetProgress();
      };

      const next = () => goTo(current + 1);
      const prev = () => goTo(current - 1);

      const startAuto = () => {
        if (reduce) return;
        stopAuto();
        timer = setInterval(next, AUTOPLAY_MS);
        startProgress();
      };
      const stopAuto = () => {
        if (timer) { clearInterval(timer); timer = null; }
        if (progTimer) { cancelAnimationFrame(progTimer); progTimer = null; }
      };

      let progStart = 0;
      const startProgress = () => {
        if (!progress || reduce) return;
        progStart = performance.now();
        const tick = (t) => {
          const elapsed = t - progStart;
          const pct = Math.min(100, (elapsed / AUTOPLAY_MS) * 100);
          progress.style.width = pct + '%';
          if (pct < 100 && timer) progTimer = requestAnimationFrame(tick);
        };
        progTimer = requestAnimationFrame(tick);
      };
      const resetProgress = () => {
        if (!progress) return;
        progress.style.width = '0%';
        startProgress();
      };

      // Buttons
      if (prevBtn) prevBtn.addEventListener('click', () => { prev(); startAuto(); });
      if (nextBtn) nextBtn.addEventListener('click', () => { next(); startAuto(); });

      // Dots
      dots.forEach((d) => {
        d.addEventListener('click', () => {
          const idx = parseInt(d.dataset.index || '0', 10);
          goTo(idx);
          startAuto();
        });
      });

      // Keyboard
      slider.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft')  { prev(); startAuto(); }
        if (e.key === 'ArrowRight') { next(); startAuto(); }
      });
      slider.setAttribute('tabindex', '0');

      // Swipe
      let touchX = null;
      slider.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; }, { passive: true });
      slider.addEventListener('touchend',   (e) => {
        if (touchX === null) return;
        const dx = e.changedTouches[0].clientX - touchX;
        if (Math.abs(dx) > 50) {
          if (dx < 0) next(); else prev();
          startAuto();
        }
        touchX = null;
      });

      // Pause on hover
      slider.addEventListener('mouseenter', stopAuto);
      slider.addEventListener('mouseleave', startAuto);

      // Pause when tab not visible
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) stopAuto(); else startAuto();
      });

      // Init
      goTo(0);
      startAuto();
    }
  }

  // ────────────────────────────────────────────────────────────────────────
  // Header shrink on scroll
  // ────────────────────────────────────────────────────────────────────────
  const header = document.getElementById('siteHeader');
  if (header) {
    let lastScroll = 0;
    const update = () => {
      const y = window.scrollY;
      header.classList.toggle('is-scrolled', y > 20);
      lastScroll = y;
    };
    update();
    window.addEventListener('scroll', () => { window.requestAnimationFrame(update); }, { passive: true });
  }

})();
