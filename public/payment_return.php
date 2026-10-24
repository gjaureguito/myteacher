<?php
/* Mercado Pago vuelve acá (back_urls del pago único) con ?payment_id=...&status=... */
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';

$u = require_login();
$id = (string) ($_GET['payment_id'] ?? $_GET['collection_id'] ?? '');

if ($id !== '' && preg_match('/^\d{3,30}$/', $id)) {
    try {
        $r = mp_sync_payment($id);
    } catch (Throwable $e) {
        $r = null;
        log_line('payment_return: ' . $e->getMessage());
    }
    if ($r && $r['user_id'] === (int) $u['id']) {
        if ($r['status'] === 'approved') {
            flash('ok', '¡Pago aprobado! Ya tenés acceso completo a todos los niveles.');
            redirect('/dashboard.php');
        }
        if (in_array($r['status'], ['rejected', 'cancelled'], true)) {
            flash('error', 'Mercado Pago no aprobó el pago. Podés intentar de nuevo con otro medio.');
            redirect('/account.php');
        }
    }
}
flash('info', 'Estamos esperando la confirmación de Mercado Pago. Si pagaste en efectivo puede tardar unas horas; te activamos el acceso automáticamente.');
redirect('/account.php');
