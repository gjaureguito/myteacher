<?php
declare(strict_types=1);

/*
 * Conexión PDO. En Railway usa MySQL (variables MYSQLHOST, MYSQLUSER, etc. o MYSQL_URL).
 * Para desarrollo local podés usar DB_DRIVER=sqlite (archivo en storage/).
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;

    $driver = env('DB_DRIVER', 'mysql');
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if ($driver === 'sqlite') {
        $file = env('SQLITE_PATH', ROOT . '/storage/app.sqlite');
        $pdo = new PDO('sqlite:' . $file, null, null, $opts);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        return $pdo;
    }

    $url = env('MYSQL_URL') ?: env('DATABASE_URL');
    if ($url) {
        $p = parse_url($url);
        $host = $p['host'];
        $port = (string) ($p['port'] ?? 3306);
        $user = urldecode($p['user'] ?? '');
        $pass = urldecode($p['pass'] ?? '');
        $name = ltrim($p['path'] ?? '', '/');
    } else {
        $host = env('MYSQLHOST', '127.0.0.1');
        $port = env('MYSQLPORT', '3306');
        $user = env('MYSQLUSER', 'root');
        $pass = env('MYSQLPASSWORD', '');
        $name = env('MYSQLDATABASE', 'ingles');
    }
    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, $opts);
    $pdo->exec("SET time_zone = '+00:00'");
    return $pdo;
}

function db_is_sqlite(): bool
{
    return env('DB_DRIVER', 'mysql') === 'sqlite';
}

/** Ejecuta una consulta preparada y devuelve el statement. */
function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}

function q_one(string $sql, array $params = []): ?array
{
    $r = q($sql, $params)->fetch();
    return $r === false ? null : $r;
}

function q_all(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

function q_val(string $sql, array $params = [])
{
    $r = q($sql, $params)->fetchColumn();
    return $r === false ? null : $r;
}

function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}

/**
 * Crea las tablas y carga el contenido la primera vez.
 * Se controla con la tabla `meta` (schema_version).
 */
function db_migrate_if_needed(): void
{
    $target = 5;
    try {
        $v = (int) q_val("SELECT v FROM meta WHERE k = 'schema_version'");
    } catch (Throwable $e) {
        $v = 0;
    }
    if ($v < $target) {
        require_once ROOT . '/src/schema.php';
        run_schema();
        meta_set('schema_version', (string) $target);
    }

    // Contenido versionado: cuando cambian los archivos de content/ se sincroniza sin borrar progreso
    require_once ROOT . '/src/seed.php';
    $sig = content_signature();
    if (meta_get('content_sig') !== $sig) {
        try {
            seed_content(false);
            meta_set('content_sig', $sig);
        } catch (Throwable $e) {
            error_log('Sincronización de contenido: ' . $e->getMessage());
        }
    }
}

function meta_get(string $k): ?string
{
    $v = q_val('SELECT v FROM meta WHERE k = ?', [$k]);
    return $v === null ? null : (string) $v;
}

function meta_set(string $k, string $v): void
{
    if (q_val('SELECT k FROM meta WHERE k = ?', [$k]) !== null) q('UPDATE meta SET v = ? WHERE k = ?', [$v, $k]);
    else q('INSERT INTO meta (k, v) VALUES (?, ?)', [$k, $v]);
}
