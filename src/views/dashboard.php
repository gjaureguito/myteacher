<?php
$full = has_full_access($u);
$colors = level_colors();
$first = explode(' ', $u['name'])[0];
?>
<link rel="stylesheet" href="/assets/learning-route.css?v=1">
<link rel="stylesheet" href="/assets/roadmap.css?v=1">
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
  <section class="mt-roadmap" aria-labelledby="roadmap-heading">
    <p class="eyebrow">TU MAPA DE APRENDIZAJE</p>
    <h2 id="roadmap-heading">Tu recorrido de A1 a C2</h2>
    <p class="mt-roadmap-subtitle">Seguís un camino progresivo. Los porcentajes reflejan ejercicios resueltos, no una certificación de dominio del idioma.</p>
    <?php
      $currentCode = $routeTarget['level_code'] ?? ($placement['level_code'] ?? 'a1');
      $hasCurrent = false;
      foreach ($levels as $levelRow) {
        if ($levelRow['code'] === $currentCode) { $hasCurrent = true; break; }
      }
      if (!$hasCurrent && $levels) $currentCode = $levels[0]['code'];
    ?>
    <ol class="mt-roadmap-levels">
      <?php foreach ($levels as $lv):
        $rp = $lp[$lv['code']] ?? ['total'=>0,'solved'=>0];
        $pc = pct($rp);
        $state = $rp['solved'] === $rp['total'] && $rp['total']>0 ? 'is-complete' : ($lv['code'] === $currentCode ? 'is-current' : ($pc>0 ? 'is-start' : 'is-pending'));
        $stateLabel = $rp['solved'] === $rp['total'] && $rp['total']>0 ? 'Completado' : ($lv['code'] === $currentCode ? 'En curso' : ($pc>0 ? 'Iniciado' : 'Por explorar'));
      ?>
      <li class="<?= $state ?>"><a href="/level.php?code=<?= e($lv['code']) ?>" aria-label="Nivel <?= e(strtoupper($lv['code'])) ?>: <?= $pc ?>% de ejercicios resueltos, <?= e($stateLabel) ?>"><span class="mt-roadmap-code"><?= e(strtoupper($lv['code'])) ?></span><small><?= e($stateLabel) ?></small><small><?= $pc ?>% completado</small></a></li>
      <?php endforeach; ?>
    </ol>
    <p><a class="btn" href="/learning-path.php">Ver conceptos aprendidos y certificados →</a></p>
    <div class="mt-roadmap-legend"><span><i class="mt-roadmap-dot current"></i> Nivel actual</span><span><i class="mt-roadmap-dot complete"></i> Ejercicios completados</span><span><i class="mt-roadmap-dot progress"></i> Nivel iniciado</span><span><i class="mt-roadmap-dot"></i> Por explorar</span></div>
  </section>
  <section class="mt-learning-route" aria-labelledby="mt-route-title">
    <div class="mt-learning-route-top">
      <div><p class="eyebrow">TU RUTA PERSONAL DE APRENDIZAJE</p><h2 id="mt-route-title"><?= $routeTarget ? e(strtoupper($routeTarget['level_code']).' · '.$routeTarget['title']) : 'Elegí tu primer capítulo' ?></h2><p>Un camino claro: estudiá, aplicá, evaluá y repasá antes de seguir.</p></div>
      <a href="/stats.php" class="mt-route-stats-link">Ver estadísticas completas ↗</a>
    </div>
    <?php if ($routeTarget): ?>
    <div class="mt-route-steps">
      <a href="/book.php?code=<?= e($routeTarget['level_code']) ?>&amp;chapter=<?= e($routeTarget['slug']) ?>"><b>01 · Comprender</b><span>Leer teoría y ejemplos</span></a>
      <a href="/unit.php?id=<?= (int)$routeTarget['id'] ?>#t-practice"><b>02 · Practicar</b><span>Resolver ejercicios</span></a>
      <a href="/grammar.php?id=<?= (int)$routeTarget['id'] ?>"><b>03 · Comprobar</b><span>Practicar gramática</span></a>
      <a href="/review.php"><b>04 · Reforzar</b><span>Revisar los errores</span></a>
    </div>
    <div class="mt-route-progress"><div><strong><?= $routeDone ?> de <?= $routeTotal ?></strong> ejercicios resueltos en esta unidad</div><div class="mt-route-track" role="progressbar" aria-label="Avance de la unidad" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $routePct ?>"><span style="width:<?= $routePct ?>%"></span></div><b><?= $routePct ?>%</b></div>
    <?php endif; ?>
    <div class="mt-route-metrics">
      <div><strong><?= $performanceAccuracy===null ? 'Sin datos' : $performanceAccuracy.'%' ?></strong><span>Precisión en intentos evaluados</span></div>
      <div><strong><?= (int)$solvedTotal ?></strong><span>Ejercicios completados</span></div>
      <div><strong><?= $pendingReview ?></strong><span>Ejercicios pendientes de refuerzo</span></div>
    </div>
    <p class="mt-route-caveat">El avance mide ejercicios resueltos; la precisión mide respuestas correctas sobre intentos, sin contar soluciones reveladas. Ninguna de las dos métricas certifica por sí sola el dominio del nivel.</p>
  </section>
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
