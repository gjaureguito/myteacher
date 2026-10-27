<?php
declare(strict_types=1);
/*
 * Envío de emails. Se usa el primer método configurado:
 *
 *   SMTP (Railway Pro permite SMTP saliente):
 *     SMTP_HOST   smtp.gmail.com | smtp.hostinger.com | …
 *     SMTP_PORT   587 (STARTTLS) o 465 (SSL). Por defecto 587.
 *     SMTP_USER   la casilla, p. ej. gjaureguito@gmail.com
 *     SMTP_PASS   contraseña de aplicación (Gmail) o de la casilla (Hostinger)
 *   API HTTP:
 *     BREVO_API_KEY  → https://api.brevo.com/v3/smtp/email
 *     RESEND_API_KEY → https://api.resend.com/emails
 *
 * Remitente: MAIL_FROM ("MyTeacher <casilla@dominio>"). Con SMTP, si falta, se usa SMTP_USER.
 */

function mail_provider(): ?string
{
    if (env('SMTP_HOST') && env('SMTP_USER') && env('SMTP_PASS')) return 'smtp';
    if (env('BREVO_API_KEY')) return 'brevo';
    if (env('RESEND_API_KEY')) return 'resend';
    return null;
}

function mail_configured(): bool
{
    return mail_provider() !== null && mail_from_parts()[1] !== '';
}

/** Separa "Nombre <correo@dominio>" en [nombre, correo]. */
function mail_from_parts(): array
{
    $from = trim((string) env('MAIL_FROM', ''));
    if ($from === '' && mail_provider() === 'smtp') $from = (string) env('SMTP_USER', '');
    if (preg_match('/^\s*(.*?)\s*<([^>]+)>\s*$/', $from, $m)) return [trim($m[1], " \"'") ?: app_name(), trim($m[2])];
    return [app_name(), $from];
}

/** Envía un email. Devuelve true si el servidor o el proveedor lo aceptó. */
function send_mail(string $to, string $subject, string $html, string $text): bool
{
    $provider = mail_provider();
    if (!$provider || !mail_configured()) {
        error_log('MAIL no configurado: cargá SMTP_HOST/SMTP_USER/SMTP_PASS, o BREVO_API_KEY / RESEND_API_KEY, y MAIL_FROM');
        return false;
    }
    [$fromName, $fromEmail] = mail_from_parts();

    if ($provider === 'smtp') {
        try {
            smtp_send($to, $subject, $html, $text, $fromName, $fromEmail);
            return true;
        } catch (Throwable $e) {
            error_log('MAIL smtp: ' . $e->getMessage());
            return false;
        }
    }

    if ($provider === 'brevo') {
        $res = http_json('POST', (string) env('BREVO_API_URL', 'https://api.brevo.com/v3/smtp/email'), ['api-key: ' . env('BREVO_API_KEY')], [
            'sender' => ['name' => $fromName, 'email' => $fromEmail],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'htmlContent' => $html,
            'textContent' => $text,
        ]);
    } else {
        $res = http_json('POST', 'https://api.resend.com/emails', ['Authorization: Bearer ' . env('RESEND_API_KEY')], [
            'from' => $fromName . ' <' . $fromEmail . '>',
            'to' => [$to],
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
        ]);
    }

    if ($res['code'] >= 200 && $res['code'] < 300) return true;
    error_log('MAIL ' . $provider . ' HTTP ' . $res['code'] . ': ' . substr(preg_replace('/\s+/', ' ', (string) $res['raw']) ?? '', 0, 400));
    return false;
}

/* ---------------- Cliente SMTP mínimo (STARTTLS o SSL, AUTH LOGIN) ---------------- */

/** Envía por SMTP. Lanza RuntimeException con el detalle si algo falla. */
function smtp_send(string $to, string $subject, string $html, string $text, string $fromName, string $fromEmail): void
{
    $host = (string) env('SMTP_HOST');
    $port = (int) env('SMTP_PORT', '587');
    $user = (string) env('SMTP_USER');
    $pass = str_replace(' ', '', (string) env('SMTP_PASS')); // las contraseñas de aplicación de Gmail se copian con espacios
    $secure = strtolower((string) env('SMTP_SECURE', $port === 465 ? 'ssl' : 'tls')); // ssl | tls | none (solo pruebas)
    $verify = env('SMTP_INSECURE') !== '1';

    $ctx = stream_context_create(['ssl' => ['verify_peer' => $verify, 'verify_peer_name' => $verify, 'SNI_enabled' => true, 'peer_name' => $host]]);
    $remote = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $fp = @stream_socket_client($remote, $errno, $errstr, 12, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) throw new RuntimeException("no conecta con $host:$port ($errstr)");
    stream_set_timeout($fp, 15);

    $read = static function () use ($fp): array {
        $lines = '';
        while (($line = fgets($fp, 1024)) !== false) {
            $lines .= $line;
            if (strlen($line) < 4 || $line[3] !== '-') break; // última línea de una respuesta multilínea
        }
        return [(int) substr($lines, 0, 3), trim($lines)];
    };
    $cmd = static function (string $line, array $expect, bool $secret = false) use ($fp, $read): array {
        fwrite($fp, $line . "\r\n");
        [$code, $msg] = $read();
        if (!in_array($code, $expect, true)) throw new RuntimeException(($secret ? '[credenciales]' : $line) . " → $msg");
        return [$code, $msg];
    };

    try {
        [$code, $msg] = $read();
        if ($code !== 220) throw new RuntimeException("saludo inesperado: $msg");
        $ehloHost = parse_url(app_url(), PHP_URL_HOST) ?: 'localhost';
        $cmd("EHLO $ehloHost", [250]);
        if ($secure === 'tls') {
            $cmd('STARTTLS', [220]);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                throw new RuntimeException('no se pudo iniciar TLS');
            }
            $cmd("EHLO $ehloHost", [250]);
        }
        $cmd('AUTH LOGIN', [334]);
        $cmd(base64_encode($user), [334], true);
        $cmd(base64_encode($pass), [235], true);
        $cmd('MAIL FROM:<' . $fromEmail . '>', [250]);
        $cmd('RCPT TO:<' . $to . '>', [250, 251]);
        $cmd('DATA', [354]);

        $enc = static fn(string $s) => '=?UTF-8?B?' . base64_encode($s) . '?=';
        $boundary = 'mt_' . bin2hex(random_bytes(12));
        $domain = substr(strrchr($fromEmail, '@') ?: '@localhost', 1);
        $headers = [
            'Date: ' . date(DATE_RFC2822),
            'From: ' . $enc($fromName) . ' <' . $fromEmail . '>',
            'To: <' . $to . '>',
            'Subject: ' . $enc($subject),
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];
        $part = static fn(string $type, string $body) => "--$boundary\r\nContent-Type: $type; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . rtrim(chunk_split(base64_encode($body), 76, "\r\n")) . "\r\n";
        $message = implode("\r\n", $headers) . "\r\n\r\n"
            . $part('text/plain', $text) . $part('text/html', $html) . "--$boundary--";
        $message = preg_replace('/^\./m', '..', $message); // "dot-stuffing" del protocolo SMTP
        fwrite($fp, $message . "\r\n.\r\n");
        [$code, $msg] = $read();
        if ($code !== 250) throw new RuntimeException("el servidor rechazó el mensaje: $msg");
        $cmd('QUIT', [221]);
    } finally {
        fclose($fp);
    }
}
