(() => {
 'use strict';
 document.querySelectorAll('.scene[data-scene]').forEach(scene => {
  const lines=[...scene.querySelectorAll('[data-line]')].map(el=>el.querySelector('[data-en]')?.textContent?.trim()||'').filter(Boolean);
  if(lines.length<2)return;
  const host=scene.querySelector('.scene-practice');
  if(!host)return;
  const lab=document.createElement('section');
  lab.className='scene-writing-lab';
  lab.innerHTML='<h3>✍️ Respondé con tus propias palabras</h3><p>Explicá en inglés el problema del diálogo, lo que propone cada persona y cuál sería el siguiente paso. Escribí al menos 40 palabras.</p><label for="scene-writing-'+scene.dataset.scene+'">Tu respuesta en inglés</label><textarea id="scene-writing-'+scene.dataset.scene+'" rows="6" spellcheck="true" lang="en" placeholder="In this conversation, the speakers..."></textarea><div class="sw-actions"><span class="sw-count" aria-live="polite">0 palabras</span><button type="button" data-sw="assess">Revisar mi respuesta</button><button type="button" data-sw="sample">Ver frases útiles</button><button type="button" data-sw="clear">Borrar borrador</button><button type="button" data-sw="copy">Copiar respuesta</button></div><div class="sw-feedback" role="status" aria-live="polite"></div><div class="sw-sample" hidden><strong>Expresiones para reformular:</strong><p lang="en">The main issue is… · One speaker suggests that… · However, the other person points out that… · A reasonable next step would be…</p></div><p class="sw-note">Revisión orientativa. El borrador se guarda solamente en este navegador, no en tu cuenta; evitá incluir información privada si usás un dispositivo compartido. No es una corrección gramatical ni suma puntos.</p>';
  host.appendChild(lab);
  const field=lab.querySelector('textarea'),count=lab.querySelector('.sw-count'),feedback=lab.querySelector('.sw-feedback');
  const draftKey='myteacher:scene:draft:'+scene.dataset.scene;
  const savedMessage=document.createElement('span');
  savedMessage.className='sw-save-status';
  savedMessage.setAttribute('role','status');
  savedMessage.setAttribute('aria-live','polite');
  lab.querySelector('.sw-actions').appendChild(savedMessage);
  try {
    const previous=localStorage.getItem(draftKey);
    if(previous){field.value=previous;savedMessage.textContent='Borrador recuperado de este dispositivo';}
  } catch { savedMessage.textContent='El navegador no permite guardar borradores'; }
  function saveDraft(){
    try {localStorage.setItem(draftKey,field.value);savedMessage.textContent='Borrador guardado en este dispositivo';}
    catch {savedMessage.textContent='No fue posible guardar el borrador';}
  }
  lab.querySelector('[data-sw="clear"]').addEventListener('click',()=>{
    if(!window.confirm('¿Eliminar este borrador guardado en el navegador?'))return;
    field.value='';
    try{localStorage.removeItem(draftKey);}catch{}
    count.textContent='0 palabras';feedback.textContent='';savedMessage.textContent='Borrador eliminado';
  });
  lab.querySelector('[data-sw="copy"]').addEventListener('click',async()=>{
    if(!field.value.trim()){savedMessage.textContent='No hay texto para copiar';return;}
    try{await navigator.clipboard.writeText(field.value);savedMessage.textContent='Respuesta copiada';}
    catch{field.focus();field.select();savedMessage.textContent='Seleccioná el texto y presioná Ctrl+C';}
  });
  function tokens(){return field.value.trim().split(/\s+/).filter(Boolean);}
  count.textContent=tokens().length+' palabras';
  field.addEventListener('input',()=>{count.textContent=tokens().length+' palabras';feedback.textContent='';saveDraft();});
  lab.querySelector('[data-sw="sample"]').addEventListener('click',()=>{const box=lab.querySelector('.sw-sample');box.hidden=!box.hidden;});
  lab.querySelector('[data-sw="assess"]').addEventListener('click',()=>{
   const text=field.value.trim(),length=tokens().length;
   const hasContrast=/\b(but|although|however|whereas|nevertheless|yet)\b/i.test(text);
   const hasProposal=/\b(suggest|recommend|should|could|would|propose|need to)\b/i.test(text);
   const remarks=[];
   const hasOpening=/\b(issue|problem|discussion|conversation|concern|situation|challenge)\b/i.test(text);
   const hasSequence=/\b(first|then|finally|therefore|as a result|next)\b/i.test(text);
   remarks.push(length>=40?'✓ Extensión suficiente.':'Te faltan '+(40-length)+' palabras para el objetivo.');
   remarks.push(hasContrast?'✓ Incluiste un contraste.':'Podés añadir un contraste usando however, although o but.');
   remarks.push(hasProposal?'✓ Incluiste lenguaje para proponer una acción.':'Podés proponer un siguiente paso con should, could o recommend.');
   remarks.push(hasOpening?'✓ Introdujiste una situación o problema.':'Podés comenzar presentando the main issue o the problem.');
   remarks.push(hasSequence?'✓ Incluiste una secuencia o conclusión.':'Podés mejorar la organización con first, then o therefore.');
   feedback.textContent=remarks.join(' ');
  });
  if(!document.getElementById('scene-writing-styles')){
   const style=document.createElement('style');style.id='scene-writing-styles';
   style.textContent='.scene-writing-lab{border:1px solid #a7b9d1;border-radius:16px;padding:18px;margin-top:24px;background:var(--surface,#f7f9fc)}.scene-writing-lab h3{margin-top:0}.scene-writing-lab label{display:block;font-weight:700;margin-bottom:6px}.scene-writing-lab textarea{display:block;width:100%;box-sizing:border-box;min-height:145px;padding:12px;border:1px solid #839bb8;border-radius:9px;font:inherit;resize:vertical;background:var(--surface,#fff);color:inherit}.sw-actions{display:flex;align-items:center;flex-wrap:wrap;gap:10px;margin:12px 0}.sw-actions button{border:1px solid #7089a7;border-radius:9px;padding:10px;background:transparent;color:inherit;cursor:pointer}.sw-feedback{font-weight:600;line-height:1.6}.sw-sample{background:#e5eefb;color:#172b49;border-radius:9px;padding:10px}.sw-note{font-size:.82rem;opacity:.85}';
   document.head.appendChild(style);
  }
 });
})();