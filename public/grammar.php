<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require_once ROOT . '/src/study_book.php';
$u = require_login();
header('Cache-Control: private, no-store');
if (!isset($_GET['id'])) {
    $unit = q_one('SELECT * FROM units WHERE is_published = 1 ORDER BY level_code, position, id LIMIT 1');
} else {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $unit = $id ? q_one('SELECT * FROM units WHERE id = ? AND is_published = 1', [$id]) : null;
}
if (!$unit) { http_response_code(404); render('notfound', [], 'No encontrado'); exit; }
if (!can_access_unit($u, $unit)) {
    flash('info', 'Este cuaderno forma parte de la suscripción. La primera unidad de cada nivel está disponible en la demo.');
    redirect('/account.php');
}
$chapter = study_chapter($unit['slug']);
$visual = study_visual($unit['slug']);
$units = q_all('SELECT id,title,level_code,position FROM units WHERE is_published = 1 ORDER BY level_code,position,id');
$exercises = q_all("SELECT e.*, COALESCE(s.solved,0) AS solved, s.last_answer FROM exercises e LEFT JOIN exercise_status s ON s.exercise_id=e.id AND s.user_id=? WHERE e.unit_id=? AND e.skill='grammar' ORDER BY e.position", [$u['id'],$unit['id']]);
$groups = [];
foreach ($exercises as $exercise) $groups[$exercise['type']][] = $exercise;
$solved = array_sum(array_column($exercises,'solved'));
render('grammar',compact('u','unit','chapter','visual','units','exercises','groups','solved'),'Cuaderno de gramática');
