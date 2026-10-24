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

  <ol class="units">
    <?php foreach ($units as $un):
      $open = can_access_unit($u, $un);
      $p = $up[(int) $un['id']] ?? ['total' => 0, 'solved' => 0];
      $pc = pct($p);
    ?>
      <li class="unit-row<?= $open ? '' : ' is-locked' ?>">
        <a href="<?= $open ? '/unit.php?id=' . (int) $un['id'] : '/account.php' ?>">
          <span class="unit-num"><?= (int) $un['position'] ?></span>
          <span class="unit-body">
            <strong><?= e($un['title']) ?></strong>
            <span class="muted"><?= e($un['summary']) ?></span>
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
