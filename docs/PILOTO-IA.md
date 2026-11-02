# Profesor particular con IA · piloto de USD 5

Integración del piloto en el proyecto PHP/MySQL existente (Railway), conservando el diccionario, los libros, las sesiones y la facturación actuales. El diccionario continúa en `/tutor.php`; el nuevo profesor está en `/profesor.php` y su API es `/api/profesor.php`. No requiere otro servicio ni servidor de voz. Entrada `/profesor.php`, enlace desde Práctica y panel privado `/admin/tutor.php`. Publicación informativa autorizada, sin cargar saldo ni habilitar llamadas de IA o cobros.

## Activar

1. Crear una clave exclusiva de un proyecto de OpenAI y cargar como máximo USD 5 de saldo para este experimento; desactivar recarga automática. Revisar impuestos y mínimo de compra en la cuenta antes de pagar: no se puede asegurar un desembolso final de USD 5 incluyendo impuestos. El límite de la aplicación es consumo de API; Railway y otros gastos existentes son independientes.
2. Esta publicación mantiene `TUTOR_RELEASE_READY=false`, piloto pausado y permiso del servidor apagado. No cargar claves ni activar consumo durante esta publicación. Antes de habilitarlo se deben completar y revisar voz, cobros, precio final ARS y prueba real, y retirar explícitamente el bloqueo de publicación en código. Configurar claves solo como secretos de Railway, nunca en JavaScript, HTML, Git o el chat.
3. Desplegar el checkout que contiene estos cambios. La migración 11 integra las tablas del piloto, pagos y auditoría, controles persistentes, precio final ARS y moneda histórica sobre la versión actual del sitio (esquema 10), sin borrar datos. Mantiene compatibilidad con los esquemas preparados anteriormente. Usar la misma base MySQL persistente para todas las réplicas. No usar SQLite efímero en producción ni borrar/restaurar el libro de reservas a una fecha anterior: eso reabriría saldo ya consumido.
4. Hacer una conversación real breve y revisar calidad, latencia, permisos de micrófono, uso y facturación. No se hizo una llamada real durante la implementación. La disponibilidad del modelo en la cuenta se debe comprobar al activar.

## Política del piloto

- Valores iniciales: 5 intentos por cuenta y día UTC, 25 en total; un intento por minuto. El administrador puede editar los límites generales y establecer excepciones por alumno. No hay renovación mensual. El límite es por cuenta registrada, no una identificación de persona: cuentas múltiples pueden agotar el presupuesto común.
- Cada intento reserva permanentemente USD 0,01 ANTES de contactar OpenAI. Con el presupuesto inicial de USD 5, máximo 500 intentos entre todos los estudiantes. El administrador puede modificar el presupuesto acumulado; no se reinicia lo ya reservado. Los errores y las respuestas perdidas también ocupan reserva y cupo, porque podrían haberse facturado.
- La transacción escribe primero en una única fila global de presupuesto y luego valida cuotas e inserta el intento. InnoDB serializa ese bloqueo entre procesos; SQLite serializa las escrituras. Una clave de solicitud única por usuario evita cobrar o ejecutar dos veces el mismo intento. No hay reintentos automáticos.
- Mensaje máximo 2000 bytes UTF-8, hasta tres respuestas anteriores de 1500 bytes cada una y 400 tokens máximos de salida, incluidos los de razonamiento. Sin herramientas, archivos, audio ni historial ilimitado. El modelo y la tarifa están fijos en código y deben revisarse juntos si cambia OpenAI.
- Modelo `gpt-6-luna`, Responses API, `reasoning.effort=none`, `service_tier=default`, `store=false`. Precios consultados el 01/11/2026: USD 0,10 por millón de tokens de entrada y USD 0,50 de salida. El contexto acotado queda muy por debajo de USD 0,01 por intento, incluso usando bytes como cota conservadora de tokens y margen para sobrecarga. 500 es un techo conservador de intentos, no una predicción de cuánto saldo real se gastará.
- El panel distingue reservas consumidas, costo estimado calculado con tokens informados, respuestas exitosas y llamadas sin costo confirmado. La estimación no reproduce descuentos de caché ni todos los ajustes de facturación. La factura de OpenAI es la fuente final. No usar la clave en otros proyectos si se quiere que el saldo de USD 5 corresponda solo a este piloto.

## Cómo presentarlo y medir interés

Modelo comercial: el profesor virtual se paga por separado de la cuota mensual y el alumno decide cuánto consumir mediante recargas voluntarias. La cuota no incluye saldo de IA; el cupo inicial es una prueba gratuita excepcional. No hay débitos automáticos. El saldo registrado por comprobantes es independiente del acceso a las unidades y de los tokens técnicos informados por OpenAI. Su consumo está deshabilitado: no se venden recargas ni se simula un pago.

Texto incluido: «Tu profesor de conversación con IA. Practicá situaciones cotidianas en inglés, recibí una corrección breve y seguí conversando a tu ritmo». Botón: «Quiero probar el profesor con IA».

La inscripción es explícita e idempotente; no se confunde con registrarse en la plataforma. El panel muestra inscritos, estudiantes que recibieron al menos una respuesta, intentos y costos por estudiante. No se mide todavía impresiones del anuncio ni conversión a una suscripción paga. Veinte estudiantes podrían usar el cupo completo de 25 intentos cada uno; pueden inscribirse más, con uso compartido hasta agotar la reserva.

## Voz: decisión pendiente

Dirección comercial confirmada: chatbot de voz con recargas adicionales a la cuota mensual. Se agregó «Me interesa recargar más práctica», que registra una solicitud única por cuenta y muestra el total al administrador. No cobra ni acredita saldo. El precio al alumno debe ser el importe final en pesos argentinos (ARS), con impuestos incluidos, para el paquete inicial de 5 minutos. La propuesta anterior de USD 0,50 no se convierte ni se usa como precio vigente: falta definir una cifra final en ARS. El administrador carga ese total; no se calculan tasas impositivas ni se agregan impuestos o recargos al valor ingresado. El proyecto ya tiene Mercado Pago para suscripciones; no se reutiliza el retorno de una suscripción como comprobante de compra de minutos. Faltan importe final ARS, vencimiento y condiciones comerciales, medición de duración desde servidor, costos de la modalidad de voz elegida, producto de pago único en ARS, webhook verificado con monto/moneda/cuenta, y débitos/reembolsos transaccionales. No habilitar venta ni uso de minutos pagos hasta implementar y verificar ese circuito. La cuota mensual y el saldo de IA se muestran por separado.

## Controles de administración y recargas

En `/admin/tutor.php`, solo administradores autenticados y con CSRF pueden guardar:

- Presupuesto acumulado en USD (0 a 1000). Cambiarlo nunca devuelve reservas ni compra créditos del proveedor; si queda por debajo de lo reservado, se detienen los nuevos intentos. El valor inicial sigue siendo USD 5.
- Pausa general del piloto, pausa de nuevas inscripciones y máximo de plazas (0 significa sin límite de plazas). Para operar también se requieren la clave privada y `TUTOR_ENABLED=1` del servidor; el panel muestra esos estados sin exponer la clave.
- Límites diarios y totales por alumno: generales o particulares, bloqueo individual; vacío hereda el general y 0 impide nuevos intentos. El límite de un intento por minuto y la reserva mínima siguen fijos.
- Precio final propuesto del paquete en ARS, impuestos incluidos, y minutos del paquete. Inicialmente el precio ARS está vacío y se muestra como pendiente; se conservan 5 minutos de duración. El importe se guarda exactamente en centavos de ARS, sin conversión ni cálculo tributario. Vaciar el campo vuelve a dejarlo pendiente. Cambiar la propuesta no modifica comprobantes anteriores, suscripciones ni inicia ventas. Presupuesto y costos del proveedor permanecen en USD.

Todos los cambios administrativos se guardan con autor, fecha y valores anteriores/nuevos en `tutor_audit`; el panel lista los últimos 30. Configuración, inscripción, reservas y pagos toman primero el mismo bloqueo global para evitar cambios simultáneos inconsistentes.

Registro manual de recargas: solo para comprobantes existentes; no es una integración de cobro. El administrador registra alumno, referencia única, importe final comprobado en ARS (impuestos incluidos), minutos acordados y descripción del comprobante. Nuevos registros guardan explícitamente `currency=ARS`. No inventar conversiones de comprobantes en otras monedas. El registro comienza como `reported` y no suma saldo. Solo tras confirmar la revisión externa pasa a `verified_manual`, que suma sus segundos una vez. `voided` retira el saldo registrado y es terminal. No ejecuta reembolsos externos. Referencias duplicadas o transiciones inválidas se rechazan sin efectos parciales. No borrar registros para corregirlos: anular y crear otro con nueva referencia. No adjuntar datos sensibles al detalle.

Migración 9 de moneda: los comprobantes preexistentes se identifican como USD porque esa era la moneda del formulario anterior. Sus importes, estados y minutos no se convierten ni se modifican; se muestran con moneda explícita y aviso de historial. Tampoco se reinterpreta su tratamiento impositivo. El antiguo `price_cents` se conserva como dato histórico USD; el nuevo `price_ars_cents` comienza en NULL, no en 50 centavos de peso. Solo una carga explícita del administrador establece el precio final ARS. Formularios antiguos con `price_usd` o `amount_usd` no se aceptan como configuración o registros nuevos en pesos. La migración es aditiva e idempotente: no reinicia presupuesto ni sobrescribe precios ARS o monedas ya guardados.

El alumno ve únicamente sus últimos 50 registros y su saldo total de minutos registrados (sumatoria de todos los comprobantes verificados). El administrador ve los últimos 100 y saldos de cada alumno. El piloto gratuito no debita esos minutos y el uso pago permanece deshabilitado. El saldo es un registro administrativo pendiente de habilitación del servicio de voz pago, no una promesa de minutos ya consumibles.

Los créditos de OpenAI son independientes del saldo de los alumnos y del presupuesto interno. El panel enlaza a [facturación de OpenAI](https://platform.openai.com/settings/organization/billing/overview), sin consultar ni modificar saldo/recarga automática. Su estado real es desconocido desde esta aplicación. Configurar allí las recargas del proveedor, si se desean, no aquí.

Se ofrece una prueba económica por turnos: dictado del navegador → revisar texto → enviar → respuesta de IA → lectura opcional del navegador. No se suben archivos de audio a OpenAI. El reconocimiento del navegador puede usar servicios externos y requiere permisos; la disponibilidad y calidad dependen del dispositivo. Se puede escribir siempre. El chat visible es temporal; se guardan respuestas de IA y métricas por cuenta, no los mensajes del alumno. Las respuestas podrían reproducir partes del mensaje. `store=false` no equivale a retención cero por parte del proveedor.

Falta elegir entre mensajes de voz procesados en servidor y una llamada en tiempo real. Realtime requiere otra integración y controles de duración/audio; la tarifa consultada de `gpt-realtime-2.1-mini` es USD 10/20 por millón de tokens de audio de entrada/salida. Transcripción de archivos `gpt-4o-mini-transcribe` estima USD 0,003/minuto, más conversación y generación de voz si se usa. No se activaron esas modalidades ni se prometen minutos de conversación por USD 5.

## Verificación

`python tests/tutor.py`: inscripción requerida, apagado por defecto, duplicados, límite por minuto/día/piloto y 20 reservas simultáneas ante un único centavo disponible; HTTP con ingreso, CSRF, inscripción, interés en recargas y panel exclusivo de administrador. También controles persistentes, presupuesto inferior al consumo, límites particulares, comprobantes pendientes/verificados/anulados, confirmación manual, deduplicación, saldo, auditoría, migración sin reinicio de consumo y cupo/pausa de inscripciones. Se reconstruye una base versión 8 para verificar que el historial USD se conserva y el precio ARS queda pendiente; también se comprueba el precio final ARS sin impuestos añadidos, la moneda explícita de nuevos comprobantes y el rechazo de formularios antiguos. Pruebas con datos temporales, sin gasto de API ni movimientos reales de dinero. Sintaxis PHP y JavaScript verificada. Pendientes: prueba real con OpenAI, concurrencia con MySQL de producción y verificación visual/voz en dispositivos reales.

Fuentes oficiales consultadas:
- https://developers.openai.com/api/docs/models/gpt-6-luna
- https://developers.openai.com/api/docs/pricing
- https://developers.openai.com/api/docs/guides/text
- https://developers.openai.com/api/docs/guides/realtime
