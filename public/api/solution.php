<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';

if (!is_post()) json_out(['error' => 'Método no permitido.'], 405);
$u = current_user();
if (!$u) json_out(['error' => 'Ingresá a tu cuenta para consultar las soluciones.'], 401);
csrf_check();
header('Cache-Control: no-store');
$in = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($in) || !isset($in['id']) || !is_scalar($in['id'])) json_out(['error' => 'Ejercicio inválido.'], 400);
$ex = q_one('SELECT e.*, u.position AS unit_position, u.title AS unit_title, u.summary AS unit_summary
    FROM exercises e JOIN units u ON u.id=e.unit_id WHERE e.id=? AND u.is_published=1', [(int)$in['id']]);
if (!$ex) json_out(['error' => 'Ejercicio no disponible.'], 404);
if (!can_access_unit($u, ['position' => $ex['unit_position']])) json_out(['error' => 'Esta solución pertenece a una unidad con suscripción.'], 403);

$answer = json_decode($ex['answer_json'], true) ?: [];
$data = json_decode($ex['data_json'], true) ?: [];
// Consultation is read-only: no attempts or solved status are written.
json_out([
    'answer' => correct_answer_text($ex),
    'alternatives' => array_values(array_slice($answer['accept'] ?? [], 1)),
    'explanation' => $ex['explanation'],
    'theory_title' => $ex['unit_title'],
    'theory_summary' => $ex['unit_summary'],
    'transcript' => in_array($ex['type'], ['listen', 'dictation'], true) ? ($data['say'] ?? '') : '',
]);
