<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';

$u = require_login();

if (is_post()) {
    csrf_check();
    $action = post_str('action', 30);

    if ($action === 'cancel') {
        if (mp_cancel_subscription($u)) {
            flash('ok', 'Cancelaste la suscripción. Seguís teniendo acceso hasta el ' . fmt_date($u['access_until']) . '.');
        } else {
            flash('error', 'No pudimos cancelar en Mercado Pago. Probá de nuevo en unos minutos.');
        }
    } elseif ($action === 'refresh' && $u['mp_preapproval_id']) {
        mp_sync_preapproval($u['mp_preapproval_id']);
        flash('info', 'Actualizamos el estado de tu suscripción.');
    } elseif ($action === 'profile') {
        $name = post_str('name', 120);
        if (mb_strlen($name) >= 2) {
            q('UPDATE users SET name = ? WHERE id = ?', [$name, $u['id']]);
            flash('ok', 'Guardamos tus datos.');
        }
    } elseif ($action === 'password') {
        $new = (string) ($_POST['new_password'] ?? '');
        $cur = (string) ($_POST['current_password'] ?? '');
        if ($u['password_hash'] && !password_verify($cur, $u['password_hash'])) {
            flash('error', 'La contraseña actual no coincide.');
        } elseif (strlen($new) < 8) {
            flash('error', 'La nueva contraseña tiene que tener al menos 8 caracteres.');
        } else {
            q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $u['id']]);
            flash('ok', 'Cambiaste tu contraseña.');
        }
    }
    redirect('/account.php');
}

render('account', ['u' => $u], 'Mi cuenta');
