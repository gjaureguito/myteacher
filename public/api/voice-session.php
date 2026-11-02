<?php
declare(strict_types=1);
require dirname(__DIR__, 2) . '/src/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

function voice_error(int $code, string $message): never {
    http_response_code($code);
    echo $message;
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') voice_error(405, 'Método no permitido');
$u = current_user();
if (!$u) voice_error(401, 'Iniciá sesión');
if (!has_full_access($u)) voice_error(403, 'Esta función requiere acceso completo');
if (env('VOICE_TEACHER_ENABLED', '0') !== '1') voice_error(503, 'Profesor de voz todavía no habilitado');
$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!is_string($csrf) || !hash_equals((string)($_SESSION['voice_csrf'] ?? ''), $csrf)) voice_error(403, 'Solicitud no autorizada');
$key = env('OPENAI_API_KEY');
if (!$key) voice_error(503, 'Falta configurar OpenAI en Railway');

// Protección básica de arranque por usuario/sesión. No sustituye cuotas de facturación.
$now = time();
$last = (int)($_SESSION['voice_last_start'] ?? 0);
if ($now - $last < 20) voice_error(429, 'Esperá unos segundos antes de reconectar');
$_SESSION['voice_last_start'] = $now;
session_write_close();

$sdp = file_get_contents('php://input', false, null, 0, 65537);
if (!$sdp || strlen($sdp) > 65536 || !str_contains($sdp, 'v=0')) voice_error(400, 'Oferta WebRTC inválida');

$model = env('OPENAI_VOICE_MODEL', 'gpt-realtime-2.1-mini');
if (!preg_match('/^[a-zA-Z0-9._-]+$/', $model)) voice_error(500, 'Modelo inválido');
$level = strtoupper((string)($_GET['level'] ?? 'A1'));
if (!in_array($level, ['A1','A2','B1','B2','C1'], true)) $level = 'A1';
$instructions = "You are the MyTeacher English tutor. The learner is at CEFR level {$level}. Speak primarily in English, with a clear, encouraging voice, at an appropriate pace. Have natural short turns, ask one question at a time, and encourage the learner to speak. Give concise corrections after the learner finishes, not at every minor mistake. If asked, explain briefly in Spanish, then return to English. Never claim to issue formal language certifications.";
$session = json_encode([
    'type' => 'realtime',
    'model' => $model,
    'instructions' => $instructions,
    'audio' => [
        'output' => ['voice' => 'marin'],
        'input' => ['turn_detection' => ['type' => 'server_vad']],
    ],
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

$ch = curl_init('https://api.openai.com/v1/realtime/calls');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key],
    CURLOPT_POSTFIELDS => [
        'sdp' => new CURLStringFile($sdp, 'offer.sdp', 'application/sdp'),
        'session' => $session
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 35,
]);
$result = curl_exec($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if (!is_string($result) || $status < 200 || $status >= 300) {
    error_log('MyTeacher OpenAI Realtime failed: HTTP ' . $status);
    voice_error(502, 'No se pudo iniciar la conversación. Verificá el modelo, la clave y los créditos.');
}
header('Content-Type: application/sdp');
echo $result;
