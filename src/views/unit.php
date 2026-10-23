<?php
$solved = array_sum(array_map(fn($x) => (int) $x['solved'], $exercises));
$total = count($exercises);
?>
<section class="wrap page unit-page" style="--lv: <?= e($color) ?>">
  <p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / <a href="/level.php?code=<?= e($unit['level_code']) ?>"><?= e(strtoupper($unit['level_code'])) ?></a> / Unidad <?= (int) $unit['position'] ?></p>
  <header class="unit-head">
    <h1><?= e($unit['title']) ?></h1>
    <p class="muted"><?= e($unit['summary']) ?></p>
  </header>

  <div class="tabs" role="tablist">
    <button role="tab" aria-selected="true" aria-controls="t-theory" id="tab-theory">Teoría</button>
    <?php if ($vocab): ?><button role="tab" aria-selected="false" aria-controls="t-vocab" id="tab-vocab">Vocabulario <span class="count"><?= count($vocab) ?></span></button><?php endif; ?>
    <button role="tab" aria-selected="false" aria-controls="t-practice" id="tab-practice">Práctica <span class="count" data-progress><?= $solved ?>/<?= $total ?></span></button>
    <?php if ($lab): ?><button role="tab" aria-selected="false" aria-controls="t-workshop" id="tab-workshop">Taller de aplicación</button><?php endif; ?>
  </div>

  <?php if ($lab): ?>
  <div class="tabpanel" id="t-workshop" role="tabpanel" aria-labelledby="tab-workshop" hidden>
    <?php require __DIR__ . '/learning_lab.php'; ?>
  </div>
  <?php endif; ?>

  <div class="tabpanel theory" id="t-theory" role="tabpanel" aria-labelledby="tab-theory">
    <?= $unit['theory_html'] /* contenido propio cargado por el admin */ ?>
    <p class="tab-next"><button class="btn" data-goto="t-practice">Ir a la práctica</button></p>
  </div>

  <?php if ($vocab): ?>
  <div class="tabpanel" id="t-vocab" role="tabpanel" aria-labelledby="tab-vocab" hidden>
    <p class="muted">Tocá una tarjeta para ver la traducción. El parlante reproduce la pronunciación.</p>
    <div class="vocab">
      <?php foreach ($vocab as $v): ?>
        <div class="card-flip" tabindex="0" role="button" aria-label="<?= e($v[0]) ?>">
          <span class="front"><?= e($v[0]) ?></span>
          <span class="back"><?= e($v[1]) ?><?php if (!empty($v[2])): ?><small><?= e($v[2]) ?></small><?php endif; ?></span>
          <button class="speak" data-say="<?= e($v[0]) ?>" aria-label="Escuchar <?= e($v[0]) ?>">🔊</button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="tabpanel" id="t-practice" role="tabpanel" aria-labelledby="tab-practice" hidden>
    <ol class="exercises">
      <?php foreach ($exercises as $i => $ex):
        $data = json_decode($ex['data_json'], true) ?: [];
      ?>
        <li class="ex<?= $ex['solved'] ? ' is-solved' : '' ?>" data-id="<?= (int) $ex['id'] ?>" data-type="<?= e($ex['type']) ?>">
          <p class="ex-prompt"><span class="ex-n"><?= $i + 1 ?></span>
            <?php if ($ex['type'] === 'fill'): ?>
              <?= preg_replace('/_{3,}/', '<input class="gap" aria-label="Completar" autocomplete="off" autocapitalize="off" spellcheck="false">', e($ex['prompt']), 1) ?>
            <?php else: ?>
              <?= e($ex['prompt']) ?>
            <?php endif; ?>
          </p>

          <?php if (in_array($ex['type'], ['listen', 'dictation'], true)): ?>
            <p><button class="btn btn-ghost btn-sm speak" data-say="<?= e($data['say']) ?>">🔊 Escuchar</button>
               <button class="btn btn-ghost btn-sm speak" data-say="<?= e($data['say']) ?>" data-rate="0.65">🐢 Más lento</button></p>
          <?php endif; ?>

          <?php if (in_array($ex['type'], ['mcq', 'listen'], true)): ?>
            <div class="options">
              <?php foreach ($data['options'] as $k => $opt): ?>
                <button class="opt" data-value="<?= $k ?>"><?= e($opt) ?></button>
              <?php endforeach; ?>
            </div>
          <?php elseif ($ex['type'] === 'order'): ?>
            <div class="built" aria-live="polite"></div>
            <div class="chips">
              <?php foreach ($data['words'] as $w): ?><button class="chip"><?= e($w) ?></button><?php endforeach; ?>
            </div>
            <p><button class="btn btn-sm check">Comprobar</button> <button class="linkbtn reset">Reiniciar</button></p>
          <?php elseif ($ex['type'] === 'dictation'): ?>
            <p><input class="dict" placeholder="Escribí lo que escuchás" autocomplete="off" autocapitalize="off" spellcheck="false"></p>
            <p><button class="btn btn-sm check">Comprobar</button></p>
          <?php elseif ($ex['type'] === 'fill'): ?>
            <p><button class="btn btn-sm check">Comprobar</button></p>
          <?php endif; ?>
          <div class="feedback" aria-live="polite"></div>
        </li>
      <?php endforeach; ?>
    </ol>

    <div class="unit-end">
      <?php if ($next): ?>
        <a class="btn" href="/unit.php?id=<?= (int) $next['id'] ?>">Siguiente: <?= e($next['title']) ?></a>
      <?php endif; ?>
      <a class="btn btn-ghost" href="/level.php?code=<?= e($unit['level_code']) ?>">Volver al nivel</a>
    </div>
  </div>
</section>
