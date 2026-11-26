<?php
declare(strict_types=1);

/**
 * Respuestas comerciales de MyTeacher.
 * Usa información pública de la plataforma y reglas locales; no envía consultas a una IA externa.
 * Las conversaciones se guardan en la base propia para el panel privado de administración.
 */
function whatsapp_sales_storage(): void
{
    static $ready = false;
    if ($ready) return;

    $sqlite = db_is_sqlite();
    $pk = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
    $fk = $sqlite ? 'INTEGER NOT NULL' : 'INT UNSIGNED NOT NULL';
    $textType = $sqlite ? 'TEXT NOT NULL' : 'MEDIUMTEXT NOT NULL';
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
        body $textType,
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

/** Devuelve una respuesta corta y basada en información publicada por MyTeacher. */
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
    $account = app_url('account.php');
    $reset = app_url('forgot.php');
    $price = money_ars(plan_price());

    if (preg_match('/\b(precio|precios|cuesta|cuestan|valor|pago|pagar|mercado pago|suscripcion|mensual)\b/u', $text)) {
        return "MyTeacher Completo cuesta {$price} ARS por mes. La prueba de 7 días es gratis, no pide tarjeta ni genera un cobro automático. Si después querés continuar, elegís contratar y pagás por Mercado Pago. Podés ver el plan acá: {$home}#precio";
    }

    if (preg_match('/\b(gratis|prueba|probar|7 dias|siete dias|sin tarjeta)\b/u', $text)) {
        return "Podés probar MyTeacher gratis durante 7 días con acceso completo a los niveles A1–C2, ejercicios, situaciones y seguimiento del progreso. No necesitás cargar una tarjeta y no hay cobro automático al terminar. Para comenzar, creá tu cuenta: {$register}";
    }

    if (preg_match('/\b(contrasena|olvide|recuperar acceso|no puedo ingresar|no puedo entrar|clave)\b/u', $text)) {
        return "Para recuperar el acceso, abrí la página de recuperación de contraseña y seguí las instrucciones: {$reset}. Si todavía no tenés cuenta, podés crearla acá: {$register}";
    }

    if (preg_match('/\b(empezar|comenzar|registr|crear cuenta|inscrib|ingresar|entrar)\b/u', $text)) {
        return "Para empezar, creá una cuenta con tu email. Podés arrancar desde A1 o hacer el test orientativo. La prueba dura 7 días, incluye acceso completo y no pide tarjeta: {$register}";
    }

    if (preg_match('/\b(cancelar|darme de baja|baja|renovar|renovacion)\b/u', $text)) {
        return "Para cancelar un plan, iniciá sesión en MyTeacher, entrá en Mi cuenta y gestioná la baja desde esa sección: {$account}. La prueba gratis no se cobra automáticamente. Si cancelás un período ya pagado, la página indica que conservás el acceso hasta que termine.";
    }

    if (preg_match('/\b(nivel|a1|a2|b1|b2|c1|c2|principiante|intermedio|avanzado|test)\b/u', $text)) {
        return "MyTeacher organiza el catálogo de práctica en seis niveles, de A1 a C2. Si empezás desde cero, podés elegir A1; si ya tenés conocimientos, hacé el test orientativo para encontrar un punto de partida: " . app_url('placement.php') . ". El test orienta, no certifica el dominio del idioma.";
    }

    if (preg_match('/\b(clase|clases|profesor|profesora|particular|en vivo|tutoria)\b/u', $text)) {
        return "MyTeacher es una plataforma de estudio autónomo; no incluye clases en vivo ni profesores particulares. Ofrece teoría en castellano, práctica en inglés, situaciones con audio y corrección explicada. Podés ver cómo funciona: {$home}#como-funciona";
    }

    if (preg_match('/\b(certificado|certificacion|certificar)\b/u', $text)) {
        return "Los niveles A1–C2 organizan el catálogo de práctica. MyTeacher no presenta el recorrido como una certificación del dominio del idioma. Podés conocer los niveles acá: " . app_url('program.php');
    }

    if (preg_match('/\b(incluye|contenido|unidades|ejercicios|audio|pronunciacion|situaciones|libros|talleres|funciona|celular|telefono|gramatica|lectura|escritura|progreso)\b/u', $text)) {
        return "MyTeacher reúne teoría gramatical en castellano, ejemplos, ejercicios con explicación, situaciones reales con audio y seguimiento del progreso. El catálogo publicado incluye 345 unidades, 3.858 ejercicios, 16 situaciones multimedia con 98 actividades y 24 talleres de lectura y escritura. Podés estudiar a tu ritmo desde el celular o la computadora: {$home}";
    }

    if (preg_match('/\b(hola|buenas|buen dia|buenas tardes|buenas noches|ayuda|menu|opciones)\b/u', $text)) {
        return "¡Hola! Soy el asistente de MyTeacher 👋 Puedo explicarte cómo empezar, qué incluye la plataforma, los niveles, la prueba gratis, el precio o cómo gestionar tu cuenta. Escribime qué querés saber o visitá {$home}";
    }

    return "Puedo informarte sobre cómo registrarte, la prueba gratis de 7 días, los niveles A1–C2, el contenido de estudio, el precio mensual y la gestión de tu cuenta. Si mi respuesta no coincide con tu consulta, probá explicarla con otras palabras o consultá la información completa en {$home}";
}
