<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';
require_admin();

if (!is_post()) redirect('/admin/payments.php');
csrf_check();
if (!mp_oauth_ready()) {
    flash('error', 'Falta crear la aplicación de Mercado Pago: cargá MP_CLIENT_ID y MP_CLIENT_SECRET en las variables del servicio.');
    redirect('/admin/payments.php');
}
mp_oauth_start();
