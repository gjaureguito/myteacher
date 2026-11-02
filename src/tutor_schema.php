<?php
declare(strict_types=1);

/** Additive migration, kept separate from the existing catalog/billing schema. */
function tutor_run_schema(): void {
    $sqlite=db_is_sqlite();
    $pk=$sqlite?'INTEGER PRIMARY KEY AUTOINCREMENT':'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
    $fk=$sqlite?'INTEGER':'INT UNSIGNED';
    $text=$sqlite?'TEXT':'MEDIUMTEXT';
    $engine=$sqlite?'':' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    foreach([
        "CREATE TABLE IF NOT EXISTS tutor_budget (id INT PRIMARY KEY,reserved_cents INT NOT NULL DEFAULT 0)$engine",
        "CREATE TABLE IF NOT EXISTS tutor_members (user_id $fk PRIMARY KEY,created_at DATETIME NOT NULL,recharge_requested_at DATETIME NULL)$engine",
        "CREATE TABLE IF NOT EXISTS tutor_calls (id $pk,user_id $fk NOT NULL,request_key VARCHAR(64) NOT NULL,status VARCHAR(20) NOT NULL,reply $text NULL,input_tokens INT NULL,output_tokens INT NULL,cost_microusd INT NULL,created_at DATETIME NOT NULL,UNIQUE(user_id,request_key))$engine",
        "CREATE TABLE IF NOT EXISTS tutor_payments (id $pk,user_id $fk NOT NULL,reference VARCHAR(100) NOT NULL UNIQUE,amount_cents INT NOT NULL,seconds INT NOT NULL,status VARCHAR(24) NOT NULL,evidence VARCHAR(500) NOT NULL,created_by $fk NOT NULL,updated_by $fk NOT NULL,created_at DATETIME NOT NULL,updated_at DATETIME NOT NULL)$engine",
        "CREATE TABLE IF NOT EXISTS tutor_audit (id $pk,admin_id $fk NOT NULL,action VARCHAR(40) NOT NULL,details $text NOT NULL,created_at DATETIME NOT NULL)$engine"
    ] as $sql)db()->exec($sql);
    // Check actual columns: reruns preserve all values, errors must fail the migration.
    $columns=[
        ['tutor_payments','currency',"VARCHAR(3) NOT NULL DEFAULT 'USD'"],
        ['tutor_budget','price_ars_cents','INT NULL'],
        ['tutor_budget','budget_cents','INT NOT NULL DEFAULT 500'],
        ['tutor_budget','daily_limit','INT NOT NULL DEFAULT 5'],
        ['tutor_budget','total_limit','INT NOT NULL DEFAULT 25'],
        ['tutor_budget','pilot_open','INT NOT NULL DEFAULT 0'],
        ['tutor_budget','enroll_open','INT NOT NULL DEFAULT 1'],
        ['tutor_budget','max_students','INT NOT NULL DEFAULT 0'],
        ['tutor_budget','price_cents','INT NOT NULL DEFAULT 50'],
        ['tutor_budget','pack_seconds','INT NOT NULL DEFAULT 300'],
        ['tutor_members','daily_limit','INT NULL'],
        ['tutor_members','total_limit','INT NULL'],
        ['tutor_members','blocked','INT NOT NULL DEFAULT 0']
    ];
    foreach($columns as [$table,$column,$definition]) {
        $names=$sqlite?array_column(q_all("PRAGMA table_info($table)"),'name'):array_column(q_all("SHOW COLUMNS FROM $table"),'Field');
        if(!in_array($column,$names,true)) {
            try{db()->exec("ALTER TABLE $table ADD COLUMN $column $definition");}
            catch(PDOException $e){
                // Another replica may have added it while we waited for a DDL lock.
                $again=$sqlite?array_column(q_all("PRAGMA table_info($table)"),'name'):array_column(q_all("SHOW COLUMNS FROM $table"),'Field');
                if(!in_array($column,$again,true))throw $e;
            }
        }
    }
    $insert=$sqlite?'INSERT OR IGNORE':'INSERT IGNORE';
    q("$insert INTO tutor_budget (id,reserved_cents) VALUES (1,0)");
}
