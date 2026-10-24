<?php
return [
    'icon' => '🎯',
    'vocab' => [
        ['striking', 'llamativo/a, impactante'], ['underlying', 'subyacente'], ['highlight', 'destacar'], ['point out', 'señalar'],
        ['stand out', 'destacarse'], ['the thing is', 'lo que pasa es que'], ['what matters', 'lo que importa'], ['ironically', 'irónicamente'],
    ],
    'vocab_icons' => [
        'vase' => '🏺', 'peace and quiet' => '🕊️', 'worry' => '😟', 'strange as it may seem' => '🌀', 'reliable' => '🔒', 'outcome' => '🎯', 'bewilder' => '😵', 'emphasis' => '❗',
        'striking' => '⚡', 'underlying' => '🧊', 'highlight' => '🖍️', 'point out' => '👉', 'stand out' => '🌟', 'the thing is' => '💬', 'what matters' => '💎', 'ironically' => '🙃',
    ],
    'readings' => [
        'review' => [
            'kind' => 'article',
            'title' => 'Review: the quietest film of the year',
            'text' => "What makes \"La casa del río\" so extraordinary is not its plot, which could be summarised in two lines, but its patience. It is a film in which almost nothing happens, and yet it is impossible to stop watching.\n\nThe story follows an elderly fisherman in the Paraná Delta and his granddaughter, who arrives from Buenos Aires for the summer. What the director, Inés Coria, does brilliantly is let silence do the talking. It is in the long, wordless scenes on the river that we understand everything about the relationship between them.\n\nStrange as it may seem, the most striking moment of the film is a ten-minute sequence in which the two characters simply repair a net. Not a single word is spoken. Beautiful it is not, in the conventional sense; honest, it certainly is.\n\nIf there is a weakness, it is the final scene, which explains too much. What the film had suggested so elegantly for two hours suddenly becomes obvious. Still, what matters is the journey, and that journey is unforgettable.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada expresión con su función.', [["What I need is…", 'enfatizar lo que necesitás'], ["It was … who/that…", 'destacar a una persona o cosa'], ["The thing is…", 'introducir un problema o explicación'], ['All I did was…', 'minimizar una acción'], ['Strange as it may seem,…', 'conceder algo sorprendente']], 'Estructuras para poner el foco en una parte de la oración.'],
        ['mcq', 'Choose the word: The report ___ the need for more investment in education.', ['highlights', 'bewilders', 'stands out'], 0, 'Highlight: destacar, subrayar.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: Her talent made her ___ from the other candidates.', ['stand out', 'point out', 'highlight'], 0, 'Stand out (from): destacarse.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: The ___ cause of the conflict was economic inequality.', ['underlying', 'striking', 'reliable'], 0, 'Underlying: subyacente, de fondo.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué enfatiza la persona?', "It wasn't the price that worried me; it was the location.", ['El precio', 'La ubicación', 'El tamaño'], 1, 'Cleft sentence: It was the location (that worried me).'],
        ['listen', '¿Qué es lo que quiere?', 'What I really want is a few days of peace and quiet.', ['Una fiesta', 'Unos días de tranquilidad', 'Un trabajo nuevo'], 1, 'What I really want is…: pseudo-cleft para enfatizar.'],
        ['dictation', 'Escuchá y escribí la oración.', 'It was my sister who broke the vase.', 'It was + persona + who: destaca quién lo hizo.'],

        ['reading', 'review', 'According to the reviewer, what makes the film extraordinary?', ['Its plot', 'Its patience', 'Its special effects'], 1, '"What makes… so extraordinary is not its plot… but its patience."'],
        ['reading', 'review', 'Where do we understand the relationship between the characters?', ['In the dialogue', 'In the long, wordless scenes on the river', 'In the final scene'], 1, '"It is in the long, wordless scenes on the river that we understand everything."'],
        ['reading', 'review', 'What does "Beautiful it is not… honest, it certainly is" do?', ['It fronts the adjectives for emphasis.', 'It shows the reviewer disliked the film.', 'It describes the actors.'], 0, 'Fronting: el adjetivo va al principio para enfatizar el contraste.'],
        ['reading', 'review', "What is the film's weakness?", ['The acting', 'The final scene explains too much.', 'It is too short.'], 1, '"It is the final scene, which explains too much."'],

        ['speak', 'Decí la oración con énfasis en voz alta.', "It's not the money that matters, it's the time.", 'Acentuá MONEY y TIME: son los elementos en contraste.'],
        ['speak', 'Decí la oración en voz alta.', 'What surprised me most was how calm she was.', 'Pausa breve antes de was.'],

        ['write', 'Escribí una reseña breve de una película, serie, libro o disco que te haya impactado. Usá estructuras de énfasis para destacar lo mejor y lo peor.',
            ['min' => 100, 'must' => [['\bwhat \w+( \w+){0,4} (is|was)\b', 'Usás una pseudo-cleft (What … is / was …)'], ["\b(it|it's) (is|was|wasn't|isn't|'s)? ?(not )?\w+( \w+){0,5} (that|who)\b", 'Usás una cleft (It is / was … that / who)'], ['\b(strange|surprising|hard|difficult|odd) as it (may|might) (seem|sound)\b|\ball (I|we|you|he|she|they) \w+ (is|was)\b', 'Usás Strange as it may seem o All I … was'], ['\b(striking|stand(s)? out|highlight|underlying|ironically|what matters)\b', 'Usás vocabulario de la unidad']],
             'model' => "What I loved most about \"Los años luz\" was its honesty. It is a small novel, but it is one that stays with you for weeks. The story is simple: two brothers meet again after twenty years. Strange as it may seem, it is the silences between them that tell the real story. What stands out is the way the author describes ordinary objects, like an old radio or a broken watch, as if they were full of memory. If I had to criticise something, it would be the ending, which feels rushed. Still, what matters is the emotional journey, and that journey is unforgettable."],
            'Las cleft sentences ordenan la información: lo nuevo o importante va al final o en el foco.'],
    ],
];
