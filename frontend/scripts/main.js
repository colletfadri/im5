/** Native scrolling and links remain usable without JavaScript. */
(() => {
  'use strict';
  const settings = window.fexPortfolio || {};
  const reduced = matchMedia('(prefers-reduced-motion: reduce)');
  const mouse = matchMedia('(hover: hover) and (pointer: fine)');
  const wide = matchMedia('(min-width: 900px)');
  const behavior = () => reduced.matches ? 'instant' : 'smooth';
  const dialog = document.querySelector('#project-dialog');
  const cards = [...document.querySelectorAll('.project')];
  const cardVideos = cards.map(card => card.querySelector('.card-preview')).filter(Boolean);
  document.documentElement.classList.add('js');
  document.documentElement.classList.toggle('motion-arrows', settings.hoverArrows === true);

  // 01 / Floating previews. A shared image/video never intercepts the mouse.
  const preview = document.createElement('div');
  preview.className = 'cursor-preview';
  preview.setAttribute('aria-hidden', 'true');
  preview.innerHTML = '<div class="cursor-preview-media"><img alt=""><video muted loop playsinline preload="none" tabindex="-1"></video></div><div class="cursor-preview-caption"><div><strong></strong><span></span></div><i>↗</i></div>';
  if (cards.length && settings.hoverPreview === true) document.body.append(preview);
  const previewImage = preview.querySelector('img');
  const previewVideo = preview.querySelector('video');
  previewVideo.muted = true;
  let activeCard = null, previewFrame = 0, previewTimer = 0, previewRequest = 0;
  let pointerX = 0, pointerY = 0, currentX = 0, currentY = 0;
  let previewWidth = 0, previewHeight = 0;
  const floatingEnabled = () => settings.hoverPreview === true && mouse.matches && wide.matches && !reduced.matches && !dialog?.open;
  function stopCardPreviews() {
    cardVideos.forEach(video => {
      if (!video.paused) video.pause();
      video.closest('.project')?.classList.remove('preview-playing');
    });
  }
  function hidePreview() {
    activeCard = null; previewRequest++;
    clearTimeout(previewTimer); cancelAnimationFrame(previewFrame); previewFrame = 0;
    preview.classList.remove('is-visible', 'is-playing');
    previewVideo.pause();
    if (previewVideo.hasAttribute('src')) { previewVideo.removeAttribute('src'); previewVideo.load(); }
  }
  function stopPreviews() { if (activeCard) hidePreview(); stopCardPreviews(); }
  function destination() {
    const gap = 24, margin = 16;
    let x = pointerX + gap;
    const y = pointerY - previewHeight * .35;
    if (x + previewWidth > innerWidth - margin) x = pointerX - previewWidth - gap;
    return {
      x: Math.max(margin, Math.min(x, innerWidth - previewWidth - margin)),
      y: Math.max(margin, Math.min(y, innerHeight - previewHeight - margin)),
    };
  }
  function movePreview() {
    if (!activeCard) { previewFrame = 0; return; }
    const target = destination();
    currentX += (target.x - currentX) * .18; currentY += (target.y - currentY) * .18;
    preview.style.transform = 'translate3d(' + currentX + 'px, ' + currentY + 'px, 0)';
    previewFrame = Math.abs(target.x - currentX) + Math.abs(target.y - currentY) > .3 ? requestAnimationFrame(movePreview) : 0;
  }
  function showPreview(card, event) {
    if (!floatingEnabled() || event.pointerType !== 'mouse' || event.buttons || card.closest('.dragging')) return;
    if (activeCard === card) {
      pointerX = event.clientX; pointerY = event.clientY;
      if (!previewFrame) previewFrame = requestAnimationFrame(movePreview);
      return;
    }
    const image = card.querySelector('.card-media');
    if (!image) return;
    hidePreview(); stopCardPreviews(); activeCard = card;
    pointerX = event.clientX; pointerY = event.clientY;
    previewImage.src = image.currentSrc || image.src;
    preview.classList.toggle('fit-contain', !!card.querySelector('.fit-contain'));
    preview.querySelector('strong').textContent = card.querySelector('h3')?.textContent || '';
    preview.querySelector('.cursor-preview-caption span').textContent = card.querySelector('.caption p')?.textContent || '';
    previewWidth = preview.offsetWidth; previewHeight = preview.offsetHeight;
    const start = destination(); currentX = start.x; currentY = start.y;
    preview.style.transform = 'translate3d(' + currentX + 'px, ' + currentY + 'px, 0)';
    preview.classList.add('is-visible');
    const video = card.querySelector('.card-preview');
    if (video?.dataset.src) {
      const request = ++previewRequest;
      previewTimer = setTimeout(() => {
        if (activeCard !== card || !floatingEnabled()) return;
        previewVideo.src = video.dataset.src;
        previewVideo.play().then(() => {
          if (request === previewRequest && activeCard === card) preview.classList.add('is-playing');
          else previewVideo.pause();
        }).catch(() => {});
      }, 150);
    }
  }
  const cardVisibility = new IntersectionObserver(entries => entries.forEach(entry => {
    if (!entry.isIntersecting) {
      entry.target.pause(); entry.target.closest('.project')?.classList.remove('preview-playing');
    }
  }), {threshold: .1});
  cards.forEach(card => {
    const video = card.querySelector('.card-preview');
    let request = 0;
    function pause() { request++; video?.pause(); card.classList.remove('preview-playing'); }
    function play() {
      if (!video || reduced.matches || dialog?.open || card.closest('.dragging')) return;
      const current = ++request;
      if (!video.hasAttribute('src')) video.src = video.dataset.src;
      video.play().then(() => {
        if (current === request && activeCard !== card && !document.hidden && !dialog?.open && !card.closest('.dragging') && (card.matches(':hover') || card === document.activeElement)) card.classList.add('preview-playing');
        else video.pause();
      }).catch(() => {});
    }
    card.addEventListener('pointerenter', event => {
      if (event.pointerType !== 'mouse') return;
      if (floatingEnabled()) showPreview(card, event); else play();
    });
    card.addEventListener('pointermove', event => showPreview(card, event));
    card.addEventListener('pointerleave', () => { pause(); if (activeCard === card) hidePreview(); });
    card.addEventListener('focus', () => { if (activeCard !== card) play(); });
    card.addEventListener('blur', pause);
    if (video) cardVisibility.observe(video);
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape') hidePreview(); });
  addEventListener('scroll', stopPreviews, {passive: true});

  // 02 / Horizontal galleries. Vertical wheel input always belongs to the page.
  const rows = [...document.querySelectorAll('.project-row')].map(section => ({
    section, track: section.querySelector('.track'), bar: section.querySelector('.progress i'),
    prev: section.querySelector('.gallery-prev'), next: section.querySelector('.gallery-next'),
  }));
  function horizontalIntent(event) {
    if (event.ctrlKey) return 0;
    const sideways = Math.abs(event.deltaX) > Math.abs(event.deltaY);
    if (!sideways && !event.shiftKey) return 0;
    const raw = sideways ? event.deltaX : event.deltaY;
    return raw * (event.deltaMode === 1 ? 16 : event.deltaMode === 2 ? innerHeight : 1);
  }
  function updateRow(row) {
    const max = Math.max(0, row.track.scrollWidth - row.track.clientWidth);
    row.bar.style.transform = 'scaleX(' + (max ? row.track.scrollLeft / max : 1) + ')';
    row.prev.disabled = row.track.scrollLeft < 3;
    row.next.disabled = row.track.scrollLeft >= max - 3;
  }
  function measure() { rows.forEach(updateRow); }
  for (const row of rows) {
    let rowFrame = 0;
    // Also enhance cached cards from previous theme versions.
    row.track.querySelectorAll('.project, img').forEach(element => { element.draggable = false; });
    // Prevent the browser's native link drag from cancelling gallery pointer capture.
    row.track.addEventListener('dragstart', event => event.preventDefault());
    row.track.addEventListener('scroll', () => {
      stopPreviews();
      if (!rowFrame) rowFrame = requestAnimationFrame(() => { rowFrame = 0; updateRow(row); });
    }, {passive: true});
    row.prev.addEventListener('click', () => row.track.scrollBy({left: -row.track.clientWidth * .8, behavior: behavior()}));
    row.next.addEventListener('click', () => row.track.scrollBy({left: row.track.clientWidth * .8, behavior: behavior()}));
    row.section.addEventListener('wheel', event => {
      stopPreviews();
      const delta = horizontalIntent(event);
      if (!delta || row.track.scrollWidth <= row.track.clientWidth) return;
      event.preventDefault(); row.track.scrollLeft += delta;
    }, {passive: false});
    row.track.addEventListener('keydown', event => {
      if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
      event.preventDefault();
      if (event.key === 'Home' || event.key === 'End') row.track.scrollTo({left: event.key === 'Home' ? 0 : row.track.scrollWidth, behavior: behavior()});
      else row.track.scrollBy({left: (event.key === 'ArrowLeft' ? -1 : 1) * row.track.clientWidth * .7, behavior: behavior()});
    });
    let drag = null, suppressClickUntil = 0;
    row.track.addEventListener('pointerdown', event => {
      if (event.pointerType !== 'mouse' || event.button !== 0) return;
      suppressClickUntil = 0;
      stopPreviews(); drag = {id: event.pointerId, x: event.clientX, left: row.track.scrollLeft, moved: false};
    });
    row.track.addEventListener('pointermove', event => {
      if (!drag || event.pointerId !== drag.id) return;
      if (!event.buttons) { endDrag(event); return; }
      const distance = event.clientX - drag.x;
      if (!drag.moved && Math.abs(distance) > 6) {
        drag.moved = true; row.track.setPointerCapture(event.pointerId);
        row.track.classList.add('dragging'); stopPreviews();
      }
      if (drag.moved) { event.preventDefault(); row.track.scrollLeft = drag.left - distance; }
    });
    function endDrag(event) {
      if (!drag || (event && event.pointerId !== drag.id)) return;
      const finished = drag;
      drag = null;
      if (finished.moved) suppressClickUntil = performance.now() + 350;
      row.track.classList.remove('dragging');
      if (row.track.hasPointerCapture(finished.id)) row.track.releasePointerCapture(finished.id);
    }
    row.track.addEventListener('pointerup', endDrag);
    row.track.addEventListener('pointercancel', endDrag);
    row.track.addEventListener('lostpointercapture', endDrag);
    addEventListener('blur', () => endDrag());
    row.track.addEventListener('pointerleave', event => { if (drag && !drag.moved) endDrag(event); });
    row.track.addEventListener('click', event => {
      if (event.detail > 0 && performance.now() < suppressClickUntil) { event.preventDefault(); event.stopImmediatePropagation(); }
    }, true);
  }
  addEventListener('resize', () => { stopPreviews(); measure(); });
  document.fonts.ready.then(measure); measure();

  // 03 / Hero motion follows only the mouse, with no scroll distortion.
  const hero = document.querySelector('.hero');
  let heroFrame = 0, targetX = 0, targetY = 0, heroX = 0, heroY = 0;
  const heroMotionEnabled = () => mouse.matches && !reduced.matches;
  function drawHero() {
    if (!hero) return;
    heroX += (targetX - heroX) * .08; heroY += (targetY - heroY) * .08;
    hero.style.setProperty('--hero-x', heroX.toFixed(2) + 'px'); hero.style.setProperty('--hero-y', heroY.toFixed(2) + 'px');
    heroFrame = Math.abs(targetX - heroX) + Math.abs(targetY - heroY) > .03 ? requestAnimationFrame(drawHero) : 0;
  }
  function resetHero() {
    targetX = 0; targetY = 0; hero?.classList.remove('glow-active');
    if (!heroFrame && hero && !reduced.matches) heroFrame = requestAnimationFrame(drawHero);
    if (reduced.matches && hero) {
      cancelAnimationFrame(heroFrame); heroFrame = 0; heroX = 0; heroY = 0;
      hero.style.removeProperty('--hero-x'); hero.style.removeProperty('--hero-y');
    }
  }
  function setHeroMotion() {
    hero?.classList.toggle('hero-motion', heroMotionEnabled() && settings.heroParallax === true);
    hero?.classList.toggle('hero-spotlight', heroMotionEnabled() && settings.heroGlow === true);
    resetHero();
  }
  hero?.addEventListener('pointermove', event => {
    if (event.pointerType !== 'mouse' || !heroMotionEnabled()) return;
    const rect = hero.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width, y = (event.clientY - rect.top) / rect.height;
    if (settings.heroParallax === true) {
      targetX = (x - .5) * 20; targetY = (y - .5) * 14;
      if (!heroFrame) heroFrame = requestAnimationFrame(drawHero);
    }
    if (settings.heroGlow === true) {
      hero.style.setProperty('--glow-x', x * 100 + '%'); hero.style.setProperty('--glow-y', y * 100 + '%'); hero.classList.add('glow-active');
    }
  });
  hero?.addEventListener('pointerleave', resetHero); setHeroMotion();

  // 04 / Decorative video pauses off screen, in hidden tabs and on reduced motion.
  const heroVideo = hero?.querySelector('.hero-video');
  const heroVideoButton = hero?.querySelector('.hero-video-toggle');
  let heroInView = true, heroUserPaused = false, heroPlayRequest = 0;
  function syncHeroVideo() {
    if (!heroVideo) return;
    const request = ++heroPlayRequest;
    const shouldPlay = heroInView && !heroUserPaused && !document.hidden && !reduced.matches && !dialog?.open;
    heroVideoButton.hidden = reduced.matches;
    if (!shouldPlay) {
      heroVideo.pause();
      if (reduced.matches) heroVideo.classList.remove('is-visible');
      heroVideoButton.textContent = 'Play background'; return;
    }
    if (!heroVideo.hasAttribute('src')) heroVideo.src = heroVideo.dataset.src;
    heroVideo.muted = true;
    heroVideo.play().then(() => {
      if (request !== heroPlayRequest) return;
      heroVideo.classList.add('is-visible'); heroVideoButton.textContent = 'Pause background';
    }).catch(() => { if (request === heroPlayRequest) heroVideoButton.textContent = 'Play background'; });
  }
  heroVideoButton?.addEventListener('click', () => { heroUserPaused = !heroVideo.paused; syncHeroVideo(); });
  if (heroVideo) {
    new IntersectionObserver(entries => { heroInView = entries[0].isIntersecting; syncHeroVideo(); }, {threshold: .05}).observe(hero);
    syncHeroVideo();
  }

  // 05 / 3D: self-hosted viewer loaded only after explicit activation.
  let modelModule = null;
  const models = new Map();
  async function loadModelViewer() {
    if (!modelModule) {
      modelModule = import(settings.modelModule).then(() => {
        const Viewer = customElements.get('model-viewer');
        if (!Viewer) throw new Error('3D viewer unavailable');
        Viewer.dracoDecoderLocation = settings.modelAssets + 'draco/';
        Viewer.ktx2TranscoderLocation = settings.modelAssets + 'basis/';
        Viewer.meshoptDecoderLocation = settings.modelAssets + 'meshopt_decoder.js';
        return Viewer;
      }).catch(error => { modelModule = null; throw error; });
    }
    return modelModule;
  }
  function syncModelMotion(stage) {
    const state = models.get(stage);
    if (!state?.ready) return;
    const canMove = !state.paused && !reduced.matches && !document.hidden && state.inView && (!dialog?.open || dialog.contains(stage));
    state.viewer.toggleAttribute('auto-rotate', canMove && state.rotate);
    if (canMove && state.animate) state.viewer.play(); else state.viewer.pause();
    const toggle = stage.querySelector('.model-motion');
    toggle.hidden = reduced.matches || !(state.rotate || state.animate);
    toggle.textContent = canMove ? 'Pause motion' : 'Play motion';
  }
  const modelVisibility = new IntersectionObserver(entries => entries.forEach(entry => {
    const state = models.get(entry.target);
    if (state) { state.inView = entry.isIntersecting; syncModelMotion(entry.target); }
  }), {threshold: .05});
  async function startModel(stage) {
    if (stage.classList.contains('is-loading') || stage.classList.contains('is-ready')) return;
    const start = stage.querySelector('.model-start'), status = stage.querySelector('.model-status');
    stage.classList.add('is-loading'); stage.classList.remove('has-error');
    start.disabled = true; status.textContent = 'Loading 3D model …';
    try {
      await loadModelViewer();
      if (!stage.isConnected) return;
      const viewer = document.createElement('model-viewer');
      viewer.setAttribute('alt', stage.dataset.modelAlt); viewer.setAttribute('tabindex', '0');
      viewer.setAttribute('camera-controls', ''); viewer.setAttribute('disable-zoom', '');
      viewer.setAttribute('touch-action', 'pan-y'); viewer.setAttribute('interaction-prompt', 'none');
      viewer.setAttribute('shadow-intensity', '.6'); viewer.setAttribute('environment-image', 'neutral');
      viewer.setAttribute('rotation-per-second', '12deg'); viewer.setAttribute('loading', 'eager');
      if (stage.dataset.modelPoster) viewer.setAttribute('poster', stage.dataset.modelPoster);
      const state = {viewer, ready: false, paused: false, inView: true, rotate: stage.dataset.modelRotate === 'true', animate: false};
      models.set(stage, state);
      viewer.addEventListener('load', () => {
        if (!stage.isConnected || models.get(stage) !== state) return;
        state.ready = true; state.animate = stage.dataset.modelAnimate === 'true' && viewer.availableAnimations.length > 0;
        const focusViewer = document.activeElement === start;
        stage.classList.remove('is-loading'); stage.classList.add('is-ready');
        start.disabled = false; status.textContent = 'Drag to rotate';
        modelVisibility.observe(stage); syncModelMotion(stage);
        if (focusViewer) viewer.focus({preventScroll: true});
      }, {once: true});
      viewer.addEventListener('error', () => { if (models.get(stage) === state) modelFailed(stage); }, {once: true});
      stage.querySelector('.model-canvas').append(viewer); viewer.setAttribute('src', stage.dataset.modelSrc);
    } catch (_) { modelFailed(stage); }
  }
  function modelFailed(stage) {
    const state = models.get(stage);
    state?.viewer.remove(); models.delete(stage); modelVisibility.unobserve(stage);
    stage.classList.remove('is-loading', 'is-ready'); stage.classList.add('has-error');
    stage.querySelector('.model-start').disabled = false;
    stage.querySelector('.model-motion').hidden = true;
    stage.querySelector('.model-status').textContent = 'The 3D model could not be loaded. Please try again.';
  }
  function disposeModels(root) {
    root.querySelectorAll('[data-model-stage]').forEach(stage => {
      const state = models.get(stage);
      state?.viewer.pause(); state?.viewer.removeAttribute('auto-rotate'); state?.viewer.remove();
      modelVisibility.unobserve(stage); models.delete(stage);
    });
  }
  document.addEventListener('click', event => {
    const start = event.target.closest('.model-start');
    if (start) startModel(start.closest('[data-model-stage]'));
    const toggle = event.target.closest('.model-motion');
    if (toggle) {
      const stage = toggle.closest('[data-model-stage]'), state = models.get(stage);
      if (state) { state.paused = !state.paused; syncModelMotion(stage); }
    }
  });

  // 06 / Fetch project details only when opened; ordinary links remain the fallback.
  let returnFocus = null;
  if (dialog && typeof dialog.showModal === 'function' && settings.projectEndpoint) {
    const host = dialog.querySelector('.modal-content');
    let projectRequest = null, projectSequence = 0;
    const projectCache = new Map();
    function showProject(html) {
      host.innerHTML = html;
      host.removeAttribute('aria-busy');
      const title = host.querySelector('.detail-title');
      if (title) { title.id = 'project-dialog-title'; dialog.setAttribute('aria-labelledby', title.id); }
      dialog.scrollTop = 0;
    }
    async function openProject(link) {
      const id = link.dataset.work;
      if (!/^\d+$/.test(id)) return;
      projectRequest?.abort();
      const sequence = ++projectSequence;
      returnFocus = link; stopPreviews();
      disposeModels(host);
      host.querySelectorAll('video').forEach(video => video.pause());
      const panel = document.createElement('div'); panel.className = 'project-loading';
      const status = document.createElement('p'); status.setAttribute('role', 'status'); status.textContent = 'Loading project …';
      const fallback = document.createElement('a'); fallback.className = 'button'; fallback.href = link.href; fallback.textContent = 'Open project page ↗';
      panel.append(status, fallback); host.replaceChildren(panel); host.setAttribute('aria-busy', 'true');
      dialog.removeAttribute('aria-labelledby');
      dialog.showModal(); dialog.scrollTop = 0; document.body.classList.add('modal-open');
      syncHeroVideo(); models.forEach((_, stage) => syncModelMotion(stage));
      if (projectCache.has(id)) { showProject(projectCache.get(id)); return; }
      const request = new AbortController(); projectRequest = request;
      const timeout = setTimeout(() => request.abort(), 15000);
      try {
        const response = await fetch(settings.projectEndpoint + id, {credentials: 'same-origin', signal: request.signal, headers: {'Accept': 'application/json'}});
        if (!response.ok) throw new Error('Project request failed');
        const data = await response.json();
        if (typeof data.html !== 'string' || !data.html.trim()) throw new Error('Project response missing');
        if (sequence !== projectSequence || !dialog.open) return;
        projectCache.set(id, data.html);
        if (projectCache.size > 8) projectCache.delete(projectCache.keys().next().value);
        showProject(data.html);
      } catch (_) {
        if (sequence !== projectSequence || !dialog.open) return;
        host.removeAttribute('aria-busy');
        status.setAttribute('role', 'alert'); status.textContent = 'The preview could not be loaded. You can open the project page instead.';
      } finally {
        clearTimeout(timeout);
        if (projectRequest === request) projectRequest = null;
      }
    }
    cards.forEach(link => link.addEventListener('click', event => {
      if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button > 0 || !/^\d+$/.test(link.dataset.work)) return;
      event.preventDefault(); openProject(link);
    }));
    dialog.querySelector('.close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', event => {
      if (event.target !== dialog) return;
      const rect = dialog.getBoundingClientRect();
      if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
    });
    dialog.addEventListener('close', () => {
      projectSequence++; projectRequest?.abort(); projectRequest = null;
      host.querySelectorAll('video').forEach(video => video.pause());
      disposeModels(host); host.replaceChildren();
      host.removeAttribute('aria-busy');
      document.body.classList.remove('modal-open'); returnFocus?.focus({preventScroll: true});
      syncHeroVideo(); models.forEach((_, stage) => syncModelMotion(stage));
    });
  }

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) stopPreviews();
    syncHeroVideo(); models.forEach((_, stage) => syncModelMotion(stage));
  });
  reduced.addEventListener('change', () => {
    stopPreviews(); setHeroMotion(); syncHeroVideo(); models.forEach((_, stage) => syncModelMotion(stage));
  });
  mouse.addEventListener('change', () => { stopPreviews(); setHeroMotion(); });
  wide.addEventListener('change', stopPreviews);
})();
