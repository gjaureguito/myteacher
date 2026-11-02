<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require_admin();
header('Cache-Control: private, no-store');
$rows = q_all("SELECT variant, COUNT(*) AS visits, SUM(CASE WHEN registered_at IS NOT NULL THEN 1 ELSE 0 END) AS registrations FROM marketing_visits WHERE NOT (source = 'prueba_tecnica' AND campaign = 'verificacion') GROUP BY variant ORDER BY variant");
$sources = q_all("SELECT source, campaign, COUNT(*) AS visits, SUM(CASE WHEN registered_at IS NOT NULL THEN 1 ELSE 0 END) AS registrations FROM marketing_visits WHERE NOT (source = 'prueba_tecnica' AND campaign = 'verificacion') GROUP BY source, campaign ORDER BY visits DESC LIMIT 50");
ob_start(); ?>
<section class="wrap page"><p class="eyebrow">GESTIÓN DE LA ACADEMIA</p><h1>Portadas y campañas</h1>
<?php require ROOT . '/src/views/admin_nav.php'; ?>
<p>Las portadas C, D, E y F están activas; A y B corresponden a la prueba anterior. Comparación de visitantes que llegaron a la portada y completaron un registro en la misma sesión. Los datos comienzan cuando se activa esta medición.</p>
<h2>Conversión por portada</h2><div class="table-scroll"><table class="tbl"><thead><tr><th>Portada</th><th>Visitas</th><th>Registros</th><th>Conversión</th></tr></thead><tbody>
<?php foreach ($rows as $row): $v=(int)$row['visits']; $r=(int)$row['registrations']; ?><tr><td><?= e($row['variant']) ?> <?= e(match ($row['variant']) { 'A' => '· anterior: bienvenida', 'B' => '· anterior: espacio de estudio', 'C' => '· nueva: estudio en casa', 'D' => '· nueva: espacio compartido', 'E' => '· estudiante en casa', 'F' => '· estudiante adulta', default => '' }) ?></td><td><?= $v ?></td><td><?= $r ?></td><td><?= $v ? number_format(100*$r/$v, 1, ',', '.') : '0' ?>%</td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="4">Todavía no hay visitas registradas.</td></tr><?php endif; ?></tbody></table></div>
<h2>Origen de las visitas</h2><p>Agregá <code>utm_source</code> y <code>utm_campaign</code> al enlace de cada anuncio para identificarlo. Sin esos parámetros se usa el sitio de procedencia cuando está disponible.</p>
<div class="table-scroll"><table class="tbl"><thead><tr><th>Origen</th><th>Campaña</th><th>Visitas</th><th>Registros</th></tr></thead><tbody><?php foreach ($sources as $row): ?><tr><td><?= e($row['source']) ?></td><td><?= e($row['campaign'] ?: 'Sin identificar') ?></td><td><?= (int)$row['visits'] ?></td><td><?= (int)$row['registrations'] ?></td></tr><?php endforeach; ?></tbody></table></div>
<p class="fine">Una sesión no equivale necesariamente a una persona. No se atribuyen registros desde otro dispositivo o después de vencer la sesión; los bloqueadores y la navegación privada pueden afectar la medición.</p></section>
<?php $html=ob_get_clean(); $pageTitle='Portadas y campañas'; require ROOT.'/src/views/layout_top.php'; echo $html; require ROOT.'/src/views/layout_bottom.php';
