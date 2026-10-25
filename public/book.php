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
$previous = $next = null;
if ($chapterUnit) {
    $index = array_search($chapterUnit['id'], array_column($units, 'id'));
    $previous = $units[$index - 1] ?? null;
    $next = $units[$index + 1] ?? null;
}
render('study_book', compact('u', 'book', 'level', 'units', 'code', 'chapterUnit', 'chapter', 'previous', 'next'), 'Libro de estudio ' . strtoupper($code));
