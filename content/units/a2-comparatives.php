<?php
return [
    'icon' => '⚖️',
    'vocab' => [
        ['big', 'grande'], ['small', 'chico/a'], ['fast', 'rápido/a'], ['slow', 'lento/a'],
        ['noisy', 'ruidoso/a'], ['friendly', 'amable'], ['comfortable', 'cómodo/a'], ['dangerous', 'peligroso/a'],
    ],
    'vocab_icons' => [
        'cheap' => '🪙', 'expensive' => '💎', 'crowded' => '👥', 'quiet' => '🤫', 'safe' => '🛡️', 'better' => '👍', 'worse' => '👎', 'than' => '⚖️',
        'big' => '🐘', 'small' => '🐭', 'fast' => '🐆', 'slow' => '🐢', 'noisy' => '📢', 'friendly' => '🤗', 'comfortable' => '🛋️', 'dangerous' => '⚠️',
    ],
    'readings' => [
        'cities' => [
            'kind' => 'article',
            'title' => 'Córdoba or Rosario? Two students compare',
            'text' => "Lautaro moved from Rosario to Córdoba to study engineering. \"Córdoba is bigger than Rosario and it has more universities,\" he says. \"The nightlife is also better here, and the mountains are only an hour away. But rent is more expensive and the buses are slower.\"\n\nHis cousin Julieta did the opposite: she left Córdoba for Rosario. \"For me, Rosario is the most beautiful city in Argentina because of the river,\" she explains. \"It's quieter than Córdoba and the people are very friendly. The worst thing is the humidity in summer: it's hotter and more uncomfortable than Córdoba.\"\n\nBoth agree on one thing: the best food in the country is at their grandmother's house in Villa María.",
        ],
    ],
    'exercises' => [
        ['image', '🐢', ['fast', 'slow', 'safe'], 1, 'Slow: lento. Comparativo: slower.'],
        ['image', '📢', ['quiet', 'noisy', 'friendly'], 1, 'Noisy: ruidoso. Comparativo: noisier (la y cambia por i).'],
        ['match', 'Uní cada adjetivo con su opuesto.', [['cheap', 'expensive'], ['quiet', 'noisy'], ['safe', 'dangerous'], ['fast', 'slow'], ['big', 'small']], 'Aprender adjetivos en pares de opuestos ayuda a recordarlos.'],
        ['match', 'Uní cada adjetivo con su comparativo.', [['good', 'better'], ['bad', 'worse'], ['big', 'bigger'], ['easy', 'easier'], ['comfortable', 'more comfortable']], 'Good y bad son irregulares; big duplica la g; easy cambia y por i; los largos usan more.'],

        ['listen', '¿Cuál departamento es más barato?', 'The flat in Almagro is cheaper than the one in Belgrano, but it\'s smaller and noisier.', ['El de Belgrano', 'El de Almagro', 'Cuestan lo mismo'], 1, '"The flat in Almagro is cheaper than the one in Belgrano."'],
        ['listen', '¿Cuál es el restaurante preferido?', "Of all the restaurants in the area, La Esquina is the best. It isn't the cheapest, but the food is amazing.", ['La Esquina, porque es el más barato', 'La Esquina, por la comida', 'Otro restaurante'], 1, '"It isn\'t the cheapest, but the food is amazing."'],
        ['dictation', 'Escuchá y escribí la oración.', 'My new job is better than my old one.', 'Good → better (irregular). Than introduce la comparación.'],

        ['reading', 'cities', 'Why did Lautaro move to Córdoba?', ['To work in the mountains', 'To study engineering', 'To live with his grandmother'], 1, '"Lautaro moved from Rosario to Córdoba to study engineering."'],
        ['reading', 'cities', 'According to Lautaro, what is worse in Córdoba?', ['The nightlife', 'The rent and the buses', 'The universities'], 1, '"Rent is more expensive and the buses are slower."'],
        ['reading', 'cities', 'True or false? Julieta thinks Rosario is noisier than Córdoba.', ['True', 'False'], 1, 'False: "It\'s quieter than Córdoba."'],
        ['reading', 'cities', 'What do both cousins agree on?', ['Rosario has the best river.', 'Their grandmother cooks the best food.', 'Córdoba is the most beautiful city.'], 1, '"The best food in the country is at their grandmother\'s house."'],

        ['speak', 'Compará en voz alta.', 'The train is faster and cheaper than the bus.', 'Than suena débil: /ðən/.'],
        ['speak', 'Decí el superlativo en voz alta.', "It's the most beautiful city in the country.", 'Most beautiful: acentuá BEAU: /ˈbjuːtɪfəl/.'],

        ['write', 'Compará tu ciudad o barrio con otro que conozcas: precios, transporte, ruido, gente. Terminá diciendo cuál es el mejor lugar para vivir y por qué.',
            ['min' => 60, 'must' => [['\b\w+er than\b', 'Usás un comparativo corto (-er than)'], ['\bmore \w+ than\b', 'Usás more … than'], ['\bthe (\w+est|most \w+|best|worst)\b', 'Usás un superlativo (the …est / the most / the best)'], ['\b(better|worse)\b', 'Usás better o worse']],
             'model' => "I live in Quilmes, but I work in the centre of Buenos Aires. Quilmes is quieter than the centre and the streets are greener. Rent is also cheaper, so my flat is bigger than my friends' flats in the city. On the other hand, the transport is worse: the train is slower and more crowded than the subway. For me, the best place to live is Quilmes, because it's the most relaxing place after a long day."],
            'Adjetivos de una sílaba: -er / the -est. Adjetivos largos: more / the most.'],
    ],
];
