<link rel="stylesheet" href="/assets/profesor.css?v=1">
<link rel="stylesheet" href="/assets/profesor-dialogo.css?v=2">
<section class="wrap page professor-page pd-page">
<header class="pd-head">
  <div>
    <p class="eyebrow">MYTEACHER · PROFESOR CON IA</p>
    <h1>Tu profesor de inglés</h1>
  </div>
  <div class="pd-toolbar">
    <label>Nivel
      <select id="pd-level"><?php foreach (['A1'=>'A1 · Inicial','A2'=>'A2 · Básico','B1'=>'B1 · Intermedio','B2'=>'B2 · Intermedio alto','C1'=>'C1 · Avanzado','C2'=>'C2 · Maestría'] as $k=>$v): ?><option value="<?= $k ?>"<?= $k==='A2'?' selected':'' ?>><?= e($v) ?></option><?php endforeach; ?></select>
    </label>
    <label>Modo
      <select id="pd-scenario"><?php foreach (TUTOR_SCENARIOS as $k=>[$label]): ?><option value="<?= e($k) ?>"><?= e($label) ?></option><?php endforeach; ?></select>
    </label>
    <button class="btn btn-ghost" type="button" id="pd-new">＋ Nueva conversación</button>
  </div>
</header>
<div class="pd-options">
  <label><input type="checkbox" id="pd-voice" checked> 🔊 Leer respuestas en voz alta</label>
  <label><input type="checkbox" id="pd-auto"> 🎙️ Conversación por voz (el micrófono se abre solo)</label>
  <label><input type="checkbox" id="pd-translate"> Mostrar traducción</label>
</div>
<div id="pd-chat" class="pd-chat" role="log" aria-label="Conversación con el profesor" aria-live="polite"></div>
<form id="pd-form" class="pd-composer">
  <label class="pd-sr" for="pd-input">Tu mensaje</label>
  <textarea id="pd-input" rows="1" maxlength="1500" placeholder="Escribí en inglés o preguntá lo que quieras… (Enter envía, Shift+Enter nueva línea)"></textarea>
  <button class="pd-icon" type="button" id="pd-mic" aria-pressed="false" title="Hablar">🎙️</button>
  <button class="pd-icon pd-send" type="submit" id="pd-send" title="Enviar">➤</button>
  <button class="pd-icon pd-stop" type="button" id="pd-stop" title="Detener respuesta" hidden>■</button>
</form>
<p id="pd-status" class="pd-status" role="status" aria-live="polite"></p>
<p class="fine">Tus mensajes se procesan con un proveedor de IA (OpenAI) para generar la respuesta y no se guardan en tu cuenta. No incluyas datos personales. La IA puede equivocarse y no reemplaza a un docente.</p>
</section>
<script src="/assets/profesor-dialogo.js?v=2" defer></script>
