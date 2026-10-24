<?php
return [
    'icon' => '🔮',
    'vocab' => [
        ['decide', 'decidir'], ['save money', 'ahorrar plata'], ['move house', 'mudarse'], ['get married', 'casarse'],
        ['weather forecast', 'pronóstico del tiempo'], ['predict', 'predecir'], ['offer', 'ofrecer'], ['tomorrow', 'mañana'],
    ],
    'vocab_icons' => [
        'plan' => '📝', 'promise' => '🤞', 'probably' => '🤔', 'tonight' => '🌙', 'next month' => '📆', 'book (a table)' => '📞', "won't" => '🙅', 'soon' => '⏳',
        'decide' => '✔️', 'save money' => '🐷', 'move house' => '📦', 'get married' => '💒', 'weather forecast' => '🌦️', 'predict' => '🔮', 'offer' => '🙋', 'tomorrow' => '🌅',
    ],
    'readings' => [
        'plans' => [
            'kind' => 'email',
            'title' => 'Big news!',
            'text' => "Hi Mati,\n\nI have some big news: Fede and I are going to move to Montevideo next March! Fede got a job at a technology company there, and I'm going to work online, so I can live anywhere.\n\nWe aren't going to sell our flat in Buenos Aires; we're going to rent it. We're going to save money this year because the first months will probably be expensive.\n\nI'm sure you'll love Montevideo. It's quieter than Buenos Aires and the beach is right there. I promise I'll cook you an asado when you visit us! Don't worry, I won't forget your birthday in October: I'll call you.\n\nAre you going to come to our goodbye party on the 20th?\n\nLove,\nLaura",
        ],
    ],
    'exercises' => [
        ['image', '📦', ['get married', 'move house', 'save money'], 1, 'Move house: mudarse. También se dice move a secas: We\'re moving.'],
        ['image', '🐷', ['save money', 'spend money', 'offer'], 0, 'Save money: ahorrar.'],
        ['image', '🌦️', ['weather forecast', 'tonight', 'predict'], 0, 'Weather forecast: pronóstico. "The forecast says it will rain."'],
        ['match', 'Uní cada situación con la forma de futuro más natural.', [['Un plan ya decidido', 'going to'], ['Una decisión en el momento', 'will'], ['Una promesa', "I'll…"], ['Una predicción con evidencia visible', "It's going to…"]], 'Going to: planes y evidencia. Will: decisiones del momento, promesas y opiniones.'],

        ['listen', '¿Qué va a hacer el fin de semana?', "This weekend I'm going to paint my bedroom. I bought the paint yesterday.", ['Va a pintar su cuarto.', 'Va a comprar pintura.', 'Va a viajar.'], 0, '"I\'m going to paint my bedroom": un plan decidido.'],
        ['listen', '¿Qué ofrece la persona?', "You look tired. Don't worry, I'll wash the dishes.", ['Cocinar', 'Lavar los platos', 'Hacer las compras'], 1, '"I\'ll wash the dishes": oferta espontánea con will.'],
        ['dictation', 'Escuchá y escribí la oración.', "I think it will rain tomorrow.", 'Predicción/opinión con I think + will.'],

        ['reading', 'plans', 'Where are Laura and Fede going to live?', ['In Buenos Aires', 'In Montevideo', 'In Punta del Este'], 1, '"We are going to move to Montevideo next March."'],
        ['reading', 'plans', 'What are they going to do with their flat?', ['Sell it', 'Rent it', 'Paint it'], 1, '"We aren\'t going to sell our flat; we\'re going to rent it."'],
        ['reading', 'plans', 'True or false? Laura promises to cook for Mati.', ['True', 'False'], 0, 'True: "I promise I\'ll cook you an asado".'],
        ['reading', 'plans', 'Why are they saving money?', ['To buy a car', 'Because the first months will probably be expensive', 'To pay for the party'], 1, '"The first months will probably be expensive."'],

        ['speak', 'Contá un plan en voz alta.', "I'm going to start a new course next month.", 'Going to en el habla rápida suena "gonna" /ˈɡənə/.'],
        ['speak', 'Hacé una promesa en voz alta.', "I promise I'll call you tomorrow.", "I'll suena /aɪl/, casi como 'isle'."],

        ['write', 'Escribile a un amigo sobre tus planes para el año que viene: qué vas a hacer, qué creés que va a pasar y una promesa.',
            ['min' => 60, 'must' => [["\b(am|is|are|'m|'s|'re) going to\b", 'Usás going to para planes'], ["\b(will|'ll)\b", 'Usás will'], ["\b(won't|will not|(isn't|aren't|'m not|not) going to)\b", 'Incluís una negativa en futuro'], ['\b(promise|probably|i think|definitely)\b', 'Usás promise, probably o I think']],
             'model' => "Hi Juli! Next year is going to be very busy. I'm going to start a programming course in March and I'm going to look for a new job in September. I think it will be difficult, but I'm excited. My sister is going to have a baby in May, so I'll probably travel to Córdoba to help her. I'm not going to buy a car this year because I want to save money. I promise I'll visit you in Rosario before the winter!"],
            'Going to para lo que ya decidiste; will para promesas, ofertas y lo que creés que va a pasar.'],
    ],
];
