(()=>{
'use strict';
const form=document.querySelector('#tutor-form');if(!form)return;
const input=document.querySelector('#tutor-question'),chat=document.querySelector('#tutor-chat'),info=document.querySelector('#tutor-remaining'),sendBtn=form.querySelector('[type=submit]'),micBtn=document.querySelector('#tutor-mic'),sayBtn=document.querySelector('#tutor-say'),lang=document.querySelector('#tutor-language'),voiceToggle=document.querySelector('#tutor-auto-voice');
let active=false,recognition=null,speaking=null,busy=false,lastAnswer='',turns=0,history=[];
const level=document.querySelector('#tutor-level');
level?.addEventListener('change',()=>{history=[];turns=0;suggestionPage=0;renderSuggestions();});
const maxTurns=15;
const QUESTION_BANK={
 A1:[["Hello! How are you today?","¡Hola! ¿Cómo estás hoy?"],["What is your name?","¿Cómo te llamás?"],["Where are you from?","¿De dónde sos?"],["What is your favorite food?","¿Cuál es tu comida favorita?"],["Can you tell me about your family?","¿Podés contarme sobre tu familia?"],["What do you do every day?","¿Qué hacés todos los días?"],["What time do you wake up?","¿A qué hora te despertás?"],["What is the weather like today?","¿Cómo está el tiempo hoy?"]],
 A2:[["What did you do last weekend?","¿Qué hiciste el fin de semana pasado?"],["Could you describe your neighborhood?","¿Podrías describir tu barrio?"],["What are you going to do tomorrow?","¿Qué vas a hacer mañana?"],["How often do you exercise?","¿Con qué frecuencia hacés ejercicio?"],["Can you recommend a place to visit?","¿Podés recomendar un lugar para visitar?"],["What was your last vacation like?","¿Cómo fueron tus últimas vacaciones?"],["How do you usually get to work?","¿Cómo solés ir al trabajo?"],["Which season do you prefer and why?","¿Qué estación preferís y por qué?"]],
 B1:[["What are the benefits of learning English?","¿Qué beneficios tiene aprender inglés?"],["Can you tell me about an important decision you made?","¿Podés contarme sobre una decisión importante que tomaste?"],["How would you describe your ideal job?","¿Cómo describirías tu trabajo ideal?"],["What would you do if you had more free time?","¿Qué harías si tuvieras más tiempo libre?"],["How has your city changed in recent years?","¿Cómo cambió tu ciudad en los últimos años?"],["What are the pros and cons of online learning?","¿Cuáles son las ventajas y desventajas del aprendizaje en línea?"],["Tell me about a problem you solved successfully.","Contame sobre un problema que resolviste con éxito."],["What goals would you like to achieve next year?","¿Qué objetivos te gustaría alcanzar el año próximo?"]],
 B2:[["How has technology influenced the way people work?","¿Cómo influyó la tecnología en la forma de trabajar?"],["Do you believe remote work improves productivity?","¿Creés que el trabajo remoto mejora la productividad?"],["How should schools prepare students for the future?","¿Cómo deberían preparar las escuelas a los alumnos para el futuro?"],["What are the main advantages and drawbacks of social media?","¿Cuáles son las ventajas y desventajas principales de las redes sociales?"],["Describe a time you had to adapt to an unexpected situation.","Describí una ocasión en que tuviste que adaptarte a una situación inesperada."],["How can companies create a healthier work environment?","¿Cómo pueden las empresas crear un ambiente laboral más saludable?"],["Should public transportation be free in large cities?","¿Debería ser gratuito el transporte público en las grandes ciudades?"],["What makes a good leader in a difficult situation?","¿Qué hace que alguien sea un buen líder en una situación difícil?"]],
 C1:[["To what extent should AI be integrated into education?","¿Hasta qué punto debería integrarse la IA en la educación?"],["How can societies reconcile economic growth with environmental protection?","¿Cómo pueden las sociedades conciliar crecimiento económico y protección ambiental?"],["What is the relationship between social mobility and education?","¿Qué relación existe entre movilidad social y educación?"],["Should employers prioritize adaptability over formal qualifications?","¿Deberían los empleadores priorizar la adaptabilidad por encima de los títulos?"],["How might automation reshape professional identities?","¿Cómo podría la automatización transformar las identidades profesionales?"],["What policies could reduce inequality without discouraging innovation?","¿Qué políticas podrían reducir la desigualdad sin desalentar la innovación?"],["How does cultural context influence effective communication?","¿Cómo influye el contexto cultural en una comunicación eficaz?"],["Can you critically evaluate the impact of globalization?","¿Podés evaluar críticamente el impacto de la globalización?"]],
 C2:[["To what extent does language shape our perception of reality?","¿Hasta qué punto el lenguaje configura nuestra percepción de la realidad?"],["Is technological progress necessarily synonymous with social progress?","¿El progreso tecnológico equivale necesariamente al progreso social?"],["How should competing ethical principles be reconciled in public policy?","¿Cómo deberían conciliarse principios éticos contrapuestos en las políticas públicas?"],["Can meritocracy coexist with structural inequality?","¿Puede coexistir la meritocracia con la desigualdad estructural?"],["What are the limits of objectivity in historical interpretation?","¿Cuáles son los límites de la objetividad en la interpretación histórica?"],["How does rhetoric influence democratic decision-making?","¿Cómo influye la retórica en las decisiones democráticas?"],["Should freedom of expression have limits in digital spaces?","¿Debería tener límites la libertad de expresión en espacios digitales?"],["Can artificial intelligence ever exercise genuine judgment?","¿Puede la inteligencia artificial ejercer alguna vez un juicio genuino?"]]
};
const suggestions=document.querySelector('#tutor-suggested-questions'),suggestionLevel=document.querySelector('#tutor-suggestions-level'),refreshSuggestions=document.querySelector('#tutor-refresh-suggestions'),saveDialog=document.querySelector('#tutor-save-dialog'),dialogCount=document.querySelector('#tutor-dialog-count');
let suggestionPage=0,transcript=[];
function renderSuggestions(){
 const selected=level?.value||'A1',items=QUESTION_BANK[selected]||QUESTION_BANK.A1;
 if(!suggestions)return;
 suggestionLevel.textContent=selected;suggestions.replaceChildren();
 const start=(suggestionPage*4)%items.length;
 for(let i=0;i<4;i++){
  const item=items[(start+i)%items.length],btn=document.createElement('button');btn.type='button';btn.className='tutor-suggestion';btn.setAttribute('aria-label','Usar pregunta: '+item[0]);
  const en=document.createElement('strong'),es=document.createElement('span');en.textContent=item[0];es.textContent=item[1];btn.append(en,es);
  btn.addEventListener('click',()=>{input.value=item[0];input.focus();input.setSelectionRange(input.value.length,input.value.length);});
  suggestions.append(btn);
 }
}
function updateExport(){if(saveDialog)saveDialog.disabled=!transcript.length;if(dialogCount)dialogCount.textContent=transcript.filter(m=>m.role==='assistant').length+' respuestas en esta sesión';}
function downloadTranscript(){
 if(!transcript.length)return;
 const lines=['MyTeacher | Diálogo de práctica de inglés','Fecha de descarga: '+new Date().toLocaleString('es-AR'),'Niveles: '+[...new Set(transcript.map(m=>m.level))].join(', '),'Este archivo se generó en tu navegador.','='.repeat(48),''];
 for(const m of transcript){lines.push('['+m.time+'] '+(m.role==='assistant'?'Profesor':'Alumno')+' ('+m.level+'):');lines.push(m.content,'');}
 const blob=new Blob(['\uFEFF'+lines.join('\r\n')],{type:'text/plain;charset=utf-8'}),url=URL.createObjectURL(blob),a=document.createElement('a');
 a.href=url;a.download='myteacher-dialogo-'+new Date().toISOString().slice(0,10)+'.txt';document.body.append(a);a.click();a.remove();setTimeout(()=>URL.revokeObjectURL(url),3000);
}
refreshSuggestions?.addEventListener('click',()=>{suggestionPage++;renderSuggestions();});
saveDialog?.addEventListener('click',downloadTranscript);
renderSuggestions();updateExport();

function message(text,who){const p=document.createElement('p');p.className='tutor-bubble '+(who||'tutor-bot');p.textContent=text;chat.append(p);chat.scrollTop=chat.scrollHeight;return p;}
function setState(text){info.textContent=text;micBtn.textContent=active?'■ Terminar conversación':'🎙️ Iniciar conversación';micBtn.setAttribute('aria-pressed',String(active));}
function stop(){active=false;if(recognition){const r=recognition;recognition=null;window.ipVoice?.stop();try{r.abort?.();}catch{}}if(speaking){speaking.onend=null;speaking.onerror=null;speaking=null;}if(window.speechSynthesis)speechSynthesis.cancel();setState('Conversación detenida. Podés escribir una consulta si lo preferís.');}
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
  const res=await fetch('/api/tutor.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':document.querySelector('meta[name=csrf]')?.content||''},body:JSON.stringify({question,unit_id:Number(form.dataset.unit||0),level:level?.value||'A1',history:history.slice(-8)})});
  const data=await res.json();pending.textContent=data.answer||data.error||'No se recibió una respuesta.';
  if(typeof data.remaining==='number')info.textContent='Consultas restantes hoy: '+data.remaining+(data.limit?' / '+data.limit:'');
  if(!res.ok){if(fromVoice)stop();return;}
  lastAnswer=data.answer||'';const time=new Date().toLocaleString('es-AR'),currentLevel=level?.value||'A1';transcript.push({role:'user',content:question,level:currentLevel,time},{role:'assistant',content:lastAnswer,level:currentLevel,time});updateExport();history.push({role:'user',content:question},{role:'assistant',content:lastAnswer});history=history.slice(-8);turns++;input.value='';
  if(fromVoice&&active){
   if(turns>=maxTurns){stop();info.textContent='Terminó la sesión de práctica (máximo '+maxTurns+' intercambios).';speak(lastAnswer,false);}
   else speak(lastAnswer,true);
  }else if(voiceToggle?.checked)speak(lastAnswer,false);
 }catch{pending.textContent='No fue posible conectar con MyTeacher IA.';if(fromVoice)stop();}
 finally{busy=false;sendBtn.disabled=false;}
}
function listen(){
 if(!active||busy||recognition)return;
 if(!window.ipRecognize){stop();info.textContent='Este navegador no permite grabar audio. Podés usar el chat escrito.';return;}
 const token={};recognition=token;
 const voiceLang=lang.value==='en-US'?'en-US':'es-AR';
 setState('Preparando el micrófono…');
 window.ipRecognize('',{lang:voiceLang,
  onStart:()=>{if(recognition===token)setState('Te escucho en '+(voiceLang==='en-US'?'inglés':'español')+'… Hablá ahora.');},
  onProcessing:()=>{if(recognition===token)setState('Entendiendo tu consulta…');},
  onResult:alts=>{if(recognition!==token)return;const question=(alts[0]||'').trim();if(question){setState('Consulta reconocida. Esperando a MyTeacher IA…');recognition=null;ask(question,true);}},
  onFail:msg=>{if(recognition!==token)return;recognition=null;stop();info.textContent=msg+' El modo oral se detuvo; podés usar el chat escrito.';},
  onEnd:()=>{if(recognition===token)recognition=null;}
 });
}
micBtn.addEventListener('click',()=>{
 if(active){stop();return;}
 active=true;turns=0;setState('Conversación iniciada.');listen();
});
sayBtn.addEventListener('click',()=>{if(lastAnswer)speak(lastAnswer,false);else info.textContent='Primero necesitás una respuesta del profesor.';});
document.querySelectorAll('[data-tutor-prompt]').forEach(b=>b.addEventListener('click',()=>{input.value=b.dataset.tutorPrompt;input.focus();}));
form.addEventListener('submit',e=>{e.preventDefault();const q=input.value.trim();if(q.length>=3)ask(q,false);});
window.addEventListener('pagehide',stop);
})();