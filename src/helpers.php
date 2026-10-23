<?php
declare(strict_types=1);

function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    $url = preg_match('#^https?://#', $path) ? $path : app_url($path);
    header('Location: ' . $url, true, 302);
    exit;
}

function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('La sesión expiró. Volvé atrás y recargá la página.');
    }
}

function json_out(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function post_str(string $k, int $max = 500): string
{
    $v = $_POST[$k] ?? '';
    return is_string($v) ? mb_substr(trim($v), 0, $max) : '';
}

function money_ars(float $n): string
{
    return '$' . number_format($n, 0, ',', '.');
}

/** Fecha UTC de la base → texto local (dd/mm/aaaa). */
function fmt_date(?string $utc): string
{
    if (!$utc) return '—';
    $d = new DateTime($utc, new DateTimeZone('UTC'));
    $d->setTimezone(new DateTimeZone(date_default_timezone_get()));
    return $d->format('d/m/Y');
}

/** Normaliza una respuesta escrita para comparar (minúsculas, sin espacios dobles ni puntuación final). */
function norm_answer(string $s): string
{
    $s = mb_strtolower(trim($s));
    $s = str_replace(['’', '‘', '`', '´'], "'", $s);
    $s = preg_replace('/\s+/u', ' ', $s);
    $s = rtrim($s, " .!?");
    return $s;
}

function render(string $view, array $vars = [], string $title = ''): void
{
    extract($vars);
    $pageTitle = $title;
    require ROOT . '/src/views/layout_top.php';
    require ROOT . '/src/views/' . $view . '.php';
    require ROOT . '/src/views/layout_bottom.php';
}

function http_json(string $method, string $url, array $headers = [], ?array $body = null, bool $form = false): array
{
    $ch = curl_init($url);
    $h = array_merge(['Accept: application/json'], $headers);
    if ($body !== null) {
        if ($form) {
            $payload = http_build_query($body);
            $h[] = 'Content-Type: application/x-www-form-urlencoded';
        } else {
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE);
            $h[] = 'Content-Type: application/json';
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $h,
        CURLOPT_TIMEOUT => 20,
    ]);
    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    $data = is_string($raw) ? json_decode($raw, true) : null;
    return ['code' => $code, 'data' => is_array($data) ? $data : [], 'raw' => $raw, 'error' => $err];
}

function log_line(string $msg): void
{
    error_log('[' . app_name() . '] ' . $msg);
}
