(() => {
 'use strict';
 const $ = id => document.getElementById(id);
 const el = (tag, cls, text) => { const e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; };
 const chat = $('pd-chat'), form = $('pd-form'), input = $('pd-input'), mic = $('pd-mic'), send = $('pd-send'), status = $('pd-status');
 const voice = $('pd-voice'), auto = $('pd-auto'), showTr = $('pd-translate'), levelSel = $('pc-level'), feedbackBox = $('pc-feedback');
 if (!chat) return;
 let catalog = null, level = 'A1', cls = null, busy = false, listening = false, session = 0;
 const csrf = () => document.querySelector('meta[name="csrf"]')?.content || '';
 const say = t => { status.textContent = t || ''; };
 const scroll = () => { chat.scrollTop = chat.scrollHeight; };

 async function api(body) {
  const r = await fetch('/api/profesor-clase.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf() }, body: JSON.stringify(body) });
  const data = await r.json().catch(() => ({ error: 'Recargá la página e ingresá de nuevo.' }));
  if (!r.ok) throw Error(data.error || 'No se pudo completar la acción.');
  return data;
 }

 /* ---------- Paneles ---------- */
 function show(panel) {
  ['ruta', 'historial', 'class'].forEach(p => { $('pc-' + p).hidden = p !== panel; });
  document.querySelectorAll('.pc-tab').forEach(t => { const on = t.dataset.tab === panel; t.classList.toggle('is-active', on); t.setAttribute('aria-selected', String(on)); });
  if (panel !== 'class') { session++; stopListening(); window.speechSynthesis?.cancel(); say(''); }
  window.scrollTo({ top: 0, behavior: 'smooth' });
 }
 document.querySelectorAll('.pc-tab').forEach(t => t.addEventListener('click', () => { if (t.dataset.tab === 'historial') loadHistory(); else loadCatalog(); show(t.dataset.tab); }));
 $('pc-back').addEventListener('click', () => { loadCatalog(); show('ruta'); });

 /* ---------- Ruta de clases ---------- */
 const lessonInfo = key => { for (const lv of catalog?.levels || []) { const i = lv.lessons.findIndex(l => l.key === key); if (i >= 0) return { ...lv.lessons[i], level: lv.code, number: i + 1 }; } return null; };
 async function loadCatalog() {
  try { catalog = await api({ action: 'catalog' }); } catch (e) { say(e.message); return; }
  if (!levelSel.options.length) { catalog.levels.forEach(l => levelSel.append(new Option(l.label, l.code))); level = catalog.current_level; }
  levelSel.value = level; renderLessons();
 }
 levelSel.addEventListener('change', () => { level = levelSel.value; renderLessons(); });
 function nextInLevel() { const lv = catalog.levels.find(l => l.code === level); return (lv.lessons.find(l => !l.times) || null); }
 function renderLessons() {
  const list = $('pc-lessons'), lv = catalog.levels.find(l => l.code === level), next = nextInLevel();
  list.replaceChildren();
  lv.lessons.forEach((l, i) => {
   const li = el('li', 'pc-lesson' + (l.times ? ' is-done' : '') + (next && next.key === l.key ? ' is-next' : ''));
   const num = el('span', 'pc-num', l.times ? '✓' : String(i + 1));
   const body = el('div', 'pc-lbody'); body.append(el('strong', '', l.title), el('span', 'pc-lgoal', l.goal));
   const meta = el('div', 'pc-lmeta');
   if (l.times) meta.append(el('span', 'pc-badge', 'Mejor puntaje: ' + l.best + '/100'));
   else if (next && next.key === l.key) meta.append(el('span', 'pc-badge pc-badge-next', 'Próxima clase'));
   const b = el('button', 'btn ' + (l.times ? 'btn-ghost' : ''), l.times ? 'Repetir' : 'Empezar'); b.type = 'button'; b.onclick = () => startLesson(l.key);
   meta.append(b); li.append(num, body, meta); list.append(li);
  });
  const btn = $('pc-next');
  if (next) { btn.hidden = false; btn.textContent = '▶ Empezar la clase ' + (lv.lessons.indexOf(next) + 1) + ': ' + next.title; btn.onclick = () => startLesson(next.key); }
  else {
   const idx = catalog.levels.findIndex(x => x.code === level), up = catalog.levels[idx + 1];
   btn.hidden = !up; if (up) { btn.textContent = '🎉 Completaste ' + level + '. Pasar a ' + up.code; btn.onclick = () => { level = up.code; levelSel.value = level; renderLessons(); }; }
  }
 }

 /* ---------- Clase ---------- */
 function setHeader(info, turnsDone) {
  $('pc-kicker').textContent = info.level + ' · Clase ' + info.number;
  $('pc-title').textContent = info.title; $('pc-goal').textContent = info.goal;
  const t = $('pc-targets'); t.replaceChildren(el('span', 'pc-tlabel', 'Vas a practicar:'));
  (info.targets || []).forEach(x => t.append(el('span', 'pc-chip', x)));
  setProgress(turnsDone, info.turns);
 }
 function setProgress(done, total) {
  $('pc-progress').max = total; $('pc-progress').value = done;
  $('pc-progress-text').textContent = done >= total ? 'Clase terminada' : 'Respuesta ' + Math.min(done + 1, total) + ' de ' + total;
 }
 function setComposer(on) { form.hidden = !on; $('pc-finish').parentElement.hidden = !on; }

 function row(who) {
  const r = el('div', 'pd-row' + (who === 'me' ? ' pd-me' : ''));
  const msg = el('div', 'pd-msg ' + (who === 'me' ? 'pd-student' : 'pd-teacher'));
  const text = el('p', 'pd-text'); msg.append(text);
  if (who !== 'me') r.append(el('div', 'pd-avatar', 'MT'));
  r.append(msg); chat.append(r); scroll();
  return { r, msg, text };
 }
 function studentBubble(t) { const b = row('me'); b.text.textContent = t; return b.r; }
 function teacherDone(b, m) {
  b.msg.classList.remove('pd-typing'); b.text.textContent = m.text || m.reply || '';
  const reply = b.text.textContent;
  if (m.translation) { const t = el('p', 'pd-tr', m.translation); t.lang = 'es'; t.hidden = !showTr.checked; b.msg.append(t); }
  if (m.correction) { const c = el('p', 'pd-fix', '💡 ' + m.correction); c.lang = 'es'; b.msg.append(c); }
  const tools = el('div', 'pd-tools'), l = el('button', '', '🔊 Escuchar'); l.type = 'button'; l.onclick = () => speak(reply, false); tools.append(l); b.msg.append(tools); scroll();
 }
 function teacherStatic(m) { const b = row('teacher'); teacherDone(b, m); }

 async function startLesson(key) {
  if (busy) return;
  const info = lessonInfo(key); if (!info) return;
  session++; cls = { id: null, info, done: 0, status: 'active' };
  chat.replaceChildren(); feedbackBox.hidden = true; feedbackBox.replaceChildren();
  setHeader(info, 0); setComposer(true); show('class'); session++;
  await turn({ action: 'start', lesson: key }, null);
 }

 async function turn(body, text) {
  if (busy) return;
  const mine = session; stopListening(); window.speechSynthesis?.cancel();
  const userRow = text ? studentBubble(text) : null;
  const b = row('teacher'); b.msg.classList.add('pd-typing'); b.text.innerHTML = '<span class="pd-dots"><i></i><i></i><i></i></span>';
  busy = true; send.disabled = true; say('');
  try {
   const res = await fetch('/api/profesor-clase.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf() }, body: JSON.stringify(body) });
   let data = null;
   if ((res.headers.get('content-type') || '').includes('text/event-stream')) {
    const reader = res.body.getReader(), dec = new TextDecoder(); let buf = '', acc = '';
    for (;;) {
     const { value, done } = await reader.read(); if (done) break;
     buf += dec.decode(value, { stream: true }); let i;
     while ((i = buf.indexOf('\n\n')) >= 0) {
      const line = buf.slice(0, i).trim(); buf = buf.slice(i + 2);
      if (!line.startsWith('data:')) continue;
      const ev = JSON.parse(line.slice(5));
      if (ev.class_id && cls) cls.id = ev.class_id;
      if (ev.delta) { acc += ev.delta; b.text.textContent = acc.split('[[')[0].trimEnd(); scroll(); }
      if (ev.done) data = ev;
     }
    }
    if (!data) throw Error('Se cortó la conexión. Probá de nuevo.');
   } else {
    data = await res.json().catch(() => ({ error: 'Recargá la página e ingresá de nuevo.' }));
    throw Error(data.error || 'No se pudo obtener respuesta.');
   }
   if (mine !== session) return;
   teacherDone(b, data);
   cls.done = data.exchange; setProgress(data.exchange, data.turns);
   if (data.ended) {
    cls.status = 'ended'; setComposer(false);
    speak(data.reply, false);
    finish();
   } else speak(data.reply, auto.checked);
  } catch (e) {
   if (mine !== session) return;
   b.msg.classList.remove('pd-typing'); b.msg.classList.add('pd-error'); b.text.textContent = '⚠️ ' + e.message;
   const tools = el('div', 'pd-tools'), again = el('button', '', '↻ Reintentar'); again.type = 'button';
   again.onclick = () => { b.r.remove(); userRow?.remove(); if (body.action === 'start') startLesson(body.lesson); else turn(body, text); };
   tools.append(again); b.msg.append(tools);
   if (body.action === 'start') setComposer(false);
  } finally { busy = false; send.disabled = false; if (!auto.checked && !form.hidden) input.focus({ preventScroll: true }); }
 }

 async function finish() {
  if (!cls?.id) return;
  setComposer(false);
  feedbackBox.hidden = false; feedbackBox.replaceChildren(el('p', 'pc-wait', '⏳ Tu profesor está preparando la devolución de la clase…'));
  feedbackBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  try {
   const data = await api({ action: 'finish', class_id: cls.id });
   if (data.discarded) { feedbackBox.hidden = true; say('La clase se descartó porque no llegaste a responder.'); loadCatalog(); show('ruta'); return; }
   cls.status = 'completed'; renderFeedback(data.class);
  } catch (e) {
   feedbackBox.replaceChildren(el('p', 'pd-error pc-wait', '⚠️ ' + e.message));
   const again = el('button', 'btn', 'Reintentar'); again.type = 'button'; again.onclick = finish; feedbackBox.append(again);
  }
 }
 $('pc-finish').addEventListener('click', () => {
  if (busy || !cls?.id) return;
  if (!confirm('¿Terminar la clase ahora? Vas a recibir la devolución con lo que hiciste hasta acá.')) return;
  session++; stopListening(); window.speechSynthesis?.cancel(); finish();
 });

 function list(title, items, cls2) {
  if (!items || !items.length) return null;
  const s = el('section', 'pc-fbsec ' + (cls2 || '')); s.append(el('h3', '', title));
  const ul = el('ul'); items.forEach(x => ul.append(el('li', '', x))); s.append(ul); return s;
 }
 function renderFeedback(c) {
  const fb = c.feedback || {}; feedbackBox.hidden = false; feedbackBox.replaceChildren();
  const head = el('div', 'pc-fbhead');
  const score = el('div', 'pc-score', String(fb.score ?? '–')); score.append(el('small', '', '/100'));
  const htext = el('div'); htext.append(el('h2', '', '¡Clase terminada!'), el('p', '', fb.resumen || ''));
  if (fb.incompleta) htext.append(el('p', 'pc-note', 'Terminaste la clase antes del final: la devolución considera lo que alcanzaste a practicar.'));
  head.append(score, htext); feedbackBox.append(head);
  [list('✅ Qué aprendiste en esta clase', fb.aprendiste, 'pc-learned'), list('💪 Lo que hiciste bien', fb.fortalezas), list('🎯 Para mejorar', fb.mejorar)].forEach(s => s && feedbackBox.append(s));
  if (fb.correcciones?.length) {
   const s = el('section', 'pc-fbsec'); s.append(el('h3', '', '✏️ Correcciones'));
   const t = el('table', 'pc-fix'); const tr = el('tr'); ['Dijiste', 'Mejor así', 'Por qué'].forEach(h => tr.append(el('th', '', h))); t.append(tr);
   fb.correcciones.forEach(x => { const r = el('tr'); [x.dijiste, x.mejor, x.explicacion].forEach((v, i) => { const td = el('td', '', v || ''); if (i < 2) td.lang = 'en'; r.append(td); }); t.append(r); });
   s.append(t); feedbackBox.append(s);
  }
  if (fb.proximo_paso) { const s = el('section', 'pc-fbsec'); s.append(el('h3', '', '➡️ Próximo paso'), el('p', '', fb.proximo_paso)); feedbackBox.append(s); }
  const actions = el('div', 'pc-fbactions');
  const nextBtn = el('button', 'btn', 'Siguiente clase ▶'); nextBtn.type = 'button';
  nextBtn.onclick = async () => { await loadCatalog(); level = c.level; levelSel.value = level; renderLessons(); const n = nextInLevel(); if (n) startLesson(n.key); else show('ruta'); };
  const again = el('button', 'btn btn-ghost', 'Repetir esta clase'); again.type = 'button'; again.onclick = () => startLesson(c.lesson);
  const hist = el('button', 'btn btn-ghost', 'Ver mi historial'); hist.type = 'button'; hist.onclick = () => { loadHistory(); show('historial'); };
  actions.append(nextBtn, again, hist); feedbackBox.append(actions);
  say(''); feedbackBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
 }

 /* ---------- Historial ---------- */
 async function loadHistory() {
  const ul = $('pc-history'); ul.replaceChildren(el('li', 'pc-empty', 'Cargando…'));
  if (!catalog) await loadCatalog();
  try {
   const { classes } = await api({ action: 'history' }); ul.replaceChildren();
   if (!classes.length) { ul.append(el('li', 'pc-empty', 'Todavía no terminaste ninguna clase. ¡Empezá la primera desde “Mi ruta”!')); return; }
   classes.forEach(c => {
    const li = el('li', 'pc-hitem'), b = el('button'); b.type = 'button';
    const d = c.finished_at ? new Date(c.finished_at.replace(' ', 'T') + 'Z').toLocaleString('es-AR', { dateStyle: 'medium', timeStyle: 'short' }) : '';
    b.append(el('span', 'pc-hscore', (c.score ?? '–') + ''), el('span', 'pc-hbody'));
    b.lastChild.append(el('strong', '', c.level + ' · Clase ' + c.number + ': ' + c.title), el('small', '', d));
    b.onclick = () => openClass(c.id); li.append(b); ul.append(li);
   });
  } catch (e) { ul.replaceChildren(el('li', 'pc-empty', e.message)); }
 }
 async function openClass(id) {
  try {
   const { class: c } = await api({ action: 'class', class_id: id });
   session++; cls = { id: c.id, info: c, done: c.turns_done, status: c.status };
   chat.replaceChildren(); setHeader(c, c.turns_done); setComposer(false); show('class');
   c.transcript.forEach(m => m.role === 'teacher' ? teacherStatic(m) : studentBubble(m.text));
   if (c.feedback) renderFeedback(c); else { feedbackBox.hidden = true; }
   chat.scrollTop = 0;
  } catch (e) { say(e.message); }
 }

 /* ---------- Voz ---------- */
 function speak(text, thenListen) {
  const mine = session;
  if (!voice.checked || !window.speechSynthesis) { if (thenListen) listen(); return; }
  speechSynthesis.cancel();
  const u = new SpeechSynthesisUtterance(text); u.lang = 'en-US'; u.rate = ['A1', 'A2'].includes(cls?.info?.level) ? 0.88 : 0.97;
  const vs = speechSynthesis.getVoices(), v = vs.find(x => x.lang.replace('_', '-') === 'en-US') || vs.find(x => x.lang.startsWith('en')); if (v) u.voice = v;
  u.onend = u.onerror = () => { if (thenListen && mine === session && cls?.status === 'active') listen(); };
  speechSynthesis.speak(u);
 }
 function setMic(on) { listening = on; mic.setAttribute('aria-pressed', String(on)); }
 function listen() {
  if (busy || listening || cls?.status !== 'active') return;
  const onResult = alts => { setMic(false); const t = (alts[0] || '').trim(); if (t) answer(t); else say('No te escuché. Tocá 🎙️ y probá de nuevo.'); };
  const onFail = msg => { setMic(false); say((msg || 'No se pudo usar el micrófono.') + ' Podés escribir tu respuesta.'); };
  setMic(true); say('Te escucho… respondé en inglés.');
  if (window.ipRecognize) { window.ipRecognize('', { lang: 'en-US', onProcessing: () => say('Entendiendo lo que dijiste…'), onResult, onFail, onEnd: () => setMic(false) }); return; }
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SR) { onFail('Tu navegador no permite dictado.'); return; }
  const rec = new SR(); rec.lang = 'en-US'; rec.onresult = e => onResult([e.results[0][0].transcript]);
  rec.onerror = e => { if (e.error !== 'aborted') onFail('No se pudo usar el micrófono (' + e.error + ').'); }; rec.onend = () => setMic(false);
  window.pdRec = rec; rec.start();
 }
 function stopListening() { if (!listening) return; window.ipVoice?.stop(); try { window.pdRec?.abort(); } catch {} setMic(false); }

 function answer(t) { if (!cls?.id || cls.status !== 'active') return; turn({ action: 'reply', class_id: cls.id, message: t }, t); }
 form.addEventListener('submit', e => { e.preventDefault(); const t = input.value.trim(); if (!t || busy) return; input.value = ''; grow(); answer(t); });
 input.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); form.requestSubmit(); } });
 const grow = () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 160) + 'px'; };
 input.addEventListener('input', grow);
 mic.addEventListener('click', () => { if (listening) { stopListening(); say('Micrófono detenido.'); return; } window.speechSynthesis?.cancel(); listen(); });
 showTr.addEventListener('change', () => chat.querySelectorAll('.pd-tr').forEach(t => { t.hidden = !showTr.checked; }));
 window.speechSynthesis?.getVoices();
 window.addEventListener('pagehide', () => { stopListening(); window.speechSynthesis?.cancel(); });
 loadCatalog();
})();
