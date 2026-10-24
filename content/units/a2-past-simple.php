<?php
return [
    'icon' => '🧳',
    'vocab' => [
        ['flight', 'vuelo'], ['luggage', 'equipaje'], ['arrive', 'llegar'], ['leave', 'salir / irse (left)'],
        ['lose', 'perder (lost)'], ['find', 'encontrar (found)'], ['meet', 'conocer / encontrarse (met)'], ['spend', 'gastar / pasar tiempo (spent)'],
    ],
    'vocab_icons' => [
        'yesterday' => '📅', 'last week' => '🗓️', 'ago' => '⏪', 'went' => '🚶', 'bought' => '🛍️', 'brought' => '🎁', 'thought' => '💭', 'trip' => '🧳',
        'flight' => '✈️', 'luggage' => '🧳', 'arrive' => '🛬', 'leave' => '🛫', 'lose' => '❓', 'find' => '🔍', 'meet' => '🤝', 'spend' => '💸',
    ],
    'readings' => [
        'trip' => [
            'kind' => 'story',
            'title' => 'The worst start to a holiday',
            'text' => "Last summer my husband and I went to Brazil for two weeks. We left Buenos Aires very early on a Saturday morning. The flight was fine, but when we arrived in Florianópolis, our luggage didn't arrive with us. We waited for an hour at the airport and then we filled in a form.\n\nWe didn't have any clothes, so we spent the first afternoon at a shopping centre. I bought a swimsuit and a pair of sandals, and my husband bought three T-shirts. In the evening we met some Argentinian tourists at the hotel and they lent us a phone charger.\n\nTwo days later, the airline phoned us: they found our bags in São Paulo! In the end, the holiday was wonderful, and now we always pack a change of clothes in our hand luggage.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada verbo con su pasado irregular.', [['go', 'went'], ['buy', 'bought'], ['leave', 'left'], ['meet', 'met'], ['find', 'found'], ['spend', 'spent']], 'Los irregulares hay que memorizarlos: conviene aprenderlos en grupos por sonido (bought, brought, thought).'],
        ['image', '🛬', ['leave', 'arrive', 'lose'], 1, 'Arrive: llegar. Arrive in (ciudad/país), arrive at (lugar puntual).'],
        ['image', '🧳', ['flight', 'luggage', 'trip'], 1, 'Luggage: equipaje. Es incontable: a piece of luggage.'],
        ['mcq', 'Choose the word: I ___ my keys yesterday and I couldn\'t open the door.', ['lost', 'spent', 'met'], 0, 'Lose → lost: perder.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué hizo el fin de semana?', 'On Saturday I visited my aunt in La Plata and on Sunday I stayed at home and watched a film.', ['Fue a La Plata el domingo', 'Visitó a su tía el sábado', 'Fue al cine'], 1, '"On Saturday I visited my aunt in La Plata."'],
        ['listen', '¿Por qué llegó tarde?', "Sorry I'm late. I missed the bus and the next one didn't come for twenty minutes.", ['Se quedó dormido', 'Perdió el colectivo', 'Había tráfico'], 1, '"I missed the bus."'],
        ['dictation', 'Escuchá y escribí la pregunta.', 'Did you enjoy the concert?', 'Pregunta en pasado: Did + sujeto + verbo base.'],

        ['reading', 'trip', 'What was the problem in Florianópolis?', ['They missed the flight.', 'Their luggage didn\'t arrive.', 'The hotel was closed.'], 1, '"Our luggage didn\'t arrive with us."'],
        ['reading', 'trip', 'What did they do on the first afternoon?', ['They went to the beach.', 'They went shopping for clothes.', 'They stayed at the airport.'], 1, '"We spent the first afternoon at a shopping centre."'],
        ['reading', 'trip', 'True or false? The airline found the bags in São Paulo.', ['True', 'False'], 0, 'True: "they found our bags in São Paulo!"'],
        ['reading', 'trip', 'What do they always do now?', ['They travel without luggage.', 'They pack extra clothes in their hand luggage.', 'They never fly to Brazil.'], 1, '"We always pack a change of clothes in our hand luggage."'],

        ['speak', 'Contá qué hiciste: decí la oración en voz alta.', 'Last weekend I went to the beach and I bought a new hat.', 'Bought se pronuncia /bɔːt/: la gh no suena.'],
        ['speak', 'Preguntá en voz alta.', 'What did you do yesterday?', 'Did you se une: /dɪdʒu/.'],

        ['write', 'Contá un viaje o un día en el que algo salió mal: qué pasó, qué hicieron y cómo terminó. Usá conectores de secuencia.',
            ['min' => 60, 'must' => [['\b(went|had|got|saw|left|bought|took|came|lost|found|made|met|spent)\b', 'Usás verbos irregulares en pasado'], ['\b\w+ed\b', 'Usás verbos regulares en pasado (-ed)'], ["\b(didn't|did not)\b", 'Incluís una negativa con didn\'t'], ['\b(first|then|after that|later|in the end|finally)\b', 'Ordenás la historia (first, then, in the end…)']],
             'model' => "Two years ago I travelled to Mendoza with my best friend. First, our bus left two hours late because of a storm. Then, when we arrived, we discovered that the hostel didn't have our reservation! We walked around the city with our backpacks for an hour. After that, a kind woman at a café recommended a small hotel near the park. It was cheap and very comfortable. In the end, we stayed there for the whole week and we loved it."],
            'En negativas y preguntas el verbo vuelve a la forma base: didn\'t go, did you buy.'],
    ],
];
