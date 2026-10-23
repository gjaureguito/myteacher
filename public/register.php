<?php
require dirname(__DIR__) . '/src/bootstrap.php';
require ROOT . '/src/oauth.php';

if (current_user()) redirect('/dashboard.php');

$errors = [];
$old = ['name' => '', 'email' => ''];

if (is_post()) {
    csrf_check();
    $old['name'] = post_str('name', 120);
    $old['email'] = strtolower(post_str('email', 190));
    $pass = (string) ($_POST['password'] ?? '');

    if (mb_strlen($old['name']) < 2) $errors[] = 'Escribí tu nombre.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'El email no es válido.';
    if (strlen($pass) < 8) $errors[] = 'La contraseña tiene que tener al menos 8 caracteres.';
    if (empty($_POST['terms'])) $errors[] = 'Tenés que aceptar los términos.';
    if (!$errors && q_val('SELECT id FROM users WHERE email = ?', [$old['email']])) {
        $errors[] = 'Ya existe una cuenta con ese email. Ingresá o usá Google/Microsoft.';
    }

    if (!$errors) {
        $id = create_user($old['email'], $old['name'], $pass);
        login_user($id);
        flash('ok', '¡Listo! Tu cuenta está creada. Ya podés probar la primera unidad de cada nivel.');
        redirect('/placement.php?welcome=1');
    }
}

render('auth_register', compact('errors', 'old'), 'Crear cuenta');
