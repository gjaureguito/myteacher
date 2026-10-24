(() => {
  'use strict';
  const cards = [...document.querySelectorAll('[data-scene-catalog] [data-category]')];
  document.querySelectorAll('[data-goal]').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('[data-goal]').forEach(option => {
      const active = option === button;
      option.classList.toggle('is-active', active);
      option.setAttribute('aria-pressed', String(active));
    });
    let shown = 0;
    cards.forEach(card => {
      card.hidden = button.dataset.goal !== 'all' && card.dataset.category !== button.dataset.goal;
      if (!card.hidden) shown++;
    });
    document.querySelectorAll('[data-catalog-count]').forEach(counter => {
      counter.textContent = shown + (shown === 1 ? ' situación para explorar.' : ' situaciones para explorar.');
    });
  }));
  // Exercise shortcuts must not submit another activity while working in a scene.
  document.querySelectorAll('.scene').forEach(scene => scene.addEventListener('keydown', event => event.stopPropagation()));
})();
