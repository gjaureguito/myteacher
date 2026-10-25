(() => {
  'use strict';
  const root = document.documentElement;
  const preference = matchMedia('(prefers-reduced-motion: reduce)');
  let optedOut = false;
  try { optedOut = localStorage.getItem('mt_motion') === 'off'; } catch {}
  const running = new Set();
  let observer;
  let toggle;
  const enabled = () => !preference.matches && !optedOut;
  const sync = () => {
    root.dataset.motion = enabled() ? 'on' : 'off';
    if (toggle) {
      toggle.textContent = enabled() ? 'Animaciones: activadas' : 'Animaciones: desactivadas';
      toggle.setAttribute('aria-pressed', String(enabled()));
      toggle.disabled = preference.matches;
      toggle.title = preference.matches ? 'Tu dispositivo solicita reducir el movimiento.' : 'Activar o desactivar las animaciones decorativas.';
    }
    if (!enabled()) {
      running.forEach(animation => animation.cancel());
      running.clear();
    }
  };
  const footer = document.querySelector('.footer-in');
  if (footer) {
    toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'mt-motion-toggle';
    toggle.addEventListener('click', () => {
      optedOut = !optedOut;
      try { localStorage.setItem('mt_motion', optedOut ? 'off' : 'on'); } catch {}
      sync();
    });
    footer.append(toggle);
  }
  sync();
  preference.addEventListener('change', sync);
  function animate(element, frames, options = {}) {
    if (!enabled() || typeof element.animate !== 'function') return;
    const animation = element.animate(frames, {duration:650, easing:'cubic-bezier(.2,.75,.25,1)', ...options});
    running.add(animation);
    animation.finished.then(() => running.delete(animation), () => running.delete(animation));
  }
  // The final layout is the baseline; failed/disabled JS never conceals content.
  if ('IntersectionObserver' in window) {
    observer = new IntersectionObserver(entries => entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const element = entry.target;
      observer.unobserve(element);
      if (!element.hasAttribute('data-reveal')) {
        animate(element, [{opacity:.15, transform:'translateY(22px)'},{opacity:1, transform:'translateY(0)'}]);
      }
      if (element.matches('.mt-study-image')) {
        const label = element.querySelector('.mt-study-label');
        if (label) animate(label, [{transform:'translateY(16px) rotate(-8deg)',opacity:0},{transform:'translateY(0) rotate(-4deg)',opacity:1}], {delay:180,duration:800});
      }
    }), {threshold:.12});
    document.querySelectorAll('.mt-hero-copy, .mt-hero-visual, .mt-feature, .mt-study-image, .mt-study-copy, .mt-method-steps li, .mt-study-dashboard').forEach(element => observer.observe(element));
  }
  // Decorative waves settle after 2.4 seconds; they never suggest audio is playing.
  const wave = document.querySelector('.mt-voice-decoration');
  if (wave) wave.querySelectorAll('i').forEach((bar, i) => animate(bar, [{transform:'scaleY(.45)'},{transform:'scaleY(1)'},{transform:'scaleY(.45)'}], {duration:600,iterations:4,delay:i*25}));
  const headline = document.querySelector('.mt-hero h1 path');
  if (headline && typeof headline.getTotalLength === 'function') {
    const length = headline.getTotalLength();
    animate(headline,[{strokeDasharray:String(length),strokeDashoffset:length},{strokeDasharray:String(length),strokeDashoffset:0}],{duration:1100,delay:250});
  }
  document.addEventListener('visibilitychange', () => {
    running.forEach(animation => document.hidden ? animation.pause() : (enabled() && animation.play()));
  });
})();
