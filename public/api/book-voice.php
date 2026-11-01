<?php
declare(strict_types=1);
require dirname(__DIR__, 2) . '/src/bootstrap.php';
if (!is_post()) json_out(['error' => 'Método no permitido'], 405);
$u = current_user();
if (!$u) json_out(['error' => 'Tu sesión expiró.'], 401);
csrf_check();
$in = json_decode((string)file_get_contents('php://input'), true) ?: [];
$unit = q_one('SELECT id, position, theory_html FROM units WHERE id = ? AND is_published = 1', [(int)($in['unit_id'] ?? 0)]);
if (!$unit || !can_access_unit($u, $unit)) json_out(['error' => 'Capítulo no disponible'], 403);
$index = filter_var($in['index'] ?? null, FILTER_VALIDATE_INT);
$total = filter_var($in['total'] ?? null, FILTER_VALIDATE_INT);
if ($index === false || $total === false || $total < 1 || $total > 1000 || $index < 0 || $index >= $total) json_out(['error' => 'Ejercicio inválido'], 400);
$hash = hash('sha256', $unit['theory_html'] . "\n" . $index);
$sql = db_is_sqlite() ? 'INSERT OR IGNORE INTO book_voice_status (user_id, unit_id, phrase_hash) VALUES (?, ?, ?)' : 'INSERT IGNORE INTO book_voice_status (user_id, unit_id, phrase_hash) VALUES (?, ?, ?)';
q($sql, [(int)$u['id'], (int)$unit['id'], $hash]);
$saved = array_fill_keys(array_column(q_all('SELECT phrase_hash FROM book_voice_status WHERE user_id = ? AND unit_id = ?', [(int)$u['id'], (int)$unit['id']]), 'phrase_hash'), true);
$count = 0;
for ($i=0; $i<$total; $i++) if (isset($saved[hash('sha256', $unit['theory_html'] . "\n" . $i)])) $count++;
if ($count === $total) {
    $sql = db_is_sqlite() ? 'INSERT OR IGNORE INTO book_section_status (user_id, unit_id, section_key) VALUES (?, ?, ?)' : 'INSERT IGNORE INTO book_section_status (user_id, unit_id, section_key) VALUES (?, ?, ?)';
    q($sql, [(int)$u['id'], (int)$unit['id'], 'theory']);
}
json_out(['ok' => true, 'done' => $count, 'total' => $total, 'complete' => $count === $total]);

