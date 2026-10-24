<?php
return [
    'icon' => '🌠',
    'vocab' => [
        ['missed opportunity', 'oportunidad perdida'], ['second thoughts', 'dudas, replanteos'], ['wishful thinking', 'expresión de deseo poco realista'], ['make amends', 'enmendar, reparar'],
        ['dwell on', 'darle vueltas a algo'], ['move on', 'seguir adelante'], ['let go', 'soltar, dejar ir'], ['what if', '¿y si…?'],
    ],
    'vocab_icons' => [
        'if only' => '🙏', 'regret' => '😔', 'take for granted' => '🫙', 'turn down' => '👎', 'shy' => '🙈', 'in hindsight' => '🔙', 'by now' => '⌛', 'would rather' => '⚖️',
        'missed opportunity' => '🚪', 'second thoughts' => '🤔', 'wishful thinking' => '🌈', 'make amends' => '🤝', 'dwell on' => '🌀', 'move on' => '➡️', 'let go' => '🎈', 'what if' => '❓',
    ],
    'readings' => [
        'letter' => [
            'kind' => 'email',
            'title' => 'Dear younger me',
            'text' => "Dear Andrés at twenty,\n\nI'm writing to you from forty-five, and there are a few things I wish someone had told me.\n\nFirst, I wish you had taken your health more seriously. If you hadn't spent your twenties sleeping four hours a night, you wouldn't have so many back problems now. I know you think you're invincible. You aren't.\n\nSecond, stop dwelling on what other people think. If you weren't so shy, you would have accepted that scholarship in Canada. In hindsight, turning it down was the biggest missed opportunity of your life. Then again, if you had gone, you wouldn't have met Lucía, and our daughters wouldn't exist. So maybe some regrets are not really regrets.\n\nFinally, call Grandpa more often. You take him for granted. I would give anything to hear his voice again.\n\nIf only I could send you this letter. I'd rather you learnt these things the easy way.\n\nWith love,\nAndrés",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada expresión con su significado.', [['dwell on', 'darle vueltas a algo'], ['move on', 'seguir adelante'], ['take for granted', 'no valorar algo o a alguien'], ['in hindsight', 'en retrospectiva'], ['turn down', 'rechazar']], 'Expresiones frecuentes para hablar de arrepentimiento y decisiones.'],
        ['mcq', "Choose the word: Stop ___ your mistakes and focus on the future.", ['dwelling on', 'taking for granted', 'making amends'], 0, 'Dwell on: darle vueltas a algo negativo.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: He tried to ___ by inviting her to dinner after the argument.', ['make amends', 'turn down', 'let go'], 0, 'Make amends: reparar un daño.', 'skill' => 'vocabulary'],
        ['mcq', "Choose the word: I'm having ___ about buying the car. It's very expensive.", ['second thoughts', 'wishful thinking', 'missed opportunities'], 0, 'Second thoughts: dudas sobre una decisión.', 'skill' => 'vocabulary'],

        ['listen', '¿De qué se arrepiente?', "I wish I hadn't sold my grandfather's watch. It meant so much to him.", ['De haber comprado un reloj', 'De haber vendido el reloj del abuelo', 'De no haber visitado al abuelo'], 1, 'Wish + past perfect: arrepentimiento sobre el pasado.'],
        ['listen', '¿Qué expresa la persona?', "If I had studied medicine, I would be a doctor now.", ['Una situación presente que depende de una decisión pasada', 'Un plan para el futuro', 'Un recuerdo agradable'], 0, 'Condicional mixto: pasado hipotético → presente distinto.'],
        ['dictation', 'Escuchá y escribí la oración.', "I'd rather you didn't tell anyone.", "Would rather + sujeto + pasado: preferiría que no le contaras a nadie."],

        ['reading', 'letter', 'Why does Andrés have back problems now?', ['He played rugby.', "He didn't sleep enough in his twenties.", 'He works at a desk.'], 1, '"If you hadn\'t spent your twenties sleeping four hours a night…"'],
        ['reading', 'letter', 'Why did he turn down the scholarship?', ['Because he was shy', 'Because he met Lucía', 'Because it was too expensive'], 0, '"If you weren\'t so shy, you would have accepted that scholarship."'],
        ['reading', 'letter', 'Why does he say "some regrets are not really regrets"?', ['Because if he had gone to Canada, he wouldn\'t have his family.', 'Because Canada is too cold.', 'Because he got another scholarship later.'], 0, 'Si se hubiera ido, no habría conocido a Lucía ni tendría a sus hijas.'],
        ['reading', 'letter', 'True or false? His grandfather is still alive.', ['True', 'False'], 1, 'False: "I would give anything to hear his voice again."'],

        ['speak', 'Decí la oración en voz alta.', 'I wish I had more free time.', 'Wish I had: deseo sobre el presente.'],
        ['speak', 'Decí el condicional mixto en voz alta.', "If I had taken that job, I'd be living in London now.", 'I\'d be living se reduce: /aɪd bi ˈlɪvɪŋ/.'],

        ['write', 'Escribile una carta a tu "yo" de hace diez o quince años: qué te gustaría que hubiera hecho distinto, qué consecuencias tiene hoy y qué no cambiarías.',
            ['min' => 90, 'must' => [["\bwish(ed)? (I|you|he|she|we|they) (had|'d) \w+", 'Usás wish + past perfect (arrepentimiento del pasado)'], ['\bwish(ed)? (I|you|he|she|we|they) (were|was|had|could|knew|lived|did|didn\'t|weren\'t)\b', 'Usás wish + pasado (deseo sobre el presente)'], ["\bif\b[^.]*\b(had|hadn't|'d) \w+[^.]*\b(would|'d|wouldn't) (be|have|live|feel|know|work)\b", 'Usás un condicional mixto (pasado → presente)'], ['\b(if only|would rather|in hindsight|take \w+ for granted|move on|dwell on)\b', 'Usás una expresión de la unidad']],
             'model' => "Dear Sofía at twenty-two,\n\nI wish you had trusted yourself more. If you had sent your designs to that agency in 2014, you would probably have your own studio now. In hindsight, fear was your biggest enemy. I also wish you had spent less money on clothes and more on travelling. On the other hand, I'm glad you moved to Córdoba: if you hadn't moved, you wouldn't be part of this wonderful group of friends. Today I wish I had more patience with Mum, but I'm learning. Don't dwell on small failures. Move on quickly.\n\nLove,\nSofía"],
            'Wish + pasado: el presente que te gustaría distinto. Wish + had + participio: el pasado que lamentás.'],
    ],
];
