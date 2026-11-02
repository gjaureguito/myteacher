<?php
declare(strict_types=1);
require dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Cache-Control: no-store');
if (!is_post()) json_out(['error' => 'Método no permitido.'], 405);
$user = current_user();
if (!$user) json_out(['error' => 'Ingresá a tu cuenta.'], 401);
csrf_check();
if (!tutor_dialog_access($user)) json_out(['error' => 'El diálogo con el profesor IA todavía no está habilitado para tu cuenta.'], 403);

$raw = (string) file_get_contents('php://input', false, null, 0, 40001);
if (strlen($raw) > 40000) json_out(['error' => 'Solicitud demasiado larga.'], 413);
$in = json_decode($raw, true);
if (!is_array($in)) json_out(['error' => 'Solicitud inválida.'], 400);

$level = strtoupper((string) ($in['level'] ?? 'A2'));
if (!in_array($level, ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'], true)) $level = 'A2';
$scenario = (string) ($in['scenario'] ?? 'chat');
if (!isset(TUTOR_SCENARIOS[$scenario])) $scenario = 'chat';
$start = !empty($in['start']);
$message = trim((string) ($in['message'] ?? ''));
if (!$start && (mb_strlen($message) < 1 || mb_strlen($message) > 1500)) json_out(['error' => 'Escribí o decí un mensaje de hasta 1500 caracteres.'], 422);

$history = is_array($in['history'] ?? null) ? $in['history'] : [];
$history = array_slice(array_values(array_filter($history, static fn($m) => is_array($m)
    && in_array($m['role'] ?? '', ['user', 'assistant'], true) && is_string($m['content'] ?? null) && trim($m['content']) !== '')), -16);
$history = array_map(static fn($m) => ['role' => $m['role'], 'content' => mb_substr($m['content'], 0, 2000)], $history);
$turn = $start
    ? ($scenario === 'chat'
        ? '(The student just opened the chat. Greet them briefly in English and offer help: practice conversation or ask any question about English.)'
        : '(The student has just joined. Start the role-play with a short greeting and a first question.)')
    : $message;

$openaiKey = (string) env('OPENAI_API_KEY', '');
if ($openaiKey === '') json_out(['error' => 'Falta configurar OPENAI_API_KEY en Railway.'], 503);

// Límite diario de protección de costos (por cuenta, día UTC).
$limit = max(1, min(2000, (int) env('TUTOR_FREE_DAILY_LIMIT', '150')));
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
$remaining = static fn(): int => max(0, $limit - (int) q_val('SELECT used FROM tutor_dialog_usage WHERE user_id=? AND usage_day=?', [$uid, $day]));
$system = tutor_dialog_system($level, $scenario);
session_write_close();

/* ---------- OpenAI: respuesta en streaming (aparece mientras se escribe) ---------- */
    $model = (string) env('OPENAI_MODEL', TUTOR_MODEL);
    if (!preg_match('/^[a-zA-Z0-9._:-]+$/', $model)) $model = TUTOR_MODEL;
    // Usar un modelo que el proyecto de la clave tenga habilitado.
    $pref = $model;
    $known = openai_working('chat');
    if ($known && ($known['pref'] ?? '') !== $pref) $known = null; // cambiaron OPENAI_MODEL: volver a elegir
    $model = $known ? (string) $known['model'] : (openai_pick_model('chat', $model) ?? $model);
    $input = $history;
    $input[] = ['role' => 'user', 'content' => $turn];
    $body = ['model' => $model, 'instructions' => $system, 'input' => $input, 'stream' => true, 'store' => false,
        'max_output_tokens' => 700, 'reasoning' => ['effort' => 'none']];
    if ($known && $known['model'] === $model && empty($known['reasoning'])) unset($body['reasoning']);

    $buffer = ''; $full = ''; $errorBody = ''; $started = false; $usage = null; $streamError = '';
    $emit = static function (array $event): void { echo 'data: ' . json_encode($event, JSON_UNESCAPED_UNICODE) . "\n\n"; @ob_flush(); flush(); };
    $begin = static function () use (&$started): void {
        if ($started) return;
        $started = true;
        if (function_exists('apache_setenv')) @apache_setenv('no-gzip', '1');
        @ini_set('zlib.output_compression', '0');
        while (ob_get_level() > 0) ob_end_flush();
        header('Content-Type: text/event-stream; charset=utf-8');
        header('X-Accel-Buffering: no');
    };
    $tried = [];
    for ($attempt = 0; $attempt < 4; $attempt++) {
        $tried[] = $body['model'];
        $errorBody = '';
        $ch = curl_init(rtrim((string) env('OPENAI_BASE_URL', 'https://api.openai.com'), '/') . '/v1/responses');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $openaiKey],
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_WRITEFUNCTION => function ($ch, string $chunk) use (&$buffer, &$full, &$errorBody, &$usage, &$streamError, $begin, $emit): int {
                if ((int) curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) { $errorBody .= $chunk; return strlen($chunk); }
                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n\n")) !== false) {
                    $block = substr($buffer, 0, $pos); $buffer = substr($buffer, $pos + 2);
                    foreach (explode("\n", $block) as $line) {
                        if (!str_starts_with($line, 'data:')) continue;
                        $ev = json_decode(trim(substr($line, 5)), true);
                        if (!is_array($ev)) continue;
                        $type = $ev['type'] ?? '';
                        if ($type === 'response.output_text.delta' && isset($ev['delta'])) {
                            $full .= $ev['delta']; $begin(); $emit(['delta' => $ev['delta']]);
                        } elseif ($type === 'response.completed') {
                            $usage = $ev['response']['usage'] ?? null;
                        } elseif ($type === 'error' || $type === 'response.failed') {
                            $streamError = (string) ($ev['message'] ?? $ev['response']['error']['message'] ?? 'error');
                        }
                    }
                }
                return strlen($chunk);
            },
        ]);
        curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        if ($started || $status === 200) break;
        $err = json_decode($errorBody, true)['error'] ?? [];
        $msg = strtolower((string) ($err['message'] ?? ''));
        if (($err['code'] ?? '') === 'model_not_found' || str_contains($msg, 'does not have access to model') || str_contains($msg, 'does not exist')) {
            $other = openai_pick_model('chat', '__ninguno__', $attempt === 0, $tried);
            if (!$other) break;
            error_log('Profesor diálogo: ' . $body['model'] . ' no habilitado, uso ' . $other);
            $body['model'] = $other;
        } elseif (isset($body['reasoning']) && ($status === 400) && (str_contains($msg, 'reasoning') || str_starts_with((string) ($err['param'] ?? ''), 'reasoning'))) {
            unset($body['reasoning']);
        } else {
            break;
        }
    }

    if (!$started) {
        // No llegó nada: se puede responder un error común en JSON.
        $refund();
        error_log('Profesor diálogo OpenAI HTTP ' . $status . ' ' . substr($errorBody . $curlErr . $streamError, 0, 400));
        $code = (string) (json_decode($errorBody, true)['error']['code'] ?? '');
        $hint = match (true) {
            $code === 'model_not_found' => 'Tu proyecto de OpenAI no tiene habilitado ningún modelo de texto. Habilitá modelos en platform.openai.com → Settings → Project → Limits.',
            $status === 401 => 'OpenAI rechazó la clave. Revisá OPENAI_API_KEY en Railway.',
            $status === 429 && $code === 'insufficient_quota' => 'La cuenta de OpenAI no tiene saldo de API. Cargá créditos en platform.openai.com.',
            $status === 429 => 'OpenAI está limitando las solicitudes. Probá en unos segundos.',
            $status === 404 || $status === 400 => 'OpenAI rechazó el modelo o la solicitud. Revisá OPENAI_MODEL.',
            $status === 0 => 'No se pudo conectar con OpenAI.',
            default => 'OpenAI no respondió (HTTP ' . $status . ').',
        };
        json_out(['error' => $hint . ' No se descontó el mensaje.'], 503);
    }
    openai_working('chat', ['pref' => $pref, 'model' => $body['model'], 'reasoning' => isset($body['reasoning'])]);
    $parsed = tutor_dialog_parse($full);
    if (is_array($usage)) error_log('Profesor diálogo tokens in=' . ($usage['input_tokens'] ?? '?') . ' out=' . ($usage['output_tokens'] ?? '?'));
    $emit(['done' => true] + $parsed + ['remaining' => $remaining(), 'incomplete' => $streamError !== '']);
    exit;
