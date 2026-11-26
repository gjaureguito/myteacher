# Agente informativo de MyTeacher por WhatsApp

El endpoint `/webhook/whatsapp.php` recibe mensajes de WhatsApp Cloud API, valida la firma de Meta y responde consultas frecuentes con información de MyTeacher.

## Qué informa el agente

- Cómo crear una cuenta y comenzar la prueba gratuita de 7 días.
- Precio mensual y forma de contratación por Mercado Pago.
- Recuperación de contraseña y acceso a la cuenta.
- Niveles A1–C2 y test orientativo.
- Contenidos, audios, ejercicios, talleres y seguimiento del progreso.
- Que la plataforma es de estudio autónomo y no incluye clases en vivo ni certificación formal.
- Cómo cancelar un plan desde Mi cuenta.

El agente responde automáticamente con reglas locales y datos del sitio. No usa un modelo de IA externo, no deriva conversaciones a una persona y no procesa pagos. Si no reconoce la consulta, invita a reformularla o a consultar MyTeacher.

## Privacidad

- El número y los mensajes de texto entrantes y salientes se guardan en la base MySQL/SQLite de MyTeacher para mostrar el historial en `/admin/whatsapp.php`.
- El panel es exclusivo para administradores; permite buscar por teléfono y fecha, leer la transcripción y eliminar el hilo.
- Las conversaciones se conservan hasta que un administrador las elimina.
- En la primera respuesta se informa al usuario sobre el registro del chat y se enlaza la política de privacidad.
- El agente nunca solicita contraseñas, códigos de verificación ni datos de tarjeta.

## Variables de entorno en Railway

Configurar en el servicio web. No guardar estos valores en GitHub:

- `META_WHATSAPP_VERIFY_TOKEN`: valor secreto elegido para verificar el webhook.
- `META_APP_SECRET`: secreto de la aplicación de Meta para validar `X-Hub-Signature-256`.
- `META_WHATSAPP_ACCESS_TOKEN`: token de acceso de Meta con permisos de WhatsApp.
- `META_WHATSAPP_PHONE_NUMBER_ID`: identificador del número de MyTeacher en WhatsApp Manager.
- `META_GRAPH_API_VERSION`: opcional; si se omite, el código usa `v27.0`.

## Activación en Meta

El número `+54 9 264 626-3385` ya está conectado en Meta. Cuando esta versión esté desplegada:

1. Cargar las variables secretas anteriores en Railway.
2. En la aplicación de Meta, configurar el callback `https://www.myteacher.com.ar/webhook/whatsapp.php`.
3. Ingresar en Meta el mismo valor configurado en `META_WHATSAPP_VERIFY_TOKEN`.
4. Suscribir la aplicación al campo `messages` de WhatsApp.
5. Enviar un mensaje desde otro teléfono y confirmar que llega una respuesta y aparece en el panel administrativo.

El callback necesita HTTPS válido y acceso público. Nunca compartir ni publicar el token de acceso o el secreto de la aplicación.
