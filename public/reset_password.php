<?php
/** Elegir una contraseña nueva desde el enlace del email. */
require dirname(__DIR__) . '/src/bootstrap.php';
require_once ROOT . '/src/password_reset.php';

header('Referrer-Policy: no-referrer'); // el token no debe filtrarse a otros sitios
$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$user = pr_user($token);
$errors = [];

if ($user && is_post()) {
    csrf_check();
    $pass = (string) ($_POST['password'] ?? '');
    $pass2 = (string) ($_POST['password2'] ?? '');
    if (strlen($pass) < 8) $errors[] = 'La contraseña tiene que tener al menos 8 caracteres.';
    elseif (strlen($pass) > 200) $errors[] = 'La contraseña es demasiado larga.';
    elseif ($pass !== $pass2) $errors[] = 'Las dos contraseñas no coinciden.';
    if (!$errors) {
        pr_consume($token, $user, $pass);
        login_user((int) $user['id']);
        flash('ok', 'Listo: cambiamos tu contraseña y ya ingresaste.');
        redirect('/dashboard.php');
    }
}

render('auth_reset', ['valid' => (bool) $user, 'token' => $token, 'errors' => $errors, 'email' => $user['email'] ?? ''], 'Elegir contraseña nueva');
