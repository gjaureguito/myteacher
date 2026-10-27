<section class="auth wrap">
  <div class="auth-card">
    <p class="eyebrow">RECUPERAR EL ACCESO</p>
<?php if ($sent): ?>
    <h1>Revisá tu correo</h1>
    <p class="muted">Si <strong><?= e($email) ?></strong> tiene una cuenta en <?= e(app_name()) ?>, en unos minutos te llega un email con un enlace para elegir una contraseña nueva.</p>
    <p class="muted">El enlace vence en 60 minutos. Si no lo ves, mirá en la carpeta de <strong>spam</strong> o «Promociones».</p>
    <p class="muted center"><a href="/login.php">Volver a ingresar</a> · <a href="/forgot.php?email=<?= e(rawurlencode($email)) ?>">Enviar otra vez</a></p>
<?php else: ?>
    <h1>¿Olvidaste tu contraseña?</h1>
    <p class="muted">Escribí el email de tu cuenta y te mandamos un enlace para elegir una nueva.</p>
    <?php if ($errors): ?>
      <div class="flash flash-error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div>
    <?php endif; ?>
    <form method="post" class="form">
      <?= csrf_field() ?>
      <label>Email<input type="email" name="email" value="<?= e($email) ?>" autocomplete="email" required autofocus></label>
      <button class="btn btn-block">Enviarme el enlace</button>
    </form>
    <p class="muted center">Si tu email es de Google, también podés <a href="/auth/start.php?provider=google">ingresar con Google</a> sin contraseña.</p>
    <p class="muted center"><a href="/login.php">Volver a ingresar</a></p>
<?php endif; ?>
  </div>
</section>
