<?php
declare(strict_types=1);

/**
 * Respuestas comerciales de MyTeacher.
 * Este agente usa reglas locales: no conserva mensajes ni los envía a un proveedor de IA.
 */
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
