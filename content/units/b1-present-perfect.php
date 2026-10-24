<?php
return [
    'icon' => '🌍',
    'vocab' => [
        ['experience', 'experiencia'], ['achieve', 'lograr'], ['abroad', 'en el exterior'], ['bucket list', 'lista de cosas por hacer en la vida'],
        ['try', 'probar / intentar'], ['recently', 'recientemente'], ['for ages', 'hace siglos / hace mucho'], ['once in a lifetime', 'única en la vida'],
    ],
    'vocab_icons' => [
        'already' => '✅', 'yet' => '⌛', 'just' => '🆕', 'ever' => '❓', 'since' => '📍', 'so far' => '📈', 'been to' => '🗺️', 'lately' => '🕐',
        'experience' => '🎒', 'achieve' => '🏆', 'abroad' => '🌍', 'bucket list' => '📋', 'try' => '🍣', 'recently' => '🆕', 'for ages' => '⏳', 'once in a lifetime' => '🌠',
    ],
    'readings' => [
        'blog' => [
            'kind' => 'article',
            'title' => 'My bucket list: halfway there',
            'text' => "When I turned thirty, I wrote a bucket list with twenty things I wanted to do before forty. I'm thirty-five now, so I've checked how I'm doing so far.\n\nI've already done eleven things on the list. I've run a half marathon, I've learnt to cook Thai food and I've been to Iceland, where I saw the Northern Lights. That was a once-in-a-lifetime experience. I've also tried scuba diving, but I didn't enjoy it at all: I panicked the first time I went under water!\n\nThere are some things I haven't done yet. I haven't learnt to play the piano and I haven't written a novel, although I've started one three times. I've lived in the same city since 2015, so working abroad is still on the list.\n\nHave you ever made a bucket list? Has it changed your life? Tell me in the comments.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada palabra con su uso.', [['already', 'antes de lo esperado (afirmativa)'], ['yet', 'todavía / ya (negativas y preguntas)'], ['just', 'recién, hace un momento'], ['ever', 'alguna vez (preguntas)'], ['since', 'desde (un punto en el tiempo)']], 'Palabras clave que acompañan al presente perfecto.'],
        ['match', 'Uní cada verbo con su participio.', [['see', 'seen'], ['write', 'written'], ['eat', 'eaten'], ['be', 'been'], ['take', 'taken'], ['do', 'done']], 'El participio es la tercera columna de los verbos irregulares.'],
        ['mcq', 'Choose the word: Going to Antarctica was a ___ experience.', ['once-in-a-lifetime', 'for ages', 'recently'], 0, 'Once-in-a-lifetime: única en la vida.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: I haven\'t seen my cousins ___ . The last time was in 2019!', ['for ages', 'just', 'abroad'], 0, 'For ages: hace muchísimo tiempo.', 'skill' => 'vocabulary'],

        ['listen', '¿Cuántas veces estuvo en Brasil?', "I've been to Brazil three times, but I've never been to Chile.", ['Nunca', 'Tres veces', 'Una vez'], 1, '"I\'ve been to Brazil three times."'],
        ['listen', '¿Desde cuándo trabaja en la empresa?', "She's worked for the same company since 2018. That's more than seven years.", ['Desde 2008', 'Desde 2018', 'Desde hace dos años'], 1, '"Since 2018": desde 2018.'],
        ['dictation', 'Escuchá y escribí la pregunta.', 'Have you ever eaten sushi?', 'Have + sujeto + ever + participio.'],

        ['reading', 'blog', 'How many things from the list has the writer done?', ['Twenty', 'Eleven', 'Five'], 1, '"I\'ve already done eleven things on the list."'],
        ['reading', 'blog', 'What did the writer think of scuba diving?', ['It was a once-in-a-lifetime experience.', "They didn't enjoy it.", "They haven't tried it yet."], 1, '"I didn\'t enjoy it at all: I panicked."'],
        ['reading', 'blog', 'True or false? The writer has finished a novel.', ['True', 'False'], 1, 'False: "I haven\'t written a novel, although I\'ve started one three times."'],
        ['reading', 'blog', 'Why is working abroad still on the list?', ['Because they have lived in the same city since 2015.', 'Because they don\'t like travelling.', 'Because they have just moved.'], 0, '"I\'ve lived in the same city since 2015."', 'hint' => 'Fijate en la oración que tiene since.'],

        ['speak', 'Contá una experiencia en voz alta.', "I've never been to Europe, but I've visited Brazil twice.", "I've se une: /aɪv/. Never: acento en NEV."],
        ['speak', 'Preguntá en voz alta.', 'How long have you lived here?', 'How long have you: /haʊ lɒŋ həv ju/.'],

        ['write', 'Escribí tu propia "bucket list": tres cosas que ya hiciste, dos que todavía no hiciste y una pregunta para tus lectores.',
            ['min' => 80, 'must' => [["\b(have|has|'ve|'s) (already |never |just |recently )?\w+(ed|en|wn|ne|ght|ung|ept|ade|ld|eft|et|it|ut|ad|ood|een)\b", 'Usás el presente perfecto'], ["\b(haven't|hasn't|have not|has not)\b.*\byet\b", "Usás haven't … yet"], ['\b(already|just|ever|never|since|for)\b', 'Usás already, just, ever, never, since o for'], ['\b(in (19|20)\d\d|last|ago|when I was)\b', 'Incluís un momento terminado con pasado simple (in 2019, last year, ago)']],
             'model' => "I made my bucket list five years ago and I've already done some amazing things. I've visited Machu Picchu, I've learnt to ride a motorbike and I've run a ten-kilometre race. The race was in 2022 and I finished it in just under an hour! However, I haven't learnt to surf yet and I haven't travelled to Japan, which is my biggest dream. I've wanted to go there since I was a teenager. What about you? Have you ever done something that was on your bucket list?"],
            'Presente perfecto para experiencias sin fecha; pasado simple cuando decís cuándo pasó.'],
    ],
];
