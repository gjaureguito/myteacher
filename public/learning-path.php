<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$u = require_login();
header('Cache-Control: private, no-store');
$pathLevels = learning_path((int)$u['id']);
award_completed_levels($u, $pathLevels);
$certificates = array_column(q_all('SELECT * FROM certificates WHERE user_id=?', [$u['id']]), null, 'level_code');
render('learning_path', compact('u', 'pathLevels', 'certificates'), 'Mi ruta y certificados');
