<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';

$u = require_login();
if (!is_post()) redirect('/account.php');
csrf_check();

if (!mp_enabled()) {
    flash('info', 'El pago online todavía no está disponible. Escribinos y te habilitamos el acceso.');
    redirect('/account.php');
}

$mode = mp_mode();
$want = post_str('kind', 20) === 'once' ? 'once' : 'subscription';
if ($mode !== 'both') $want = $mode;

if ($want === 'subscription' && has_full_access($u) && $u['sub_status'] === 'active') {
    flash('info', 'Ya tenés una suscripción activa.');
    redirect('/account.php');
}

try {
    redirect($want === 'once' ? mp_create_one_time($u) : mp_create_subscription($u));
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect('/account.php');
}
