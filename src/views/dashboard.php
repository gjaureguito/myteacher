<?php
$full = has_full_access($u);
$colors = level_colors();
$first = explode(' ', $u['name'])[0];
?>
<section class="wrap page learning-dashboard">
  <p class="eyebrow">MYTEACHER / TU PRÓXIMO PASO</p>
  <div class="dash-head">
    <div>
      <h1><span data-greet>Hola</span>, <?= e($first) ?></h1>
      <p class="muted">Tu inglés se construye practicando. Retomá donde quedaste.</p>
    </div>
    <span class="pill <?= $full ? 'pill-ok' : 'pill-demo' ?>"><?= e(sub_label($u)) ?></span>
  </div>
  <?php
    $doneUnits = 0; $allSolved = 0; $allTotal = 0;
    foreach ($lp as $p) { $allSolved += $p['solved']; $allTotal += $p['total']; }
    $globalPct = $allTotal ? (int) round($allSolved * 100 / $allTotal) : 0;
  ?>
  <div class="stat-row">
    <div class="stat flame"><span class="stat-ico" aria-hidden="true">🔥</span><div><strong data-count="<?= (int) $streak ?>"><?= (int) $streak ?></strong><span>días de racha</span></div></div>
    <div class="stat"><span class="stat-ico" aria-hidden="true">✅</span><div><strong data-count="<?= (int) $solvedTotal ?>"><?= (int) $solvedTotal ?></strong><span>ejercicios resueltos</span></div></div>
    <div class="stat"><span class="stat-ico" aria-hidden="true">⭐</span><div><strong data-xp>0</strong><span>puntos de experiencia</span></div></div>
    <div class="stat" style="--lv: var(--brand-2)"><span class="pring" style="--p: <?= $globalPct ?>"><span><?= $globalPct ?>%</span></span><div><strong style="font-size:1.1rem">Recorrido total</strong><span>A1 → C2 publicado</span></div></div>
  </div>

<?php $startUnit=$last ?: q_one('SELECT id,title,level_code FROM units WHERE is_published=1 ORDER BY level_code,position LIMIT 1'); ?>
  <div class="mt-dashboard-start"><article class="mt-start-main"><p class="eyebrow"><?= $last ? 'SEGUÍ DONDE QUEDASTE' : 'TU PRIMERA CONVERSACIÓN' ?></p><h2><?= $startUnit ? e($startUnit['title']) : 'Encontrá tu punto de partida.' ?></h2><p>Una explicación clara, una situación para escuchar y actividades para poner tu inglés en movimiento.</p><a class="btn" href="<?= $startUnit ? '/unit.php?id='.(int)$startUnit['id'].'#t-practice' : '/placement.php' ?>"><?= $last ? 'Continuar mi práctica' : 'Empezar mi práctica' ?> ↗</a></article><article class="mt-start-side"><span class="mt-badge">REAL LIFE ENGLISH</span><h3>¿Para qué querés usar tu inglés hoy?</h3><p>Un café, un viaje, tu trabajo. Elegí una escena y practicá con diálogos, fotos y audio.</p><a class="mt-text-link" href="/situations.php">Elegir una situación ↗</a></article></div>

  <article class="mt-study-dashboard"><img src="/assets/images/study-desk-v2-640.webp" width="640" height="427" alt="Cuaderno y auriculares en una mesa de estudio. Imagen ilustrativa generada con IA." loading="lazy" decoding="async"><div class="mt-study-dashboard-copy"><p class="eyebrow">TU BIBLIOTECA DE ESTUDIO</p><h2>Una idea nueva. Un capítulo más.</h2><p>Teoría desarrollada, ejemplos para comprender y actividades con soluciones comentadas. Dale tiempo a lo que querés aprender.</p><a class="mt-text-link" href="/book.php">Abrir mis libros de estudio ↗</a></div></article>
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
      <a class="resume primary" href="/unit.php?id=<?= (int) $last['id'] ?>">
        <span class="muted">▶ Seguí donde quedaste</span>
        <strong><?= e(strtoupper($last['level_code'])) ?> · <?= e($last['title']) ?></strong>
      </a>
    <?php endif; ?>
    <a class="resume" href="/placement.php">
      <span class="muted"><?= $placement ? 'Tu último test de nivel' : '¿No sabés por dónde empezar?' ?></span>
      <strong><?= $placement ? 'Resultado: ' . strtoupper(e($placement['level_code'])) . ' (' . (int) $placement['score'] . '/' . (int) $placement['total'] . ')' : 'Hacé el test de nivel (10 min)' ?></strong>
    </a>
  </div>

  <div class="desk-section-heading"><h2 class="section-title">Elegí tu próximo capítulo</h2><a class="text-link" href="/library.php">Ir a la biblioteca ↗</a></div>
  <ol class="level-grid">
    <?php foreach ($levels as $i => $lv):
      $p = $lp[$lv['code']] ?? ['total' => 0, 'solved' => 0];
      $pc = pct($p);
      $rec = $placement && $placement['level_code'] === $lv['code'];
    ?>
      <li class="level-card" style="--lv: <?= e($colors[$lv['code']] ?? '#2747D9') ?>">
        <a href="/level.php?code=<?= e($lv['code']) ?>">
          <span class="lc-top">
            <img src="/assets/images/levels/<?= e($lv['code']) ?>.svg" width="76" height="76" alt="">
            <span class="pring" style="--p: <?= $pc ?>" aria-label="Progreso <?= $pc ?>%"><span><?= $pc ?>%</span></span>
          </span>
          <h3><small><?= e(strtoupper($lv['code'])) ?></small><?= e($lv['name']) ?></h3>
          <p><?= e($lv['description']) ?></p>
          <?php if ($rec): ?><em class="tag">★ Recomendado para vos</em><?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>
</section>
