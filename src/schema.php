<?php
declare(strict_types=1);

function run_schema(): void
{
    $sqlite = db_is_sqlite();
    $pk = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
    $fk = $sqlite ? 'INTEGER' : 'INT UNSIGNED';
    $text = $sqlite ? 'TEXT' : 'MEDIUMTEXT';
    $engine = $sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    $tables = [
        "CREATE TABLE IF NOT EXISTS purchase_agreements (
            id $pk,
            user_id $fk NOT NULL,
            nonce VARCHAR(64) NOT NULL UNIQUE,
            version VARCHAR(40) NOT NULL,
            document_hash VARCHAR(64) NOT NULL,
            document_text $text NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            currency VARCHAR(3) NOT NULL,
            kind VARCHAR(20) NOT NULL,
            days INT NOT NULL,
            accepted_at DATETIME NOT NULL
        )$engine",
        "CREATE TABLE IF NOT EXISTS consumer_requests (
            id $pk,
            receipt VARCHAR(40) NOT NULL UNIQUE,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL,
            kind VARCHAR(20) NOT NULL,
            reference VARCHAR(300) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'received',
            created_at DATETIME NOT NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS purchase_billing (
            nonce VARCHAR(64) NOT NULL PRIMARY KEY,
            user_id $fk NOT NULL,
            billing_data $text NOT NULL,
            created_at DATETIME NOT NULL
        )$engine",
        "CREATE TABLE IF NOT EXISTS scene_status (
            user_id $fk NOT NULL,
            scene_id VARCHAR(80) NOT NULL,
            question_id INT NOT NULL,
            solved INT NOT NULL DEFAULT 0,
            tries INT NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (user_id, scene_id, question_id)
        )$engine",
        "CREATE TABLE IF NOT EXISTS meta (
            k VARCHAR(64) NOT NULL PRIMARY KEY,
            v VARCHAR(255) NOT NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS users (
            id $pk,
            email VARCHAR(190) NOT NULL UNIQUE,
            name VARCHAR(120) NOT NULL,
            password_hash VARCHAR(255) NULL,
            google_sub VARCHAR(190) NULL,
            microsoft_sub VARCHAR(190) NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'student',
            sub_status VARCHAR(20) NOT NULL DEFAULT 'none',
            access_until DATETIME NULL,
            mp_preapproval_id VARCHAR(80) NULL,
            current_level VARCHAR(4) NULL,
            created_at DATETIME NOT NULL,
            last_login_at DATETIME NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS levels (
            code VARCHAR(4) NOT NULL PRIMARY KEY,
            name VARCHAR(80) NOT NULL,
            description VARCHAR(500) NOT NULL,
            position INT NOT NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS units (
            id $pk,
            level_code VARCHAR(4) NOT NULL,
            slug VARCHAR(120) NOT NULL UNIQUE,
            position INT NOT NULL,
            title VARCHAR(160) NOT NULL,
            summary VARCHAR(500) NOT NULL,
            theory_html $text NOT NULL,
            vocab_json $text NOT NULL,
            is_published INT NOT NULL DEFAULT 1,
            updated_at DATETIME NOT NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS exercises (
            id $pk,
            unit_id $fk NOT NULL,
            position INT NOT NULL,
            type VARCHAR(20) NOT NULL,
            prompt VARCHAR(1000) NOT NULL,
            data_json $text NOT NULL,
            answer_json $text NOT NULL,
            explanation VARCHAR(1000) NOT NULL DEFAULT ''
        )$engine",

        "CREATE TABLE IF NOT EXISTS attempts (
            id $pk,
            user_id $fk NOT NULL,
            exercise_id $fk NOT NULL,
            is_correct INT NOT NULL,
            created_at DATETIME NOT NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS exercise_status (
            user_id $fk NOT NULL,
            exercise_id $fk NOT NULL,
            solved INT NOT NULL DEFAULT 0,
            tries INT NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (user_id, exercise_id)
        )$engine",

        "CREATE TABLE IF NOT EXISTS payments_log (
            id $pk,
            user_id $fk NULL,
            topic VARCHAR(60) NOT NULL,
            resource_id VARCHAR(80) NOT NULL,
            status VARCHAR(40) NULL,
            payload $text NULL,
            created_at DATETIME NOT NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS placement_results (
            id $pk,
            user_id $fk NOT NULL,
            score INT NOT NULL,
            total INT NOT NULL,
            level_code VARCHAR(4) NOT NULL,
            created_at DATETIME NOT NULL
        )$engine",

        "CREATE TABLE IF NOT EXISTS login_throttle (
            k VARCHAR(190) NOT NULL PRIMARY KEY,
            fails INT NOT NULL DEFAULT 0,
            locked_until DATETIME NULL
        )$engine",
    ];

    foreach ($tables as $sql) {
        db()->exec($sql);
    }

    $indexes = [
        'CREATE INDEX idx_units_level ON units (level_code, position)',
        'CREATE INDEX idx_ex_unit ON exercises (unit_id, position)',
        'CREATE INDEX idx_attempts_user ON attempts (user_id, created_at)',
        'CREATE INDEX idx_users_preapproval ON users (mp_preapproval_id)',
    ];
    foreach ($indexes as $sql) {
        try { db()->exec($sql); } catch (Throwable $e) { /* ya existe */ }
    }

    // v2: habilidades, pistas, soluciones vistas, tiempo por respuesta e íconos de unidad
    $columns = [
        ['exercises', 'skill', "VARCHAR(20) NOT NULL DEFAULT 'grammar'"],
        ['exercises', 'hint', "VARCHAR(500) NOT NULL DEFAULT ''"],
        ['exercises', 'compact_json', "$text NULL"],
        ['attempts', 'revealed', 'INT NOT NULL DEFAULT 0'],
        ['attempts', 'ms', 'INT NULL'],
        ['units', 'icon', "VARCHAR(16) NOT NULL DEFAULT ''"],
        ['units', 'source_hash', 'VARCHAR(64) NULL'],
        ['exercise_status', 'last_answer', "$text NULL"],
    ];
    foreach ($columns as [$table, $col, $def]) {
        try { db()->exec("ALTER TABLE $table ADD COLUMN $col $def"); } catch (Throwable $e) { /* ya existe */ }
    }
    db()->exec("UPDATE exercises SET skill = CASE type WHEN 'listen' THEN 'listening' WHEN 'dictation' THEN 'listening' ELSE skill END WHERE skill = 'grammar'");
    foreach (['CREATE INDEX idx_attempts_ex ON attempts (exercise_id)', 'CREATE INDEX idx_status_user ON exercise_status (user_id, solved)'] as $sql) {
        try { db()->exec($sql); } catch (Throwable $e) { /* ya existe */ }
    }
}
