(()=>{'use strict';
function model(answer){return answer.split(/\s+\/\s+/)[0].trim();}
function wrongs(answer){
 const a=model(answer),c=[];
 function put(s){s=s.trim();if(s&&s.toLowerCase()!==a.toLowerCase()&&!c.some(v=>v.toLowerCase()===s.toLowerCase()))c.push(s);}
 const plain=a.replace(/[.!?]+$/,'');
 if(/^(am|is|are|was|were|do|does|did|has|have|had|a|an|the)$/i.test(plain)){
   const alternatives={am:['is','are'],is:['are','am'],are:['is','am'],was:['were','is'],were:['was','are'],do:['does','did'],does:['do','did'],did:['do','does'],has:['have','had'],have:['has','had'],had:['has','have'],a:['an','the'],an:['a','the'],the:['a','an']};
   (alternatives[plain.toLowerCase()]||[]).forEach(put);
 }else if(/^[a-z'-]+$/i.test(plain)){
   if(/ies$/i.test(plain)){put(plain.replace(/ies$/i,'ys'));put(plain.replace(/ies$/i,'y'));}
   if(/s$/i.test(plain)){put(plain.replace(/s$/i,''));put(plain+'s');}
   else{put(plain+'s');put(plain+'ed');}
   put('not '+plain);
 }else{
   const changes=[
    [/\bdoesn't\b/i,"don't"],[/\bdon't\b/i,"doesn't"],[/\bdoes\b/i,'do'],[/\bdo\b/i,'does'],
    [/\bare\b/i,'is'],[/\bis\b/i,'are'],[/\bam\b/i,'is'],[/\bwas\b/i,'were'],
    [/\bworks\b/i,'work'],[/\bwork\b/i,'works'],[/\bstudies\b/i,'study'],
    [/\bstudy\b/i,'studies'],[/\blive\b/i,'lives'],[/\blives\b/i,'live'],
    [/\bhas\b/i,'have'],[/\bhave\b/i,'has'],[/\btheir\b/i,'there']
   ];
   changes.forEach(([re,to])=>{if(re.test(a))put(a.replace(re,to));});
   const words=a.split(/\s+/);
   if(words.length>2){const swapped=words.slice();[swapped[0],swapped[1]]=[swapped[1],swapped[0]];put(swapped.join(' '));}
   put(a.replace(/([.!?])?$/,' not$1'));
   if(words.length>3){const cut=words.slice();cut.splice(1,1);put(cut.join(' '));}
 }
 return c.slice(0,2);
}
document.querySelectorAll('[data-book-quiz] .book-quiz-question').forEach((card,index)=>{
 const answer=card.dataset.answer||'',reference=model(answer),alternatives=wrongs(answer),slot=card.querySelector('.book-quiz-options'),check=card.querySelector('.book-quiz-check'),retry=card.querySelector('.book-quiz-retry'),feedback=card.querySelector('.book-quiz-feedback');
 if(!reference||alternatives.length<2)return;
 const options=[reference,...alternatives];const rotation=index%3;
 const ordered=options.slice(rotation).concat(options.slice(0,rotation));
 const groupName='book-quiz-'+index;
 ordered.forEach(option=>{
   const label=document.createElement('label');label.className='book-quiz-choice';
   const input=document.createElement('input');input.type='radio';input.name=groupName;input.value=option;
   const span=document.createElement('span');span.textContent=option;
   label.append(input,span);slot.append(label);
   input.addEventListener('change',()=>{check.disabled=false;feedback.textContent='';});
 });
 check.addEventListener('click',()=>{
   const selected=slot.querySelector('input:checked');if(!selected)return;
   const ok=selected.value===reference;
   feedback.textContent=ok?'¡Correcto! Consultá la solución comentada para entender por qué.':'Todavía no. Revisá la regla y probá otra opción.';
   feedback.classList.toggle('is-correct',ok);feedback.classList.toggle('is-wrong',!ok);
   if(ok){slot.querySelectorAll('input').forEach(i=>i.disabled=true);check.disabled=true;retry.hidden=true;}else retry.hidden=false;
 });
 retry.addEventListener('click',()=>{slot.querySelectorAll('input').forEach(i=>{i.checked=false;i.disabled=false;});check.disabled=true;retry.hidden=true;feedback.textContent='';feedback.classList.remove('is-wrong','is-correct');});
});
})();
