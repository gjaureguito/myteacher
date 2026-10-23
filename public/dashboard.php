<?php
require dirname(__DIR__) . '/src/bootstrap.php';

$u = require_login();

// Si la suscripción figura activa pero venció la fecha, re-consultamos a Mercado Pago
if ($u['sub_status'] === 'active' && $u['mp_preapproval_id'] && !has_full_access($u) && env('MP_ACCESS_TOKEN')) {
    require ROOT . '/src/mercadopago.php';
    mp_sync_preapproval($u['mp_preapproval_id']);
    $u = q_one('SELECT * FROM users WHERE id = ?', [$u['id']]);
}

$levels = q_all('SELECT * FROM levels ORDER BY position');
$lp = level_progress((int) $u['id']);
$last = last_unit((int) $u['id']);
$streak = streak_days((int) $u['id']);
$placement = q_one('SELECT * FROM placement_results WHERE user_id = ? ORDER BY id DESC LIMIT 1', [$u['id']]);
$solvedTotal = (int) q_val('SELECT COUNT(*) FROM exercise_status WHERE user_id = ? AND solved = 1', [$u['id']]);

render('dashboard', compact('u', 'levels', 'lp', 'last', 'streak', 'placement', 'solvedTotal'), 'Mis niveles');
