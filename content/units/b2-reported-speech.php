<?php
return [
    'icon' => '💬',
    'vocab' => [
        ['claim', 'afirmar / sostener'], ['insist', 'insistir'], ['complain', 'quejarse'], ['remind', 'recordarle a alguien'],
        ['announce', 'anunciar'], ['suggest', 'sugerir'], ['according to', 'según'], ['spokesperson', 'vocero/a'],
    ],
    'vocab_icons' => [
        'ask' => '❓', 'tell' => '🗨️', 'explain' => '🧑‍🏫', 'admit' => '🙇', 'deny' => '🙅', 'warn' => '⚠️', 'the next day' => '📆', 'whether' => '🤷',
        'claim' => '📢', 'insist' => '☝️', 'complain' => '😤', 'remind' => '📝', 'announce' => '📣', 'suggest' => '💡', 'according to' => '📰', 'spokesperson' => '🎙️',
    ],
    'readings' => [
        'news' => [
            'kind' => 'article',
            'title' => 'City promises new bike lanes after protest',
            'text' => "More than two thousand cyclists rode through the city centre on Sunday to demand safer streets. The protest was organised after three cyclists were injured in accidents last month.\n\nAt the end of the march, the mayor's spokesperson announced that the city would build forty kilometres of new bike lanes before the end of next year. She said that the project had already been approved and that work would start in March.\n\nHowever, not everybody was convinced. Laura Gómez, from the cyclists' association, told reporters that the city had made the same promise two years earlier. \"They said they were going to build lanes in 2023 and nothing happened,\" she complained. She also asked whether the money for the project was really available.\n\nThe spokesperson denied that the plan had been delayed and insisted that this time the budget was guaranteed. She reminded journalists that the city had opened ten kilometres of lanes in the south last year.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada verbo de reporte con su significado.', [['admit', 'admitir'], ['deny', 'negar'], ['warn', 'advertir'], ['complain', 'quejarse'], ['remind', 'recordar a alguien'], ['insist', 'insistir']], 'Elegir el verbo de reporte correcto le da precisión al relato: no todo es said.'],
        ['mcq', 'Choose the verb: "I didn\'t take the money!" → He ___ taking the money.', ['denied', 'admitted', 'reminded'], 0, 'Deny + -ing: negar haber hecho algo.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the verb: "Don\'t forget to bring your passport." → She ___ me to bring my passport.', ['reminded', 'complained', 'denied'], 0, 'Remind + persona + to + verbo.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the verb: "Be careful, the floor is wet." → He ___ us that the floor was wet.', ['warned', 'admitted', 'suggested'], 0, 'Warn: advertir de un peligro.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué dijo el jefe?', 'My boss told me that I could work from home on Fridays.', ['Que tenía que ir los viernes', 'Que podía trabajar desde casa los viernes', 'Que no podía trabajar desde casa'], 1, '"He told me that I could work from home" (can → could).'],
        ['listen', '¿Qué preguntó la médica?', 'The doctor asked me whether I had been sleeping well.', ['Si había dormido bien', 'Si tomaba medicamentos', 'Si comía bien'], 0, 'Pregunta indirecta con whether: si había estado durmiendo bien.'],
        ['dictation', 'Escuchá y escribí la oración.', 'She said that she would call me the next day.', 'Will → would; tomorrow → the next day.'],

        ['reading', 'news', 'Why was the protest organised?', ['Because the city closed some streets', 'Because three cyclists were injured', 'Because bikes became more expensive'], 1, '"After three cyclists were injured in accidents last month."'],
        ['reading', 'news', 'What did the spokesperson announce?', ['The city would build 40 km of bike lanes.', 'The city would ban cars.', 'The project was cancelled.'], 0, '"The city would build forty kilometres of new bike lanes."'],
        ['reading', 'news', 'Why wasn\'t Laura Gómez convinced?', ['The city had made the same promise before.', 'She doesn\'t like bike lanes.', 'The lanes were in the wrong place.'], 0, '"The city had made the same promise two years earlier."'],
        ['reading', 'news', 'True or false? The spokesperson admitted that the plan had been delayed.', ['True', 'False'], 1, 'False: "The spokesperson denied that the plan had been delayed."'],

        ['speak', 'Reportá en voz alta lo que dijo alguien.', 'She told me that she was moving to Spain.', 'Told me: /təʊld miː/. La l es oscura.'],
        ['speak', 'Hacé una pregunta indirecta en voz alta.', 'Could you tell me what time the museum opens?', 'En preguntas indirectas no se invierte el orden: what time the museum opens.'],

        ['write', 'Contá una conversación o una noticia reciente: qué dijo cada persona, qué preguntó alguien y qué respondieron. Usá verbos de reporte variados.',
            ['min' => 100, 'must' => [['\b(said|told \w+) (that )?\w+', 'Usás said / told + oración reportada'], ['\basked (\w+ )?(if|whether|what|where|when|why|how|who)\b', 'Reportás una pregunta (asked if / whether / what…)'], ['\b(admitted|denied|warned|complained|reminded|insisted|explained|suggested|promised|announced)\b', 'Usás al menos un verbo de reporte distinto de say/tell'], ['\b(had|would|could|was|were)\b', 'Corrés los tiempos verbales hacia atrás']],
             'model' => "Last week I had a strange conversation with my landlord. He called me and said that he needed to talk to me urgently. When we met, he explained that he was going to sell the flat and asked whether I wanted to buy it. I told him that I couldn't afford it, but he insisted that the price would be very reasonable. Then he admitted that he had already received an offer from another person and warned me that I would have to decide quickly. I promised that I would give him an answer the next day, and I spent the whole night doing calculations."],
            'Al reportar, los tiempos suelen ir un paso hacia atrás: is → was, will → would, has done → had done.'],
    ],
];
