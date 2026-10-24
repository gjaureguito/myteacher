<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();
$uid = (int) $u['id'];
$mode = ($_GET['m'] ?? '') === 'refuerzo' ? 'refuerzo' : 'errores';

$base = 'SELECT e.id, e.type, e.prompt, e.data_json, e.skill, s.solved, s.tries, un.id AS unit_id, un.title AS unit_title, un.position, un.level_code
         FROM exercise_status s JOIN exercises e ON e.id = s.exercise_id JOIN units un ON un.id = e.unit_id
         WHERE s.user_id = ? AND un.is_published = 1 AND ';
$rows = $mode === 'errores'
    ? q_all($base . 's.solved = 0 AND s.tries > 0 ORDER BY un.level_code, un.position, e.position', [$uid])
    : q_all($base . 's.solved = 1 AND s.tries > 1 ORDER BY s.updated_at DESC', [$uid]);
$rows = array_values(array_filter($rows, fn($r) => can_access_unit($u, $r)));
$countErr = (int) q_val('SELECT COUNT(*) FROM exercise_status WHERE user_id = ? AND solved = 0 AND tries > 0', [$uid]);
$countRef = (int) q_val('SELECT COUNT(*) FROM exercise_status WHERE user_id = ? AND solved = 1 AND tries > 1', [$uid]);
$rows = array_slice($rows, 0, 40);

// El cuaderno muestra cada ejercicio como nuevo: el estado "resuelto" no se pinta en refuerzo
if ($mode === 'refuerzo') foreach ($rows as &$r) $r['solved'] = 0;
unset($r);

render('review', compact('u', 'rows', 'mode', 'countErr', 'countRef'), 'Repaso');
