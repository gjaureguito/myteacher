<?php
declare(strict_types=1);
/*
 * Envío de emails por API HTTP (Railway bloquea SMTP en los planes sin Pro).
 * Se usa el primer proveedor configurado:
 *   BREVO_API_KEY   → https://api.brevo.com/v3/smtp/email
 *   RESEND_API_KEY  → https://api.resend.com/emails
 * Remitente: MAIL_FROM, por ejemplo "MyTeacher <no-responder@myteacher.com.ar>".
 */

function mail_provider(): ?string
{
    if (env('BREVO_API_KEY')) return 'brevo';
    if (env('RESEND_API_KEY')) return 'resend';
    return null;
}

function mail_configured(): bool
{
    return mail_provider() !== null && (string) env('MAIL_FROM', '') !== '';
}

/** Separa "Nombre <correo@dominio>" en [nombre, correo]. */
function mail_from_parts(): array
{
    $from = trim((string) env('MAIL_FROM', ''));
    if (preg_match('/^\s*(.*?)\s*<([^>]+)>\s*$/', $from, $m)) return [trim($m[1], " \"'") ?: app_name(), trim($m[2])];
    return [app_name(), $from];
}

/** Envía un email. Devuelve true si el proveedor lo aceptó. */
function send_mail(string $to, string $subject, string $html, string $text): bool
{
    $provider = mail_provider();
    if (!$provider || !mail_configured()) {
        error_log('MAIL no configurado: falta BREVO_API_KEY o RESEND_API_KEY, y MAIL_FROM');
        return false;
    }
    [$fromName, $fromEmail] = mail_from_parts();

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
