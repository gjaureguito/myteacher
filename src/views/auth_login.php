<section class="auth wrap">
  <div class="auth-card">
    <p class="eyebrow">VOLVÉ A TU ESPACIO DE ESTUDIO</p>
    <h1>Ingresá a tu cuenta</h1>
    <p class="muted">Tus unidades, ejercicios y próximos descubrimientos.</p>
    <?php require __DIR__ . '/_social.php'; ?>
    <?php if ($errors): ?>
      <div class="flash flash-error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div>
    <?php endif; ?>
    <form method="post" class="form">
      <?= csrf_field() ?>
      <label>Email<input type="email" name="email" value="<?= e($email) ?>" autocomplete="email" required></label>
      <label>Contraseña<input type="password" name="password" autocomplete="current-password" required></label>
      <button class="btn btn-block">Ingresar</button>
    </form>
    <p class="muted center"><a href="/forgot.php<?= $email ? '?email=' . e(rawurlencode($email)) : '' ?>">¿Olvidaste tu contraseña?</a></p>
    <p class="muted center">¿No tenés cuenta? <a href="/register.php">Creala gratis</a></p>
  </div>
</section>
