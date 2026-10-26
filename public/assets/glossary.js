(()=>{
'use strict';
const $=s=>document.querySelector(s),key='mt_glossary_v1';
let serverEntries=[];const get=()=>serverEntries.slice().sort((a,b)=>a.word.localeCompare(b.word,'es',{sensitivity:'base'}));
const sync=async()=>{try{const r=await fetch('/api/glossary.php',{cache:'no-store'});if(!r.ok)throw Error();const j=await r.json();serverEntries=j.entries||[];render();const local=JSON.parse(localStorage.getItem(key)||'[]');if(Array.isArray(local)&&local.length){for(const x of local){if(x.word&&x.translation&&!serverEntries.some(v=>v.word.toLocaleLowerCase()===x.word.toLocaleLowerCase()))await save(x);}localStorage.removeItem(key);const rr=await fetch('/api/glossary.php');if(rr.ok)serverEntries=(await rr.json()).entries||[];render();}}catch{status.textContent='No se pudo sincronizar el glosario con tu cuenta.';}};
const save=async x=>{const csrf=document.querySelector('meta[name=csrf]')?.content||'';const r=await fetch('/api/glossary.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:JSON.stringify({action:'save',term:x.word,translation:x.translation,context:x.context||'',category:x.tag||'General'})});if(!r.ok)throw Error((await r.json()).error||'No se pudo guardar');};
const remove=async term=>{const csrf=document.querySelector('meta[name=csrf]')?.content||'';const r=await fetch('/api/glossary.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:JSON.stringify({action:'delete',term})});if(!r.ok)throw Error('No se pudo eliminar');};
const put=()=>true;
const clean=s=>String(s||'').trim(),esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let current='',reviewIndex=-1,showTranslation=false;
const status=$('#lex-result');
function pronounce(t){if(!('speechSynthesis' in window))return;speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(t);u.lang='en-US';u.rate=.85;speechSynthesis.speak(u)}
function render(){const a=get(),q=clean($('#lex-filter').value).toLowerCase();$('#lex-count').textContent='('+a.length+')';const rows=a.filter(x=>(x.word+' '+x.translation+' '+x.tag+' '+x.context).toLowerCase().includes(q));$('#lex-list').innerHTML=rows.length?rows.map(x=>'<article class="lex-entry"><div><strong>'+esc(x.word)+'</strong> <small>'+esc(x.tag)+'</small><p>'+esc(x.translation)+'</p>'+(x.context?'<small>'+esc(x.context)+'</small>':'')+'</div><div><button type="button" data-hear="'+encodeURIComponent(x.word)+'" aria-label="Pronunciar">🔊</button><button type="button" data-remove="'+encodeURIComponent(x.word)+'" aria-label="Eliminar palabra">Eliminar</button></div></article>').join(''):'<p class="muted">Todavía no hay palabras guardadas para esta búsqueda.</p>';}
$('#lex-form').addEventListener('submit',async e=>{e.preventDefault();current=clean($('#lex-word').value);if(!current)return;status.textContent='Consultando traducción…';$('#lex-save-form').hidden=true;
try{const res=await fetch('/api/translate.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':document.querySelector('meta[name=csrf]')?.content||''},body:JSON.stringify({term:current})});const data=await res.json();if(!res.ok||!data.translation)throw Error('No encontramos una traducción confiable');$('#lex-translation').value=data.translation;status.textContent='Traducción sugerida. Revisala según el contexto antes de guardarla.';}
catch{status.textContent='No se pudo obtener la traducción. Podés completarla manualmente.';$('#lex-translation').value='';}
$('#lex-save-form').hidden=false;});
$('#lex-save-form').addEventListener('submit',async e=>{e.preventDefault();const word=clean(current),translation=clean($('#lex-translation').value);if(!word||!translation)return;try{await save({word,translation,context:clean($('#lex-context').value),tag:$('#lex-tag').value});status.textContent='Guardado en tu cuenta.';await sync();}catch(err){status.textContent=err.message||'No se pudo guardar.';}});
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