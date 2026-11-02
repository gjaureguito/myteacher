(() => {
 'use strict';
 const $ = id => document.getElementById(id);
 const chat = $('pd-chat'), form = $('pd-form'), input = $('pd-input'), mic = $('pd-mic'), status = $('pd-status');
 const level = $('pd-level'), scenario = $('pd-scenario'), auto = $('pd-auto'), showTr = $('pd-translate'), voice = $('pd-voice');
 if (!chat || !form) return;
 let history = [], busy = false, listening = false, active = false, session = 0;

 const csrf = () => document.querySelector('meta[name="csrf"]')?.content || '';
 const say = text => { status.textContent = text; };

 function bubble(who, text, extra = {}) {
  chat.querySelector('.pd-empty')?.remove();
  const box = document.createElement('div');
  box.className = 'pd-msg ' + (who === 'teacher' ? 'pd-teacher' : 'pd-student');
  const label = document.createElement('span'); label.className = 'pd-who'; label.textContent = who === 'teacher' ? 'Profesor' : 'Vos';
  const p = document.createElement('p'); p.lang = 'en'; p.textContent = text;
  box.append(label, p);
  if (extra.translation) { const t = document.createElement('p'); t.className = 'pd-tr'; t.lang = 'es'; t.textContent = extra.translation; t.hidden = !showTr.checked; box.append(t); }
  if (extra.correction) { const c = document.createElement('p'); c.className = 'pd-fix'; c.lang = 'es'; c.textContent = '💡 ' + extra.correction; box.append(c); }
  if (who === 'teacher') { const b = document.createElement('button'); b.type = 'button'; b.className = 'pd-replay'; b.textContent = '🔊 Escuchar de nuevo'; b.onclick = () => speak(text, false); box.append(b); }
  chat.append(box); chat.scrollTop = chat.scrollHeight;
  return box;
 }

 function speak(text, thenListen) {
  if (!voice.checked || !window.speechSynthesis) { if (thenListen) listen(); return; }
  speechSynthesis.cancel();
  const u = new SpeechSynthesisUtterance(text);
  u.lang = 'en-US'; u.rate = level.value <= 'A2' ? 0.85 : 0.95;
  const en = speechSynthesis.getVoices().find(v => /^en[-_]US/i.test(v.lang)) || speechSynthesis.getVoices().find(v => /^en/i.test(v.lang));
  if (en) u.voice = en;
  const mine = session;
  u.onend = () => { if (thenListen && mine === session) listen(); };
  u.onerror = () => { if (thenListen && mine === session) listen(); };
  speechSynthesis.speak(u);
 }

 async function turn(message) {
  if (busy) return;
  busy = true; form.querySelector('#pd-send').disabled = true;
  const mine = session;
  if (message) bubble('student', message);
  const thinking = bubble('teacher', '…'); thinking.querySelector('.pd-replay')?.remove();
  say('El profesor está pensando…');
  try {
   const r = await fetch('/api/profesor-dialogo.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf() },
    body: JSON.stringify({ start: !message, message: message || '', level: level.value, scenario: scenario.value, history: history.slice(-12) }) });
   let data; try { data = await r.json(); } catch { throw Error('Recargá la página e ingresá de nuevo.'); }
   thinking.remove();
   if (mine !== session) return;
   if (!r.ok) throw Error(data.error || 'No se pudo obtener respuesta.');
   if (message) history.push({ role: 'user', content: message });
   history.push({ role: 'assistant', content: data.reply });
   bubble('teacher', data.reply, data);
   say(auto.checked ? 'Escuchá la respuesta; después hablás vos.' : 'Tu turno: respondé hablando o escribiendo.');
   speak(data.reply, auto.checked && active);
  } catch (e) {
   thinking.remove(); say(e.message);
  } finally { busy = false; form.querySelector('#pd-send').disabled = false; input.focus({ preventScroll: true }); }
 }

 function setMic(on) { listening = on; mic.setAttribute('aria-pressed', String(on)); mic.textContent = on ? '■ Dejar de escuchar' : '🎙️ Hablar'; }

 function listen() {
  if (!active || busy || listening) return;
  const onResult = alts => { setMic(false); const text = (alts[0] || '').trim(); if (text) turn(text); else say('No te escuché. Tocá Hablar y probá de nuevo.'); };
  const onFail = msg => { setMic(false); say((msg || 'No se pudo usar el micrófono.') + ' Podés escribir tu respuesta.'); };
  setMic(true); say('Te escucho… hablá en inglés.');
  if (window.ipRecognize) {
   window.ipRecognize('', { lang: 'en-US', onStart: () => say('Te escucho… hablá en inglés.'), onProcessing: () => say('Entendiendo lo que dijiste…'), onResult, onFail, onEnd: () => setMic(false) });
   return;
  }
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SR) { onFail('Tu navegador no permite dictado.'); return; }
  const rec = new SR(); rec.lang = 'en-US'; rec.interimResults = false;
  rec.onresult = e => onResult([e.results[0][0].transcript]);
  rec.onerror = e => { if (e.error !== 'aborted') onFail('No se pudo usar el micrófono (' + e.error + ').'); };
  rec.onend = () => setMic(false);
  window.pdRec = rec; rec.start();
 }
 function stopListening() { window.ipVoice?.stop(); try { window.pdRec?.abort(); } catch {} setMic(false); }

 $('pd-start').addEventListener('click', () => {
  session++; stopListening(); window.speechSynthesis?.cancel();
  history = []; active = true; chat.replaceChildren(); form.hidden = false;
  $('pd-start').textContent = '↺ Empezar de nuevo';
  turn('');
 });
 mic.addEventListener('click', () => { if (listening) { stopListening(); say('Micrófono detenido.'); return; } window.speechSynthesis?.cancel(); active = true; listen(); });
 form.addEventListener('submit', e => { e.preventDefault(); const t = input.value.trim(); if (!t) return; input.value = ''; stopListening(); window.speechSynthesis?.cancel(); turn(t); });
 $('pd-end').addEventListener('click', () => {
  session++; active = false; stopListening(); window.speechSynthesis?.cancel();
  say('Diálogo terminado. Podés empezar otro cuando quieras.'); form.hidden = true; $('pd-start').textContent = '▶ Empezar diálogo';
 });
 showTr.addEventListener('change', () => chat.querySelectorAll('.pd-tr').forEach(t => { t.hidden = !showTr.checked; }));
 window.speechSynthesis?.getVoices();
 window.addEventListener('pagehide', () => { stopListening(); window.speechSynthesis?.cancel(); });
})();
