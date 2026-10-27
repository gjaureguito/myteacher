<section class="wrap page">
  <p class="eyebrow">GESTIÓN DE LA ACADEMIA</p><h1>Resumen de actividad</h1><p class="muted">Accesos, suscripciones y contenido en un mismo lugar.</p>
  <?php require __DIR__ . '/admin_nav.php'; ?>

  <div class="stats">
    <?php foreach ($stats as $label => $n): ?>
      <div class="stat"><strong><?= $n ?></strong><span class="muted"><?= e($label) ?></span></div>
    <?php endforeach; ?>
    <div class="stat"><strong><?= money_ars($mrr) ?></strong><span class="muted">Ingreso mensual teórico de suscripciones activas</span></div>
  </div>

  <p class="fine">La estimación usa el precio actual y excluye accesos manuales. No representa cobros conciliados, descuentos ni ingresos históricos.</p>
  <h2>Usuarios</h2>
  <details class="panel admin-create">
    <summary><strong>Agregar usuario</strong></summary>
    <form method="post" class="form" autocomplete="off"><?= csrf_field() ?>
      <input type="hidden" name="action" value="create">
      <label>Email<input type="email" name="email" required maxlength="190" autocomplete="off"></label>
      <label>Nombre <small>(opcional)</small><input name="name" maxlength="120" autocomplete="off"></label>
      <label>Contraseña <small>(opcional, mínimo 8; si la dejás vacía generamos una y te la mostramos una sola vez)</small>
        <input type="text" name="password" minlength="8" autocomplete="new-password" spellcheck="false"></label>
      <label>Días de acceso completo <small>(0 = solo la demo gratuita)</small>
        <input type="number" name="days" value="30" min="0" max="3650"></label>
      <label>Rol
        <select name="role"><option value="student">Alumno</option><option value="admin">Administrador</option></select></label>
      <button class="btn">Crear usuario</button>
    </form>
  </details>
  <form class="form admin-search" method="get">
    <label>Buscar por email o nombre<input name="s" value="<?= e($s) ?>" type="search"></label><button class="btn">Buscar</button><a href="/admin/">Limpiar</a>
  </form>
  <div class="table-scroll" role="region" aria-label="Usuarios" tabindex="0">
  <table class="tbl"><caption>Hasta 100 usuarios, ordenados por alta más reciente.</caption>
    <thead><tr><th>Usuario</th><th>Alta</th><th>Ingreso</th><th>Estado</th><th>Acceso hasta</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php if (!$users): ?><tr><td colspan="6">No encontramos usuarios con esa búsqueda.</td></tr><?php endif; ?>
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
          <form method="post" class="inline" onsubmit="return confirm('¿Generar una contraseña nueva para <?= e($x['email']) ?>? La actual deja de funcionar.')"><?= csrf_field() ?>
            <input type="hidden" name="user_id" value="<?= (int) $x['id'] ?>"><input type="hidden" name="action" value="newpass">
            <button class="linkbtn">Nueva contraseña</button>
          </form>
          <form method="post" class="inline"><?= csrf_field() ?>
            <input type="hidden" name="user_id" value="<?= (int) $x['id'] ?>"><input type="hidden" name="action" value="sendlink">
            <button class="linkbtn">Enviar enlace de recuperación</button>
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
  <div class="table-scroll" role="region" aria-label="Notificaciones de pagos" tabindex="0"><table class="tbl"><caption>Últimas 15 notificaciones recibidas.</caption>
    <thead><tr><th>Fecha</th><th>Evento</th><th>ID</th><th>Usuario</th><th>Estado</th></tr></thead>
    <tbody>
    <?php foreach ($log as $l): ?>
      <tr><td><?= fmt_date($l['created_at']) ?></td><td><?= e($l['topic']) ?></td><td><?= e($l['resource_id']) ?></td><td><?= e($l['email'] ?? '—') ?></td><td><?= e($l['status'] ?? '—') ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
