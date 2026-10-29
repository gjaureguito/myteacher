<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();

// Si la suscripción figura activa pero venció la fecha, re-consultamos a Mercado Pago
if ($u['sub_status'] === 'active' && $u['mp_preapproval_id'] && !has_full_access($u) && env('MP_ACCESS_TOKEN')) {
    require ROOT . '/src/mercadopago.php';
    mp_sync_preapproval($u['mp_preapproval_id']);
    $u = q_one('SELECT * FROM users WHERE id = ?', [$u['id']]);
}

award_completed_levels($u);

$levels = q_all('SELECT * FROM levels ORDER BY position');
$lp = level_progress((int) $u['id']);
$last = last_unit((int) $u['id']);
$streak = streak_days((int) $u['id']);
$placement = q_one('SELECT * FROM placement_results WHERE user_id = ? ORDER BY id DESC LIMIT 1', [$u['id']]);
$solvedTotal = (int) q_val('SELECT COUNT(*) FROM exercise_status WHERE user_id = ? AND solved = 1', [$u['id']]);


// Ruta personalizada basada en ejercicios ya registrados, sin alterar el progreso.
$unitRoute = q_all('SELECT un.id, un.slug, un.title, un.level_code, un.position,
 COUNT(e.id) total, COALESCE(SUM(CASE WHEN es.solved=1 THEN 1 ELSE 0 END),0) solved
 FROM units un LEFT JOIN exercises e ON e.unit_id=un.id
 LEFT JOIN exercise_status es ON es.exercise_id=e.id AND es.user_id=?
 WHERE un.is_published=1 GROUP BY un.id,un.slug,un.title,un.level_code,un.position
 ORDER BY CASE un.level_code WHEN "a1" THEN 1 WHEN "a2" THEN 2 WHEN "b1" THEN 3 WHEN "b2" THEN 4 WHEN "c1" THEN 5 ELSE 6 END,un.position,un.id', [(int)$u['id']]);
$routeTarget = null;
if ($last) foreach ($unitRoute as $candidate) if ((int)$candidate['id']===(int)$last['id'] && (int)$candidate['solved']<(int)$candidate['total']) {$routeTarget=$candidate;break;}
if (!$routeTarget) foreach ($unitRoute as $candidate) if ((int)$candidate['total']>0 && (int)$candidate['solved']<(int)$candidate['total'] && can_access_unit($u,$candidate)) {$routeTarget=$candidate;break;}
if (!$routeTarget && $unitRoute) $routeTarget=$unitRoute[0];
$attemptSummary=q_one('SELECT COUNT(*) n, COALESCE(SUM(CASE WHEN revealed=0 THEN 1 ELSE 0 END),0) tries, COALESCE(SUM(CASE WHEN revealed=0 AND is_correct=1 THEN 1 ELSE 0 END),0) ok FROM attempts WHERE user_id=?',[(int)$u['id']]);
$performanceAccuracy=(int)$attemptSummary['tries']>0 ? (int)round(100*(int)$attemptSummary['ok']/(int)$attemptSummary['tries']):null;
$pendingReview=(int)q_val('SELECT COUNT(*) FROM exercise_status WHERE user_id=? AND solved=0 AND tries>0',[(int)$u['id']]);
$routeDone=$routeTarget ? (int)$routeTarget['solved'] : 0;
$routeTotal=$routeTarget ? (int)$routeTarget['total'] : 0;
$routePct=$routeTotal ? (int)round(100*$routeDone/$routeTotal):0;

render('dashboard', compact('u', 'levels', 'lp', 'last', 'streak', 'placement', 'solvedTotal', 'routeTarget', 'routeDone', 'routeTotal', 'routePct', 'performanceAccuracy', 'pendingReview'), 'Mis niveles');
