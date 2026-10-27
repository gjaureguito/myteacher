/*
 * Motor de voz de MyTeacher.
 * Reemplaza el reconocimiento de voz del navegador (webkitSpeechRecognition), que en Chrome para Windows
 * solo usa el micrófono predeterminado y falla con "audio-capture".
 *
 * 1. Busca solo un micrófono que funcione: el que anduvo la última vez, el predeterminado con y sin
 *    procesamiento y cada micrófono conectado. Descarta los que abren pero no entregan sonido.
 * 2. Graba, detecta cuándo empezás y cuándo terminás de hablar, y corta solo.
 * 3. Arma un WAV de 16 kHz mono y lo transcribe en el servidor (/api/transcribe.php).
 *
 * API: window.ipVoice.recognize(target, { onStart, onSearching, onLevel, onProcessing, onResult, onFail, onEnd, lang, fallback })
 */
(() => {
  'use strict';

  const SAVED_MIC = 'mt_mic_id';
  const Ctx = window.AudioContext || window.webkitAudioContext;
  const supported = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia && Ctx);
  const wait = ms => new Promise(resolve => setTimeout(resolve, ms));
  const store = {
    get: k => { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch (e) { /* opcional */ } },
    del: k => { try { localStorage.removeItem(k); } catch (e) { /* opcional */ } },
  };
  const csrf = () => document.querySelector('meta[name=csrf]')?.content || '';

  const MESSAGES = {
    denied: 'Para escucharte necesitamos permiso para usar el micrófono. Tocá «Permitir» cuando el navegador lo pregunte. Si antes elegiste «Bloquear», tocá el candado junto a la dirección del sitio y cambialo a «Permitir».',
    nomic: 'No encontramos un micrófono que funcione en este equipo. Podés seguir igual: escuchá la frase y repetila en voz alta.',
    blocked: 'Tu computadora no está dejando que el navegador use el micrófono. Seguí los pasos del recuadro y te avisamos cuando quede listo.',
    nospeech: 'No te escuchamos. Tocá el botón y hablá cerca del micrófono.',
    server: 'No pudimos procesar tu voz en este momento. Probá de nuevo en unos segundos.',
    quota: 'Llegaste al límite de prácticas con voz por hoy. Mañana podés seguir.',
    login: 'Ingresá a tu cuenta para practicar con tu voz.',
    unsupported: 'Este navegador no permite grabar audio. Podés seguir escuchando y repitiendo en voz alta.',
  };

  class VoiceError extends Error {
    constructor(code, details) { super(MESSAGES[code] || MESSAGES.server); this.code = code; this.details = details || []; }
  }

  /* ---------- 1. Encontrar un micrófono que funcione ---------- */
  const RAW = { echoCancellation: false, noiseSuppression: false, autoGainControl: false };

  async function hasSignal(stream) {
    // Un micrófono "abierto" pero muerto entrega ceros exactos; uno real siempre tiene algo de ruido.
    const track = stream.getAudioTracks()[0];
    if (!track || track.readyState !== 'live') return false;
    let ctx;
    try {
      ctx = new Ctx();
      if (ctx.state === 'suspended') await ctx.resume().catch(() => {});
      const src = ctx.createMediaStreamSource(stream);
      const an = ctx.createAnalyser();
      an.fftSize = 2048;
      src.connect(an);
      const buf = new Float32Array(an.fftSize);
      const t0 = performance.now();
      while (performance.now() - t0 < 600) {
        await wait(60);
        an.getFloatTimeDomainData(buf);
        for (let i = 0; i < buf.length; i++) if (buf[i] !== 0) return true;
      }
      return false;
    } catch (e) {
      return true; // si no se puede medir, no descartamos el micrófono
    } finally {
      ctx?.close().catch(() => {});
    }
  }

  function remember(stream) {
    const id = stream.getAudioTracks()[0]?.getSettings?.().deviceId;
    if (id && id !== 'default' && id !== 'communications') store.set(SAVED_MIC, id);
  }

  async function permissionDenied() {
    try { return (await navigator.permissions.query({ name: 'microphone' })).state === 'denied'; }
    catch (e) { return false; } // Safari/Firefox antiguos no lo informan
  }

  async function openMic({ onSearching } = {}) {
    if (!supported) throw new VoiceError('unsupported');
    if (await permissionDenied()) throw new VoiceError('denied');
    const saved = store.get(SAVED_MIC);
    let denied = false, tries = 0;
    const errors = [];

    const attempt = async audio => {
      if (++tries === 2 && onSearching) onSearching(); // el primero falló: avisamos que seguimos buscando
      let stream;
      try {
        stream = await navigator.mediaDevices.getUserMedia({ audio });
      } catch (e) {
        errors.push(e && e.name || 'Error');
        if (e && (e.name === 'NotAllowedError' || e.name === 'SecurityError')) denied = true;
        if (e && (e.name === 'NotReadableError' || e.name === 'AbortError')) await wait(350); // Windows libera el dispositivo con demora
        return null;
      }
      if (await hasSignal(stream)) return stream;
      errors.push('Silent');
      stream.getTracks().forEach(t => t.stop());
      return null;
    };
    const win = stream => { remember(stream); return stream; };

    const first = [saved ? { deviceId: { exact: saved } } : null, true, RAW].filter(Boolean);
    for (const c of first) {
      const s = await attempt(c);
      if (s) return win(s);
      if (denied) break;
    }

    if (!denied) {
      let inputs = [];
      try {
        inputs = (await navigator.mediaDevices.enumerateDevices())
          .filter(d => d.kind === 'audioinput' && d.deviceId && !['default', 'communications', saved].includes(d.deviceId));
      } catch (e) { /* sin lista de dispositivos */ }
      for (const d of inputs) {
        for (const c of [{ deviceId: { exact: d.deviceId } }, { deviceId: { exact: d.deviceId }, ...RAW }]) {
          const s = await attempt(c);
          if (s) return win(s);
          if (denied) break;
        }
        if (denied) break;
      }
      if (!denied) {
        await wait(1200); // último intento: a veces el audio de Windows tarda en volver
        const s = await attempt(true);
        if (s) return win(s);
      }
    }

    if (saved) store.del(SAVED_MIC);
    if (denied || await permissionDenied()) throw new VoiceError('denied', errors);
    // Permiso del sitio concedido, pero el sistema no deja abrir ningún micrófono (privacidad de Windows o
    // un programa que lo tiene tomado): el caso que más se repite en Chrome para Windows.
    const sysBlocked = errors.length > 0 && errors.every(n => n === 'NotReadableError' || n === 'AbortError');
    throw new VoiceError(sysBlocked ? 'blocked' : 'nomic', errors);
  }

  /* ---------- 2. Grabar y cortar solo ---------- */
  function capture(stream, { onLevel, onSpeech, maxMs = 10000, startTimeoutMs = 7000, silenceMs = 1100 } = {}) {
    let stopRequested = false;
    const promise = (async () => {
      const ctx = new Ctx();
      if (ctx.state === 'suspended') await ctx.resume().catch(() => {});
      const src = ctx.createMediaStreamSource(stream);
      const proc = ctx.createScriptProcessor(4096, 1, 1);
      const mute = ctx.createGain();
      mute.gain.value = 0;
      src.connect(proc); proc.connect(mute); mute.connect(ctx.destination);

      return new Promise(resolve => {
        const chunks = [];
        const preroll = [];
        let started = false, done = false, frames = 0, noise = 0, lastVoice = 0;
        const t0 = performance.now();
        const finish = reason => {
          if (done) return;
          done = true;
          proc.onaudioprocess = null;
          try { src.disconnect(); proc.disconnect(); mute.disconnect(); } catch (e) { /* ya desconectado */ }
          stream.getTracks().forEach(t => t.stop());
          const rate = ctx.sampleRate;
          ctx.close().catch(() => {});
          resolve({ reason, rate, chunks, spoke: started });
        };
        proc.onaudioprocess = ev => {
          const data = new Float32Array(ev.inputBuffer.getChannelData(0));
          let sum = 0;
          for (let i = 0; i < data.length; i++) sum += data[i] * data[i];
          const rms = Math.sqrt(sum / data.length);
          const now = performance.now();
          frames++;
          if (frames <= 3) noise = Math.max(noise, Math.min(rms, 0.03)); // ruido de fondo de la habitación
          const threshold = Math.max(0.012, noise * 2.5);
          onLevel && onLevel(Math.min(1, rms / Math.max(threshold * 4, 0.05)));

          if (!started) {
            preroll.push(data);
            if (preroll.length > 4) preroll.shift();
            if (frames > 3 && rms > threshold) {
              started = true;
              lastVoice = now;
              chunks.push(...preroll);
              onSpeech && onSpeech();
            }
          } else {
            chunks.push(data);
            if (rms > threshold * 0.7) lastVoice = now;
          }

          if (stopRequested) finish('manual');
          else if (started && now - lastVoice > silenceMs) finish('silence');
          else if (now - t0 > maxMs) finish('max');
          else if (!started && now - t0 > startTimeoutMs) finish('nospeech');
        };
      });
    })();
    return { promise, stop: () => { stopRequested = true; } };
  }

  /* ---------- 3. WAV de 16 kHz mono ---------- */
  function toWav(chunks, rate, outRate = 16000) {
    let length = 0;
    chunks.forEach(c => { length += c.length; });
    const input = new Float32Array(length);
    let offset = 0;
    chunks.forEach(c => { input.set(c, offset); offset += c.length; });

    const ratio = rate / outRate;
    const outLength = Math.floor(input.length / ratio);
    const out = new Float32Array(outLength);
    let peak = 0;
    for (let i = 0; i < outLength; i++) {
      const start = Math.floor(i * ratio), end = Math.min(input.length, Math.floor((i + 1) * ratio));
      let acc = 0;
      for (let j = start; j < end; j++) acc += input[j];
      out[i] = end > start ? acc / (end - start) : 0;
      peak = Math.max(peak, Math.abs(out[i]));
    }
    const gain = peak > 0 && peak < 0.5 ? Math.min(8, 0.9 / peak) : 1; // micrófonos muy bajos
    const buffer = new ArrayBuffer(44 + outLength * 2);
    const v = new DataView(buffer);
    const str = (o, s) => { for (let i = 0; i < s.length; i++) v.setUint8(o + i, s.charCodeAt(i)); };
    str(0, 'RIFF'); v.setUint32(4, 36 + outLength * 2, true); str(8, 'WAVE');
    str(12, 'fmt '); v.setUint32(16, 16, true); v.setUint16(20, 1, true); v.setUint16(22, 1, true);
    v.setUint32(24, outRate, true); v.setUint32(28, outRate * 2, true); v.setUint16(32, 2, true); v.setUint16(34, 16, true);
    str(36, 'data'); v.setUint32(40, outLength * 2, true);
    for (let i = 0; i < outLength; i++) {
      const s = Math.max(-1, Math.min(1, out[i] * gain));
      v.setInt16(44 + i * 2, s < 0 ? s * 0x8000 : s * 0x7fff, true);
    }
    return { blob: new Blob([buffer], { type: 'audio/wav' }), seconds: outLength / outRate };
  }

  /* ---------- 4. Transcribir en el servidor ---------- */
  async function transcribe(blob, lang) {
    for (let attempt = 0; attempt < 2; attempt++) {
      const fd = new FormData();
      fd.append('audio', blob, 'voz.wav');
      fd.append('lang', lang || 'en-US');
      let res;
      try {
        res = await fetch('/api/transcribe.php', { method: 'POST', headers: { 'X-CSRF-Token': csrf() }, body: fd, credentials: 'same-origin' });
      } catch (e) {
        if (attempt === 0) { await wait(900); continue; }
        throw new VoiceError('server');
      }
      const data = await res.json().catch(() => ({}));
      if (res.ok) return String(data.text || '').trim();
      if (res.status === 401) throw new VoiceError('login');
      if (res.status === 429) throw new VoiceError('quota');
      if (res.status === 503 && data.code === 'not_configured') throw new VoiceError('not_configured');
      if (res.status >= 500 && attempt === 0) { await wait(900); continue; }
      throw new VoiceError('server');
    }
    throw new VoiceError('server');
  }

  /* ---------- Rescate: el sistema bloquea el micrófono ---------- */
  const isWindows = /Windows/.test(navigator.userAgent);
  const isMac = /Macintosh/.test(navigator.userAgent) && !/Mobile/.test(navigator.userAgent);
  const browser = /Edg\//.test(navigator.userAgent) ? 'Edge' : /Firefox\//.test(navigator.userAgent) ? 'Firefox' : 'Chrome';
  let rescue = null;

  function report(code, details) {
    try {
      const body = JSON.stringify({ code, details: (details || []).slice(0, 12), page: location.pathname });
      navigator.sendBeacon?.('/api/voice_log.php', new Blob([body], { type: 'application/json' }));
    } catch (e) { /* el registro es opcional */ }
  }

  function el(tag, attrs, ...children) {
    const n = document.createElement(tag);
    Object.entries(attrs || {}).forEach(([k, v]) => { if (k === 'class') n.className = v; else n.setAttribute(k, v); });
    children.forEach(c => n.append(c));
    return n;
  }

  function showRescue() {
    if (rescue) { rescue.box.hidden = false; rescue.watch(); return; }
    const steps = isWindows ? [
      'Tocá «Descargar el reparador». Si el navegador pregunta si querés conservar el archivo, elegí «Conservar».',
      'Abrí el archivo descargado. Si aparece «Windows protegió tu PC», tocá «Más información» y después «Ejecutar de todas formas».',
      'Seguí los mensajes y tocá «Sí». Tarda menos de un minuto.',
      'Cerrá el navegador, abrilo de nuevo y volvé a esta página.',
    ] : isMac ? [
      'Tocá el botón azul. Se abre Privacidad y seguridad › Micrófono.',
      'Activá el interruptor de ' + browser + '.',
      'Volvé a esta página: te avisamos solos cuando funcione.',
    ] : [
      'Revisá que el micrófono esté conectado y que ningún otro programa lo esté usando.',
      'Volvé a esta página: te avisamos solos cuando funcione.',
    ];
    const settingsUrl = isMac ? 'x-apple.systempreferences:com.apple.preference.security?Privacy_Microphone' : '';

    const status = el('p', { class: 'mtv-status', role: 'status' }, 'Esperando que habilites el micrófono…');
    const close = el('button', { type: 'button', class: 'mtv-close', 'aria-label': 'Cerrar' }, '×');
    const list = el('ol', {}, ...steps.map(t => el('li', {}, t)));
    const actions = el('div', { class: 'mtv-actions' });
    if (isWindows) {
      actions.append(
        el('a', { class: 'mtv-btn', href: '/descargas/Activar-microfono-MyTeacher.cmd', download: 'Activar-microfono-MyTeacher.cmd' }, 'Descargar el reparador'),
        el('a', { class: 'mtv-link', href: 'ms-settings:privacy-microphone' }, 'Prefiero hacerlo a mano'));
    } else if (settingsUrl) {
      actions.append(el('a', { class: 'mtv-btn', href: settingsUrl }, 'Abrir el ajuste del Mac'));
    }
    const tip = el('div', { class: 'mtv-tip' },
      el('p', {}, el('b', {}, '¿Sigue sin funcionar? '), 'Tu antivirus puede estar bloqueando el micrófono' + (isWindows ? ' (el reparador te dice cuál tenés)' : '') + '. Abrí el antivirus (Kaspersky, ESET, Avast, AVG, Norton, Bitdefender, McAfee…), buscá «micrófono» y permití que ' + browser + ' lo use.'),
      el('p', {}, 'Si tenés abierto Zoom, Teams, Meet o Discord, cerralos: a veces se quedan con el micrófono.'));
    const box = el('section', { class: 'mtv-rescue', role: 'dialog', 'aria-live': 'polite', 'aria-label': 'Activar el micrófono' },
      close, el('h3', {}, isWindows ? 'Reparemos el micrófono de tu computadora' : 'Activá el micrófono de tu computadora'), list, actions, tip, status);
    document.body.append(box);

    let timer = null, until = 0, checking = false;
    const check = async () => {
      if (checking || box.hidden) return;
      checking = true;
      try {
        const s = await navigator.mediaDevices.getUserMedia({ audio: true });
        const ok = await hasSignal(s);
        if (ok) remember(s);
        s.getTracks().forEach(t => t.stop());
        if (ok) {
          stopWatch();
          box.classList.add('is-ok');
          status.textContent = '¡Listo! Ya te escucho. Tocá «Decilo vos» y hablá.';
          report('rescued');
          window.dispatchEvent(new CustomEvent('ipvoice:ready'));
          setTimeout(() => { box.hidden = true; box.classList.remove('is-ok'); }, 6000);
        }
      } catch (e) { /* todavía bloqueado */ }
      finally { checking = false; }
      if (timer && Date.now() > until) { stopWatch(); status.textContent = 'Cuando lo actives, volvé a tocar «Decilo vos».'; }
    };
    const stopWatch = () => { clearInterval(timer); timer = null; };
    const watch = () => {
      until = Date.now() + 5 * 60 * 1000;
      status.textContent = 'Esperando que habilites el micrófono…';
      if (!timer) timer = setInterval(check, 2500);
    };
    document.addEventListener('visibilitychange', () => { if (!document.hidden) check(); });
    window.addEventListener('focus', check);
    close.addEventListener('click', () => { box.hidden = true; stopWatch(); });
    rescue = { box, watch };
    watch();
  }

  /* ---------- API pública ---------- */
  let current = null;

  async function recognize(target, { onStart, onSearching, onLevel, onProcessing, onResult, onFail, onEnd, lang = 'en-US', fallback } = {}) {
    if (current) current.stop();
    if (window.speechSynthesis) speechSynthesis.cancel(); // que el micrófono no escuche la voz del sitio
    let session = null;
    const handle = { stop: () => session?.stop() };
    current = handle;
    try {
      const stream = await openMic({ onSearching });
      if (current !== handle) { stream.getTracks().forEach(t => t.stop()); return; }
      onStart && onStart();
      session = capture(stream, { onLevel });
      const rec = await session.promise;
      onLevel && onLevel(0);
      if (!rec.spoke) throw new VoiceError('nospeech');
      const wav = toWav(rec.chunks, rec.rate);
      if (wav.seconds < 0.3) throw new VoiceError('nospeech');
      onProcessing && onProcessing();
      const text = await transcribe(wav.blob, lang);
      if (!text) throw new VoiceError('nospeech');
      onResult && onResult([text]);
    } catch (err) {
      const code = err instanceof VoiceError ? err.code : 'server';
      if ((code === 'login' || code === 'not_configured') && typeof fallback === 'function') {
        fallback(target, { onStart, onResult, onFail, onEnd }); // sin cuenta o sin servidor: reconocimiento del navegador
        if (current === handle) current = null;
        return;
      }
      if (code === 'blocked' || code === 'nomic' || code === 'denied' || code === 'server') report(code, err.details || [err.name]);
      if (code === 'blocked') showRescue();
      onFail && onFail(err instanceof VoiceError ? err.message : MESSAGES.server, code);
    } finally {
      if (current === handle) current = null;
    }
    onEnd && onEnd();
  }

  window.ipVoice = {
    supported,
    recognize,
    stop: () => current?.stop(),
    openMic,
    toWav,
    messages: MESSAGES,
    showRescue,
  };
})();
