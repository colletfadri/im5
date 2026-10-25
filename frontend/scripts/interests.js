(() => {
  'use strict';
  const reduced = matchMedia('(prefers-reduced-motion: reduce)');
  // 08 / Interests follow the native scroll position in either direction.
  // No time-based entrance animation, pinned panel or wheel interception.
  const interests = document.querySelector('.interests');
  if (interests) {
    const panels = Array.from(interests.querySelectorAll('[data-interest]')).map(node => ({
      node, heading: node.querySelector('h2'), visual: node.querySelector('.interest-visual'),
      headingProgress: -1, imageProgress: -1,
    }));
    let interestFrame = 0, interestsNear = true;
    const clamp = value => Math.min(1, Math.max(0, value));
    const phase = (value, start, end) => {
      const t = clamp((value - start) / (end - start));
      return t * t * (3 - 2 * t);
    };
    const interestsEnabled = () => interests.dataset.interestMotion === 'on' && !reduced.matches;
    function drawInterests() {
      interestFrame = 0;
      if (!interestsEnabled()) { interests.classList.remove('scroll-story'); return; }
      const height = document.documentElement.clientHeight || innerHeight;
      // Read all bounds first; transforms only affect the child elements.
      const positions = panels.map(panel => {
        // Each element must enter the viewport itself before it starts moving.
        // This matters on phones, where the image sits below the text.
        return {
          heading: clamp((height * .76 - panel.heading.getBoundingClientRect().top) / (height * .46)),
          image: panel.visual ? clamp((height * .78 - panel.visual.getBoundingClientRect().top) / (height * .42)) : 1,
        };
      });
      panels.forEach((panel, index) => {
        const progress = positions[index];
        if (Math.abs(panel.headingProgress - progress.heading) < .0005 && Math.abs(panel.imageProgress - progress.image) < .0005) return;
        panel.headingProgress = progress.heading; panel.imageProgress = progress.image;
        const horror = panel.node.classList.contains('interest--horror');
        const direction = panel.node.classList.contains('interest--reverse') ? 1 : -1;
        const heading = phase(progress.heading, 0, 1);
        const text = phase(progress.heading, .18, 1);
        const image = phase(progress.image, horror ? .12 : 0, 1);
        const distance = innerWidth <= 700 ? 24 : 64;
        const values = {
          '--interest-heading': heading,
          '--interest-copy-x': (horror ? 0 : direction * distance * (1 - heading)).toFixed(2) + 'px',
          '--interest-text': text,
          '--interest-text-y': (10 * (1 - text)).toFixed(2) + 'px',
          '--interest-image': image,
          '--interest-image-x': (horror ? 0 : -direction * distance * (1 - image)).toFixed(2) + 'px',
          '--interest-light': .35 + image * .65,
          '--interest-corners': phase(progress.image, .35, .96),
        };
        Object.entries(values).forEach(([key, value]) => panel.node.style.setProperty(key, String(value)));
      });
      interests.classList.add('scroll-story');
    }
    function queueInterests(force = false) {
      if (!interestFrame && !document.hidden && (force || interestsNear)) interestFrame = requestAnimationFrame(drawInterests);
    }
    const interestVisibility = new IntersectionObserver(entries => {
      interestsNear = entries[0].isIntersecting;
      queueInterests(true);
    }, {rootMargin: '400px 0px'});
    interestVisibility.observe(interests);
    addEventListener('scroll', () => queueInterests(), {passive: true});
    addEventListener('resize', () => {
      panels.forEach(panel => { panel.headingProgress = -1; panel.imageProgress = -1; });
      queueInterests(true);
    });
    addEventListener('pageshow', () => queueInterests(true));
    document.fonts.ready.then(() => queueInterests(true));
    if ('ResizeObserver' in window) {
      const interestResize = new ResizeObserver(() => queueInterests(true));
      panels.forEach(panel => interestResize.observe(panel.node));
    }
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) { cancelAnimationFrame(interestFrame); interestFrame = 0; }
      else queueInterests(true);
    });
    reduced.addEventListener('change', () => {
      cancelAnimationFrame(interestFrame); interestFrame = 0;
      interests.classList.remove('scroll-story');
      panels.forEach(panel => { panel.headingProgress = -1; panel.imageProgress = -1; });
      queueInterests(true);
    });
    drawInterests();
  }

})();
