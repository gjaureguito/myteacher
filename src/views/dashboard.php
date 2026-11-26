<?php
$full = has_full_access($u);
$colors = level_colors();
$first = explode(' ', $u['name'])[0];
?>
<link rel="stylesheet" href="/assets/learning-route.css?v=1">
<link rel="stylesheet" href="/assets/roadmap.css?v=1">
<section class="wrap page learning-dashboard">
  <?php trial_notice($u); ?>
  <p><a href="/opinion.php">¿Cómo te está yendo? Compartí tu experiencia</a></p>
  <p class="eyebrow">MYTEACHER / TU PRÓXIMO PASO</p>
  <div class="dash-head">
    <div>
      <h1><span data-greet>Hola</span>, <?= e($first) ?></h1>
      <p class="muted">Tu inglés se construye practicando. Retomá donde quedaste.</p>
    </div>
    <span class="pill <?= $full ? 'pill-ok' : 'pill-demo' ?>"><?= e(sub_label($u)) ?></span>
  </div>
<?php $startUnit=$last ?: q_one('SELECT id,title,level_code FROM units WHERE is_published=1 ORDER BY level_code,position LIMIT 1'); ?>
  <div class="mt-dashboard-start"><article class="mt-start-main"><p class="eyebrow"><?= $last ? 'SEGUÍ DONDE QUEDASTE' : 'TU PRIMERA CONVERSACIÓN' ?></p><h2><?= $startUnit ? e($startUnit['title']) : 'Encontrá tu punto de partida.' ?></h2><p>Una explicación clara, una situación para escuchar y actividades para poner tu inglés en movimiento.</p><a class="btn" href="<?= $startUnit ? '/unit.php?id='.(int)$startUnit['id'].'#t-practice' : '/placement.php' ?>"><?= $last ? 'Continuar mi práctica' : 'Empezar mi práctica' ?> ↗</a></article><article class="mt-start-side"><span class="mt-badge">REAL LIFE ENGLISH</span><h3>¿Para qué querés usar tu inglés hoy?</h3><p>Un café, un viaje, tu trabajo. Elegí una escena y practicá con diálogos, fotos y audio.</p><a class="mt-text-link" href="/situations.php">Elegir una situación ↗</a></article></div>

  <section class="mt-professor-feature" aria-labelledby="professor-feature-title">
    <div class="mt-professor-feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="2" width="6" height="12" rx="3"/><path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3M8 22h8"/></svg></div>
    <div class="mt-professor-feature-copy"><p class="mt-professor-kicker">NUEVO EN MYTEACHER <span><?= tutor_dialog_access(current_user())?'Diálogo activo':(tutor_enabled()?'Piloto disponible':'Conversación pausada') ?></span></p><h2 id="professor-feature-title">Tu profesor de conversación con IA</h2><p>Un espacio para practicar inglés a tu ritmo. Conocé el piloto y revisá tu acceso.</p><p class="mt-professor-feature-note"><?= tutor_enabled()?'Servicio independiente de la cuota mensual.':'La conversación todavía está desactivada. No se realizan cobros ni se consume saldo.' ?></p></div>
    <a class="mt-professor-feature-cta" href="/profesor.php">Abrir Profesor con IA <span aria-hidden="true">→</span></a>
  </section>
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

<style>
/* Dashboard: jerarquía visual, foco en la próxima acción y superficies más claras. */
body:has(.learning-dashboard) main > :first-child{display:none!important}

.learning-dashboard{max-width:1320px;padding-bottom:64px}
.learning-dashboard>.dash-head{margin:22px 0 18px;padding:30px 34px;border:1px solid #d9e6f2;border-radius:24px;background:linear-gradient(120deg,#f0f7ff 0%,#fff 78%);box-shadow:0 16px 42px -32px rgba(16,45,82,.35)}
.learning-dashboard>.dash-head h1{font-size:clamp(2rem,4vw,3rem);letter-spacing:-.045em;color:#102d52}
.learning-dashboard>.dash-head .muted{font-size:1.04rem;line-height:1.6}
.learning-dashboard>.mt-dashboard-start{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(260px,.85fr);gap:18px;margin:20px 0 28px}
.learning-dashboard .mt-start-main,.learning-dashboard .mt-start-side{min-height:220px;padding:clamp(24px,3vw,36px);border:1px solid #dce6ef;border-radius:24px;box-shadow:0 16px 38px -30px rgba(16,45,82,.35)}
.learning-dashboard .mt-start-main{display:flex;flex-direction:column;align-items:flex-start;justify-content:center;gap:12px;background:linear-gradient(125deg,#102d52 0%,#174f83 100%)!important;color:#fff!important}
.learning-dashboard .mt-start-main .eyebrow{color:#b9d7f2!important}
.learning-dashboard .mt-start-main h2{max-width:24ch;color:#fff!important;font-size:clamp(1.55rem,2.6vw,2.1rem);line-height:1.16}
.learning-dashboard .mt-start-main>p:not(.eyebrow){max-width:58ch;color:#d8e7f5!important;line-height:1.6}
.learning-dashboard .mt-start-main .btn{margin-top:5px;background:#c83342!important;color:#fff!important;border:1px solid #c83342!important;border-radius:12px;padding:12px 19px;font-weight:750;box-shadow:0 8px 22px -14px rgba(0,0,0,.7)}
.learning-dashboard .mt-start-side{display:flex;flex-direction:column;align-items:flex-start;justify-content:center;gap:10px;background:linear-gradient(145deg,#fff 0%,#f5f8fc 100%)}
.learning-dashboard .mt-start-side h3{font-size:1.25rem;line-height:1.25;color:#102d52}
.learning-dashboard .mt-start-side p{line-height:1.55;color:#526579}
.learning-dashboard .mt-start-side .mt-badge{color:#174f83;background:#e8f1fa;border-radius:999px;padding:6px 10px;font-size:.72rem;font-weight:800;letter-spacing:.08em}
.learning-dashboard .mt-start-side .mt-text-link{margin-top:4px;color:#c83342;font-weight:750}
.learning-dashboard .mt-professor-feature{margin:22px 0 26px;border-radius:22px}
.learning-dashboard .mt-roadmap,.learning-dashboard .mt-learning-route{box-shadow:0 14px 36px -30px rgba(16,45,82,.34)}
.learning-dashboard .stat-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:22px 0 30px}
.learning-dashboard .stat-row>.stat{min-width:0;border:1px solid #e0e8f0;border-radius:18px;background:#fff;padding:18px 20px;box-shadow:0 10px 26px -24px rgba(16,45,82,.45)}
.learning-dashboard .stat-row>.stat strong{color:#102d52}
.learning-dashboard .stat-row>.stat .stat-ico{font-size:1.3rem}
.learning-dashboard .mt-study-dashboard{overflow:hidden;border:1px solid #dce6ef;border-radius:22px;box-shadow:0 14px 34px -30px rgba(16,45,82,.35)}
.learning-dashboard .level-grid{gap:18px}
.learning-dashboard .level-card{border-radius:20px;transition:transform .18s ease,box-shadow .18s ease}
.learning-dashboard .level-card:hover{transform:translateY(-4px);box-shadow:0 18px 38px -28px rgba(16,45,82,.45)}
.learning-dashboard .desk-section-heading{margin-top:34px}
.learning-dashboard .upsell{margin:24px 0;border-radius:20px}
@media(max-width:850px){
 .learning-dashboard>.mt-dashboard-start{grid-template-columns:1fr}
 .learning-dashboard .stat-row{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:560px){
 body:has(.learning-dashboard) main > :has(a[href*="facebook.com/profile.php"]){display:none!important}
 .learning-dashboard>.dash-head{padding:22px;border-radius:18px}
 .learning-dashboard .mt-start-main,.learning-dashboard .mt-start-side{min-height:0;padding:22px;border-radius:18px}
 .learning-dashboard .stat-row{gap:8px}
 .learning-dashboard .stat-row>.stat{padding:14px;border-radius:15px}
}
html[data-theme=dark] .learning-dashboard>.dash-head{background:linear-gradient(120deg,#142c43,#172d42);border-color:#38556f}
html[data-theme=dark] .learning-dashboard>.dash-head h1,html[data-theme=dark] .learning-dashboard .mt-start-side h3{color:#eef5fc}
html[data-theme=dark] .learning-dashboard .mt-start-side,html[data-theme=dark] .learning-dashboard .stat-row>.stat{background:#172d42;border-color:#38556f}
html[data-theme=dark] .learning-dashboard .mt-start-side p,html[data-theme=dark] .learning-dashboard .stat-row>.stat strong{color:#dbe9f7}
</style>
