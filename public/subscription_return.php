<?php
/* Mercado Pago vuelve acá (back_url) con ?preapproval_id=... al terminar el checkout. */
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';

$u = require_login();
$id = (string) ($_GET['preapproval_id'] ?? $u['mp_preapproval_id'] ?? '');

if ($id !== '' && preg_match('/^[A-Za-z0-9_-]{6,80}$/', $id)) {
    $r = mp_sync_preapproval($id);
    if ($r && $r['user_id'] === (int) $u['id'] && $r['status'] === 'authorized') {
        flash('ok', '¡Suscripción activa! Ya tenés acceso completo a todos los niveles.');
        redirect('/dashboard.php');
    }
}
flash('info', 'Estamos esperando la confirmación de Mercado Pago. Suele tardar unos minutos; te activamos el acceso automáticamente.');
redirect('/account.php');
