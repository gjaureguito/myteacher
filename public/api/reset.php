<?php
/** Reinicia la práctica de una unidad para el usuario: los ejercicios vuelven a quedar sin resolver. El historial de intentos se conserva para las estadísticas. */
require dirname(__DIR__, 2) . '/src/bootstrap.php';

if (!is_post()) json_out(['error' => 'method'], 405);
$u = current_user();
if (!$u) json_out(['error' => 'Tu sesión expiró. Volvé a ingresar.'], 401);
csrf_check();

$in = json_decode((string) file_get_contents('php://input'), true) ?: [];
$unitId = (int) ($in['unit_id'] ?? 0);
$exId = (int) ($in['exercise_id'] ?? 0);
if ($exId) {
    q('DELETE FROM exercise_status WHERE user_id = ? AND exercise_id = ?', [$u['id'], $exId]);
    json_out(['ok' => true]);
}
if (!q_val('SELECT id FROM units WHERE id = ?', [$unitId])) json_out(['error' => 'Unidad inexistente'], 404);
q('DELETE FROM exercise_status WHERE user_id = ? AND exercise_id IN (SELECT id FROM exercises WHERE unit_id = ?)', [$u['id'], $unitId]);
json_out(['ok' => true]);
