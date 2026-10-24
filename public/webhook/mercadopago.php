<?php
/*
 * Webhook de Mercado Pago. Configurar en "Tus integraciones → Webhooks" con la URL
 *   https://TU-DOMINIO/webhook/mercadopago.php
 * y los eventos "Pagos" (pagos únicos) y "Planes y suscripciones" (subscription_preapproval y subscription_authorized_payment).
 */
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require ROOT . '/src/mercadopago.php';

$body = json_decode((string) file_get_contents('php://input'), true) ?: [];
$topic = (string) ($body['type'] ?? $_GET['type'] ?? $_GET['topic'] ?? '');
$dataId = (string) ($body['data']['id'] ?? $_GET['data_id'] ?? $_GET['data.id'] ?? $_GET['id'] ?? '');

if ($dataId === '' || !preg_match('/^[A-Za-z0-9_-]{1,80}$/', $dataId)) {
    http_response_code(200); // respondemos 200 para que MP no reintente basura
    exit('ignored');
}

if (!mp_valid_signature($dataId)) {
    log_line("Webhook MP con firma inválida ($topic $dataId)");
    http_response_code(401);
    exit('bad signature');
}

$result = null;
try {
    if ($topic === 'subscription_preapproval' || $topic === 'preapproval') {
        $result = mp_sync_preapproval($dataId);
    } elseif ($topic === 'subscription_authorized_payment' || $topic === 'authorized_payment') {
        $ap = mp_request('GET', '/authorized_payments/' . rawurlencode($dataId));
        $pid = $ap['data']['preapproval_id'] ?? null;
        if ($pid) $result = mp_sync_preapproval((string) $pid);
    } elseif ($topic === 'payment') {
        $result = mp_sync_payment($dataId);
    }
} catch (Throwable $e) {
    log_line('Webhook MP error: ' . $e->getMessage());
    http_response_code(500); // MP reintenta
    exit('error');
}

q('INSERT INTO payments_log (user_id, topic, resource_id, status, payload, created_at) VALUES (?,?,?,?,?,?)', [
    $result['user_id'] ?? null, $topic ?: 'unknown', $dataId, $result['status'] ?? null,
    substr(json_encode($body, JSON_UNESCAPED_UNICODE), 0, 4000), now_utc(),
]);

http_response_code(200);
echo 'ok';
