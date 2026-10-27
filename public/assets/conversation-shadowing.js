(() => {
  'use strict';
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  document.querySelectorAll('.scene[data-scene]').forEach(scene => {
    const lines = [...scene.querySelectorAll('.scene-transcript [data-line]')];
    if (!lines.length) return;
    const container = document.createElement('section');
    container.className = 'shadow-lab';
    container.setAttribute('aria-label','Laboratorio de conversación');
    container.innerHTML = '<h3>🎙️ Repetí el diálogo</h3><p>Escuchá una frase y repetila en inglés. Podés mostrar u ocultar el texto para practicar memoria auditiva.</p><div class="shadow-controls"><button type="button" data-shadow="prev">← Frase anterior</button><button type="button" data-shadow="listen">🔊 Escuchar</button><button type="button" data-shadow="record">🎤 Practicar pronunciación</button><button type="button" data-shadow="next">Siguiente →</button><label><input type="checkbox" data-shadow="hide"> Ocultar texto inglés</label></div><p class="shadow-position" aria-live="polite"></p><p class="shadow-reference" lang="en"></p><p class="shadow-result" role="status" aria-live="polite"></p><details class="shadow-help" hidden><summary>¿Problemas con el micrófono?</summary><p data-shadow-help-text></p><button type="button" data-shadow="testmic">Probar micrófono</button><p data-shadow-test-result role="status" aria-live="polite"></p><p>También podés escuchar y repetir por tu cuenta, y seguir con los ejercicios sin usar el micrófono.</p></details><p class="shadow-note">El reconocimiento de voz depende del navegador y puede utilizar su servicio de procesamiento de voz. No se envía una grabación al servidor de MyTeacher mediante este ejercicio.</p>';
    const reference = scene.querySelector('.scene-transcript');
    reference.after(container);
    const $ = name => container.querySelector('[data-shadow="'+name+'"]');
    let current=0,rec=null;
    const help=container.querySelector('.shadow-help');
    const errorAdvice={
      'audio-capture':'El navegador no logra abrir el micrófono. Comprobá el dispositivo de entrada en la configuración del navegador, cerrá aplicaciones de audio y volvé a intentar.',
      'not-allowed':'El navegador no tiene permiso para grabar. Abrí los permisos del sitio desde el ícono junto a la dirección y permití el micrófono.',
      'service-not-allowed':'El servicio de reconocimiento de voz está bloqueado. Podés continuar sin dictado o probar otro navegador compatible.',
      'no-speech':'No se detectó voz. Acercate al micrófono, comprobá que no esté silenciado y probá otra vez.',
      'network':'El servicio de reconocimiento de voz no respondió. Revisá tu conexión y reintentá; podés continuar sin reconocimiento.',
      'language-not-supported':'El idioma de reconocimiento no está disponible en este navegador. Podés practicar escuchando y repitiendo sin evaluación automática.'
    };
    function showHelp(message){help.hidden=false;help.open=true;help.querySelector('[data-shadow-help-text]').textContent=message;}
    $('testmic').addEventListener('click',async()=>{
      const status=help.querySelector('[data-shadow-test-result]');
      if(!navigator.mediaDevices?.getUserMedia){status.textContent='Este navegador no admite la prueba de audio. Podés continuar sin usar el micrófono.';return;}
      status.textContent='Comprobando acceso al micrófono…';
      let stream;
      try{
        stream=await navigator.mediaDevices.getUserMedia({audio:true});
        status.textContent='✓ El navegador puede abrir el micrófono. Si el dictado falla, puede ser el servicio de reconocimiento de voz.';
      }catch(err){
        const names={NotAllowedError:'Permiso denegado: revisá los permisos del sitio.',NotFoundError:'No se encontró un micrófono disponible.',NotReadableError:'El micrófono existe, pero otra aplicación o el controlador podría impedir la captura.',OverconstrainedError:'El dispositivo no admite la configuración solicitada.'};
        status.textContent=names[err.name]||'No se pudo acceder al micrófono ('+err.name+').';
      }finally{stream?.getTracks().forEach(track=>track.stop());}
    });
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
      if(rec){window.ipVoice?.stop();try{rec.abort?.();}catch{}rec=null;}
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
    // Este script carga antes que el motor de voz, así que se consulta al tocar el botón.
    const canListen=()=>!!(window.ipRecognize&&(window.ipVoice?.supported||SpeechRecognition));
    if(!navigator.mediaDevices?.getUserMedia&&!SpeechRecognition){$('record').disabled=true;container.querySelector('.shadow-result').textContent='Este navegador no permite grabar audio. Podés escuchar y repetir en voz alta.';}
    $('record').addEventListener('click',()=>{
      if(!canListen())return;
      if(rec){window.ipVoice?.stop();return;}
      const target=lines[current].querySelector('[data-en]')?.textContent?.trim()||'';
      const expected=words(target);
      const result=container.querySelector('.shadow-result');
      const button=$('record');
      rec={};
      result.textContent='Preparando el micrófono…';
      window.ipRecognize(target,{
        onStart:()=>{result.textContent='Te escucho… pronunciá la frase completa. Se corta solo cuando terminás.';},
        onSearching:()=>{result.textContent='Buscando un micrófono que funcione…';},
        onProcessing:()=>{button.disabled=true;result.textContent='Analizando tu pronunciación…';},
        onResult:alts=>{
          const spoken=alts[0]||'';
          const heard=words(spoken),remaining=[...expected];
          let hits=0;
          heard.forEach(token=>{const i=remaining.indexOf(token);if(i!==-1){hits++;remaining.splice(i,1);}});
          const pct=expected.length?Math.round(100*hits/Math.max(expected.length,heard.length)):0;
          result.textContent='Escuché: “'+spoken+'”. Coincidencia aproximada de palabras: '+pct+'%. '+(pct>=80?'¡Muy bien!':'Escuchá de nuevo y probá otra vez.');
          // Coincidencia de transcripción, no calificación fonética.
        },
        onFail:msg=>{result.textContent=msg;},
        onEnd:()=>{rec=null;button.disabled=false;},
      });
    });
    const styleId='shadow-lab-styles';
    if(!document.getElementById(styleId)){
      const css=document.createElement('style');css.id=styleId;
      css.textContent='.shadow-lab{margin:20px 0;padding:16px;border:1px solid #91acc8;border-radius:14px;background:var(--surface,#f7f9fc)}.shadow-lab h3{margin-top:0}.shadow-controls{display:flex;flex-wrap:wrap;align-items:center;gap:8px}.shadow-controls button{padding:10px;border:1px solid #8199b4;border-radius:8px;background:transparent;color:inherit;cursor:pointer}.shadow-controls button:disabled{opacity:.45;cursor:default}.shadow-controls label{display:flex;align-items:center;gap:6px}.shadow-position{font-weight:700}.shadow-reference{font-size:1.2rem;line-height:1.5}.shadow-result{font-weight:600}.shadow-note{font-size:.82rem;opacity:.8}';
      document.head.append(css);
    }
    refresh();
    const hintStyle=document.createElement('style');hintStyle.textContent='.shadow-help{margin:12px 0;padding:12px;border:1px solid #9ab7aa;border-radius:10px}.shadow-help summary{cursor:pointer;font-weight:700}.shadow-help p{margin:8px 0}.shadow-help button{padding:8px 12px;border-radius:8px;border:1px solid #8199b4;background:transparent;color:inherit;cursor:pointer}';container.append(hintStyle);
  });
})();