<?php
// Ampliación de la unidad A1 · to be: vocabulario con imágenes, lectura, listening, speaking y writing.
return [
    'icon' => '👋',
    'vocab' => [
        ['student', 'estudiante'], ['doctor', 'médico/a'], ['friend', 'amigo/a'], ['neighbour', 'vecino/a'],
        ['single', 'soltero/a'], ['angry', 'enojado/a'], ['late', 'tarde / atrasado'], ['ready', 'listo/a'],
    ],
    'vocab_icons' => [
        'teacher' => '👩‍🏫', 'tired' => '😴', 'hungry' => '🍽️', 'cold' => '🥶', 'from' => '🌎', 'married' => '💍', 'busy' => '📅', 'happy' => '😊',
        'student' => '🎒', 'doctor' => '🩺', 'friend' => '🤝', 'neighbour' => '🏘️', 'single' => '🙋', 'angry' => '😠', 'late' => '⏰', 'ready' => '✅',
    ],
    'readings' => [
        'profile' => [
            'kind' => 'chat',
            'title' => 'Meet the new students',
            'text' => "Hi! I'm Lucía. I'm twenty-three and I'm from Córdoba, in Argentina. I'm a student at the university and I'm also a waitress at a café. I'm not married. My best friend is Tom.\n\nTom is from Dublin, in Ireland. He isn't a student; he's a nurse at a big hospital. He is very friendly but he is always tired because he works at night.\n\nWe are in the same English class on Mondays. Our teacher is Mrs Park. She's from Seoul and she is very patient. The class is small: we are only eight students.",
        ],
    ],
    'exercises' => [
        ['image', '🩺', ['teacher', 'doctor', 'student'], 1, 'Doctor: médico/a. El estetoscopio es el símbolo típico.'],
        ['image', '😴', ['happy', 'angry', 'tired'], 2, 'Tired: cansado/a. Se usa con to be: I am tired.'],
        ['image', '🥶', ['cold', 'hungry', 'late'], 0, 'Cold: frío / con frío. En español "tengo frío", en inglés "I am cold".'],
        ['match', 'Uní cada adjetivo con su significado.', [['busy', 'ocupado/a'], ['married', 'casado/a'], ['ready', 'listo/a'], ['angry', 'enojado/a'], ['late', 'tarde']], 'Estos adjetivos se combinan con to be: I am busy, we are late.'],

        ['listen', '¿De dónde es la persona?', "Hi, I'm Daniel. I'm from Canada, but my wife is from Mexico.", ['Mexico', 'Canada', 'Spain'], 1, 'Daniel dice "I\'m from Canada". La esposa es de México.'],
        ['listen', '¿Cómo está Anna?', "Anna isn't at work today. She's at home because she's ill.", ['Está en el trabajo.', 'Está enferma en casa.', 'Está de vacaciones.'], 1, '"She\'s at home because she\'s ill": está en casa porque está enferma.'],
        ['dictation', 'Escuchá y escribí la pregunta.', 'Are you a student?', 'Pregunta con to be: el verbo va antes del sujeto. Are you…?'],

        ['reading', 'profile', 'How old is Lucía?', ['21', '23', '33'], 1, 'El texto dice "I\'m twenty-three": tiene 23 años.'],
        ['reading', 'profile', 'What is Tom\'s job?', ['He is a student.', 'He is a waiter.', 'He is a nurse.'], 2, '"He isn\'t a student; he\'s a nurse at a big hospital."', 'hint' => 'Buscá el párrafo que habla de Tom.'],
        ['reading', 'profile', 'True or false? Tom is always tired.', ['True', 'False'], 0, 'True: "he is always tired because he works at night".'],
        ['reading', 'profile', 'True or false? There are twenty students in the class.', ['True', 'False'], 1, 'False: "we are only eight students".'],

        ['speak', 'Presentate: decí esta oración en voz alta.', "Hi, I'm Lucía. I'm from Argentina.", 'Unís sujeto y verbo con la contracción I\'m. Pronunciá la m final: /aɪm/.'],
        ['speak', 'Hacé la pregunta en voz alta.', 'Where are you from?', 'En las preguntas con palabra interrogativa la entonación baja al final.'],

        ['write', 'Escribí un mensaje para presentarte a tu clase de inglés: nombre, edad, de dónde sos, tu trabajo o estudio y cómo estás hoy.',
            ['min' => 30, 'must' => [["\\bI(\\s+a|')m\\b", 'Usás I am / I\'m'], ['\\byears? old\\b', 'Decís tu edad (… years old)'], ['\\bfrom\\b', 'Decís de dónde sos (from …)'], ["\\b(isn't|aren't|'m not|am not|is not|are not)\\b", 'Incluís al menos una negativa']],
             'model' => "Hi everyone! I'm Martín and I'm thirty years old. I'm from Mendoza, but I live in Buenos Aires now. I'm a software developer. I'm not married. Today I'm happy but a little tired because it's Monday!"],
            'Revisá que cada oración tenga sujeto y verbo: en inglés no se puede decir "Am from Mendoza" sin I.'],
    ],
];
