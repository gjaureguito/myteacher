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

  function say(text, rate = 0.95, btn = null) {
    if (!window.speechSynthesis) { (window.ipToast || alert)('Tu navegador no permite reproducir audio. Probá con Chrome, Edge o Safari.'); return; }
    speechSynthesis.cancel();
    document.querySelectorAll('.is-speaking').forEach(b => b.classList.remove('is-speaking'));
    const u = new SpeechSynthesisUtterance(text);
    u.lang = voice?.lang || 'en-US';
    if (voice) u.voice = voice;
    u.rate = Math.min(2, Math.max(0.4, rate * (parseFloat(localStorage.getItem('ip_rate') || '1') || 1)));
    if (btn) {
      u.onstart = () => btn.classList.add('is-speaking');
      u.onend = u.onerror = () => btn.classList.remove('is-speaking');
    }
    speechSynthesis.speak(u);
  }
  window.ipSay = say;

  document.addEventListener('click', e => {
    const b = e.target.closest('.speak');
    if (!b) return;
    e.stopPropagation();
    say(b.dataset.say, parseFloat(b.dataset.rate || '0.95'), b);
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
        document.dispatchEvent(new CustomEvent('ip:answer', { detail: { li: b, correct: true, demo: true } }));
        say('My brother goes to work by bus.');
        demo.querySelectorAll('.opt').forEach(o => o.disabled = true);
      } else {
        b.classList.add('is-bad');
        fb.textContent = 'Casi. Pensá en quién hace la acción: my brother = he.';
        document.dispatchEvent(new CustomEvent('ip:answer', { detail: { li: b, correct: false, demo: true } }));
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
  let lastAnswerAt = Date.now();

  async function send(li, payload) {
    const t0 = li._t0 || lastAnswerAt;
    const res = await fetch('/api/answer.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify(Object.assign({ id: +li.dataset.id, ms: Date.now() - t0 }, payload))
    });
    const data = await res.json().catch(() => ({ error: 'Error de conexión' }));
    if (!res.ok) throw new Error(data.error || 'Error');
    lastAnswerAt = Date.now(); li._t0 = null;
    return data;
  }

  function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

  function renderDetail(li, data) {
    const d = data.detail || {};
    let html = '';
    if (li.dataset.type === 'write' && d.criteria) {
      li.querySelectorAll('.criteria li').forEach((c, i) => {
        c.classList.toggle('ok', !!(d.criteria[i] && d.criteria[i].ok));
        c.classList.toggle('bad', !!(d.criteria[i] && !d.criteria[i].ok));
      });
      if (d.criteria[0]) li.querySelector('.criteria li[data-c="0"]').textContent = d.criteria[0].label;
      if (d.model) html += '<div class="model-answer"><span>Texto modelo</span><p lang="en">' + esc(d.model) + '</p></div>';
    }
    if (li.dataset.type === 'speak' && d.score != null) {
      const cls = d.score >= 80 ? 'good' : d.score >= 55 ? 'mid' : 'low';
      html += '<div class="speak-score ' + cls + '"><span class="meter"><i style="width:' + d.score + '%"></i></span><b>' + d.score + '%</b> · Entendí: “' + esc(d.heard) + '”'
        + (d.missing && d.missing.length && cls !== 'good' ? '<br>Revisá: <em>' + d.missing.map(esc).join(', ') + '</em>' : '') + '</div>';
    }
    if (li.dataset.type === 'dictation' && d.words && d.words.length && !data.correct) {
      html += '<p class="dict-diff" lang="en">' + d.words.map(t => t.t === 'ok' ? '<span class="dw ok">' + esc(t.w) + '</span>'
        : t.t === 'bad' ? '<span class="dw bad">' + esc(t.w) + '</span>' : '<span class="dw miss" title="Falta una palabra">＿＿</span>').join(' ') + '</p>';
      html += '<p class="fine">Acertaste ' + d.right + ' de ' + d.total + ' palabras. Lo verde ya está bien: corregí solo lo rojo y completá los huecos.</p>';
    }
    if (li.dataset.type === 'match' && d.marks) {
      const pairs = li._pairs || [];
      d.marks.forEach((ok, i) => {
        const l = li.querySelector('.match-item[data-side="l"][data-i="' + i + '"]');
        if (l) { l.classList.toggle('m-ok', ok); l.classList.toggle('m-bad', !ok); }
        const r = pairs[i] != null ? li.querySelector('.match-item[data-side="r"][data-pair="' + i + '"]') : null;
        if (r) { r.classList.toggle('m-ok', ok); r.classList.toggle('m-bad', !ok); }
      });
      if (!data.correct) html += '<p class="fine">Acertaste ' + d.right + ' de ' + d.total + ' pares.</p>';
    }
    return html;
  }

  function showFeedback(li, data) {
    const fb = li.querySelector('.feedback');
    fb.className = 'feedback ' + (data.correct ? 'is-ok' : (data.solution ? 'is-info' : 'is-bad'));
    let head;
    if (data.solution) head = '💡 Solución';
    else if (data.correct) head = data.first_try ? '¡Correcto a la primera! 🎯' : '¡Correcto!';
    else if (li.dataset.type === 'write') head = '✍️ Casi: revisá los criterios marcados en rojo y compará con el modelo.';
    else if (li.dataset.type === 'speak') head = 'Probá de nuevo: escuchá el modelo y repetí más despacio.';
    else if (li.dataset.type === 'dictation' && data.detail && data.detail.right > 0) head = data.detail.right === data.detail.total ? 'Casi: sobra alguna palabra.' : 'Casi: te falta poco.';
    else head = data.reveal ? 'No es correcto.' : 'Todavía no. Probá otra vez.';
    let html = '<strong>' + head + '</strong>';
    html += renderDetail(li, data);
    if (data.hint) html += '<p class="hint">💭 Pista: ' + esc(data.hint) + '</p>';
    if (data.reveal && data.answer && !data.correct && li.dataset.type !== 'write') html += '<p>Respuesta correcta: <em lang="en">' + esc(data.answer) + '</em></p>';
    if (data.explanation) html += '<p class="why">' + esc(data.explanation) + '</p>';
    fb.innerHTML = html;

    const firstSolve = data.correct && !li.classList.contains('is-solved');
    let solved = 0, total = 0;
    if (progress) [solved, total] = progress.textContent.split('/').map(Number);
    if (data.correct) lockDone(li);
    if (firstSolve) {
      li.classList.add('is-solved');
      solved += 1;
      if (progress) progress.textContent = solved + '/' + total;
      const chip = document.querySelector('[data-skill-chip="' + li.dataset.skill + '"] [data-s]');
      if (chip) chip.textContent = +chip.textContent + 1;
    }
    if (!data.solution) document.dispatchEvent(new CustomEvent('ip:answer', { detail: { li, correct: !!data.correct, firstSolve, firstTry: !!data.first_try, skill: li.dataset.skill, solved, total, reveal: !!data.reveal } }));
    if (data.say && (data.reveal || data.solution) && li.dataset.type !== 'speak') say(data.say);
  }

  function markOptions(li, data, chosen) {
    if (chosen) chosen.classList.add(data.correct ? 'is-ok' : 'is-bad');
    if (data.reveal || data.solution) {
      li.querySelectorAll('.opt').forEach(x => {
        if (+x.dataset.value === data.index) x.classList.add('is-ok');
        x.disabled = true;
      });
    }
  }

  async function handle(li, answer, onResult) {
    try {
      const data = await send(li, { answer });
      showFeedback(li, data);
      onResult && onResult(data);
    } catch (err) {
      const fb = li.querySelector('.feedback');
      fb.className = 'feedback is-bad';
      fb.textContent = err.message;
    }
  }

  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;

  document.querySelectorAll('.ex').forEach(li => {
    const type = li.dataset.type;
    const solution = li.querySelector('.exercise-solution');
    if (solution) {
      let loading = false;
      let loaded = false;
      solution.addEventListener('toggle', async () => {
        if (!solution.open || loading || loaded) return;
        loading = true;
        const content = solution.querySelector('.solution-content');
        content.textContent = 'Consultando la solución…';
        try {
          const res = await fetch('/api/solution.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ id: +li.dataset.id })
          });
          const data = await res.json().catch(() => ({ error: 'No pudimos leer la solución.' }));
          if (!res.ok) throw new Error(data.error || 'No pudimos consultar la solución.');
          content.replaceChildren();
          const section = (title, text, className = '') => {
            const heading = document.createElement('h4');
            heading.textContent = title;
            const paragraph = document.createElement('p');
            paragraph.textContent = text;
            if (className) paragraph.className = className;
            content.append(heading, paragraph);
          };
          section('Respuesta', data.answer, 'solution-answer');
          if (data.alternatives?.length) section('También se acepta', data.alternatives.join(' / '));
          section('Por qué es correcta', data.explanation || 'Repasá la explicación de esta unidad para analizar la respuesta.');
          if (data.transcript) section('Transcripción del audio', data.transcript);
          section('Conexión con la teoría: ' + data.theory_title, data.theory_summary);
          loaded = true;
        } catch (err) {
          content.textContent = err.message + ' Cerrá y volvé a abrir la sección para reintentar.';
        } finally {
          loading = false;
        }
      });
    }
    const mark = () => { if (!li._t0) li._t0 = Date.now(); };
    li.addEventListener('pointerdown', mark); li.addEventListener('focusin', mark);

    /* Ver solución: siempre disponible */
    li.querySelector('.show-solution')?.addEventListener('click', async () => {
      try {
        const data = await send(li, { reveal: true });
        showFeedback(li, data);
        if (data.index != null) markOptions(li, data, null);
        if (type === 'fill' && data.answer) { const g = li.querySelector('.gap'); if (g && !g.value) g.placeholder = data.answer; }
        if (type === 'match' && data.pairs) solveMatch(li, data.pairs);
        document.dispatchEvent(new CustomEvent('ip:solution', { detail: { li } }));
      } catch (err) { (window.ipToast || alert)(err.message); }
    });

    if (['mcq', 'listen', 'reading', 'image'].includes(type)) {
      li.querySelectorAll('.opt').forEach(o => o.addEventListener('click', () => {
        li.querySelectorAll('.opt').forEach(x => x.classList.remove('is-bad'));
        handle(li, +o.dataset.value, data => markOptions(li, data, o));
      }));
    }

    if (type === 'fill' || type === 'dictation') {
      const input = li.querySelector(type === 'fill' ? '.gap' : '.dict');
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

    if (type === 'match') {
      li._pairs = [];
      let selL = null;
      const colors = ['#3b7be0', '#c2405a', '#14915e', '#8150d8', '#e08a1e', '#13897e', '#a42d72', '#5b5fe0'];
      const paint = () => {
        li.querySelectorAll('.match-item').forEach(b => { b.style.removeProperty('--mc'); b.classList.remove('paired', 'sel', 'm-ok', 'm-bad'); b.removeAttribute('data-pair'); });
        li._pairs.forEach((r, i) => {
          if (r == null) return;
          const l = li.querySelector('.match-item[data-side="l"][data-i="' + i + '"]');
          const rb = [...li.querySelectorAll('.match-item[data-side="r"]')].find(b => b.dataset.text === r && !b.dataset.pair);
          [l, rb].forEach(b => { if (b) { b.classList.add('paired'); b.style.setProperty('--mc', colors[i % colors.length]); } });
          if (rb) rb.dataset.pair = i;
        });
        if (selL != null) li.querySelector('.match-item[data-side="l"][data-i="' + selL + '"]').classList.add('sel');
      };
      li._paint = paint;
      li.querySelector('.match').addEventListener('click', e => {
        const b = e.target.closest('.match-item'); if (!b) return;
        if (b.dataset.side === 'l') {
          const i = +b.dataset.i;
          if (li._pairs[i] != null) { li._pairs[i] = null; selL = null; } else selL = selL === i ? null : i;
        } else {
          if (b.dataset.pair != null) { li._pairs[+b.dataset.pair] = null; }
          else if (selL != null) { li._pairs[selL] = b.dataset.text; selL = null; }
        }
        paint();
      });
      li.querySelector('.reset').addEventListener('click', () => { li._pairs = []; selL = null; paint(); });
      li.querySelector('.check').addEventListener('click', () => {
        const n = +li.querySelector('.match').dataset.n;
        const ans = Array.from({ length: n }, (_, i) => li._pairs[i] ?? '');
        if (ans.some(a => !a)) { (window.ipToast || alert)('Uní todos los pares antes de comprobar.'); return; }
        handle(li, ans);
      });
    }

    if (type === 'write') {
      const box = li.querySelector('.write-box');
      const wc = li.querySelector('.wc b');
      const count = () => { const n = box.value.trim() ? box.value.trim().split(/\s+/).length : 0; wc.textContent = n; wc.parentElement.classList.toggle('ok', n >= parseInt(wc.parentElement.textContent.split('/')[1], 10)); };
      box.addEventListener('input', count);
      li.querySelector('.check').addEventListener('click', () => { if (box.value.trim()) handle(li, box.value); });
    }

    if (type === 'speak') {
      const rec = li.querySelector('.record');
      const self = li.querySelector('.self-check');
      const fb = li.querySelector('.feedback');
      if (!SR && !window.ipVoice?.supported) self.hidden = false;
      self.addEventListener('click', () => handle(li, { self: true }));
      rec.addEventListener('click', () => {
        if (rec.classList.contains('is-busy') || !window.ipRecognize) return;
        if (rec.classList.contains('is-listening')) { window.ipVoice?.stop(); return; }
        rec.classList.add('is-listening'); rec.textContent = '🎙️ Preparando…';
        window.ipPlay && ipPlay('mic');
        window.ipRecognize(li.querySelector('.say-target')?.textContent || '', {
          onStart: () => { rec.textContent = '🔴 Te escucho… (tocá para terminar)'; },
          onSearching: () => { rec.textContent = '🔎 Buscando micrófono…'; },
          onLevel: v => rec.style.setProperty('--mic-level', v.toFixed(2)),
          onProcessing: () => { rec.classList.remove('is-listening'); rec.classList.add('is-busy'); rec.textContent = '⏳ Analizando…'; },
          onResult: alts => handle(li, { transcript: alts[0] }),
          onFail: msg => { fb.className = 'feedback is-bad'; fb.textContent = msg; self.hidden = false; },
          onEnd: () => { rec.classList.remove('is-listening', 'is-busy'); rec.style.removeProperty('--mic-level'); rec.textContent = '🎙️ Grabar de nuevo'; },
        });
      });
    }
  });

  /* ---------- Ejercicios terminados: quedan resueltos hasta que el alumno los reinicie ---------- */
  function lockDone(li) {
    li.classList.add('is-done');
    li.querySelector('.done-badge')?.removeAttribute('hidden');
    li.querySelector('.redo')?.removeAttribute('hidden');
    li.querySelector('.show-solution')?.setAttribute('hidden', '');
    li.querySelectorAll('.opt, .chip, .match-item').forEach(b => b.disabled = true);
    li.querySelectorAll('.gap, .dict, .write-box').forEach(i => i.readOnly = true);
    li.querySelectorAll('.check, .reset, .record, .self-check').forEach(b => b.hidden = true);
  }
  function renderDone(li, d) {
    const type = li.dataset.type;
    const mine = typeof d.mine === 'string' ? d.mine : (d.mine && d.mine.transcript) || '';
    if (d.index != null) li.querySelectorAll('.opt').forEach(o => { if (+o.dataset.value === d.index) o.classList.add('is-ok'); });
    if (type === 'fill' || type === 'dictation') { const i = li.querySelector(type === 'fill' ? '.gap' : '.dict'); i.value = mine || d.answer; i.classList.add('is-ok'); }
    if (type === 'order') {
      const built = li.querySelector('.built'), pool = li.querySelector('.chips');
      (mine || d.answer).split(/\s+/).forEach(w => { const c = [...pool.children].find(x => x.textContent === w); if (c) built.appendChild(c); });
    }
    if (type === 'match' && d.pairs) solveMatch(li, d.pairs);
    if (type === 'write') { const box = li.querySelector('.write-box'); box.value = mine; li.querySelectorAll('.criteria li').forEach(c => c.classList.add('ok')); const wc = li.querySelector('.wc b'); if (wc) wc.textContent = mine ? mine.trim().split(/\s+/).length : 0; }
    let html = '<strong>✅ Completado</strong>';
    if (type === 'speak' && mine) html += '<p>Dijiste: “' + esc(mine) + '”</p>';
    if (type === 'write' && d.answer) html += '<div class="model-answer"><span>Texto modelo</span><p lang="en">' + esc(d.answer) + '</p></div>';
    if (d.explanation) html += '<p class="why">' + esc(d.explanation) + '</p>';
    const fb = li.querySelector('.feedback'); fb.className = 'feedback is-ok is-done-fb'; fb.innerHTML = html;
    lockDone(li);
  }
  function countDone() {
    const el = document.querySelector('[data-done-count]'); if (!el) return;
    const all = document.querySelectorAll('.exercises .ex'), done = document.querySelectorAll('.exercises .ex.is-solved');
    el.textContent = done.length + ' de ' + all.length + ' ejercicios completados';
  }
  async function resetReq(payload) {
    const res = await fetch('/api/reset.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf }, body: JSON.stringify(payload) });
    if (!res.ok) throw new Error('No se pudo reiniciar. Recargá la página e intentá de nuevo.');
    try { sessionStorage.setItem('ip_scroll', String(scrollY)); } catch {}
    location.reload();
  }
  function confirmBox(title, text, okLabel, onOk) {
    const back = document.createElement('div'); back.className = 'modal-back';
    back.innerHTML = '<div class="modal" role="dialog" aria-modal="true"><h2>' + esc(title) + '</h2><p class="muted">' + esc(text) + '</p><div class="modal-actions"><button type="button" class="btn" data-ok>' + esc(okLabel) + '</button><button type="button" class="btn btn-ghost" data-no>Cancelar</button></div></div>';
    document.body.append(back);
    back.querySelector('[data-ok]').focus();
    back.addEventListener('click', e => {
      if (e.target.closest('[data-ok]')) { back.remove(); onOk(); }
      else if (e.target === back || e.target.closest('[data-no]')) back.remove();
    });
  }
  document.querySelectorAll('.ex[data-done]').forEach(li => { try { renderDone(li, JSON.parse(li.dataset.done)); } catch {} });
  countDone();
  document.addEventListener('ip:answer', () => setTimeout(countDone, 0));
  document.addEventListener('click', e => {
    const redo = e.target.closest('.ex .redo');
    if (redo) {
      const li = redo.closest('.ex');
      confirmBox('¿Repetir este ejercicio?', 'Se borra su resultado y vuelve a quedar sin resolver. Tus estadísticas conservan el historial.', 'Repetir', () => resetReq({ exercise_id: +li.dataset.id }).catch(err => (window.ipToast || alert)(err.message)));
    }
    const ru = e.target.closest('.reset-unit');
    if (ru) confirmBox('¿Reiniciar la práctica de esta unidad?', 'Todos los ejercicios de la unidad vuelven a quedar sin resolver para que los hagas de nuevo. Tus estadísticas conservan el historial de intentos.', 'Sí, reiniciar', () => resetReq({ unit_id: +ru.dataset.unit }).catch(err => (window.ipToast || alert)(err.message)));
  });
  try { const y = sessionStorage.getItem('ip_scroll'); if (y !== null) { sessionStorage.removeItem('ip_scroll'); requestAnimationFrame(() => scrollTo(0, +y)); } } catch {}

  function solveMatch(li, pairs) {
    const col = li.querySelector('.match-right');
    pairs.forEach(t => { const b = [...col.children].find(x => x.dataset.text === t && !x._moved); if (b) { b._moved = true; col.appendChild(b); } });
    li._pairs = pairs.slice();
    li._paint && li._paint();
    li.querySelectorAll('.match-item').forEach(b => b.classList.add('m-ok'));
  }
})();
