<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
$admin = require_admin();

if (is_post()) {
    csrf_check();
    $uid = (int) ($_POST['user_id'] ?? 0);
    $action = post_str('action', 20);
    if ($action === 'grant') {
        $days = max(1, min(365, (int) ($_POST['days'] ?? 30)));
        $cur = q_val('SELECT access_until FROM users WHERE id = ?', [$uid]);
        $base = ($cur && $cur > now_utc()) ? strtotime($cur . ' UTC') : time();
        q("UPDATE users SET access_until = ?, sub_status = CASE WHEN sub_status = 'active' THEN 'active' ELSE 'manual' END WHERE id = ?",
            [gmdate('Y-m-d H:i:s', $base + $days * 86400), $uid]);
        flash('ok', "Acceso extendido $days días.");
    } elseif ($action === 'create') {
        $email = strtolower(post_str('email', 190));
        $name = post_str('name', 120);
        $password = (string) ($_POST['password'] ?? '');
        $role = post_str('role', 20) === 'admin' ? 'admin' : 'student';
        $days = max(0, min(3650, (int) ($_POST['days'] ?? 0)));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Ingresá un email válido.');
        } elseif (q_val('SELECT COUNT(*) FROM users WHERE email = ?', [$email])) {
            flash('error', "Ya existe un usuario con el email $email. Buscalo en la lista para darle acceso.");
        } elseif ($password !== '' && strlen($password) < 8) {
            flash('error', 'La contraseña tiene que tener al menos 8 caracteres, o dejala vacía para generar una.');
        } else {
            $generated = $password === '';
            if ($generated) $password = rtrim(strtr(base64_encode(random_bytes(9)), '+/', 'kq'), '=');
            $extra = ['role' => $role];
            if ($days > 0) {
                $extra['access_until'] = gmdate('Y-m-d H:i:s', time() + $days * 86400);
                $extra['sub_status'] = 'manual';
            }
            create_user($email, $name !== '' ? $name : explode('@', $email)[0], $password, $extra);
            log_line("Admin {$admin['email']} creó el usuario $email ($role, $days días)");
            $msg = "Creamos la cuenta de $email" . ($role === 'admin' ? ' como administrador' : '')
                . ($days > 0 ? " con $days días de acceso completo" : ' con acceso de demo') . '.';
            $msg .= $generated ? " Contraseña generada: $password — compartila ahora, no se vuelve a mostrar." : '';
            $msg .= ' También puede entrar con Google o Microsoft usando ese email.';
            flash('ok', $msg);
            redirect('/admin/?s=' . urlencode($email));
        }
        redirect('/admin/');
    } elseif ($action === 'revoke') {
        q("UPDATE users SET access_until = NULL, sub_status = 'none' WHERE id = ?", [$uid]);
        flash('ok', 'Acceso quitado.');
    }
    redirect('/admin/?s=' . urlencode((string) ($_GET['s'] ?? '')));
}

$s = trim((string) ($_GET['s'] ?? ''));
$params = [];
$where = '';
if ($s !== '') { $where = 'WHERE email LIKE ? OR name LIKE ?'; $params = ["%$s%", "%$s%"]; }
$users = q_all("SELECT * FROM users $where ORDER BY id DESC LIMIT 100", $params);

$stats = [
    'Usuarios' => (int) q_val('SELECT COUNT(*) FROM users'),
    'Con acceso vigente' => (int) q_val('SELECT COUNT(*) FROM users WHERE access_until > ?', [now_utc()]),
    'Suscripciones activas' => (int) q_val("SELECT COUNT(*) FROM users WHERE sub_status = 'active' AND access_until > ?", [now_utc()]),
    'Nuevos (7 días)' => (int) q_val('SELECT COUNT(*) FROM users WHERE created_at > ?', [gmdate('Y-m-d H:i:s', time() - 7 * 86400)]),
    'Respuestas hoy' => (int) q_val('SELECT COUNT(*) FROM attempts WHERE created_at > ?', [gmdate('Y-m-d 00:00:00')]),
];
$active = $stats['Suscripciones activas'];
$mrr = $active * plan_price();
$log = q_all('SELECT p.*, u.email FROM payments_log p LEFT JOIN users u ON u.id = p.user_id ORDER BY p.id DESC LIMIT 15');

render('admin_home', compact('users', 'stats', 's', 'mrr', 'log'), 'Admin');
