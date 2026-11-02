<link rel="stylesheet" href="/assets/profesor.css?v=1">
<link rel="stylesheet" href="/assets/profesor-clase.css?v=1">
<section class="wrap page professor-page pc-page">
<header class="pc-head">
  <div>
    <p class="eyebrow">MYTEACHER · PROFESOR CON IA</p>
    <h1>Clases de conversación</h1>
    <p class="pc-lead">Tu profesor te guía en diálogos progresivos: él conduce la clase, la cierra y te da una devolución con lo que aprendiste. Cada clase queda guardada en tu historial.</p>
  </div>
  <div class="pc-tabs" role="tablist">
    <button type="button" class="pc-tab is-active" data-tab="ruta" role="tab" aria-selected="true">📚 Mi ruta</button>
    <button type="button" class="pc-tab" data-tab="historial" role="tab" aria-selected="false">🗂️ Mi historial</button>
  </div>
</header>

<!-- Ruta de clases -->
<div id="pc-ruta" class="pc-panel">
  <div class="pc-levelbar">
    <label>Nivel <select id="pc-level"></select></label>
    <button class="btn" type="button" id="pc-next">▶ Empezar la próxima clase</button>
  </div>
  <ol id="pc-lessons" class="pc-lessons"></ol>
</div>

<!-- Historial -->
<div id="pc-historial" class="pc-panel" hidden>
  <ul id="pc-history" class="pc-history"></ul>
</div>

<!-- Clase en curso o guardada -->
<div id="pc-class" class="pc-panel" hidden>
  <div class="pc-classhead">
    <button type="button" class="pc-back" id="pc-back">← Volver</button>
    <div>
      <p class="pc-kicker" id="pc-kicker"></p>
      <h2 id="pc-title"></h2>
      <p id="pc-goal" class="pc-goal"></p>
      <div id="pc-targets" class="pc-targets"></div>
    </div>
  </div>
  <div class="pc-progress"><span id="pc-progress-text"></span><progress id="pc-progress" max="1" value="0"></progress></div>
  <div class="pd-options">
    <label><input type="checkbox" id="pd-voice" checked> 🔊 Leer en voz alta</label>
    <label><input type="checkbox" id="pd-auto"> 🎙️ Responder con voz automáticamente</label>
    <label><input type="checkbox" id="pd-translate"> Mostrar traducción</label>
  </div>
  <div id="pd-chat" class="pd-chat" role="log" aria-label="Clase con el profesor" aria-live="polite"></div>
  <form id="pd-form" class="pd-composer">
    <label class="pd-sr" for="pd-input">Tu respuesta</label>
    <textarea id="pd-input" rows="1" maxlength="800" placeholder="Respondé en inglés… (Enter envía, Shift+Enter nueva línea)"></textarea>
    <button class="pd-icon" type="button" id="pd-mic" aria-pressed="false" title="Hablar">🎙️</button>
    <button class="pd-icon pd-send" type="submit" id="pd-send" title="Enviar">➤</button>
  </form>
  <div class="pc-classactions"><button type="button" class="btn btn-ghost" id="pc-finish">Terminar la clase ahora</button></div>
  <div id="pc-feedback" class="pc-feedback" hidden></div>
  <p id="pd-status" class="pd-status" role="status" aria-live="polite"></p>
</div>
<p class="fine">Tus respuestas se procesan con OpenAI para generar las del profesor. Las clases (diálogo y devolución) se guardan en tu cuenta para que puedas repasarlas. No incluyas datos personales. La IA puede equivocarse y no reemplaza a un docente.</p>
</section>
<script src="/assets/profesor-clase.js?v=1" defer></script>
