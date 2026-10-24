<?php
return [
    'icon' => '🎲',
    'vocab' => [
        ['make a fortune', 'hacer una fortuna'], ['quit (a job)', 'renunciar'], ['turning point', 'punto de inflexión'], ['by chance', 'por casualidad'],
        ['get the job', 'conseguir el trabajo'], ['live up to', 'estar a la altura de'], ['look back', 'mirar hacia atrás'], ['make a difference', 'hacer la diferencia'],
    ],
    'vocab_icons' => [
        'regret' => '😔', 'offer' => '🤝', 'lottery' => '🎰', 'afford' => '💳', 'if I were you' => '🫵', 'take the chance' => '🎲', 'in time' => '⏱️', 'otherwise' => '↪️',
        'make a fortune' => '💰', 'quit (a job)' => '🚪', 'turning point' => '🔄', 'by chance' => '🍀', 'get the job' => '💼', 'live up to' => '📏', 'look back' => '🔙', 'make a difference' => '🌟',
    ],
    'readings' => [
        'story' => [
            'kind' => 'story',
            'title' => 'The email I almost didn\'t send',
            'text' => "Seven years ago I was working as an accountant in a big bank in Buenos Aires. The salary was good, but I was bored. One night, by chance, I saw an advert for a cooking course in Lyon. The deadline was midnight. I wrote an application in twenty minutes and sent it at 11:58.\n\nIf I hadn't seen that advert, I would never have applied. And if the course hadn't accepted me, I would probably still be working at the bank today. That email was the turning point of my life.\n\nI quit my job, spent a year in France and came back to open a small restaurant in San Telmo. It wasn't easy: if my parents hadn't lent me money, I couldn't have paid the rent for the first six months.\n\nPeople sometimes ask me: \"If you could go back, would you do it again?\" The answer is yes, without a doubt. If I were still at the bank, I'd have more money, but I wouldn't be half as happy.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada expresión con su significado.', [['turning point', 'punto de inflexión'], ['by chance', 'por casualidad'], ['make a fortune', 'hacer una fortuna'], ['look back', 'mirar hacia atrás'], ['take the chance', 'aprovechar la oportunidad']], 'Expresiones frecuentes para hablar de decisiones y su impacto.'],
        ['mcq', 'Choose the word: She ___ her job to travel around Asia for a year.', ['quit', 'afforded', 'regretted'], 0, 'Quit: renunciar a un trabajo (quit – quit – quit).', 'skill' => 'vocabulary'],
        ['mcq', "Choose the word: The new film didn't ___ the expectations of the fans.", ['live up to', 'look back', 'make a difference'], 0, 'Live up to: estar a la altura de.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: We left early; ___, we would have missed the flight.', ['otherwise', 'in time', 'by chance'], 0, 'Otherwise: de lo contrario.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué haría con el dinero?', "If I won the lottery, I'd buy a house by the sea and stop working for a year.", ['Viajaría por el mundo', 'Compraría una casa junto al mar', 'Abriría un negocio'], 1, '"I\'d buy a house by the sea" (segundo condicional: situación imaginaria).'],
        ['listen', '¿Por qué perdieron el vuelo?', "If the taxi hadn't broken down, we would have caught the flight.", ['Porque se quedaron dormidos', 'Porque se rompió el taxi', 'Porque había mucho tráfico'], 1, '"If the taxi hadn\'t broken down…" (tercer condicional: pasado que no se puede cambiar).'],
        ['dictation', 'Escuchá y escribí la oración.', "If I were you, I would accept the offer.", 'If I were you: estructura fija para dar consejos.'],

        ['reading', 'story', 'What was the writer\'s job seven years ago?', ['A chef', 'An accountant', 'A teacher'], 1, '"I was working as an accountant in a big bank."'],
        ['reading', 'story', 'What does "That email was the turning point of my life" mean?', ['It changed the direction of the writer\'s life.', 'It was a mistake.', 'It arrived too late.'], 0, 'Turning point: el momento en que algo cambia de dirección.'],
        ['reading', 'story', 'True or false? The writer paid the first six months of rent alone.', ['True', 'False'], 1, 'False: "if my parents hadn\'t lent me money, I couldn\'t have paid the rent".'],
        ['reading', 'story', 'How does the writer feel about the decision now?', ['They regret it.', 'They would do it again.', "They aren't sure."], 1, '"The answer is yes, without a doubt."'],

        ['speak', 'Decí la oración en voz alta.', "If I had more time, I'd learn to play the piano.", "I'd suena /aɪd/: la d es muy suave."],
        ['speak', 'Decí el tercer condicional en voz alta.', "If I'd known, I would have helped you.", 'Would have en el habla se reduce a /ˈwʊdəv/.'],

        ['write', 'Pensá en una decisión o casualidad importante de tu vida. Contá qué pasó, qué habría pasado si hubiera sido distinto y qué harías hoy en una situación parecida.',
            ['min' => 100, 'must' => [["\bif\b[^.]*\b(had|'d) (\w+ed|been|known|seen|met|gone|taken|done|got|made)\b", 'Usás el tercer condicional (if + had + participio)'], ["\b(would|'d|could|might) have\b", 'Usás would have / could have'], ["\bif\b[^.]*\b(were|was|had|could|did|knew|lived|went)\b[^.]*\b(would|'d|could|might)\b", 'Usás el segundo condicional (if + pasado, would)'], ['\b(regret|turning point|by chance|look back)\b', 'Usás vocabulario de la unidad']],
             'model' => "Ten years ago I failed my university entrance exam. At the time it felt like a disaster, but looking back, it was a turning point. Because I failed, I started working at my uncle's travel agency, and there I discovered that I loved organising trips. If I had passed the exam, I would have studied law like my father, and I probably wouldn't have met my wife, who was a client of the agency! I don't regret anything. If a friend were in a similar situation today, I would tell them not to panic: sometimes a failure opens a better door."],
            'Segundo condicional: imaginar el presente. Tercero: imaginar un pasado distinto.'],
    ],
];
