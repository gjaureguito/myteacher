<?php
return [
    'icon' => '🔄',
    'vocab' => [
        ['scarcely', 'apenas'], ['hardly ever', 'casi nunca'], ['breakthrough', 'avance decisivo'], ['acknowledge', 'reconocer'],
        ['remarkable', 'notable, extraordinario'], ['overwhelming', 'abrumador/a'], ['setback', 'revés, contratiempo'], ['take place', 'tener lugar'],
    ],
    'vocab_icons' => [
        'seldom' => '🌘', 'under no circumstances' => '⛔', 'realise' => '💡', 'outcome' => '🎯', 'unprecedented' => '🆕', 'in no way' => '🚫', 'at no time' => '⏱️', 'such' => '✨',
        'scarcely' => '🤏', 'hardly ever' => '🌒', 'breakthrough' => '🚀', 'acknowledge' => '🙇', 'remarkable' => '🌟', 'overwhelming' => '🌊', 'setback' => '↩️', 'take place' => '📍',
    ],
    'readings' => [
        'speech' => [
            'kind' => 'article',
            'title' => 'Not once did they give up',
            'text' => "When a small research team at a public laboratory in Buenos Aires started studying the virus in 2018, few people took them seriously. Their funding was tiny, their equipment was old and their first results were disappointing. Never had the director faced so many setbacks in a single year.\n\nYet not once did the team consider abandoning the project. \"Only when you have failed a hundred times do you begin to understand the problem,\" says Dr Paula Méndez, who led the laboratory. In 2021 came the breakthrough: a test that could detect the infection in under fifteen minutes and at a fraction of the usual cost.\n\nThe response was overwhelming. Hardly had the results been published when laboratories from Brazil, India and Kenya began to request the method. Under no circumstances, the team insisted, would the test be patented in a way that made it unaffordable for poorer countries.\n\nRarely does a scientific story have such a satisfying ending. Little did the sceptics know that the small team in Buenos Aires would change the way the disease is diagnosed.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada expresión con su significado.', [['seldom', 'rara vez'], ['scarcely', 'apenas'], ['under no circumstances', 'bajo ningún concepto'], ['at no time', 'en ningún momento'], ['little did I know', 'no tenía idea de que']], 'Adverbios negativos o restrictivos que provocan inversión al ir al principio.'],
        ['mcq', 'Choose the word: The discovery was a major ___ in cancer research.', ['breakthrough', 'setback', 'outcome'], 0, 'Breakthrough: avance decisivo.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: The company refused to ___ its mistakes.', ['acknowledge', 'take place', 'realise'], 0, 'Acknowledge: reconocer (públicamente).', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: The support from fans was ___ ; we received thousands of messages.', ['overwhelming', 'scarce', 'unprecedented'], 0, 'Overwhelming: abrumador.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué enfatiza la persona?', 'Never before have I seen such a beautiful sunset.', ['Que nunca había visto un atardecer tan lindo', 'Que ve atardeceres seguido', 'Que no le gustan los atardeceres'], 0, 'Never before have I seen: inversión para enfatizar.'],
        ['listen', '¿Cuándo empezó a llover?', 'No sooner had we left the house than it started to pour.', ['Antes de salir', 'Justo después de salir', 'Cuando volvieron'], 1, 'No sooner… than: apenas habíamos salido, empezó a llover.'],
        ['dictation', 'Escuchá y escribí la oración.', 'Not only did she win, but she also broke the record.', 'Not only + auxiliar + sujeto… but also.'],

        ['reading', 'speech', 'How was the project at the beginning?', ['Well-funded and modern', 'Poorly funded with disappointing results', 'Supported by many countries'], 1, '"Their funding was tiny… their first results were disappointing."'],
        ['reading', 'speech', 'What does Dr Méndez mean by her quote?', ['Failure is part of understanding a problem.', 'You should give up after many failures.', 'Science is always fast.'], 0, '"Only when you have failed a hundred times do you begin to understand the problem."'],
        ['reading', 'speech', 'True or false? The team wanted to patent the test to make it expensive.', ['True', 'False'], 1, 'False: "Under no circumstances… would the test be patented in a way that made it unaffordable."'],
        ['reading', 'speech', 'What does "Little did the sceptics know" suggest?', ['The sceptics were well informed.', 'The sceptics had no idea of the future success.', 'The sceptics helped the team.'], 1, 'Little did they know: no tenían idea.'],

        ['speak', 'Decí la oración con énfasis en voz alta.', 'Under no circumstances should you open this door.', 'El énfasis cae en NO y en OPEN.'],
        ['speak', 'Decí la oración en voz alta.', 'Rarely do we get such an opportunity.', 'Rarely do we: ritmo marcado, acento en RARE-.'],

        ['write', 'Escribí el discurso de apertura (formal) para un evento que celebra un logro importante: un equipo, una ONG, una empresa. Usá la inversión para dar énfasis.',
            ['min' => 90, 'must' => [['\b(never|rarely|seldom|little|not once|not only|no sooner|hardly|only when|only after|under no circumstances|at no time)\b,?\s+(had|have|has|did|do|does|was|were|is|are|would|will|could|should|can)\b', 'Usás al menos una inversión'], ['\bnot only\b[^.]*\bbut\b', 'Usás not only … but (also)'], ['\b(breakthrough|setback|remarkable|overwhelming|unprecedented|acknowledge)\b', 'Usás vocabulario de la unidad'], ['\b(ladies and gentlemen|dear|good evening|welcome)\b', 'Empezás con un saludo formal']],
             'model' => "Good evening, ladies and gentlemen, and welcome. Ten years ago, a group of six volunteers started teaching children to read in a borrowed classroom in La Matanza. Never did they imagine that their project would one day reach more than five thousand families. Not only have they built three libraries, but they have also trained over two hundred teachers. There were setbacks, of course. At no time, however, did they consider giving up. Rarely does a community achieve something so remarkable with so few resources. Tonight we acknowledge their work and say, simply, thank you."],
            'Después del adverbio negativo va el auxiliar y luego el sujeto, como en una pregunta.'],
    ],
];
