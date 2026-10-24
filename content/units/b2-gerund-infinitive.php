<?php
return [
    'icon' => '🔁',
    'vocab' => [
        ['give up', 'dejar (un hábito)'], ['keep', 'seguir / continuar'], ['be into', 'estar enganchado con, gustar mucho'], ['tend to', 'tender a'],
        ['be worth', 'valer la pena'], ['struggle to', 'costar mucho'], ['habit', 'hábito'], ['routine', 'rutina'],
    ],
    'vocab_icons' => [
        'avoid' => '🙈', 'look forward to' => '🤩', "can't stand" => '😖', 'afford' => '💳', 'refuse' => '✋', 'manage to' => '💪', 'get used to' => '🔄', 'suggest' => '💡',
        'give up' => '🚭', 'keep' => '➡️', 'be into' => '😍', 'tend to' => '📊', 'be worth' => '💎', 'struggle to' => '😓', 'habit' => '🔁', 'routine' => '🗓️',
    ],
    'readings' => [
        'habits' => [
            'kind' => 'article',
            'title' => 'Why do we keep making the same mistakes?',
            'text' => "Most of us have tried to change a habit at least once. We decide to stop eating sugar, to go to the gym three times a week or to give up checking our phones before bed. And most of us fail within a month. Why?\n\nAccording to psychologist Martín Lagos, the problem is that we tend to rely on motivation. \"Motivation is great on Monday morning, but by Thursday it has disappeared,\" he explains. \"People who manage to change habits usually avoid depending on willpower. Instead, they change their environment.\"\n\nFor example, if you want to stop eating biscuits, it's not worth buying them and promising yourself to eat only one. It's much easier not to have them at home. If you want to start running, he suggests leaving your trainers next to your bed.\n\nLagos also recommends being patient. \"We used to think a new habit took 21 days. Research shows it can take more than two months, so don't give up after a bad week.\"",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada verbo con la continuación correcta.', [['I enjoy', 'cooking at weekends.'], ['We decided', 'to move abroad.'], ['She avoids', 'talking about politics.'], ['They managed', 'to finish on time.'], ["I'm looking forward to", 'meeting you.']], 'Enjoy, avoid y look forward to van con -ing; decide y manage, con to.'],
        ['mcq', 'Choose the word: I finally ___ smoking last year.', ['gave up', 'kept', 'tended'], 0, 'Give up + -ing: dejar un hábito.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: The museum is really ___ visiting.', ['worth', 'into', 'used'], 0, 'Be worth + -ing: valer la pena.', 'skill' => 'vocabulary'],
        ['mcq', "Choose the word: I'm really ___ yoga at the moment. I do it every day.", ['into', 'worth', 'used'], 0, 'Be into something: gustarle mucho algo.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué no soporta?', "I can't stand waiting in long queues, so I always buy tickets online.", ['Comprar por internet', 'Esperar en filas largas', 'Ir al cine'], 1, '"I can\'t stand waiting in long queues."'],
        ['listen', '¿A qué se está acostumbrando?', "I'm slowly getting used to working from home, but I still miss my colleagues.", ['A trabajar en la oficina', 'A trabajar desde casa', 'A levantarse temprano'], 1, 'Get used to + -ing: acostumbrarse.'],
        ['dictation', 'Escuchá y escribí la oración.', "I'm looking forward to seeing you.", 'Look forward to + -ing: to es preposición acá.'],

        ['reading', 'habits', 'According to Lagos, what do people tend to rely on?', ['Their environment', 'Motivation', 'Their friends'], 1, '"We tend to rely on motivation."'],
        ['reading', 'habits', 'What do people who manage to change habits usually do?', ['They change their environment.', 'They use more willpower.', 'They only try on Mondays.'], 0, '"Instead, they change their environment."'],
        ['reading', 'habits', 'True or false? He suggests buying biscuits and eating only one.', ['True', 'False'], 1, 'False: "it\'s not worth buying them… It\'s much easier not to have them at home."'],
        ['reading', 'habits', 'How long can a new habit take?', ['21 days', 'One week', 'More than two months'], 2, '"It can take more than two months."'],

        ['speak', 'Decí la oración en voz alta.', "I'm trying to give up eating sugar.", 'Give up se une: /ɡɪvʌp/.'],
        ['speak', 'Decí la oración en voz alta.', 'I used to play football, but I gave it up.', 'Used to se pronuncia /juːstə/: la d no suena.'],

        ['write', 'Contá un hábito que cambiaste o que querés cambiar: qué solías hacer, qué intentaste, qué te costó y qué consejo darías.',
            ['min' => 100, 'must' => [['\bused to\b', 'Usás used to'], ['\b(enjoy|avoid|stop|give up|keep|mind|suggest|can.t stand|finish|consider)\w* \w+ing\b', 'Usás un verbo + -ing'], ['\b(decide|want|try|manage|hope|plan|refuse|learn|need|struggle)\w* to \w+', 'Usás un verbo + to'], ['\b(get|got|getting|be|am|is|was) used to \w+ing\b', 'Usás get/be used to + -ing']],
             'model' => "I used to spend at least two hours a day on social media, especially at night. I decided to change this habit last year because I was sleeping very badly. At first I tried to use an app that limits screen time, but I kept ignoring it. Then a friend suggested leaving my phone in the kitchen before going to bed. It was hard at the beginning, and I struggled to fall asleep without scrolling. However, after a few weeks I got used to reading a book instead. My advice: avoid relying only on willpower, be patient and change your environment instead."],
            'Used to + verbo base (antes hacía); be/get used to + -ing (estar/acostumbrarse a).'],
    ],
];
