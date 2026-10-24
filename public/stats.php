<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();
$uid = (int) $u['id'];
$labels = skill_labels();

// Habilidades: ejercicios publicados, resueltos, intentos y aciertos
$avail = [];
foreach (q_all('SELECT e.skill, COUNT(*) n FROM exercises e JOIN units un ON un.id = e.unit_id WHERE un.is_published = 1 GROUP BY e.skill') as $r) $avail[$r['skill']] = (int) $r['n'];
$solved = [];
foreach (q_all('SELECT e.skill, COUNT(*) n FROM exercise_status s JOIN exercises e ON e.id = s.exercise_id WHERE s.user_id = ? AND s.solved = 1 GROUP BY e.skill', [$uid]) as $r) $solved[$r['skill']] = (int) $r['n'];
$att = [];
foreach (q_all('SELECT e.skill, SUM(CASE WHEN a.revealed = 0 THEN 1 ELSE 0 END) tries, SUM(a.is_correct) ok, SUM(a.revealed) rev
                FROM attempts a JOIN exercises e ON e.id = a.exercise_id WHERE a.user_id = ? GROUP BY e.skill', [$uid]) as $r) {
    $att[$r['skill']] = ['tries' => (int) $r['tries'], 'ok' => (int) $r['ok'], 'rev' => (int) $r['rev']];
}
$skills = [];
foreach ($labels as $k => [$en, $es, $ico]) {
    $a = $att[$k] ?? ['tries' => 0, 'ok' => 0, 'rev' => 0];
    $skills[$k] = [
        'en' => $en, 'es' => $es, 'ico' => $ico,
        'avail' => $avail[$k] ?? 0, 'solved' => $solved[$k] ?? 0,
        'progress' => ($avail[$k] ?? 0) ? (int) round(100 * ($solved[$k] ?? 0) / $avail[$k]) : 0,
        'accuracy' => $a['tries'] ? (int) round(100 * $a['ok'] / $a['tries']) : null,
        'tries' => $a['tries'], 'rev' => $a['rev'],
    ];
}

// Totales
$tot = q_one('SELECT COUNT(*) n, COALESCE(SUM(is_correct),0) ok, COALESCE(SUM(revealed),0) rev,
              COALESCE(SUM(CASE WHEN ms IS NULL THEN 30000 WHEN ms > 300000 THEN 300000 ELSE ms END),0) ms
              FROM attempts WHERE user_id = ?', [$uid]);
$triesNoRev = (int) $tot['n'] - (int) $tot['rev'];
$accuracy = $triesNoRev ? (int) round(100 * $tot['ok'] / $triesNoRev) : null;
$minutes = (int) round($tot['ms'] / 60000);
$solvedTotal = array_sum($solved);
$streak = streak_days($uid);
$firstTry = (int) q_val('SELECT COUNT(*) FROM exercise_status WHERE user_id = ? AND solved = 1 AND tries = 1', [$uid]);

// Actividad diaria de las últimas 12 semanas
$from = gmdate('Y-m-d', strtotime('-83 days'));
$daily = [];
foreach (q_all('SELECT SUBSTR(created_at, 1, 10) d, COUNT(*) n, SUM(is_correct) ok FROM attempts WHERE user_id = ? AND created_at >= ? GROUP BY SUBSTR(created_at, 1, 10)', [$uid, $from]) as $r) {
    $daily[$r['d']] = ['n' => (int) $r['n'], 'ok' => (int) $r['ok']];
}
$days = [];
for ($i = 83; $i >= 0; $i--) { $d = gmdate('Y-m-d', strtotime("-$i days")); $days[$d] = $daily[$d] ?? ['n' => 0, 'ok' => 0]; }
$weeks = array_chunk($days, 7, true);
$activeDays = count(array_filter($days, fn($x) => $x['n'] > 0));

// Niveles y puntos débiles
$levels = q_all('SELECT * FROM levels ORDER BY position');
$lp = level_progress($uid);
$weak = q_all('SELECT un.id, un.title, un.level_code, COUNT(*) n, SUM(a.is_correct) ok
               FROM attempts a JOIN exercises e ON e.id = a.exercise_id JOIN units un ON un.id = e.unit_id
               WHERE a.user_id = ? GROUP BY un.id, un.title, un.level_code HAVING COUNT(*) >= 4
               ORDER BY (SUM(a.is_correct) * 1.0 / COUNT(*)) ASC LIMIT 5', [$uid]);
$pending = (int) q_val('SELECT COUNT(*) FROM exercise_status WHERE user_id = ? AND solved = 0 AND tries > 0', [$uid]);

render('stats', compact('u', 'skills', 'accuracy', 'minutes', 'solvedTotal', 'streak', 'firstTry', 'tot', 'weeks', 'activeDays', 'levels', 'lp', 'weak', 'pending'), 'Mis estadísticas');
