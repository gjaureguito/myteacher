<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require_admin();
$units = q_all('SELECT id, slug, title, level_code, is_published FROM units ORDER BY level_code, position');
render('admin_learning', compact('units'), 'Revisión de talleres');
