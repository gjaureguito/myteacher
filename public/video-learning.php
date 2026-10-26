<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
$student = require_login();
$videoId = 'U08KdJ52E2M';
// Primera lección piloto: no se presentan transcripciones ni respuestas inventadas.
$videoUrl = 'https://www.youtube.com/watch?v=' . $videoId;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Video Learning de MyTeacher: escuchá inglés real y practicá a tu ritmo.">
<title>Video Learning A1 | MyTeacher</title>
<style>
:root{color-scheme:light;--ink:#14233d;--blue:#2458d3;--muted:#61728c;--line:#e1e7f2;--surface:#fff;--bg:#f4f7fc}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.6 system-ui,-apple-system,Segoe UI,sans-serif}
a{color:var(--blue)}.shell{max-width:1150px;margin:auto;padding:24px}.header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:26px}
.brand{font-weight:800;font-size:22px;text-decoration:none;color:var(--ink)}.back{font-size:14px;text-decoration:none;font-weight:650}
.eyebrow{font-size:12px;letter-spacing:.12em;text-transform:uppercase;font-weight:800;color:var(--blue)}
h1{font-size:clamp(30px,4vw,48px);line-height:1.12;margin:8px 0}p{margin:10px 0;color:var(--muted)}
.layout{display:grid;grid-template-columns:minmax(270px,390px) minmax(0,1fr);gap:24px;align-items:start;margin-top:25px}
.panel{border:1px solid var(--line);background:var(--surface);border-radius:20px;padding:24px;box-shadow:0 9px 28px #19356409}
.player{aspect-ratio:9/16;width:100%;overflow:hidden;background:#121c2d;border-radius:16px}
.player iframe{display:block;width:100%;height:100%;border:0}
.step{display:inline-flex;background:#eaf1ff;color:#214cae;border-radius:20px;padding:4px 10px;font-size:13px;font-weight:750}
h2{font-size:22px;line-height:1.3;margin:14px 0}h3{font-size:17px;margin:22px 0 6px}
textarea{width:100%;min-height:100px;resize:vertical;border:1px solid #cbd6e7;border-radius:12px;padding:14px;font:inherit;color:var(--ink)}
textarea:focus-visible,a:focus-visible,button:focus-visible{outline:3px solid #89b0ff;outline-offset:3px}
button{background:var(--blue);color:white;border:0;border-radius:12px;padding:12px 20px;font:inherit;font-weight:750;cursor:pointer}
button:hover{background:#1847b4}.help{font-size:14px}.status{color:#215735;font-weight:650;min-height:24px}
.note{background:#f3f7ff;border:1px solid #dce7ff;border-radius:12px;padding:13px 16px;color:#375071;font-size:14px;margin-top:18px}
@media(max-width:760px){.shell{padding:16px}.layout{grid-template-columns:1fr}.panel{padding:18px}.player{max-width:380px;margin:auto}.header{margin-bottom:16px}}
</style>
</head>
<body>
<main class="shell">
<header class="header"><a href="/dashboard.php" class="brand">MyTeacher</a><a class="back" href="/dashboard.php">← Volver a mis niveles</a></header>
<div class="eyebrow">Video Learning · Nivel A1</div>
<h1>Aprendé inglés con videos reales</h1>
<p>Mirá, escuchá y practicá. Podés repetir el video todas las veces que necesites.</p>
<div class="layout">
<section class="panel" aria-label="Video de la primera lección">
<span class="step">Lección piloto 01</span>
<h2>Escuchá con atención</h2>
<div class="player"><iframe src="https://www.youtube-nocookie.com/embed/U08KdJ52E2M" title="Video de inglés: primera lección A1" loading="lazy" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
<p class="help">Si el creador del video no permite insertarlo, <a href="<?= htmlspecialchars($videoUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">abrilo directamente en YouTube</a>.</p>
</section>
<section class="panel">
<span class="step">Práctica de listening</span>
<h2>Primero escuchá, después escribí</h2>
<p>Hacé una primera escucha sin pausar. En la segunda, anotá lo que entendiste. Todavía no hay preguntas puntuadas: estamos preparando los ejercicios basados en la transcripción auténtica.</p>
<label for="words"><h3>1. ¿Qué palabras en inglés reconociste?</h3></label>
<textarea id="words" maxlength="1500" placeholder="Escribí las palabras que escuchaste..."></textarea>
<label for="context"><h3>2. ¿Qué creés que sucede en el video?</h3></label>
<textarea id="context" maxlength="1500" placeholder="Describí la situación en tus palabras..."></textarea>
<label for="difficult"><h3>3. ¿Qué te gustaría volver a escuchar?</h3></label>
<textarea id="difficult" maxlength="1500" placeholder="Anotá la palabra o expresión que te resultó difícil..."></textarea>
<p class="help">Tus notas se guardan únicamente en este navegador por ahora. No forman parte de una evaluación y no se envían al servidor.</p>
<button type="button" id="save">Guardar notas de práctica</button>
<p id="status" class="status" role="status" aria-live="polite"></p>
<div class="note"><strong>Próximo paso:</strong> agregar la transcripción verificada, vocabulario, ejercicios de selección y evaluación integrada con el progreso de MyTeacher.</div>
</section>
</div>
</main>
<script>
(function(){
const key='myteacher-video-a1-U08KdJ52E2M';
const ids=['words','context','difficult'];
try{const saved=JSON.parse(localStorage.getItem(key)||'{}');ids.forEach(id=>{if(typeof saved[id]==='string')document.getElementById(id).value=saved[id];});}catch(e){}
document.getElementById('save').addEventListener('click',function(){
try{const notes={};ids.forEach(id=>notes[id]=document.getElementById(id).value);localStorage.setItem(key,JSON.stringify(notes));document.getElementById('status').textContent='Notas guardadas en este navegador.';}
catch(e){document.getElementById('status').textContent='No se pudieron guardar las notas. Podés copiar el texto para conservarlo.';}
});
})();
</script>
</body></html>