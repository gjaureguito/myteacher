<?php
declare(strict_types=1);

/*
 * Webhook de WhatsApp Cloud API.
 * Configurá la URL pública https://TU-DOMINIO/webhook/whatsapp.php en Meta.
 */
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/whatsapp_sales.php';

function whatsapp_webhook_text(string $body): never
{
    header('Content-Type: text/plain; charset=utf-8');
    echo $body;
    exit;
}

$verifyToken = (string) env('META_WHATSAPP_VERIFY_TOKEN', '');
$appSecret = (string) env('META_APP_SECRET', '');
$accessToken = (string) env('META_WHATSAPP_ACCESS_TOKEN', '');
$phoneNumberId = (string) env('META_WHATSAPP_PHONE_NUMBER_ID', '');
$apiVersion = (string) env('META_GRAPH_API_VERSION', 'v27.0');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $mode = (string) ($_GET['hub_mode'] ?? $_GET['hub.mode'] ?? '');
    $token = (string) ($_GET['hub_verify_token'] ?? $_GET['hub.verify_token'] ?? '');
    $challenge = (string) ($_GET['hub_challenge'] ?? $_GET['hub.challenge'] ?? '');

    if ($mode === 'subscribe' && $verifyToken !== '' && hash_equals($verifyToken, $token)) {
        whatsapp_webhook_text($challenge);
    }

    http_response_code(403);
    whatsapp_webhook_text('forbidden');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: GET, POST');
    http_response_code(405);
    whatsapp_webhook_text('method not allowed');
}

if ($verifyToken === '' || $appSecret === '' || $accessToken === '' || $phoneNumberId === '') {
    http_response_code(503);
    whatsapp_webhook_text('WhatsApp agent is not configured');
}

$raw = (string) file_get_contents('php://input');
$signature = (string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '');
$expected = 'sha256=' . hash_hmac('sha256', $raw, $appSecret);
if ($signature === '' || !hash_equals($expected, $signature)) {
    http_response_code(401);
    whatsapp_webhook_text('invalid signature');
}

$payload = json_decode($raw, true);
if (!is_array($payload) || ($payload['object'] ?? '') !== 'whatsapp_business_account') {
    http_response_code(200);
    whatsapp_webhook_text('ignored');
}

$messages = [];
foreach (($payload['entry'] ?? []) as $entry) {
    foreach (($entry['changes'] ?? []) as $change) {
        if (($change['field'] ?? '') !== 'messages') continue;
        foreach (($change['value']['messages'] ?? []) as $message) {
            if (($message['type'] ?? '') !== 'text') continue;
            $from = (string) ($message['from'] ?? '');
            $body = trim((string) ($message['text']['body'] ?? ''));
            if ($from !== '' && $body !== '') $messages[] = [$from, mb_substr($body, 0, 2000, 'UTF-8'), (string) ($message['id'] ?? '')];
        }
    }
}

foreach ($messages as [$recipient, $incomingText, $incomingId]) {
    // Guardamos el mensaje entrante antes de responder; el WAMID evita duplicados de Meta.
    $threadId = whatsapp_store_message($recipient, $incomingText, $incomingId, 'in');
    if (!$threadId) continue;

    $reply = whatsapp_sales_reply($incomingText);
    $inboundCount = (int) q_val("SELECT COUNT(*) FROM whatsapp_messages WHERE thread_id=? AND direction='in'", [$threadId]);
    if ($inboundCount === 1) {
        $reply = "Aviso: para atenderte, MyTeacher guarda tu número y el texto de este chat en su base de datos. Solo el equipo administrador puede consultarlo. Detalles: " . app_url('privacy.php') . " No envíes contraseñas, códigos ni datos de tarjeta.\n\n" . $reply;
    }
    $request = [
        'messaging_product' => 'whatsapp',
        'recipient_type' => 'individual',
        'to' => $recipient,
        'type' => 'text',
        'text' => ['preview_url' => false, 'body' => mb_substr($reply, 0, 3500, 'UTF-8')],
    ];
    $ch = curl_init("https://graph.facebook.com/{$apiVersion}/" . rawurlencode($phoneNumberId) . '/messages');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($request, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        // Registrar solo el error estructurado de Meta; nunca el texto del chat ni credenciales.
        $decodedResponse = is_string($response) ? json_decode($response, true) : null;
        $apiError = is_array($decodedResponse) && is_array($decodedResponse['error'] ?? null)
            ? $decodedResponse['error'] : [];
        $apiCode = isset($apiError['code']) ? (string) $apiError['code'] : 'n/a';
        $apiSubcode = isset($apiError['error_subcode']) ? (string) $apiError['error_subcode'] : 'n/a';
        $apiType = preg_replace('/[^A-Za-z0-9_ -]/', '', (string) ($apiError['type'] ?? 'n/a')) ?? 'n/a';
        $apiMessage = trim((string) ($apiError['message'] ?? ''));
        if ($apiMessage !== '') {
            $apiMessage = str_replace($recipient, '[recipient]', $apiMessage);
            $apiMessage = preg_replace('/\\bEAA[A-Za-z0-9_-]{20,}\\b/', '[redacted-token]', $apiMessage) ?? '[unavailable]';
            $apiMessage = preg_replace('/\\b\\d{8,15}\\b/', '[number]', $apiMessage) ?? '[unavailable]';
            $apiMessage = mb_substr($apiMessage, 0, 240, 'UTF-8');
        } else {
            $apiMessage = $curlError !== '' ? 'transport error' : 'no Meta error message';
        }
        $trace = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($apiError['fbtrace_id'] ?? '')) ?? '';
        error_log('MyTeacher WhatsApp send failed; HTTP ' . $httpCode
            . '; Meta code ' . $apiCode
            . '; subcode ' . $apiSubcode
            . '; type ' . $apiType
            . '; message ' . $apiMessage
            . ($trace !== '' ? '; trace ' . $trace : '')
            . ($curlError !== '' ? '; transport error' : ''));
    } else {
        $sent = json_decode((string) $response, true);
        $outgoingId = (string) ($sent['messages'][0]['id'] ?? '');
        whatsapp_store_message($recipient, $reply, $outgoingId, 'out');
    }
}

http_response_code(200);
whatsapp_webhook_text('ok');
