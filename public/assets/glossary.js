(()=>{
'use strict';
const $=s=>document.querySelector(s),key='mt_glossary_v1';
let serverEntries=[];const get=()=>serverEntries.slice().sort((a,b)=>a.word.localeCompare(b.word,'es',{sensitivity:'base'}));
const sync=async()=>{try{const r=await fetch('/api/glossary.php',{cache:'no-store'});if(!r.ok)throw Error();const j=await r.json();serverEntries=j.entries||[];render();const local=JSON.parse(localStorage.getItem(key)||'[]');if(Array.isArray(local)&&local.length){for(const x of local){if(x.word&&x.translation&&!serverEntries.some(v=>v.word.toLocaleLowerCase()===x.word.toLocaleLowerCase()))await save(x);}localStorage.removeItem(key);const rr=await fetch('/api/glossary.php');if(rr.ok)serverEntries=(await rr.json()).entries||[];render();}}catch{status.textContent='No se pudo sincronizar el glosario con tu cuenta.';}};
const save=async x=>{const csrf=document.querySelector('meta[name=csrf]')?.content||'';const r=await fetch('/api/glossary.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:JSON.stringify({action:'save',term:x.word,translation:x.translation,context:x.context||'',category:x.tag||'General'})});if(!r.ok)throw Error((await r.json()).error||'No se pudo guardar');};
const remove=async term=>{const csrf=document.querySelector('meta[name=csrf]')?.content||'';const r=await fetch('/api/glossary.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:JSON.stringify({action:'delete',term})});if(!r.ok)throw Error('No se pudo eliminar');};
const put=()=>true;
const clean=s=>String(s||'').trim(),esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let current='',lastDirection='en-es',reviewIndex=-1,showTranslation=false;
const status=$('#lex-result');
function pronounce(t){if(!('speechSynthesis' in window))return;speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(t);u.lang='en-US';u.rate=.85;speechSynthesis.speak(u)}
const alphabet='ABCDEFGHIJKLMNÑOPQRSTUVWXYZ'.split('');
let activeLetter='Todas';
const initial=word=>{const first=String(word||'').trim().charAt(0).toLocaleUpperCase('es');if(first==='Ñ')return 'Ñ';return first.normalize('NFD').replace(/[\u0300-\u036f]/g,'');};
function renderAlphabet(entries){
 const nav=$('#lex-alphabet');nav.replaceChildren();
 const counts=new Map();entries.forEach(x=>{const letter=initial(x.word);counts.set(letter,(counts.get(letter)||0)+1);});
 for(const letter of ['Todas',...alphabet]){
   const button=document.createElement('button');button.type='button';button.className='lex-letter'+(activeLetter===letter?' is-active':'');
   button.textContent=letter;button.setAttribute('aria-pressed',String(activeLetter===letter));
   const count=letter==='Todas'?entries.length:(counts.get(letter)||0);
   button.setAttribute('aria-label',letter==='Todas'?'Mostrar todas las palabras ('+count+')':'Letra '+letter+': '+count+' palabras');
   if(letter!=='Todas'&&!count)button.disabled=true;
   button.addEventListener('click',()=>{activeLetter=letter;render();});
   nav.append(button);
 }
}
function render(){
 const a=get(),q=clean($('#lex-filter').value).toLocaleLowerCase('es');
 $('#lex-count').textContent='('+a.length+')';renderAlphabet(a);
 const rows=a.filter(x=>(activeLetter==='Todas'||initial(x.word)===activeLetter)&&((x.word+' '+x.translation+' '+x.tag+' '+x.context).toLocaleLowerCase('es').includes(q)));
 const statusEl=$('#lex-letter-status');
 statusEl.textContent=activeLetter==='Todas'?'Mostrando '+rows.length+' de '+a.length+' palabras': 'Letra '+activeLetter+' · '+rows.length+' '+(rows.length===1?'palabra':'palabras');
 $('#lex-list').innerHTML=rows.length?rows.map(x=>{const tag=clean(x.tag),note=clean(x.context),showTag=tag&&tag.toLowerCase()!=='general',showNote=note&&!/^selecci[oó]n de texto en myteacher$/i.test(note);return '<article class="lex-entry"><div><strong>'+esc(x.word)+'</strong>'+(showTag?' <small class="lex-tag">'+esc(tag)+'</small>':'')+'<p>'+esc(x.translation)+'</p>'+(showNote?'<small class="lex-context">Ejemplo o nota: '+esc(note)+'</small>':'')+'</div><div><button type="button" data-hear="'+encodeURIComponent(x.word)+'" aria-label="Pronunciar">🔊</button><button type="button" data-remove="'+encodeURIComponent(x.word)+'" aria-label="Eliminar palabra">Eliminar</button></div></article>';}).join(''):'<p class="muted">No hay palabras para esta letra o búsqueda. Elegí otra pestaña o probá con otro término.</p>';
}

$('#lex-form').addEventListener('submit',async e=>{e.preventDefault();current=clean($('#lex-word').value);if(!current)return;status.textContent='Consultando traducción…';$('#lex-save-form').hidden=true;
try{const res=await fetch('/api/translate.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':document.querySelector('meta[name=csrf]')?.content||''},body:JSON.stringify({term:current,direction:$('#lex-direction').value})});const data=await res.json();if(!res.ok||!data.translation)throw Error('No encontramos una traducción confiable');lastDirection=data.direction||$('#lex-direction').value;$('#lex-translation').value=data.translation;status.textContent='Traducción '+(data.direction==='es-en'?'español → inglés':'inglés → español')+'. Revisala antes de guardarla.';}
catch{status.textContent='No se pudo obtener la traducción. Podés completarla manualmente.';$('#lex-translation').value='';lastDirection='en-es';}
$('#lex-save-form').hidden=false;});
$('#lex-save-form').addEventListener('submit',async e=>{e.preventDefault();const typed=clean(current),translated=clean($('#lex-translation').value),reverse=$('#lex-direction').value==='es-en'||($('#lex-direction').value==='auto'&&lastDirection==='es-en'),word=reverse?translated:typed,translation=reverse?typed:translated;if(!word||!translation)return;try{await save({word,translation,context:clean($('#lex-context').value),tag:$('#lex-tag').value});status.textContent='Guardado en tu cuenta.';await sync();}catch(err){status.textContent=err.message||'No se pudo guardar.';}});
$('#lex-pronounce').addEventListener('click',()=>pronounce(current));
$('#lex-filter').addEventListener('input',render);
$('#lex-list').addEventListener('click',async e=>{const b=e.target.closest('button');if(!b)return;if(b.dataset.hear)pronounce(decodeURIComponent(b.dataset.hear));if(b.dataset.remove){const w=decodeURIComponent(b.dataset.remove);if(confirm('¿Eliminar «'+w+'» del glosario?')){try{await remove(w);await sync();}catch{status.textContent='No se pudo eliminar.';}}}});
function card(){
 const container=$('#lex-card'),a=get();
 container.replaceChildren();
 if(!a.length){container.textContent='Primero guardá una palabra.';return;}
 if(reviewIndex<0)reviewIndex=0;
 const x=a[reviewIndex%a.length];
 const word=document.createElement('strong');word.textContent=x.word;container.append(word);
 if(showTranslation){
  const meaning=document.createElement('p');meaning.textContent=x.translation;meaning.className='lex-card-meaning';container.append(meaning);
  const note=String(x.context||'').trim();
  if(note&&!/^selecci[oó]n de texto en myteacher$/i.test(note)){
   const context=document.createElement('small');context.textContent='Ejemplo o nota: '+note;context.className='lex-card-context';container.append(context);
  }
 }
 $('#lex-reveal').disabled=showTranslation;
}
$('#lex-next').addEventListener('click',()=>{const a=get();if(!a.length)return;reviewIndex=(reviewIndex+1)%a.length;showTranslation=false;card()});
$('#lex-reveal').addEventListener('click',()=>{if(reviewIndex<0)reviewIndex=0;showTranslation=true;card()});
$('#lex-export').addEventListener('click',()=>{const a=get();const quote=x=>'"'+String(x||'').replace(/"/g,'""')+'"';const csv='\uFEFF'+[['Palabra','Traducción','Contexto','Categoría'],...a.map(x=>[x.word,x.translation,x.context,x.tag])].map(r=>r.map(quote).join(',')).join('\r\n');const url=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8'}));const link=document.createElement('a');link.href=url;link.download='myteacher-glosario.csv';link.click();setTimeout(()=>URL.revokeObjectURL(url),1000)});
render();sync();
})();