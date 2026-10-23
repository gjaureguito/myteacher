<?php
declare(strict_types=1);

function current_user(): ?array
{
    static $cache = false;
    if ($cache !== false) return $cache;
    $id = $_SESSION['uid'] ?? null;
    $cache = $id ? q_one('SELECT * FROM users WHERE id = ?', [$id]) : null;
    return $cache;
}

function require_login(): array
{
    $u = current_user();
    if (!$u) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'] ?? '/dashboard.php';
        flash('info', 'Ingresá a tu cuenta para continuar.');
        redirect('/login.php');
    }
    return $u;
}

function is_admin(?array $u): bool
{
    if (!$u) return false;
    if ($u['role'] === 'admin') return true;
    $list = array_filter(array_map('trim', explode(',', strtolower(env('ADMIN_EMAILS', '')))));
    return in_array(strtolower($u['email']), $list, true);
}

function require_admin(): array
{
    $u = require_login();
    if (!is_admin($u)) {
        http_response_code(403);
        exit('Acceso restringido.');
    }
    return $u;
}

function login_user(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = $id;
    q('UPDATE users SET last_login_at = ? WHERE id = ?', [now_utc(), $id]);
}

function after_login_redirect(): never
{
    $to = $_SESSION['after_login'] ?? '/dashboard.php';
    unset($_SESSION['after_login']);
    // Solo rutas internas
    if (!is_string($to) || !str_starts_with($to, '/') || str_starts_with($to, '//')) $to = '/dashboard.php';
    redirect($to);
}

function create_user(string $email, string $name, ?string $password, array $extra = []): int
{
    $cols = ['email' => strtolower($email), 'name' => $name, 'created_at' => now_utc()];
    if ($password !== null) $cols['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    $cols = array_merge($cols, $extra);
    $keys = implode(', ', array_keys($cols));
    $ph = implode(', ', array_fill(0, count($cols), '?'));
    q("INSERT INTO users ($keys) VALUES ($ph)", array_values($cols));
    return (int) db()->lastInsertId();
}

/**
 * Login/registro con proveedor externo (google | microsoft).
 * Si el email ya existe, vincula la cuenta; si no, la crea.
 */
function oauth_login(string $provider, string $sub, string $email, string $name): int
{
    $col = $provider === 'google' ? 'google_sub' : 'microsoft_sub';
    $u = q_one("SELECT id FROM users WHERE $col = ?", [$sub]);
    if ($u) return (int) $u['id'];

    $u = q_one('SELECT id FROM users WHERE email = ?', [strtolower($email)]);
    if ($u) {
        q("UPDATE users SET $col = ? WHERE id = ?", [$sub, $u['id']]);
        return (int) $u['id'];
    }
    return create_user($email, $name ?: explode('@', $email)[0], null, [$col => $sub]);
}

/* ---- Protección contra fuerza bruta en el login ---- */

function throttle_key(string $email): string
{
    return hash('sha256', strtolower($email) . '|' . ($_SERVER['REMOTE_ADDR'] ?? ''));
}

function throttle_locked(string $email): bool
{
    $r = q_one('SELECT locked_until FROM login_throttle WHERE k = ?', [throttle_key($email)]);
    return $r && $r['locked_until'] && $r['locked_until'] > now_utc();
}

function throttle_fail(string $email): void
{
    $k = throttle_key($email);
    $r = q_one('SELECT fails FROM login_throttle WHERE k = ?', [$k]);
    $fails = ($r['fails'] ?? 0) + 1;
    $lock = $fails >= 5 ? gmdate('Y-m-d H:i:s', time() + 15 * 60) : null;
    if ($r) {
        q('UPDATE login_throttle SET fails = ?, locked_until = ? WHERE k = ?', [$fails >= 5 ? 0 : $fails, $lock, $k]);
    } else {
        q('INSERT INTO login_throttle (k, fails, locked_until) VALUES (?, ?, ?)', [$k, $fails, $lock]);
    }
}

function throttle_clear(string $email): void
{
    q('DELETE FROM login_throttle WHERE k = ?', [throttle_key($email)]);
}
