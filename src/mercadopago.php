<?php
declare(strict_types=1);

/*
 * Suscripciones de Mercado Pago sin plan asociado (endpoint /preapproval).
 * - Se crea una preapproval en estado "pending" y se redirige al usuario a init_point.
 * - Mercado Pago avisa por webhook (subscription_preapproval / subscription_authorized_payment).
 * - Nunca confiamos en el cuerpo del webhook: siempre volvemos a consultar la API.
 */

function mp_api(): string
{
    return rtrim(env('MP_API_BASE', 'https://api.mercadopago.com'), '/'); // MP_API_BASE solo para pruebas
}

function mp_token(): string
{
    $t = env('MP_ACCESS_TOKEN');
    if (!$t) throw new RuntimeException('Falta configurar MP_ACCESS_TOKEN.');
    return $t;
}

function mp_request(string $method, string $path, ?array $body = null): array
{
    $headers = ['Authorization: Bearer ' . mp_token()];
    if ($method === 'POST') $headers[] = 'X-Idempotency-Key: ' . bin2hex(random_bytes(16));
    return http_json($method, mp_api() . $path, $headers, $body);
}

/** Crea la suscripción y devuelve la URL de pago (init_point). */
function mp_create_subscription(array $user): string
{
    $res = mp_request('POST', '/preapproval', [
        'reason' => app_name() . ' — Suscripción mensual',
        'external_reference' => 'user:' . $user['id'],
        'payer_email' => $user['email'],
        'back_url' => app_url('/subscription_return.php'),
        'status' => 'pending',
        'auto_recurring' => [
            'frequency' => 1,
            'frequency_type' => 'months',
            'transaction_amount' => plan_price(),
            'currency_id' => 'ARS',
        ],
    ]);

    if ($res['code'] >= 300 || empty($res['data']['init_point'])) {
        log_line('MP create preapproval error ' . $res['code'] . ': ' . substr((string) $res['raw'], 0, 800));
        $msg = $res['data']['message'] ?? 'Mercado Pago no respondió.';
        throw new RuntimeException('No pudimos iniciar la suscripción: ' . $msg);
    }

    q('UPDATE users SET mp_preapproval_id = ?, sub_status = ? WHERE id = ?',
        [$res['data']['id'], 'pending', $user['id']]);

    return $res['data']['init_point'];
}

/**
 * Consulta la preapproval y actualiza al usuario.
 * authorized → acceso hasta el próximo cobro + 3 días de gracia.
 * cancelled/paused → se mantiene el acceso ya pago (access_until no se toca).
 */
function mp_sync_preapproval(string $preapprovalId): ?array
{
    $res = mp_request('GET', '/preapproval/' . rawurlencode($preapprovalId));
    if ($res['code'] !== 200) {
        log_line("MP get preapproval $preapprovalId → " . $res['code']);
        return null;
    }
    $pa = $res['data'];

    $user = null;
    if (preg_match('/^user:(\d+)$/', (string) ($pa['external_reference'] ?? ''), $m)) {
        $user = q_one('SELECT * FROM users WHERE id = ?', [(int) $m[1]]);
    }
    $user ??= q_one('SELECT * FROM users WHERE mp_preapproval_id = ?', [$preapprovalId]);
    if (!$user) {
        log_line("MP preapproval $preapprovalId sin usuario asociado");
        return null;
    }

    $status = (string) ($pa['status'] ?? 'pending');
    $map = ['authorized' => 'active', 'paused' => 'paused', 'cancelled' => 'cancelled', 'pending' => 'pending'];
    $local = $map[$status] ?? $status;
    $accessUntil = $user['access_until'];

    if ($status === 'authorized') {
        $next = !empty($pa['next_payment_date']) ? strtotime($pa['next_payment_date']) : false;
        $candidate = $next ? $next + 3 * 86400 : time() + 33 * 86400;
        $candidateStr = gmdate('Y-m-d H:i:s', $candidate);
        if (!$accessUntil || $candidateStr > $accessUntil) $accessUntil = $candidateStr;
    }

    q('UPDATE users SET sub_status = ?, access_until = ?, mp_preapproval_id = ? WHERE id = ?',
        [$local, $accessUntil, $preapprovalId, $user['id']]);

    return ['user_id' => (int) $user['id'], 'status' => $status];
}

function mp_cancel_subscription(array $user): bool
{
    if (empty($user['mp_preapproval_id'])) return false;
    $res = mp_request('PUT', '/preapproval/' . rawurlencode($user['mp_preapproval_id']), ['status' => 'cancelled']);
    if ($res['code'] >= 300) {
        log_line('MP cancel error ' . $res['code'] . ': ' . substr((string) $res['raw'], 0, 500));
        return false;
    }
    q("UPDATE users SET sub_status = 'cancelled' WHERE id = ?", [$user['id']]);
    return true;
}

/**
 * Valida la firma x-signature del webhook (si MP_WEBHOOK_SECRET está configurado).
 * Manifest: id:{data.id};request-id:{x-request-id};ts:{ts};
 */
function mp_valid_signature(string $dataId): bool
{
    $secret = env('MP_WEBHOOK_SECRET');
    if (!$secret) return true; // sin secreto, igual re-consultamos la API
    $sig = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    $reqId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
    $ts = $v1 = '';
    foreach (explode(',', $sig) as $part) {
        [$k, $v] = array_pad(array_map('trim', explode('=', $part, 2)), 2, '');
        if ($k === 'ts') $ts = $v;
        if ($k === 'v1') $v1 = $v;
    }
    if ($ts === '' || $v1 === '') return false;
    $id = ctype_alnum($dataId) ? strtolower($dataId) : $dataId;
    $manifest = "id:$id;" . ($reqId !== '' ? "request-id:$reqId;" : '') . "ts:$ts;";
    return hash_equals(hash_hmac('sha256', $manifest, $secret), $v1);
}
