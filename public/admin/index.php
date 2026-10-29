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
    } elseif ($action === 'newpass' || $action === 'sendlink') {
        require_once ROOT . '/src/mailer.php';
        require_once ROOT . '/src/password_reset.php';
        $target = q_one('SELECT * FROM users WHERE id = ?', [$uid]);
        if (!$target) {
            flash('error', 'No encontramos ese usuario.');
        } elseif ($action === 'sendlink') {
            $ok = mail_configured() && pr_send_email($target, pr_create($uid));
            flash($ok ? 'ok' : 'error', $ok
                ? "Le enviamos a {$target['email']} un enlace para elegir una contraseña nueva (vence en 60 minutos)."
                : 'No pudimos enviar el email. Revisá en Railway SMTP_HOST, SMTP_USER y SMTP_PASS (y los logs «MAIL»), o usá «Nueva contraseña».');
            log_line("Admin {$admin['email']} envió enlace de recuperación a {$target['email']}");
        } else {
            $password = rtrim(strtr(base64_encode(random_bytes(9)), '+/', 'kq'), '=');
            pr_table();
            q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $uid]);
            q('UPDATE password_resets SET used_at = ? WHERE user_id = ? AND used_at IS NULL', [now_utc(), $uid]);
            throttle_clear((string) $target['email']);
            log_line("Admin {$admin['email']} generó una contraseña nueva para {$target['email']}");
            flash('ok', "Contraseña nueva para {$target['email']}: $password — compartila ahora, no se vuelve a mostrar. El alumno puede cambiarla después con «¿Olvidaste tu contraseña?».");
        }
    } elseif ($action === 'delete') {
        $confirm = trim((string) ($_POST['confirm_email'] ?? ''));
        $target = $uid > 0 ? q_one('SELECT id, email, role, sub_status, mp_preapproval_id FROM users WHERE id = ?', [$uid]) : null;
        if (!$target) {
            flash('error', 'No encontramos ese usuario.');
        } elseif ((int) $target['id'] === (int) $admin['id']) {
            flash('error', 'No podés eliminar tu propia cuenta de administrador.');
        } elseif (!hash_equals(mb_strtolower((string) $target['email']), mb_strtolower($confirm))) {
            flash('error', 'Para eliminar un usuario, confirmá su dirección de email exacta.');
        } elseif (in_array((string) $target['sub_status'], ['active', 'pending', 'authorized'], true) || !empty($target['mp_preapproval_id'])) {
            flash('error', 'Este usuario tiene o tuvo una suscripción vinculada. Revisá y desvinculá o cancelá correctamente la suscripción en Mercado Pago antes de eliminarlo.');
        } elseif ($target['role'] === 'admin' && (int) q_val("SELECT COUNT(*) FROM users WHERE role = 'admin'") <= 1) {
            flash('error', 'No se puede eliminar el último administrador.');
        } else {
            try {
                $pdo = db();
                $pdo->beginTransaction();
                // Limpiar datos asociados de tablas que tengan columna user_id.
                // Mantener el historial de pagos sin vincularlo al usuario eliminado.
                if (db_is_sqlite()) {
                    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($tables as $table) {
                        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string) $table) || $table === 'users') continue;
                        $cols = $pdo->query('PRAGMA table_info("' . $table . '")')->fetchAll(PDO::FETCH_ASSOC);
                        if (!in_array('user_id', array_column($cols, 'name'), true)) continue;
                        if ($table === 'payments_log') $pdo->prepare('UPDATE payments_log SET user_id = NULL WHERE user_id = ?')->execute([$uid]);
                        else $pdo->prepare('DELETE FROM "' . $table . '" WHERE user_id = ?')->execute([$uid]);
                    }
                } else {
                    $stmt = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME = 'user_id'");
                    $stmt->execute();
                    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $table) {
                        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string) $table) || $table === 'users') continue;
                        $sql = $table === 'payments_log'
                            ? 'UPDATE `payments_log` SET user_id = NULL WHERE user_id = ?'
                            : 'DELETE FROM `' . $table . '` WHERE user_id = ?';
                        $pdo->prepare($sql)->execute([$uid]);
                    }
                }
                $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$uid]);
                $pdo->commit();
                log_line("Admin {$admin['email']} eliminó definitivamente la cuenta {$target['email']} (ID {$uid})");
                flash('ok', 'Usuario eliminado. Su cuenta y progreso asociado ya no están disponibles.');
            } catch (Throwable $e) {
                if (db()->inTransaction()) db()->rollBack();
                error_log('Admin delete account: ' . $e->getMessage());
                flash('error', 'No fue posible eliminar el usuario. No se aplicaron cambios.');
            }
        }
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

render('admin_home', compact('users', 'stats', 's', 'mrr', 'log', 'admin'), 'Admin');
