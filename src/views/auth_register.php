<section class="auth wrap">
  <div class="auth-card">
    <p class="eyebrow">TU PRÓXIMO CAPÍTULO</p>
    <h1>Creá tu cuenta</h1>
    <p class="muted">Gratis y sin tarjeta. Accedés a la primera unidad de cada nivel y al test de nivel.</p>
    <?php require __DIR__ . '/_social.php'; ?>
    <?php if ($errors): ?>
      <div class="flash flash-error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div>
    <?php endif; ?>
    <form method="post" class="form">
      <?= csrf_field() ?>
      <label>Nombre<input name="name" value="<?= e($old['name']) ?>" autocomplete="name" required></label>
      <label>Email<input type="email" name="email" value="<?= e($old['email']) ?>" autocomplete="email" required></label>
      <label>Contraseña<input type="password" name="password" minlength="8" autocomplete="new-password" required>
        <small>Mínimo 8 caracteres.</small></label>
      <label class="check"><input type="checkbox" name="terms" value="1" required> Acepto los <a href="/terms.php" target="_blank" rel="noopener">términos</a> y la <a href="/privacy.php" target="_blank" rel="noopener">política de privacidad</a></label>
      <button class="btn btn-block">Crear cuenta</button>
    </form>
    <p class="muted center">¿Ya tenés cuenta? <a href="/login.php">Ingresá</a></p>
  </div>
</section>
