<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();
$id = (int) ($_GET['id'] ?? 0);
$unit = q_one('SELECT * FROM units WHERE id = ? AND is_published = 1', [$id]);
if (!$unit) { http_response_code(404); render('notfound', [], 'No encontrado'); exit; }

if (!can_access_unit($u, $unit)) {
    flash('info', 'Esta unidad es parte de la suscripción. La demo incluye la primera unidad de cada nivel.');
    redirect('/account.php');
}

$level = q_one('SELECT * FROM levels WHERE code = ?', [$unit['level_code']]);
$exercises = q_all('SELECT e.id, e.type, e.prompt, e.data_json, COALESCE(s.solved, 0) AS solved
                    FROM exercises e
                    LEFT JOIN exercise_status s ON s.exercise_id = e.id AND s.user_id = ?
                    WHERE e.unit_id = ? ORDER BY e.position', [$u['id'], $id]);
$vocab = json_decode($unit['vocab_json'], true) ?: [];
$siblings = q_all('SELECT id, position, title FROM units WHERE level_code = ? AND is_published = 1 ORDER BY position', [$unit['level_code']]);
$next = null;
foreach ($siblings as $s) {
    if ($s['position'] > $unit['position']) { $next = $s; break; }
}
$color = level_colors()[$unit['level_code']] ?? '#2747D9';
$lab = learning_lab($unit['slug']);

render('unit', compact('u', 'unit', 'level', 'exercises', 'vocab', 'next', 'color', 'lab'), $unit['title']);
