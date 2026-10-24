/* =====================================================================
   InglésPro · capa "Pro": sonidos (Web Audio, sin archivos), confeti,
   XP y combos, atajos de teclado, práctica de pronunciación con
   micrófono, modo oscuro, animaciones de entrada y toasts.
   ===================================================================== */
(() => {
  'use strict';
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const store = {
    get: (k, d) => { try { const v = localStorage.getItem(k); return v === null ? d : v; } catch { return d; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch {} }
  };
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.documentElement.classList.add('js');

  /* ---------- Toasts ---------- */
  let toastWrap;
  window.ipToast = (msg) => {
    if (!toastWrap) { toastWrap = document.createElement('div'); toastWrap.className = 'toast-wrap'; toastWrap.setAttribute('role', 'status'); document.body.append(toastWrap); }
    const t = document.createElement('div'); t.className = 'toast'; t.textContent = msg;
    toastWrap.append(t); setTimeout(() => t.remove(), 3400);
  };

  /* ---------- Sonido: sintetizado con Web Audio ---------- */
  let ctx = null;
  let soundOn = store.get('ip_sound', '1') === '1';
  function ac() {
    if (!ctx) { const C = window.AudioContext || window.webkitAudioContext; if (!C) return null; ctx = new C(); }
    if (ctx.state === 'suspended') ctx.resume();
    return ctx;
  }
  function tone(freq, start, dur, { type = 'sine', gain = 0.18, to = null } = {}) {
    const a = ac(); if (!a) return;
    const t0 = a.currentTime + start;
    const o = a.createOscillator(), g = a.createGain();
    o.type = type; o.frequency.setValueAtTime(freq, t0);
    if (to) o.frequency.exponentialRampToValueAtTime(to, t0 + dur);
    g.gain.setValueAtTime(0.0001, t0);
    g.gain.exponentialRampToValueAtTime(gain, t0 + 0.012);
    g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
    o.connect(g).connect(a.destination); o.start(t0); o.stop(t0 + dur + 0.05);
  }
  const sfx = {
    tap: () => tone(880, 0, 0.06, { type: 'triangle', gain: 0.05 }),
    flip: () => tone(420, 0, 0.14, { type: 'sine', gain: 0.09, to: 900 }),
    ok: () => { tone(659.25, 0, 0.14, { type: 'triangle' }); tone(987.77, 0.09, 0.26, { type: 'triangle' }); },
    bad: () => { tone(220, 0, 0.18, { type: 'square', gain: 0.05, to: 160 }); tone(185, 0.12, 0.22, { type: 'square', gain: 0.04, to: 130 }); },
    combo: () => [784, 988, 1175].forEach((f, i) => tone(f, i * 0.07, 0.16, { type: 'triangle', gain: 0.12 })),
    complete: () => [523.25, 659.25, 783.99, 1046.5, 1318.5].forEach((f, i) => tone(f, i * 0.11, i === 4 ? 0.7 : 0.22, { type: 'triangle', gain: 0.15 })),
    mic: () => tone(1200, 0, 0.08, { type: 'sine', gain: 0.08 }),
  };
  const play = (name) => { if (soundOn && sfx[name]) sfx[name](); };
  window.ipPlay = play;

  /* ---------- Botones de herramientas: sonido y tema ---------- */
  const soundBtn = $('#tool-sound'), themeBtn = $('#tool-theme');
  if (soundBtn) {
    soundBtn.setAttribute('aria-pressed', String(soundOn));
    soundBtn.addEventListener('click', () => {
      soundOn = !soundOn; store.set('ip_sound', soundOn ? '1' : '0');
      soundBtn.setAttribute('aria-pressed', String(soundOn));
      if (soundOn) play('ok');
      ipToast(soundOn ? 'Efectos de sonido activados' : 'Efectos de sonido silenciados');
    });
  }
  if (themeBtn) {
    const sync = () => themeBtn.setAttribute('aria-pressed', String(document.documentElement.dataset.theme === 'dark'));
    sync();
    themeBtn.addEventListener('click', () => {
      const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
      document.documentElement.dataset.theme = next; store.set('ip_theme', next); sync(); play('flip');
    });
  }

  /* ---------- Topbar al hacer scroll ---------- */
  const topbar = $('.topbar');
  const onScroll = () => topbar && topbar.classList.toggle('is-scrolled', scrollY > 8);
  addEventListener('scroll', onScroll, { passive: true }); onScroll();
  const setTopH = () => topbar && document.documentElement.style.setProperty('--topbar-h', topbar.offsetHeight + 'px');
  setTopH(); addEventListener('resize', setTopH);

  /* ---------- Efecto ripple y clic sutil ---------- */
  document.addEventListener('pointerdown', e => {
    const b = e.target.closest('.btn, .opt, .chip, .tabs button, .tool-btn');
    if (!b) return;
    if (b.matches('.btn') && !reduced) {
      const r = b.getBoundingClientRect(), s = Math.max(r.width, r.height);
      const span = document.createElement('span'); span.className = 'ripple';
      span.style.cssText = `width:${s}px;height:${s}px;left:${e.clientX - r.left - s / 2}px;top:${e.clientY - r.top - s / 2}px`;
      b.append(span); setTimeout(() => span.remove(), 650);
    }
    if (!b.matches('.opt')) play('tap');
  });
  document.addEventListener('click', e => { if (e.target.closest('.card-flip') && !e.target.closest('.speak, .mic-btn')) play('flip'); });

  /* ---------- Animaciones de entrada ---------- */
  const revealSel = ['.academic-facts p', '.section-intro', '.experience-grid article', '.featured-intro', '.featured-cover',
    '.program-intro', '.track-row', '.method-layout > div', '.method-notebook li', '.academic-plans article', '.academic-faq > div',
    '.academic-closing .wrap > *', '.stat', '.level-card', '.dash-cards .resume', '.desk-guide', '.unit-row', '.library-card', '.unit-index'];
  const targets = $$(revealSel.join(','));
  const groups = new Map();
  targets.forEach(el => {
    const parent = el.parentElement; const i = groups.get(parent) || 0; groups.set(parent, i + 1);
    el.dataset.reveal = ''; el.style.setProperty('--d', Math.min(i, 8));
  });
  if ('IntersectionObserver' in window && !reduced) {
    const io = new IntersectionObserver(entries => entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } }), { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    targets.forEach(el => io.observe(el));
  } else targets.forEach(el => el.classList.add('is-in'));

  /* ---------- Contadores animados ---------- */
  const countUp = (el) => {
    const target = parseInt(el.dataset.count ?? el.textContent.replace(/\D/g, ''), 10);
    if (!target || reduced) return;
    const t0 = performance.now(), dur = 1200;
    const step = (t) => { const k = Math.min(1, (t - t0) / dur); el.textContent = Math.round(target * (1 - Math.pow(1 - k, 3))).toLocaleString('es-AR'); if (k < 1) requestAnimationFrame(step); };
    requestAnimationFrame(step);
  };
  if ('IntersectionObserver' in window) {
    const io2 = new IntersectionObserver(es => es.forEach(en => { if (en.isIntersecting) { countUp(en.target); io2.unobserve(en.target); } }));
    $$('.academic-facts strong, [data-count]').forEach(el => io2.observe(el));
  }
  $$('.pring').forEach(r => { const p = r.style.getPropertyValue('--p'); if (!reduced) { r.style.setProperty('--p', 0); setTimeout(() => r.style.setProperty('--p', p), 300); } });

  /* ---------- Íconos de audio y micrófono ---------- */
  const ICON_SPK = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3A4.5 4.5 0 0 0 14 7.97v8.05A4.47 4.47 0 0 0 16.5 12zM14 3.23v2.06a7 7 0 0 1 0 13.42v2.06A9 9 0 0 0 14 3.23z"/></svg>';
  const ICON_MIC = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.92V21h2v-3.08A7 7 0 0 0 19 11h-2z"/></svg>';
  $$('.ex-say, .card-flip .speak').forEach(b => { if (b.textContent.trim() === '🔊') b.innerHTML = ICON_SPK; });
  $$('.speak').forEach(b => { if (b.textContent.includes('🔊')) b.innerHTML = b.innerHTML.replace('🔊', ICON_SPK); });

  /* ---------- Práctica de pronunciación (Web Speech Recognition) ---------- */
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  const norm = s => s.toLowerCase().replace(/[’']/g, "'").replace(/[^a-z0-9' ]+/g, ' ').replace(/\s+/g, ' ').trim();
  function similarity(target, heard) {
    const a = norm(target).split(' '), b = norm(heard).split(' ');
    const dp = Array.from({ length: a.length + 1 }, () => new Array(b.length + 1).fill(0));
    for (let i = 1; i <= a.length; i++) for (let j = 1; j <= b.length; j++)
      dp[i][j] = a[i - 1] === b[j - 1] ? dp[i - 1][j - 1] + 1 : Math.max(dp[i - 1][j], dp[i][j - 1]);
    const missing = a.filter(w => !b.includes(w));
    return { score: Math.round(100 * dp[a.length][b.length] / Math.max(a.length, b.length)), missing };
  }
  /* Reconocimiento de voz compartido (también lo usan los ejercicios de speaking en app.js) */
  const SR_ERRORS = {
    'not-allowed': 'El navegador bloqueó el micrófono. Tocá el candado junto a la dirección del sitio, permití el micrófono y probá de nuevo.',
    'service-not-allowed': 'Este navegador no permite el reconocimiento de voz (pasa en Brave y algunos navegadores con bloqueo). Probá con Chrome o Edge.',
    'no-speech': 'No te escuché. Tocá el botón y hablá apenas aparezca "Escuchando…".',
    'audio-capture': 'No se encontró ningún micrófono. Revisá que esté conectado y habilitado.',
    'network': 'El reconocimiento de voz necesita conexión a internet (Chrome usa un servicio online). Revisá la conexión y probá de nuevo.',
    'language-not-supported': 'Tu navegador no tiene disponible el reconocimiento en inglés.',
  };
  /* ---------- Asistente para activar el micrófono ---------- */
  const isWin = /Windows/.test(navigator.userAgent);
  const isEdge = /Edg\//.test(navigator.userAgent);
  const browserName = isEdge ? 'Edge' : 'Chrome';
  const siteSettingsUrl = (isEdge ? 'edge' : 'chrome') + '://settings/content/siteDetails?site=' + encodeURIComponent(location.origin);
  const PS_FIX = [
    "$p = 'HKCU:\\Software\\Microsoft\\Windows\\CurrentVersion\\CapabilityAccessManager\\ConsentStore\\microphone'",
    "foreach ($k in @($p, \"$p\\NonPackaged\")) {",
    "  if (-not (Test-Path $k)) { New-Item -Path $k | Out-Null }",
    "  Set-ItemProperty -Path $k -Name Value -Value Allow",
    "}",
    "Write-Host 'Listo: cerrá Chrome por completo y volvé a abrirlo.'",
  ].join('\n');
  const PS_AUDIO = 'Restart-Service -Name AudioEndpointBuilder -Force; Start-Service -Name Audiosrv';
  async function testMic() {
    if (!navigator.mediaDevices?.getUserMedia) return { code: 'unsupported' };
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      return { code: 'ok', stream };
    } catch (e) {
      if (e.name === 'NotAllowedError') return { code: /system/i.test(e.message) ? 'system' : 'blocked', err: e.name };
      return { code: { NotReadableError: 'busy', SecurityError: 'blocked', NotFoundError: 'nodevice', OverconstrainedError: 'nodevice' }[e.name] || 'busy', err: e.name };
    }
  }
  function micHelp() {
    document.querySelector('.mic-help')?.remove();
    const back = document.createElement('div'); back.className = 'modal-back mic-help';
    back.innerHTML = `<div class="modal mic-modal" role="dialog" aria-modal="true" aria-labelledby="mh-t">
      <p class="eyebrow">MICRÓFONO</p><h2 id="mh-t">Activá tu micrófono</h2>
      <div class="mh-status" aria-live="polite">Probando el micrófono…</div>
      <div class="mh-meter" hidden><i></i></div>
      <section class="mh-step" data-for="system">
        <h3>${isWin ? 'Permití el micrófono en Windows' : 'Permití el micrófono en el sistema'}</h3>
        ${isWin ? `<p>Windows está bloqueando el micrófono para ${browserName}. Tocá el botón, aceptá cuando el navegador pregunte si querés abrir Configuración, y activá <b>Acceso al micrófono</b> y <b>Permitir que las aplicaciones de escritorio accedan al micrófono</b>.</p>
        <a class="btn" href="ms-settings:privacy-microphone">Abrir configuración de micrófono de Windows</a>
        <div class="mh-auto">
          <h4>⚡ Solución automática</h4>
          <p>Descargá el activador y abrilo con doble clic: aplica estos permisos por vos y abre la configuración para que verifiques. Para que también reinicie el audio de Windows, abrilo con clic derecho → <b>Ejecutar como administrador</b>.</p>
          <div class="mh-actions"><a class="btn" href="/tools/activar-microfono.bat" download="activar-microfono.bat">⬇️ Descargar activador (.bat)</a><button type="button" class="btn btn-ghost" data-copy-ps>Copiar comando de PowerShell</button></div>
          <p class="fine">Si Windows muestra "Windows protegió su PC", tocá <b>Más información</b> → <b>Ejecutar de todas formas</b>. El archivo solo cambia los permisos de micrófono; podés ver exactamente qué hace acá abajo.</p>
          <details><summary>Ver los comandos</summary><pre class="mh-pre">${PS_FIX.replace(/</g, '&lt;')}</pre></details>
        </div>` : `<p>En macOS: Configuración del Sistema → Privacidad y seguridad → Micrófono → activá ${browserName}. En el celular: Ajustes → Apps → ${browserName} → Permisos → Micrófono.</p>`}
        <p class="fine">Si ya estaba activado, cerrá Zoom, Teams, Discord o WhatsApp (pueden estar ocupando el micrófono) y reiniciá ${browserName}.</p>
      </section>
      <section class="mh-step" data-for="busy">
        <h3>${isWin ? 'Reiniciá el audio de Windows' : 'Liberá el micrófono'}</h3>
        <p>El micrófono está detectado, pero no arranca: suele pasar cuando el servicio de audio de ${isWin ? 'Windows' : 'sistema'} quedó trabado o cuando otro programa (Zoom, Teams, Discord, WhatsApp) lo tiene tomado.</p>
        ${isWin ? `<div class="mh-auto">
          <h4>⚡ Comando seguro (una línea)</h4>
          <p>Abrí la Terminal como administrador (<b>Win + X</b> → <b>Terminal (administrador)</b>), pegá esta línea y apretá Enter. Solo reinicia el servicio de audio de Windows; el sonido se corta unos segundos.</p>
          <div class="mh-copy"><code>${PS_AUDIO}</code><button type="button" class="btn btn-ghost btn-sm" data-copy-audio>Copiar</button></div>
          <p>O descargá el reparador completo: pide permiso de administrador, reinicia el audio, habilita los permisos de micrófono, reactiva micrófonos deshabilitados y te avisa qué programas lo pueden estar ocupando.</p>
          <div class="mh-actions"><a class="btn" href="/tools/reparar-audio.bat" download="reparar-audio.bat">⬇️ Descargar reparador (.bat)</a></div>
          <p class="fine">Después cerrá ${browserName} por completo, volvé a abrirlo y tocá Probar de nuevo.</p>
        </div>
        <p class="fine">Si sigue igual: Configuración → Sistema → Sonido → tu micrófono → Propiedades → desmarcá "Permitir que las aplicaciones tomen control exclusivo de este dispositivo", o actualizá el driver de audio (Realtek) desde el Administrador de dispositivos.</p>` : `<p>Cerrá las apps que usan el micrófono y reiniciá ${browserName}. Si sigue igual, reiniciá el equipo.</p>`}
      </section>
      <section class="mh-step" data-for="blocked">
        <h3>Permití el micrófono en este sitio</h3>
        <p>Tocá el ícono 🔒 o ⚙️ a la izquierda de la dirección del sitio, buscá <b>Micrófono</b> y elegí <b>Permitir</b>. Después recargá la página.</p>
        <p>También podés abrir los ajustes del sitio directamente: copiá esta dirección y pegala en una pestaña nueva.</p>
        <div class="mh-copy"><code>${siteSettingsUrl}</code><button type="button" class="btn btn-ghost btn-sm" data-copy>Copiar</button></div>
      </section>
      <section class="mh-step" data-for="nodevice">
        <h3>No hay ningún micrófono disponible</h3>
        <p>Conectá un micrófono o auriculares con micrófono y volvé a probar. En notebooks, revisá que no esté desactivado con una tecla de función.</p>
      </section>
      <section class="mh-step" data-for="unsupported">
        <h3>Este navegador no permite usar el micrófono acá</h3>
        <p>Abrí el sitio en Chrome o Edge (en la compu o en Android) para practicar pronunciación.</p>
      </section>
      <p class="fine mh-guide">¿Otro problema? <a href="/microfono.php">Ver todos los comandos para solucionar el micrófono →</a></p>
      <div class="modal-actions"><button type="button" class="btn" data-retry>Probar de nuevo</button><button type="button" class="btn btn-ghost" data-close>Cerrar</button></div>
    </div>`;
    document.body.append(back);
    const st = back.querySelector('.mh-status'), meter = back.querySelector('.mh-meter'), bar = meter.querySelector('i');
    let live = null;
    const stop = () => { if (live) { cancelAnimationFrame(live.raf); live.stream.getTracks().forEach(t => t.stop()); live.ac.close(); live = null; } };
    const show = code => back.querySelectorAll('.mh-step').forEach(sec => { sec.hidden = sec.dataset.for !== code; });
    async function run() {
      stop(); st.className = 'mh-status'; st.textContent = 'Probando el micrófono… si el navegador te pregunta, tocá Permitir.'; meter.hidden = true; show(null);
      const perm = await navigator.permissions?.query({ name: 'microphone' }).then(p => p.state).catch(() => null);
      const r = await testMic();
      if (r.code === 'ok') {
        st.className = 'mh-status ok'; st.textContent = '✅ ¡El micrófono funciona! Hablá y mirá cómo se mueve la barra. Ya podés cerrar y usar "Decilo vos".';
        meter.hidden = false;
        const ac = new AudioContext(), an = ac.createAnalyser(); ac.createMediaStreamSource(r.stream).connect(an);
        const buf = new Uint8Array(an.fftSize);
        live = { stream: r.stream, ac, raf: 0 };
        const tick = () => { an.getByteTimeDomainData(buf); let m = 0; for (const v of buf) m = Math.max(m, Math.abs(v - 128)); bar.style.width = Math.min(100, m * 1.6) + '%'; live && (live.raf = requestAnimationFrame(tick)); };
        tick();
        return;
      }
      const code = perm === 'denied' ? 'blocked' : r.code;
      st.className = 'mh-status bad';
      st.textContent = { busy: '⚠️ El micrófono está detectado pero no arranca: el audio de ' + (isWin ? 'Windows' : 'sistema') + ' está trabado o lo está usando otro programa.', system: '⚠️ ' + (isWin ? 'Windows' : 'El sistema') + ' no le da permiso de micrófono a ' + browserName + '.', blocked: '⚠️ Este sitio tiene el micrófono bloqueado en ' + browserName + '.', nodevice: '⚠️ No se encontró ningún micrófono.', unsupported: '⚠️ Este navegador no permite usar el micrófono.' }[code];
      show(code);
    }
    back.addEventListener('click', async e => {
      if (e.target === back || e.target.closest('[data-close]')) { stop(); back.remove(); }
      if (e.target.closest('[data-retry]')) run();
      if (e.target.closest('[data-copy-audio]')) {
        try { await navigator.clipboard.writeText(PS_AUDIO); ipToast('Copiado. Pegalo en la Terminal (administrador) y apretá Enter.'); }
        catch { ipToast('Seleccioná la línea y copiala con Ctrl+C.'); }
      }
      if (e.target.closest('[data-copy-ps]')) {
        try { await navigator.clipboard.writeText(PS_FIX); ipToast('Comando copiado. Abrí PowerShell (Win+X → Terminal), pegalo con clic derecho y apretá Enter.'); }
        catch { ipToast('Abrí "Ver los comandos" y copialos a mano.'); }
      }
      if (e.target.closest('[data-copy]')) {
        try { await navigator.clipboard.writeText(siteSettingsUrl); ipToast('Dirección copiada. Pegala en una pestaña nueva.'); }
        catch { ipToast('Seleccioná la dirección y copiala con Ctrl+C.'); }
      }
    });
    document.addEventListener('keydown', function k(e) { if (e.key === 'Escape') { stop(); back.remove(); document.removeEventListener('keydown', k); } });
    run();
  }
  window.ipMicHelp = micHelp;
  document.addEventListener('click', async e => {
    const c = e.target.closest('[data-copy-text]');
    if (!c) return;
    try { await navigator.clipboard.writeText(c.dataset.copyText); c.textContent = '✓ Copiado'; setTimeout(() => c.textContent = 'Copiar', 1800); }
    catch { ipToast('Seleccioná el comando y copialo con Ctrl+C.'); }
  });
  const diagBtn = document.querySelector('[data-mic-diag]');
  if (diagBtn) {
    const out = document.querySelector('[data-diag-out]'), meter = document.querySelector('[data-diag-meter]');
    const target = { busy: isWin ? 'win-audio' : (navigator.platform.startsWith('Mac') ? 'mac' : 'linux'), system: isWin ? 'win-privacidad' : 'mac', blocked: 'navegador', nodevice: isWin ? 'win-deshabilitado' : 'mac', unsupported: 'android' };
    const text = { busy: 'El micrófono está detectado pero no arranca: el audio del sistema está trabado o lo usa otro programa.', system: 'El sistema no le da permiso de micrófono al navegador.', blocked: 'Este sitio tiene el micrófono bloqueado en el navegador.', nodevice: 'No se encontró ningún micrófono habilitado.', unsupported: 'Este navegador no permite usar el micrófono.' };
    diagBtn.addEventListener('click', async () => {
      out.hidden = false; out.className = 'mh-status'; out.textContent = 'Probando… si el navegador pregunta, tocá Permitir.';
      const perm = await navigator.permissions?.query({ name: 'microphone' }).then(p => p.state).catch(() => null);
      const r = await testMic();
      if (r.code === 'ok') {
        out.className = 'mh-status ok'; out.textContent = '✅ Tu micrófono funciona en este navegador. Hablá y mirá la barra. Si "Decilo vos" igual falla, revisá tu conexión a internet: el reconocimiento de voz de Chrome funciona online.';
        meter.hidden = false; const bar = meter.querySelector('i');
        const ac = new AudioContext(), an = ac.createAnalyser(); ac.createMediaStreamSource(r.stream).connect(an);
        const buf = new Uint8Array(an.fftSize); const t0 = Date.now();
        const tick = () => { an.getByteTimeDomainData(buf); let m = 0; for (const v of buf) m = Math.max(m, Math.abs(v - 128)); bar.style.width = Math.min(100, m * 1.6) + '%';
          if (Date.now() - t0 < 15000) requestAnimationFrame(tick); else { r.stream.getTracks().forEach(t => t.stop()); ac.close(); bar.style.width = '0'; } };
        tick(); return;
      }
      const code = perm === 'denied' ? 'blocked' : r.code;
      out.className = 'mh-status bad'; out.innerHTML = '';
      const msg = document.createElement('span'); msg.textContent = '⚠️ ' + text[code] + ' ';
      const go = document.createElement('a'); go.href = '#' + target[code]; go.textContent = 'Ir a la solución →';
      out.append(msg, go);
      document.getElementById(target[code])?.classList.add('mic-hit');
      setTimeout(() => document.getElementById(target[code])?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 600);
    });
  }
  document.addEventListener('click', e => { if (e.target.closest('[data-mic-help]')) { e.preventDefault(); micHelp(); } });

  /* Cuando Chrome no puede abrir el micrófono, averigua por qué para dar instrucciones concretas */
  async function diagnoseMic() {
    const win = /Windows/.test(navigator.userAgent);
    if (!navigator.mediaDevices?.getUserMedia) return SR_ERRORS['audio-capture'];
    try {
      const st = await navigator.mediaDevices.getUserMedia({ audio: true });
      st.getTracks().forEach(t => t.stop());
      return 'El micrófono funciona pero el reconocimiento no pudo usarlo. Cerrá otras pestañas o apps que estén usando el micrófono y probá de nuevo.';
    } catch (e) {
      if (e.name === 'NotFoundError') return 'No se encontró ningún micrófono. Revisá que esté conectado y habilitado.';
      if (e.name === 'NotAllowedError') return /system/i.test(e.message)
        ? (win ? 'Windows no le da permiso de micrófono a Chrome.' : 'El sistema no le da permiso de micrófono al navegador.')
        : SR_ERRORS['not-allowed'];
      if (e.name === 'NotReadableError') return win
        ? 'El micrófono está conectado pero no arranca: el audio de Windows está trabado o lo usa otro programa.'
        : 'El micrófono no arranca: lo está usando otra aplicación o el audio del sistema está trabado.';
      return SR_ERRORS['audio-capture'];
    }
  }
  function recognize(target, { onStart, onResult, onFail, onEnd }) {
    if (!SR) { onFail('Tu navegador no permite el reconocimiento de voz. Probá con Chrome o Edge en la compu o en Android.'); onEnd && onEnd(); return; }
    if (window.speechSynthesis) speechSynthesis.cancel(); // que el micrófono no escuche la voz del sitio
    const rec = new SR(); rec.lang = 'en-US'; rec.interimResults = false; rec.continuous = false; rec.maxAlternatives = 3;
    let got = false, failed = false;
    const safety = setTimeout(() => { try { rec.stop(); } catch {} }, 9000);
    rec.onstart = () => onStart && onStart();
    rec.onresult = ev => {
      got = true;
      try {
        const res = ev.results[ev.results.length - 1];
        const alts = [];
        for (let i = 0; i < res.length; i++) if (res[i] && res[i].transcript) alts.push(res[i].transcript.trim());
        if (!alts.length) { failed = true; onFail(SR_ERRORS['no-speech']); return; }
        onResult(alts);
      } catch (err) { failed = true; onFail('No pude procesar lo que dijiste (' + err.message + '). Probá de nuevo.'); }
    };
    rec.onerror = ev => {
      if (ev.error === 'aborted') return;
      failed = true;
      if (ev.error === 'audio-capture') { diagnoseMic().then(onFail); return; }
      onFail(SR_ERRORS[ev.error] || ('No se pudo usar el micrófono (' + ev.error + '). Probá de nuevo.'));
    };
    rec.onend = () => {
      clearTimeout(safety);
      if (!got && !failed) onFail(SR_ERRORS['no-speech']);
      onEnd && onEnd();
    };
    try { rec.start(); } catch (err) { clearTimeout(safety); onFail('El micrófono ya está en uso. Esperá un segundo y probá de nuevo.'); onEnd && onEnd(); }
  }
  window.ipRecognize = recognize;

  function listen(btn, target, outEl) {
    if (btn.classList.contains('is-listening')) return;
    const lab = btn.querySelector('.lab');
    const status = (cls, html) => { outEl.className = 'pron-result ' + cls; outEl.innerHTML = html; };
    btn.classList.add('is-listening'); if (lab) lab.textContent = ' Escuchando…';
    status('wait', '🎙️ Preparando el micrófono…');
    play('mic');
    recognize(target, {
      onStart: () => status('wait', '🔴 Escuchando… decí la frase en inglés.'),
      onResult: alts => {
        const best = alts.map(t => ({ t, ...similarity(target, t) })).sort((x, y) => y.score - x.score)[0];
        const cls = best.score >= 85 ? 'good' : best.score >= 55 ? 'mid' : 'low';
        const msg = cls === 'good' ? '¡Excelente pronunciación!' : cls === 'mid' ? 'Muy cerca. Escuchá de nuevo y repetí.' : 'Probá otra vez, más despacio.';
        outEl.className = 'pron-result ' + cls; outEl.innerHTML = '';
        const b = document.createElement('b'); b.textContent = best.score + '% · ' + msg;
        const sm = document.createElement('span'); sm.textContent = ' Entendí: "' + best.t + '"' + (best.missing.length && cls !== 'good' ? ' · Revisá: ' + best.missing.slice(0, 4).join(', ') : '');
        outEl.append(b, sm);
        play(cls === 'good' ? 'ok' : cls === 'mid' ? 'tap' : 'bad');
        if (cls === 'good') { addXP(5, btn); burst(btn, 26); }
      },
      onFail: msg => { status('low', '⚠️ ' + msg.replace(/[<>&]/g, '') + ' <button type="button" class="linkbtn mh-link" data-mic-help>Cómo activar el micrófono →</button>'); play('bad'); },
      onEnd: () => { btn.classList.remove('is-listening'); if (lab) lab.textContent = ' Decilo vos'; },
    });
  }
  function micButton(target, label = true) {
    const b = document.createElement('button'); b.type = 'button'; b.className = 'mic-btn';
    b.setAttribute('aria-label', 'Practicar pronunciación: ' + target);
    b.innerHTML = ICON_MIC + (label ? '<span class="lab"> Decilo vos</span>' : '');
    return b;
  }
  {
    $$('.theory .ex').forEach(p => {
      const say = p.querySelector('.ex-say'); if (!say) return;
      const target = say.dataset.say; const btn = micButton(target); const out = document.createElement('span'); out.className = 'pron-result';
      const es = p.querySelector('.es'); if (es) es.before(btn); else p.append(btn);
      p.append(out);
      btn.addEventListener('click', e => { e.stopPropagation(); listen(btn, target, out); });
    });
    $$('.card-flip').forEach(c => {
      const s = c.querySelector('.speak'); if (!s) return;
      const btn = micButton(s.dataset.say, false); btn.style.cssText = 'position:absolute;left:8px;top:8px;margin:0;padding:6px';
      const out = document.createElement('span'); out.className = 'pron-result'; out.style.cssText = 'position:absolute;left:8px;right:8px;bottom:-26px;font-size:.72rem;text-align:left';
      c.append(btn, out); c.style.marginBottom = '22px';
      btn.addEventListener('click', e => { e.stopPropagation(); listen(btn, s.dataset.say, out); });
    });
  }

  /* ---------- Velocidad de la voz ---------- */
  const rateIn = $('#voice-rate');
  if (rateIn) {
    rateIn.value = store.get('ip_rate', '1');
    const lbl = $('#voice-rate-val'); const upd = () => { if (lbl) lbl.textContent = (+rateIn.value).toFixed(2).replace(/0$/, '') + '×'; };
    upd();
    rateIn.addEventListener('input', () => { store.set('ip_rate', rateIn.value); upd(); });
    rateIn.addEventListener('change', () => window.ipSay && ipSay('This is how fast I will speak.'));
  }

  /* ---------- Confeti ---------- */
  let canvas, cctx, parts = [], raf = 0;
  const COLORS = ['#c2405a', '#f0a35e', '#ffd27a', '#2bb07b', '#3b7be0', '#8150d8', '#ffffff'];
  function loop() {
    cctx.clearRect(0, 0, canvas.width, canvas.height);
    parts = parts.filter(p => p.life > 0);
    for (const p of parts) {
      p.vy += 0.25; p.vx *= 0.99; p.x += p.vx; p.y += p.vy; p.rot += p.vr; p.life--;
      cctx.save(); cctx.translate(p.x, p.y); cctx.rotate(p.rot); cctx.globalAlpha = Math.min(1, p.life / 40);
      cctx.fillStyle = p.c; if (p.round) { cctx.beginPath(); cctx.arc(0, 0, p.s / 2, 0, 7); cctx.fill(); } else cctx.fillRect(-p.s / 2, -p.s / 4, p.s, p.s / 2);
      cctx.restore();
    }
    raf = parts.length ? requestAnimationFrame(loop) : 0;
  }
  function burst(origin, n = 80, spread = 1) {
    if (reduced) return;
    if (!canvas) { canvas = document.createElement('canvas'); canvas.id = 'confetti'; document.body.append(canvas); cctx = canvas.getContext('2d'); }
    const dpr = devicePixelRatio || 1; canvas.width = innerWidth * dpr; canvas.height = innerHeight * dpr; cctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    let x = innerWidth / 2, y = innerHeight / 3;
    if (origin && origin.getBoundingClientRect) { const r = origin.getBoundingClientRect(); x = r.left + r.width / 2; y = r.top + r.height / 2; }
    for (let i = 0; i < n; i++) {
      const a = Math.random() * Math.PI * 2, v = (3 + Math.random() * 7) * spread;
      parts.push({ x, y, vx: Math.cos(a) * v, vy: Math.sin(a) * v - 5 * spread, s: 6 + Math.random() * 7, c: COLORS[i % COLORS.length], rot: Math.random() * 6, vr: (Math.random() - .5) * .4, life: 90 + Math.random() * 50, round: Math.random() < .3 });
    }
    if (!raf) raf = requestAnimationFrame(loop);
  }
  window.ipConfetti = burst;

  /* ---------- XP y combos ---------- */
  let xp = parseInt(store.get('ip_xp', '0'), 10) || 0, combo = 0;
  const hud = $('.xp-hud'); const xpVal = $('.xp-val'), comboEl = $('.combo');
  const renderXP = () => { if (xpVal) xpVal.textContent = xp.toLocaleString('es-AR') + ' XP'; $$('[data-xp]').forEach(el => el.textContent = xp.toLocaleString('es-AR')); };
  renderXP();
  function addXP(n, el) {
    xp += n; store.set('ip_xp', String(xp)); renderXP();
    if (el && !reduced) {
      const r = el.getBoundingClientRect(); const f = document.createElement('span'); f.className = 'xp-float'; f.textContent = '+' + n + ' XP';
      f.style.left = (r.left + r.width / 2 - 24) + 'px'; f.style.top = (r.top - 8) + 'px'; document.body.append(f); setTimeout(() => f.remove(), 1000);
    }
  }

  /* ---------- Barra de progreso de la unidad ---------- */
  const bar = $('.unit-progress span');
  const setBar = (s, t) => { if (bar && t) bar.style.width = (100 * s / t) + '%'; };
  const progEl = $('[data-progress]');
  if (progEl) { const [s, t] = progEl.textContent.split('/').map(Number); setBar(s, t); }

  /* ---------- Resumen al completar la unidad ---------- */
  const session = { first: 0, answered: 0, revealed: 0, t0: Date.now() };
  document.addEventListener('ip:solution', () => session.revealed++);
  const SK = { listening: ['🎧', 'Listening'], reading: ['📖', 'Reading'], writing: ['✍️', 'Writing'], speaking: ['🗣️', 'Speaking'], grammar: ['🧩', 'Grammar'], vocabulary: ['🔤', 'Vocabulary'] };
  function showSummary() {
    const exs = $$('.exercises .ex'); const by = {};
    exs.forEach(li => { const k = li.dataset.skill || 'grammar'; by[k] ??= [0, 0]; by[k][1]++; if (li.classList.contains('is-solved')) by[k][0]++; });
    const ratio = session.answered ? session.first / session.answered : 1;
    const stars = ratio >= .85 ? 3 : ratio >= .6 ? 2 : 1;
    const mins = Math.max(1, Math.round((Date.now() - session.t0) / 60000));
    const next = $('.unit-end a.btn:not(.btn-ghost)');
    const back = document.createElement('div'); back.className = 'modal-back';
    back.innerHTML = '<div class="modal" role="dialog" aria-modal="true" aria-labelledby="sum-h"><p class="eyebrow">UNIDAD COMPLETADA</p>'
      + '<div class="stars">' + [1, 2, 3].map(i => '<span class="' + (i <= stars ? '' : 'off') + '">⭐</span>').join('') + '</div>'
      + '<h2 id="sum-h">' + (stars === 3 ? '¡Impecable!' : stars === 2 ? '¡Muy buen trabajo!' : '¡Unidad terminada!') + '</h2>'
      + '<p class="muted">' + session.first + ' correctas a la primera · ' + session.revealed + ' soluciones vistas · ~' + mins + ' min en esta sesión</p>'
      + '<div class="sum-grid">' + Object.entries(by).map(([k, [s, t]]) => '<div class="sum-row skill-' + k + '"><span>' + (SK[k] || ['', k]).join(' ') + '</span><span class="meter"><i data-w="' + Math.round(100 * s / t) + '"></i></span><b>' + s + '/' + t + '</b></div>').join('') + '</div>'
      + '<div class="modal-actions">' + (next ? '<a class="btn" href="' + next.getAttribute('href') + '">Siguiente unidad →</a>' : '') + '<a class="btn btn-ghost" href="/stats.php">Ver mis estadísticas</a><button type="button" class="btn btn-ghost" data-close>Seguir acá</button></div></div>';
    document.body.append(back);
    requestAnimationFrame(() => back.querySelectorAll('.meter i').forEach(i => i.style.width = i.dataset.w + '%'));
    const close = () => back.remove();
    back.addEventListener('click', e => { if (e.target === back || e.target.closest('[data-close]')) close(); });
    document.addEventListener('keydown', function k(e) { if (e.key === 'Escape') { close(); document.removeEventListener('keydown', k); } });
    back.querySelector('.btn')?.focus();
  }

  document.addEventListener('ip:answer', ev => {
    const { li, correct, firstSolve, solved, total, demo } = ev.detail;
    if (!demo) { session.answered++; if (ev.detail.firstTry) session.first++; }
    if (correct) {
      combo++;
      play(combo >= 3 ? 'combo' : 'ok');
      if (demo) { burst(li, 60); return; }
      if (firstSolve) addXP(10 + Math.min(combo - 1, 4) * 2, li.querySelector('.feedback') || li);
      if (comboEl) { comboEl.textContent = '🔥 x' + combo; comboEl.classList.toggle('on', combo >= 2); }
      if (combo === 3 || combo === 5 || combo === 10) { burst(li, 40, .7); ipToast(combo === 10 ? '¡Imparable! 10 seguidas 🔥' : combo + ' respuestas seguidas 🔥'); }
      setBar(solved, total);
      if (firstSolve && total && solved === total) {
        setTimeout(() => { play('complete'); burst(null, 220, 1.4); showSummary(); }, 450);
      }
    } else {
      combo = 0; if (comboEl) comboEl.classList.remove('on');
      play('bad');
      if (li && !demo) { li.classList.remove('shake'); void li.offsetWidth; li.classList.add('shake'); }
    }
  });

  const practice = $('#t-practice');
  if (practice) {
    const upd = () => document.body.classList.toggle('practicing', !practice.hidden);
    upd(); new MutationObserver(upd).observe(practice, { attributes: true, attributeFilter: ['hidden'] });
  }

  /* ---------- Atajos de teclado en la práctica ---------- */
  const exs = $$('.exercises .ex');
  if (exs.length) {
    exs.forEach(li => $$('.opt', li).forEach((o, i) => { if (i < 9) { const k = document.createElement('span'); k.className = 'kbd'; k.textContent = i + 1; k.setAttribute('aria-hidden', 'true'); o.prepend(k); } }));
    let active = null;
    exs.forEach(li => { li.addEventListener('pointerenter', () => active = li); li.addEventListener('focusin', () => active = li); });
    const current = () => {
      if (active && !active.closest('[hidden]')) return active;
      const vis = exs.filter(li => { const r = li.getBoundingClientRect(); return r.top < innerHeight * .7 && r.bottom > 80; });
      return vis.find(li => !li.classList.contains('is-solved')) || vis[0];
    };
    document.addEventListener('keydown', e => {
      if (e.target.matches('input, textarea, select') || e.metaKey || e.ctrlKey || e.altKey) return;
      if ($('#t-practice')?.hidden) return;
      const li = current(); if (!li) return;
      if (/^[1-9]$/.test(e.key)) {
        const o = $$('.opt', li)[+e.key - 1]; if (o && !o.disabled) { e.preventDefault(); o.click(); }
      } else if (e.key.toLowerCase() === 'l' || e.key.toLowerCase() === 'r') {
        const s = $('.speak', li); if (s) { e.preventDefault(); s.click(); }
      } else if (e.key === 'n' || e.key === 'ArrowDown') {
        const i = exs.indexOf(li); const nx = exs.slice(i + 1).find(x => !x.classList.contains('is-solved')) || exs[i + 1];
        if (nx) { e.preventDefault(); active = nx; nx.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'center' }); nx.querySelector('input')?.focus({ preventScroll: true }); }
      }
    });
  }

  /* ---------- Saludo en el panel según la hora ---------- */
  const greet = $('[data-greet]');
  if (greet) { const h = new Date().getHours(); greet.textContent = h < 6 ? 'Buenas noches' : h < 13 ? 'Buen día' : h < 20 ? 'Buenas tardes' : 'Buenas noches'; }
})();
