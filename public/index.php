<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$levels = q_all('SELECT l.*, (SELECT COUNT(*) FROM units u WHERE u.level_code = l.code AND u.is_published = 1) AS n_units FROM levels l ORDER BY position');
$counts = q_one('SELECT (SELECT COUNT(*) FROM units WHERE is_published = 1) AS units, (SELECT COUNT(*) FROM exercises e JOIN units u ON u.id = e.unit_id WHERE u.is_published = 1) AS exercises');
$syllabus = q_all('SELECT level_code, position, title, summary FROM units WHERE is_published = 1 ORDER BY level_code, position');
$workshops = q_all('SELECT slug, position FROM units WHERE is_published=1 ORDER BY level_code, position');
$workshopCount = 0;
$sampleCount = 0;
$featured = null;
foreach ($workshops as $workshop) {
    $lab = learning_lab($workshop['slug']);
    if (!$lab) continue;
    $workshopCount++;
    if ((int)$workshop['position'] === 1) {
        $sampleCount++;
        $featured ??= ['slug' => $workshop['slug'], 'lab' => $lab];
    }
}

render('home', compact('levels', 'counts', 'syllabus', 'workshopCount', 'sampleCount', 'featured'), 'Inglés para pensar, conectar y crecer');
