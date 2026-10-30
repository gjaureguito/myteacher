<link rel="stylesheet" href="/assets/admin-billing.css?v=1">
<section class="wrap page">
<p class="eyebrow">ADMINISTRACIÓN / FACTURACIÓN</p><h1>Datos para facturación</h1>
<?php require __DIR__.'/admin_nav.php'; ?>
<p>La última información fiscal declarada por cada alumno, junto a su correo y estado de suscripción.</p>
<p class="fine">Esta vista no acredita cobros ni emite facturas. El importe corresponde a la última contratación aceptada; verificá el pago antes de facturar. Los datos no informados pueden ser opcionales según el caso.</p>
<form method="get" class="admin-billing-filter">
<label>Nombre, razón social, correo o CUIT/CUIL<input type="search" name="s" maxlength="190" value="<?= e($search) ?>" placeholder="Buscar un alumno o documento"></label>
<label>Mostrar<select name="status"><?php foreach(['all'=>'Todos los registros','active'=>'Suscripciones activas con acceso vigente','pending'=>'Suscripciones pendientes','missing'=>'Sin declaración fiscal'] as $value=>$label): ?><option value="<?= e($value) ?>" <?= $filter===$value?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
<button class="btn">Buscar</button><a href="/admin/billing.php">Limpiar</a>
</form>
<p><strong><?= $total ?></strong> alumnos encontrados · Página <?= $page ?> de <?= $pages ?></p>
<?php if (!$rows): ?><div class="panel"><h2>No encontramos registros</h2><p>Probá otra búsqueda o cambiá el filtro. Los usuarios anteriores a la solicitud de datos fiscales pueden no tener una declaración guardada.</p></div><?php endif; ?>
<?php $billingExpanded=true; foreach($rows as $x): ?>
<article class="admin-billing-card">
<header class="admin-billing-heading"><div><h2><?= e($x['name']) ?></h2><p><a href="mailto:<?= e($x['email']) ?>"><?= e($x['email']) ?></a></p></div><span class="pill"><?= e(admin_subscription_label($x['sub_status'])) ?></span></header>
<p>Acceso hasta: <?= fmt_date($x['access_until']) ?> · <a href="/admin/?s=<?= rawurlencode($x['email']) ?>">Administrar usuario</a></p>
<?php require __DIR__.'/_admin_billing_details.php'; ?>
<?php if($x['billing_agreement_id']): ?><p class="fine">Importe de la última contratación: <?= e($x['billing_currency']) ?> <?= e(number_format((float)$x['billing_amount'],2,',','.')) ?> · No implica pago confirmado.</p><?php endif; ?>
</article>
<?php endforeach; ?>
<nav class="admin-billing-pagination" aria-label="Páginas de facturación">
<?php if($page>1): ?><a href="?<?= e(http_build_query(['s'=>$search,'status'=>$filter,'page'=>$page-1])) ?>">← Anterior</a><?php endif; ?>
<?php if($page<$pages): ?><a href="?<?= e(http_build_query(['s'=>$search,'status'=>$filter,'page'=>$page+1])) ?>">Siguiente →</a><?php endif; ?>
</nav>
</section>
