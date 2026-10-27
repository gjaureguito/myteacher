<section class="auth wrap">
  <div class="auth-card">
    <p class="eyebrow">RECUPERAR EL ACCESO</p>
<?php if (!$valid): ?>
    <h1>Este enlace ya no sirve</h1>
    <p class="muted">Los enlaces vencen a los 60 minutos y se pueden usar una sola vez. Si pediste más de uno, solo funciona el último.</p>
    <p><a class="btn btn-block" href="/forgot.php">Pedir un enlace nuevo</a></p>
    <p class="muted center"><a href="/login.php">Volver a ingresar</a></p>
<?php else: ?>
    <h1>Elegí una contraseña nueva</h1>
    <p class="muted">Para la cuenta <strong><?= e($email) ?></strong>.</p>
    <?php if ($errors): ?>
      <div class="flash flash-error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div>
    <?php endif; ?>
    <form method="post" class="form" action="/reset_password.php">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <input type="text" name="username" value="<?= e($email) ?>" autocomplete="username" hidden>
      <label>Contraseña nueva<input type="password" name="password" minlength="8" maxlength="200" autocomplete="new-password" required autofocus></label>
      <label>Repetila<input type="password" name="password2" minlength="8" maxlength="200" autocomplete="new-password" required></label>
      <p class="muted" style="margin:0">Mínimo 8 caracteres.</p>
      <button class="btn btn-block">Guardar y entrar</button>
    </form>
<?php endif; ?>
  </div>
</section>
