<?php
$full = has_full_access($u);
$colors = level_colors();
$first = explode(' ', $u['name'])[0];
?>
<section class="wrap page">
  <div class="dash-head">
    <div>
      <h1>Hola, <?= e($first) ?></h1>
      <p class="muted">
        <?= $solvedTotal ?> ejercicios resueltos
        <?php if ($streak): ?>· racha de <?= $streak ?> <?= $streak === 1 ? 'día' : 'días' ?><?php endif; ?>
      </p>
    </div>
    <span class="pill <?= $full ? 'pill-ok' : 'pill-demo' ?>"><?= e(sub_label($u)) ?></span>
  </div>

  <?php if (!$full): ?>
    <div class="upsell">
      <div>
        <h2>Estás usando la demo</h2>
        <p>Tenés abierta la primera unidad de cada nivel. Con la suscripción desbloqueás todas las unidades, de A1 a C2.</p>
      </div>
      <a class="btn" href="/account.php">Desbloquear todo por <?= money_ars(plan_price()) ?>/mes</a>
    </div>
  <?php endif; ?>

  <div class="dash-cards">
    <?php if ($last): ?>
      <a class="resume" href="/unit.php?id=<?= (int) $last['id'] ?>">
        <span class="muted">Seguí donde quedaste</span>
        <strong><?= e(strtoupper($last['level_code'])) ?> · <?= e($last['title']) ?></strong>
      </a>
    <?php endif; ?>
    <a class="resume" href="/placement.php">
      <span class="muted"><?= $placement ? 'Tu último test de nivel' : '¿No sabés por dónde empezar?' ?></span>
      <strong><?= $placement ? 'Resultado: ' . strtoupper(e($placement['level_code'])) . ' (' . (int) $placement['score'] . '/' . (int) $placement['total'] . ')' : 'Hacé el test de nivel (10 min)' ?></strong>
    </a>
  </div>

  <h2 class="section-title">Niveles</h2>
  <ol class="ladder">
    <?php foreach ($levels as $i => $lv):
      $p = $lp[$lv['code']] ?? ['total' => 0, 'solved' => 0];
      $pc = pct($p);
      $rec = $placement && $placement['level_code'] === $lv['code'];
    ?>
      <li class="rung" style="--lv: <?= e($colors[$lv['code']] ?? '#2747D9') ?>; --step: <?= $i ?>">
        <a href="/level.php?code=<?= e($lv['code']) ?>">
          <span class="rung-code"><?= e(strtoupper($lv['code'])) ?></span>
          <span class="rung-body">
            <strong><?= e($lv['name']) ?><?php if ($rec): ?> <em class="tag">Recomendado</em><?php endif; ?></strong>
            <span class="muted"><?= e($lv['description']) ?></span>
            <span class="bar" aria-label="Progreso <?= $pc ?>%"><span style="width: <?= $pc ?>%"></span></span>
          </span>
          <span class="rung-pct"><?= $pc ?>%</span>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>
</section>
