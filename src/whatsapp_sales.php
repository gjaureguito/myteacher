<?php
declare(strict_types=1);

/**
 * Respuestas comerciales de MyTeacher.
 * Los mensajes se guardan en la base propia de MyTeacher para mostrarlos a administradores.
 * No se envían a un proveedor externo de IA.
 */
function whatsapp_sales_storage(): void
{
    static $ready = false;
    if ($ready) return;

    $sqlite = db_is_sqlite();
    $pk = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
    $fk = $sqlite ? 'INTEGER NOT NULL' : 'INT UNSIGNED NOT NULL';
    $text = $sqlite ? 'TEXT NOT NULL' : 'MEDIUMTEXT NOT NULL';
    $engine = $sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    db()->exec("CREATE TABLE IF NOT EXISTS whatsapp_threads (
        id $pk,
        wa_id VARCHAR(32) NOT NULL UNIQUE,
        created_at DATETIME NOT NULL,
        last_message_at DATETIME NOT NULL
    )$engine");
    db()->exec("CREATE TABLE IF NOT EXISTS whatsapp_messages (
        id $pk,
        thread_id $fk,
        direction VARCHAR(8) NOT NULL,
        body $text,
        message_id VARCHAR(160) NULL UNIQUE,
        created_at DATETIME NOT NULL,
        FOREIGN KEY (thread_id) REFERENCES whatsapp_threads(id) ON DELETE CASCADE
    )$engine");
    try { db()->exec('CREATE INDEX idx_whatsapp_messages_thread ON whatsapp_messages (thread_id, id)'); } catch (Throwable $e) { /* ya existe */ }
    try { db()->exec('CREATE INDEX idx_whatsapp_threads_last ON whatsapp_threads (last_message_at)'); } catch (Throwable $e) { /* ya existe */ }

    $ready = true;
}

/**
 * Guarda una entrada o salida y devuelve el id del hilo.
 * Si Meta reenvía un mensaje ya procesado, devuelve null para evitar responder dos veces.
 */
function whatsapp_store_message(string $waId, string $body, string $messageId, string $direction): ?int
{
    whatsapp_sales_storage();
    $waId = preg_replace('/[^0-9]/', '', $waId) ?? '';
    if ($waId === '' || !in_array($direction, ['in', 'out'], true)) return null;

    $thread = q_one('SELECT id FROM whatsapp_threads WHERE wa_id = ?', [$waId]);
    if (!$thread) {
        try {
            q('INSERT INTO whatsapp_threads (wa_id, created_at, last_message_at) VALUES (?, ?, ?)', [$waId, now_utc(), now_utc()]);
        } catch (PDOException $e) {
            // Otra entrega simultánea pudo crear el mismo hilo.
            $thread = q_one('SELECT id FROM whatsapp_threads WHERE wa_id = ?', [$waId]);
            if (!$thread) throw $e;
        }
        $thread = $thread ?: q_one('SELECT id FROM whatsapp_threads WHERE wa_id = ?', [$waId]);
    }
    if (!$thread) return null;

    $threadId = (int) $thread['id'];
    $messageId = trim($messageId);
    if ($messageId !== '' && q_val('SELECT id FROM whatsapp_messages WHERE message_id = ?', [$messageId]) !== null) return null;

    try {
        q('INSERT INTO whatsapp_messages (thread_id, direction, body, message_id, created_at) VALUES (?, ?, ?, ?, ?)',
            [$threadId, $direction, mb_substr($body, 0, 3500, 'UTF-8'), $messageId !== '' ? $messageId : null, now_utc()]);
    } catch (PDOException $e) {
        if ($messageId !== '' && q_val('SELECT id FROM whatsapp_messages WHERE message_id = ?', [$messageId]) !== null) return null;
        throw $e;
    }
    q('UPDATE whatsapp_threads SET last_message_at = ? WHERE id = ?', [now_utc(), $threadId]);
    return $threadId;
}

function whatsapp_sales_reply(string $message): string
{
    $text = mb_strtolower(trim($message), 'UTF-8');
    $text = strtr($text, [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n',
        '¿'=>' ','?'=>' ','¡'=>' ','!'=>' ',
    ]);
    $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text) ?? $text;
    $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

    $home = app_url();
    $register = app_url('register.php');
    $price = money_ars(plan_price());

    if (preg_match('/\b(precio|precios|cuesta|cuestan|valor|pago|pagar|mercado pago|suscripcion|mensual)\b/u', $text)) {
        return "MyTeacher Completo cuesta {$price} ARS por mes. La prueba de 7 días es gratis, no pide tarjeta y no genera un cobro automático. Si después querés continuar, contratás expresamente por Mercado Pago. Más información: {$home}#precio";
    }

    if (preg_match('/\b(gratis|prueba|probar|7 dias|siete dias|sin tarjeta)\b/u', $text)) {
        return "Podés probar MyTeacher gratis durante 7 días con acceso completo a los niveles A1–C2. No necesitás cargar una tarjeta y no hay cobro automático al terminar. Creá tu cuenta acá: {$register}";
    }

    if (preg_match('/\b(empezar|comenzar|registr|crear cuenta|inscrib|ingresar)\b/u', $text)) {
        return "Para empezar, creá una cuenta gratuita con tu email. Podés arrancar desde A1 o hacer el test orientativo. La prueba dura 7 días, incluye acceso completo y no pide tarjeta: {$register}";
    }

    if (preg_match('/\b(cancelar|darme de baja|baja|renovar|renovacion)\b/u', $text)) {
        return "La prueba gratis no se cobra automáticamente. Si contrataste el plan, podés gestionar la baja desde Mi cuenta en MyTeacher. Para solicitar ayuda, ingresá al sitio: {$home}";
    }

    if (preg_match('/\b(nivel|a1|a2|b1|b2|c1|c2|principiante|intermedio|avanzado|test)\b/u', $text)) {
        return "MyTeacher organiza el catálogo de práctica en seis niveles, de A1 a C2. Si no sabés cuál elegir, podés hacer el test orientativo o empezar por A1. El test recomienda un punto de partida; no es una certificación. {$home}program.php";
    }

    if (preg_match('/\b(incluye|contenido|unidades|ejercicios|audio|pronunciacion|situaciones|libros|talleres|funciona|celular|telefono)\b/u', $text)) {
        return "MyTeacher reúne teoría gramatical en castellano, ejemplos, ejercicios con explicación, situaciones reales con audio y seguimiento del progreso. El catálogo publicado incluye 345 unidades, 3.858 ejercicios, 16 situaciones multimedia con 98 actividades y 24 talleres de lectura y escritura. Podés usarlo a tu ritmo desde el celular o la computadora. {$home}";
    }

    if (preg_match('/\b(hola|buenas|buen dia|buenas tardes|buenas noches|ayuda|menu|opciones)\b/u', $text)) {
        return "¡Hola! Soy el asistente comercial de MyTeacher 👋\n\nPuedo ayudarte con la prueba gratis, los contenidos, los niveles o el precio. Escribime tu consulta o visitá {$home}";
    }

    return "Gracias por escribir a MyTeacher. Puedo orientarte sobre la prueba gratis de 7 días, los niveles A1–C2, los contenidos y el precio mensual. ¿Qué te gustaría saber? También podés ver el sitio: {$home}";
}
