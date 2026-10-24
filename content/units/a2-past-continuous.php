<?php
return [
    'icon' => '🌩️',
    'vocab' => [
        ['storm', 'tormenta'], ['power cut', 'corte de luz'], ['slip', 'resbalarse'], ['drop', 'dejar caer / caerse algo'],
        ['notice', 'darse cuenta / notar'], ['shout', 'gritar'], ['run away', 'escaparse'], ['knock on the door', 'golpear la puerta'],
    ],
    'vocab_icons' => [
        'while' => '⏱️', 'suddenly' => '💥', 'break down' => '🚗', 'ring (rang)' => '📞', 'fall asleep' => '😴', 'happen' => '❗', 'accident' => '🚑', 'at that moment' => '🎯',
        'storm' => '⛈️', 'power cut' => '🕯️', 'slip' => '🍌', 'drop' => '⬇️', 'notice' => '👀', 'shout' => '🗣️', 'run away' => '🏃', 'knock on the door' => '🚪',
    ],
    'readings' => [
        'night' => [
            'kind' => 'story',
            'title' => 'The night the lights went out',
            'text' => "It was a hot night in January. My parents were watching a film in the living room, my brother was playing video games and I was reading in bed. Suddenly, there was a loud noise and all the lights went out. A big storm was passing over the city.\n\nWhile my father was looking for candles in the kitchen, he slipped on some water and dropped the box of matches. My brother was shouting because he lost his game. Then somebody knocked on the door. We were all a bit scared!\n\nIt was our neighbour, Doña Elsa. She was holding a torch and a plate of empanadas. \"I was cooking when the power cut happened,\" she said. \"I didn't want to eat alone.\" We ate together by candlelight and it was one of the best nights of that summer.",
        ],
    ],
    'exercises' => [
        ['image', '⛈️', ['storm', 'accident', 'power cut'], 0, 'Storm: tormenta.'],
        ['image', '🕯️', ['storm', 'power cut', 'slip'], 1, 'Power cut: corte de luz. "There was a power cut last night."'],
        ['match', 'Uní cada verbo con su significado.', [['slip', 'resbalarse'], ['drop', 'dejar caer'], ['notice', 'darse cuenta'], ['shout', 'gritar'], ['fall asleep', 'quedarse dormido']], 'Verbos útiles para narrar historias.'],
        ['mcq', 'Choose the word: The car ___ on the motorway, so we called a mechanic.', ['broke down', 'fell asleep', 'ran away'], 0, 'Break down: romperse (un auto o una máquina).', 'skill' => 'vocabulary'],

        ['listen', '¿Qué estaba haciendo cuando sonó el teléfono?', 'I was having a shower when the phone rang, so I didn\'t answer it.', ['Estaba cocinando', 'Se estaba duchando', 'Estaba durmiendo'], 1, '"I was having a shower when the phone rang."'],
        ['listen', '¿Qué pasó mientras caminaba?', 'While I was walking to work, I saw an old friend from school.', ['Perdió el colectivo', 'Vio a un amigo de la escuela', 'Empezó a llover'], 1, '"While I was walking to work, I saw an old friend from school."'],
        ['dictation', 'Escuchá y escribí la oración.', 'What were you doing at nine o\'clock?', 'Pregunta en pasado continuo: were + sujeto + -ing.'],

        ['reading', 'night', 'What was the narrator doing when the lights went out?', ['Watching a film', 'Reading in bed', 'Playing video games'], 1, '"I was reading in bed."'],
        ['reading', 'night', 'What happened to the father?', ['He fell asleep.', 'He slipped and dropped the matches.', 'He went to the neighbour\'s house.'], 1, '"He slipped on some water and dropped the box of matches."'],
        ['reading', 'night', 'True or false? Doña Elsa was watching TV when the power cut happened.', ['True', 'False'], 1, 'False: "I was cooking when the power cut happened."'],
        ['reading', 'night', 'How did the night end?', ['Badly: everybody was scared.', 'They ate empanadas together by candlelight.', 'The lights came back immediately.'], 1, '"We ate together by candlelight and it was one of the best nights."'],

        ['speak', 'Contá qué estabas haciendo en voz alta.', 'I was cooking dinner when the lights went out.', 'Was cooking: was débil /wəz/, acento en COOK.'],
        ['speak', 'Preguntá en voz alta.', 'What were you doing when it happened?', 'Were you se une: /wəju/.'],

        ['write', 'Contá una anécdota con algo inesperado: qué estaban haciendo las personas cuando pasó y qué pasó después.',
            ['min' => 60, 'must' => [["\b(was|were) \w+ing\b", 'Usás el pasado continuo (was/were + -ing)'], ['\b(when|while)\b', 'Usás when o while'], ['\b(suddenly|at that moment|then|in the end)\b', 'Usás suddenly, then o in the end'], ['\b(went|saw|heard|came|fell|broke|rang|said|took|got)\b', 'Usás pasado simple de verbos irregulares']],
             'model' => "Last winter I was travelling by train to Mar del Plata. It was raining and most people were sleeping or reading. I was listening to music when suddenly the train stopped in the middle of the countryside. Nobody knew what was happening. While we were waiting, a woman next to me started to talk and we discovered that we went to the same school twenty years ago! Two hours later the train started again. In the end, we became good friends."],
            'El continuo describe la escena de fondo; el simple, la acción que la interrumpe.'],
    ],
];
