<?php
declare(strict_types=1);
require dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Cache-Control: no-store');
if (!is_post()) json_out(['error' => 'Método no permitido.'], 405);
$user = current_user();
if (!$user) json_out(['error' => 'Ingresá a tu cuenta.'], 401);
csrf_check();
if (!tutor_dialog_access($user)) json_out(['error' => 'Las clases con el profesor IA todavía no están habilitadas para tu cuenta.'], 403);

$raw = (string) file_get_contents('php://input', false, null, 0, 8193);
$in = json_decode($raw, true);
if (!is_array($in)) json_out(['error' => 'Solicitud inválida.'], 400);
$uid = (int) $user['id'];
$action = (string) ($in['action'] ?? '');
tutor_classes_schema();

$loadClass = static function () use ($in, $uid): array {
    $row = q_one('SELECT * FROM tutor_classes WHERE id=? AND user_id=?', [(int) ($in['class_id'] ?? 0), $uid]);
    if (!$row) json_out(['error' => 'No encontramos esa clase.'], 404);
    $row['transcript'] = json_decode((string) $row['transcript'], true) ?: [];
    $row['feedback'] = $row['feedback'] ? json_decode((string) $row['feedback'], true) : null;
    return $row;
};
$publicClass = static function (array $row): array {
    $l = tutor_lesson($row['lesson_key']);
    return ['id' => (int) $row['id'], 'lesson' => $row['lesson_key'], 'title' => $l['title'] ?? $row['lesson_key'], 'goal' => $l['goal'] ?? '',
        'targets' => $l['targets'] ?? [], 'level' => $row['level'], 'number' => $l['number'] ?? 0, 'turns' => $l['turns'] ?? 0,
        'turns_done' => (int) $row['turns_done'], 'status' => $row['status'], 'score' => $row['score'] === null ? null : (int) $row['score'],
        'started_at' => $row['started_at'], 'finished_at' => $row['finished_at'], 'transcript' => $row['transcript'], 'feedback' => $row['feedback']];
};

/* ---------- Lecturas: plan de clases, historial, una clase ---------- */
if ($action === 'catalog') {
    $progress = tutor_class_progress($uid);
    $last = q_one('SELECT level FROM tutor_classes WHERE user_id=? ORDER BY id DESC LIMIT 1', [$uid]);
    $level = $last['level'] ?? 'A1';
    $levels = [];
    foreach (TUTOR_LEVELS as $code => $label) {
        $levels[] = ['code' => $code, 'label' => $label, 'lessons' => array_map(static fn($l) => [
            'key' => $l[0], 'title' => $l[1], 'goal' => $l[2], 'targets' => $l[4], 'turns' => TUTOR_LEVEL_TURNS[$code], 'best' => $progress[$l[0]]['best'] ?? null, 'times' => $progress[$l[0]]['times'] ?? 0,
        ], TUTOR_LESSONS[$code])];
    }
    json_out(['levels' => $levels, 'current_level' => $level, 'next' => tutor_next_lesson($progress, $level)]);
}
if ($action === 'history') {
    $rows = q_all("SELECT * FROM tutor_classes WHERE user_id=? AND status='completed' ORDER BY id DESC LIMIT 100", [$uid]);
    json_out(['classes' => array_map(static function ($r) {
        $l = tutor_lesson($r['lesson_key']);
        return ['id' => (int) $r['id'], 'title' => $l['title'] ?? $r['lesson_key'], 'level' => $r['level'], 'number' => $l['number'] ?? 0,
            'score' => $r['score'] === null ? null : (int) $r['score'], 'finished_at' => $r['finished_at']];
    }, $rows)]);
}
if ($action === 'class') json_out(['class' => $publicClass($loadClass())]);

/* ---------- Acciones que usan la IA: límite diario por cuenta ---------- */
if (!in_array($action, ['start', 'reply', 'finish'], true)) json_out(['error' => 'Acción inválida.'], 400);
if (!env('OPENAI_API_KEY')) json_out(['error' => 'Falta configurar OPENAI_API_KEY en Railway.'], 503);
$limit = max(1, min(2000, (int) env('TUTOR_FREE_DAILY_LIMIT', '150')));
$day = gmdate('Y-m-d');
q((db_is_sqlite() ? 'INSERT OR IGNORE' : 'INSERT IGNORE') . ' INTO tutor_dialog_usage (user_id,usage_day,used) VALUES (?,?,0)', [$uid, $day]);
if (q('UPDATE tutor_dialog_usage SET used=used+1 WHERE user_id=? AND usage_day=? AND used<?', [$uid, $day, $limit])->rowCount() !== 1) {
    json_out(['error' => 'Llegaste al límite diario de ' . $limit . ' mensajes. Volvé mañana.'], 429);
}
$refund = static fn() => q('UPDATE tutor_dialog_usage SET used=CASE WHEN used>0 THEN used-1 ELSE 0 END WHERE user_id=? AND usage_day=?', [$uid, $day]);

/* ---------- Cerrar la clase y generar la devolución ---------- */
if ($action === 'finish') {
    $row = $loadClass();
    if ($row['status'] === 'completed') { $refund(); json_out(['class' => $publicClass($row)]); }
    $lesson = tutor_lesson($row['lesson_key']);
    if (!$lesson) json_out(['error' => 'Clase desconocida.'], 404);
    if ((int) $row['turns_done'] < 1) {
        $refund();
        q('DELETE FROM tutor_classes WHERE id=? AND user_id=?', [$row['id'], $uid]);
        json_out(['discarded' => true]);
    }
    session_write_close();
    $fb = tutor_class_feedback($lesson, $row['transcript']);
    if (!$fb) { $refund(); json_out(['error' => 'No se pudo generar la devolución. Tocá "Reintentar".'], 503); }
    if ($row['status'] !== 'ended') $fb['incompleta'] = true; // el alumno cortó antes del final
    q("UPDATE tutor_classes SET status='completed', feedback=?, score=?, finished_at=? WHERE id=? AND user_id=?",
        [json_encode($fb, JSON_UNESCAPED_UNICODE), $fb['score'], now_utc(), $row['id'], $uid]);
    json_out(['class' => $publicClass($loadClass())]);
}

/* ---------- Empezar o continuar la clase (respuesta en streaming) ---------- */
if ($action === 'start') {
    $lesson = tutor_lesson((string) ($in['lesson'] ?? ''));
    if (!$lesson) { $refund(); json_out(['error' => 'Clase desconocida.'], 404); }
    // Clases empezadas y nunca respondidas no se acumulan.
    q("DELETE FROM tutor_classes WHERE user_id=? AND status='active' AND turns_done=0", [$uid]);
    q("INSERT INTO tutor_classes (user_id,lesson_key,level,status,turns_done,transcript,started_at) VALUES (?,?,?,'active',0,'[]',?)",
        [$uid, $lesson['key'], $lesson['level'], now_utc()]);
    $row = ['id' => (int) db()->lastInsertId(), 'transcript' => [], 'turns_done' => 0, 'status' => 'active', 'lesson_key' => $lesson['key']];
    $exchange = 0;
} else {
    $row = $loadClass();
    $lesson = tutor_lesson($row['lesson_key']);
    if ($row['status'] !== 'active' || !$lesson) { $refund(); json_out(['error' => 'Esta clase ya terminó.'], 409); }
    $message = trim((string) ($in['message'] ?? ''));
    if (mb_strlen($message) < 1 || mb_strlen($message) > 800) { $refund(); json_out(['error' => 'Escribí o decí una respuesta de hasta 800 caracteres.'], 422); }
    $row['transcript'][] = ['role' => 'student', 'text' => $message];
    $exchange = (int) $row['turns_done'] + 1;
}
session_write_close();

$started = false;
$emit = static function (array $event): void { echo 'data: ' . json_encode($event, JSON_UNESCAPED_UNICODE) . "\n\n"; @ob_flush(); flush(); };
$begin = static function () use (&$started, $emit, $row): void {
    if ($started) return;
    $started = true;
    if (function_exists('apache_setenv')) @apache_setenv('no-gzip', '1');
    @ini_set('zlib.output_compression', '0');
    while (ob_get_level() > 0) ob_end_flush();
    header('Content-Type: text/event-stream; charset=utf-8');
    header('X-Accel-Buffering: no');
    $emit(['class_id' => $row['id']]);
};
$r = openai_respond([
    'instructions' => tutor_class_system($lesson, $exchange),
    'input' => tutor_class_input($row['transcript']),
    'max_output_tokens' => 600,
], static function (string $delta) use ($begin, $emit): void { $begin(); $emit(['delta' => $delta]); });

if (!$r['ok'] && !$started) {
    $refund();
    if ($action === 'start') q('DELETE FROM tutor_classes WHERE id=? AND user_id=?', [$row['id'], $uid]);
    json_out(['error' => openai_error_message($r) . ' No se descontó el mensaje.'], 503);
}
$parsed = tutor_dialog_parse($r['text']);
$row['transcript'][] = ['role' => 'teacher', 'text' => $parsed['reply'], 'translation' => $parsed['translation'], 'correction' => $parsed['correction']];
$ended = $exchange >= $lesson['turns'];
q('UPDATE tutor_classes SET transcript=?, turns_done=?, status=? WHERE id=? AND user_id=?',
    [json_encode($row['transcript'], JSON_UNESCAPED_UNICODE), $exchange, $ended ? 'ended' : 'active', $row['id'], $uid]);
$begin();
$emit(['done' => true] + $parsed + ['exchange' => $exchange, 'turns' => $lesson['turns'], 'ended' => $ended]);
exit;
