<?php
declare(strict_types=1);

/*
 * Suscripciones de Mercado Pago sin plan asociado (endpoint /preapproval).
 * - Se crea una preapproval en estado "pending" y se redirige al usuario a init_point.
 * - Mercado Pago avisa por webhook (subscription_preapproval / subscription_authorized_payment).
 * - Nunca confiamos en el cuerpo del webhook: siempre volvemos a consultar la API.
 */

require_once ROOT . '/src/settings.php';

function mp_api(): string
{
    return rtrim(env('MP_API_BASE', 'https://api.mercadopago.com'), '/'); // MP_API_BASE solo para pruebas
}

/** Token del panel de administración; si no hay, el de la variable MP_ACCESS_TOKEN. */
function mp_token(): string
{
    mp_oauth_refresh_if_needed();
    $t = setting_get('mp_access_token') ?: env('MP_ACCESS_TOKEN');
    if (!$t) throw new RuntimeException('Mercado Pago todavía no está configurado.');
    return $t;
}

function mp_webhook_secret(): ?string
{
    return setting_get('mp_webhook_secret') ?: env('MP_WEBHOOK_SECRET');
}

function mp_configured(): bool
{
    return (bool) (setting_get('mp_access_token') ?: env('MP_ACCESS_TOKEN'));
}

/** Pagos habilitados para los alumnos (interruptor del panel; por defecto sí, si hay token). */
function mp_enabled(): bool
{
    return mp_configured() && setting_get('mp_enabled', '1') === '1';
}

/** 'subscription' (débito automático), 'once' (pago único por período) o 'both'. */
function mp_mode(): string
{
    $m = setting_get('mp_mode', 'subscription');
    return in_array($m, ['subscription', 'once', 'both'], true) ? $m : 'subscription';
}

function mp_once_days(): int
{
    return max(1, min(365, (int) setting_get('mp_once_days', '30')));
}

function mp_test_mode(): bool
{
    return str_starts_with((string) (setting_get('mp_access_token') ?: env('MP_ACCESS_TOKEN')), 'TEST-');
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
    $secret = mp_webhook_secret();
    if (!$secret) return true; // sin secreto, igual re-consultamos la API
    $sig = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
    // Los avisos de notification_url (pagos únicos) pueden llegar sin firma: el estado
    // siempre se vuelve a consultar en la API con nuestro token, así que no es un riesgo.
    if ($sig === '') return true;
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

/* ---------- Conexión ---------- */

/** Valida un token contra /users/me. Devuelve los datos de la cuenta o lanza una excepción. */
function mp_check_token(string $token): array
{
    $res = http_json('GET', mp_api() . '/users/me', ['Authorization: Bearer ' . $token]);
    if ($res['code'] !== 200) {
        $msg = $res['data']['message'] ?? ($res['error'] ?: 'respuesta ' . $res['code']);
        throw new RuntimeException('Mercado Pago rechazó el token: ' . $msg);
    }
    return $res['data'];
}

/* ---------- Pago único (Checkout Pro) ---------- */

/** Crea una preferencia de pago por mp_once_days() días de acceso y devuelve la URL de pago. */
function mp_create_one_time(array $user): string
{
    $days = mp_once_days();
    $res = mp_request('POST', '/checkout/preferences', [
        'items' => [[
            'id' => 'acceso-' . $days . 'd',
            'title' => app_name() . ' — Acceso completo por ' . $days . ' días',
            'quantity' => 1,
            'currency_id' => 'ARS',
            'unit_price' => plan_price(),
        ]],
        'payer' => ['email' => $user['email']],
        'external_reference' => 'user:' . $user['id'] . ':once:' . $days,
        'back_urls' => [
            'success' => app_url('/payment_return.php'),
            'pending' => app_url('/payment_return.php'),
            'failure' => app_url('/payment_return.php'),
        ],
        'auto_return' => 'approved',
        'notification_url' => app_url('/webhook/mercadopago.php?source_news=webhooks'),
        'statement_descriptor' => mb_substr(preg_replace('/[^A-Za-z0-9 ]/', '', app_name()) ?: 'MYTEACHER', 0, 13),
    ]);

    $url = mp_test_mode() ? ($res['data']['sandbox_init_point'] ?? null) : null;
    $url ??= $res['data']['init_point'] ?? null;
    if ($res['code'] >= 300 || !$url) {
        log_line('MP create preference error ' . $res['code'] . ': ' . substr((string) $res['raw'], 0, 800));
        $msg = $res['data']['message'] ?? 'Mercado Pago no respondió.';
        throw new RuntimeException('No pudimos iniciar el pago: ' . $msg);
    }
    return $url;
}

/**
 * Consulta un pago y, si está aprobado, acredita los días al usuario una sola vez.
 * Devuelve ['user_id', 'status'] o null si el pago no es de un acceso de esta app.
 */
function mp_sync_payment(string $paymentId): ?array
{
    $res = mp_request('GET', '/v1/payments/' . rawurlencode($paymentId));
    if ($res['code'] !== 200) {
        log_line("MP get payment $paymentId → " . $res['code']);
        return null;
    }
    $p = $res['data'];
    // Los cobros de la suscripción también llegan como "payment": esos los maneja la preapproval.
    if (!preg_match('/^user:(\d+):once:(\d+)$/', (string) ($p['external_reference'] ?? ''), $m)) return null;
    $userId = (int) $m[1];
    $days = max(1, min(365, (int) $m[2]));
    $status = (string) ($p['status'] ?? 'pending');
    $amount = (float) ($p['transaction_amount'] ?? 0);
    $pid = (string) ($p['id'] ?? $paymentId);

    settings_ensure();
    $row = q_one('SELECT * FROM mp_payments WHERE payment_id = ?', [$pid]);
    if (!$row) {
        try {
            q('INSERT INTO mp_payments (payment_id, user_id, amount, status, days, credited, created_at, updated_at) VALUES (?,?,?,?,?,0,?,?)',
                [$pid, $userId, $amount, $status, $days, now_utc(), now_utc()]);
        } catch (Throwable $e) { /* otro aviso simultáneo ya lo insertó */ }
    } else {
        q('UPDATE mp_payments SET status = ?, updated_at = ? WHERE payment_id = ?', [$status, now_utc(), $pid]);
    }

    if ($status === 'approved') {
        // Marca atómica: solo el primer proceso que cambia credited 0→1 suma los días.
        $claimed = q('UPDATE mp_payments SET credited = 1 WHERE payment_id = ? AND credited = 0', [$pid])->rowCount();
        if ($claimed === 1) {
            $u = q_one('SELECT * FROM users WHERE id = ?', [$userId]);
            if ($u) {
                $base = (!empty($u['access_until']) && $u['access_until'] > now_utc()) ? strtotime($u['access_until'] . ' UTC') : time();
                $status_local = $u['sub_status'] === 'active' ? 'active' : 'paid';
                q('UPDATE users SET access_until = ?, sub_status = ? WHERE id = ?',
                    [gmdate('Y-m-d H:i:s', $base + $days * 86400), $status_local, $userId]);
            }
        }
    }
    return ['user_id' => $userId, 'status' => $status];
}


/* ---------- Conexión con un clic (OAuth) ---------- */
/*
 * La aplicación de Mercado Pago (MP_CLIENT_ID / MP_CLIENT_SECRET) se crea una sola vez.
 * Después el administrador toca "Conectar Mercado Pago", autoriza su cuenta y el panel guarda
 * el token (cifrado) y su refresh token. El token vence: se renueva solo antes de usarlo.
 */

function mp_oauth_ready(): bool
{
    return (bool) (env('MP_CLIENT_ID') && env('MP_CLIENT_SECRET'));
}

function mp_oauth_connected(): bool
{
    return setting_get('mp_oauth', '0') === '1' && (bool) setting_get('mp_refresh_token');
}

function mp_oauth_redirect_uri(): string
{
    return app_url('/admin/mp_callback.php');
}

/** Redirige a Mercado Pago para autorizar la cuenta (con PKCE y state ligado a la sesión). */
function mp_oauth_start(): never
{
    $state = bin2hex(random_bytes(24));
    $verifier = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
    $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    $_SESSION['mp_oauth'] = ['state' => $state, 'verifier' => $verifier, 'exp' => time() + 600];
    redirect('https://auth.mercadopago.com/authorization?' . http_build_query([
        'client_id' => env('MP_CLIENT_ID'),
        'response_type' => 'code',
        'platform_id' => 'mp',
        'state' => $state,
        'redirect_uri' => mp_oauth_redirect_uri(),
        'code_challenge' => $challenge,
        'code_challenge_method' => 'S256',
    ]));
}

/** Pide tokens a Mercado Pago. Lanza RuntimeException si no los entrega. */
function mp_oauth_token(array $body): array
{
    $body += ['client_id' => env('MP_CLIENT_ID'), 'client_secret' => env('MP_CLIENT_SECRET')];
    $res = http_json('POST', mp_api() . '/oauth/token', [], $body);
    $d = $res['data'];
    if ($res['code'] !== 200 || empty($d['access_token']) || empty($d['refresh_token'])) {
        log_line('MP oauth token error ' . $res['code'] . ': ' . substr((string) $res['raw'], 0, 300));
        throw new RuntimeException('Mercado Pago no completó la autorización.');
    }
    return $d;
}

function mp_oauth_store(array $t): void
{
    setting_set('mp_access_token', (string) $t['access_token']);
    setting_set('mp_refresh_token', (string) $t['refresh_token']);
    setting_set('mp_token_expires', (string) (time() + (int) ($t['expires_in'] ?? 15552000)));
    setting_set('mp_oauth', '1');
}

/** Cierra el flujo: valida state, canjea el código, verifica la cuenta y guarda todo. */
function mp_oauth_finish(string $state, string $code): array
{
    $s = $_SESSION['mp_oauth'] ?? null;
    unset($_SESSION['mp_oauth']);
    if (!$s || $s['exp'] < time() || $state === '' || !hash_equals($s['state'], $state) || $code === '') {
        throw new RuntimeException('La autorización venció o no corresponde a esta sesión. Volvé a intentar.');
    }
    $t = mp_oauth_token([
        'grant_type' => 'authorization_code', 'code' => $code,
        'redirect_uri' => mp_oauth_redirect_uri(), 'code_verifier' => $s['verifier'],
    ]);
    $me = mp_check_token((string) $t['access_token']);
    mp_oauth_store($t);
    setting_set('mp_account', trim(($me['nickname'] ?? '') . ' · ' . ($me['email'] ?? ''), ' ·') . ' (ID ' . ($me['id'] ?? '?') . ')');
    return $me;
}

/** Renueva el token si vence en menos de 5 minutos. Solo aplica a cuentas conectadas por OAuth. */
function mp_oauth_refresh_if_needed(): void
{
    if (!mp_oauth_ready() || !mp_oauth_connected()) return;
    $exp = (int) setting_get('mp_token_expires', '0');
    if ($exp > time() + 300) return;
    try {
        $t = mp_oauth_token(['grant_type' => 'refresh_token', 'refresh_token' => (string) setting_get('mp_refresh_token')]);
        mp_oauth_store($t);
    } catch (RuntimeException $e) {
        log_line('MP no pudo renovar el token: ' . $e->getMessage());
    }
}
