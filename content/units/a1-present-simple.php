<?php
return [
    'icon' => '⏰',
    'vocab' => [
        ['go to work', 'ir a trabajar'], ['finish work', 'terminar de trabajar'], ['cook dinner', 'cocinar la cena'], ['watch TV', 'mirar televisión'],
        ['go to bed', 'irse a dormir'], ['take a shower', 'darse una ducha'], ['do the shopping', 'hacer las compras'], ['sometimes', 'a veces'],
    ],
    'vocab_icons' => [
        'wake up' => '⏰', 'get up' => '🛏️', 'have breakfast' => '🥐', 'commute' => '🚇', 'usually' => '🔁', 'often' => '📆', 'never' => '🚫', 'weekend' => '🎉',
        'go to work' => '💼', 'finish work' => '🏁', 'cook dinner' => '🍳', 'watch TV' => '📺', 'go to bed' => '🌙', 'take a shower' => '🚿', 'do the shopping' => '🛒', 'sometimes' => '🤷',
    ],
    'readings' => [
        'day' => [
            'kind' => 'article',
            'title' => 'A day in the life of a baker',
            'text' => "Marco is a baker in Rosario. His day starts very early. He wakes up at three o'clock in the morning, takes a quick shower and goes to the bakery by bike. He doesn't have breakfast at home; he eats a croissant at work.\n\nMarco and his team make bread, medialunas and cakes. The shop opens at seven. Marco finishes work at eleven and has lunch with his wife, Silvia. In the afternoon he usually sleeps for two hours.\n\nMarco never goes out on weekdays because he goes to bed at nine. On Sundays the bakery is closed, so he plays football with his friends and cooks dinner for the family.",
        ],
    ],
    'exercises' => [
        ['image', '🚿', ['take a shower', 'cook dinner', 'go to bed'], 0, 'Take a shower: darse una ducha. En inglés usamos el verbo take.'],
        ['image', '🛒', ['watch TV', 'do the shopping', 'go to work'], 1, 'Do the shopping: hacer las compras. Ojo: se usa do, no make.'],
        ['image', '🌙', ['wake up', 'get up', 'go to bed'], 2, 'Go to bed: irse a dormir. Lo contrario es get up.'],
        ['match', 'Uní cada adverbio de frecuencia con su significado.', [['always', 'siempre'], ['usually', 'normalmente'], ['often', 'a menudo'], ['sometimes', 'a veces'], ['never', 'nunca']], 'De más a menos: always, usually, often, sometimes, never.'],

        ['listen', '¿A qué hora se levanta?', 'I usually get up at half past six, but on Saturdays I get up at nine.', ['6:30', '6:00', '9:30'], 0, '"Half past six" = 6:30. Los sábados se levanta a las nueve.'],
        ['listen', '¿Cómo va a trabajar Julia?', "Julia doesn't drive to work. She takes the train and then walks for ten minutes.", ['En auto', 'En tren y caminando', 'En bicicleta'], 1, '"She takes the train and then walks": tren y después camina.'],
        ['dictation', 'Escuchá y escribí la oración.', 'She never watches TV in the morning.', 'Never va antes del verbo y watch lleva -es con she: watches.'],

        ['reading', 'day', 'What time does Marco wake up?', ['At 3 am', 'At 7 am', 'At 11 am'], 0, '"He wakes up at three o\'clock in the morning."'],
        ['reading', 'day', 'Where does Marco have breakfast?', ['At home', 'At work', 'In a café'], 1, '"He doesn\'t have breakfast at home; he eats a croissant at work."'],
        ['reading', 'day', 'True or false? Marco goes out with friends on weekdays.', ['True', 'False'], 1, 'False: "Marco never goes out on weekdays because he goes to bed at nine."'],
        ['reading', 'day', 'What does Marco do on Sundays?', ['He works at the bakery.', 'He sleeps all day.', 'He plays football and cooks dinner.'], 2, 'Los domingos la panadería está cerrada: juega al fútbol y cocina la cena.'],

        ['speak', 'Contá tu rutina: decí esta oración en voz alta.', 'I usually get up at seven and have breakfast at home.', 'Pronunciá bien la /h/ aspirada de have y home.'],
        ['speak', 'Hacé la pregunta en voz alta.', 'What time do you finish work?', 'En preguntas con What time + do, la entonación baja al final.'],

        ['write', 'Describí un día típico tuyo de lunes a viernes: a qué hora te levantás, cómo vas a trabajar o estudiar, qué hacés a la noche. Usá al menos dos adverbios de frecuencia.',
            ['min' => 40, 'must' => [['\b(always|usually|often|sometimes|never)\b.*\b(always|usually|often|sometimes|never)\b', 'Usás al menos dos adverbios de frecuencia'], ["\b(at|around) (\d|one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve|half|quarter)", 'Decís a qué hora (at seven, at 8…)'], ["\b(don't|doesn't|do not|does not)\b", 'Incluís una oración negativa con don\'t/doesn\'t']],
             'model' => "On weekdays I always get up at seven o'clock. I take a shower and I usually have coffee and toast for breakfast. I don't drive to work; I take the bus. I start work at nine and I finish at six. In the evening I often cook dinner with my partner and we sometimes watch a series. I never go to bed after midnight."],
            'Recordá: con I/you/we/they el verbo no lleva -s; los adverbios van antes del verbo principal.'],
    ],
];
