(()=>{
'use strict';
const $=s=>document.querySelector(s),key='mt_glossary_v1';
const get=()=>{try{return JSON.parse(localStorage.getItem(key)||'[]')}catch{return []}};
const put=a=>{try{localStorage.setItem(key,JSON.stringify(a));return true}catch{return false}};
const clean=s=>String(s||'').trim(),esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let current='',reviewIndex=-1,showTranslation=false;
const status=$('#lex-result');
function pronounce(t){if(!('speechSynthesis' in window))return;speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(t);u.lang='en-US';u.rate=.85;speechSynthesis.speak(u)}
function render(){const a=get(),q=clean($('#lex-filter').value).toLowerCase();$('#lex-count').textContent='('+a.length+')';const rows=a.filter(x=>(x.word+' '+x.translation+' '+x.tag+' '+x.context).toLowerCase().includes(q));$('#lex-list').innerHTML=rows.length?rows.map(x=>'<article class="lex-entry"><div><strong>'+esc(x.word)+'</strong> <small>'+esc(x.tag)+'</small><p>'+esc(x.translation)+'</p>'+(x.context?'<small>'+esc(x.context)+'</small>':'')+'</div><div><button type="button" data-hear="'+encodeURIComponent(x.word)+'" aria-label="Pronunciar">🔊</button><button type="button" data-remove="'+encodeURIComponent(x.word)+'" aria-label="Eliminar palabra">Eliminar</button></div></article>').join(''):'<p class="muted">Todavía no hay palabras guardadas para esta búsqueda.</p>';}
$('#lex-form').addEventListener('submit',async e=>{e.preventDefault();current=clean($('#lex-word').value);if(!current)return;status.textContent='Consultando traducción…';$('#lex-save-form').hidden=true;
try{const res=await fetch('https://api.mymemory.translated.net/get?q='+encodeURIComponent(current)+'&langpair=en|es');if(!res.ok)throw Error('Servicio temporalmente no disponible');const data=await res.json();if(data.responseStatus!==200||!data.responseData?.translatedText)throw Error('No encontramos una traducción confiable');$('#lex-translation').value=data.responseData.translatedText;status.textContent='Traducción sugerida. Revisala según el contexto antes de guardarla.';}
catch{status.textContent='No se pudo obtener la traducción. Podés completarla manualmente.';$('#lex-translation').value='';}
$('#lex-save-form').hidden=false;});
$('#lex-save-form').addEventListener('submit',e=>{e.preventDefault();const word=clean(current),translation=clean($('#lex-translation').value);if(!word||!translation)return;const a=get().filter(x=>x.word.toLowerCase()!==word.toLowerCase());a.unshift({word,translation,context:clean($('#lex-context').value),tag:$('#lex-tag').value,created:new Date().toISOString()});if(!put(a)){status.textContent='No se pudo guardar: verificá el almacenamiento del navegador.';return;}status.textContent='Palabra guardada. Podés repasarla desde las tarjetas.';render();});
$('#lex-pronounce').addEventListener('click',()=>pronounce(current));
$('#lex-filter').addEventListener('input',render);
$('#lex-list').addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;if(b.dataset.hear)pronounce(decodeURIComponent(b.dataset.hear));if(b.dataset.remove){const w=decodeURIComponent(b.dataset.remove);if(confirm('¿Eliminar «'+w+'» del glosario?')){put(get().filter(x=>x.word!==w));render();}}});
function card(){const a=get();if(!a.length){$('#lex-card').textContent='Primero guardá una palabra.';return;}const x=a[reviewIndex%a.length];$('#lex-card').textContent=showTranslation?x.word+' → '+x.translation+(x.context?' | '+x.context:''):x.word;}
$('#lex-next').addEventListener('click',()=>{const a=get();if(!a.length)return;reviewIndex=(reviewIndex+1)%a.length;showTranslation=false;card()});
$('#lex-reveal').addEventListener('click',()=>{if(reviewIndex<0)reviewIndex=0;showTranslation=true;card()});
$('#lex-export').addEventListener('click',()=>{const a=get();const quote=x=>'"'+String(x||'').replace(/"/g,'""')+'"';const csv='\uFEFF'+[['Palabra','Traducción','Contexto','Categoría'],...a.map(x=>[x.word,x.translation,x.context,x.tag])].map(r=>r.map(quote).join(',')).join('\r\n');const url=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8'}));const link=document.createElement('a');link.href=url;link.download='myteacher-glosario.csv';link.click();setTimeout(()=>URL.revokeObjectURL(url),1000)});
render();
})();