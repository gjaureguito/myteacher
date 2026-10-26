<section class="wrap page" style="--lv: <?= e($color) ?>">
  <p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / <?= e(strtoupper($level['code'])) ?></p>
  <header class="level-head">
    <img src="/assets/images/levels/<?= e($level['code']) ?>.svg" width="96" height="96" alt="Nivel <?= e(strtoupper($level['code'])) ?>">
    <div>
      <h1><?= e($level['name']) ?></h1>
      <p class="muted"><?= e($level['description']) ?></p>
      <p class="fine">Cada unidad combina teoría, vocabulario, ejercicios y un taller de lectura y escritura cuando está disponible.</p>
    </div>
  </header>

  <link rel="stylesheet" href="/assets/roadmap.css?v=1">
  <section class="mt-roadmap" aria-labelledby="unit-roadmap-heading">
    <p class="eyebrow">RUTA DE APRENDIZAJE · <?= e(strtoupper($level['code'])) ?></p>
    <h2 id="unit-roadmap-heading">Tu recorrido por las unidades</h2>
    <p class="mt-roadmap-subtitle">Cada parada lleva a su unidad de estudio. El estado se basa en ejercicios resueltos y se actualiza con tu práctica.</p>
    <ol class="mt-unit-roadmap">
    <?php $nextOpenFound=false; foreach ($units as $un):
      $open=can_access_unit($u,$un);
      $p=$up[(int)$un['id']]??['total'=>0,'solved'=>0];
      $pc=pct($p);
      $done=$p['total']>0&&$pc===100;
      $active=$open&&!$done&&!$nextOpenFound;
      if($active)$nextOpenFound=true;
      $state=!$open?'is-locked':($done?'is-done':($active?'is-current':'is-next'));
      $status=!$open?'Con suscripción':($done?'Completada':($active?'Siguiente paso':'Por estudiar'));
    ?>
      <li class="<?= $state ?>"><a href="<?= $open?'/unit.php?id='.(int)$un['id']:'/account.php' ?>">
        <span class="mt-unit-node"><?= $done?'✓':(int)$un['position'] ?></span>
        <span class="mt-unit-copy"><strong><?= e($un['title']) ?></strong><small><?= $p['solved'] ?>/<?= $p['total'] ?> ejercicios · <?= $pc ?>% completado</small><span class="mt-unit-bar" aria-hidden="true"><i style="width:<?= $pc ?>%"></i></span></span>
        <span class="mt-unit-status"><?= e($status) ?></span>
      </a></li>
    <?php endforeach; ?>
    </ol>
    <p class="mt-roadmap-subtitle"><a href="/stats.php">Ver estadísticas y temas para reforzar ↗</a></p>
  </section>
  <a class="book-link panel" href="/book.php?code=<?= e($level['code']) ?>"><strong>Leer el libro de estudio <?= e(strtoupper($level['code'])) ?> →</strong><span>Capítulos con teoría desarrollada, ejemplos analizados, práctica guiada y un proyecto de cierre.</span></a>
  <ol class="units">
    <?php foreach ($units as $un):
      $open = can_access_unit($u, $un);
      $p = $up[(int) $un['id']] ?? ['total' => 0, 'solved' => 0];
      $pc = pct($p);
    ?>
      <li class="unit-row<?= $open ? '' : ' is-locked' ?>">
        <a href="<?= $open ? '/unit.php?id=' . (int) $un['id'] : '/account.php' ?>">
          <span class="unit-num"><?= $un['icon'] !== '' ? e($un['icon']) : (int) $un['position'] ?></span>
          <span class="unit-body">
            <strong><?= e($un['title']) ?></strong>
            <span class="muted"><?= e($un['summary']) ?></span>
            <span class="unit-skills"><span class="unit-pos">Unidad <?= (int) $un['position'] ?></span><?php foreach (skill_labels() as $sk => $lab): if (empty($skillCounts[(int) $un['id']][$sk])) continue; ?><span class="skill-<?= e($sk) ?>" title="<?= e($lab[0]) ?>"><?= $lab[2] ?> <?= $skillCounts[(int) $un['id']][$sk] ?></span><?php endforeach; ?></span>
          </span>
          <span class="unit-state">
            <?php if (!$open): ?>
              <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V7a5 5 0 0 0-5-5zm-3 8V7a3 3 0 1 1 6 0v3H9z"/></svg>
              <span>Con suscripción</span>
            <?php elseif ($p['total']): ?>
              <span class="ring" style="--p: <?= $pc ?>"><span><?= $pc ?>%</span></span>
            <?php endif; ?>
            <?php if ($open && is_demo_unit($un) && !has_full_access($u)): ?><em class="tag">Demo</em><?php endif; ?>
          </span>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>
</section>
