<?php
require dirname(__DIR__) . '/src/bootstrap.php';
$samples = q_all('SELECT u.slug, u.title, u.level_code FROM units u JOIN levels l ON l.code=u.level_code WHERE u.position=1 AND u.is_published=1 ORDER BY l.position');
$tracks = learning_tracks();
render('library', compact('samples', 'tracks'), 'Biblioteca de aprendizaje');
