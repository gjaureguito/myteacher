# Profesor virtual por voz (versión piloto)

Nueva página aislada: `/profesor-virtual.php` y endpoint `/api/voice-session.php`.
No reemplaza la plataforma ni modifica el login, cursos o pagos existentes.

## Preparación en Railway
En **Variables** del servicio web PHP:
- `OPENAI_API_KEY`: clave privada de tu proyecto OpenAI. **Nunca** subir a GitHub ni colocar en JavaScript.
- `OPENAI_VOICE_MODEL=gpt-realtime-2.1-mini`: modelo sugerido, sujeto a disponibilidad en tu cuenta.
- `VOICE_TEACHER_ENABLED=1`: activar **solo después de revisar costos y probar**.
Por defecto está deshabilitado. El acceso exige login y acceso completo (incluye administrador y prueba vigente).

## Prueba
1. Hacer deploy de la rama de prueba en un entorno separado, no en producción directamente.
2. Entrar a `/profesor-virtual.php` en Chrome o Edge con HTTPS.
3. Autorizar micrófono, seleccionar nivel, iniciar y hablar.
4. Comprobar respuesta audible, interrupciones naturales y que Finalizar apaga el micrófono.
5. Comprobar 401/403 sin login y respuestas 429 al reconectar inmediatamente.
6. Comprobar que la clave nunca aparece en el HTML, el JS o la respuesta del endpoint.

## Límites importantes
- El temporizador de 10 minutos es **del navegador**: no equivale a un límite duro de gasto en servidor. Configurar límites de proyecto en OpenAI y, antes de liberar a alumnos, cuotas persistentes por usuario y límites de concurrencia.
- El reconocimiento y fin de turno dependen del detector de voz y del entorno acústico.
- En el piloto no se guardan transcripciones ni se sincroniza el progreso académico.
- Las voces y el formato de sesión pueden cambiar con versiones de la API, por lo que requieren verificación antes de lanzar.
