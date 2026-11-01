<?php $visualSlug=$chapterUnit['slug'] ?? $unit['slug'] ?? ''; $visual=study_visual($visualSlug); $quizUnit = $chapterUnit ?? $unit; $quizDone = array_fill_keys(array_column(q_all('SELECT question_hash FROM book_quiz_status WHERE user_id = ? AND unit_id = ?', [(int)$u['id'], (int)$quizUnit['id']]), 'question_hash'), true); ?>
<?php $bookChapter = $chapterUnit ?? null; $bookSectionDone = $sectionDone ?? []; $markSection = static function (string $key, string $label, string $anchor) use ($bookChapter, $bookSectionDone): void { if (!$bookChapter) return; $done = !empty($bookSectionDone[$key]); ?>
<form method="post" class="book-section-mark"><?= csrf_field() ?><input type="hidden" name="section" value="<?= e($key) ?>"><input type="hidden" name="done" value="<?= $done ? '0' : '1' ?>"><input type="hidden" name="return_to" value="<?= e($anchor) ?>"><button type="submit" class="btn btn-sm<?= $done ? ' btn-ghost' : '' ?>"><?= $done ? '✓ Completado · Marcar como pendiente' : '✓ Marcar ' . e($label) . ' como completado' ?></button></form>
<?php }; ?>
<link rel="stylesheet" href="/assets/book-quiz.css?v=2">
<script src="/assets/book-quiz.js?v=3" defer></script>
<section class="book-section book-chapter-developed" aria-label="Desarrollo y práctica guiada">
  <p class="eyebrow">COMPRENDER ANTES DE MEMORIZAR</p>
  <h2 id="chapter-foundation">1. Comprender el concepto</h2>
  <p class="book-section-lead">Primero identificá qué expresa la estructura, cuándo se utiliza y qué cambia si elegís otra forma.</p>
  <p><?= e($chapter['explain']) ?></p>
  <h3>Cuándo se usa y cómo se diferencia de otras formas</h3>
  <p><?= e($chapter['contrast']) ?></p>
  <?php if ($visual): ?><h3>El fundamento: por qué funciona así</h3><p><?= e($visual['foundation']) ?></p><?php endif; ?>
  <?php $markSection('foundation', 'explicación', 'chapter-foundation'); ?>
  <figure class="book-example">
    <figcaption id="chapter-example">2. Ejemplo explicado paso a paso</figcaption>
    <blockquote lang="en"><?= e($chapter['example']) ?></blockquote>
    <button type="button" class="linkbtn speak" data-say="<?= e($chapter['example']) ?>">Escuchar ejemplo</button>
    <p><?= e($chapter['reason']) ?></p>
    <?php $markSection('example', 'ejemplo comentado', 'chapter-example'); ?>
  </figure>
  <h2 id="chapter-practice">3. Práctica guiada y razonada</h2>
  <p>Elegí la respuesta que corresponde, comprobá tu elección y leé la explicación. También podés abrir la solución comentada. Estas actividades son de autoestudio y no modifican las calificaciones de la unidad.</p>
  <ol class="book-activities" data-book-quiz data-unit-id="<?= (int)$quizUnit['id'] ?>">
    <?php foreach ($chapter['practice'] as $quizIndex => [$prompt, $answer, $why]): $quizHash = hash('sha256', $prompt . "\n" . $answer); $isQuizDone = isset($quizDone[$quizHash]); ?>
    <li class="book-quiz-question" data-prompt="<?= e($prompt) ?>" data-answer="<?= e($answer) ?>" data-index="<?= (int)$quizIndex ?>" data-done="<?= $isQuizDone ? '1' : '0' ?>">
      <p><?= e($prompt) ?> <span class="book-quiz-done"<?= $isQuizDone ? '' : ' hidden' ?>>✓ Completado</span></p>
      <div class="book-quiz-options" role="group" aria-label="Elegí una respuesta"></div>
      <div class="book-quiz-actions"><button type="button" class="btn btn-sm book-quiz-check" disabled>Comprobar respuesta</button><button type="button" class="linkbtn book-quiz-retry" hidden>Intentar otra vez</button></div>
      <p class="book-quiz-feedback" role="status" aria-live="polite"></p>
      <details><summary>Ver solución comentada</summary><p class="book-answer"><?= e($answer) ?></p><p><?= e($why) ?></p></details>
    </li>
    <?php endforeach; ?>
  </ol>
  <?php if ($visual): require __DIR__ . '/study_visual.php'; endif; ?>
  <h2 id="chapter-writing">4. Aplicación: escritura y expresión oral</h2>
  <h3>Escribí una respuesta propia</h3>
  <p><?= e($chapter['task']) ?></p>
  <p class="fine">Trabajá en tu cuaderno. Estas actividades son de revisión autónoma y no suman puntos al progreso de la plataforma.</p>
  <details class="book-model"><summary>Leer un ejemplo breve para empezar</summary><p lang="en"><?= e($chapter['model']) ?></p><p class="fine">Este ejemplo ilustra las decisiones de lenguaje; no completa necesariamente la extensión pedida. Desarrollá tu propio texto con detalles, razones y ejemplos.</p></details>
  <?php $markSection('writing', 'escritura y conversación', 'chapter-writing'); ?>
  <h3>Expresión oral: de la página a la conversación</h3>
  <p><?= e($chapter['speaking']) ?></p>
  <div class="book-recall" id="chapter-check"><h3>5. Comprobá lo aprendido antes de seguir</h3><ol><li>Explicá la regla con tus palabras, sin mirar.</li><li>Inventá dos ejemplos distintos de los del capítulo.</li><li>Justificá una elección de estructura o vocabulario.</li><li>Volvé mañana a una actividad que te haya costado.</li></ol></div>
  <?php $markSection('check', 'repaso final', 'chapter-check'); ?>
</section>

