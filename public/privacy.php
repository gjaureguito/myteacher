<?php
require dirname(__DIR__) . '/src/bootstrap.php';
ob_start(); ?>
<section class="wrap page legal">
  <h1>Política de privacidad</h1>
  <p class="muted">Última actualización: <?= date('d/m/Y') ?>. Revisá este texto con tu asesor legal antes de publicar.</p>
  <h2>Qué datos guardamos</h2>
  <p>Tu nombre, tu email, la contraseña cifrada (si te registrás con email), el identificador de tu cuenta de Google o Microsoft (si ingresás con ellas), tu progreso en los ejercicios y el estado de tu suscripción.</p>
  <h2>Constancia de contratación</h2><p>Guardamos la fecha, precio, modalidad, versión y texto del contrato aceptado. Podés consultarlo desde Mi cuenta. También conservamos las solicitudes de baja y arrepentimiento para gestionarlas y cumplir obligaciones legales.</p>
  <h2>Datos de facturación</h2>
  <p>Para gestionar comprobantes fiscales guardamos la condición frente al IVA, nombre o razón social y, cuando corresponda o se informen voluntariamente, DNI, CUIL o CUIT y domicilio de facturación. Conservamos estos datos asociados a la contratación durante los plazos necesarios para cumplir obligaciones fiscales y atender consultas. Se informarán a ARCA cuando corresponda para emitir el comprobante. Podés solicitar su rectificación mediante nuestro correo de contacto.</p>
  <h2>Pagos</h2>
  <p>Los pagos se procesan en Mercado Pago. Nunca vemos ni guardamos los datos de tu tarjeta; solo recibimos el estado de la suscripción.</p>
  <h2>Para qué los usamos</h2>
  <p>Para darte acceso a la plataforma, guardar tu progreso, gestionar la suscripción y contactarte por temas de tu cuenta. No vendemos tus datos. Solo los comunicamos cuando resulta necesario para prestar el servicio o cumplir obligaciones legales.</p>
  <h2>Cookies</h2>
  <p>Usamos una única cookie de sesión, necesaria para mantenerte conectado.</p>
  <h2>Tus derechos</h2>
  <p>Podés pedir acceso, corrección o eliminación de tus datos escribiendo a <?= e(env('CONTACT_EMAIL', 'nuestro email de contacto')) ?>, conforme a la Ley 25.326 de Protección de Datos Personales. La Agencia de Acceso a la Información Pública es el órgano de control de esa ley.</p>
</section>
<?php $html = ob_get_clean();
$pageTitle = 'Privacidad';
require ROOT . '/src/views/layout_top.php'; echo $html; require ROOT . '/src/views/layout_bottom.php';
