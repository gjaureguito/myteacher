(()=>{'use strict';
const csrf=()=>document.querySelector('meta[name=csrf]')?.content||'';
const menu=document.createElement('div');menu.className='mt-selection-menu';menu.hidden=true;menu.innerHTML='<button type="button" data-action="lookup">🔎 Buscar en diccionario con IA</button><button type="button" data-action="save">＋ Agregar al glosario</button>';
document.body.append(menu);
let selected='';
function selectionText(){const el=document.activeElement;let txt='';if(el instanceof HTMLInputElement||el instanceof HTMLTextAreaElement){if(typeof el.selectionStart==='number')txt=el.value.slice(el.selectionStart,el.selectionEnd);}else txt=window.getSelection()?.toString()||'';return txt.trim().replace(/\s+/g,' ');}
function hide(){menu.hidden=true;}
document.addEventListener('contextmenu',e=>{const t=selectionText();if(!t||t.length>220||!e.target.closest('main')){hide();return;}selected=t;e.preventDefault();menu.hidden=false;menu.style.left=Math.min(e.clientX,innerWidth-260)+'px';menu.style.top=Math.min(e.clientY,innerHeight-95)+'px';});
document.addEventListener('click',e=>{if(!menu.contains(e.target))hide();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')hide();});
menu.addEventListener('click',async e=>{const action=e.target.closest('button')?.dataset.action;if(!action)return;const term=selected;hide();if(action==='lookup'){location.href='/tutor.php?term='+encodeURIComponent(term);return;}let translation=prompt('Traducción para guardar «'+term+'» en tu glosario:','');if(!translation?.trim())return;translation=translation.trim();try{const r=await fetch('/api/glossary.php',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf()},body:JSON.stringify({term,translation,context:'Selección de texto en MyTeacher',category:'General'})});if(!r.ok)throw Error();alert('Guardado en tu glosario personal.');}catch{alert('No se pudo guardar. Intentá nuevamente desde Mi glosario.');}});
})();