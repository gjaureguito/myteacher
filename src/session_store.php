<?php
declare(strict_types=1);

/**
 * Sesiones PHP persistentes en la base de datos.
 * El contenido de la sesión nunca se expone al navegador.
 */
final class DatabaseSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    private PDO $pdo;
    private int $lifetime;

    public function __construct(PDO $pdo, int $lifetime)
    {
        $this->pdo = $pdo;
        $this->lifetime = $lifetime;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $stmt = $this->pdo->prepare('SELECT session_data FROM php_sessions WHERE session_id = ? AND expires_at > ?');
        $stmt->execute([$id, time()]);
        $data = $stmt->fetchColumn();
        return $data === false ? '' : (string) $data;
    }

    public function write(string $id, string $data): bool
    {
        $expiry = time() + $this->lifetime;
        if (db_is_sqlite()) {
            $sql = 'INSERT INTO php_sessions (session_id, session_data, expires_at) VALUES (?, ?, ?)
                    ON CONFLICT(session_id) DO UPDATE SET session_data=excluded.session_data, expires_at=excluded.expires_at';
        } else {
            $sql = 'INSERT INTO php_sessions (session_id, session_data, expires_at) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE session_data=VALUES(session_data), expires_at=VALUES(expires_at)';
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $data, $expiry]);
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM php_sessions WHERE session_id = ?');
        return $stmt->execute([$id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $stmt = $this->pdo->prepare('DELETE FROM php_sessions WHERE expires_at <= ?');
        $stmt->execute([time()]);
        return $stmt->rowCount();
    }

    public function validateId(string $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM php_sessions WHERE session_id = ? AND expires_at > ?');
        $stmt->execute([$id, time()]);
        return $stmt->fetchColumn() !== false;
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        $stmt = $this->pdo->prepare('UPDATE php_sessions SET expires_at = ? WHERE session_id = ?');
        $stmt->execute([time() + $this->lifetime, $id]);
        return $stmt->rowCount() > 0 || $this->write($id, $data);
    }
}

/** Crea la tabla compartida una sola vez; MySQL la conserva entre despliegues. */
function enable_database_sessions(int $lifetime): void
{
    $pdo = db();
    $pdo->exec(db_is_sqlite()
        ? 'CREATE TABLE IF NOT EXISTS php_sessions (session_id TEXT PRIMARY KEY, session_data BLOB NOT NULL, expires_at INTEGER NOT NULL)'
        : 'CREATE TABLE IF NOT EXISTS php_sessions (session_id VARCHAR(128) PRIMARY KEY, session_data MEDIUMBLOB NOT NULL, expires_at BIGINT NOT NULL, INDEX php_sessions_expiry (expires_at))');
    session_set_save_handler(new DatabaseSessionHandler($pdo, $lifetime), true);
}
