<?php $full = has_full_access($u); ?>
<section class="wrap page">
  <h1>Mi cuenta</h1>

  <div class="grid-2">
    <div class="panel">
      <h2>Suscripción</h2>
      <p class="status-line"><span class="pill <?= $full ? 'pill-ok' : 'pill-demo' ?>"><?= e(sub_label($u)) ?></span></p>

      <?php if (is_admin($u)): ?>
        <p class="muted">Como administrador tenés acceso completo sin suscripción.</p>
      <?php elseif ($u['sub_status'] === 'active' && $full): ?>
        <p>Tu suscripción se renueva mensualmente por Mercado Pago. El importe vigente es el de tu autorización de pago.</p>
        <form method="post" onsubmit="return confirm('¿Cancelar la suscripción? Mantenés el acceso hasta el fin del período pago.')">
          <?= csrf_field() ?><input type="hidden" name="action" value="cancel">
          <button class="btn btn-ghost">Cancelar suscripción</button>
        </form>
      <?php else: ?>
        <?php if ($u['sub_status'] === 'pending'): ?>
          <p class="note">Iniciaste el pago y estamos esperando la confirmación de Mercado Pago.</p>
          <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="refresh">
            <button class="btn btn-ghost btn-sm">Ya pagué, actualizar estado</button></form>
        <?php endif; ?>
        <div class="price-box">
          <?php $mode = mp_mode(); $days = mp_once_days(); ?>
          <p class="price"><?= money_ars(plan_price()) ?><span><?= $mode === 'once' ? ' por ' . $days . ' días' : '/mes' ?></span></p>
          <p>Acceso completo a los 6 niveles, todas las unidades, ejercicios con corrección y audio.<?= $mode === 'once' ? ' Sin débito automático: pagás cuando querés renovar.' : ' Cancelás cuando quieras.' ?></p>
          <?php if (!mp_enabled()): ?>
            <p class="note">El pago online todavía no está disponible. Escribinos y te habilitamos el acceso.</p>
          <?php else: ?>
            <?php if ($mode !== 'once'): ?>
              <form method="post" action="/subscribe.php"><?= csrf_field() ?><input type="hidden" name="kind" value="subscription">
                <button class="btn btn-mp">Suscribirme con Mercado Pago</button></form>
            <?php endif; ?>
            <?php if ($mode !== 'subscription'): ?>
              <form method="post" action="/subscribe.php" style="margin-top:10px"><?= csrf_field() ?><input type="hidden" name="kind" value="once">
                <button class="btn <?= $mode === 'once' ? 'btn-mp' : 'btn-ghost' ?>" style="width:100%">Pagar <?= $days ?> días con Mercado Pago</button></form>
              <p class="fine">El pago único acepta tarjeta, débito, dinero en cuenta y efectivo (Rapipago, Pago Fácil).</p>
            <?php endif; ?>
            <p class="fine">Usá en Mercado Pago el mismo email de tu cuenta (<?= e($u['email']) ?>) para que el pago se asocie sin demoras.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="panel">
      <h2>Tus datos</h2>
      <form method="post" class="form">
        <?= csrf_field() ?><input type="hidden" name="action" value="profile">
        <label>Nombre<input name="name" value="<?= e($u['name']) ?>" required></label>
        <label>Email<input value="<?= e($u['email']) ?>" disabled></label>
        <button class="btn btn-ghost">Guardar</button>
      </form>
      <h3>Contraseña</h3>
      <form method="post" class="form">
        <?= csrf_field() ?><input type="hidden" name="action" value="password">
        <?php if ($u['password_hash']): ?>
          <label>Contraseña actual<input type="password" name="current_password" autocomplete="current-password" required></label>
        <?php else: ?>
          <p class="muted">Ingresás con Google o Microsoft. Si querés, podés crear una contraseña para entrar también con email.</p>
        <?php endif; ?>
        <label>Nueva contraseña<input type="password" name="new_password" minlength="8" autocomplete="new-password" required></label>
        <button class="btn btn-ghost">Cambiar contraseña</button>
      </form>
    </div>
  </div>
</section>
