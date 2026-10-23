<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$levels = q_all('SELECT * FROM levels ORDER BY position');
$units = q_all('SELECT slug, title, summary, level_code, position FROM units WHERE is_published=1 ORDER BY level_code, position');
$tracks = learning_tracks();
render('program', compact('levels', 'units', 'tracks'), 'Programa de inglés A1–C2');
