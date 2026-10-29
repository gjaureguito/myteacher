<?php
require dirname(__DIR__).'/src/bootstrap.php';
header('Cache-Control: no-store');
$error='';$receipt=null;
if(is_post()){
 csrf_check();
 $name=post_str('name',120);$email=strtolower(post_str('email',190));$kind=post_str('kind',20);$reference=post_str('reference',300);
 if(!in_array($kind,['withdrawal','cancellation'],true))$error='Elegí arrepentimiento o baja.';
 elseif(mb_strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL))$error='Completá tu nombre y un correo válido para identificar la contratación.';
 elseif(!empty($_POST['website']))$error='No pudimos registrar la solicitud.';
 else {
  $window=$_SESSION['consumer_window']??['start'=>time(),'count'=>0];
  if($window['start']<time()-3600)$window=['start'=>time(),'count'=>0];
  if($window['count']>=5){http_response_code(429);$error='Registraste varias solicitudes. Podés contactarnos por el correo publicado.';}
  else {
   $receipt='MT-'.gmdate('Ymd').'-'.strtoupper(bin2hex(random_bytes(6)));
   q('INSERT INTO consumer_requests(receipt,name,email,kind,reference,created_at) VALUES(?,?,?,?,?,?)',[$receipt,$name,$email,$kind,$reference,now_utc()]);
   $window['count']++;$_SESSION['consumer_window']=$window;
  }
 }
}
ob_start(); ?>
<section class="wrap page legal"><h1>Botón de arrepentimiento y baja</h1>
<p>Podés registrar una solicitud sin iniciar sesión y sin explicar sus motivos. Arrepentimiento y cancelación de futuras renovaciones son trámites distintos.</p>
<?php if($receipt): ?><div class="panel" role="status"><h2>Solicitud registrada</h2><p>Tu constancia: <strong><?= e($receipt) ?></strong></p><p>Fecha: <?= e(now_utc()) ?> UTC. Tipo: <?= $kind==='withdrawal'?'Arrepentimiento':'Baja del servicio' ?>. Correo informado: <?= e($email) ?>.</p><p>Guardá esta página o imprimila como constancia. El prestador revisará la identificación de la contratación y gestionará la solicitud y los reintegros que correspondan. Este registro no ejecuta por sí solo un reintegro ni confirma una baja en Mercado Pago.</p></div>
<?php else: ?>
<?php if($error): ?><p class="flash flash-error" role="alert"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="panel form"><?= csrf_field() ?>
 <label>Trámite<select name="kind"><option value="withdrawal">Arrepentimiento de la contratación</option><option value="cancellation">Baja / cancelar renovaciones</option></select></label>
 <label>Nombre completo<input name="name" required maxlength="120" autocomplete="name" value="<?= e(post_str('name',120)) ?>"></label>
 <label>Correo de la contratación<input name="email" type="email" required maxlength="190" autocomplete="email" value="<?= e(post_str('email',190)) ?>"></label>
 <label>Referencia del pago o fecha de contratación (opcional)<input name="reference" maxlength="300" value="<?= e(post_str('reference',300)) ?>"></label>
 <label style="display:none" aria-hidden="true">Dejá este campo vacío<input name="website" tabindex="-1" autocomplete="off"></label>
 <p class="fine">Usaremos estos datos para identificar y tramitar tu solicitud, según la <a href="/privacy.php">Política de privacidad</a>. No incluyas datos de tarjeta ni documentos.</p>
 <button class="btn">Registrar solicitud y obtener constancia</button>
</form><?php endif; ?>
<p>También podés escribir a <?= e(env('CONTACT_EMAIL','el correo de contacto del prestador')) ?>. Para una suscripción activa, podés cancelar futuras renovaciones desde <a href="/account.php">Mi cuenta</a>.</p><p>El derecho de arrepentimiento se reconoce en los términos y plazos legales aplicables a la contratación a distancia. No se exige fundamentar la decisión.</p>
</section>
<?php $html=ob_get_clean();$pageTitle='Arrepentimiento y baja';require ROOT.'/src/views/layout_top.php';echo $html;require ROOT.'/src/views/layout_bottom.php';
