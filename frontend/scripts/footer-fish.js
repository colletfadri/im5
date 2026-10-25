(() => {
  'use strict';
  const reduced = matchMedia('(prefers-reduced-motion: reduce)');
  // 07 / A click or tap makes the footer name dive, then a fish jumps out.
  // Keep the ordinary home link until the decorative image is ready.
  const footerFish = document.querySelector('[data-footer-fish]');
  if (footerFish) {
    const fallback = footerFish.parentElement.querySelector('.footer-home');
    const name = footerFish.querySelector('.footer-brand-name');
    const image = footerFish.querySelector('img');
    let playing = false, resetTimer = 0;
    function resetFish() {
      playing = false;
      clearTimeout(resetTimer);
      footerFish.classList.remove('is-playing');
      footerFish.removeAttribute('aria-busy');
    }
    function readyFish() {
      if (!image.naturalWidth) return;
      footerFish.hidden = false;
      fallback.hidden = true;
    }
    footerFish.addEventListener('click', () => {
      if (playing || document.hidden) return;
      playing = true;
      footerFish.setAttribute('aria-busy', 'true');
      footerFish.classList.add('is-playing');
      // The timeout also restores the name if an animation-end event is lost.
      resetTimer = setTimeout(resetFish, 3000);
    });
    name.addEventListener('animationend', event => {
      if (event.target === name) resetFish();
    });
    name.addEventListener('animationcancel', resetFish);
    image.addEventListener('error', () => {
      resetFish(); footerFish.hidden = true; fallback.hidden = false;
    });
    image.addEventListener('load', readyFish, {once: true});
    function loadFish() {
      if (image.hasAttribute('src')) return;
      image.src = image.dataset.src;
      if (typeof image.decode === 'function') image.decode().then(readyFish).catch(() => {});
      else if (image.complete) readyFish();
    }
    const fishVisibility = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting) loadFish();
      else resetFish();
    }, {rootMargin: '400px 0px'});
    fishVisibility.observe(footerFish.parentElement);
    document.addEventListener('visibilitychange', () => { if (document.hidden) resetFish(); });
    addEventListener('pagehide', resetFish);
    reduced.addEventListener('change', resetFish);
  }


})();
