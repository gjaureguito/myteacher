<?php
return [
    'icon' => '🗞️',
    'vocab' => [
        ['investigate', 'investigar'], ['sue', 'demandar'], ['suspect', 'sospechar / sospechoso'], ['evidence', 'pruebas, evidencia'],
        ['launch', 'lanzar'], ['carry out', 'llevar a cabo'], ['get something fixed', 'hacer arreglar algo'], ['have one\'s hair cut', 'cortarse el pelo (en la peluquería)'],
    ],
    'vocab_icons' => [
        'allegedly' => '🤫', 'resign' => '🚪', 'estimate' => '📊', 'fake' => '🎭', 'service (the car)' => '🔧', 'repair' => '🛠️', 'rumoured' => '👂', 'spokesperson' => '🎙️',
        'investigate' => '🔎', 'sue' => '⚖️', 'suspect' => '🕵️', 'evidence' => '🧾', 'launch' => '🚀', 'carry out' => '📋', 'get something fixed' => '🔩', "have one's hair cut" => '💇',
    ],
    'readings' => [
        'report' => [
            'kind' => 'article',
            'title' => 'Wine company accused of selling fake labels',
            'text' => "A well-known wine company in Mendoza is being investigated after hundreds of bottles were allegedly sold with fake labels. The bottles, which were labelled as premium Malbec from a single vineyard, are believed to contain a cheaper blend of wines.\n\nThe scandal broke last week when a sommelier in Miami had several bottles analysed by an independent laboratory. The results showed that the wine was not what the label claimed. It is estimated that more than four thousand bottles were exported to the United States and Europe between 2022 and 2024.\n\nThe company's director, who is said to have known about the problem for months, resigned on Monday. In a statement, a spokesperson insisted that the labels had been printed by an external supplier and that the company had been deceived. However, according to local journalists, internal emails suggest that the decision was taken inside the company.\n\nSeveral importers are reported to be preparing to sue. An official inspection will be carried out next month.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada palabra con su significado.', [['allegedly', 'supuestamente'], ['resign', 'renunciar a un cargo'], ['estimate', 'estimar'], ['sue', 'demandar'], ['evidence', 'pruebas']], 'Vocabulario típico de noticias y textos periodísticos.'],
        ['mcq', 'Choose the word: The police found new ___ against the suspect.', ['evidence', 'estimate', 'spokesperson'], 0, 'Evidence: pruebas. Es incontable: a piece of evidence.', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: The government will ___ a new campaign against smoking.', ['launch', 'sue', 'resign'], 0, 'Launch: lanzar (un producto, una campaña).', 'skill' => 'vocabulary'],
        ['mcq', "Choose the word: The minister ___ after the scandal.", ['resigned', 'estimated', 'launched'], 0, 'Resign: renunciar a un cargo.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué hizo con el auto?', "I'm getting my car serviced tomorrow, so I'll come by bus.", ['Lo vendió', 'Lo va a llevar al service', 'Lo arregló él mismo'], 1, 'Get something done (causativo): otra persona hace el trabajo.'],
        ['listen', '¿Qué se dice del actor?', 'The actor is rumoured to be leaving the series at the end of the season.', ['Que se va de la serie, según rumores', 'Que confirmó su salida', 'Que va a protagonizar la próxima temporada'], 0, 'Be rumoured to: se rumorea que.'],
        ['dictation', 'Escuchá y escribí la oración.', 'The bridge is thought to have been built by the Romans.', 'Pasiva impersonal con infinitivo perfecto: is thought to have been built.'],

        ['reading', 'report', 'What is the company accused of?', ['Selling wine without paying taxes', 'Selling bottles with fake labels', 'Exporting wine illegally'], 1, '"Bottles were allegedly sold with fake labels."'],
        ['reading', 'report', 'How was the problem discovered?', ['A journalist investigated the company.', 'A sommelier had bottles analysed.', 'An employee reported it.'], 1, '"A sommelier in Miami had several bottles analysed by an independent laboratory."'],
        ['reading', 'report', 'What does the company\'s spokesperson claim?', ['The director printed the labels.', 'An external supplier printed the labels.', 'The labels were correct.'], 1, '"The labels had been printed by an external supplier."'],
        ['reading', 'report', 'True or false? The director is said to have known about the problem for months.', ['True', 'False'], 0, 'True: "who is said to have known about the problem for months".'],

        ['speak', 'Decí la oración en voz alta.', "I had my hair cut last week, but nobody noticed.", 'Had my hair cut: acento en HAIR y CUT.'],
        ['speak', 'Leé el titular en voz alta.', 'The president is expected to announce new measures today.', 'Is expected to: /ɪz ɪkˈspektɪd tə/.'],

        ['write', 'Escribí una noticia breve (real o inventada) sobre un hecho de tu ciudad: un robo, un descubrimiento, una obra. Usá estructuras impersonales y pasivas.',
            ['min' => 100, 'must' => [['\b(is|are|was|were) (said|believed|thought|reported|expected|known|alleged|rumoured|estimated) to\b', 'Usás pasiva impersonal (is said to / is believed to…)'], ['\bit (is|was) (said|believed|thought|reported|expected|estimated|claimed) that\b', 'Usás It is said / believed that…'], ['\b(have|has|had|get|got|getting) (\w+ ){1,3}(fixed|repaired|done|cut|built|checked|analysed|cleaned|serviced|painted|delivered|installed|tested)\b', 'Usás el causativo (have/get something done)'], ['\b(allegedly|according to|spokesperson|evidence|investigate\w*)\b', 'Usás vocabulario periodístico']],
             'model' => "A famous mural in the centre of Rosario was damaged on Saturday night. It is believed that a group of young people threw paint at the wall after a football match. The mural, which was painted in 2010, is said to be one of the most photographed places in the city. According to the police, there is no evidence yet, but the incident is being investigated and several cameras are reported to have recorded the attack. A spokesperson for the city council said the mural would be restored. The council will have it cleaned by professional restorers next week and is expected to install new lights in the area."],
            'Las pasivas impersonales permiten informar sin afirmar algo como propio: The director is said to…'],
    ],
];
