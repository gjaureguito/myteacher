<?php
declare(strict_types=1);
function marketing_label(string $value, int $length): string {
    $value = preg_replace('/[^a-zA-Z0-9_. -]/', '', $value) ?? '';
    return substr(trim($value), 0, $length);
}
function marketing_visit(): string {
    if (in_array($_SESSION['marketing_variant'] ?? '', ['C', 'D'], true)) return $_SESSION['marketing_variant'];
    $variant = random_int(0, 1) ? 'D' : 'C';
    $_SESSION['marketing_variant'] = $variant;
    $source = marketing_label((string)($_GET['utm_source'] ?? ''), 80);
    if ($source === '') {
        $host = strtolower((string)parse_url((string)($_SERVER['HTTP_REFERER'] ?? ''), PHP_URL_HOST));
        $source = match (true) {
            str_contains($host, 'facebook.com'), str_contains($host, 'fb.com') => 'facebook',
            str_contains($host, 'instagram.com') => 'instagram',
            str_contains($host, 'google.') => 'google',
            default => 'directo / otro',
        };
    }
    $campaign = marketing_label((string)($_GET['utm_campaign'] ?? ''), 100);
    try {
        $id = bin2hex(random_bytes(32));
        q('INSERT INTO marketing_visits (id, variant, source, campaign, visited_at) VALUES (?, ?, ?, ?, ?)', [$id,$variant,$source,$campaign,now_utc()]);
        $_SESSION['marketing_visit_id'] = $id;
    } catch (Throwable $e) { error_log('Marketing: no se pudo registrar visita'); }
    return $variant;
}
function marketing_registration(): void {
    $id = $_SESSION['marketing_visit_id'] ?? '';
    if (!is_string($id) || !preg_match('/^[a-f0-9]{64}$/', $id)) return;
    try { q('UPDATE marketing_visits SET registered_at=? WHERE id=? AND registered_at IS NULL', [now_utc(), $id]); }
    catch (Throwable $e) { error_log('Marketing: no se pudo registrar conversión'); }
}
