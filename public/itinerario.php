<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/bootstrap.php';
$itinerario = require dirname(__DIR__) . '/content/itinerario_original.php';
function eplan(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?><!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Itinerario académico original | MyTeacher</title>
<style>
:root{font-family:system-ui,-apple-system,Segoe UI,sans-serif;color:#192b43;background:#f4f7fb}
*{box-sizing:border-box}body{margin:0}.wrap{max-width:1100px;margin:0 auto;padding:30px 18px}
header{background:#101f3b;color:#fff;padding:42px 0}header h1{font-size:clamp(2rem,4vw,3rem);margin:8px 0}
header p{max-width:760px;line-height:1.6;color:#dce5f4}
nav a{color:#d7e9ff;margin-right:20px}.chip{display:inline-block;border:1px solid #aebbd1;border-radius:30px;padding:7px 12px;margin:5px}
h2{margin-top:40px}.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(245px,1fr));gap:15px}
article{border:1px solid #dce3ee;background:white;border-radius:14px;padding:17px;box-shadow:0 4px 16px #15294208}
article b{display:block;margin-bottom:9px}article p{color:#53657e;font-size:.9rem;line-height:1.5}
.notice{background:#fff8e7;border-left:4px solid #cf8d25;padding:14px 18px;border-radius:8px;line-height:1.5}
a{color:#1859a7}footer{margin:40px 0;color:#51627a}
</style></head><body>
<header><div class="wrap"><nav><a href="/program.php">Programa actual</a><a href="/">Inicio</a></nav>
<h1>Itinerario académico MyTeacher</h1>
<p>Una hoja de ruta propia para aprender inglés desde A1 hasta C2. La cobertura aborda gramática, vocabulario y comunicación real, con ejercicios, audio, lecturas y producción oral y escrita.</p>
<span class="chip">6 niveles</span><span class="chip">72 módulos planificados</span><span class="chip">Metodología original</span></div></header>
<main class="wrap"><div class="notice"><strong>Plan de ampliación.</strong> Estos 72 módulos son una propuesta editorial y no indican que sus lecciones o actividades ya estén disponibles. Consultá el <a href="/program.php">programa actual</a> para acceder al contenido publicado.</div>
<?php foreach ($itinerario as $nivel => $modulos): ?>
<section id="<?=eplan($nivel)?>"><h2>Nivel <?=eplan($nivel)?></h2><div class="grid">
<?php foreach ($modulos as $i=>$titulo): ?>
<article><b><?=eplan($nivel)?> · Módulo <?=($i+1)?></b><h3><?=eplan($titulo)?></h3>
<p>Secuencia propia de teoría en español, ejemplos originales, diálogo contextualizado, escucha, práctica guiada y evaluación de competencias.</p></article>
<?php endforeach; ?></div></section>
<?php endforeach; ?>
<footer>MyTeacher · Este itinerario no reproduce textos, ejercicios ni índices editoriales de terceros.</footer></main></body></html>