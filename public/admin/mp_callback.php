<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';
require_admin();

if (isset($_GET['error'])) {
    unset($_SESSION['mp_oauth']);
    flash('error', 'Mercado Pago no autorizó la conexión. Podés intentar de nuevo.');
    redirect('/admin/payments.php');
}
try {
    $me = mp_oauth_finish((string) ($_GET['state'] ?? ''), (string) ($_GET['code'] ?? ''));
    flash('ok', 'Conectamos la cuenta ' . ($me['nickname'] ?? $me['email'] ?? '') . ' de Mercado Pago.');
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
}
redirect('/admin/payments.php');
