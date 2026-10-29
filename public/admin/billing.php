<?php
require dirname(__DIR__,2).'/src/bootstrap.php';
require_admin();
require ROOT.'/src/billing.php';
header('Cache-Control: private, no-store');
$search=trim((string)($_GET['s']??''));
$rows=q_all('SELECT a.id,a.accepted_at,u.email,b.billing_data FROM purchase_billing b JOIN purchase_agreements a ON a.nonce=b.nonce AND a.user_id=b.user_id JOIN users u ON u.id=b.user_id WHERE u.email LIKE ? ORDER BY a.id DESC LIMIT 100',['%'.$search.'%']);
ob_start(); ?>
<section class="wrap page"><h1>Datos para facturación</h1><p><a href="/admin/">Volver al panel</a></p><p>Datos declarados al contratar. Estas constancias no acreditan el cobro ni son facturas emitidas. Verificá cada pago y la condición fiscal antes de facturar.</p>
<form method="get" class="form"><label>Buscar por correo<input type="search" name="s" value="<?= e($search) ?>"></label><button class="btn">Buscar</button></form>
<?php foreach($rows as $row): ?><article class="panel"><h2><?= e($row['email']) ?></h2><p><?= e($row['accepted_at']) ?> UTC</p><p style="white-space:pre-wrap"><?= e(billing_summary(json_decode($row['billing_data'],true,512,JSON_THROW_ON_ERROR))) ?></p><a href="/agreement.php?id=<?= (int)$row['id'] ?>">Ver constancia</a></article><?php endforeach; ?>
<?php if(!$rows): ?><p>No hay datos de facturación para esta búsqueda.</p><?php endif; ?>
<p class="fine">Se muestran hasta 100 contrataciones recientes.</p></section>
<?php $html=ob_get_clean();$pageTitle='Datos para facturación';require ROOT.'/src/views/layout_top.php';echo $html;require ROOT.'/src/views/layout_bottom.php';
