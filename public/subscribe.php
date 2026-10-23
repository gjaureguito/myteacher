<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';

$u = require_login();
if (!is_post()) redirect('/account.php');
csrf_check();

if (has_full_access($u) && $u['sub_status'] === 'active') {
    flash('info', 'Ya tenés una suscripción activa.');
    redirect('/account.php');
}

try {
    redirect(mp_create_subscription($u));
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect('/account.php');
}
