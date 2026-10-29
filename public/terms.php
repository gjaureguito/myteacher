<?php
require dirname(__DIR__).'/src/bootstrap.php';
require ROOT.'/src/mercadopago.php';require ROOT.'/src/contract.php';
$document=purchase_contract(['name'=>'Persona que contrata','email'=>'correo de su cuenta'],mp_mode()==='once'?'once':'subscription');
ob_start(); ?>
<section class="wrap page legal"><h1>Términos y condiciones del servicio</h1><p>Versión <?= e($document['version']) ?>. Antes de cada nuevo pago se muestra el contrato con tu identidad, el precio y la modalidad que vas a contratar. La cuenta gratuita permite explorar la demo sin contratar el acceso pago.</p>
<?php if(mp_mode()==='both'): ?><p>Hay suscripción mensual y pago único. La revisión previa al pago especifica la modalidad y duración elegidas.</p><?php endif; ?>
<?php foreach($document['sections'] as $heading=>$body): ?><section><h2><?= e($heading) ?></h2><p><?= e($body) ?></p></section><?php endforeach; ?>
<p><a href="/consumer-request.php">Botón de arrepentimiento y baja</a> · <a href="/privacy.php">Política de privacidad</a></p></section>
<?php $html=ob_get_clean();$pageTitle='Términos';require ROOT.'/src/views/layout_top.php';echo $html;require ROOT.'/src/views/layout_bottom.php';
