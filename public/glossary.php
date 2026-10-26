<?php
require_once __DIR__ . '/../src/bootstrap.php';
require_login();
$pageTitle = 'Mi glosario';
require ROOT . '/src/views/layout_top.php';
?>
<section class="wrap lex-page" aria-labelledby="lex-title">
  <p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / Mi glosario</p>
  <header class="lex-head"><p class="eyebrow">VOCABULARIO PERSONAL</p><h1 id="lex-title">Mi glosario de inglés</h1><p>Buscá traducciones, guardá palabras con contexto y repasá el vocabulario que necesitás recordar.</p></header>
  <div class="lex-layout">
    <section class="lex-panel"><h2>Buscar y agregar palabras</h2>
      <form id="lex-form" class="lex-form"><label for="lex-word">Palabra o expresión en inglés o español</label><input id="lex-word" maxlength="120" required placeholder="Ej.: maintenance, look after"><label for="lex-direction">Dirección de traducción</label><select id="lex-direction"><option value="auto">Automática</option><option value="es-en">Español → Inglés</option><option value="en-es">Inglés → Español</option></select><button class="btn" type="submit">Buscar traducción</button></form>
      <p id="lex-result" role="status" aria-live="polite"></p>
      <form id="lex-save-form" hidden>
        <label for="lex-translation">Traducción (podés corregirla)</label><input id="lex-translation" maxlength="220" required>
        <label for="lex-context">Ejemplo o frase de contexto (opcional)</label><textarea id="lex-context" maxlength="500" rows="3" placeholder="Ej.: The maintenance team checks the equipment."></textarea>
        <label for="lex-tag">Categoría</label><select id="lex-tag"><option>General</option><option>Trabajo</option><option>Viajes</option><option>Vida cotidiana</option><option>Gramática</option></select>
        <button type="submit" class="btn">Guardar en mi glosario</button>
        <button type="button" class="btn btn-ghost" id="lex-pronounce">🔊 Pronunciar</button>
      </form>
      <p class="lex-note">La traducción automática es orientativa. Comprobá el sentido según el contexto antes de guardarla. Las palabras y frases se guardan en tu cuenta y se muestran por orden alfabético. El glosario anterior del navegador se importará automáticamente si existe. También podés exportar una copia de seguridad.</p>
    </section>
    <section class="lex-panel"><div class="lex-row"><h2>Mis palabras <small id="lex-count"></small></h2><button class="btn btn-ghost btn-sm" id="lex-export">Exportar CSV</button></div>
      <label for="lex-filter">Filtrar mi vocabulario</label><input id="lex-filter" placeholder="Buscar palabra, traducción o tema">
      <div id="lex-list" class="lex-list"></div>
      <h3>Repaso con tarjetas</h3><button class="btn btn-sm" id="lex-next">Mostrar palabra</button><div id="lex-card" aria-live="polite">Guardá palabras para empezar a practicar.</div><button class="btn btn-ghost btn-sm" id="lex-reveal">Mostrar traducción</button>
    </section>
  </div>
</section>
<link rel="stylesheet" href="/assets/glossary.css?v=1">
<script src="/assets/glossary.js?v=4" defer></script>
<?php require ROOT . '/src/views/layout_bottom.php'; ?>
