<?php
$solved = array_sum(array_map(fn($x) => (int) $x['solved'], $exercises));
$total = count($exercises);
?>
<link rel="stylesheet" href="/assets/book.css?v=1">
<section class="wrap page unit-page" style="--lv: <?= e($color) ?>">
  <p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / <a href="/level.php?code=<?= e($unit['level_code']) ?>"><?= e(strtoupper($unit['level_code'])) ?></a> / Unidad <?= (int) $unit['position'] ?></p>
  <header class="unit-head">
    <h1><?= e($unit['title']) ?></h1>
    <p class="muted"><?= e($unit['summary']) ?></p>
  </header>

  <a class="book-link" href="/book.php?code=<?= e($unit['level_code']) ?>&amp;chapter=<?= e($unit['slug']) ?>"><strong>Estudiar este capítulo en el libro →</strong><span>Lectura desarrollada, ejemplos paso a paso y práctica con soluciones comentadas.</span></a>
  <p><a class="btn btn-sm" href="/grammar.php?id=<?= (int)$unit['id'] ?>">Abrir cuaderno de gramática</a></p>
  <div class="unit-progress" aria-hidden="true"><span></span></div>
  <div class="unit-toolbar">
  <div class="tabs" role="tablist">
    <button role="tab" aria-selected="true" aria-controls="t-theory" id="tab-theory">Teoría</button>
    <?php if ($vocab): ?><button role="tab" aria-selected="false" aria-controls="t-vocab" id="tab-vocab">Vocabulario <span class="count"><?= count($vocab) ?></span></button><?php endif; ?>
    <button role="tab" aria-selected="false" aria-controls="t-practice" id="tab-practice">Práctica <span class="count" data-progress><?= $solved ?>/<?= $total ?></span></button>
    <?php if ($lab): ?><button role="tab" aria-selected="false" aria-controls="t-workshop" id="tab-workshop">Taller de aplicación</button><?php endif; ?>
  </div>
  <label class="voice-ctl">🎙️ Velocidad de voz <input type="range" id="voice-rate" min="0.6" max="1.3" step="0.05" value="1"> <span id="voice-rate-val">1×</span></label>
  <button type="button" class="linkbtn mh-link" data-mic-help>🎙️ Probar micrófono</button> <a class="linkbtn mh-link" href="/microfono.php">Solucionar micrófono</a>
  <span data-edge-shortcut></span>
  </div>

  <?php if ($lab): ?>
  <div class="tabpanel" id="t-workshop" role="tabpanel" aria-labelledby="tab-workshop" hidden>
    <?php require __DIR__ . '/learning_lab.php'; ?>
  </div>
  <?php endif; ?>

  <div class="tabpanel theory" id="t-theory" role="tabpanel" aria-labelledby="tab-theory">
    <?= $unit['theory_html'] /* contenido propio cargado por el admin */ ?>
    <?php require_once ROOT . '/src/study_book.php'; $chapter = study_chapter($unit['slug']); if ($chapter): ?>
    <details class="book-unit-expansion" open><summary>Desarrollo del libro y práctica guiada</summary><?php require __DIR__ . '/study_chapter.php'; ?></details>
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
  </div>
  <?php endif; ?>

  <div class="tabpanel" id="t-practice" role="tabpanel" aria-labelledby="tab-practice" hidden>
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
<script src="/assets/conversation-shadowing.js?v=1" defer></script>
<script src="/assets/scene-writing.js?v=2" defer></script>
<div class="xp-hud" aria-live="polite"><span aria-hidden="true">⭐</span><span class="xp-val">0 XP</span><span class="combo"></span></div>
