<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';
$admin = require_admin();

if (is_post()) {
    csrf_check();
    $action = post_str('action', 20);

    if ($action === 'save') {
        $token = post_str('mp_access_token', 300);
        if ($token !== '') {
            if (!preg_match('/^(APP_USR|TEST)-[A-Za-z0-9-]{20,}$/', $token)) {
                flash('error', 'El Access Token tiene que empezar con APP_USR- (producción) o TEST- (prueba). Copialo completo desde Tus integraciones → Credenciales.');
                redirect('/admin/payments.php');
            }
            try {
                $me = mp_check_token($token);
                setting_set('mp_access_token', $token);
                setting_set('mp_account', trim(($me['nickname'] ?? '') . ' · ' . ($me['email'] ?? ''), ' ·') . ' (ID ' . ($me['id'] ?? '?') . ')');
            } catch (RuntimeException $e) {
                flash('error', $e->getMessage() . ' No guardamos el token.');
                redirect('/admin/payments.php');
            }
        }

        $secret = post_str('mp_webhook_secret', 200);
        if ($secret !== '') setting_set('mp_webhook_secret', $secret);
        if (!empty($_POST['clear_secret'])) setting_set('mp_webhook_secret', null);

        $price = round((float) str_replace(',', '.', post_str('plan_price', 20)));
        if ($price < 100 || $price > 10000000) {
            flash('error', 'El precio tiene que ser un monto en pesos entre $100 y $10.000.000.');
            redirect('/admin/payments.php');
        }
        setting_set('plan_price', (string) (int) $price);

        $mode = post_str('mp_mode', 20);
        setting_set('mp_mode', in_array($mode, ['subscription', 'once', 'both'], true) ? $mode : 'subscription');
        setting_set('mp_once_days', (string) max(1, min(365, (int) ($_POST['mp_once_days'] ?? 30))));
        setting_set('mp_enabled', !empty($_POST['mp_enabled']) ? '1' : '0');

        flash('ok', 'Guardamos la configuración de Mercado Pago.');
    } elseif ($action === 'test') {
        try {
            $me = mp_check_token(mp_token());
            setting_set('mp_account', trim(($me['nickname'] ?? '') . ' · ' . ($me['email'] ?? ''), ' ·') . ' (ID ' . ($me['id'] ?? '?') . ')');
            flash('ok', 'Conexión correcta con la cuenta ' . ($me['nickname'] ?? $me['email'] ?? '') . '.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }
    } elseif ($action === 'disconnect') {
        setting_set('mp_access_token', null);
        setting_set('mp_account', null);
        flash('ok', 'Quitamos el token guardado en el panel.' . (env('MP_ACCESS_TOKEN') ? ' Queda activo el de la variable MP_ACCESS_TOKEN de Railway.' : ''));
    }
    redirect('/admin/payments.php');
}

settings_ensure();
$cfg = [
    'token_panel' => setting_get('mp_access_token'),
    'token_env' => env('MP_ACCESS_TOKEN'),
    'token_unreadable' => setting_unreadable('mp_access_token'),
    'secret' => mp_webhook_secret(),
    'account' => setting_get('mp_account'),
    'price' => plan_price(),
    'mode' => mp_mode(),
    'once_days' => mp_once_days(),
    'enabled' => setting_get('mp_enabled', '1') === '1',
    'test' => mp_test_mode(),
    'webhook_url' => app_url('/webhook/mercadopago.php'),
];
$payments = q_all('SELECT p.*, u.email FROM mp_payments p LEFT JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC LIMIT 20');

render('admin_payments', compact('cfg', 'payments'), 'Mercado Pago');
