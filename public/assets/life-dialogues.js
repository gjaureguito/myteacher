(()=>{'use strict';
const dataEl=document.getElementById('life-data');if(!dataEl)return;
const catalog=JSON.parse(dataEl.textContent||'[]'),level=document.getElementById('life-level'),filter=document.getElementById('life-filter'),list=document.getElementById('life-scenes'),chat=document.getElementById('life-chat'),choices=document.getElementById('life-choices'),status=document.getElementById('life-status'),translation=document.getElementById('life-translate'),autoplay=document.getElementById('life-autoplay');
let current=null,step=0,transcript=[];
const ROLE_NAMES={"Cafetería":"Barista","Supermercado":"Cajero","Parada de autobús":"Conductor","Hotel":"Recepcionista","Estación de tren":"Empleado de boletería","Panadería":"Panadero","Farmacia":"Farmacéutico","Librería":"Librero","Tienda de ropa":"Vendedor","Parque":"Guardaparques","Gimnasio":"Entrenador","Recepción de oficina":"Recepcionista","Reserva de restaurante":"Recepcionista","Alquiler de bicicleta":"Empleado de alquiler","Consulta médica":"Médico","Comprar un boleto":"Empleado de boletería","Devolución de producto":"Atención al cliente","Visita a museo":"Guía del museo","Pedir indicaciones":"Transeúnte","Servicio de taxi":"Taxista","Reserva de hotel":"Recepcionista","Compra de regalo":"Vendedor","Organizar una fiesta":"Organizador","Centro deportivo":"Instructor","Entrevista laboral":"Entrevistador","Reclamo de equipaje":"Agente de equipajes","Mudanza a otra ciudad":"Asesor","Reunión de equipo":"Coordinador","Problemas de conexión":"Soporte técnico","Elegir un curso":"Asesor académico","Planear vacaciones":"Agente de viajes","Cambiar una reserva":"Agente de reservas","Negociar un horario":"Supervisor","Visitar una vivienda":"Agente inmobiliario","Atención al cliente":"Representante de atención","Viaje de trabajo":"Coordinador de viajes","Presentación comercial":"Cliente potencial","Debate sobre teletrabajo":"Moderador","Queja formal":"Responsable de reclamos","Reunión de proyecto":"Director de proyecto","Entrevista técnica":"Entrevistador técnico","Conflicto de equipo":"Mediador","Propuesta de inversión":"Inversor","Sostenibilidad empresarial":"Responsable de sostenibilidad","Revisión de contrato":"Asesor legal","Plan de carrera":"Mentor","Turismo responsable":"Coordinador de turismo","Gestión de crisis":"Director de crisis"};
const npcRole=()=>current?.npc_role||ROLE_NAMES[current?.title]||(current?.level==='C1'||current?.level==='C2'?'Moderador':current?.level==='B2'?'Especialista':'Representante');
const learnerRole=()=>current?.role||'Participante';
const englishVoices=()=>window.speechSynthesis?.getVoices().filter(v=>/^en(?:-|_)/i.test(v.lang))||[];
const voiceFor=(speaker)=>{const voices=englishVoices();return voices.length>1?voices[speaker===learnerRole()?1:0]:voices[0]||null;};
let playbackToken=0;
function speakTurns(turns){
 if(!('speechSynthesis'in window))return;
 const token=++playbackToken;speechSynthesis.cancel();
 let index=0;
 function next(){
  if(token!==playbackToken||index>=turns.length)return;
  const turn=turns[index++],msg=new SpeechSynthesisUtterance(turn.en);
  const voice=voiceFor(turn.who);
  if(voice)msg.voice=voice;
  msg.lang=voice?.lang||'en-US';msg.rate=.85;msg.pitch=turn.who===learnerRole()?1.2:.88;
  msg.onend=()=>{if(token===playbackToken)next();};
  msg.onerror=()=>{if(token===playbackToken)next();};
  speechSynthesis.speak(msg);
 }
 next();
}
const say=(text,speaker)=>speakTurns([{who:speaker,en:text}]);
function bubble(speaker,en,es){
 const previous=chat.lastElementChild;
 // Un mismo interlocutor puede responder y continuar su pregunta: es un solo turno visual.
 if(previous?.dataset.speaker===speaker){
   const content=previous.querySelector('.life-bubble-text'),trans=previous.querySelector('.life-translation'),play=previous.querySelector('.life-speak');
   const fullEn=(previous.dataset.english||'')+' '+en;
   previous.dataset.english=fullEn;content.textContent=fullEn;
   trans.textContent+=(trans.textContent?' ':'')+es;
   play.onclick=()=>say(fullEn,speaker);
   chat.scrollTop=chat.scrollHeight;
   
   return;
 }
 const p=document.createElement('div');p.className='life-bubble '+(speaker===learnerRole()?'is-student':'');
 p.dataset.speaker=speaker;p.dataset.english=en;
 const h=document.createElement('strong');h.textContent=speaker;
 const content=document.createElement('p');content.className='life-bubble-text';content.textContent=en;
 const trans=document.createElement('small');trans.className='life-translation';trans.textContent=es;trans.hidden=!translation.checked;
 const play=document.createElement('button');play.type='button';play.className='life-speak';play.textContent='🔊 Escuchar';play.addEventListener('click',()=>say(p.dataset.english,speaker));
 p.append(h,content,trans,play);chat.append(p);chat.scrollTop=chat.scrollHeight;
 
}
function showStep(){choices.replaceChildren();if(!current)return;if(step>=current.steps.length){status.textContent='¡Escena completada! Podés descargar el diálogo o reiniciar para practicar otras respuestas.';try{localStorage.setItem('life-completed-'+current.slug,'1');}catch{}renderList();return;}const node=current.steps[step];bubble(npcRole(),node.npc,node.es);transcript.push({who:npcRole(),en:node.npc,es:node.es});status.textContent='Tu turno: '+current.role+'. Elegí una respuesta.';for(const option of node.choices){const b=document.createElement('button');b.type='button';b.className='life-choice';const en=document.createElement('strong');en.textContent=option.en;const es=document.createElement('span');es.textContent=option.es;es.hidden=!translation.checked;b.append(en,es);b.addEventListener('click',()=>{const first=transcript.length;bubble(learnerRole(),option.en,option.es);transcript.push({who:learnerRole(),en:option.en,es:option.es});if(option.reply){bubble(npcRole(),option.reply.en,option.reply.es);transcript.push({who:npcRole(),en:option.reply.en,es:option.reply.es});}step++;showStep();if(autoplay.checked)speakTurns(transcript.slice(first));});choices.append(b);}}
function selectScene(scene){if('speechSynthesis'in window)++playbackToken;speechSynthesis.cancel();current=scene;step=0;transcript=[];chat.replaceChildren();document.getElementById('life-title').textContent=scene.title;document.getElementById('life-role').textContent=scene.level+' · '+scene.category+' · Tu rol: '+learnerRole()+' · Otro personaje: '+npcRole();renderList();showStep();if(autoplay.checked)speakTurns(transcript.slice());}
function renderList(){list.replaceChildren();const q=filter.value.toLocaleLowerCase('es');const items=catalog.filter(s=>s.level===level.value&&s.title.toLocaleLowerCase('es').includes(q));document.getElementById('life-catalog-count').textContent=items.length+' situaciones en este nivel';for(const item of items){const b=document.createElement('button');b.type='button';b.className='life-scene'+(current?.slug===item.slug?' selected':'');let completed=false;try{completed=localStorage.getItem('life-completed-'+item.slug)==='1';}catch{}b.textContent=item.title+(completed?' ✓':'');b.addEventListener('click',()=>selectScene(item));list.append(b);}}
level.addEventListener('change',()=>{renderList();const first=catalog.find(x=>x.level===level.value);if(first)selectScene(first);});filter.addEventListener('input',renderList);
translation.addEventListener('change',()=>{chat.querySelectorAll('.life-translation').forEach(el=>el.hidden=!translation.checked);choices.querySelectorAll('.life-choice span').forEach(el=>el.hidden=!translation.checked);});
const replay=document.getElementById('life-replay');
if(replay)replay.addEventListener('click',()=>{if(transcript.length)speakTurns(transcript);else status.textContent='Primero elegí una situación.';});
document.getElementById('life-reset').addEventListener('click',()=>{if(current)selectScene(current);});
document.getElementById('life-export').addEventListener('click',()=>{if(!current||!transcript.length){status.textContent='Elegí una escena para descargar su diálogo.';return;}const parts=['MyTeacher · Situación real: '+current.title,'Nivel: '+current.level+' | Rol: '+current.role,'Fecha: '+new Date().toLocaleString('es-AR'),'='.repeat(50),''];for(const line of transcript)parts.push(line.who+': '+line.en,'Traducción: '+line.es,'');const blob=new Blob(['\uFEFF'+parts.join('\r\n')],{type:'text/plain;charset=utf-8'}),url=URL.createObjectURL(blob),a=document.createElement('a');a.href=url;a.download='myteacher-'+current.slug+'.txt';document.body.append(a);a.click();a.remove();setTimeout(()=>URL.revokeObjectURL(url),2000);});
window.addEventListener('pagehide',()=>{if('speechSynthesis'in window)speechSynthesis.cancel();});const requestedLevel=new URLSearchParams(window.location.search).get('level')?.toUpperCase();if(requestedLevel&&['A1','A2','B1','B2','C1','C2'].includes(requestedLevel))level.value=requestedLevel;renderList();if(catalog.length)selectScene(catalog.find(x=>x.level===level.value)||catalog[0]);
})();