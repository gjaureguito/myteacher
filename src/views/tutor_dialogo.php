<link rel="stylesheet" href="/assets/profesor.css?v=1">
<link rel="stylesheet" href="/assets/profesor-dialogo.css?v=1">
<section class="wrap page professor-page pd-page">
<p class="eyebrow">MYTEACHER · PROFESOR CON IA</p>
<h1>Conversá en inglés con tu profesor</h1>
<p>Elegí tu nivel y una situación, tocá <strong>Empezar diálogo</strong> y respondé hablando o escribiendo. El profesor te contesta en voz alta, te corrige con una explicación breve en español y sigue la conversación.</p>
<div class="pd-setup">
  <label>Nivel
    <select id="pd-level"><?php foreach (['A1'=>'A1 · Inicial','A2'=>'A2 · Básico','B1'=>'B1 · Intermedio','B2'=>'B2 · Intermedio alto','C1'=>'C1 · Avanzado','C2'=>'C2 · Maestría'] as $k=>$v): ?><option value="<?= $k ?>"<?= $k==='A2'?' selected':'' ?>><?= e($v) ?></option><?php endforeach; ?></select>
  </label>
  <label>Situación
    <select id="pd-scenario"><?php foreach (TUTOR_SCENARIOS as $k=>[$label]): ?><option value="<?= e($k) ?>"><?= e($label) ?></option><?php endforeach; ?></select>
  </label>
  <button class="btn" type="button" id="pd-start">▶ Empezar diálogo</button>
</div>
<div class="pd-options">
  <label><input type="checkbox" id="pd-auto" checked> Modo conversación: el micrófono se abre solo cuando el profesor termina de hablar</label>
  <label><input type="checkbox" id="pd-translate"> Mostrar traducción al español</label>
  <label><input type="checkbox" id="pd-voice" checked> Leer en voz alta las respuestas</label>
</div>
<div id="pd-chat" class="pd-chat" role="log" aria-label="Diálogo con el profesor" aria-live="polite">
  <p class="pd-empty">Tu diálogo va a aparecer acá.</p>
</div>
<form id="pd-form" class="pd-form" hidden>
  <button class="btn pd-mic" type="button" id="pd-mic" aria-pressed="false">🎙️ Hablar</button>
  <label class="pd-input-label" for="pd-input">Tu respuesta en inglés</label>
  <input id="pd-input" maxlength="600" autocomplete="off" placeholder="Escribí tu respuesta o tocá Hablar…">
  <button class="btn" type="submit" id="pd-send">Enviar</button>
  <button class="btn btn-ghost" type="button" id="pd-end">Terminar</button>
</form>
<p id="pd-status" class="pd-status" role="status" aria-live="polite"></p>
<p class="fine">Tus mensajes se procesan con Gemini (Google) para generar la respuesta y no se guardan en tu cuenta. No incluyas datos personales. La IA puede equivocarse y no reemplaza a un docente.</p>
</section>
<script src="/assets/profesor-dialogo.js?v=1" defer></script>
