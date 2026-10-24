(() => {
  'use strict';
  const synth = window.speechSynthesis;
  let run = 0;
  const clear = () => document.querySelectorAll('[data-line]').forEach(x => x.classList.remove('is-speaking'));
  function stop() { run++; synth?.cancel(); clear(); }
  document.querySelectorAll('[data-scene]').forEach(scene => {
    const status = scene.querySelector('[data-audio-status]');
    const play = scene.querySelector('[data-scene-play]');
    if (!synth) { play.disabled = true; status.textContent = 'Tu navegador no ofrece audio TTS. La transcripción está disponible.'; }
    scene.querySelector('[data-scene-translation]').addEventListener('change', e => {
      scene.querySelectorAll('[data-es]').forEach(p => p.hidden = !e.target.checked);
    });
    scene.querySelector('[data-scene-stop]').addEventListener('click', () => { stop(); status.textContent = 'Reproducción detenida.'; });
    play.addEventListener('click', () => {
      stop();
      const current = run;
      const lines = [...scene.querySelectorAll('[data-line]')];
      const voices = synth.getVoices();
      const voice = voices.find(v => /^en[-_]US/i.test(v.lang)) || voices.find(v => /^en/i.test(v.lang));
      function next(i) {
        if (current !== run) return;
        clear();
        if (i >= lines.length) { status.textContent = 'Diálogo finalizado. Podés volver a escucharlo.'; return; }
        const utterance = new SpeechSynthesisUtterance(lines[i].querySelector('[data-en]').textContent);
        utterance.lang = voice?.lang || 'en-US';
        if (voice) utterance.voice = voice;
        utterance.rate = +scene.querySelector('[data-scene-rate]').value;
        utterance.onstart = () => { if (current === run) { lines[i].classList.add('is-speaking'); status.textContent = `Escuchando frase ${i+1} de ${lines.length}`; } };
        utterance.onend = () => next(i+1);
        utterance.onerror = () => { if (current === run) { clear(); status.textContent = 'No se pudo reproducir. Revisá las voces en inglés del dispositivo y reintentá.'; } };
        synth.speak(utterance);
      }
      status.textContent = 'Preparando audio…'; next(0);
    });
    scene.querySelectorAll('[data-scene-question]').forEach(form => {
      const built = form.querySelector('[data-built]');
      const pool = form.querySelector('[data-pool]');
      form.querySelectorAll('[data-word]').forEach(word => word.addEventListener('click', () => (word.parentElement === pool ? built : pool).append(word)));
      form.addEventListener('submit', async e => {
        e.preventDefault();
        const result = form.querySelector('[data-result]');
        const button = form.querySelector('[type="submit"]');
        const value = new FormData(form).get('answer');
        const answer = built ? [...built.children].map(x=>x.textContent).join(' ') : form.querySelector('[type="radio"]') ? Number(value) : value;
        if (built && pool.children.length) { result.textContent = 'Usá todas las palabras antes de comprobar.'; return; }
        button.disabled = true;
        try {
          const response = await fetch('/api/scene.php', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':document.querySelector('meta[name="csrf"]').content},body:JSON.stringify({scene:scene.dataset.scene,question:Number(form.dataset.sceneQuestion),answer})});
          const data = await response.json().catch(()=>{throw new Error('No pudimos guardar la respuesta. Recargá la página y reintentá.');});
          if (!response.ok) throw new Error(data.error || 'No pudimos guardar tu respuesta.');
          result.textContent = (data.correct ? '✓ ¡Correcto! ' : 'Todavía no. Volvé a escuchar y reintentá. ') + data.explanation;
          result.className = data.correct ? 'feedback is-ok' : 'feedback is-bad';
          scene.querySelector('[data-scene-score]').textContent = `${data.solved}/${data.total} · ${data.score}/100 puntos`;
          scene.querySelector('[data-scene-progress]').value = data.solved;
        } catch (error) { result.textContent = error.message; }
        finally { button.disabled = false; }
      });
    });
  });
  document.addEventListener('click', e => { if (e.target.closest('.speak,[role="tab"],[data-goto]')) stop(); }, true);
  window.addEventListener('pagehide', stop);
})();
