<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
$student = require_login();
$lesson = (($_GET['lesson'] ?? '1') === '2') ? 2 : 1;
$videoId = $lesson === 2 ? 'k8PJhpSCiSs' : 'U08KdJ52E2M';
$videoTitle = $lesson === 2 ? 'New video · Listening A1' : 'The Wrong Coffee';
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title>Video Learning A1 | MyTeacher</title>
<style>
:root{--ink:#132543;--blue:#2656cb;--muted:#62748f;--line:#dce5f2;--bg:#f4f7fc}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.55 system-ui,-apple-system,"Segoe UI",sans-serif}
.wrapper{max-width:1500px;margin:auto;padding:20px 28px 48px}.top{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap}
.brand{font-size:23px;font-weight:850;text-decoration:none;color:var(--ink)}.link{color:var(--blue);font-size:14px;font-weight:700}
h1{font-size:clamp(28px,3.5vw,43px);line-height:1.15;margin:22px 0 7px}p{color:var(--muted);margin:6px 0 16px}
.grid{display:grid;grid-template-columns:minmax(460px,.95fr) minmax(470px,1.05fr);gap:22px;align-items:start;margin-top:20px}
.card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:22px;box-shadow:0 12px 35px #1b32610a}
.pill{display:inline-block;background:#eaf0ff;color:#234dad;border-radius:999px;padding:5px 12px;font-size:13px;font-weight:750}
.player{width:min(100%,470px);margin:18px auto 8px;aspect-ratio:9/16;max-height:76vh;min-height:500px;background:#0d1729;border-radius:16px;overflow:hidden}
.player iframe{width:100%;height:100%;border:0;display:block}
h2{font-size:23px;margin:12px 0}h3{font-size:18px;line-height:1.35;margin:14px 0}
button{font:inherit;cursor:pointer;border:1px solid #b7c9e7;border-radius:12px;padding:11px 15px;background:white;color:var(--ink)}
button:focus-visible,a:focus-visible{outline:3px solid #8ab0ff;outline-offset:3px}
button.primary{background:var(--blue);color:white;border-color:var(--blue);font-weight:750}
button.choice{display:block;width:100%;text-align:left;margin:9px 0;background:#f9fbff;font-weight:600}
button.choice[aria-pressed=true]{border-color:var(--blue);background:#e9f0ff;box-shadow:inset 0 0 0 1px var(--blue)}
button:disabled{opacity:.48;cursor:not-allowed}
.toggle{display:flex;align-items:center;gap:10px;font-weight:650}.toggle input{width:21px;height:21px;accent-color:var(--blue)}
.bar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}.question{border-top:1px solid var(--line);margin-top:17px;padding-top:16px}
.es{display:none;color:#667b99;font-size:14px;font-weight:500}.translated .es{display:block}
.words{display:flex;flex-wrap:wrap;gap:9px}.words button{font-weight:700}
.word-slot{min-height:50px;background:#f5f8ff;border:1px dashed #a8b9d5;border-radius:12px;padding:10px 14px;margin:12px 0}
.feedback{min-height:34px;font-weight:750;color:#1e6b45}.feedback.bad{color:#ab3449}.info{padding:12px 15px;background:#f1f6ff;border-radius:12px;font-size:14px;color:#425a7c}
@media(max-width:1050px){.grid{grid-template-columns:1fr}.player{width:min(100%,520px);max-height:none;min-height:0}.wrapper{padding:15px}}
</style></head><body><main class="wrapper">
<div class="top"><a class="brand" href="/dashboard.php">MyTeacher</a><a class="link" href="/dashboard.php">← Back to dashboard</a></div>
<h1>Video Learning · A1</h1><p>Watch, listen and choose. No typing required. <span class="es" style="display:inline">Mirá, escuchá y elegí. Sin escribir.</span></p>
<nav aria-label="Video lessons" style="display:flex;gap:12px;flex-wrap:wrap;margin:14px 0"><a class="pill" href="/video-learning.php?lesson=1" <?= $lesson === 1 ? 'aria-current="page"' : '' ?>>Lesson 01 · The Wrong Coffee</a><a class="pill" href="/video-learning.php?lesson=2" <?= $lesson === 2 ? 'aria-current="page"' : '' ?>>Lesson 02 · New video</a></nav>
<div class="grid">
<section class="card"><span class="pill">Lesson <?= $lesson === 2 ? '02' : '01' ?> · <?= e($videoTitle) ?></span><h2>Watch the scene</h2>
<p>Listen carefully. Replay whenever you need.</p>
<div class="player"><iframe title="The Wrong Coffee · YouTube Short" src="https://www.youtube-nocookie.com/embed/<?= e($videoId) ?>" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>
<p><a class="link" href="https://www.youtube.com/shorts/<?= e($videoId) ?>" target="_blank" rel="noopener noreferrer">Open on YouTube</a></p>
</section>
<?php if ($lesson === 2): ?>
<section class="card"><span class="pill">Listening · Lesson 02</span><h2>Watch and listen</h2><p>Play the video. Listen once without subtitles, then replay it and identify familiar words.</p><p><strong>Español:</strong> Mirá el video, escuchá una vez sin subtítulos y volvé a reproducirlo para reconocer palabras conocidas.</p><div class="info">Interactive questions for this video are awaiting a verified transcript. No dialogue or answers have been invented.</div><p><a href="/video-learning.php?lesson=1">Practice with Lesson 01 →</a></p></section>
<?php else: ?>
<section class="card" id="exercise"><div class="bar"><span class="pill">Interactive practice</span><label class="toggle"><input id="translation" type="checkbox"> Español / Translate</label></div>
<h2>What do you see?</h2><p>Choose the correct answer for each question. <span class="es">Elegí la respuesta correcta para cada pregunta.</span></p>
<div id="questions"></div>
<div class="question"><h3>Complete the sentence</h3><div class="es">Completá la oración eligiendo una palabra.</div><p>The customer is in a <strong id="slot">_____</strong>.</p><div class="es">El cliente está en una cafetería.</div><div class="words" id="words"></div></div>
<div class="bar" style="margin-top:22px"><button id="retry" type="button">Start again <span class="es">Reiniciar</span></button><button id="check" class="primary" type="button">Check answers <span class="es">Comprobar</span></button></div>
<p id="feedback" class="feedback" role="status" aria-live="polite"></p>
<div class="info">These questions test visible details and basic café vocabulary, not a verified transcript. Listening-specific dialogue questions will be added after the transcript is reviewed.<span class="es"> Estas preguntas evalúan detalles visibles y vocabulario básico, no una transcripción verificada.</span></div>
</section>
<?php endif; ?>
</div></main>
<?php if ($lesson === 1): ?>
<script>
const items=[
{q:"Where does the scene take place?",es:"¿Dónde sucede la escena?",options:[["At a coffee shop","En una cafetería"],["At a train station","En una estación de tren"],["At a hospital","En un hospital"]],answer:0},
{q:"Who is behind the counter?",es:"¿Quién está detrás del mostrador?",options:[["A police officer","Un policía"],["A barista","Un barista"],["A teacher","Un profesor"]],answer:1},
{q:"What is unusually large in the scene?",es:"¿Qué objeto es inusualmente grande en la escena?",options:[["A coffee cup","Una taza de café"],["A suitcase","Una valija"],["A bicycle","Una bicicleta"]],answer:0},
{q:"How many people are clearly visible at the counter?",es:"¿Cuántas personas se ven claramente junto al mostrador?",options:[["One","Una"],["Two","Dos"],["Four","Cuatro"]],answer:1}
];
const choices=new Array(items.length).fill(null);let chosenWord=null;
const questions=document.getElementById('questions'),words=document.getElementById('words'),feedback=document.getElementById('feedback');
function draw(){questions.innerHTML='';items.forEach((item,i)=>{const d=document.createElement('section');d.className='question';const h=document.createElement('h3');h.textContent=(i+1)+'. '+item.q;d.append(h);const t=document.createElement('span');t.className='es';t.textContent=item.es;d.append(t);item.options.forEach((option,j)=>{const b=document.createElement('button');b.type='button';b.className='choice';b.setAttribute('aria-pressed',String(choices[i]===j));b.textContent=option[0];const trans=document.createElement('span');trans.className='es';trans.textContent=option[1];b.append(trans);b.addEventListener('click',()=>{choices[i]=j;feedback.textContent='';draw();});d.append(b);});questions.append(d);});words.innerHTML='';[['café','cafetería'],['airport','aeropuerto'],['school','escuela']].forEach(([word,es])=>{const b=document.createElement('button');b.type='button';b.setAttribute('aria-pressed',String(chosenWord===word));b.textContent=word;const t=document.createElement('span');t.className='es';t.textContent=es;b.append(t);b.onclick=()=>{chosenWord=word;document.getElementById('slot').textContent=word;feedback.textContent='';draw();};words.append(b);});document.body.classList.toggle('translated',document.getElementById('translation').checked);}
document.getElementById('translation').addEventListener('change',draw);
document.getElementById('check').addEventListener('click',()=>{if(choices.includes(null)||!chosenWord){feedback.className='feedback bad';feedback.textContent='Complete all questions first.'+(document.getElementById('translation').checked?' Completá todas las preguntas.':'');return;}const correct=choices.filter((a,i)=>a===items[i].answer).length+(chosenWord==='café'?1:0);feedback.className='feedback '+(correct===5?'':'bad');feedback.textContent=correct+'/5 correct. '+(correct===5?'Excellent!':'Try again and replay the video.');});
document.getElementById('retry').onclick=()=>{choices.fill(null);chosenWord=null;document.getElementById('slot').textContent='_____';feedback.textContent='';draw();};
draw();
</script>
<?php endif; ?>
</body></html>