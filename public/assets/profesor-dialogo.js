(() => {
 'use strict';
 const $ = id => document.getElementById(id);
 const chat = $('pd-chat'), form = $('pd-form'), input = $('pd-input'), mic = $('pd-mic'), send = $('pd-send'), stopBtn = $('pd-stop'), status = $('pd-status');
 const level = $('pd-level'), scenario = $('pd-scenario'), auto = $('pd-auto'), showTr = $('pd-translate'), voice = $('pd-voice');
 if (!chat || !form) return;
 let history = [], busy = false, listening = false, session = 0, controller = null;
 const SUGGEST = [
  ['💬 Charlemos en inglés', "Let's have a conversation in English."],
  ['📘 ¿Cuándo uso present perfect?', '¿Cuándo uso el present perfect y cuándo el past simple?'],
  ['✍️ Corregí esta frase', 'Correct this sentence: Yesterday I go to the cinema with my friends.'],
  ['🗣️ Practicar una entrevista', 'Can we practice a job interview?']
 ];
 const csrf = () => document.querySelector('meta[name="csrf"]')?.content || '';
 const say = t => { status.textContent = t || ''; };
 const scroll = () => { chat.scrollTop = chat.scrollHeight; };

 function welcome() {
  chat.replaceChildren();
  const w = document.createElement('div'); w.className = 'pd-welcome';
  const roleplay = scenario.value !== 'chat';
  w.innerHTML = '<div class="pd-avatar" style="margin:0 auto">MT</div><h2></h2><p></p><div class="pd-chips"></div>';
  w.querySelector('h2').textContent = roleplay ? scenario.options[scenario.selectedIndex].text : '¿En qué te ayudo hoy?';
  w.querySelector('p').textContent = roleplay ? 'Escena de práctica: el profesor hace su papel y vos respondés en inglés.' : 'Escribí o hablá en inglés, o preguntame cualquier duda. Te respondo y te corrijo al instante.';
  const chips = w.querySelector('.pd-chips');
  if (roleplay) { const b = chip('▶ Empezar la escena', null); b.onclick = () => turn(''); chips.append(b); }
  else SUGGEST.forEach(([label, text]) => chips.append(chip(label, text)));
  chat.append(w);
 }
 function chip(label, text) { const b = document.createElement('button'); b.type = 'button'; b.className = 'pd-chip'; b.textContent = label; if (text) b.onclick = () => turn(text); return b; }

 function row(who) {
  chat.querySelector('.pd-welcome')?.remove();
  const r = document.createElement('div'); r.className = 'pd-row' + (who === 'me' ? ' pd-me' : '');
  const msg = document.createElement('div'); msg.className = 'pd-msg ' + (who === 'me' ? 'pd-student' : 'pd-teacher');
  const text = document.createElement('p'); text.className = 'pd-text'; msg.append(text);
  if (who !== 'me') { const a = document.createElement('div'); a.className = 'pd-avatar'; a.textContent = 'MT'; r.append(a); }
  r.append(msg); chat.append(r); scroll();
  return { r, msg, text };
 }
 function userBubble(t) { const b = row('me'); b.text.textContent = t; return b.r; }

 function finishTeacher(b, data) {
  b.msg.classList.remove('pd-typing');
  b.text.textContent = data.reply;
  if (data.translation) { const t = document.createElement('p'); t.className = 'pd-tr'; t.lang = 'es'; t.textContent = data.translation; t.hidden = !showTr.checked; b.msg.append(t); }
  if (data.correction) { const c = document.createElement('p'); c.className = 'pd-fix'; c.lang = 'es'; c.textContent = '💡 ' + data.correction; b.msg.append(c); }
  const tools = document.createElement('div'); tools.className = 'pd-tools';
  const listen1 = document.createElement('button'); listen1.type = 'button'; listen1.textContent = '🔊 Escuchar'; listen1.onclick = () => speak(data.reply, false);
  const copy = document.createElement('button'); copy.type = 'button'; copy.textContent = '📋 Copiar'; copy.onclick = async () => { try { await navigator.clipboard.writeText(data.reply); say('Respuesta copiada.'); } catch { say('No se pudo copiar.'); } };
  tools.append(listen1, copy); b.msg.append(tools); scroll();
 }
 function errorBubble(b, msg, retry) {
  b.msg.classList.remove('pd-typing'); b.msg.classList.add('pd-error'); b.text.textContent = '⚠️ ' + msg;
  const tools = document.createElement('div'); tools.className = 'pd-tools';
  const again = document.createElement('button'); again.type = 'button'; again.textContent = '↻ Reintentar'; again.onclick = () => { b.r.remove(); retry(); };
  tools.append(again); b.msg.append(tools); scroll();
 }

 const isSpanish = t => (t.match(/\b(el|la|los|las|que|de|para|con|una|por|cuando|usás|vos|es|se)\b/gi) || []).length > (t.match(/\b(the|is|are|you|and|to|of|in|it|what|do)\b/gi) || []).length;
 function speak(text, thenListen) {
  const mine = session;
  if (!voice.checked || !window.speechSynthesis) { if (thenListen) listen(); return; }
  speechSynthesis.cancel();
  const lang = isSpanish(text) ? 'es' : 'en';
  const u = new SpeechSynthesisUtterance(text.replace(/[*_#`]/g, ''));
  u.lang = lang === 'es' ? 'es-AR' : 'en-US'; u.rate = lang === 'en' && level.value <= 'A2' ? 0.88 : 0.97;
  const voices = speechSynthesis.getVoices(), v = voices.find(x => x.lang.replace('_', '-') === u.lang) || voices.find(x => x.lang.startsWith(lang));
  if (v) u.voice = v;
  u.onend = u.onerror = () => { if (thenListen && mine === session) listen(); };
  speechSynthesis.speak(u);
 }

 function setBusy(on) { busy = on; send.hidden = on; stopBtn.hidden = !on; }

 async function turn(message) {
  if (busy) return;
  const text = (message || '').trim(), start = !text;
  const mine = session;
  stopListening(); window.speechSynthesis?.cancel();
  const userRow = start ? null : userBubble(text);
  const b = row('teacher'); b.msg.classList.add('pd-typing'); b.text.innerHTML = '<span class="pd-dots"><i></i><i></i><i></i></span>';
  setBusy(true); say('');
  controller = new AbortController();
  const retry = () => { userRow?.remove(); turn(text); };
  try {
   const res = await fetch('/api/profesor-dialogo.php', { method: 'POST', signal: controller.signal,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf() },
    body: JSON.stringify({ start, message: text, level: level.value, scenario: scenario.value, history: history.slice(-16) }) });
   let data = null;
   if ((res.headers.get('content-type') || '').includes('text/event-stream')) {
    const reader = res.body.getReader(), dec = new TextDecoder(); let buf = '', acc = '';
    b.text.textContent = '';
    for (;;) {
     const { value, done } = await reader.read(); if (done) break;
     buf += dec.decode(value, { stream: true });
     let i;
     while ((i = buf.indexOf('\n\n')) >= 0) {
      const line = buf.slice(0, i).trim(); buf = buf.slice(i + 2);
      if (!line.startsWith('data:')) continue;
      const ev = JSON.parse(line.slice(5));
      if (ev.delta) { acc += ev.delta; b.text.textContent = acc.split('[[')[0].trimEnd(); scroll(); }
      if (ev.done) data = ev;
     }
    }
    if (!data) data = { reply: acc.split('[[')[0].trim() };
   } else {
    data = await res.json().catch(() => ({ error: 'Recargá la página e ingresá de nuevo.' }));
    if (!res.ok) throw Error(data.error || 'No se pudo obtener respuesta.');
   }
   if (mine !== session) return;
   if (!data.reply) throw Error('El profesor no respondió. Probá de nuevo.');
   if (!start) history.push({ role: 'user', content: text });
   history.push({ role: 'assistant', content: data.reply });
   finishTeacher(b, data);
   if (typeof data.remaining === 'number' && data.remaining <= 10) say('Te quedan ' + data.remaining + ' mensajes hoy.');
   speak(data.reply, auto.checked);
  } catch (e) {
   if (e.name === 'AbortError') { b.msg.classList.remove('pd-typing'); if (!b.text.textContent.trim() || b.text.querySelector('.pd-dots')) b.r.remove(); say('Respuesta detenida.'); }
   else if (mine === session) errorBubble(b, e.message, retry);
  } finally { controller = null; setBusy(false); if (!auto.checked) input.focus({ preventScroll: true }); }
 }

 function setMic(on) { listening = on; mic.setAttribute('aria-pressed', String(on)); mic.title = on ? 'Dejar de escuchar' : 'Hablar'; }
 function listen() {
  if (busy || listening) return;
  const onResult = alts => { setMic(false); const t = (alts[0] || '').trim(); if (t) turn(t); else say('No te escuché. Tocá 🎙️ y probá de nuevo.'); };
  const onFail = msg => { setMic(false); say((msg || 'No se pudo usar el micrófono.') + ' Podés escribir tu mensaje.'); };
  setMic(true); say('Te escucho… hablá en inglés.');
  if (window.ipRecognize) {
   window.ipRecognize('', { lang: 'en-US', onStart: () => say('Te escucho… hablá en inglés.'), onProcessing: () => say('Entendiendo lo que dijiste…'), onResult, onFail, onEnd: () => setMic(false) });
   return;
  }
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SR) { onFail('Tu navegador no permite dictado.'); return; }
  const rec = new SR(); rec.lang = 'en-US';
  rec.onresult = e => onResult([e.results[0][0].transcript]);
  rec.onerror = e => { if (e.error !== 'aborted') onFail('No se pudo usar el micrófono (' + e.error + ').'); };
  rec.onend = () => setMic(false);
  window.pdRec = rec; rec.start();
 }
 function stopListening() { if (!listening) return; window.ipVoice?.stop(); try { window.pdRec?.abort(); } catch {} setMic(false); }

 function reset() { session++; controller?.abort(); stopListening(); window.speechSynthesis?.cancel(); history = []; say(''); welcome(); }

 form.addEventListener('submit', e => { e.preventDefault(); const t = input.value.trim(); if (!t || busy) return; input.value = ''; grow(); turn(t); });
 input.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); form.requestSubmit(); } });
 const grow = () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 180) + 'px'; };
 input.addEventListener('input', grow);
 mic.addEventListener('click', () => { if (listening) { stopListening(); say('Micrófono detenido.'); return; } window.speechSynthesis?.cancel(); listen(); });
 stopBtn.addEventListener('click', () => controller?.abort());
 $('pd-new').addEventListener('click', reset);
 scenario.addEventListener('change', () => { reset(); if (scenario.value !== 'chat') turn(''); });
 level.addEventListener('change', () => say('Nivel ' + level.value + ': se aplica desde tu próximo mensaje.'));
 showTr.addEventListener('change', () => chat.querySelectorAll('.pd-tr').forEach(t => { t.hidden = !showTr.checked; }));
 window.speechSynthesis?.getVoices();
 window.addEventListener('pagehide', () => { controller?.abort(); stopListening(); window.speechSynthesis?.cancel(); });
 welcome();
})();
