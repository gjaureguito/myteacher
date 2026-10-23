<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$slug = (string) ($_GET['slug'] ?? '');
$sample = q_one('SELECT slug, title, level_code FROM units WHERE slug=? AND position=1 AND is_published=1', [$slug]);
$lab = $sample ? learning_lab($sample['slug']) : null;
if (!$lab) { http_response_code(404); render('notfound', [], 'Lectura no encontrada'); exit; }
render('reading', compact('sample', 'lab'), $lab['title'] . ' · Lectura ' . strtoupper($sample['level_code']));
