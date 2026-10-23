<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/oauth.php';

$p = (string) ($_GET['provider'] ?? '');
if (!oauth_enabled($p)) {
    flash('error', 'Ese método de ingreso todavía no está habilitado. Usá email y contraseña.');
    redirect('/login.php');
}
oauth_start($p);
