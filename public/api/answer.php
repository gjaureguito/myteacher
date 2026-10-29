<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';

if (!is_post()) json_out(['error' => 'method'], 405);
$u = current_user();
if (!$u) json_out(['error' => 'Tu sesión expiró. Volvé a ingresar.'], 401);
csrf_check();

$in = json_decode((string) file_get_contents('php://input'), true) ?: [];
$id = (int) ($in['id'] ?? 0);
$given = $in['answer'] ?? null;
$wantsSolution = !empty($in['reveal']);
$ms = isset($in['ms']) ? max(0, min(600000, (int) $in['ms'])) : null;

$ex = q_one('SELECT e.*, u.position AS unit_position, u.level_code FROM exercises e JOIN units u ON u.id = e.unit_id WHERE e.id = ?', [$id]);
if (!$ex) json_out(['error' => 'Ejercicio inexistente'], 404);
if (!can_access_unit($u, ['position' => $ex['unit_position']])) json_out(['error' => 'Necesitás la suscripción para esta unidad.'], 403);

// Límite simple: máx. 120 respuestas por minuto por usuario
$recent = (int) q_val('SELECT COUNT(*) FROM attempts WHERE user_id = ? AND created_at > ?', [$u['id'], gmdate('Y-m-d H:i:s', time() - 60)]);
if ($recent > 120) json_out(['error' => 'Vas muy rápido. Esperá un momento.'], 429);

$data = json_decode($ex['data_json'], true) ?: [];
$answer = json_decode($ex['answer_json'], true) ?: [];
$st = q_one('SELECT solved, tries FROM exercise_status WHERE user_id = ? AND exercise_id = ?', [$u['id'], $id]);

if ($wantsSolution) {
    // Ver la solución cuenta como intento no logrado (para las estadísticas), pero no bloquea volver a intentarlo
    q('INSERT INTO attempts (user_id, exercise_id, is_correct, revealed, ms, created_at) VALUES (?,?,?,?,?,?)', [$u['id'], $id, 0, 1, $ms, now_utc()]);
    if (!$st) q('INSERT INTO exercise_status (user_id, exercise_id, solved, tries, updated_at) VALUES (?,?,?,?,?)', [$u['id'], $id, 0, 1, now_utc()]);
    json_out([
        'correct' => false,
        'solution' => true,
        'reveal' => true,
        'answer' => correct_answer_text($ex),
        'index' => isset($answer['index']) ? (int) $answer['index'] : null,
        'pairs' => $ex['type'] === 'match' ? ($answer['pairs'] ?? []) : null,
        'explanation' => $ex['explanation'],
        'say' => $data['say'] ?? null,
        'skill' => $ex['skill'],
    ]);
}

$g = grade_detail($ex, $given);
$ok = $g['ok'];

q('INSERT INTO attempts (user_id, exercise_id, is_correct, revealed, ms, created_at) VALUES (?,?,?,?,?,?)', [$u['id'], $id, $ok ? 1 : 0, 0, $ms, now_utc()]);
$lastAnswer = $ok ? mb_substr(json_encode($given, JSON_UNESCAPED_UNICODE), 0, 5000) : null;
if ($st) {
    q('UPDATE exercise_status SET solved = ?, tries = ?, updated_at = ?, last_answer = COALESCE(?, last_answer) WHERE user_id = ? AND exercise_id = ?',
        [($st['solved'] || $ok) ? 1 : 0, $st['tries'] + 1, now_utc(), $lastAnswer, $u['id'], $id]);
} else {
    q('INSERT INTO exercise_status (user_id, exercise_id, solved, tries, updated_at, last_answer) VALUES (?,?,?,?,?,?)',
        [$u['id'], $id, $ok ? 1 : 0, 1, now_utc(), $lastAnswer]);
}

if ($ok) award_completed_levels($u);

$tries = ($st['tries'] ?? 0) + 1;
// La respuesta correcta se revela si acertó o después de 2 intentos fallidos. En escritura el modelo se muestra siempre.
$reveal = $ok || $tries >= 2 || $ex['type'] === 'write';

json_out([
    'correct' => $ok,
    'reveal' => $reveal,
    'first_try' => $ok && $tries === 1,
    'answer' => $reveal ? correct_answer_text($ex) : null,
    'index' => ($reveal && isset($answer['index'])) ? (int) $answer['index'] : null,
    'pairs' => ($reveal && $ex['type'] === 'match') ? ($answer['pairs'] ?? []) : null,
    'explanation' => $reveal ? $ex['explanation'] : '',
    'hint' => (!$ok && !$reveal) ? ($ex['hint'] !== '' ? $ex['hint'] : generic_hint($ex['type'])) : '',
    'detail' => $g['detail'],
    'say' => $data['say'] ?? null,
    'skill' => $ex['skill'],
]);
