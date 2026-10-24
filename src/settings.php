<?php
declare(strict_types=1);

/*
 * Configuración editable desde el panel de administración (tabla `settings`).
 * Las claves secretas (token de Mercado Pago, clave del webhook) se guardan cifradas
 * con AES-256-GCM. La clave de cifrado sale de APP_KEY o, si no existe, de otras
 * variables de entorno que no viven en la base: un volcado de la base sola no las expone.
 * Si cambian esas variables, los secretos no se pueden descifrar y hay que volver a cargarlos.
 */

const SECRET_SETTINGS = ['mp_access_token', 'mp_webhook_secret'];

function settings_ensure(): void
{
    static $done = false;
    if ($done) return;
    $sqlite = db_is_sqlite();
    $engine = $sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $text = $sqlite ? 'TEXT' : 'MEDIUMTEXT';
    $fk = $sqlite ? 'INTEGER' : 'INT UNSIGNED';
    db()->exec("CREATE TABLE IF NOT EXISTS settings (
        k VARCHAR(64) NOT NULL PRIMARY KEY,
        v $text NOT NULL,
        updated_at DATETIME NOT NULL
    )$engine");
    // Pagos únicos (Checkout Pro). payment_id es la clave: un pago se acredita una sola vez.
    db()->exec("CREATE TABLE IF NOT EXISTS mp_payments (
        payment_id VARCHAR(40) NOT NULL PRIMARY KEY,
        user_id $fk NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        status VARCHAR(30) NOT NULL,
        days INT NOT NULL,
        credited INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    )$engine");
    $done = true;
}

function settings_all(): array
{
    static $cache = null;
    if ($cache !== null) return $cache;
    try {
        settings_ensure();
        $cache = [];
        foreach (q_all('SELECT k, v FROM settings') as $r) $cache[$r['k']] = $r['v'];
    } catch (Throwable $e) {
        log_line('settings: ' . $e->getMessage());
        $cache = [];
    }
    return $cache;
}

function setting_get(string $key, ?string $default = null): ?string
{
    $all = settings_all();
    if (!array_key_exists($key, $all) || $all[$key] === '') return $default;
    $v = $all[$key];
    if (in_array($key, SECRET_SETTINGS, true)) {
        $v = settings_decrypt($v);
        if ($v === null || $v === '') return $default;
    }
    return $v;
}

function setting_set(string $key, ?string $value): void
{
    settings_ensure();
    $value = $value ?? '';
    if ($value !== '' && in_array($key, SECRET_SETTINGS, true)) $value = settings_encrypt($value);
    if ($value === '') {
        q('DELETE FROM settings WHERE k = ?', [$key]);
    } elseif (q_val('SELECT COUNT(*) FROM settings WHERE k = ?', [$key])) {
        q('UPDATE settings SET v = ?, updated_at = ? WHERE k = ?', [$value, now_utc(), $key]);
    } else {
        q('INSERT INTO settings (k, v, updated_at) VALUES (?, ?, ?)', [$key, $value, now_utc()]);
    }
}

/** true si el valor guardado existe pero no se puede descifrar (cambió la clave). */
function setting_unreadable(string $key): bool
{
    $all = settings_all();
    return isset($all[$key]) && $all[$key] !== '' && settings_decrypt($all[$key]) === null;
}

function settings_key(): string
{
    $material = env('APP_KEY') ?: (env('MYSQL_URL', '') . '|' . env('GOOGLE_CLIENT_SECRET', '') . '|' . env('APP_NAME', ''));
    return hash('sha256', 'myteacher-settings|' . $material, true);
}

function settings_encrypt(string $plain): string
{
    $iv = random_bytes(12);
    $tag = '';
    $ct = openssl_encrypt($plain, 'aes-256-gcm', settings_key(), OPENSSL_RAW_DATA, $iv, $tag);
    return 'enc1:' . base64_encode($iv . $tag . $ct);
}

function settings_decrypt(string $stored): ?string
{
    if (!str_starts_with($stored, 'enc1:')) return null;
    $raw = base64_decode(substr($stored, 5), true);
    if ($raw === false || strlen($raw) < 29) return null;
    $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', settings_key(), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
    return $plain === false ? null : $plain;
}

/** Muestra solo el comienzo y el final de un secreto. */
function mask_secret(?string $s): string
{
    if (!$s) return '';
    return strlen($s) <= 12 ? str_repeat('•', strlen($s)) : substr($s, 0, 8) . '…' . substr($s, -4);
}
