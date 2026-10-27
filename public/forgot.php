<?php
/** «¿Olvidaste tu contraseña?»: envía un enlace para elegir una nueva. */
require dirname(__DIR__) . '/src/bootstrap.php';
require_once ROOT . '/src/mailer.php';
require_once ROOT . '/src/password_reset.php';

if (current_user()) redirect('/dashboard.php');

$errors = [];
$sent = false;
$email = strtolower(trim((string) ($_GET['email'] ?? '')));

if (is_post()) {
    csrf_check();
    $email = strtolower(post_str('email', 190));
    $_SESSION['forgot_tries'] = array_values(array_filter($_SESSION['forgot_tries'] ?? [], static fn($t) => $t > time() - 3600));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Escribí el email con el que creaste tu cuenta.';
    } elseif (count($_SESSION['forgot_tries']) >= 5) {
        $errors[] = 'Hiciste varios pedidos seguidos. Esperá un rato y revisá tu correo, incluida la carpeta de spam.';
    } elseif (!mail_configured()) {
        error_log('FORGOT sin servicio de email configurado');
        $contact = (string) env('CONTACT_EMAIL', '');
        $errors[] = 'En este momento no podemos enviar emails.' . ($contact ? " Escribinos a $contact y te ayudamos a recuperar tu cuenta." : ' Escribinos y te ayudamos a recuperar tu cuenta.');
    } else {
        $_SESSION['forgot_tries'][] = time();
        $u = q_one('SELECT * FROM users WHERE email = ?', [$email]);
        if ($u && pr_recent_count((int) $u['id']) < RESET_PER_HOUR) {
            $token = pr_create((int) $u['id']);
            if (!pr_send_email($u, $token)) error_log('FORGOT no se pudo enviar el email al usuario ' . (int) $u['id']);
        }
        // Misma respuesta exista o no la cuenta: así nadie puede averiguar qué emails están registrados.
        $sent = true;
    }
}

render('auth_forgot', compact('errors', 'sent', 'email'), 'Recuperar contraseña');
