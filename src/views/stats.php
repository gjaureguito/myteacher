<?php
$colors = level_colors();
$withData = array_filter($skills, fn($s) => $s['accuracy'] !== null && $s['tries'] >= 3);
uasort($withData, fn($a, $b) => $b['accuracy'] <=> $a['accuracy']);
$best = $withData ? array_key_first($withData) : null;
$worst = count($withData) > 1 ? array_key_last($withData) : null;
$untouched = array_keys(array_filter($skills, fn($s) => $s['tries'] === 0 && $s['avail'] > 0));
$maxDay = max(1, ...array_map(fn($w) => max(array_column($w, 'n')), $weeks));
$weekTotals = array_map(fn($w) => array_sum(array_column($w, 'n')), $weeks);
$weekOk = array_map(fn($w) => array_sum(array_column($w, 'ok')), $weeks);
$maxWeek = max(1, ...$weekTotals);
$shade = fn(int $n) => $n === 0 ? 0 : min(4, (int) ceil(4 * $n / $maxDay));
?>
<section class="wrap page stats-page">
  <p class="eyebrow">LEARNING ANALYTICS / TU PROGRESO</p>
  <div class="dash-head">
    <div>
      <h1>Mis estadísticas</h1>
      <p class="muted">Cómo venís en cada habilidad, cuánto practicaste y qué conviene reforzar.</p>
    </div>
    <a class="btn" href="/review.php">Repasar mis errores<?php if ($pending): ?> (<?= $pending ?>)<?php endif; ?></a>
  </div>

  <div class="stat-row stat-row-5">
    <div class="stat"><span class="stat-ico" aria-hidden="true">⏱️</span><div><strong data-count="<?= $minutes ?>"><?= $minutes ?></strong><span>minutos de práctica</span></div></div>
    <div class="stat"><span class="stat-ico" aria-hidden="true">✅</span><div><strong data-count="<?= $solvedTotal ?>"><?= $solvedTotal ?></strong><span>ejercicios resueltos</span></div></div>
    <div class="stat"><span class="stat-ico" aria-hidden="true">🎯</span><div><strong><?= $accuracy === null ? '—' : $accuracy . '%' ?></strong><span>precisión general</span></div></div>
    <div class="stat"><span class="stat-ico" aria-hidden="true">⚡</span><div><strong data-count="<?= $firstTry ?>"><?= $firstTry ?></strong><span>correctos a la primera</span></div></div>
    <div class="stat flame"><span class="stat-ico" aria-hidden="true">🔥</span><div><strong data-count="<?= $streak ?>"><?= $streak ?></strong><span>días de racha</span></div></div>
  </div>

  <?php if ($best || $untouched): ?>
  <div class="insight">
    <span aria-hidden="true">💡</span>
    <p>
      <?php if ($best): ?>Tu punto fuerte es <strong><?= e($skills[$best]['en']) ?></strong> (<?= $skills[$best]['accuracy'] ?>% de precisión).<?php endif; ?>
      <?php if ($worst && $worst !== $best && $skills[$worst]['accuracy'] < 80): ?> Para seguir creciendo, dedicale más tiempo a <strong><?= e($skills[$worst]['en']) ?></strong> (<?= $skills[$worst]['accuracy'] ?>%).<?php endif; ?>
      <?php if ($untouched): ?> Todavía no practicaste <?= e(implode(', ', array_map(fn($k) => $skills[$k]['en'], $untouched))) ?>: lo encontrás en la pestaña Práctica de cada unidad.<?php endif; ?>
    </p>
  </div>
  <?php endif; ?>

  <div class="viz-card">
    <div class="viz-head">
      <div><h2>Habilidades</h2><p class="muted">Avance: ejercicios resueltos sobre los publicados. Precisión: respuestas correctas sobre intentos (sin contar soluciones vistas).</p></div>
      <div class="legend" aria-hidden="true"><span><i class="sw sw-1"></i>Avance</span><span><i class="sw sw-2"></i>Precisión</span></div>
    </div>
    <div class="skill-bars" role="table" aria-label="Avance y precisión por habilidad">
      <?php foreach ($skills as $k => $s): ?>
        <div class="skill-row" role="row">
          <div class="skill-name" role="rowheader"><span aria-hidden="true"><?= $s['ico'] ?></span><div><strong lang="en"><?= e($s['en']) ?></strong><small><?= e($s['es']) ?></small></div></div>
          <div class="bars" role="cell">
            <div class="bar-line" data-tip="Avance <?= e($s['en']) ?>: <?= $s['solved'] ?> de <?= $s['avail'] ?> ejercicios (<?= $s['progress'] ?>%)">
              <span class="bar b1" style="--w: <?= $s['progress'] ?>%"></span><b><?= $s['progress'] ?>%</b>
            </div>
            <div class="bar-line" data-tip="Precisión <?= e($s['en']) ?>: <?= $s['accuracy'] === null ? 'sin intentos todavía' : $s['accuracy'] . '% en ' . $s['tries'] . ' intentos' ?><?= $s['rev'] ? ' · ' . $s['rev'] . ' soluciones vistas' : '' ?>">
              <span class="bar b2" style="--w: <?= (int) $s['accuracy'] ?>%"></span><b><?= $s['accuracy'] === null ? '—' : $s['accuracy'] . '%' ?></b>
            </div>
          </div>
          <div class="skill-count" role="cell"><?= $s['solved'] ?>/<?= $s['avail'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="viz-grid">
    <div class="viz-card">
      <div class="viz-head"><div><h2>Actividad</h2><p class="muted"><?= $activeDays ?> <?= $activeDays === 1 ? "día" : "días" ?> con práctica en las últimas 12 semanas.</p></div></div>
      <div class="heatmap" role="img" aria-label="Ejercicios respondidos por día en las últimas 12 semanas">
        <?php foreach ($weeks as $w): ?><div class="hm-col"><?php foreach ($w as $d => $x): ?><span class="hm-cell s<?= $shade($x['n']) ?>" data-tip="<?= e(date('d/m', strtotime($d))) ?>: <?= $x['n'] ?> respuestas<?= $x['n'] ? ' · ' . round(100 * $x['ok'] / $x['n']) . '% correctas' : '' ?>"></span><?php endforeach; ?></div><?php endforeach; ?>
      </div>
      <div class="hm-scale" aria-hidden="true">Menos <span class="hm-cell s0"></span><span class="hm-cell s1"></span><span class="hm-cell s2"></span><span class="hm-cell s3"></span><span class="hm-cell s4"></span> Más</div>
    </div>
    <div class="viz-card">
      <div class="viz-head"><div><h2>Respuestas por semana</h2><p class="muted">Pasá el cursor sobre cada barra para ver la precisión de la semana.</p></div></div>
      <div class="week-bars" role="img" aria-label="Respuestas por semana">
        <?php foreach ($weekTotals as $i => $n): ?>
          <div class="wb" data-tip="Semana del <?= e(date('d/m', strtotime(array_key_first($weeks[$i])))) ?>: <?= $n ?> respuestas<?= $n ? ' · ' . round(100 * $weekOk[$i] / $n) . '% correctas' : '' ?>"><span style="--h: <?= round(100 * $n / $maxWeek) ?>%"></span></div>
        <?php endforeach; ?>
      </div>
      <div class="wb-axis" aria-hidden="true"><span>hace 12 semanas</span><span>esta semana</span></div>
    </div>
  </div>

  <div class="viz-grid">
    <div class="viz-card">
      <div class="viz-head"><div><h2>Avance por nivel</h2></div></div>
      <div class="lvl-bars">
        <?php foreach ($levels as $lv): $p = $lp[$lv['code']] ?? ['total' => 0, 'solved' => 0]; $pc = pct($p); ?>
          <a class="lvl-row" href="/level.php?code=<?= e($lv['code']) ?>" style="--lv: <?= e($colors[$lv['code']]) ?>">
            <img src="/assets/images/levels/<?= e($lv['code']) ?>.svg" width="34" height="34" alt="">
            <span><strong><?= e(strtoupper($lv['code'])) ?></strong> <?= e($lv['name']) ?></span>
            <span class="lvl-meter" data-tip="<?= $p['solved'] ?> de <?= $p['total'] ?> ejercicios"><i style="width: <?= $pc ?>%"></i></span>
            <b><?= $pc ?>%</b>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="viz-card">
      <div class="viz-head"><div><h2>Para reforzar</h2><p class="muted">Unidades con menor precisión (mínimo 4 respuestas).</p></div></div>
      <?php if ($weak): ?>
        <ol class="weak-list">
          <?php foreach ($weak as $w): $acc = (int) round(100 * $w['ok'] / $w['n']); ?>
            <li><a href="/unit.php?id=<?= (int) $w['id'] ?>#t-practice"><span class="weak-acc <?= $acc < 50 ? 'low' : ($acc < 75 ? 'mid' : 'good') ?>"><?= $acc ?>%</span><span><small><?= e(strtoupper($w['level_code'])) ?></small> <?= e($w['title']) ?></span><span aria-hidden="true">→</span></a></li>
          <?php endforeach; ?>
        </ol>
      <?php else: ?>
        <p class="empty-note">Cuando respondas algunos ejercicios vas a ver acá las unidades que conviene repasar.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
