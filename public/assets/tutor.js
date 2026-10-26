(()=>{
'use strict';
const form=document.querySelector('#tutor-form');if(!form)return;
const input=document.querySelector('#tutor-question'),chat=document.querySelector('#tutor-chat'),info=document.querySelector('#tutor-remaining'),sendBtn=form.querySelector('[type=submit]'),micBtn=document.querySelector('#tutor-mic'),sayBtn=document.querySelector('#tutor-say'),lang=document.querySelector('#tutor-language'),voiceToggle=document.querySelector('#tutor-auto-voice');
let active=false,recognition=null,speaking=null,busy=false,lastAnswer='',turns=0;
const maxTurns=15;
function message(text,who){const p=document.createElement('p');p.className='tutor-bubble '+(who||'tutor-bot');p.textContent=text;chat.append(p);chat.scrollTop=chat.scrollHeight;return p;}
function setState(text){info.textContent=text;micBtn.textContent=active?'■ Terminar conversación':'🎙️ Iniciar conversación';micBtn.setAttribute('aria-pressed',String(active));}
function stop(){active=false;if(recognition){const r=recognition;recognition=null;try{r.abort();}catch{}}if(speaking){speaking.onend=null;speaking.onerror=null;speaking=null;}if(window.speechSynthesis)speechSynthesis.cancel();setState('Conversación detenida. Podés escribir una consulta si lo preferís.');}
function speak(text,continueAfter){
 if(!window.speechSynthesis){setState('Tu navegador no permite reproducir la voz.');if(continueAfter)stop();return;}
 speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(text);speaking=u;u.lang=lang.value==='en-US'?'en-US':'es-AR';u.rate=.92;
 u.onend=()=>{if(speaking!==u)return;speaking=null;if(continueAfter&&active)listen();};
 u.onerror=()=>{if(speaking!==u)return;speaking=null;if(continueAfter)stop();};
 speechSynthesis.speak(u);
}
async function ask(question,fromVoice){
 if(busy)return;busy=true;sendBtn.disabled=true;message(question,'tutor-user');const pending=message('El profesor está pensando…');
 try{
  const res=await fetch('/api/tutor.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':document.querySelector('meta[name=csrf]')?.content||''},body:JSON.stringify({question,unit_id:Number(form.dataset.unit||0)})});
  const data=await res.json();pending.textContent=data.answer||data.error||'No se recibió una respuesta.';
  if(typeof data.remaining==='number')info.textContent='Consultas restantes hoy: '+data.remaining+(data.limit?' / '+data.limit:'');
  if(!res.ok){if(fromVoice)stop();return;}
  lastAnswer=data.answer||'';turns++;input.value='';
  if(fromVoice&&active){
   if(turns>=maxTurns){stop();info.textContent='Terminó la sesión de práctica (máximo '+maxTurns+' intercambios).';speak(lastAnswer,false);}
   else speak(lastAnswer,true);
  }else if(voiceToggle?.checked)speak(lastAnswer,false);
 }catch{pending.textContent='No fue posible conectar con Gemini.';if(fromVoice)stop();}
 finally{busy=false;sendBtn.disabled=false;}
}
function listen(){
 if(!active||busy||recognition)return;
 const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
 if(!SR){stop();info.textContent='Este navegador no admite reconocimiento de voz.';return;}
 const r=new SR();recognition=r;r.lang=lang.value||'es-AR';r.continuous=false;r.interimResults=false;
 let received=false,failed=false;
 r.onresult=e=>{const question=(e.results[0]?.[0]?.transcript||'').trim();received=true;if(question){setState('Consulta reconocida. Esperando a Gemini…');ask(question,true);}};
 r.onerror=e=>{failed=true;const code=e.error||'error';stop();info.textContent='No se pudo captar el micrófono ('+code+'). El modo oral se detuvo. Podés usar el chat escrito.';};
 r.onend=()=>{if(recognition===r)recognition=null;if(active&&!received&&!failed){setState('No se escuchó ninguna frase. Intentá hablar de nuevo.');stop();}};
 try{r.start();setState('Escuchando en '+(lang.value==='en-US'?'inglés':'español')+'… Hablá ahora.');}catch(e){recognition=null;stop();info.textContent='No se pudo iniciar la escucha ('+(e.name||'Error')+').';}
}
micBtn.addEventListener('click',async()=>{
 if(active){stop();return;}
 if(!navigator.mediaDevices?.getUserMedia){info.textContent='El navegador no admite captura de audio.';return;}
 micBtn.disabled=true;
 try{const stream=await navigator.mediaDevices.getUserMedia({audio:true});stream.getTracks().forEach(t=>t.stop());active=true;turns=0;setState('Conversación iniciada. Preparando escucha…');listen();}
 catch(e){info.textContent='Chrome no puede abrir el micrófono: '+(e.name||'Error')+'. Revisá la protección de privacidad o usá texto.';}
 finally{micBtn.disabled=false;}
});
sayBtn.addEventListener('click',()=>{if(lastAnswer)speak(lastAnswer,false);else info.textContent='Primero necesitás una respuesta del profesor.';});
document.querySelectorAll('[data-tutor-prompt]').forEach(b=>b.addEventListener('click',()=>{input.value=b.dataset.tutorPrompt;input.focus();}));
form.addEventListener('submit',e=>{e.preventDefault();const q=input.value.trim();if(q.length>=3)ask(q,false);});
window.addEventListener('pagehide',stop);
})();