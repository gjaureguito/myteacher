# Auditoría y rediseño de InglésPro

Fecha: 23/10/2026. Repositorio: gjaureguito/myteacher. Base revisada: 2acc978.

## Alcance y evidencia

Se inspeccionaron por HTTP y navegador la portada pública, ingreso, registro y acceso a administración; por código, panel administrativo, edición de unidades, recorrido del alumno, nivelación, acceso, suscripciones y siembra de contenidos. Las páginas públicas estaban operativas. /admin/ redirige al ingreso sin sesión; no se accedió a cuentas o datos privados de producción.

La implementación usa PHP, PDO y MySQL en Railway; SQLite permite verificación local. Los datos publicados anuncian 6 niveles, 24 unidades y 234 ejercicios. Estos números describen el catálogo, no demuestran cobertura completa del MCER. No se realizó una certificación pedagógica, auditoría legal ni prueba de penetración.

## Hallazgos y decisiones

| Prioridad | Área | Evidencia | Resolución |
| --- | --- | --- | --- |
| Alta | Confianza | terms.php y privacy.php muestran una instrucción de revisión antes de publicar; contacto vacío | Pendiente completar CONTACT_EMAIL y LEGAL_OWNER con datos auténticos; no se inventaron ni se eliminó la advertencia para simular resolución |
| Alta | Métricas | admin/index.php contaba todos los accesos vigentes como suscriptores y multiplicaba por precio | Implementado: separar accesos vigentes y suscripciones active no vencidas; ingreso teórico identificado y explicado |
| Alta | Currículo | Cuatro unidades por nivel; gramática predominante y algunos ejercicios ambiguos | Temario público generado desde unidades publicadas; aclaración de alcance y propuesta pedagógica abajo |
| Media | Presentación | Portada de texto lineal, poca diferenciación entre demo y plan | Nueva composición editorial, práctica destacada, programa explorable, método y comparación de planes |
| Media | Registro | novalidate y consentimiento sin required en HTML | Validación nativa restaurada, consentimiento requerido, validación del servidor conservada |
| Media | SEO | robots.txt y sitemap.xml respondían 404; título de portada solo marca; sin canonical | robots.txt, sitemap.php enlazado desde páginas públicas, título descriptivo, canonical y Open Graph; noindex en páginas de cuenta |
| Media | Catálogo | Contador público incluía ejercicios de borradores | Consulta limitada a ejercicios de unidades publicadas, con prueba de regresión |
| Media | Accesibilidad | Pestañas sin flechas/Home/End ni foco itinerante | Teclado y tabindex implementados; hash ajeno ya no oculta paneles |
| Media | Administración | Búsqueda sin botón explícito ni vacío; tablas difíciles de recorrer en pantallas pequeñas | Búsqueda, limpiar, vacío, captions, regiones desplazables y jerarquía visual |
| Media | Suscripción | Preapproval autorizado habilita acceso con próximo cobro + gracia; no es conciliación de pagos | Documentado. La nueva métrica no debe presentarse como facturación efectivamente cobrada |
| Media | Operaciones | Editar ejercicios elimina estado de esos ejercicios; force seed reemplaza contenido/progreso | No se ejecutaron sincronizaciones ni modificaciones sobre datos reales; respaldo y versionado antes de futuras migraciones |

## Arquitectura propuesta y aplicada

Inicio → método → programa A1–C2 con unidades y resúmenes → comparación demo/completo → preguntas → registro. Se mantiene el ejercicio gratuito sin cuenta. El temario se genera desde la base para respetar cambios editoriales y ocultar borradores. No se exponen respuestas ni material privado en ese temario.

Alumno: ingreso → test orientativo o selección de nivel → unidad → teoría / vocabulario / práctica → progreso → cuenta. Se mantiene este recorrido; el sistema visual compartido y las pestañas mejoran su coherencia. El audio sigue dependiendo del dispositivo; no se presenta como grabación docente.

Administrador: resumen de actividad → búsqueda de usuarios → acciones existentes / contenido → editor. Las métricas diferencian acceso de suscripción y explican el alcance del ingreso estimado. No se agregaron permisos, cobros ni accesos manuales en producción.

## Contenidos: siguiente fase editorial

La calidad no se resuelve multiplicando unidades automáticamente. Para ampliar el programa, cada unidad debería tener: objetivo observable, requisitos, situación de uso, explicación con contraste español/inglés, ejemplos contextualizados, vocabulario con colocaciones, comprensión de un texto o diálogo, práctica guiada, transferencia a una situación nueva y autoevaluación. Separar objetivos de gramática, léxico, lectura, escucha, producción escrita y oral.

Auditar los distractores antes de ampliar volumen: por ejemplo, en content/c2.php «___ you require further information, please contact us.» admite tanto «If» como «Should» entre las opciones actuales, aunque se califica solo «Should». Hay que especificar que se pide inversión formal o revisar opciones. No se ha aplicado un force seed porque borraría progreso real. Las correcciones editoriales deben migrarse conservando identificadores y con respaldo.

Priorizar A1/A2 con tareas de uso cotidiano y repaso acumulativo; luego B1/B2 con textos y diálogos más extensos; C1/C2 con registro, argumentación y matices. No anunciar horas, docentes, clases en vivo, evaluación de pronunciación, diplomas o resultados garantizados que el producto no respalda. El test de 24 preguntas de elección múltiple se presenta como orientación, no como certificación.

## Pendientes técnicos de la siguiente iteración

- Recuperación de contraseña: no hay flujo visible en el ingreso; diseñar entrega de email y tokens de un solo uso antes de añadir el enlace.
- Pagos: validar altas, reintentos, cobros rechazados, cancelación, eventos repetidos y asociación de preapproval con credenciales sandbox. No se realizaron transacciones reales ni se cambiaron webhooks.
- Configurar APP_URL con dominio definitivo antes de indexación. Registrar /sitemap.php en la herramienta del buscador. No anunciar sitemap.xml: el endpoint implementado es sitemap.php.
- Revisar la política de vinculación OAuth y errores con proveedores usando cuentas de prueba; no se cambió autenticación en este rediseño.
- Añadir paginación y filtros cuando crezca la base; la tabla informa el límite actual de 100 usuarios.
- Versionar ejercicios y respaldar restauraciones para que los cambios editoriales no borren progreso; un editor estructurado necesita validación de esquema y previsualización antes de reemplazar HTML/JSON.
- Revisar contraste, reflujo a 320/375 px, zoom 200%, teclado y lectores de pantalla en todos los flujos. La revisión visual hecha corresponde al navegador local disponible; no se declara conformidad WCAG completa.

## Verificación reproducible

Requisitos: PHP con pdo_sqlite, mbstring y Python. Ejecutar `python tests/smoke.py` desde la raíz. El script crea una base temporal propia, un servidor local y usuarios ficticios; no usa MySQL ni Mercado Pago. Limpia su servidor y archivos al terminar.

Casos comprobados: catálogo y contadores excluyen unidades no publicadas; metadata pública; validaciones de registro; CSRF inválido rechazado; redirección administrativa anónima; ingreso de administrador; separación manual/activo/vencido; búsqueda sin resultados; sitemap XML válido; robots responde; acción privilegiada sin CSRF rechazada.

Además: sintaxis de PHP en public/src, sintaxis JavaScript y navegador local para portada, temario desplegable y ejercicio de muestra (incorrecto → explicación; correcto → respuesta y bloqueo). No se probaron credenciales OAuth reales, cobros, MySQL de producción ni recuperación desde respaldo. La versión local usa PHP 8.5; Docker de producción declara 8.3, por lo que queda pendiente ejecutar la misma suite en esa imagen/MySQL.

## Publicación y reversión

Este cambio no añade tablas ni migra contenido. Publicar el código no requiere tocar registros. Evitar «Reemplazar todo con los archivos». Antes de publicar confirmar que el commit corresponde a esta revisión y que /health.php devuelve 200. Tras publicar comprobar portada, recursos, registro, acceso administrativo anónimo y sitemap. Si falla, revertir el commit o desplegar la versión previa en Railway; no restaurar una base porque estos cambios no requieren mutaciones de datos.
