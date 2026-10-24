<?php
return [
    'icon' => '🕰️',
    'title' => 'El pasado de to be: was y were',
    'summary' => 'Hablar de dónde estabas, cómo era algo y qué había: was, were, there was, there were.',
    'theory' => <<<'HTML'
<h2>Dos formas en pasado</h2>
<p>El verbo <strong>to be</strong> tiene dos formas en pasado: <strong>was</strong> y <strong>were</strong>. Equivalen a <em>era / estaba / fue</em> según el contexto.</p>
<table class="tbl">
<thead><tr><th>Sujeto</th><th>Afirmativa</th><th>Negativa</th><th>Pregunta</th></tr></thead>
<tbody>
<tr><td>I / he / she / it</td><td>was</td><td>wasn't</td><td>Was she…?</td></tr>
<tr><td>you / we / they</td><td>were</td><td>weren't</td><td>Were they…?</td></tr>
</tbody></table>
<p class="ex">I was at home yesterday. <span class="es">Ayer estuve en casa.</span></p>
<p class="ex">The film wasn't very good. <span class="es">La película no estuvo muy buena.</span></p>
<p class="ex">Were you tired? — Yes, I was. <span class="es">¿Estabas cansado? — Sí.</span></p>

<h2>There was / there were</h2>
<p>Es el pasado de <em>there is / there are</em> (<em>había / hubo</em>).</p>
<p class="ex">There was a problem with the train. <span class="es">Hubo un problema con el tren.</span></p>
<p class="ex">There were a lot of people at the concert. <span class="es">Había mucha gente en el recital.</span></p>

<h2>Expresiones de tiempo pasado</h2>
<p><strong>yesterday, last night, last week, last year, two days ago, in 2015, when I was a child</strong></p>
<div class="warn"><strong>Error típico:</strong> decir <em>I was born in 1995</em> está bien, pero no <em>I born</em>. "Nacer" en inglés se expresa con <strong>was / were born</strong>.</div>
<div class="tip"><strong>Ago</strong> va después del período: <em>three years ago</em> (hace tres años).</div>
HTML,
    'vocab' => [
        ['yesterday', 'ayer'], ['last night', 'anoche'], ['ago', 'hace (tiempo)'], ['was born', 'nació / nací'],
        ['concert', 'recital / concierto'], ['party', 'fiesta'], ['beach', 'playa'], ['museum', 'museo'],
        ['crowded', 'lleno de gente'], ['boring', 'aburrido/a'], ['delicious', 'delicioso/a'], ['sunny', 'soleado'],
        ['childhood', 'infancia'], ['school', 'escuela'], ['holiday', 'vacaciones / feriado'], ['weather', 'clima / tiempo'],
    ],
    'vocab_icons' => [
        'yesterday' => '📅', 'last night' => '🌃', 'ago' => '⏪', 'was born' => '👶', 'concert' => '🎵', 'party' => '🥳', 'beach' => '🏖️', 'museum' => '🏛️',
        'crowded' => '👥', 'boring' => '🥱', 'delicious' => '😋', 'sunny' => '☀️', 'childhood' => '🧸', 'school' => '🏫', 'holiday' => '🧳', 'weather' => '🌤️',
    ],
    'readings' => [
        'postcard' => [
            'kind' => 'email',
            'title' => 'A weekend in Colonia',
            'text' => "Hi Ines,\n\nI'm back from Colonia! The trip was great. The weather was sunny and warm, and the old town was really beautiful. There were a lot of tourists on Saturday, but on Sunday the streets were quiet.\n\nThe hotel wasn't expensive and the room was big, but the breakfast was terrible! There was only bread and coffee. The best part was the restaurant near the port: the fish was delicious.\n\nWere you at Diego's party last night? Was it fun? I was very tired after the ferry, so I was in bed at ten.\n\nBig hug,\nCarla",
        ],
    ],
    'exercises' => [
        ['mcq', 'I ___ at the cinema last night.', ['were', 'was', 'am'], 1, 'I + was.'],
        ['mcq', 'They ___ very happy with the hotel.', ['was', 'were', 'is'], 1, 'They + were.'],
        ['fill', 'The film ___ (not) good. It was boring.', ["wasn't", 'was not'], 'Negativa con it/the film: wasn\'t.'],
        ['mcq', '___ you at home yesterday?', ['Was', 'Were', 'Did'], 1, 'Pregunta con you: Were you…?'],
        ['mcq', 'There ___ a lot of people at the concert.', ['was', 'were', 'is'], 1, 'People es plural: there were.'],
        ['fill', 'I ___ born in Tucumán in 1998.', ['was'], '"Nacer" = be born: I was born.'],
        ['mcq', 'Was the museum open? — No, it ___ .', ["wasn't", "weren't", "didn't"], 0, 'Respuesta corta con it: No, it wasn\'t.'],
        ['order', 'Ordená la pregunta.', 'Where were you last weekend?', 'Palabra interrogativa + were + sujeto + tiempo.'],
        ['order', 'Ordená la oración.', 'There was a problem with the train.', 'There was + sustantivo singular.'],

        ['image', '🏖️', ['beach', 'museum', 'school'], 0, 'Beach: playa.'],
        ['image', '🥱', ['delicious', 'crowded', 'boring'], 2, 'Boring: aburrido. Ojo: bored es "aburrido/a" como estado de la persona.'],
        ['image', '🎵', ['party', 'concert', 'holiday'], 1, 'Concert: recital o concierto.'],
        ['match', 'Uní cada expresión con su significado.', [['yesterday', 'ayer'], ['last night', 'anoche'], ['two days ago', 'hace dos días'], ['last year', 'el año pasado'], ['when I was a child', 'cuando era chico']], 'Expresiones de tiempo para el pasado.'],

        ['listen', '¿Dónde estuvo Lucas el sábado?', 'On Saturday I was at my grandparents\' house in Tandil. It was cold but lovely.', ['En Tandil', 'En Córdoba', 'En su casa'], 0, '"I was at my grandparents\' house in Tandil."'],
        ['listen', '¿Cómo estuvo la fiesta?', "The party wasn't very good. The music was too loud and there weren't many people.", ['Excelente', 'Muy aburrida y vacía', 'No tan buena'], 2, '"The party wasn\'t very good": la música muy fuerte y poca gente.'],
        ['dictation', 'Escuchá y escribí la oración.', 'We were at the beach two days ago.', 'We + were. Ago va después del período: two days ago.'],

        ['reading', 'postcard', 'How was the weather in Colonia?', ['Rainy and cold', 'Sunny and warm', 'Windy'], 1, '"The weather was sunny and warm."'],
        ['reading', 'postcard', 'True or false? The breakfast at the hotel was very good.', ['True', 'False'], 1, 'False: "the breakfast was terrible!"'],
        ['reading', 'postcard', 'What was the best part of the trip?', ['The hotel', 'The ferry', 'The restaurant near the port'], 2, '"The best part was the restaurant near the port."'],
        ['reading', 'postcard', 'Why wasn\'t Carla at the party?', ['She was in Colonia.', 'She was tired after the ferry.', 'She was at work.'], 1, '"I was very tired after the ferry, so I was in bed at ten."'],

        ['speak', 'Contá dónde estuviste: decí la oración en voz alta.', 'I was at home last night. It was very cold.', 'Was en oración afirmativa suena débil: /wəz/.'],
        ['speak', 'Preguntá en voz alta.', 'Where were you yesterday?', 'Were y where suenan parecido: /weə/ y /wɜː/. Practicá la diferencia.'],

        ['write', 'Contá un fin de semana o unas vacaciones que recuerdes: dónde estuviste, cómo era el lugar, cómo estuvo el clima y qué había.',
            ['min' => 40, 'must' => [['\bwas\b', 'Usás was'], ['\bwere\b', 'Usás were'], ['\bthere (was|were)\b', 'Usás there was / there were'], ["\b(wasn't|weren't|was not|were not)\b", 'Incluís una negativa'], ['\b(ago|last|yesterday|in (19|20)\d\d)\b', 'Decís cuándo (ago, last…, in 2019)']],
             'model' => "Two years ago I was in Salta with my brother. It was our first trip to the north. The weather was perfect: it was sunny every day and it wasn't too hot. The hostel was small but clean, and there were a lot of young travellers from other countries. The food was amazing and the mountains were beautiful. The bus trips were long, but we weren't bored."],
            'Was con I/he/she/it; were con you/we/they.'],
    ],
];
