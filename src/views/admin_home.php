<section class="wrap page">
  <h1>Administración</h1>
  <?php require __DIR__ . '/admin_nav.php'; ?>

  <div class="stats">
    <?php foreach ($stats as $label => $n): ?>
      <div class="stat"><strong><?= $n ?></strong><span class="muted"><?= e($label) ?></span></div>
    <?php endforeach; ?>
    <div class="stat"><strong><?= money_ars($mrr) ?></strong><span class="muted">Ingreso mensual estimado</span></div>
  </div>

  <h2>Usuarios</h2>
  <form class="form" method="get" style="max-width: 420px">
    <label>Buscar por email o nombre<input name="s" value="<?= e($s) ?>"></label>
  </form>
  <div style="overflow-x:auto">
  <table class="tbl">
    <thead><tr><th>Usuario</th><th>Alta</th><th>Ingreso</th><th>Estado</th><th>Acceso hasta</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($users as $x): ?>
      <tr>
        <td><strong><?= e($x['name']) ?></strong><br><span class="muted"><?= e($x['email']) ?></span></td>
        <td><?= fmt_date($x['created_at']) ?></td>
        <td><?= $x['google_sub'] ? 'Google ' : '' ?><?= $x['microsoft_sub'] ? 'Microsoft ' : '' ?><?= $x['password_hash'] ? 'Email' : '' ?></td>
        <td><?= e($x['sub_status']) ?></td>
        <td><?= fmt_date($x['access_until']) ?></td>
        <td>
          <form method="post" class="inline"><?= csrf_field() ?>
            <input type="hidden" name="user_id" value="<?= (int) $x['id'] ?>"><input type="hidden" name="action" value="grant">
            <input type="number" name="days" value="30" min="1" max="365" style="width:70px" aria-label="Días">
            <button class="btn btn-sm btn-ghost">Dar acceso</button>
          </form>
          <?php if ($x['access_until']): ?>
          <form method="post" class="inline" onsubmit="return confirm('¿Quitar el acceso?')"><?= csrf_field() ?>
            <input type="hidden" name="user_id" value="<?= (int) $x['id'] ?>"><input type="hidden" name="action" value="revoke">
            <button class="linkbtn">Quitar</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>

  <h2>Últimas notificaciones de Mercado Pago</h2>
  <?php if (!$log): ?>
    <p class="muted">Todavía no llegó ninguna. Cuando configures el webhook vas a ver acá cada aviso de alta, cobro o cancelación.</p>
  <?php else: ?>
  <table class="tbl">
    <thead><tr><th>Fecha</th><th>Evento</th><th>ID</th><th>Usuario</th><th>Estado</th></tr></thead>
    <tbody>
    <?php foreach ($log as $l): ?>
      <tr><td><?= fmt_date($l['created_at']) ?></td><td><?= e($l['topic']) ?></td><td><?= e($l['resource_id']) ?></td><td><?= e($l['email'] ?? '—') ?></td><td><?= e($l['status'] ?? '—') ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</section>
