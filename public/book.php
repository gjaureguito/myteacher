<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require_once ROOT . '/src/study_book.php';
$u = require_login();
header('Cache-Control: private, no-store');
$code = !isset($_GET['code']) ? 'a1' : (is_string($_GET['code']) ? strtolower($_GET['code']) : '');
$book = study_level($code);
$level = $book ? q_one('SELECT * FROM levels WHERE code = ?', [$code]) : null;
if (!$level) { http_response_code(404); render('notfound', [], 'No encontrado'); exit; }
$units = q_all('SELECT * FROM units WHERE level_code = ? AND is_published = 1 ORDER BY position, id', [$code]);
$slug = !isset($_GET['chapter']) ? '' : (is_string($_GET['chapter']) ? $_GET['chapter'] : '__invalid__');
$chapterUnit = null;
foreach ($units as $candidate) {
    if ($candidate['slug'] === $slug) { $chapterUnit = $candidate; break; }
}
if ($slug !== '' && !$chapterUnit) { http_response_code(404); render('notfound', [], 'No encontrado'); exit; }
if ($chapterUnit && !can_access_unit($u, $chapterUnit)) {
    flash('info', 'Este capítulo forma parte de la suscripción. Podés leer el primer capítulo de cada nivel con tu cuenta demo.');
    redirect('/account.php');
}
$chapter = $chapterUnit ? study_chapter($chapterUnit['slug']) : null;
$sectionKeys = ['theory', 'foundation', 'example', 'practice', 'writing', 'check'];
$sectionDone = [];
$practiceDone = false;
$guidedSolved = 0;
$guidedTotal = 0;
if ($chapterUnit) {
    if (is_post()) {
        csrf_check();
        $key = is_string($_POST['section'] ?? null) ? $_POST['section'] : '';
        if (!in_array($key, $sectionKeys, true) || (!$chapter && $key !== 'theory')) {
            http_response_code(400); exit('Sección inválida');
        }
        $params = [(int) $u['id'], (int) $chapterUnit['id'], $key];
        q('DELETE FROM book_section_status WHERE user_id = ? AND unit_id = ? AND section_key = ?', $params);
        if (($_POST['done'] ?? '') === '1') q('INSERT INTO book_section_status (user_id, unit_id, section_key) VALUES (?, ?, ?)', $params);
        redirect('/book.php?code=' . rawurlencode($code) . '&chapter=' . rawurlencode($slug) . '#chapter-contents');
    }
    $sectionDone = array_fill_keys(array_column(q_all('SELECT section_key FROM book_section_status WHERE user_id = ? AND unit_id = ?', [(int) $u['id'], (int) $chapterUnit['id']]), 'section_key'), true);
    if ($chapter) {
        $guidedHashes = array_fill_keys(array_column(q_all('SELECT question_hash FROM book_quiz_status WHERE user_id = ? AND unit_id = ?', [(int) $u['id'], (int) $chapterUnit['id']]), 'question_hash'), true);
        foreach ($chapter['practice'] as [$prompt, $answer]) {
            $guidedTotal++;
            if (isset($guidedHashes[hash('sha256', $prompt . "\n" . $answer)])) $guidedSolved++;
        }
    }
    $progress = unit_progress((int) $u['id'], $code)[(int) $chapterUnit['id']] ?? ['total' => 0, 'solved' => 0];
    $practiceDone = $progress['total'] > 0 && $progress['solved'] >= $progress['total'];
}
$previous = $next = null;
if ($chapterUnit) {
    $index = array_search($chapterUnit['id'], array_column($units, 'id'));
    $previous = $units[$index - 1] ?? null;
    $next = $units[$index + 1] ?? null;
}
render('study_book', compact('u', 'book', 'level', 'units', 'code', 'chapterUnit', 'chapter', 'previous', 'next', 'sectionDone', 'practiceDone', 'guidedSolved', 'guidedTotal'), 'Libro de estudio ' . strtoupper($code));

