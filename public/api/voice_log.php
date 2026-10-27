<?php
declare(strict_types=1);
/*
 * Registro liviano de fallas del micrófono (voice.js usa sendBeacon).
 * Solo escribe una línea en el log del servidor (Railway → Logs, buscar "VOICE"); no guarda nada en la base.
 */
require dirname(__DIR__, 2) . '/src/bootstrap.php';
if (!is_post()) { http_response_code(405); exit; }
$raw = (string) file_get_contents('php://input', false, null, 0, 2000);
$in = json_decode($raw, true);
if (!is_array($in)) { http_response_code(400); exit; }

// Como mucho 20 registros por sesión, para que nadie llene los logs.
$_SESSION['voice_logs'] = (int) ($_SESSION['voice_logs'] ?? 0) + 1;
if ($_SESSION['voice_logs'] > 20) { http_response_code(204); exit; }

$clean = static fn($v, int $max) => substr(preg_replace('/[^A-Za-z0-9_\-\/.: ]/', '', (string) $v) ?? '', 0, $max);
$code = $clean($in['code'] ?? '', 20);
$details = implode(',', array_map(static fn($d) => $clean($d, 24), array_slice((array) ($in['details'] ?? []), 0, 12)));
$page = $clean($in['page'] ?? '', 60);
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$browser = preg_match('/Edg\/(\d+)/', $ua, $m) ? 'Edge ' . $m[1] : (preg_match('/Firefox\/(\d+)/', $ua, $m) ? 'Firefox ' . $m[1] : (preg_match('/Chrome\/(\d+)/', $ua, $m) ? 'Chrome ' . $m[1] : (preg_match('/Safari/', $ua) ? 'Safari' : 'Otro')));
$os = str_contains($ua, 'Windows') ? 'Windows' : (str_contains($ua, 'Android') ? 'Android' : (preg_match('/iPhone|iPad/', $ua) ? 'iOS' : (str_contains($ua, 'Mac') ? 'Mac' : 'Otro')));
$uid = current_user()['id'] ?? 0;

error_log(sprintf('VOICE code=%s user=%d os=%s browser=%s page=%s errors=%s', $code, (int) $uid, $os, $browser, $page, $details));
http_response_code(204);
