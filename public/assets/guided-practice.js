(() => {
  'use strict';
  const panel = document.getElementById('t-practice');
  if (!panel) return;
  const list = panel.querySelector('.exercises');
  if (!list) return;
  const items = [...list.querySelectorAll('.ex')];
  if (!items.length) return;
  const bar = document.createElement('nav');
  bar.className = 'guided-practice';
  bar.setAttribute('aria-label', 'Navegación de práctica');
  bar.innerHTML = '<div class="guided-top"><strong>🎯 Práctica guiada</strong><span class="guided-status" role="status" aria-live="polite"></span></div><div class="guided-track" role="progressbar" aria-label="Ejercicios resueltos" aria-valuemin="0" aria-valuemax="' + items.length + '" aria-valuenow="0"><span></span></div><div class="guided-actions"><button type="button" data-guide="prev">← Anterior</button><button type="button" data-guide="next">Siguiente →</button><button type="button" data-guide="pending" aria-pressed="false">Solo pendientes</button><button type="button" data-guide="review" aria-pressed="false">Repasar errores</button></div><p class="guided-tip">Atajos cuando no escribís: 1–4 para elegir, L para escuchar y N para el siguiente. Las correcciones siguen siendo del servidor.</p>';
  list.before(bar);
  const btn = name => bar.querySelector('[data-guide="' + name + '"]');
  let selected = 0;
  let mode = 'all';
  const missed = new Set();
  function visible(item) {
    if (mode === 'pending') return !item.classList.contains('is-solved');
    if (mode === 'review') return missed.has(item.dataset.id);
    return true;
  }
  function update() {
    const done = items.filter(x => x.classList.contains('is-solved')).length;
    const available = items.filter(visible);
    if (available.length && !visible(items[selected])) selected = items.indexOf(available[0]);
    items.forEach((el, i) => {
      el.hidden = !visible(el);
      el.classList.toggle('guided-current', i === selected && !el.hidden);
    });
    // Keep reading passages and section labels visible so questions retain their context.
    const progress = bar.querySelector('.guided-track');
    progress.setAttribute('aria-valuenow', String(done));
    progress.querySelector('span').style.width = (100 * done / items.length) + '%';
    bar.querySelector('.guided-status').textContent = done + '/' + items.length + ' resueltos · ' + available.length + ' visibles';
    ['pending', 'review'].forEach(key => btn(key).setAttribute('aria-pressed', String(mode === key)));
    btn('prev').disabled = available.length < 2;
    btn('next').disabled = available.length < 2;
    if (mode === 'review' && !available.length) bar.querySelector('.guided-status').textContent += ' · Sin errores registrados en esta sesión';
  }
  function go(step) {
    const available = items.filter(visible);
    if (!available.length) return;
    const idx = Math.max(0, available.indexOf(items[selected]));
    const target = available[(idx + step + available.length) % available.length];
    selected = items.indexOf(target);
    update();
    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    const action = target.querySelector('input:not([disabled]),textarea:not([disabled]),.opt:not([disabled]),.chip:not([disabled]),.check:not([disabled])');
    if (action) action.focus({ preventScroll: true });
  }
  btn('prev').addEventListener('click', () => go(-1));
  btn('next').addEventListener('click', () => go(1));
  ['pending', 'review'].forEach(key => btn(key).addEventListener('click', () => { mode = mode === key ? 'all' : key; update(); }));
  items.forEach((el, index) => el.addEventListener('focusin', () => { if (!el.hidden) { selected = index; update(); } }));
  document.addEventListener('ip:answer', e => {
    const el = e.detail && e.detail.li;
    if (!el || !items.includes(el) || e.detail.demo) return;
    if (!e.detail.correct) missed.add(el.dataset.id);
    requestAnimationFrame(update);
  });
  document.addEventListener('ip:solution', e => { if (items.includes(e.detail?.li)) { missed.add(e.detail.li.dataset.id); update(); } });
  document.addEventListener('keydown', e => {
    if (panel.hidden || e.defaultPrevented || e.altKey || e.ctrlKey || e.metaKey) return;
    const tag = document.activeElement?.tagName;
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(tag) || document.activeElement?.isContentEditable) return;
    if (e.key.toLowerCase() === 'n') { e.preventDefault(); go(1); return; }
    const current = items[selected];
    if (!current || current.hidden) return;
    if (/^[1-4]$/.test(e.key)) {
      const opt = current.querySelectorAll('.opt:not([disabled])')[Number(e.key) - 1];
      if (opt) { e.preventDefault(); opt.click(); }
    } else if (e.key.toLowerCase() === 'l') {
      const speak = current.querySelector('.speak');
      if (speak) { e.preventDefault(); speak.click(); }
    }
  });
  const style = document.createElement('style');
  style.textContent = '.guided-practice{background:var(--surface,#f6f8fc);border:1px solid #c8d5e8;border-radius:16px;padding:16px;margin:16px 0 24px}.guided-top{display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px}.guided-status{font-size:.9rem}.guided-track{height:9px;background:#dfe7f0;border-radius:99px;overflow:hidden;margin:12px 0}.guided-track span{display:block;background:#16805d;height:100%;transition:width .2s}.guided-actions{display:flex;flex-wrap:wrap;gap:8px}.guided-actions button{border:1px solid #879cb9;border-radius:8px;padding:9px 12px;background:transparent;color:inherit;cursor:pointer}.guided-actions button[aria-pressed="true"]{background:#d7efe5;color:#135440}.guided-actions button:disabled{opacity:.45;cursor:default}.guided-tip{font-size:.83rem;margin:10px 0 0}.ex.guided-current{outline:2px solid #4586dc;outline-offset:4px}.ex[hidden]{display:none!important}@media(prefers-reduced-motion:reduce){.guided-track span{transition:none}}';
  document.head.append(style);
  update();
})();