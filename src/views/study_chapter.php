<?php $visualSlug=$chapterUnit['slug'] ?? $unit['slug'] ?? ''; $visual=study_visual($visualSlug); ?>
<section class="book-section" aria-label="Desarrollo y práctica guiada">
  <p class="eyebrow">COMPRENDER ANTES DE MEMORIZAR</p>
  <h2>Del significado a la estructura</h2>
  <p><?= e($chapter['explain']) ?></p>
  <h3>Contrastes y decisiones de uso</h3>
  <p><?= e($chapter['contrast']) ?></p>
  <?php if ($visual): ?><h3>El fundamento: por qué funciona así</h3><p><?= e($visual['foundation']) ?></p><?php endif; ?>
  <figure class="book-example">
    <figcaption>Un ejemplo, paso a paso</figcaption>
    <blockquote lang="en"><?= e($chapter['example']) ?></blockquote>
    <button type="button" class="linkbtn speak" data-say="<?= e($chapter['example']) ?>">Escuchar ejemplo</button>
    <p><?= e($chapter['reason']) ?></p>
  </figure>
  <h2>Práctica guiada y razonada</h2>
  <p>Resolvé cada actividad en tu cuaderno antes de desplegar la solución. Compará tanto la forma como la explicación. Puede haber otras formulaciones válidas.</p>
  <ol class="book-activities">
    <?php foreach ($chapter['practice'] as [$prompt, $answer, $why]): ?>
    <li><p><?= e($prompt) ?></p><details><summary>Ver solución comentada</summary><p class="book-answer"><?= e($answer) ?></p><p><?= e($why) ?></p></details></li>
    <?php endforeach; ?>
  </ol>
  <?php if ($visual): require __DIR__ . '/study_visual.php'; endif; ?>
  <h2>Escritura: construí tu propia respuesta</h2>
  <p><?= e($chapter['task']) ?></p>
  <p class="fine">Trabajá en tu cuaderno. Estas actividades son de revisión autónoma y no suman puntos al progreso de la plataforma.</p>
  <details class="book-model"><summary>Leer un ejemplo breve para empezar</summary><p lang="en"><?= e($chapter['model']) ?></p><p class="fine">Este ejemplo ilustra las decisiones de lenguaje; no completa necesariamente la extensión pedida. Desarrollá tu propio texto con detalles, razones y ejemplos.</p></details>
  <h2>Expresión oral: de la página a la conversación</h2>
  <p><?= e($chapter['speaking']) ?></p>
  <div class="book-recall"><h3>Antes de seguir</h3><ol><li>Explicá la regla con tus palabras, sin mirar.</li><li>Inventá dos ejemplos distintos de los del capítulo.</li><li>Justificá una elección de estructura o vocabulario.</li><li>Volvé mañana a una actividad que te haya costado.</li></ol></div>
</section>
