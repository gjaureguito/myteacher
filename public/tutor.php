<?php
require dirname(__DIR__).'/src/bootstrap.php';
require_login();
$pageTitle='Diccionario y glosario';
require ROOT.'/src/views/layout_top.php';
?>
<link rel="stylesheet" href="/assets/tutor.css?v=4">
<section class="wrap tutor-page">
<p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / Diccionario con IA</p>
<header><p class="eyebrow">DICCIONARIO PERSONAL · INGLÉS Y ESPAÑOL</p><h1>Diccionario con IA</h1><p>Traducí automáticamente palabras y expresiones breves y guardalas en tu glosario personal. La traducción se puede editar.</p></header>
<div class="tutor-layout">
<section class="tutor-panel">
<h2>¿Qué significa?</h2>
<form id="dictionary-form">
<div class="tutor-input-group"><label for="dictionary-term">Palabra o frase corta</label><textarea id="dictionary-term" maxlength="220" rows="3" placeholder="Escribí una palabra o expresión en inglés, por ejemplo: look after"></textarea></div>
<div class="tutor-actions"><span>Hasta 220 caracteres por búsqueda</span><button type="submit" class="btn">Traducir y guardar</button></div>
</form>
<p id="dictionary-status" role="status"></p>
<div id="dictionary-result" hidden class="dictionary-result">
<h3 id="dictionary-heading"></h3><div id="dictionary-definition" class="dictionary-definition"></div>
<div class="tutor-export-tools"><button type="button" class="tutor-outline" id="dictionary-hear">🔊 Escuchar término</button><button type="button" class="tutor-outline" id="dictionary-save">＋ Guardar en mi glosario</button></div>
<label for="dictionary-translation">Traducción breve para el glosario (editable)</label><input id="dictionary-translation" maxlength="800" placeholder="Traducción al español">
<label for="dictionary-context">Notas o ejemplo (opcional)</label><textarea id="dictionary-context" rows="3" maxlength="1000"></textarea>
<label for="dictionary-category">Categoría</label><select id="dictionary-category"><option>General</option><option>Trabajo</option><option>Viajes</option><option>Vida cotidiana</option><option>Gramática</option></select>
</div>
</section>
<aside class="tutor-aside"><h2>Tu vocabulario</h2><p>Seleccioná una palabra o frase corta en los textos de MyTeacher. Hacé clic derecho y elegí <strong>Agregar al glosario</strong>.</p><p>Las entradas se ordenan alfabéticamente y quedan asociadas a tu cuenta.</p><p><a href="/glossary.php" class="btn">Abrir mi glosario</a></p><p>Los significados generados por IA pueden requerir revisión según el contexto.</p></aside>
</div></section>
<script src="/assets/dictionary.js?v=3" defer></script>
<?php require ROOT.'/src/views/layout_bottom.php'; ?>