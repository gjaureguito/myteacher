<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();
$code = strtolower((string) ($_GET['code'] ?? ''));
$level = q_one('SELECT * FROM levels WHERE code = ?', [$code]);
if (!$level) { http_response_code(404); render('notfound', [], 'No encontrado'); exit; }

$units = q_all('SELECT id, slug, position, title, summary, icon FROM units WHERE level_code = ? AND is_published = 1 ORDER BY position', [$code]);
$skillCounts = [];
foreach (q_all('SELECT e.unit_id, e.skill, COUNT(*) n FROM exercises e JOIN units un ON un.id = e.unit_id WHERE un.level_code = ? GROUP BY e.unit_id, e.skill', [$code]) as $r) {
    $skillCounts[(int) $r['unit_id']][$r['skill']] = (int) $r['n'];
}
$up = unit_progress((int) $u['id'], $code);
$color = level_colors()[$code] ?? '#2747D9';

render('level', compact('u', 'level', 'units', 'up', 'color', 'skillCounts'), strtoupper($code) . ' ' . $level['name']);
