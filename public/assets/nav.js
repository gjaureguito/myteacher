// Header de MyTeacher: altura y sombra al hacer scroll, y menú desplegable para visitantes en pantallas chicas.
(() => {
  'use strict';
  const header = document.querySelector('.mtn-header');
  if (!header) return;

  const root = document.documentElement;
  const setHeight = () => root.style.setProperty('--topbar-h', header.offsetHeight + 'px');
  const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 4);
  setHeight();
  onScroll();
  window.addEventListener('resize', setHeight);
  window.addEventListener('scroll', onScroll, { passive: true });

  const button = header.querySelector('.mtn-menu-btn');
  const nav = document.getElementById('mtn-nav');
  if (!button || !nav) return;

  const setOpen = open => {
    header.classList.toggle('is-menu-open', open);
    button.setAttribute('aria-expanded', String(open));
    button.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
  };
  button.addEventListener('click', () => setOpen(!header.classList.contains('is-menu-open')));
  nav.addEventListener('click', event => { if (event.target.closest('a')) setOpen(false); });
  document.addEventListener('pointerdown', event => { if (!header.contains(event.target)) setOpen(false); });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && header.classList.contains('is-menu-open')) { setOpen(false); button.focus(); }
  });
  window.matchMedia('(min-width: 901px)').addEventListener('change', () => setOpen(false));
})();
