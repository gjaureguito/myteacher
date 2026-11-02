<?php
declare(strict_types=1);
require dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Cache-Control: no-store');
if (!is_post()) json_out(['error' => 'Método no permitido.'], 405);
$user = current_user();
if (!$user) json_out(['error' => 'Ingresá a tu cuenta.'], 401);
csrf_check();
if (!tutor_dialog_access($user)) json_out(['error' => 'El diálogo con el profesor IA todavía no está habilitado para tu cuenta.'], 403);

$raw = (string) file_get_contents('php://input', false, null, 0, 32769);
if (strlen($raw) > 32768) json_out(['error' => 'Solicitud demasiado larga.'], 413);
$in = json_decode($raw, true);
if (!is_array($in)) json_out(['error' => 'Solicitud inválida.'], 400);

$level = strtoupper((string) ($in['level'] ?? 'A2'));
if (!in_array($level, ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'], true)) $level = 'A2';
$scenario = (string) ($in['scenario'] ?? 'libre');
if (!isset(TUTOR_SCENARIOS[$scenario])) $scenario = 'libre';
$start = !empty($in['start']);
$message = trim((string) ($in['message'] ?? ''));
if (!$start && (mb_strlen($message) < 1 || mb_strlen($message) > 600)) json_out(['error' => 'Escribí o decí un mensaje de hasta 600 caracteres.'], 422);

$history = is_array($in['history'] ?? null) ? $in['history'] : [];
$history = array_slice(array_values(array_filter($history, static fn($m) => is_array($m)
    && in_array($m['role'] ?? '', ['user', 'assistant'], true) && is_string($m['content'] ?? null))), -12);

$key = env('GEMINI_API_KEY');
if (!$key) json_out(['error' => 'Falta configurar GEMINI_API_KEY en Railway.'], 503);

// Límite diario de protección de costos (por cuenta, día UTC).
$limit = max(1, min(1000, (int) env('TUTOR_FREE_DAILY_LIMIT', '150')));
$uid = (int) $user['id'];
$day = gmdate('Y-m-d');
db()->exec(db_is_sqlite()
    ? 'CREATE TABLE IF NOT EXISTS tutor_dialog_usage (user_id INTEGER NOT NULL, usage_day TEXT NOT NULL, used INTEGER NOT NULL DEFAULT 0, PRIMARY KEY(user_id,usage_day))'
    : 'CREATE TABLE IF NOT EXISTS tutor_dialog_usage (user_id BIGINT NOT NULL, usage_day DATE NOT NULL, used INT NOT NULL DEFAULT 0, PRIMARY KEY(user_id,usage_day))');
q((db_is_sqlite() ? 'INSERT OR IGNORE' : 'INSERT IGNORE') . ' INTO tutor_dialog_usage (user_id,usage_day,used) VALUES (?,?,0)', [$uid, $day]);
if (q('UPDATE tutor_dialog_usage SET used=used+1 WHERE user_id=? AND usage_day=? AND used<?', [$uid, $day, $limit])->rowCount() !== 1) {
    json_out(['error' => 'Llegaste al límite diario de ' . $limit . ' mensajes. Volvé mañana.'], 429);
}
$refund = static fn() => q('UPDATE tutor_dialog_usage SET used=CASE WHEN used>0 THEN used-1 ELSE 0 END WHERE user_id=? AND usage_day=?', [$uid, $day]);

$contents = [];
foreach ($history as $m) {
    $contents[] = ['role' => $m['role'] === 'assistant' ? 'model' : 'user', 'parts' => [['text' => mb_substr($m['content'], 0, 1200)]]];
}
$contents[] = ['role' => 'user', 'parts' => [['text' => $start
    ? '(The student has just joined. Start the role-play with a short greeting and a first question.)'
    : $message]]];
// Gemini requiere que la conversación empiece con un turno del usuario.
if ($contents[0]['role'] === 'model') array_unshift($contents, ['role' => 'user', 'parts' => [['text' => '(Start.)']]]);

$payload = [
    'systemInstruction' => ['parts' => [['text' => tutor_dialog_system($level, $scenario)]]],
    'contents' => $contents,
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 700,
        'responseMimeType' => 'application/json',
        'responseSchema' => [
            'type' => 'OBJECT',
            'properties' => [
                'reply' => ['type' => 'STRING'],
                'translation' => ['type' => 'STRING'],
                'correction' => ['type' => 'STRING'],
            ],
            'required' => ['reply', 'translation', 'correction'],
        ],
    ],
];
$call = static function (string $model) use ($payload, $key): array {
    return http_json('POST', rtrim((string) env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'), '/') . '/v1beta/models/' . rawurlencode($model) . ':generateContent',
        ['x-goog-api-key: ' . $key], $payload);
};
$model = env('GEMINI_MODEL', 'gemini-3.5-flash-lite');
if (!preg_match('/^[a-zA-Z0-9._-]+$/', $model)) $model = 'gemini-3.5-flash-lite';
$r = $call($model);
if ($r['code'] === 404 && $model !== 'gemini-3.5-flash-lite') $r = $call('gemini-3.5-flash-lite');

if ($r['code'] !== 200) {
    $refund();
    error_log('Profesor diálogo Gemini HTTP ' . $r['code'] . ' ' . substr((string) $r['raw'], 0, 300));
    $hint = match (true) {
        $r['code'] === 429 => 'Gemini alcanzó su límite de uso por ahora. Probá en unos minutos.',
        $r['code'] === 401 || $r['code'] === 403 => 'Gemini rechazó la clave. Revisá GEMINI_API_KEY.',
        $r['code'] === 0 => 'No se pudo conectar con Gemini.',
        default => 'Gemini no respondió (HTTP ' . $r['code'] . ').',
    };
    json_out(['error' => $hint . ' No se descontó el mensaje.'], 503);
}
$text = '';
foreach ($r['data']['candidates'][0]['content']['parts'] ?? [] as $p) $text .= (string) ($p['text'] ?? '');
$out = json_decode(trim($text), true);
if (!is_array($out)) $out = ['reply' => trim($text), 'translation' => '', 'correction' => ''];
$reply = trim((string) ($out['reply'] ?? ''));
if ($reply === '') {
    $refund();
    json_out(['error' => 'El profesor no devolvió respuesta. No se descontó el mensaje.'], 503);
}
$used = (int) q_val('SELECT used FROM tutor_dialog_usage WHERE user_id=? AND usage_day=?', [$uid, $day]);
json_out([
    'reply' => mb_substr($reply, 0, 1200),
    'translation' => mb_substr(trim((string) ($out['translation'] ?? '')), 0, 1500),
    'correction' => mb_substr(trim((string) ($out['correction'] ?? '')), 0, 1500),
    'remaining' => max(0, $limit - $used),
]);
