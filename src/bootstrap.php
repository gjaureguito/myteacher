<?php
declare(strict_types=1);

/*
 * Punto de entrada común: configuración, sesión, base de datos y helpers.
 * Toda la configuración sale de variables de entorno (Railway → Variables).
 */

define('ROOT', dirname(__DIR__));

function env(string $key, ?string $default = null): ?string
{
    $v = getenv($key);
    if ($v !== false) $v = trim($v, " \t\n\r\"'"); // tolera espacios o comillas pegadas por error
    if ($v === false || $v === '') {
        return $default;
    }
    return $v;
}

// Carga opcional de .env en desarrollo local
if (is_file(ROOT . '/.env')) {
    foreach (file(ROOT . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        if (getenv($k) === false) putenv("$k=" . trim($v, "\"'"));
    }
}

const PLAN_PRICE = 30000;

function app_name(): string { return env('APP_NAME', 'InglésPro'); }
function plan_price(): float { return (float) env('PLAN_PRICE', (string) PLAN_PRICE); }

function app_url(string $path = ''): string
{
    $base = env('APP_URL');
    if (!$base) {
        $proto = ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') ?: ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = $proto . '://' . $host;
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function is_https(): bool
{
    return (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
}

date_default_timezone_set(env('TZ', 'America/Argentina/Buenos_Aires'));

require ROOT . '/src/db.php';
require ROOT . '/src/helpers.php';
require ROOT . '/src/auth.php';
require ROOT . '/src/access.php';
require ROOT . '/src/progress.php';
require ROOT . '/src/grading.php';
require ROOT . '/src/learning.php';

if (PHP_SAPI !== 'cli') {
    session_name('ipsess');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

db_migrate_if_needed();
