# Profesor virtual por voz (versión piloto)

Nueva página aislada: `/profesor-virtual.php` y endpoint `/api/voice-session.php`.
No reemplaza la plataforma ni modifica el login, cursos o pagos existentes.

## Preparación en Railway
En **Variables** del servicio web PHP:
- `OPENAI_API_KEY`: clave privada de tu proyecto OpenAI. **Nunca** subir a GitHub ni colocar en JavaScript.
- `OPENAI_VOICE_MODEL=gpt-realtime-2.1-mini`: modelo sugerido, sujeto a disponibilidad en tu cuenta.
- `VOICE_TEACHER_ENABLED=1`: activar **solo después de revisar costos y probar**.
- `VOICE_DAILY_SESSIONS=3`: cantidad máxima de inicios de sesión por usuario y día UTC (guardada en base de datos; no mide minutos ni dólares).
Por defecto está deshabilitado. El acceso exige login y acceso completo (incluye administrador y prueba vigente).

## Prueba
1. Hacer deploy de la rama de prueba en un entorno separado, no en producción directamente.
2. Entrar a `/profesor-virtual.php` en Chrome o Edge con HTTPS.
3. Autorizar micrófono, seleccionar nivel, iniciar y hablar.
4. Comprobar respuesta audible, interrupciones naturales y que Finalizar apaga el micrófono.
5. Comprobar 401/403 sin login y respuestas 429 al reconectar inmediatamente.
6. Comprobar que la clave nunca aparece en el HTML, el JS o la respuesta del endpoint.

## Límites importantes
- Hay una cuota persistente de inicios por usuario y día, pero **no es un límite de gasto**: el temporizador de 10 minutos es solo del navegador. Configurar límites de proyecto en OpenAI y, antes de liberar a alumnos, incorporar límite de concurrencia y corte de sesiones del lado del servidor.
- La cuota diaria se registra antes de llamar a OpenAI. Si la solicitud falla puede consumir uno de los inicios permitidos.
- El reconocimiento y fin de turno dependen del detector de voz y del entorno acústico.
- En el piloto no se guardan transcripciones ni se sincroniza el progreso académico.
- Las voces y el formato de sesión pueden cambiar con versiones de la API, por lo que requieren verificación antes de lanzar.
