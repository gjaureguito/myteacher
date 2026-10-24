<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
if (!is_post()) json_out(['error'=>'Método no permitido'],405);
$user = current_user();
if (!$user) json_out(['error'=>'Ingresá nuevamente.'],401);
csrf_check();
header('Cache-Control: no-store');
$minute = (int) floor(time()/60);
if (($_SESSION['scene_minute'] ?? null) !== $minute) { $_SESSION['scene_minute']=$minute; $_SESSION['scene_requests']=0; }
if (++$_SESSION['scene_requests'] > 120) json_out(['error'=>'Esperá un momento antes de volver a responder.'],429);
$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in)) json_out(['error'=>'Solicitud inválida'],400);
$scene = null;
foreach (scenes() as $s) if ($s['id'] === ($in['scene'] ?? null)) $scene = $s;
if (!$scene) json_out(['error'=>'Lección inexistente'],404);
$unit = q_one('SELECT * FROM units WHERE slug = ? AND is_published = 1',[$scene['unit']]);
if (!$unit) json_out(['error'=>'Lección no publicada'],404);
if (!can_access_unit($user,$unit)) json_out(['error'=>'Esta unidad requiere suscripción.'],403);
$i = $in['question'] ?? null;
if (!is_int($i) || !isset($scene['questions'][$i])) json_out(['error'=>'Ejercicio inexistente'],404);
$question = $scene['questions'][$i];
$given = $in['answer'] ?? null;
$correct = $question['type']==='mcq'
    ? is_int($given) && $given === $question['answer']
    : is_string($given) && norm_answer($given) === norm_answer($question['answer']);
$pdo = db();
$pdo->beginTransaction();
try {
    $insert = db_is_sqlite() ? 'INSERT OR IGNORE' : 'INSERT IGNORE';
    q("$insert INTO scene_status (user_id,scene_id,question_id,solved,tries,updated_at) VALUES (?,?,?,0,0,?)",[$user['id'],$scene['id'],$i,now_utc()]);
    q('UPDATE scene_status SET solved = CASE WHEN ? THEN 1 ELSE solved END, tries = tries + 1, updated_at = ? WHERE user_id = ? AND scene_id = ? AND question_id = ?',[$correct?1:0,now_utc(),$user['id'],$scene['id'],$i]);
    $state = scene_progress((int)$user['id'],$scene['id']);
    $pdo->commit();
} catch (Throwable $e) { $pdo->rollBack(); throw $e; }
$solved = array_sum(array_column($state,'solved'));
json_out(['correct'=>$correct,'explanation'=>$question['explanation'],'answer'=>$correct ? ($question['type']==='mcq' ? $question['options'][$question['answer']] : $question['answer']) : null,'solved'=>$solved,'total'=>count($scene['questions']),'score'=>$solved*20]);
