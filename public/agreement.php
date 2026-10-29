<?php
require dirname(__DIR__).'/src/bootstrap.php';
$u=require_login();
require ROOT.'/src/billing.php';
$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);
$agreement=$id?q_one('SELECT * FROM purchase_agreements WHERE id=?',[$id]):null;
if(!$agreement || ((int)$agreement['user_id']!==(int)$u['id'] && !is_admin($u))){http_response_code(404);render('notfound',[],'No encontrado');exit;}
header('Cache-Control: private, no-store');
$billingRow=q_one('SELECT billing_data FROM purchase_billing WHERE nonce=? AND user_id=?',[$agreement['nonce'],$agreement['user_id']]);
$billingText=$billingRow?billing_summary(json_decode($billingRow['billing_data'],true,512,JSON_THROW_ON_ERROR)):'';
if(($_GET['download']??'')==='1'){
 header('Content-Type: text/plain; charset=utf-8');header('Content-Disposition: attachment; filename="myteacher-contrato-'.(int)$agreement['id'].'.txt"');
 echo 'Aceptado el '.$agreement['accepted_at']." UTC\n".'Constancia #'.$agreement['id']."\n\n".$agreement['document_text']."\n\n".$billingText;exit;
}
ob_start(); ?>
<?php if($billingText): ?><section class="wrap page legal"><h2>Datos de facturación</h2><p style="white-space:pre-wrap"><?= e($billingText) ?></p></section><?php endif; ?>
<section class="wrap page legal"><h1>Tu constancia de conformidad</h1><p>Aceptada el <?= e($agreement['accepted_at']) ?> UTC · Constancia #<?= (int)$agreement['id'] ?></p><p>Esta aceptación acredita la conformidad con las condiciones; no acredita que un pago se haya completado.</p><a class="btn" href="/agreement.php?id=<?= (int)$agreement['id'] ?>&amp;download=1">Descargar copia</a><div style="white-space:pre-wrap;line-height:1.8;margin-top:24px"><?= e($agreement['document_text']) ?></div><p class="fine">Huella del documento: <?= e($agreement['document_hash']) ?></p><a href="/account.php">Volver a Mi cuenta</a></section>
<?php $html=ob_get_clean();$pageTitle='Constancia de conformidad';require ROOT.'/src/views/layout_top.php';echo $html;require ROOT.'/src/views/layout_bottom.php';
