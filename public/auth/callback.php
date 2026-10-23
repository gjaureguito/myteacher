<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/oauth.php';

$p = (string) ($_GET['provider'] ?? '');
if (!oauth_enabled($p)) redirect('/login.php');

try {
    $info = oauth_finish($p);
    $isNew = !q_val('SELECT id FROM users WHERE email = ?', [strtolower($info['email'])]);
    $id = oauth_login($p, $info['sub'], $info['email'], $info['name']);
    login_user($id);
    if ($isNew) {
        flash('ok', '¡Bienvenido/a! Tu cuenta está creada. Empezá con el test de nivel o probá la primera unidad de cada nivel.');
        redirect('/placement.php?welcome=1');
    }
    after_login_redirect();
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect('/login.php');
}
