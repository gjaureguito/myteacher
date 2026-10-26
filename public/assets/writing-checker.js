(() => {
 'use strict';
 const rules=[
 {id:'i-cap',re:/(^|[.!?]\s+)i(?=\s|['’]m\b)/g,label:'Pronombre I',why:'En inglés, el pronombre personal I siempre va con mayúscula.',suggest:'Escribí I en lugar de i.'},
 {id:'dont',re:/\bdont\b/gi,label:'Contracción don’t',why:'La forma negativa abreviada necesita apóstrofo.',suggest:'Escribí don’t.'},
 {id:'doesnt',re:/\bdoesnt\b/gi,label:'Contracción doesn’t',why:'Does not se contrae como doesn’t.',suggest:'Escribí doesn’t.'},
 {id:'cant',re:/\bcant\b/gi,label:'Contracción can’t',why:'Can not se expresa habitualmente como cannot o can’t.',suggest:'Escribí can’t o cannot.'},
 {id:'im',re:/\bim\b/gi,label:'Contracción I’m',why:'I am se puede contraer a I’m.',suggest:'Escribí I’m.'},
 {id:'he-she',re:/\b(he|she|it)\s+(?:go|work|want|need|think|have)\b/gi,label:'Tercera persona singular',why:'En presente simple la tercera persona singular cambia la forma verbal. Hay excepciones y otros tiempos verbales.',suggest:'Revisá la concordancia (goes, works, wants, needs, thinks, has).'},
 {id:'there',re:/\bthere\s+is\s+(?:many|several|two|three|four|five)\b/gi,label:'There are con plural',why:'Con un sustantivo plural suele usarse there are.',suggest:'Revisá si corresponde there are.'},
 {id:'a-vowel',re:/\ba\s+(?:apple|orange|umbrella|egg|idea|answer|example|office)\b/gi,label:'Artículo a / an',why:'Ante palabras que comienzan con sonido vocálico suele utilizarse an.',suggest:'Revisá si corresponde an apple, an idea o an example.',good:'an apple'},
 {id:'an-consonant',re:/\ban\s+(?:book|car|dog|house|student|teacher|project)\b/gi,label:'Artículo an / a',why:'Ante estas palabras, que comienzan con sonido consonántico, se utiliza a.',suggest:'Revisá a book, a car o a student.',good:'a student'},
 {id:'did-past',re:/\bdid\s+(?:went|saw|bought|took|wrote|made|had|said)\b/gi,label:'Did + verbo base',why:'En preguntas o negaciones con did se utiliza la forma base del verbo.',suggest:'Ejemplo: Did you go? (no Did you went?).',good:'Did you go?'},
 {id:'more-better',re:/\bmore\s+better\b/gi,label:'Comparativo duplicado',why:'Better ya es el comparativo de good; normalmente no lleva more.',suggest:'Escribí better.',good:'a better solution'},
 {id:'much-many',re:/\bmuch\s+(?:people|books|students|problems|questions)\b/gi,label:'Much / many',why:'Con sustantivos contables plurales se suele utilizar many.',suggest:'Ejemplo: many questions.',good:'many questions'},
 {id:'double-space',re:/ {2,}/g,label:'Espacios repetidos',why:'En textos formales se recomienda un solo espacio entre palabras.',suggest:'Eliminá los espacios duplicados.'}
 ];
 const cap=14;
 document.querySelectorAll('.scene-writing-lab').forEach(lab=>{
  const field=lab.querySelector('textarea');
  const actions=lab.querySelector('.sw-actions');
  if(!field||!actions)return;
  const button=document.createElement('button');
  button.type='button';button.textContent='🔎 Revisar gramática básica';button.className='sw-grammar-trigger';
  const results=document.createElement('div');results.className='sw-grammar-results';results.setAttribute('aria-live','polite');
  actions.append(button);lab.querySelector('.sw-feedback')?.after(results);
  button.addEventListener('click',()=>{
   const text=field.value.trim();results.replaceChildren();
   if(!text){results.textContent='Escribí primero una respuesta para analizar.';return;}
   const found=[];
   for(const rule of rules){
    rule.re.lastIndex=0;
    const match=rule.re.exec(text);
    if(match)found.push({...rule,example:match[0].trim()});
   }
   const title=document.createElement('strong');
   title.textContent=found.length?'Sugerencias para revisar ('+found.length+')':'Sin coincidencias con las reglas básicas disponibles';
   results.append(title);
   if(!found.length){const note=document.createElement('p');note.textContent='Esto no significa que el texto sea correcto. Esta herramienta solo revisa un conjunto limitado de patrones.';results.append(note);return;}
   const list=document.createElement('ol');
   found.slice(0,cap).forEach(r=>{
    const li=document.createElement('li');const heading=document.createElement('strong');heading.textContent=r.label+' · «'+r.example+'»';
    const why=document.createElement('p');why.textContent=r.why;
    const suggestion=document.createElement('p');suggestion.textContent='Sugerencia: '+r.suggest;
    li.append(heading,why,suggestion);
    if(r.good){const good=document.createElement('p');good.textContent='Ejemplo correcto: '+r.good;li.append(good);}
    list.append(li);
   });results.append(list);
   const note=document.createElement('p');note.textContent='Estas sugerencias son orientativas: algunas coincidencias pueden ser válidas según el contexto. Revisá antes de modificar tu texto.';results.append(note);
  });
 });
 const style=document.createElement('style');style.textContent='.sw-grammar-results{margin-top:12px;line-height:1.5}.sw-grammar-results ol{padding-left:22px}.sw-grammar-results li{margin:10px 0}.sw-grammar-results p{margin:4px 0}';document.head.append(style);
})();