<section class="wrap page review-page">
  <p class="eyebrow">ERROR NOTEBOOK / CUADERNO DE ERRORES</p>
  <div class="dash-head">
    <div>
      <h1>Repaso</h1>
      <p class="muted">Volvé sobre lo que te costó. Cada ejercicio que resolvés acá cuenta para tu progreso y tus estadísticas.</p>
    </div>
    <a class="btn btn-ghost" href="/stats.php">Ver estadísticas</a>
  </div>
  <div class="tabs review-tabs" role="navigation" aria-label="Tipo de repaso">
    <a href="/review.php" role="tab" aria-selected="<?= $mode === 'errores' ? 'true' : 'false' ?>">Pendientes <span class="count"><?= $countErr ?></span></a>
    <a href="/review.php?m=refuerzo" role="tab" aria-selected="<?= $mode === 'refuerzo' ? 'true' : 'false' ?>">Para reforzar <span class="count"><?= $countRef ?></span></a>
  </div>
  <?php if (!$rows): ?>
    <div class="empty-state">
      <div class="empty-ico" aria-hidden="true"><?= $mode === 'errores' ? '🎉' : '🌱' ?></div>
      <h2><?= $mode === 'errores' ? 'No tenés errores pendientes' : 'Nada para reforzar todavía' ?></h2>
      <p class="muted"><?= $mode === 'errores' ? 'Cuando un ejercicio te salga mal, aparece acá para que lo vuelvas a intentar.' : 'Acá aparecen los ejercicios que resolviste después de varios intentos.' ?></p>
      <a class="btn" href="/dashboard.php">Seguir con mis unidades</a>
    </div>
  <?php else: ?>
    <div class="tabpanel" id="t-practice">
      <?php $exList = $rows; $groupBySkill = false; require __DIR__ . '/_exercise.php'; ?>
    </div>
  <?php endif; ?>
</section>
