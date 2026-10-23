<?php
declare(strict_types=1);

/*
 * OpenID Connect con Google y Microsoft (cuentas personales Hotmail/Outlook y laborales).
 * Flujo "authorization code" del lado servidor: el id_token llega directo del endpoint
 * de tokens por TLS, así que validamos emisor, audiencia y vencimiento.
 */

function oauth_providers(): array
{
    return [
        'google' => [
            'label' => 'Google',
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token_url' => 'https://oauth2.googleapis.com/token',
            'scope' => 'openid email profile',
            'issuers' => ['https://accounts.google.com', 'accounts.google.com'],
        ],
        'microsoft' => [
            'label' => 'Microsoft',
            'client_id' => env('MS_CLIENT_ID'),
            'client_secret' => env('MS_CLIENT_SECRET'),
            'auth_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'token_url' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'scope' => 'openid email profile',
            'issuers' => null, // varía por tenant; se valida el formato
        ],
    ];
}

function oauth_enabled(string $p): bool
{
    $cfg = oauth_providers()[$p] ?? null;
    return $cfg && $cfg['client_id'] && $cfg['client_secret'];
}

function oauth_redirect_uri(string $p): string
{
    return app_url('/auth/callback.php?provider=' . $p);
}

function oauth_start(string $p): never
{
    $cfg = oauth_providers()[$p];
    $state = bin2hex(random_bytes(16));
    $nonce = bin2hex(random_bytes(16));
    $_SESSION['oauth'] = ['state' => $state, 'nonce' => $nonce, 'provider' => $p];
    $params = [
        'client_id' => $cfg['client_id'],
        'redirect_uri' => oauth_redirect_uri($p),
        'response_type' => 'code',
        'scope' => $cfg['scope'],
        'state' => $state,
        'nonce' => $nonce,
        'prompt' => 'select_account',
    ];
    redirect($cfg['auth_url'] . '?' . http_build_query($params));
}

function jwt_payload(string $jwt): array
{
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return [];
    $json = base64_decode(strtr($parts[1], '-_', '+/') . str_repeat('=', (4 - strlen($parts[1]) % 4) % 4));
    $data = json_decode((string) $json, true);
    return is_array($data) ? $data : [];
}

/** Devuelve ['sub','email','name'] o lanza excepción con mensaje para el usuario. */
function oauth_finish(string $p): array
{
    $st = $_SESSION['oauth'] ?? null;
    unset($_SESSION['oauth']);
    if (!$st || $st['provider'] !== $p || !hash_equals($st['state'], (string) ($_GET['state'] ?? ''))) {
        throw new RuntimeException('La solicitud de inicio de sesión no es válida. Intentá de nuevo.');
    }
    if (!empty($_GET['error'])) {
        throw new RuntimeException('Cancelaste el inicio de sesión o el proveedor lo rechazó.');
    }
    $code = (string) ($_GET['code'] ?? '');
    if ($code === '') throw new RuntimeException('Falta el código de autorización.');

    $cfg = oauth_providers()[$p];
    $res = http_json('POST', $cfg['token_url'], [], [
        'code' => $code,
        'client_id' => $cfg['client_id'],
        'client_secret' => $cfg['client_secret'],
        'redirect_uri' => oauth_redirect_uri($p),
        'grant_type' => 'authorization_code',
    ], true);

    $idt = $res['data']['id_token'] ?? null;
    if (!$idt) {
        log_line("OAuth $p token error: " . substr((string) $res['raw'], 0, 500));
        throw new RuntimeException('No pudimos validar tu cuenta con ' . $cfg['label'] . '.');
    }
    $c = jwt_payload($idt);

    $audOk = ($c['aud'] ?? null) === $cfg['client_id'];
    $expOk = ($c['exp'] ?? 0) > time() - 60;
    $nonceOk = isset($c['nonce']) && hash_equals($st['nonce'], (string) $c['nonce']);
    $issOk = $cfg['issuers']
        ? in_array($c['iss'] ?? '', $cfg['issuers'], true)
        : (bool) preg_match('#^https://login\.microsoftonline\.com/[0-9a-f-]{36}/v2\.0$#', $c['iss'] ?? '');
    if (!$audOk || !$expOk || !$nonceOk || !$issOk) {
        throw new RuntimeException('El token recibido no es válido.');
    }

    if ($p === 'google') {
        if (empty($c['email']) || empty($c['email_verified'])) {
            throw new RuntimeException('Tu cuenta de Google no tiene un email verificado.');
        }
        $email = $c['email'];
    } else {
        $email = $c['email'] ?? $c['preferred_username'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Tu cuenta de Microsoft no compartió un email.');
        }
    }

    // En Microsoft, el "sub" es por aplicación; combinamos con tid para que sea único.
    $sub = $p === 'microsoft' ? (($c['tid'] ?? '') . ':' . ($c['oid'] ?? $c['sub'])) : (string) $c['sub'];

    return ['sub' => $sub, 'email' => $email, 'name' => (string) ($c['name'] ?? '')];
}
