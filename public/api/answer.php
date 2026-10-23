<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';

if (!is_post()) json_out(['error' => 'method'], 405);
$u = current_user();
if (!$u) json_out(['error' => 'Tu sesión expiró. Volvé a ingresar.'], 401);
csrf_check();

$in = json_decode((string) file_get_contents('php://input'), true) ?: [];
$id = (int) ($in['id'] ?? 0);
$given = $in['answer'] ?? null;

$ex = q_one('SELECT e.*, u.position AS unit_position, u.level_code FROM exercises e JOIN units u ON u.id = e.unit_id WHERE e.id = ?', [$id]);
if (!$ex) json_out(['error' => 'Ejercicio inexistente'], 404);
if (!can_access_unit($u, ['position' => $ex['unit_position']])) json_out(['error' => 'Necesitás la suscripción para esta unidad.'], 403);

// Límite simple: máx. 120 respuestas por minuto por usuario
$recent = (int) q_val('SELECT COUNT(*) FROM attempts WHERE user_id = ? AND created_at > ?', [$u['id'], gmdate('Y-m-d H:i:s', time() - 60)]);
if ($recent > 120) json_out(['error' => 'Vas muy rápido. Esperá un momento.'], 429);

$ok = grade_exercise($ex, $given);

q('INSERT INTO attempts (user_id, exercise_id, is_correct, created_at) VALUES (?,?,?,?)', [$u['id'], $id, $ok ? 1 : 0, now_utc()]);
$st = q_one('SELECT solved, tries FROM exercise_status WHERE user_id = ? AND exercise_id = ?', [$u['id'], $id]);
if ($st) {
    q('UPDATE exercise_status SET solved = ?, tries = ?, updated_at = ? WHERE user_id = ? AND exercise_id = ?',
        [($st['solved'] || $ok) ? 1 : 0, $st['tries'] + 1, now_utc(), $u['id'], $id]);
} else {
    q('INSERT INTO exercise_status (user_id, exercise_id, solved, tries, updated_at) VALUES (?,?,?,?,?)',
        [$u['id'], $id, $ok ? 1 : 0, 1, now_utc()]);
}

$tries = ($st['tries'] ?? 0) + 1;
// La respuesta correcta se revela si acertó o después de 2 intentos fallidos
$reveal = $ok || $tries >= 2;
$data = json_decode($ex['data_json'], true) ?: [];
$answer = json_decode($ex['answer_json'], true) ?: [];

json_out([
    'correct' => $ok,
    'reveal' => $reveal,
    'answer' => $reveal ? correct_answer_text($ex) : null,
    'index' => ($reveal && isset($answer['index'])) ? (int) $answer['index'] : null,
    'explanation' => $reveal ? $ex['explanation'] : '',
    'say' => $data['say'] ?? null,
]);
