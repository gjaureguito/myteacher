# Agente comercial por WhatsApp

El endpoint `/webhook/whatsapp.php` recibe mensajes de WhatsApp Cloud API, valida la firma de Meta y responde consultas comerciales frecuentes de MyTeacher.

## Privacidad y comportamiento

- Las respuestas salen de reglas y datos de la página, sin llamar a un modelo de IA externo.
- El número del contacto y los mensajes de texto entrantes y salientes se guardan en la base MySQL/SQLite de MyTeacher para consulta en `/admin/whatsapp.php`.
- El panel se limita a administradores y permite buscar por teléfono/fecha, abrir la transcripción y eliminar el hilo.
- La conversación se conserva hasta que un administrador la elimina. No se registra el texto en los logs de aplicación ni se envía a un proveedor externo de IA.
- Solo procesa mensajes de texto. El precio sale de la configuración vigente del plan; las respuestas incluyen enlaces al sitio y al registro.
- El agente explica la prueba de 7 días, precio, contenidos, niveles y cancelación. No procesa pagos ni pide contraseñas, datos de tarjeta o códigos de verificación.

## Variables de entorno en Railway

Configurar en el servicio web, sin poner valores secretos en GitHub:

- `META_WHATSAPP_VERIFY_TOKEN`: valor privado elegido por el administrador para validar la suscripción del webhook.
- `META_APP_SECRET`: secreto de la aplicación de Meta, para validar `X-Hub-Signature-256`.
- `META_WHATSAPP_ACCESS_TOKEN`: token de acceso de sistema de Meta con permisos de WhatsApp necesarios.
- `META_WHATSAPP_PHONE_NUMBER_ID`: identificador del número de MyTeacher en WhatsApp Manager.
- `META_GRAPH_API_VERSION`: opcional; por defecto `v27.0`.

## Atención de conversaciones\n\nEl agente debe informar al contacto en su primera respuesta que se conserva el número y el texto para atender consultas comerciales, y enlazar la política de privacidad. No solicitar ni guardar contraseñas, códigos de verificación ni datos de pago.\n\n## Configuración en Meta

1. Crear o seleccionar una aplicación de Meta y agregar el producto WhatsApp Cloud API para el negocio MyTeacher.
2. Completar el registro del número `+54 9 264 626-3385` y la verificación que Meta envíe al teléfono.
3. Si se mantiene WhatsApp Business App, completar la incorporación por el flujo de coexistencia de Meta cuando la cuenta y el número sean elegibles. No eliminar la cuenta del teléfono como parte de este flujo.
4. En la aplicación de Meta, configurar el callback como `https://TU-DOMINIO/webhook/whatsapp.php`, cargar el mismo valor de `META_WHATSAPP_VERIFY_TOKEN` y suscribir el campo `messages`.
5. Cargar las variables secretas en Railway y comprobar la recepción con un mensaje de prueba desde otro teléfono.

La dirección pública debe tener HTTPS válido y estar disponible desde internet. No publicar el token de acceso ni el secreto de la aplicación.
