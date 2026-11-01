<?php
declare(strict_types=1);
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require_once ROOT . '/src/study_book.php';

if (!is_post()) json_out(['error' => 'Método no permitido'], 405);
$u = current_user();
if (!$u) json_out(['error' => 'Tu sesión expiró. Volvé a ingresar.'], 401);
csrf_check();
$input = json_decode((string) file_get_contents('php://input'), true) ?: [];
$unit = q_one('SELECT id, slug, position FROM units WHERE id = ? AND is_published = 1', [(int)($input['unit_id'] ?? 0)]);
if (!$unit || !can_access_unit($u, $unit)) json_out(['error' => 'Capítulo no disponible'], 403);
$chapter = study_chapter($unit['slug']);
$index = filter_var($input['index'] ?? null, FILTER_VALIDATE_INT);
$item = $index !== false && $index >= 0 ? ($chapter['practice'][$index] ?? null) : null;
if (!$item || !is_string($input['answer'] ?? null)) json_out(['error' => 'Ejercicio inválido'], 400);
[$prompt, $answer] = $item;
$correct = trim(preg_split('/\s+\/\s+/', $answer)[0]);
if ($input['answer'] !== $correct) json_out(['error' => 'Respuesta incorrecta'], 400);
$hash = hash('sha256', $prompt . "\n" . $answer);
$sql = db_is_sqlite() ? 'INSERT OR IGNORE INTO book_quiz_status (user_id, unit_id, question_hash) VALUES (?, ?, ?)' : 'INSERT IGNORE INTO book_quiz_status (user_id, unit_id, question_hash) VALUES (?, ?, ?)';
q($sql, [(int)$u['id'], (int)$unit['id'], $hash]);
json_out(['ok' => true]);

