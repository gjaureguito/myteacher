<?php
return [
    'icon' => '🗝️',
    'vocab' => [
        ['bite the bullet', 'hacer de tripas corazón'], ['the last straw', 'la gota que rebalsó el vaso'], ['break the ice', 'romper el hielo'], ['on the same page', 'en sintonía, de acuerdo'],
        ['go the extra mile', 'hacer un esfuerzo extra'], ['make ends meet', 'llegar a fin de mes'], ['take the plunge', 'animarse, tirarse a la pileta'], ['in the long run', 'a largo plazo'],
    ],
    'vocab_icons' => [
        'meet a deadline' => '📅', 'raise awareness' => '📣', 'pose a threat' => '⚠️', 'carry out' => '📋', 'cut corners' => '✂️', 'get the hang of' => '🎯', 'bear in mind' => '🧠', 'a blessing in disguise' => '🎁',
        'bite the bullet' => '😬', 'the last straw' => '🥤', 'break the ice' => '🧊', 'on the same page' => '📄', 'go the extra mile' => '🏃', 'make ends meet' => '💵', 'take the plunge' => '🏊', 'in the long run' => '🛤️',
    ],
    'readings' => [
        'startup' => [
            'kind' => 'story',
            'title' => 'How we almost went bust',
            'text' => "Three years ago, my partner and I decided to take the plunge and quit our jobs to launch an app for small restaurants. We were full of enthusiasm and very little money.\n\nThe first year was brutal. We were working fourteen hours a day and still struggling to make ends meet. To meet our deadlines, we started cutting corners: we skipped testing and ignored some complaints from early users. In the short term, it seemed to work. In the long run, it was a disaster.\n\nThe last straw came when a bug deleted the orders of a popular restaurant on a Saturday night. They posted about it online and we lost half our clients in a week. We had to bite the bullet, apologise publicly and rebuild the app almost from scratch.\n\nLooking back, that crisis was a blessing in disguise. It forced us to get on the same page about quality, and our clients noticed. Today we go the extra mile for every customer, and we always bear in mind one lesson: a reputation takes years to build and minutes to destroy.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada idiom con su significado.', [['bite the bullet', 'enfrentar algo difícil inevitable'], ['the last straw', 'lo que colma la paciencia'], ['break the ice', 'romper el hielo'], ['make ends meet', 'llegar a fin de mes'], ['take the plunge', 'animarse a algo arriesgado'], ['cut corners', 'hacer algo mal para ahorrar tiempo o dinero']], 'Los idioms no se traducen palabra por palabra: se aprenden como unidad.'],
        ['match', 'Uní cada verbo con su colocación más natural.', [['meet', 'a deadline'], ['raise', 'awareness'], ['pose', 'a threat'], ['carry out', 'research'], ['make', 'a decision']], 'Las colocaciones son combinaciones fijas: decimos make a decision, no do a decision.'],
        ['mcq', "Choose the expression: Let's make sure we're all ___ before the meeting with the client.", ['on the same page', 'in the long run', 'a blessing in disguise'], 0, 'On the same page: de acuerdo, en sintonía.', 'skill' => 'vocabulary'],
        ['mcq', "Choose the expression: It took me a few weeks to ___ the new software.", ['get the hang of', 'bite the bullet', 'break the ice'], 0, 'Get the hang of: agarrarle la mano.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué quiere decir la persona?', 'Losing that job was a blessing in disguise: now I have my own business.', ['Que perder el trabajo fue algo bueno al final', 'Que extraña su trabajo', 'Que lo despidieron injustamente'], 0, 'A blessing in disguise: algo malo que resulta positivo.'],
        ['listen', '¿Por qué renunció?', "When they cancelled my holidays for the third time, that was the last straw. I resigned the next day.", ['Le ofrecieron otro trabajo', 'Le cancelaron las vacaciones por tercera vez', 'Se mudó'], 1, 'The last straw: la gota que rebalsó el vaso.'],
        ['dictation', 'Escuchá y escribí la oración.', 'Bear in mind that the deadline is on Friday.', 'Bear in mind: tené en cuenta.'],

        ['reading', 'startup', 'What does "take the plunge" mean in the first paragraph?', ['They decided to do something risky.', 'They went swimming.', 'They lost money.'], 0, 'Take the plunge: animarse a algo arriesgado.'],
        ['reading', 'startup', 'Why did they start cutting corners?', ['To meet their deadlines', 'To please their clients', 'Because they had too many employees'], 0, '"To meet our deadlines, we started cutting corners."'],
        ['reading', 'startup', 'What was "the last straw"?', ['A bug deleted a restaurant\'s orders.', 'They ran out of money.', 'Their partner quit.'], 0, '"A bug deleted the orders of a popular restaurant on a Saturday night."'],
        ['reading', 'startup', 'True or false? The writer now sees the crisis as entirely negative.', ['True', 'False'], 1, 'False: "that crisis was a blessing in disguise."'],

        ['speak', 'Decí la oración en voz alta.', 'We had to bite the bullet and start again from scratch.', 'Bite the bullet: ritmo regular, acento en BITE y BULL-.'],
        ['speak', 'Decí la oración en voz alta.', 'In the long run, cutting corners always costs more.', 'Long run: unís las palabras /lɒŋrʌn/.'],

        ['write', 'Contá una situación difícil (laboral, académica o personal) y cómo la resolviste. Usá al menos tres idioms y dos colocaciones de la unidad de forma natural.',
            ['min' => 110, 'must' => [['\b(bite the bullet|the last straw|break the ice|on the same page|go the extra mile|make ends meet|take the plunge|in the long run|a blessing in disguise|cut(ting)? corners|get the hang of)\b.*\b(bite the bullet|the last straw|break the ice|on the same page|go the extra mile|make ends meet|take the plunge|in the long run|a blessing in disguise|cut(ting)? corners|get the hang of)\b.*\b(bite the bullet|the last straw|break the ice|on the same page|go the extra mile|make ends meet|take the plunge|in the long run|a blessing in disguise|cut(ting)? corners|get the hang of)\b', 'Usás al menos tres idioms'], ['\b(meet(ing)? (a|the) deadline|raise awareness|pose(d)? a threat|carr(y|ied) out|bear in mind|make a decision|made a decision)\b', 'Usás una colocación (meet a deadline, carry out…)']],
             'model' => "Last year I was asked to lead a project with a team I didn't know at all. At the first meeting nobody spoke, so I organised a lunch to break the ice. It worked, but the real problem was the deadline: we had six weeks to carry out a job that normally takes three months. Some colleagues wanted to cut corners, but I knew that in the long run it would cost us more. So we bit the bullet, worked some weekends and made sure everyone was on the same page every Monday. We managed to meet the deadline, and the client was so happy that they hired us again."],
            'Los idioms suenan naturales cuando aparecen en el contexto adecuado; no conviene acumularlos.'],
    ],
];
