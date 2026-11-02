<section class="auth wrap">
  <div class="auth-card">
    <p class="eyebrow">TU PRÓXIMO CAPÍTULO</p>
    <h1>Creá tu cuenta</h1>
    <p class="muted">7 días gratis de acceso completo desde tu registro, sin tarjeta. Después, continuás solo si elegís pagar por Mercado Pago. Una prueba por cuenta; tu progreso se conserva.</p>
    <p class="fine">Precio del plan al continuar: <?= money_ars(plan_price()) ?> ARS. Consultá duración y opciones en <a href="/#precio">planes</a>.</p>
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
      <label class="check"><input type="checkbox" name="terms" value="1" required><span>Acepto los <a href="/terms.php" target="_blank" rel="noopener">términos</a> y la <a href="/privacy.php" target="_blank" rel="noopener">política de privacidad</a>.</span></label>
      <button class="btn btn-block">Crear cuenta</button>
    </form>
    <p class="muted center">¿Ya tenés cuenta? <a href="/login.php">Ingresá</a></p>
  </div>
</section>
