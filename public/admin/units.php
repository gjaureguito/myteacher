<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require_admin();

if (is_post()) {
    csrf_check();
    require_once ROOT . '/src/seed.php';
    $force = post_str('mode', 10) === 'force';
    $st = seed_content($force);
    flash('ok', "Contenido sincronizado: {$st['units_new']} unidades nuevas, {$st['units_updated']} actualizadas.");
    redirect('/admin/units.php');
}

$units = q_all('SELECT u.*, (SELECT COUNT(*) FROM exercises e WHERE e.unit_id = u.id) AS n_ex FROM units u ORDER BY u.level_code, u.position');
render('admin_units', compact('units'), 'Contenido');
