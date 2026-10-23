<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/oauth.php';

if (current_user()) redirect('/dashboard.php');

$errors = [];
$email = '';

if (is_post()) {
    csrf_check();
    $email = strtolower(post_str('email', 190));
    $pass = (string) ($_POST['password'] ?? '');

    if (throttle_locked($email)) {
        $errors[] = 'Demasiados intentos fallidos. Esperá 15 minutos y probá de nuevo.';
    } else {
        $u = q_one('SELECT id, password_hash FROM users WHERE email = ?', [$email]);
        if ($u && $u['password_hash'] && password_verify($pass, $u['password_hash'])) {
            throttle_clear($email);
            login_user((int) $u['id']);
            after_login_redirect();
        }
        throttle_fail($email);
        $errors[] = ($u && !$u['password_hash'])
            ? 'Esta cuenta se creó con Google o Microsoft. Ingresá con ese botón.'
            : 'El email o la contraseña no coinciden.';
    }
}

render('auth_login', compact('errors', 'email'), 'Ingresar');
