<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();
$code = strtolower((string) ($_GET['code'] ?? ''));
$level = q_one('SELECT * FROM levels WHERE code = ?', [$code]);
if (!$level) { http_response_code(404); render('notfound', [], 'No encontrado'); exit; }

$units = q_all('SELECT id, slug, position, title, summary FROM units WHERE level_code = ? AND is_published = 1 ORDER BY position', [$code]);
$up = unit_progress((int) $u['id'], $code);
$color = level_colors()[$code] ?? '#2747D9';

render('level', compact('u', 'level', 'units', 'up', 'color'), strtoupper($code) . ' ' . $level['name']);
