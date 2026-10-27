<?php
declare(strict_types=1);
/*
 * Transcribe la voz del alumno con Gemini.
 * Recibe un WAV mono de 16 kHz grabado por /assets/voice.js (máx. ~15 s) y devuelve {"text": "..."}.
 * Requiere sesión iniciada y CSRF. Límite diario por alumno: STT_DAILY_LIMIT (por defecto 300).
 */
require dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Cache-Control: no-store');

if (!is_post()) json_out(['error' => 'Método no permitido'], 405);
$u = current_user();
if (!$u) json_out(['error' => 'Iniciá sesión para practicar con tu voz.', 'code' => 'login'], 401);
csrf_check();

$key = (string) env('GEMINI_API_KEY', '');
if ($key === '') json_out(['error' => 'La transcripción de voz no está configurada.', 'code' => 'not_configured'], 503);

$file = $_FILES['audio'] ?? null;
if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    json_out(['error' => 'No llegó el audio.', 'code' => 'no_audio'], 422);
}
$size = (int) $file['size'];
if ($size < 1000 || $size > 600000) json_out(['error' => 'El audio es demasiado corto o demasiado largo.', 'code' => 'bad_size'], 422);
$audio = (string) file_get_contents($file['tmp_name']);

// Solo aceptamos el WAV que arma voice.js: PCM 16 bits, mono, 8–48 kHz.
$hdr = unpack('a4riff/Vsize/a4wave/a4fmt/Vfmtlen/vformat/vchannels/Vrate/Vbyterate/vblock/vbits', substr($audio, 0, 36));
if (!$hdr || $hdr['riff'] !== 'RIFF' || $hdr['wave'] !== 'WAVE' || $hdr['format'] !== 1 || $hdr['channels'] !== 1
    || $hdr['bits'] !== 16 || $hdr['rate'] < 8000 || $hdr['rate'] > 48000) {
    json_out(['error' => 'Formato de audio no válido.', 'code' => 'bad_format'], 415);
}
$seconds = ($size - 44) / ($hdr['rate'] * 2);
if ($seconds > 16) json_out(['error' => 'El audio es demasiado largo.', 'code' => 'bad_size'], 422);

$lang = ($_POST['lang'] ?? 'en-US') === 'es-AR' ? 'es-AR' : 'en-US';

// Límite diario por alumno (protege el costo de la API).
$limit = max(10, min(2000, (int) env('STT_DAILY_LIMIT', '300')));
try {
    $pdo = db();
    $pdo->exec(db_is_sqlite()
        ? 'CREATE TABLE IF NOT EXISTS stt_usage (user_id INTEGER NOT NULL, usage_day TEXT NOT NULL, used INTEGER NOT NULL DEFAULT 0, PRIMARY KEY(user_id,usage_day))'
        : 'CREATE TABLE IF NOT EXISTS stt_usage (user_id BIGINT NOT NULL, usage_day DATE NOT NULL, used INT NOT NULL DEFAULT 0, PRIMARY KEY(user_id,usage_day))');
    $day = gmdate('Y-m-d');
    q((db_is_sqlite() ? 'INSERT OR IGNORE' : 'INSERT IGNORE') . ' INTO stt_usage (user_id,usage_day,used) VALUES (?,?,0)', [(int) $u['id'], $day]);
    if (!is_admin($u) && !q('UPDATE stt_usage SET used=used+1 WHERE user_id=? AND usage_day=? AND used<?', [(int) $u['id'], $day, $limit])->rowCount()) {
        json_out(['error' => 'Llegaste al límite de prácticas con voz por hoy.', 'code' => 'quota'], 429);
    }
} catch (Throwable $e) {
    error_log('STT quota: ' . $e->getMessage()); // si falla el contador, no bloqueamos al alumno
}

$prompt = $lang === 'es-AR'
    ? 'Transcribí exactamente lo que dice la persona en este audio, en el idioma en que habla. No corrijas errores ni completes palabras. Si no se entiende nada, respondé solo: [inaudible]. Devolvé únicamente la transcripción, sin comillas ni comentarios.'
    : 'Transcribe exactly what the speaker says in this audio. The speaker is a Spanish-speaking learner practicing English. Write the words as they were actually pronounced: do not fix grammar, do not complete missing words and do not guess a more correct sentence. If nothing is intelligible, answer only: [inaudible]. Return only the transcript, without quotes or comments.';

$payload = json_encode([
    'contents' => [[
        'role' => 'user',
        'parts' => [
            ['inline_data' => ['mime_type' => 'audio/wav', 'data' => base64_encode($audio)]],
            ['text' => $prompt],
        ],
    ]],
    'generationConfig' => ['temperature' => 0, 'maxOutputTokens' => 200],
], JSON_UNESCAPED_UNICODE);

$base = rtrim((string) env('GEMINI_API_BASE', 'https://generativelanguage.googleapis.com/v1beta'), '/');
$call = static function (string $model) use ($base, $key, $payload): array {
    $ch = curl_init($base . '/models/' . rawurlencode($model) . ':generateContent');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, is_string($raw) ? (json_decode($raw, true) ?: []) : [], is_string($raw) ? $raw : ''];
};

$fallback = 'gemini-3.5-flash-lite';
$model = (string) (env('STT_MODEL') ?: env('GEMINI_MODEL', $fallback));
[$status, $data, $raw] = $call($model);
if (($status === 404 || $status === 400) && $model !== $fallback) [$status, $data, $raw] = $call($fallback);
if ($status === 429 || $status >= 500) { usleep(600000); [$status, $data, $raw] = $call($model); }

if ($status !== 200) {
    error_log('STT Gemini HTTP ' . $status . ': ' . substr(preg_replace('/\s+/', ' ', $raw) ?? '', 0, 500));
    // 401/402/403: la cuenta de Gemini no está habilitada (clave inválida, sin crédito o sin facturación).
    // Respondemos "unavailable" para que el navegador pase solo a su propio reconocimiento de voz.
    if (in_array($status, [401, 402, 403], true)) json_out(['error' => 'El servicio de transcripción no está disponible.', 'code' => 'unavailable'], 503);
    json_out(['error' => 'No pudimos procesar tu voz en este momento.', 'code' => 'upstream'], 502);
}

$text = '';
foreach ($data['candidates'][0]['content']['parts'] ?? [] as $part) $text .= (string) ($part['text'] ?? '');
$text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
$text = trim($text, " \"'“”«»");
if ($text === '' || stripos($text, '[inaudible]') !== false) $text = '';

json_out(['text' => mb_substr($text, 0, 300), 'seconds' => round($seconds, 1)]);
