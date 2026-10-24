<?php
return [
    'icon' => '🔀',
    'vocab' => [
        ['boil', 'hervir'], ['freeze', 'congelarse'], ['fail', 'reprobar / fracasar'], ['pass (an exam)', 'aprobar'],
        ['get lost', 'perderse'], ['run out of', 'quedarse sin'], ['charge', 'cargar (batería)'], ['on time', 'a tiempo / puntual'],
    ],
    'vocab_icons' => [
        'unless' => '🚧', 'as soon as' => '⚡', 'melt' => '🫠', 'miss (the bus)' => '🚌', 'hurry up' => '🏃', 'get a discount' => '🏷️', 'in case' => '☂️', 'until' => '⏳',
        'boil' => '♨️', 'freeze' => '🧊', 'fail' => '❌', 'pass (an exam)' => '🎓', 'get lost' => '🧭', 'run out of' => '🪫', 'charge' => '🔌', 'on time' => '⏰',
    ],
    'readings' => [
        'tips' => [
            'kind' => 'article',
            'title' => 'Ten minutes that can save your exam',
            'text' => "Every year thousands of students fail exams not because they don't know the answers, but because they panic. Here's some advice from Dr Silvia Rey, a psychologist who works with university students.\n\n\"If you sleep less than six hours the night before, your memory doesn't work as well. So don't stay up until three in the morning studying,\" she says. \"If you feel nervous at the start of the exam, close your eyes and breathe slowly for one minute. It sounds simple, but it works.\"\n\nDr Rey also recommends reading all the questions first. \"If you start with the questions you know, you'll feel more confident. Unless the exam has a strict order, leave the difficult questions until the end.\"\n\nFinally, take a bottle of water and a snack in case the exam is long. \"If your body is hungry, your brain gets tired very quickly.\"",
        ],
    ],
    'exercises' => [
        ['image', '🪫', ['charge', 'run out of', 'freeze'], 1, 'Run out of: quedarse sin. "My phone has run out of battery."'],
        ['image', '🧊', ['boil', 'melt', 'freeze'], 2, 'Freeze: congelarse. Water freezes at 0°C.'],
        ['match', 'Uní cada conector con su significado.', [['unless', 'a menos que'], ['as soon as', 'en cuanto'], ['in case', 'por si'], ['until', 'hasta que'], ['if', 'si']], 'Conectores que se usan con condicionales y tiempo futuro.'],
        ['mcq', 'Choose the word: Take an umbrella ___ it rains.', ['in case', 'unless', 'until'], 0, 'In case: por si acaso (precaución).', 'skill' => 'vocabulary'],

        ['listen', '¿Qué pasa si no se apura?', "If we don't hurry up, we'll miss the last train home.", ['Van a perder el último tren.', 'Van a llegar temprano.', 'Van a tomar un taxi.'], 0, '"We\'ll miss the last train home."'],
        ['listen', '¿Cuándo va a llamar?', "I'll call you as soon as I arrive at the hotel.", ['Antes de salir', 'En cuanto llegue al hotel', 'Mañana a la mañana'], 1, '"As soon as I arrive at the hotel."'],
        ['dictation', 'Escuchá y escribí la oración.', "If you heat ice, it melts.", 'Condicional cero (verdad general): presente + presente.'],

        ['reading', 'tips', 'According to Dr Rey, why do many students fail?', ['They don\'t study enough.', 'They panic.', 'The exams are too long.'], 1, '"Not because they don\'t know the answers, but because they panic."'],
        ['reading', 'tips', 'What should you do if you feel nervous at the start?', ['Leave the room.', 'Close your eyes and breathe slowly.', 'Start with the difficult questions.'], 1, '"Close your eyes and breathe slowly for one minute."'],
        ['reading', 'tips', 'True or false? She recommends answering the difficult questions first.', ['True', 'False'], 1, 'False: "leave the difficult questions until the end".'],
        ['reading', 'tips', 'Why take a snack?', ['In case the exam is long', 'Because it\'s allowed', 'To share with other students'], 0, '"Take a bottle of water and a snack in case the exam is long."'],

        ['speak', 'Decí el condicional en voz alta.', "If it rains tomorrow, we'll stay at home.", "We'll se reduce: /wiːl/. La coma marca una pequeña pausa."],
        ['speak', 'Decí la oración en voz alta.', "I won't go unless you come with me.", "Won't /wəʊnt/ es diferente de want /wɒnt/."],

        ['write', 'Un amigo va a rendir un examen importante. Escribile un mensaje con consejos: qué pasa si hace ciertas cosas y qué debería evitar.',
            ['min' => 70, 'must' => [["\bif\b[^.]*\b(will|'ll|won't)\b", 'Usás el primer condicional (if + presente, will)'], ['\bunless\b', 'Usás unless'], ['\b(as soon as|in case|until|when)\b', 'Usás as soon as, in case, until o when'], ["\b(don't|do not)\b", 'Incluís un consejo negativo (don\'t…)']],
             'model' => "Hi Caro! Good luck tomorrow! Here are some tips. If you go to bed early tonight, you'll remember things better. Don't drink too much coffee, because if you drink a lot, you'll feel nervous. Take a sandwich in case the exam is very long. When you get the exam, read all the questions first. You won't finish on time unless you organise your time well. Text me as soon as you leave the classroom!"],
            'Después de if, unless, when y as soon as usá presente aunque hables del futuro.'],
    ],
];
