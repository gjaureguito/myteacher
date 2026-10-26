(()=>{'use strict';
const root=document.querySelector('.tutor-page');if(!root)return;
const form=document.querySelector('#tutor-form'),input=document.querySelector('#tutor-question'),chat=document.querySelector('#tutor-chat'),info=document.querySelector('#tutor-remaining'),button=form.querySelector('button[type=submit]');
const sayButton=document.querySelector('#tutor-say'),micButton=document.querySelector('#tutor-mic');
const voiceToggle=document.querySelector('#tutor-auto-voice');
let lastAnswer='',recognition=null;
function bubble(text,kind){const p=document.createElement('p');p.className='tutor-bubble '+kind;p.textContent=text;chat.append(p);chat.scrollTop=chat.scrollHeight;return p;}
function speak(t){if(!window.speechSynthesis){info.textContent='Este navegador no permite reproducir voz.';return;}speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(t);u.lang=/[¿¡áéíóúñ]/i.test(t)?'es-AR':'en-US';u.rate=.9;speechSynthesis.speak(u);}
document.querySelectorAll('[data-tutor-prompt]').forEach(b=>b.addEventListener('click',()=>{input.value=b.dataset.tutorPrompt;input.focus();}));
if(sayButton)sayButton.addEventListener('click',()=>{if(lastAnswer)speak(lastAnswer);else info.textContent='Primero hacé una pregunta.';});
if(micButton)micButton.addEventListener('click',async()=>{
 const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
 if(!SR){info.textContent='El navegador no admite reconocimiento de voz. Podés escribir tu consulta.';return;}
 if(recognition){try{recognition.stop();}catch{}recognition=null;return;}
 if(navigator.mediaDevices?.getUserMedia){try{const test=await navigator.mediaDevices.getUserMedia({audio:true});test.getTracks().forEach(track=>track.stop());}catch(e){info.textContent='No se pudo abrir el micrófono ('+(e.name||'Error')+'). Esta falla es del acceso de Chrome al audio, antes de Gemini. Usá la pregunta escrita mientras lo resolvemos.';return;}}
 const r=new SR();recognition=r;r.lang=document.querySelector('#tutor-language')?.value||'es-AR';r.interimResults=false;r.continuous=false;
 micButton.textContent='Detener';info.textContent='Escuchando...';
 r.onresult=e=>{input.value=(e.results[0]?.[0]?.transcript||'').trim();info.textContent='Pregunta capturada. Consultando a Gemini…';if(input.value.length>=3)form.requestSubmit();};;
 r.onerror=e=>{const code=e.error||'desconocido';info.textContent=code==='audio-capture'?'Chrome no puede abrir el micrófono (audio-capture). Revisá permisos, programas de seguridad y el dispositivo; podés preguntar por escrito.':code==='not-allowed'?'El navegador bloqueó el micrófono. Autorizá este sitio y reintentá.':'No se pudo reconocer la voz ('+code+'). Probá otra vez o escribí la pregunta.';};;
 r.onend=()=>{recognition=null;micButton.textContent='🎙️ Dictar pregunta';};
 try{r.start();}catch{recognition=null;micButton.textContent='🎙️ Dictar pregunta';info.textContent='No se pudo iniciar la captura de voz.';}
});
form.addEventListener('submit',async e=>{
 e.preventDefault();const question=input.value.trim();if(question.length<3)return;
 button.disabled=true;bubble(question,'tutor-user');const pending=bubble('Preparando una explicación...','tutor-bot');
 try{
  const response=await fetch('/api/tutor.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':document.querySelector('meta[name=csrf]')?.content||''},body:JSON.stringify({question,unit_id:Number(form.dataset.unit||0)})});
  const result=await response.json();
  pending.textContent=result.answer||result.error||'Sin respuesta.';
  if(typeof result.remaining==='number')info.textContent='Consultas disponibles hoy: '+result.remaining+(result.limit?' de '+result.limit:'');
  if(response.ok){lastAnswer=result.answer||'';input.value='';if(voiceToggle?.checked)speak(lastAnswer);}
 }catch{pending.textContent='No se pudo conectar. Probá de nuevo.';}
 finally{button.disabled=false;}
});
})();