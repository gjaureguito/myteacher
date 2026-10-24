<?php
return [
    'icon' => '🔗',
    'vocab' => [
        ['flatmate', 'compañero/a de departamento'], ['mentor', 'mentor/a'], ['childhood friend', 'amigo/a de la infancia'], ['owner', 'dueño/a'],
        ['founder', 'fundador/a'], ['rely on', 'contar con / depender de'], ['look up to', 'admirar'], ['get on with', 'llevarse bien con'],
    ],
    'vocab_icons' => [
        'whose' => '🔑', 'grow up' => '🌱', 'upstairs' => '⬆️', 'lend (lent)' => '🤲', 'colleague' => '👔', 'landlord' => '🏠', 'hometown' => '📍', 'nearby' => '📌',
        'flatmate' => '🛋️', 'mentor' => '🧑‍🏫', 'childhood friend' => '🧸', 'owner' => '🔑', 'founder' => '🏗️', 'rely on' => '🤝', 'look up to' => '🌟', 'get on with' => '😊',
    ],
    'readings' => [
        'people' => [
            'kind' => 'article',
            'title' => 'The people who shaped me',
            'text' => "Everyone has people who changed their life. For me, there are three.\n\nThe first is my grandmother Elena, who taught me to read before I started school. She lived in a small town in Entre Ríos, where I spent every summer of my childhood. Her house, which had a huge garden full of orange trees, was my favourite place in the world.\n\nThe second is Mr Ferreyra, the maths teacher whose classes made me fall in love with numbers. He was the kind of teacher that you remember forever: strict but fair, and always ready to help students who were struggling.\n\nThe third is my first boss, Daniela, the founder of a small design studio where I worked for five years. She is someone I still look up to. She gave me responsibilities that I didn't think I was ready for, and that's how I learnt to rely on myself.\n\nWho are the people that shaped you?",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada phrasal verb con su significado.', [['grow up', 'crecer, criarse'], ['look up to', 'admirar'], ['get on with', 'llevarse bien con'], ['rely on', 'contar con'], ['look after', 'cuidar']], 'Phrasal verbs muy usados para hablar de personas y relaciones.'],
        ['match', 'Uní cada persona con su definición.', [['landlord', 'the person who owns the flat you rent'], ['colleague', 'a person you work with'], ['flatmate', 'a person you share a flat with'], ['founder', 'the person who started a company'], ['mentor', 'an experienced person who advises you']], 'Las definiciones en inglés usan justamente oraciones de relativo.'],
        ['mcq', 'Choose the word: I ___ very well with my new colleagues.', ['get on', 'look up', 'rely'], 0, 'Get on (well) with: llevarse bien con.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: She ___ me some money when I lost my job.', ['lent', 'borrowed', 'grew'], 0, 'Lend (lent): prestar a alguien. Borrow: pedir prestado.', 'skill' => 'vocabulary'],

        ['listen', '¿Quién es Marta?', "Marta is the woman whose son plays in the same team as mine.", ['La entrenadora del equipo', 'La madre de un compañero de equipo de su hijo', 'Su vecina de arriba'], 1, 'Whose = cuyo/a: el hijo de Marta juega en el mismo equipo.'],
        ['listen', '¿Qué restaurante recomienda?', 'The restaurant where we had dinner last Friday is opening a new place nearby.', ['Uno nuevo que no conoce', 'El restaurante donde cenaron el viernes', 'Uno en su ciudad natal'], 1, '"The restaurant where we had dinner last Friday."'],
        ['dictation', 'Escuchá y escribí la oración.', 'The book that you lent me was fantastic.', 'That (o which) se refiere a cosas; en este caso se podría omitir.'],

        ['reading', 'people', 'What did the writer\'s grandmother teach them?', ['To cook', 'To read', 'To garden'], 1, '"Who taught me to read before I started school."'],
        ['reading', 'people', 'What was special about the grandmother\'s house?', ['It was in Buenos Aires.', 'It had a huge garden with orange trees.', 'It was next to the school.'], 1, '"Her house, which had a huge garden full of orange trees."'],
        ['reading', 'people', 'True or false? Mr Ferreyra was a relaxed teacher who never gave homework.', ['True', 'False'], 1, 'False: "strict but fair".'],
        ['reading', 'people', 'What did the writer learn from Daniela?', ['To rely on themselves', 'To design websites', 'To speak in public'], 0, '"That\'s how I learnt to rely on myself."'],

        ['speak', 'Describí a alguien en voz alta.', 'She is someone I really look up to.', 'Look up to: acento en UP.'],
        ['speak', 'Decí la oración en voz alta.', "That's the town where I grew up.", 'Grew up suena /ɡruː ʌp/: se unen las dos palabras.'],

        ['write', 'Escribí sobre dos o tres personas que influyeron en tu vida: quiénes son, dónde los conociste y qué aprendiste de ellos.',
            ['min' => 100, 'must' => [['\b(who|that)\b', 'Usás who o that para personas'], ['\bwhich\b|\bwhere\b', 'Usás which o where'], ['\bwhose\b', 'Usás whose'], [',\s*(who|which|where|whose)\b', 'Incluís una cláusula no definitoria (con coma)']],
             'model' => "The person who influenced me most is my uncle Raúl, whose passion for music changed my life. He lived in Bahía Blanca, where I spent many winter holidays. His living room, which was full of old records, was like a museum for me. He was the one who gave me my first guitar when I was ten. Another important person is my high school teacher Mónica, who encouraged me to study abroad. She is someone that I still write to every year. From both of them I learnt something very simple: that the things you love deserve time and effort."],
            'Who para personas, which para cosas, where para lugares, whose para posesión. Con coma (información extra) no se usa that.'],
    ],
];
