<?php
return [
    'icon' => '🏛️',
    'vocab' => [
        ['adhere to', 'atenerse a, cumplir'], ['hereby', 'por la presente'], ['notwithstanding', 'no obstante, a pesar de'], ['pursuant to', 'de conformidad con'],
        ['insist that', 'insistir en que'], ['propose', 'proponer'], ['essential', 'esencial'], ['waive', 'renunciar a (un derecho), eximir'],
    ],
    'vocab_icons' => [
        'imperative' => '❗', 'be that as it may' => '🤷', 'come what may' => '🌪️', 'suffice it to say' => '🤐', 'hesitate' => '⏸️', 'comply' => '✅', 'forthwith' => '⚡', 'stipulate' => '📜',
        'adhere to' => '📏', 'hereby' => '✍️', 'notwithstanding' => '🧱', 'pursuant to' => '⚖️', 'insist that' => '☝️', 'propose' => '💡', 'essential' => '🔑', 'waive' => '🤚',
    ],
    'readings' => [
        'minutes' => [
            'kind' => 'article',
            'title' => 'Minutes of the Board meeting',
            'text' => "The meeting was opened by the Chair at 10:00. Ms Herrera reported that, notwithstanding the difficult economic context, revenue had increased by eight per cent in the last quarter.\n\nThe Finance Director recommended that the company postpone the opening of the Montevideo office until the second half of the year. She stressed that it was essential that every new investment be approved by the full Board, pursuant to the agreement signed in March.\n\nMr Ruiz objected, arguing that further delay would allow competitors to enter the market. Be that as it may, the majority considered it vital that the company not take unnecessary risks. Suffice it to say that the discussion was long and, at times, heated.\n\nThe Board finally resolved that the project be reviewed again in July and that a detailed risk assessment be submitted beforehand. It was further proposed that all directors adhere strictly to the new confidentiality policy, which comes into effect forthwith. The meeting closed at 12:40.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada expresión formal con su equivalente más neutro.', [['forthwith', 'immediately'], ['notwithstanding', 'despite'], ['pursuant to', 'in accordance with'], ['be that as it may', 'even so'], ['suffice it to say', "I'll only say that"]], 'Expresiones propias de documentos legales, actas y discursos formales.'],
        ['mcq', 'Choose the word: All employees must ___ to the new safety regulations.', ['adhere', 'waive', 'propose'], 0, 'Adhere to: atenerse a, cumplir.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: The bank agreed to ___ the cancellation fee.', ['waive', 'stipulate', 'hesitate'], 0, 'Waive a fee: eximir de un cargo.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: The contract ___ that payment must be made within 30 days.', ['stipulates', 'complies', 'adheres'], 0, 'Stipulate: estipular.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué recomienda el abogado?', 'My lawyer recommends that I not sign anything until next week.', ['Que firme ya', 'Que no firme nada hasta la semana próxima', 'Que cambie de abogado'], 1, 'Subjuntivo: recommend that + sujeto + not + verbo base.'],
        ['listen', '¿Qué expresa el hablante?', 'Come what may, we will finish this project on time.', ['Que va a abandonar el proyecto', 'Que pase lo que pase lo terminarán a tiempo', 'Que necesita más tiempo'], 1, 'Come what may: pase lo que pase.'],
        ['dictation', 'Escuchá y escribí la oración.', 'It is essential that every member be informed.', 'Subjuntivo con be: It is essential that… be informed.'],

        ['reading', 'minutes', 'How did revenue change in the last quarter?', ['It decreased.', 'It increased by eight per cent.', 'It stayed the same.'], 1, '"Revenue had increased by eight per cent."'],
        ['reading', 'minutes', 'What did the Finance Director recommend?', ['Opening the office immediately', 'Postponing the Montevideo office', 'Closing the company'], 1, '"Recommended that the company postpone the opening of the Montevideo office."'],
        ['reading', 'minutes', 'What does "Suffice it to say that the discussion was long" imply?', ['The writer prefers not to give all the details.', 'The discussion was short.', 'Nobody spoke.'], 0, 'Suffice it to say: basta con decir (sin entrar en detalles).'],
        ['reading', 'minutes', 'True or false? The new confidentiality policy will start next year.', ['True', 'False'], 1, 'False: "comes into effect forthwith" (de inmediato).'],

        ['speak', 'Decí la oración formal en voz alta.', 'We request that all participants arrive punctually.', 'Request that: ritmo pausado y claro, propio del registro formal.'],
        ['speak', 'Decí la oración en voz alta.', 'Should you have any questions, do not hesitate to contact us.', 'Should you have…: inversión formal equivalente a If you have…'],

        ['write', 'Redactá un email formal a los vecinos de tu edificio (o empleados de una empresa) comunicando nuevas normas y recomendaciones tras una reunión.',
            ['min' => 100, 'must' => [['\b(recommend|suggest|insist|request|propose|ask|demand|require)(s|ed)? that \w+ (be|not|\w+)\b', 'Usás un verbo + that + subjuntivo'], ['\b(essential|vital|imperative|important|crucial|necessary) that \w+( \w+)? (be|not|\w+)\b', 'Usás It is essential / vital that + subjuntivo'], ['\bshould you\b', 'Usás Should you… (condicional formal)'], ['\b(dear|to whom it may concern)\b', 'Empezás con un saludo formal'], ['\b(yours sincerely|yours faithfully|kind regards|best regards)\b', 'Cerrás con una despedida formal']],
             'model' => "Dear residents,\n\nFollowing the meeting held on 12 May, I am writing to inform you of several decisions regarding the use of the common areas.\n\nFirstly, the committee has insisted that the rooftop terrace be closed after 11 pm, owing to repeated complaints about noise. Secondly, it is essential that all rubbish be taken to the containers on the ground floor and not left in the corridors. It was also proposed that each flat contribute a small fee towards the repair of the lift, which will be carried out next month.\n\nShould you have any questions or objections, please do not hesitate to contact the administration before 30 May.\n\nYours sincerely,\nMarcela Torres\nBuilding Administrator"],
            'En el subjuntivo inglés el verbo va en forma base para todas las personas: that he be, that she arrive.'],
    ],
];
