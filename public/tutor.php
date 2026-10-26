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
<h2>Conversá con tu profesor de inglés</h2>
<div class="tutor-examples"><button type="button" data-tutor-prompt="Explicame la regla principal de esta lección, con excepciones.">Explicar la regla</button><button type="button" data-tutor-prompt="Dame tres ejemplos cotidianos en inglés y su traducción.">Ver ejemplos</button><button type="button" data-tutor-prompt="Prepará un ejercicio breve sobre este tema y explicá su respuesta.">Ejercicio personalizado</button></div>
<div id="tutor-chat" class="tutor-chat" role="log" aria-live="polite"><p class="tutor-bubble">¡Hola! Soy el profesor virtual. ¿Qué tema de inglés querés estudiar?</p></div>
<form id="tutor-form" data-unit="<?=(int)($unit['id']??0)?>">
<label for="tutor-question">Tu pregunta</label>
<textarea id="tutor-question" rows="3" maxlength="700" required placeholder="Ej.: ¿Cuándo uso present perfect y cuándo past simple?"></textarea>
<div class="tutor-actions"><span id="tutor-remaining" role="status">Consultas diarias limitadas</span><button class="btn" type="submit">Preguntar</button></div><div class="tutor-examples"><label for="tutor-language">Idioma del dictado</label><select id="tutor-language"><option value="en-US" selected>English</option><option value="es-AR">Español</option></select><label for="tutor-level">Nivel</label><select id="tutor-level"><option>A1</option><option>A2</option><option>B1</option><option>B2</option><option>C1</option><option>C2</option></select><button type="button" id="tutor-mic">🎙️ Iniciar conversación</button><button type="button" id="tutor-say">🔊 Escuchar respuesta</button><label><input type="checkbox" id="tutor-auto-voice" checked> Leer respuestas en voz alta</label></div><p class="muted" style="font-size:.88rem">Presioná «Iniciar conversación» una vez. Hablá, escuchá al profesor y volvé a responder. «Terminar conversación» detiene el diálogo. El micrófono debe funcionar en el navegador; si Chrome lo bloquea, podés usar el chat escrito.</p></form></div>
<aside class="tutor-aside"><h2>Cómo aprovecharlo</h2><p>Primero estudiá el capítulo y después preguntá sobre las reglas o solicitá ejemplos.</p><p>El profesor virtual es un complemento y puede equivocarse. Compará sus respuestas con el material de la unidad.</p><p>El profesor usa la IA privada alojada en Railway cuando está configurada; la primera respuesta puede demorar si el modelo está iniciando.</p><p>No compartas datos personales o contraseñas.</p></aside>
</div></section>
<script src="/assets/tutor.js?v=5" defer></script>
<?php require ROOT.'/src/views/layout_bottom.php'; ?>
