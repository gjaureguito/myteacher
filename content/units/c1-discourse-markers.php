<?php
return [
    'icon' => '🧵',
    'vocab' => [
        ['on the other hand', 'por otro lado'], ['in addition', 'además'], ['as a result', 'como resultado'], ['in contrast', 'en cambio'],
        ['to sum up', 'en resumen'], ['furthermore', 'además (formal)'], ['even though', 'aunque (enfático)'], ['consequently', 'en consecuencia'],
    ],
    'vocab_icons' => [
        'however' => '↔️', 'whereas' => '⚖️', 'despite' => '🧱', 'therefore' => '➡️', 'owing to' => '🔗', 'moreover' => '➕', 'nevertheless' => '🛡️', 'subsequently' => '⏭️',
        'on the other hand' => '🤚', 'in addition' => '➕', 'as a result' => '🎯', 'in contrast' => '🔀', 'to sum up' => '📌', 'furthermore' => '📎', 'even though' => '🤷', 'consequently' => '🔚',
    ],
    'readings' => [
        'essay' => [
            'kind' => 'article',
            'title' => 'Should university be free for everyone?',
            'text' => "In Argentina, public universities have been free since 1949, and many people consider this one of the country's greatest achievements. However, the debate about who should pay for higher education appears regularly in the media.\n\nSupporters of free education argue that it creates equal opportunities. Students from low-income families can study medicine or engineering without taking on huge debts, whereas in countries such as the United States, graduates often spend decades repaying loans. Moreover, a more educated population benefits the whole economy.\n\nOn the other hand, critics point out that a large proportion of students at public universities come from middle- and high-income families. As a result, they argue, the system indirectly uses taxes paid by poorer citizens to subsidise wealthier ones. Furthermore, a high dropout rate suggests that resources are not always used efficiently.\n\nTo sum up, despite its limitations, free university education remains a powerful tool for social mobility. Nevertheless, it would benefit from better support for students from disadvantaged backgrounds, so that they not only enter university but also finish their degrees.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada conector con su función.', [['however', 'contraste'], ['moreover', 'agregar información'], ['therefore', 'consecuencia'], ['to sum up', 'conclusión'], ['owing to', 'causa'], ['subsequently', 'secuencia (después)']], 'Clasificar los conectores por función ayuda a elegir el correcto al escribir.'],
        ['mcq', 'Choose the connector: The flight was delayed ___ bad weather.', ['owing to', 'however', 'moreover'], 0, 'Owing to + sustantivo: debido a.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the connector: ___ the rain, the concert went ahead.', ['Despite', 'Although', 'However'], 0, 'Despite + sustantivo; although + oración.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the connector: My sister loves the city, ___ I prefer the countryside.', ['whereas', 'therefore', 'moreover'], 0, 'Whereas: mientras que (contraste entre dos ideas).', 'skill' => 'vocabulary'],

        ['listen', '¿Qué relación hay entre las ideas?', 'The product is cheaper than its competitors. However, its quality is much lower.', ['Causa y consecuencia', 'Contraste', 'Ejemplo'], 1, 'However introduce un contraste.'],
        ['listen', '¿Cuál fue la consecuencia?', 'Sales fell by forty per cent last year. Consequently, the company had to close two stores.', ['Abrieron dos locales', 'Cerraron dos locales', 'Aumentaron las ventas'], 1, 'Consequently: en consecuencia, cerraron dos locales.'],
        ['dictation', 'Escuchá y escribí la oración.', 'Nevertheless, the results were encouraging.', 'Nevertheless: sin embargo (formal). Va seguido de coma al inicio.'],

        ['reading', 'essay', 'Since when have Argentine public universities been free?', ['1918', '1949', '1983'], 1, '"Free since 1949."'],
        ['reading', 'essay', 'What is the main argument of supporters of free education?', ['It reduces taxes.', 'It creates equal opportunities.', 'It reduces dropout rates.'], 1, '"Supporters of free education argue that it creates equal opportunities."'],
        ['reading', 'essay', 'What is the function of "On the other hand" in paragraph 3?', ['It adds a similar idea.', 'It introduces the opposing view.', 'It concludes the essay.'], 1, 'Introduce los argumentos de los críticos.'],
        ['reading', 'essay', "What does the writer suggest in the conclusion?", ['Ending free education', 'More support for disadvantaged students', 'Copying the US system'], 1, '"It would benefit from better support for students from disadvantaged backgrounds."'],

        ['speak', 'Decí la oración en voz alta.', 'On the other hand, there are some serious disadvantages.', 'Pausa breve después de On the other hand.'],
        ['speak', 'Decí la oración en voz alta.', 'Despite the cost, the project was a success.', 'Despite: acento en la segunda sílaba /dɪˈspaɪt/.'],

        ['write', 'Escribí un ensayo breve de opinión: "¿Debería ser obligatorio aprender inglés en la escuela primaria?" Presentá argumentos a favor, en contra y una conclusión.',
            ['min' => 120, 'must' => [['\b(however|nevertheless|on the other hand|whereas|in contrast)\b', 'Usás un conector de contraste'], ['\b(moreover|furthermore|in addition|what is more)\b', 'Usás un conector para agregar'], ['\b(therefore|as a result|consequently|thus)\b', 'Usás un conector de consecuencia'], ['\b(to sum up|in conclusion|all in all|to conclude)\b', 'Usás un conector de conclusión'], ['\b(despite|in spite of|although|even though)\b', 'Usás despite / although']],
             'model' => "English is often described as the language of opportunity, and many people believe that children should start learning it at primary school. In my view, this is a positive idea, although it should be implemented carefully.\n\nFirstly, young children learn pronunciation more naturally than adults. Moreover, early exposure to another language develops cognitive skills such as flexibility and attention. As a result, students who start early usually feel more confident later on.\n\nOn the other hand, many schools lack qualified teachers. Therefore, making English compulsory without enough resources could lead to poor teaching and frustration. Furthermore, some argue that children should first master their own language.\n\nTo sum up, despite the practical difficulties, I believe English should be compulsory in primary schools, provided that governments invest in teacher training."],
            'Variá los conectores y fijate en la puntuación: However, … / … ; however, …'],
    ],
];
