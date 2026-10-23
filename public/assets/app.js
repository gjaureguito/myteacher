(() => {
  'use strict';
  const csrf = document.querySelector('meta[name="csrf"]')?.content || '';

  /* ---------- Audio con la voz del navegador (Web Speech API, gratis) ---------- */
  let voice = null;
  function pickVoice() {
    const vs = window.speechSynthesis ? speechSynthesis.getVoices() : [];
    voice = vs.find(v => /en[-_]US/i.test(v.lang) && /Google|Samantha|Aria|Jenny/i.test(v.name))
      || vs.find(v => /en[-_](US|GB)/i.test(v.lang)) || vs.find(v => /^en/i.test(v.lang)) || null;
  }
  if (window.speechSynthesis) { pickVoice(); speechSynthesis.onvoiceschanged = pickVoice; }

  function say(text, rate = 0.95) {
    if (!window.speechSynthesis) { alert('Tu navegador no permite reproducir audio. Probá con Chrome, Edge o Safari.'); return; }
    speechSynthesis.cancel();
    const u = new SpeechSynthesisUtterance(text);
    u.lang = voice?.lang || 'en-US';
    if (voice) u.voice = voice;
    u.rate = rate;
    speechSynthesis.speak(u);
  }

  document.addEventListener('click', e => {
    const b = e.target.closest('.speak');
    if (!b) return;
    e.stopPropagation();
    say(b.dataset.say, parseFloat(b.dataset.rate || '0.95'));
  });

  // Botón de audio en cada ejemplo de la teoría (lee solo la parte en inglés)
  document.querySelectorAll('.theory .ex').forEach(p => {
    const clone = p.cloneNode(true);
    clone.querySelectorAll('.es').forEach(n => n.remove());
    const text = clone.textContent.trim();
    if (!text) return;
    const btn = document.createElement('button');
    btn.className = 'ex-say speak';
    btn.dataset.say = text;
    btn.setAttribute('aria-label', 'Escuchar: ' + text);
    btn.textContent = '🔊';
    p.prepend(btn);
  });

  /* ---------- Ejercicio demo de la portada ---------- */
  const demo = document.getElementById('demo-opts');
  if (demo) {
    demo.addEventListener('click', e => {
      const b = e.target.closest('.opt');
      if (!b) return;
      const fb = document.getElementById('demo-fb');
      demo.querySelectorAll('.opt').forEach(o => o.classList.remove('is-ok', 'is-bad'));
      if (b.dataset.ok === '1') {
        b.classList.add('is-ok');
        const gap = document.getElementById('demo-gap');
        gap.textContent = 'goes';
        gap.classList.add('is-ok');
        fb.textContent = '¡Bien! Con he/she/it el verbo lleva -s: goes.';
        demo.querySelectorAll('.opt').forEach(o => o.disabled = true);
      } else {
        b.classList.add('is-bad');
        fb.textContent = 'Casi. Pensá en quién hace la acción: my brother = he.';
      }
    });
  }

  /* ---------- Pestañas ---------- */
  const tabs = document.querySelectorAll('.tabs [role="tab"]');
  function openTab(id) {
    if (![...tabs].some(t => t.getAttribute('aria-controls') === id)) return;
    tabs.forEach(t => {
      const on = t.getAttribute('aria-controls') === id;
      t.setAttribute('aria-selected', on);
      t.tabIndex = on ? 0 : -1;
      document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
    });
    history.replaceState(null, '', '#' + id);
  }
  tabs.forEach(t => t.tabIndex = t.getAttribute('aria-selected') === 'true' ? 0 : -1);
  tabs.forEach(t => t.addEventListener('click', () => openTab(t.getAttribute('aria-controls'))));
  tabs.forEach((t, index) => t.addEventListener('keydown', e => {
    let next;
    if (e.key === 'ArrowRight') next = (index + 1) % tabs.length;
    if (e.key === 'ArrowLeft') next = (index - 1 + tabs.length) % tabs.length;
    if (e.key === 'Home') next = 0;
    if (e.key === 'End') next = tabs.length - 1;
    if (next === undefined) return;
    e.preventDefault();
    openTab(tabs[next].getAttribute('aria-controls'));
    tabs[next].focus();
  }));
  document.querySelectorAll('[data-goto]').forEach(b => b.addEventListener('click', () => {
    openTab(b.dataset.goto); window.scrollTo({ top: document.querySelector('.tabs').offsetTop - 80, behavior: 'smooth' });
  }));
  if (tabs.length && location.hash && document.getElementById(location.hash.slice(1))) openTab(location.hash.slice(1));

  /* ---------- Tarjetas de vocabulario ---------- */
  document.querySelectorAll('.card-flip').forEach(c => {
    const flip = () => c.classList.toggle('is-flipped');
    c.addEventListener('click', flip);
    c.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); flip(); } });
  });

  /* ---------- Ejercicios: corrección en el servidor ---------- */
  const progress = document.querySelector('[data-progress]');

  async function send(li, answer) {
    const res = await fetch('/api/answer.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify({ id: +li.dataset.id, answer })
    });
    const data = await res.json().catch(() => ({ error: 'Error de conexión' }));
    if (!res.ok) throw new Error(data.error || 'Error');
    return data;
  }

  function showFeedback(li, data) {
    const fb = li.querySelector('.feedback');
    fb.className = 'feedback ' + (data.correct ? 'is-ok' : 'is-bad');
    let html = '<strong>' + (data.correct ? '¡Correcto!' : (data.reveal ? 'No es correcto.' : 'No es correcto. Probá otra vez.')) + '</strong>';
    if (data.reveal && data.answer && !data.correct) html += 'Respuesta correcta: <em>' + esc(data.answer) + '</em><br>';
    if (data.explanation) html += esc(data.explanation);
    fb.innerHTML = html;
    if (data.correct && !li.classList.contains('is-solved')) {
      li.classList.add('is-solved');
      if (progress) {
        const [s, t] = progress.textContent.split('/').map(Number);
        progress.textContent = (s + 1) + '/' + t;
      }
    }
    if (data.say && data.reveal) say(data.say);
  }

  function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

  async function handle(li, answer, onResult) {
    try {
      const data = await send(li, answer);
      showFeedback(li, data);
      onResult && onResult(data);
    } catch (err) {
      const fb = li.querySelector('.feedback');
      fb.className = 'feedback is-bad';
      fb.textContent = err.message;
    }
  }

  document.querySelectorAll('.ex').forEach(li => {
    const type = li.dataset.type;

    if (type === 'mcq' || type === 'listen') {
      li.querySelectorAll('.opt').forEach(o => o.addEventListener('click', () => {
        li.querySelectorAll('.opt').forEach(x => x.classList.remove('is-bad'));
        handle(li, +o.dataset.value, data => {
          if (data.correct) o.classList.add('is-ok'); else o.classList.add('is-bad');
          if (data.reveal) {
            li.querySelectorAll('.opt').forEach(x => {
              if (+x.dataset.value === data.index) x.classList.add('is-ok');
              x.disabled = true;
            });
          }
        });
      }));
    }

    if (type === 'fill') {
      const input = li.querySelector('.gap');
      const go = () => input.value.trim() && handle(li, input.value);
      li.querySelector('.check').addEventListener('click', go);
      input.addEventListener('keydown', e => { if (e.key === 'Enter') go(); });
    }

    if (type === 'dictation') {
      const input = li.querySelector('.dict');
      const go = () => input.value.trim() && handle(li, input.value);
      li.querySelector('.check').addEventListener('click', go);
      input.addEventListener('keydown', e => { if (e.key === 'Enter') go(); });
    }

    if (type === 'order') {
      const built = li.querySelector('.built');
      const pool = li.querySelector('.chips');
      li.addEventListener('click', e => {
        const chip = e.target.closest('.chip');
        if (!chip) return;
        (chip.parentElement === pool ? built : pool).appendChild(chip);
      });
      li.querySelector('.reset').addEventListener('click', () => {
        [...built.children].forEach(c => pool.appendChild(c));
      });
      li.querySelector('.check').addEventListener('click', () => {
        const sentence = [...built.children].map(c => c.textContent).join(' ');
        if (sentence) handle(li, sentence);
      });
    }
  });
})();
