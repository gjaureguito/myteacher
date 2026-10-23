<?php
require dirname(__DIR__) . '/src/bootstrap.php';
ob_start(); ?>
<section class="wrap page legal">
  <h1>Política de privacidad</h1>
  <p class="muted">Última actualización: <?= date('d/m/Y') ?>. Revisá este texto con tu asesor legal antes de publicar.</p>
  <h2>Qué datos guardamos</h2>
  <p>Tu nombre, tu email, la contraseña cifrada (si te registrás con email), el identificador de tu cuenta de Google o Microsoft (si ingresás con ellas), tu progreso en los ejercicios y el estado de tu suscripción.</p>
  <h2>Pagos</h2>
  <p>Los pagos se procesan en Mercado Pago. Nunca vemos ni guardamos los datos de tu tarjeta; solo recibimos el estado de la suscripción.</p>
  <h2>Para qué los usamos</h2>
  <p>Para darte acceso a la plataforma, guardar tu progreso, gestionar la suscripción y contactarte por temas de tu cuenta. No vendemos ni cedemos tus datos.</p>
  <h2>Cookies</h2>
  <p>Usamos una única cookie de sesión, necesaria para mantenerte conectado.</p>
  <h2>Tus derechos</h2>
  <p>Podés pedir acceso, corrección o eliminación de tus datos escribiendo a <?= e(env('CONTACT_EMAIL', 'nuestro email de contacto')) ?>, conforme a la Ley 25.326 de Protección de Datos Personales. La Agencia de Acceso a la Información Pública es el órgano de control de esa ley.</p>
</section>
<?php $html = ob_get_clean();
$pageTitle = 'Privacidad';
require ROOT . '/src/views/layout_top.php'; echo $html; require ROOT . '/src/views/layout_bottom.php';
