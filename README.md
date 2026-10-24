# InglésPro — plataforma de inglés A1–C2

PHP 8.3 + MySQL, sin frameworks. Registro con email, Google o Microsoft (Hotmail/Outlook), demo gratuita (primera unidad de cada nivel + test de nivel) y suscripción mensual de $30.000 con Mercado Pago. Listo para Railway con Dockerfile.

## Qué incluye

- 6 niveles, 30 unidades (A1 completo con 10), 708 ejercicios en seis habilidades: Grammar, Vocabulary (con imágenes y unir pares), Listening (comprensión y dictado), Reading (30 textos con preguntas), Speaking (micrófono) y Writing (criterios automáticos y texto modelo). Teoría en castellano con tablas, ejemplos con audio y "errores típicos".
- Corrección en el servidor (las respuestas nunca viajan al navegador antes de responder), progreso por unidad y nivel, racha de días.
- Test de nivel de 24 preguntas que recomienda por dónde empezar.
- Audio con la voz en inglés del navegador (Web Speech API): sin costo ni archivos.
- Panel `/admin`: usuarios, acceso manual, ingresos estimados, avisos de Mercado Pago y editor de unidades.
- Seguridad: CSRF en todos los formularios, contraseñas con `password_hash`, bloqueo tras 5 intentos fallidos, cookies `HttpOnly`/`Secure`, validación de `state` y `nonce` en OAuth y firma de webhooks.

## Estructura

```
public/        raíz web (páginas, /api, /admin, /auth, /webhook, assets)
src/           lógica: db, auth, oauth, mercadopago, progreso, vistas
content/       contenido A1–C2 (a1.php … c2.php) y test de nivel
docker/        start.sh (puerto de Railway) y php.ini
```

Las tablas se crean y el contenido se carga solo en el primer request.

## Probar en tu PC

```bash
cp .env.example .env        # dejá DB_DRIVER=sqlite y APP_URL=http://localhost:8000
php -S localhost:8000 -t public
```

## Deploy en Railway

1. **Subí el código a GitHub**
   ```bash
   git init && git add . && git commit -m "InglésPro inicial"
   git remote add origin https://github.com/gjaureguito/ingles-pro.git
   git push -u origin main
   ```
2. En Railway: **New Project → Deploy from GitHub repo** y elegí el repo. Detecta el `Dockerfile` solo.
3. En el mismo proyecto: **+ New → Database → MySQL**.
4. En el servicio web → **Variables**, cargá las de `.env.example`. Para la base: `MYSQL_URL = ${{MySQL.MYSQL_URL}}`.
5. **Settings → Networking → Generate Domain** para tener una URL `*.up.railway.app` y probar.

Con la CLI de Railway:
```bash
npm i -g @railway/cli
railway login
railway link            # elegí el proyecto
railway variables --set "APP_URL=https://www.tudominio.com"
railway up              # deploy desde tu carpeta
railway logs            # ver errores
```

## Dominio propio

1. Railway → servicio web → **Settings → Networking → Custom Domain** → escribí `www.tudominio.com`. Railway te muestra un registro **CNAME** (y un TXT de verificación si lo pide).
2. En el panel de DNS de tu dominio (por ejemplo Hostinger → Dominios → DNS / Nameservers) creá ese CNAME: nombre `www`, destino el que indica Railway.
3. Para el dominio sin `www`: en Hostinger configurá una redirección de `tudominio.com` a `https://www.tudominio.com`, o usá Cloudflare con CNAME flattening.
4. Esperá a que Railway marque el certificado como activo y actualizá `APP_URL` con el dominio final.

Verificación: `nslookup www.tudominio.com` debe responder con el host de Railway.

## Mercado Pago

1. [Tus integraciones](https://www.mercadopago.com.ar/developers/panel/app) → creá una aplicación (producto: Suscripciones).
2. Copiá el **Access Token de producción** en `MP_ACCESS_TOKEN`.
3. **Webhooks → Configurar notificaciones**: URL `https://www.tudominio.com/webhook/mercadopago.php`, eventos **Planes y suscripciones**. Copiá la clave secreta en `MP_WEBHOOK_SECRET`.
4. Para probar sin plata real: usá credenciales de prueba y creá **dos usuarios de prueba** (vendedor y comprador). El email del comprador en Mercado Pago tiene que ser el mismo que el de la cuenta en la plataforma, porque la suscripción se crea con `payer_email`.

Cómo funciona: al suscribirse se crea un `preapproval` en estado `pending` y el usuario paga en Mercado Pago. El webhook (o la vuelta a `/subscription_return.php`) consulta la API y, si está `authorized`, habilita el acceso hasta el próximo cobro + 3 días de gracia. Si cancela, mantiene el acceso hasta el fin del período pago.

## Google

[console.cloud.google.com](https://console.cloud.google.com) → APIs y servicios → **Pantalla de consentimiento** (externa, con links a `/privacy.php` y `/terms.php`) → **Credenciales → Crear ID de cliente OAuth → Aplicación web**.
URI de redirección autorizado: `https://www.tudominio.com/auth/callback.php?provider=google`

## Microsoft (Hotmail / Outlook)

[entra.microsoft.com](https://entra.microsoft.com) → **Registros de aplicaciones → Nuevo registro**.
Tipos de cuenta: *cuentas de cualquier directorio organizativo y cuentas personales de Microsoft*.
URI de redirección (Web): `https://www.tudominio.com/auth/callback.php?provider=microsoft`
Después: **Certificados y secretos → Nuevo secreto** → copiá el *Valor* en `MS_CLIENT_SECRET` y el *Id. de aplicación* en `MS_CLIENT_ID`.

Mientras no cargues las credenciales de Google o Microsoft, el botón correspondiente aparece atenuado y avisa que ese ingreso no está habilitado.

## Agregar contenido

- Rápido: `/admin/units.php` → editar o crear unidades desde el navegador.
- Versionado: agregá unidades en `content/xx.php` con el mismo formato, hacé deploy y tocá **Agregar unidades nuevas** en el admin.

La unidad en posición 1 de cada nivel es la que entra en la demo.

## Biblioteca y talleres de aplicación

La experiencia incluye 24 talleres originales con lecturas, comprensión explicada y escritura guiada. Las muestras abiertas están en `/library.php`; el programa, en `/program.php`. Cada unidad de la plataforma incorpora una pestaña Taller de aplicación. Los talleres se versionan en `content/labs/` y no requieren sincronizar ni reemplazar ejercicios de la base. El inventario editorial de consulta está en `/admin/learning.php`.

Ver [diseño y contrato editorial](docs/EXPERIENCIA-ACADEMICA.md). Ejecutar `python tests/curriculum.py` y `python tests/smoke.py` para verificar contenido y flujos con una base temporal.

## Experiencia "Pro" (interfaz)

`public/assets/pro.css` y `public/assets/pro.js` se cargan después de los estilos originales y no tocan la lógica del servidor:

- Modo claro/oscuro (respeta el del sistema y se recuerda), topbar de vidrio, animaciones de entrada y navegación inferior en móvil.
- Efectos de sonido sintetizados con Web Audio (acierto, error, combo, unidad completada), con botón para silenciar. Sin archivos de audio.
- Confeti, XP y combos en la práctica, barra de progreso fija, atajos de teclado (1–4, L, N, Enter) y control de velocidad de la voz.
- Práctica de pronunciación con micrófono ("Decilo vos") en ejemplos y vocabulario (Chrome/Edge, Web Speech Recognition).
- Ilustraciones SVG originales por nivel en `public/assets/images/levels/`.

## Contenido por unidad y estadísticas

- `content/units/<slug>.php` amplía una unidad existente (vocabulario, lecturas, ejercicios nuevos al final) o define una unidad completa nueva. Formato de cada tipo de ejercicio: ver el encabezado de `src/seed.php`.
- Al hacer deploy, el contenido se sincroniza solo y sin borrar progreso: los ejercicios se actualizan por posición y las unidades editadas desde `/admin` no se pisan.
- Validación sin base de datos: `php tests/content_check.php` (índices, textos, que cada texto modelo cumpla sus propios criterios).
- `/stats.php`: avance y precisión por habilidad, minutos de práctica, actividad de 12 semanas, avance por nivel y unidades a reforzar.
- `/review.php`: cuaderno de errores para rehacer lo pendiente y reforzar lo que costó.
