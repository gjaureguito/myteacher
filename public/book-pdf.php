<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require_once ROOT . '/src/study_book.php';
require_once ROOT . '/src/book_pdf.php';

$user = require_login();
$code = is_string($_GET['code'] ?? null) ? strtolower($_GET['code']) : '';
$slug = is_string($_GET['chapter'] ?? null) ? $_GET['chapter'] : '';
if (!preg_match('/^[a-c][1-2]$/D', $code) || !preg_match('/^[a-z0-9-]+$/D', $slug)) {
    http_response_code(404);
    exit('No encontramos esa unidad.');
}
$unit = q_one('SELECT * FROM units WHERE level_code = ? AND slug = ? AND is_published = 1', [$code, $slug]);
if (!$unit) {
    http_response_code(404);
    exit('No encontramos esa unidad.');
}
if (!can_access_unit($user, $unit)) {
    flash('info', 'Esta unidad requiere acceso activo. La demo incluye la primera unidad de cada nivel.');
    redirect('/account.php');
}
$chapter = study_chapter_english($slug) ?? study_english_unit_fallback($unit);
$unit['title'] = (string) ($chapter['title'] ?? 'English study guide');
$unit['summary'] = (string) ($chapter['summary'] ?? '');
$visual = null;
$pdf = study_pdf_generate($unit, $code, $chapter, $visual);
$filename = 'MyTeacher-' . strtoupper($code) . '-unidad-' . (int) $unit['position'] . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($pdf));
header('Cache-Control: private, no-store');
echo $pdf;
