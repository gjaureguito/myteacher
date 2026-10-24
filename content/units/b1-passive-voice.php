<?php
return [
    'icon' => '🏭',
    'vocab' => [
        ['produce', 'producir'], ['export', 'exportar'], ['design', 'diseñar'], ['discover', 'descubrir'],
        ['paint', 'pintar'], ['arrest', 'arrestar'], ['harvest', 'cosechar'], ['factory', 'fábrica'],
    ],
    'vocab_icons' => [
        'build (built)' => '🏗️', 'steal (stolen)' => '🕵️', 'invent' => '💡', 'deliver' => '📦', 'translate' => '🌐', 'grow' => '🌱', 'found' => '🏛️', 'damage' => '💥',
        'produce' => '🏭', 'export' => '🚢', 'design' => '📐', 'discover' => '🔭', 'paint' => '🎨', 'arrest' => '🚓', 'harvest' => '🌾', 'factory' => '🏭',
    ],
    'readings' => [
        'mate' => [
            'kind' => 'article',
            'title' => 'From the plantation to your mate',
            'text' => "Yerba mate is drunk by millions of people in Argentina, Uruguay, Paraguay and the south of Brazil. But how is it made?\n\nThe plant is grown mainly in the provinces of Misiones and Corrientes. The leaves are harvested by hand or with machines between April and October. After that, they are dried quickly over a fire so that they don't lose their green colour. This process is called sapecado.\n\nThen the leaves are taken to warehouses, where they are left to rest for several months, sometimes up to two years. This gives the yerba a softer taste. Finally, the leaves are ground, packed and delivered to supermarkets all over the country.\n\nToday Argentine yerba is exported to more than fifty countries. In Syria and Lebanon, for example, it has been popular for over a century.",
        ],
    ],
    'exercises' => [
        ['image', '🚢', ['export', 'deliver', 'arrest'], 0, 'Export: exportar. Argentina exports soya, beef and wine.'],
        ['image', '🌾', ['harvest', 'paint', 'discover'], 0, 'Harvest: cosechar.'],
        ['match', 'Uní cada verbo con su participio.', [['steal', 'stolen'], ['build', 'built'], ['grow', 'grown'], ['make', 'made'], ['give', 'given'], ['write', 'written']], 'La voz pasiva siempre usa el participio: is made, was built.'],
        ['mcq', 'Choose the word: The Eiffel Tower ___ in 1889.', ['was built', 'was stolen', 'was harvested'], 0, 'Build → built: fue construida.', 'skill' => 'vocabulary'],

        ['listen', '¿Dónde se fabrican los autos?', 'These cars are designed in Germany, but they are made in Brazil.', ['Se diseñan y fabrican en Alemania.', 'Se diseñan en Alemania y se fabrican en Brasil.', 'Se fabrican en Argentina.'], 1, '"Designed in Germany… made in Brazil."'],
        ['listen', '¿Qué pasó con el cuadro?', 'The painting was stolen from the museum last night, but the thief was arrested this morning.', ['Lo vendieron.', 'Lo robaron y arrestaron al ladrón.', 'Lo dañaron.'], 1, '"Was stolen… the thief was arrested."'],
        ['dictation', 'Escuchá y escribí la oración.', 'English is spoken all over the world.', 'Pasiva en presente: is + participio.'],

        ['reading', 'mate', 'Where is yerba mate mainly grown?', ['In Buenos Aires', 'In Misiones and Corrientes', 'In Syria'], 1, '"Grown mainly in the provinces of Misiones and Corrientes."'],
        ['reading', 'mate', 'Why are the leaves dried quickly over a fire?', ['To make them cheaper', 'So they don\'t lose their green colour', 'To export them faster'], 1, '"So that they don\'t lose their green colour."'],
        ['reading', 'mate', 'True or false? The leaves are packed immediately after drying.', ['True', 'False'], 1, 'False: primero se dejan descansar varios meses.'],
        ['reading', 'mate', 'How many countries is Argentine yerba exported to?', ['About five', 'More than fifty', 'Only to Brazil'], 1, '"Exported to more than fifty countries."'],

        ['speak', 'Decí la oración pasiva en voz alta.', 'Our products are made by hand in Mendoza.', 'Made by hand: unís made-by /meɪdbaɪ/.'],
        ['speak', 'Decí la oración en voz alta.', 'The bridge was built more than a hundred years ago.', 'Built suena /bɪlt/: la u no se pronuncia.'],

        ['write', 'Explicá cómo se hace un producto típico de tu región (vino, dulce de leche, alfajores, café…): de dónde viene y qué pasos tiene.',
            ['min' => 70, 'must' => [["\b(is|are) (\w+ed|made|grown|built|done|sold|taken|kept|given|eaten|drunk|cut|put|left|brought|ground)\b", 'Usás la pasiva en presente (is/are + participio)'], ['\b(was|were) (\w+ed|made|grown|built|founded|invented|discovered|brought|taken)\b', 'Usás la pasiva en pasado (was/were + participio)'], ['\b(first|then|after that|finally|next)\b', 'Ordenás los pasos (first, then, finally…)'], ['\bby\b', 'Mencionás quién lo hace con by']],
             'model' => "Dulce de leche is eaten all over Argentina, but the most famous brands are produced in the province of Buenos Aires. It is said that it was discovered by accident in the nineteenth century. First, fresh milk is mixed with sugar and a little baking soda. Then the mixture is heated slowly for several hours and it is stirred all the time by the cooks or by machines. After that, it is cooled and packed in jars. Finally, it is sold in supermarkets and exported to many countries."],
            'Usá la pasiva cuando importa la acción o el objeto, no quién la hace.'],
    ],
];
