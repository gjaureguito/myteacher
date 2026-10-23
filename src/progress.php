<?php
declare(strict_types=1);

/** Progreso por unidad: [unit_id => ['total'=>n, 'solved'=>n]] */
function unit_progress(int $userId, ?string $level = null): array
{
    $sql = 'SELECT u.id, COUNT(e.id) AS total, COALESCE(SUM(s.solved), 0) AS solved
            FROM units u
            JOIN exercises e ON e.unit_id = u.id
            LEFT JOIN exercise_status s ON s.exercise_id = e.id AND s.user_id = ?
            WHERE u.is_published = 1' . ($level ? ' AND u.level_code = ?' : '') . '
            GROUP BY u.id';
    $rows = q_all($sql, $level ? [$userId, $level] : [$userId]);
    $out = [];
    foreach ($rows as $r) $out[(int) $r['id']] = ['total' => (int) $r['total'], 'solved' => (int) $r['solved']];
    return $out;
}

function level_progress(int $userId): array
{
    $rows = q_all('SELECT u.level_code, COUNT(e.id) AS total, COALESCE(SUM(s.solved), 0) AS solved
                   FROM units u
                   JOIN exercises e ON e.unit_id = u.id
                   LEFT JOIN exercise_status s ON s.exercise_id = e.id AND s.user_id = ?
                   WHERE u.is_published = 1
                   GROUP BY u.level_code', [$userId]);
    $out = [];
    foreach ($rows as $r) $out[$r['level_code']] = ['total' => (int) $r['total'], 'solved' => (int) $r['solved']];
    return $out;
}

function pct(array $p): int
{
    return $p['total'] ? (int) round($p['solved'] * 100 / $p['total']) : 0;
}

/** Última unidad en la que practicó el usuario. */
function last_unit(int $userId): ?array
{
    return q_one('SELECT u.* FROM attempts a
                  JOIN exercises e ON e.id = a.exercise_id
                  JOIN units u ON u.id = e.unit_id
                  WHERE a.user_id = ?
                  ORDER BY a.id DESC LIMIT 1', [$userId]);
}

function streak_days(int $userId): int
{
    $days = q_all('SELECT DISTINCT substr(created_at, 1, 10) AS d FROM attempts WHERE user_id = ? ORDER BY d DESC LIMIT 60', [$userId]);
    $set = array_column($days, 'd');
    $n = 0;
    $t = time();
    // Si hoy todavía no practicó, la racha cuenta desde ayer
    if (!in_array(gmdate('Y-m-d', $t), $set, true)) $t -= 86400;
    while (in_array(gmdate('Y-m-d', $t), $set, true)) { $n++; $t -= 86400; }
    return $n;
}

function level_colors(): array
{
    return ['a1' => '#1F9D6B', 'a2' => '#13897E', 'b1' => '#2563C9', 'b2' => '#3B47C9', 'c1' => '#6D3BC4', 'c2' => '#A42D72'];
}
