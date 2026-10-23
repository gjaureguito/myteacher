<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$owner = env('LEGAL_OWNER', app_name());
ob_start(); ?>
<section class="wrap page legal">
  <h1>Términos y condiciones</h1>
  <p class="muted">Última actualización: <?= date('d/m/Y') ?>. Revisá este texto con tu asesor legal antes de publicar.</p>
  <h2>1. El servicio</h2>
  <p><?= e(app_name()) ?> es una plataforma online para aprender inglés, operada por <?= e($owner) ?>. Al crear una cuenta aceptás estos términos.</p>
  <h2>2. Cuenta y demo</h2>
  <p>El registro es gratuito e incluye acceso de prueba a la primera unidad de cada nivel y al test de nivel. La cuenta es personal e intransferible.</p>
  <h2>3. Suscripción y pagos</h2>
  <p>El acceso completo cuesta <?= money_ars(plan_price()) ?> por mes (precio final, en pesos argentinos), cobrado por Mercado Pago como débito automático mensual. El precio puede actualizarse con aviso previo de 30 días por email.</p>
  <h2>4. Cancelación y arrepentimiento</h2>
  <p>Podés cancelar en cualquier momento desde "Mi cuenta"; conservás el acceso hasta el final del período pago. Conforme a la Ley 24.240 de Defensa del Consumidor, tenés 10 días corridos desde la primera contratación para revocar la aceptación y pedir el reintegro total escribiendo a <?= e(env('CONTACT_EMAIL', 'nuestro email de contacto')) ?>.</p>
  <h2>5. Uso aceptable</h2>
  <p>No está permitido copiar, revender ni distribuir el contenido de la plataforma, ni compartir la cuenta con terceros.</p>
  <h2>6. Contacto</h2>
  <p>Consultas: <?= e(env('CONTACT_EMAIL', '—')) ?>.</p>
</section>
<?php $html = ob_get_clean();
$pageTitle = 'Términos';
require ROOT . '/src/views/layout_top.php'; echo $html; require ROOT . '/src/views/layout_bottom.php';
