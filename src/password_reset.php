<?php
declare(strict_types=1);
/*
 * Recuperación de contraseña.
 * - El enlace lleva un token aleatorio de 256 bits; en la base solo se guarda su hash SHA-256.
 * - Vence a los 60 minutos y sirve una sola vez. Pedir uno nuevo anula los anteriores.
 * - Límite: 3 pedidos por email por hora.
 */

const RESET_TTL = 3600;
const RESET_PER_HOUR = 3;

function pr_table(): void
{
    static $done = false;
    if ($done) return;
    $sqlite = db_is_sqlite();
    db()->exec('CREATE TABLE IF NOT EXISTS password_resets (
        token_hash CHAR(64) NOT NULL PRIMARY KEY,
        user_id ' . ($sqlite ? 'INTEGER' : 'INT UNSIGNED') . ' NOT NULL,
        created_at DATETIME NOT NULL,
        expires_at DATETIME NOT NULL,
        used_at DATETIME NULL
    )' . ($sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'));
    $done = true;
}

/** Cantidad de pedidos de este usuario en la última hora. */
function pr_recent_count(int $userId): int
{
    pr_table();
    $r = q_one('SELECT COUNT(*) AS n FROM password_resets WHERE user_id = ? AND created_at > ?', [$userId, gmdate('Y-m-d H:i:s', time() - 3600)]);
    return (int) ($r['n'] ?? 0);
}

/** Crea un enlace nuevo (anula los anteriores sin usar) y devuelve el token en claro. */
function pr_create(int $userId): string
{
    pr_table();
    $token = bin2hex(random_bytes(32));
    $now = time();
    q('UPDATE password_resets SET used_at = ? WHERE user_id = ? AND used_at IS NULL', [gmdate('Y-m-d H:i:s', $now), $userId]);
    q('DELETE FROM password_resets WHERE expires_at < ?', [gmdate('Y-m-d H:i:s', $now - 86400)]);
    q('INSERT INTO password_resets (token_hash, user_id, created_at, expires_at) VALUES (?,?,?,?)',
        [hash('sha256', $token), $userId, gmdate('Y-m-d H:i:s', $now), gmdate('Y-m-d H:i:s', $now + RESET_TTL)]);
    return $token;
}

/** Devuelve el usuario del enlace si es válido, o null si no existe, venció o ya se usó. */
function pr_user(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) return null;
    pr_table();
    $r = q_one('SELECT user_id, expires_at, used_at FROM password_resets WHERE token_hash = ?', [hash('sha256', $token)]);
    if (!$r || $r['used_at'] || strtotime($r['expires_at'] . ' UTC') < time()) return null;
    return q_one('SELECT * FROM users WHERE id = ?', [(int) $r['user_id']]);
}

/** Cambia la contraseña, marca el enlace como usado y anula el resto. */
function pr_consume(string $token, array $user, string $password): void
{
    $now = gmdate('Y-m-d H:i:s');
    q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), (int) $user['id']]);
    q('UPDATE password_resets SET used_at = ? WHERE user_id = ? AND used_at IS NULL', [$now, (int) $user['id']]);
    throttle_clear((string) $user['email']);
}

/** Arma y envía el email con el enlace. */
function pr_send_email(array $user, string $token): bool
{
    $link = app_url('/reset_password.php?token=' . $token);
    $name = explode(' ', trim((string) $user['name']))[0] ?: 'Hola';
    $app = app_name();
    $subject = "Restablecé tu contraseña de $app";
    $text = "$name, recibimos un pedido para restablecer la contraseña de tu cuenta de $app.\n\n"
        . "Para elegir una nueva, abrí este enlace (vence en 60 minutos y sirve una sola vez):\n$link\n\n"
        . "Si no lo pediste vos, ignorá este email: tu contraseña sigue igual.";
    $e = static fn(string $s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    $html = '<!doctype html><html lang="es"><body style="margin:0;background:#edf7ff;font-family:Segoe UI,Arial,sans-serif;color:#142c49">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:32px 16px">'
        . '<table role="presentation" width="100%" style="max-width:520px;background:#ffffff;border-radius:16px;padding:32px" cellpadding="0" cellspacing="0"><tr><td>'
        . '<p style="margin:0 0 18px;font-weight:800;font-size:20px;color:#1264c7">' . $e($app) . '</p>'
        . '<h1 style="margin:0 0 12px;font-size:22px">Restablecé tu contraseña</h1>'
        . '<p style="margin:0 0 20px;font-size:16px;line-height:1.5">' . $e($name) . ', recibimos un pedido para restablecer la contraseña de tu cuenta. Tocá el botón para elegir una nueva.</p>'
        . '<p style="margin:0 0 24px"><a href="' . $e($link) . '" style="display:inline-block;background:#ff812d;color:#ffffff;text-decoration:none;font-weight:700;padding:14px 24px;border-radius:999px">Elegir una contraseña nueva</a></p>'
        . '<p style="margin:0 0 8px;font-size:14px;color:#4a5f76">El enlace vence en 60 minutos y sirve una sola vez. Si el botón no funciona, copiá esta dirección en el navegador:</p>'
        . '<p style="margin:0 0 20px;font-size:13px;word-break:break-all"><a href="' . $e($link) . '" style="color:#1264c7">' . $e($link) . '</a></p>'
        . '<p style="margin:0;font-size:14px;color:#4a5f76">Si no lo pediste vos, ignorá este email: tu contraseña sigue igual.</p>'
        . '</td></tr></table></td></tr></table></body></html>';
    return send_mail((string) $user['email'], $subject, $html, $text);
}
