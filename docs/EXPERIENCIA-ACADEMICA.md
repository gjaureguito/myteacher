# Experiencia académica y ampliación editorial

## Qué cambia en esta revisión

Esta revisión sustituye la dirección visual anterior y amplía la experiencia dentro de las unidades. Portada editorial en tinta, bordó y papel; tipografía serif; programa propio; biblioteca pública; panel del alumno con guía de sesión; y taller de aplicación en cada una de las 24 unidades originales.

No existe relación con Harvard ni con otra universidad. La referencia de exigencia académica se traduce en objetivos, contexto, evidencia, producción y revisión. No se usan escudos universitarios, diplomas, testimonios ni avales inventados.

## Contenido nuevo

24 talleres originales, con 24 lecturas o diálogos, 48 preguntas de comprensión con explicación, 96 entradas de glosario, 24 contrastes de lenguaje, 24 tareas de escritura y 24 respuestas modelo. Cada taller incluye tres criterios concretos de autoevaluación. Son 6.821 palabras en lecturas, modelos, estrategias, objetivos y consignas; esa cifra excluye glosarios y explicaciones. El material va desde presentaciones personales hasta registro académico, cautela argumentativa, atribución y precisión léxica.

Los seis talleres asociados a unidades publicadas en posición 1 tienen una versión abierta en Biblioteca, sin cuenta. El resto se integra dentro de la unidad y respeta las reglas existentes de acceso. Si una unidad pasa a borrador, su muestra deja de estar disponible y sale del sitemap. El programa público muestra objetivos y resúmenes, sin exponer la teoría o respuestas de ejercicios restringidos.

El diseño curricular toma como referencia el enfoque orientado a la acción y las actividades de recepción y producción del Consejo de Europa: https://www.coe.int/en/web/common-european-framework-reference-languages/the-user/learners-as-a-social-agent . Los textos son originales, no reproducciones de materiales del Consejo. No se afirma alineación validada, certificación ni cobertura completa del MCER.

## Dónde vive y cómo se conserva el progreso

Los talleres viven en `content/labs/<slug-de-unidad>.json`. `src/learning.php` los carga mediante una lista de caracteres permitidos para el slug. `public/unit.php` asocia cada taller con la unidad existente. No se ejecuta una siembra forzada, no se reemplazan ejercicios y no se cambian identificadores ni tablas.

La lectura tiene audio mediante la síntesis de voz ya existente. Las dos comprobaciones de comprensión son autónomas y no se mezclan con el progreso guardado de ejercicios. El borrador de escritura se mantiene solo en el documento abierto: contador de palabras y botón para copiar, sin envío ni persistencia. Esta limitación se explica junto al campo. Las respuestas modelo y las listas de revisión no se presentan como una calificación docente o una evaluación automática del escrito.

El inventario `/admin/learning.php` permite al administrador consultar el taller, su objetivo, extensión de lectura, cantidad de preguntas y enlace de revisión. Es una vista de consulta; la edición se realiza en los archivos versionados. El inventario incluye borradores claramente identificados; su contenido no se hace público.

## Contrato editorial

Cada JSON contiene slug, título, objetivo comunicativo, estrategia de lectura, texto, glosario, dos preguntas, consigna, modelo, tres puntos de revisión y un contraste comentado. Las preguntas tienen tres opciones distintas y un único índice de respuesta. Las explicaciones deben justificar la elección a partir del texto o la forma lingüística, no limitarse a declarar correcto/incorrecto.

Las tareas proponen producciones concretas. Cuando indican un rango de palabras, el modelo cumple ese rango. En el taller de colocaciones se incluyen dos versiones completas, informal y formal, con extensiones diferentes según la consigna. Los casos sobre estudios y proyectos se presentan como práctica o situaciones hipotéticas; no se anuncian resultados de investigaciones reales.

La revisión automatizada comprueba estructura, cobertura, duplicaciones, índices y extensiones; no constituye validación independiente de calidad lingüística o pedagógica. La revisión humana posterior puede profundizar la adecuación por nivel y ampliar comprensión auditiva con voces grabadas. El catálogo no equivale a un título universitario.

## Verificación

- `python tests/curriculum.py`: 24 talleres únicos, cobertura de unidades, 48 preguntas, glosarios, modelos y rangos de extensión.
- `python tests/smoke.py`: base SQLite temporal, una unidad despublicada, catálogo y contadores, muestras permitidas y restringidas, ruta inválida, sitemap, registro/CSRF, acceso de administrador, inventario y renderizado de las 23 unidades publicadas.
- Sintaxis PHP de public/src y sintaxis de ambos archivos JavaScript.
- Navegador: portada, biblioteca, lectura A1, respuesta incorrecta y correcta, explicación desplegada y contador de escritura. Revisión visual con marcos de 375 px para portada y 320 px para lectura. Estas comprobaciones no se presentan como una auditoría WCAG completa ni como prueba en dispositivos físicos.

## Publicación

No hay migración de datos ni actualización destructiva de contenido almacenado. Se despliega el código y los talleres versionados. Verificar en el dominio público la nueva portada, biblioteca, seis lecturas, programa, sitemap, salud y redirección administrativa anónima. No probar cobros o modificar cuentas reales. Para volver atrás, revertir el commit de esta revisión; no restaurar la base porque esta revisión no requiere mutaciones sobre sus registros.
