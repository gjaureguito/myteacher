<?php
require dirname(__DIR__).'/src/bootstrap.php';
$u=require_login();
$pageTitle='Profesor virtual';
$unitId=(int)($_GET['unit']??0);
$unit=$unitId?q_one('SELECT id,title,summary,level_code,position FROM units WHERE id=? AND is_published=1',[$unitId]):null;
if($unit && !can_access_unit($u,$unit))$unit=null;
require ROOT.'/src/views/layout_top.php';
?>
<link rel="stylesheet" href="/assets/tutor.css?v=1">
<section class="wrap tutor-page">
<p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / Profesor virtual</p>
<header><p class="eyebrow">ASISTENCIA PERSONALIZADA</p><h1>Profesor virtual de inglés</h1><p>Preguntá sobre gramática, vocabulario y ejemplos de la lección. Las explicaciones de IA complementan la teoría y pueden contener errores.</p></header>
<?php if($unit): ?><p class="tutor-context">Lección: <strong><?=e($unit['title'])?></strong> · <a href="/unit.php?id=<?=(int)$unit['id']?>">Volver a la teoría</a></p><?php endif; ?>
<div class="tutor-layout">
<div class="tutor-panel">
<h2>Hacé tu consulta</h2>
<div class="tutor-examples"><button type="button" data-tutor-prompt="Explicame la regla principal de esta lección, con excepciones.">Explicar la regla</button><button type="button" data-tutor-prompt="Dame tres ejemplos cotidianos en inglés y su traducción.">Ver ejemplos</button><button type="button" data-tutor-prompt="Prepará un ejercicio breve sobre este tema y explicá su respuesta.">Ejercicio personalizado</button></div>
<div id="tutor-chat" class="tutor-chat" role="log" aria-live="polite"><p class="tutor-bubble">¡Hola! Soy el profesor virtual. ¿Qué tema de inglés querés estudiar?</p></div>
<form id="tutor-form" data-unit="<?=(int)($unit['id']??0)?>">
<label for="tutor-question">Tu pregunta</label>
<textarea id="tutor-question" rows="3" maxlength="700" required placeholder="Ej.: ¿Cuándo uso present perfect y cuándo past simple?"></textarea>
<div class="tutor-actions"><span id="tutor-remaining" role="status">Consultas diarias limitadas</span><button class="btn" type="submit">Preguntar</button></div></form></div>
<aside class="tutor-aside"><h2>Cómo aprovecharlo</h2><p>Primero estudiá el capítulo y después preguntá sobre las reglas o solicitá ejemplos.</p><p>El profesor virtual es un complemento y puede equivocarse. Compará sus respuestas con el material de la unidad.</p><p>La IA funciona cuando el administrador configure un modelo privado de Ollama en Railway.</p><p>No compartas datos personales o contraseñas.</p></aside>
</div></section>
<script src="/assets/tutor.js?v=1" defer></script>
<?php require ROOT.'/src/views/layout_bottom.php'; ?>
