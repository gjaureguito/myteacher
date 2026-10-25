<?php
declare(strict_types=1);

/** Versioned original explanations; independent of stored exercise IDs and progress. */
function study_chapter(string $slug): ?array
{
    static $chapters;
    $chapters ??= json_decode(file_get_contents(ROOT . '/content/books/chapters.json'), true, 512, JSON_THROW_ON_ERROR);
    return $chapters[$slug] ?? null;
}

function study_level(string $code): ?array
{
    static $levels;
    $levels ??= [
        'a1' => [
            'title' => 'Primeras conversaciones',
            'intro' => 'Empezamos con oraciones breves y completas. Aprenderás a presentarte, hablar de tu familia y tu rutina, describir lugares y pedir lo que necesitás. Cada capítulo conecta una forma gramatical con una situación cotidiana y te pide producir tus propios mensajes.',
            'before' => 'No necesitás conocimientos previos de gramática inglesa. Tené un cuaderno para anotar vocabulario con una frase propia y escuchá los ejemplos antes de intentar repetirlos.',
            'outcomes' => ['Presentarte y preguntar por datos personales.', 'Describir hábitos, acciones actuales, capacidades y gustos.', 'Pedir cantidades, dar indicaciones y describir estados pasados.'],
            'project' => 'Prepará una guía personal de 120–160 palabras para recibir a un visitante: quién sos, cómo es tu barrio, qué hacés habitualmente, qué pueden hacer juntos y cómo llegar a un lugar. Presentala oralmente durante un minuto.',
            'review' => ['¿Cada oración tiene un sujeto y un verbo?', '¿Distingo una rutina de una acción que sucede ahora?', '¿Uso auxiliares y formas negativas sin duplicarlos?', '¿Puedo responder preguntas personales sin leer?'],
        ],
        'a2' => [
            'title' => 'Historias, decisiones y planes',
            'intro' => 'Pasamos de describir a narrar y comparar. Vas a organizar acontecimientos, crear el fondo de una historia, justificar elecciones y explicar planes. Trabajá con secuencias: una oración aislada puede ser correcta y aun así no contar bien una historia.',
            'before' => 'Repasá be, presente simple y continuo, preguntas y expresiones de cantidad. Necesitás distinguir sujeto, verbo y complemento para comparar las formas del pasado.',
            'outcomes' => ['Narrar una experiencia con una secuencia comprensible.', 'Combinar acontecimientos con acciones en desarrollo.', 'Comparar opciones y distinguir planes, ofrecimientos y predicciones.'],
            'project' => 'Escribí una crónica de 150–180 palabras de un viaje real o imaginario. Incluí lo que ocurrió, el contexto, una comparación entre dos opciones y un plan para una próxima visita. Contala en dos minutos.',
            'review' => ['¿Después de did uso el verbo base?', '¿La secuencia temporal se entiende sin adivinar?', '¿Elijo pasado simple o continuo por el significado?', '¿Puedo justificar una elección con comparativos?'],
        ],
        'b1' => [
            'title' => 'Experiencias y explicaciones',
            'intro' => 'El objetivo es desarrollar una idea más allá de una respuesta breve. Vas a relacionar pasado y presente, explicar condiciones, describir procesos y distinguir reglas de consejos. Prestá atención a qué sabe tu lector y a qué información necesita para seguirte.',
            'before' => 'Repasá pasado simple, formas futuras y participios frecuentes. Antes de avanzar, comprobá que podés narrar un episodio y formular preguntas sobre él.',
            'outcomes' => ['Vincular experiencias con un contexto temporal claro.', 'Explicar condiciones y consecuencias posibles.', 'Describir procesos y formular obligaciones, permisos y consejos.'],
            'project' => 'Redactá una guía de 180–220 palabras para incorporarse a una actividad comunitaria. Presentá tu experiencia, explicá cómo funciona la actividad y sus reglas, y proponé alternativas si cambia el plan. Respondé preguntas sobre la guía.',
            'review' => ['¿Distingo un período terminado de una experiencia hasta ahora?', '¿La condición y su consecuencia son coherentes?', '¿La pasiva tiene be y participio?', '¿Diferencio prohibición de ausencia de obligación?'],
        ],
        'b2' => [
            'title' => 'Hipótesis y perspectivas',
            'intro' => 'Trabajamos con decisiones, mensajes ajenos y descripciones complejas. Una respuesta eficaz mantiene consistencia temporal y hace explícitas las relaciones entre sus partes. Vas a reformular ideas sin cambiar involuntariamente su significado.',
            'before' => 'Necesitás manejar los tiempos básicos, el presente perfecto, la pasiva y la estructura de una cláusula. Repasá también participios e infinitivos.',
            'outcomes' => ['Distinguir hipótesis presentes de alternativas pasadas.', 'Transmitir mensajes adaptando referencias y atribución.', 'Usar relativas y patrones verbales para construir textos precisos.'],
            'project' => 'Escribí un informe de 220–260 palabras sobre una decisión de un equipo ficticio. Resumí lo que dijeron dos participantes, identificá propuestas con relativas y explorá una alternativa pasada y una actual. Defendé tu recomendación oralmente.',
            'review' => ['¿La hipótesis corresponde al período que imagino?', '¿El discurso indirecto conserva quién dijo qué y cuándo?', '¿Las relativas identifican o agregan información con claridad?', '¿Los complementos verbales mantienen el significado previsto?'],
        ],
        'c1' => [
            'title' => 'Argumentación y control del énfasis',
            'intro' => 'La precisión incluye organizar un argumento y decidir qué destacar. Vamos a trabajar con inversión, relaciones temporales complejas, atribución y cohesión. Elegí estructuras por su función: un texto avanzado no necesita que cada oración parezca complicada.',
            'before' => 'Repasá condicionales, pasivas, discurso indirecto y relaciones entre cláusulas. Debés poder sostener un argumento con ejemplos y una objeción.',
            'outcomes' => ['Controlar el énfasis según propósito y registro.', 'Relacionar causas y resultados de distintos períodos.', 'Atribuir información y construir argumentos cohesionados.'],
            'project' => 'Escribí una propuesta de 260–320 palabras sobre una mejora educativa. Incluí diagnóstico, evidencia ficticia identificada como tal, una objeción y una respuesta. Usá énfasis selectivo, atribución y una consecuencia contrafactual.',
            'review' => ['¿Cada estructura enfática cumple una función?', '¿Las relaciones temporales son interpretables?', '¿Distingo afirmaciones propias de información atribuida?', '¿Cada párrafo desarrolla una idea y enlaza con el siguiente?'],
        ],
        'c2' => [
            'title' => 'Matices, registro y estilo',
            'intro' => 'El trabajo se centra en decisiones de lenguaje: fuerza de una afirmación, foco informativo, colocaciones y formulaciones institucionales. Vas a comparar versiones y justificar cambios según destinatario, evidencia y propósito.',
            'before' => 'Necesitás control flexible de las estructuras anteriores y experiencia revisando textos extensos. Tené a mano tus propias lecturas para registrar combinaciones léxicas en contexto.',
            'outcomes' => ['Formular recomendaciones sin ambigüedad de alcance.', 'Cambiar el foco conservando el contenido esencial.', 'Ajustar certeza, colocaciones y registro a distintas audiencias.'],
            'project' => 'Prepará un informe crítico de 300–350 palabras y una adaptación pública de 120 palabras sobre una propuesta ficticia. Diferenciá hechos, inferencias y recomendaciones; justificá cinco cambios de registro entre las versiones.',
            'review' => ['¿El alcance de mis afirmaciones coincide con la evidencia?', '¿El foco conduce al lector hacia la información relevante?', '¿Las combinaciones léxicas resultan naturales y adecuadas?', '¿Puedo simplificar el registro sin deformar el argumento?'],
        ],
    ];
    return $levels[$code] ?? null;
}
