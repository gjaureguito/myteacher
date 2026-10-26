(() => {
  'use strict';
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  document.querySelectorAll('.scene[data-scene]').forEach(scene => {
    const lines = [...scene.querySelectorAll('.scene-transcript [data-line]')];
    if (!lines.length) return;
    const container = document.createElement('section');
    container.className = 'shadow-lab';
    container.setAttribute('aria-label','Laboratorio de conversación');
    container.innerHTML = '<h3>🎙️ Repetí el diálogo</h3><p>Escuchá una frase y repetila en inglés. Podés mostrar u ocultar el texto para practicar memoria auditiva.</p><div class="shadow-controls"><button type="button" data-shadow="prev">← Frase anterior</button><button type="button" data-shadow="listen">🔊 Escuchar</button><button type="button" data-shadow="record">🎤 Practicar pronunciación</button><button type="button" data-shadow="next">Siguiente →</button><label><input type="checkbox" data-shadow="hide"> Ocultar texto inglés</label></div><p class="shadow-position" aria-live="polite"></p><p class="shadow-reference" lang="en"></p><p class="shadow-result" role="status" aria-live="polite"></p><p class="shadow-note">El reconocimiento de voz depende del navegador y puede utilizar su servicio de procesamiento de voz. No se envía una grabación al servidor de MyTeacher mediante este ejercicio.</p>';
    const reference = scene.querySelector('.scene-transcript');
    reference.after(container);
    const $ = name => container.querySelector('[data-shadow="'+name+'"]');
    let current=0,rec=null;
    const normalize=s=>(s||'').toLocaleLowerCase('en').replace(/[^a-z0-9\s']/g,' ').replace(/\s+/g,' ').trim();
    const words=s=>normalize(s).split(' ').filter(Boolean);
    const clamp=n=>Math.max(0,Math.min(lines.length-1,n));
    function refresh(){
      const line=lines[current];
      const text=line.querySelector('[data-en]')?.textContent?.trim()||'';
      container.querySelector('.shadow-position').textContent='Frase '+(current+1)+' de '+lines.length+' · '+(line.querySelector('strong')?.textContent||'');
      const hidden=$('hide').checked;
      container.querySelector('.shadow-reference').textContent=hidden?'Texto oculto. Podés escucharlo o desmarcar la casilla.':text;
      $('prev').disabled=current===0;
      $('next').disabled=current===lines.length-1;
      container.querySelector('.shadow-result').textContent='';
    }
    function stopRecognition(){
      if(rec){try{rec.abort();}catch{}rec=null;}
      $('record').disabled=false;
    }
    function move(n){stopRecognition();current=clamp(current+n);refresh();}
    $('prev').addEventListener('click',()=>move(-1));
    $('next').addEventListener('click',()=>move(1));
    $('hide').addEventListener('change',refresh);
    $('listen').addEventListener('click',()=>{
      const button=lines[current].querySelector('.speak');
      if(button)button.click();
      else if(window.speechSynthesis){const u=new SpeechSynthesisUtterance(lines[current].querySelector('[data-en]')?.textContent||'');u.lang='en-US';speechSynthesis.cancel();speechSynthesis.speak(u);}
    });
    if(!SpeechRecognition){$('record').disabled=true;container.querySelector('.shadow-result').textContent='El reconocimiento de voz no está disponible en este navegador. Podés practicar escuchando y repitiendo sin evaluación automática.';}
    $('record').addEventListener('click',()=>{
      if(!SpeechRecognition)return;
      stopRecognition();
      const target=lines[current].querySelector('[data-en]')?.textContent?.trim()||'';
      const expected=words(target);
      const result=container.querySelector('.shadow-result');
      let engine;
      try{
        engine=new SpeechRecognition();rec=engine;engine.lang='en-US';engine.interimResults=false;engine.maxAlternatives=1;
        $('record').disabled=true;result.textContent='Escuchando… pronunciá la frase completa.';
        engine.onresult=e=>{
          if(rec!==engine)return;
          const spoken=e.results[0][0].transcript||'';
          const heard=words(spoken),remaining=[...expected];
          let hits=0;
          heard.forEach(token=>{const i=remaining.indexOf(token);if(i!==-1){hits++;remaining.splice(i,1);}});
          const pct=expected.length?Math.round(100*hits/Math.max(expected.length,heard.length)):0;
          result.textContent='Escuché: “'+spoken+'”. Coincidencia aproximada de palabras: '+pct+'%. '+(pct>=80?'¡Muy bien!':'Escuchá de nuevo y probá otra vez.');
          // Coincidencia de transcripción, no calificación fonética.
        };
        engine.onerror=e=>{if(rec===engine)result.textContent='No se pudo reconocer la voz ('+e.error+'). Revisá el permiso del micrófono y probá nuevamente.';};
        engine.onend=()=>{if(rec===engine){rec=null;$('record').disabled=false;}};
        engine.start();
      }catch{rec=null;$('record').disabled=false;result.textContent='El navegador no pudo iniciar el reconocimiento de voz.';}
    });
    const styleId='shadow-lab-styles';
    if(!document.getElementById(styleId)){
      const css=document.createElement('style');css.id=styleId;
      css.textContent='.shadow-lab{margin:20px 0;padding:16px;border:1px solid #91acc8;border-radius:14px;background:var(--surface,#f7f9fc)}.shadow-lab h3{margin-top:0}.shadow-controls{display:flex;flex-wrap:wrap;align-items:center;gap:8px}.shadow-controls button{padding:10px;border:1px solid #8199b4;border-radius:8px;background:transparent;color:inherit;cursor:pointer}.shadow-controls button:disabled{opacity:.45;cursor:default}.shadow-controls label{display:flex;align-items:center;gap:6px}.shadow-position{font-weight:700}.shadow-reference{font-size:1.2rem;line-height:1.5}.shadow-result{font-weight:600}.shadow-note{font-size:.82rem;opacity:.8}';
      document.head.append(css);
    }
    refresh();
  });
})();