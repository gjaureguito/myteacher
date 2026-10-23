<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();
$questions = require ROOT . '/content/_placement.php';
$result = null;

if (is_post()) {
    csrf_check();
    $byLevel = [];
    $score = 0;
    foreach ($questions as $i => $qq) {
        $lv = $qq[0];
        $byLevel[$lv] ??= ['ok' => 0, 'n' => 0];
        $byLevel[$lv]['n']++;
        $given = $_POST['q' . $i] ?? null;
        if ($given !== null && (int) $given === $qq[3]) {
            $byLevel[$lv]['ok']++;
            $score++;
        }
    }
    // Nivel recomendado: el primero donde acierta menos del 75 %
    $rec = 'c2';
    foreach (['a1', 'a2', 'b1', 'b2', 'c1', 'c2'] as $lv) {
        $b = $byLevel[$lv] ?? ['ok' => 0, 'n' => 1];
        if ($b['ok'] / $b['n'] < 0.75) { $rec = $lv; break; }
    }
    q('INSERT INTO placement_results (user_id, score, total, level_code, created_at) VALUES (?,?,?,?,?)',
        [$u['id'], $score, count($questions), $rec, now_utc()]);
    q('UPDATE users SET current_level = ? WHERE id = ?', [$rec, $u['id']]);
    $result = ['score' => $score, 'total' => count($questions), 'level' => $rec, 'by' => $byLevel,
        'name' => q_val('SELECT name FROM levels WHERE code = ?', [$rec])];
}

render('placement', ['questions' => $questions, 'result' => $result, 'welcome' => isset($_GET['welcome'])], 'Test de nivel');
