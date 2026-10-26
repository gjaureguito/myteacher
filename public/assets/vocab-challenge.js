(() => {
 'use strict';
 const panel=document.getElementById('t-vocab');
 const cards=[...(panel?.querySelectorAll('.card-flip')||[])];
 if(cards.length<4)return;
 const words=cards.map(card=>({en:card.querySelector('.front')?.textContent?.trim()||'',es:card.querySelector('.back')?.childNodes[0]?.textContent?.trim()||''})).filter(x=>x.en&&x.es);
 if(words.length<4)return;
 const root=document.createElement('section');root.className='vocab-challenge';
 root.innerHTML='<h3>🧠 Desafío de vocabulario</h3><p>Elegí la traducción correcta. Las preguntas aparecen en orden aleatorio y podés practicar otra vez.</p><p class="vc-score" role="status" aria-live="polite"></p><div class="vc-question"></div><div class="vc-options" role="group" aria-label="Elegí una traducción"></div><p class="vc-feedback" role="status" aria-live="polite"></p><div class="vc-actions"><button type="button" data-vc="hear">🔊 Escuchar</button><button type="button" data-vc="next" disabled>Siguiente →</button><button type="button" data-vc="restart">Reiniciar desafío</button></div>';
 panel.insertBefore(root,panel.querySelector('.vocab'));
 const score=root.querySelector('.vc-score'),question=root.querySelector('.vc-question'),options=root.querySelector('.vc-options'),feedback=root.querySelector('.vc-feedback');
 const next=root.querySelector('[data-vc="next"]');
 function shuffle(a){const r=a.slice();for(let i=r.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[r[i],r[j]]=[r[j],r[i]];}return r;}
 let deck=[],position=0,correct=0,answered=false;
 function show(){
  answered=false;next.disabled=true;feedback.textContent='';options.replaceChildren();
  if(position>=deck.length){question.textContent='¡Desafío terminado!';score.textContent='Resultado: '+correct+' de '+deck.length+' respuestas correctas';next.disabled=true;return;}
  const entry=deck[position];question.textContent=entry.en;question.lang='en';
  score.textContent='Pregunta '+(position+1)+' de '+deck.length+' · '+correct+' aciertos';
  const pool=shuffle([entry,...shuffle(words.filter(x=>x.es!==entry.es)).slice(0,3)]);
  pool.forEach(option=>{
   const b=document.createElement('button');b.type='button';b.textContent=option.es;
   b.addEventListener('click',()=>{
    if(answered)return;
    answered=true;const ok=option.es===entry.es;if(ok)correct++;
    [...options.children].forEach(child=>{child.disabled=true;if(child.textContent===entry.es)child.classList.add('vc-correct');else if(child===b)child.classList.add('vc-wrong');});
    feedback.textContent=ok?'✓ ¡Correcto!':'Respuesta correcta: '+entry.es;
    next.disabled=false;next.textContent=position===deck.length-1?'Ver resultado':'Siguiente →';
    score.textContent='Pregunta '+(position+1)+' de '+deck.length+' · '+correct+' aciertos';
   });options.appendChild(b);
  });
 }
 function start(){deck=shuffle(words);position=0;correct=0;next.textContent='Siguiente →';show();}
 next.addEventListener('click',()=>{position++;show();});
 root.querySelector('[data-vc="restart"]').addEventListener('click',start);
 root.querySelector('[data-vc="hear"]').addEventListener('click',()=>{
  if(position>=deck.length||!('speechSynthesis'in window))return;
  const utter=new SpeechSynthesisUtterance(deck[position].en);utter.lang='en-US';utter.rate=.85;
  speechSynthesis.cancel();speechSynthesis.speak(utter);
 });
 const style=document.createElement('style');
 style.textContent='.vocab-challenge{padding:18px;margin:20px 0;border:1px solid #bacce2;border-radius:16px;background:var(--surface,#f7f9fc)}.vocab-challenge h3{margin-top:0}.vc-score{font-weight:600}.vc-question{font-size:1.45rem;font-weight:700;margin:14px 0}.vc-options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.vc-options button,.vc-actions button{border:1px solid #7891af;background:transparent;color:inherit;border-radius:10px;padding:12px;text-align:left;cursor:pointer}.vc-options button:disabled{cursor:default}.vc-options .vc-correct{background:#d5f4e1;color:#155536;border-color:#16804b}.vc-options .vc-wrong{background:#ffe2e2;color:#802222;border-color:#c44848}.vc-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}.vc-actions button:disabled{opacity:.5}.vc-feedback{min-height:1.5em;font-weight:600}@media(max-width:560px){.vc-options{grid-template-columns:1fr}}';
 document.head.appendChild(style);start();
})();