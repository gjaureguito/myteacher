<?php
$solved = array_sum(array_map(fn($x) => (int) $x['solved'], $exercises));
$total = count($exercises);
?>
<link rel="stylesheet" href="/assets/book.css?v=2">
<link rel="stylesheet" href="/assets/practice-reference.css?v=2">
<section class="wrap page unit-page" style="--lv: <?= e($color) ?>">
  <p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / <a href="/level.php?code=<?= e($unit['level_code']) ?>"><?= e(strtoupper($unit['level_code'])) ?></a> / Unidad <?= (int) $unit['position'] ?></p>
  <header class="unit-head">
    <h1><?= e($unit['title']) ?></h1>
    <p class="muted"><?= e($unit['summary']) ?></p>
  </header>

  <a class="book-link" href="/book.php?code=<?= e($unit['level_code']) ?>&amp;chapter=<?= e($unit['slug']) ?>"><strong>Estudiar este capítulo en el libro →</strong><span>Lectura desarrollada, ejemplos paso a paso y práctica con soluciones comentadas.</span></a>
  <p><a class="btn btn-sm" href="/grammar.php?id=<?= (int)$unit['id'] ?>">Abrir cuaderno de gramática</a></p>
  <nav class="learning-route" aria-label="Ruta de esta unidad"><strong>Ruta de aprendizaje</strong><button type="button" data-goto="t-theory">1. Comprender la teoría</button><button type="button" data-goto="t-practice">2. Aplicar lo aprendido</button><button type="button" data-goto="t-extra">3. Ampliar la práctica</button><a href="/glossary.php">4. Mi glosario ↗</a><a href="/tutor.php?unit=<?= (int)$unit['id'] ?>">Consultar diccionario</a></nav>
  <div class="unit-progress" aria-hidden="true"><span></span></div>
  <div class="unit-toolbar">
  <div class="tabs" role="tablist">
    <button role="tab" aria-selected="true" aria-controls="t-theory" id="tab-theory">1 · Teoría completa</button>
    <?php if ($vocab): ?><button role="tab" aria-selected="false" aria-controls="t-vocab" id="tab-vocab">Vocabulario <span class="count"><?= count($vocab) ?></span></button><?php endif; ?>
    <button role="tab" aria-selected="false" aria-controls="t-practice" id="tab-practice">2 · Práctica guiada <span class="count" data-progress><?= $solved ?>/<?= $total ?></span></button>
    <button role="tab" aria-selected="false" aria-controls="t-extra" id="tab-extra">3 · Práctica complementaria</button>
  </div>
  <label class="voice-ctl">🎙️ Velocidad de voz <input type="range" id="voice-rate" min="0.6" max="1.3" step="0.05" value="1"> <span id="voice-rate-val">1×</span></label>
  </div>

<?php require __DIR__ . '/_mic_notice.php'; ?>
  <div class="tabpanel" id="t-extra" role="tabpanel" aria-labelledby="tab-extra" hidden>
    <h2>Prácticas complementarias</h2>
    <p class="muted">Consolidá lo aprendido mediante distintas actividades: situaciones reales, lectura, escritura y revisión de vocabulario.</p>
    <?php if ($lab): ?><?php require __DIR__ . '/learning_lab.php'; ?><?php endif; ?>
    <div class="extra-practice-grid">
      <a href="/review.php"><strong>Repaso espaciado</strong><span>Revisá los contenidos para fortalecer la memoria.</span></a>
      <a href="/library.php"><strong>Lectura y comprensión</strong><span>Aplicá la gramática y el vocabulario a textos completos.</span></a>
      <a href="/situations.php"><strong>Inglés en situaciones reales</strong><span>Practicá contextos cotidianos con escenas y diálogos.</span></a>
      <a href="/glossary.php"><strong>Mi glosario personal</strong><span>Buscá, guardá y repasá palabras que encontraste en esta unidad.</span></a>
    </div>
  </div>

  <div class="tabpanel theory" id="t-theory" role="tabpanel" aria-labelledby="tab-theory">
    <h2>Teoría de esta unidad</h2><p class="muted">Leé el desarrollo del libro para estudiar el tema de forma ordenada. Este apartado sirve como consulta rápida antes de resolver los ejercicios.</p><p><a class="btn btn-sm" href="/book.php?code=<?= e($unit['level_code']) ?>&amp;chapter=<?= e($unit['slug']) ?>">Leer el capítulo completo en el libro</a></p>
    <?= $unit['theory_html'] /* contenido propio cargado por el admin */ ?>
    <?php require_once ROOT . '/src/study_book.php'; $chapter = study_chapter($unit['slug']); if ($chapter): ?>
    <details class="book-unit-expansion"><summary>Ampliar explicación y ver ejemplos resueltos</summary><?php require __DIR__ . '/study_chapter.php'; ?></details>
    <?php endif; ?>
    <p class="tab-next"><button class="btn" data-goto="t-practice">Ir a la práctica</button></p>
  </div>

  <?php if ($vocab): ?>
  <div class="tabpanel" id="t-vocab" role="tabpanel" aria-labelledby="tab-vocab" hidden>
    <p class="muted">Tocá una tarjeta para ver la traducción. El parlante reproduce la pronunciación.</p>
    <div class="vocab">
      <?php foreach ($vocab as $v): ?>
        <div class="card-flip" tabindex="0" role="button" aria-label="<?= e($v[0]) ?>">
          <span class="front"><?php if (!empty($v[3])): ?><span class="vocab-pic" aria-hidden="true"><?= e($v[3]) ?></span><?php endif; ?><?= e($v[0]) ?></span>
          <span class="back"><?= e($v[1]) ?><?php if (!empty($v[2])): ?><small><?= e($v[2]) ?></small><?php endif; ?></span>
          <button class="speak" data-say="<?= e($v[0]) ?>" aria-label="Escuchar <?= e($v[0]) ?>">🔊</button>
        </div>
      <?php endforeach; ?>
    </div>
    <p><a class="btn btn-ghost" href="/glossary.php">Buscar y guardar nuevas palabras en mi glosario →</a></p>
  </div>
  <?php endif; ?>

  <div class="tabpanel" id="t-practice" role="tabpanel" aria-labelledby="tab-practice" hidden>
    <details class="mt-practice-reference" aria-label="Consultar teoría durante la práctica">
      <summary class="mt-practice-reference-head">
        <span><strong>Consultar teoría de esta unidad</strong><small>Reglas y ejemplos disponibles cuando los necesites</small></span>
        <span class="mt-practice-reference-chevron" aria-hidden="true">⌄</span>
      </summary>
      <div class="mt-practice-reference-body theory">
        <?= $unit['theory_html'] /* contenido existente */ ?>
      </div>
      <p class="mt-practice-reference-link"><a href="/book.php?code=<?= e($unit['level_code']) ?>&amp;chapter=<?= e($unit['slug']) ?>">Leer capítulo completo ↗</a></p>
    </details>
    <div class="mt-practice-start"><h2>Ejercitación interactiva</h2><p class="muted">Resolvé los ejercicios con la explicación siempre a mano.</p></div>
    <?php require __DIR__ . '/scenes.php'; ?>
    <?php
      $bySkill = [];
      foreach ($exercises as $x) { $k = $x['skill']; $bySkill[$k] ??= [0, 0]; $bySkill[$k][1]++; if ($x['solved']) $bySkill[$k][0]++; }
      $sl = skill_labels();
    ?>
    <div class="unit-skillbar">
      <?php foreach ($bySkill as $k => [$sv, $tt]): ?>
        <span class="skill-chip skill-<?= e($k) ?>" data-skill-chip="<?= e($k) ?>"><?= $sl[$k][2] ?> <?= e($sl[$k][0]) ?> <b><span data-s><?= $sv ?></span>/<?= $tt ?></b></span>
      <?php endforeach; ?>
    </div>
    <div class="practice-tools"><span class="muted" data-done-count></span><button type="button" class="linkbtn reset-unit" data-unit="<?= (int) $unit['id'] ?>">↺ Reiniciar práctica</button></div>
    <p class="kbd-hint">Atajos: <span class="kbd">1</span>–<span class="kbd">4</span> elegir opción · <span class="kbd">L</span> escuchar · <span class="kbd">N</span> siguiente · <span class="kbd">Enter</span> comprobar</p>
    <?php $exList = $exercises; $groupBySkill = true; require __DIR__ . '/_exercise.php'; ?>

    <div class="unit-end">
      <?php if ($next): ?>
        <a class="btn" href="/unit.php?id=<?= (int) $next['id'] ?>">Siguiente: <?= e($next['title']) ?></a>
      <?php endif; ?>
      <a class="btn btn-ghost" href="/level.php?code=<?= e($unit['level_code']) ?>">Volver al nivel</a>
    </div>
  </div>
</section>
<script src="/assets/guided-practice.js?v=1" defer></script>
<script src="/assets/vocab-challenge.js?v=1" defer></script>
<script src="/assets/conversation-shadowing.js?v=2" defer></script>
<script src="/assets/scene-writing.js?v=2" defer></script>
<script src="/assets/writing-checker.js?v=7" defer></script>
<div class="xp-hud" aria-live="polite"><span aria-hidden="true">⭐</span><span class="xp-val">0 XP</span><span class="combo"></span></div>
