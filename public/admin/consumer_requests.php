<?php
require dirname(__DIR__,2).'/src/bootstrap.php';
$u=require_admin();header('Cache-Control: private, no-store');
if(is_post()){
 csrf_check();$id=(int)($_POST['id']??0);$status=post_str('status',20);
 if(in_array($status,['received','processing','resolved'],true))q('UPDATE consumer_requests SET status=? WHERE id=?',[$status,$id]);
 flash('info','Estado administrativo actualizado. Esto no realiza bajas ni reintegros en Mercado Pago.');redirect('/admin/consumer_requests.php');
}
$requests=q_all('SELECT * FROM consumer_requests ORDER BY id DESC LIMIT 100');
ob_start(); ?>
<section class="wrap page"><h1>Arrepentimientos y bajas</h1><?php require ROOT.'/src/views/admin_nav.php'; ?>
<p>Atendé las solicitudes y enviá las comunicaciones y constancias que exija la normativa, dentro de los plazos aplicables. Verificá la contratación antes de gestionar bajas y reintegros en Mercado Pago. Cambiar este estado no ejecuta operaciones financieras. No se envían correos automáticamente.</p>
<?php foreach($requests as $request): ?><article class="panel"><h2><?= e($request['receipt']) ?> · <?= $request['kind']==='withdrawal'?'Arrepentimiento':'Baja' ?></h2><p><?= e($request['name']) ?> · <?= e($request['email']) ?> · <?= e($request['created_at']) ?> UTC</p><p>Referencia: <?= e($request['reference']) ?></p><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$request['id'] ?>"><label>Estado<select name="status"><?php foreach(['received'=>'Recibida','processing'=>'En gestión','resolved'=>'Resuelta'] as $key=>$label): ?><option value="<?= e($key) ?>" <?= $request['status']===$key?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label><button class="btn btn-sm">Guardar estado</button></form></article><?php endforeach; ?>
<?php if(!$requests): ?><p>No hay solicitudes registradas.</p><?php endif; ?></section>
<?php $html=ob_get_clean();$pageTitle='Solicitudes de consumidores';require ROOT.'/src/views/layout_top.php';echo $html;require ROOT.'/src/views/layout_bottom.php';
