<section class="wrap page">
  <p class="eyebrow">GESTIÓN DE LA ACADEMIA</p><h1>Mercado Pago</h1>
  <p class="muted">Credenciales, precio y forma de cobro. Los cambios se aplican al instante, sin redesplegar.</p>
  <?php require __DIR__ . '/admin_nav.php'; ?>

  <div class="grid-2">
    <div class="panel">
      <h2>Estado</h2>
      <?php if ($cfg['token_unreadable']): ?>
        <p class="warn">El token guardado no se puede leer porque cambiaron las variables de cifrado del servidor. Volvé a cargarlo.</p>
      <?php endif; ?>
      <?php if ($cfg['token_panel']): ?>
        <p class="status-line"><span class="pill pill-ok">Conectado</span>
          <?php if ($cfg['test']): ?> <span class="pill pill-demo">Modo prueba</span><?php endif; ?></p>
        <p>Cuenta: <strong><?= e($cfg['account'] ?? '—') ?></strong></p>
        <p class="fine">Token: <code><?= e(mask_secret($cfg['token_panel'])) ?></code></p>
      <?php elseif ($cfg['token_env']): ?>
        <p class="status-line"><span class="pill pill-ok">Conectado por variable</span></p>
        <p class="fine">Se usa MP_ACCESS_TOKEN de Railway (<code><?= e(mask_secret($cfg['token_env'])) ?></code>). Si cargás un token acá, tiene prioridad.</p>
      <?php else: ?>
        <p class="status-line"><span class="pill pill-demo">Sin configurar</span></p>
        <p class="muted">Los alumnos todavía no pueden pagar. Cargá el Access Token para habilitar el cobro.</p>
      <?php endif; ?>
      <p>Cobro para alumnos: <strong><?= $cfg['enabled'] && ($cfg['token_panel'] || $cfg['token_env']) ? 'habilitado' : 'deshabilitado' ?></strong></p>

      <form method="post" action="/admin/mp_connect.php" class="inline"><?= csrf_field() ?>
        <button class="btn" <?= $cfg['oauth_ready'] ? '' : 'disabled' ?>><?= $cfg['oauth_connected'] ? 'Reconectar Mercado Pago' : 'Conectar Mercado Pago' ?></button></form>
      <p class="fine"><?php if ($cfg['oauth_ready']): ?>Ingresás en Mercado Pago, autorizás tu cuenta y volvés al panel. No tenés que copiar claves.<?php else: ?>Para activar el botón, creá la aplicación en Mercado Pago (Tus integraciones) y cargá <code>MP_CLIENT_ID</code> y <code>MP_CLIENT_SECRET</code> en Railway. En la aplicación, registrá esta URL de redirección: <code><?= e($cfg['redirect_uri']) ?></code><?php endif; ?></p>

      <?php if ($cfg['token_panel'] || $cfg['token_env']): ?>
        <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="test">
          <button class="btn btn-ghost btn-sm">Probar conexión</button></form>
      <?php endif; ?>
      <?php if ($cfg['token_panel']): ?>
        <form method="post" class="inline" onsubmit="return confirm('¿Quitar el token guardado? Los alumnos no van a poder pagar hasta que cargues otro.')"><?= csrf_field() ?>
          <input type="hidden" name="action" value="disconnect"><button class="linkbtn">Desconectar</button></form>
      <?php endif; ?>

      <h3>Webhook</h3>
      <p class="fine">En Mercado Pago → Tus integraciones → tu aplicación → Webhooks, pegá esta URL y marcá los eventos <strong>Pagos</strong> y <strong>Planes y suscripciones</strong>:</p>
      <p><code><?= e($cfg['webhook_url']) ?></code></p>
      <p class="fine">Clave secreta del webhook: <?= $cfg['secret'] ? '<strong>cargada</strong>' : 'sin cargar (opcional: igual verificamos cada aviso consultando la API)' ?>.</p>
    </div>

    <div class="panel">
      <h2>Configuración</h2>
      <form method="post" class="form" autocomplete="off"><?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <label>Access Token
          <input name="mp_access_token" type="password" placeholder="<?= $cfg['token_panel'] ? 'Guardado — dejá vacío para no cambiarlo' : 'APP_USR-…' ?>" autocomplete="new-password" spellcheck="false">
          <small>Tus integraciones → tu aplicación → Credenciales de producción. Con un token TEST- se usa el entorno de prueba. Lo validamos con Mercado Pago antes de guardarlo y queda cifrado.</small>
        </label>
        <label>Clave secreta del webhook <small>(opcional)</small>
          <input name="mp_webhook_secret" type="password" placeholder="<?= $cfg['secret'] ? 'Guardada — dejá vacío para no cambiarla' : '' ?>" autocomplete="new-password" spellcheck="false">
        </label>
        <?php if ($cfg['secret']): ?><label class="check"><input type="checkbox" name="clear_secret" value="1"> Borrar la clave secreta guardada</label><?php endif; ?>

        <label>Precio en pesos
          <input name="plan_price" type="number" min="100" max="10000000" step="1" value="<?= (int) $cfg['price'] ?>" required>
          <small>Monto de la suscripción mensual o del pago único. Las suscripciones ya activas mantienen su precio.</small>
        </label>

        <label>Forma de cobro
          <select name="mp_mode">
            <option value="subscription" <?= $cfg['mode'] === 'subscription' ? 'selected' : '' ?>>Suscripción mensual (débito automático)</option>
            <option value="once" <?= $cfg['mode'] === 'once' ? 'selected' : '' ?>>Pago único por período (tarjeta, débito, dinero en cuenta o efectivo)</option>
            <option value="both" <?= $cfg['mode'] === 'both' ? 'selected' : '' ?>>Ambas: el alumno elige</option>
          </select>
        </label>
        <label>Días de acceso por pago único
          <input name="mp_once_days" type="number" min="1" max="365" value="<?= (int) $cfg['once_days'] ?>">
        </label>

        <label class="check"><input type="checkbox" name="mp_enabled" value="1" <?= $cfg['enabled'] ? 'checked' : '' ?>> Habilitar el cobro para los alumnos</label>
        <button class="btn">Guardar</button>
      </form>
    </div>
  </div>

  <h2>Pagos únicos recientes</h2>
  <?php if (!$payments): ?>
    <p class="muted">Todavía no hay pagos únicos. Las suscripciones aparecen en Usuarios y pagos.</p>
  <?php else: ?>
  <div class="table-scroll" role="region" aria-label="Pagos únicos" tabindex="0">
    <table class="tbl"><caption>Últimos 20 pagos únicos.</caption>
      <thead><tr><th>Fecha</th><th>Usuario</th><th>Monto</th><th>Días</th><th>Estado</th><th>ID de pago</th></tr></thead>
      <tbody>
      <?php foreach ($payments as $p): ?>
        <tr><td><?= fmt_date($p['created_at']) ?></td><td><?= e($p['email'] ?? '—') ?></td><td><?= money_ars((float) $p['amount']) ?></td>
          <td><?= (int) $p['days'] ?></td><td><?= e($p['status']) ?><?= $p['credited'] ? ' · acreditado' : '' ?></td><td><?= e($p['payment_id']) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>
