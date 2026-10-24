<?php
return [
    'icon' => '🗺️',
    'title' => 'La ciudad: imperativos, lugares y cómo llegar',
    'summary' => 'Dar y pedir indicaciones, preposiciones de lugar y el imperativo para instrucciones.',
    'theory' => <<<'HTML'
<h2>El imperativo</h2>
<p>Para dar instrucciones usamos el verbo en forma base, sin sujeto. Para la negativa, <strong>don't</strong> + verbo.</p>
<p class="ex">Turn left at the bank. <span class="es">Doblá a la izquierda en el banco.</span></p>
<p class="ex">Don't cross the street here. <span class="es">No crucés la calle acá.</span></p>
<div class="tip">Para sonar amable agregá <strong>please</strong>: <em>Please sit down.</em> Para proponer algo juntos usá <strong>Let's</strong>: <em>Let's take a taxi.</em></div>

<h2>Preposiciones de lugar</h2>
<table class="tbl">
<thead><tr><th>Inglés</th><th>Significado</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>next to</td><td>al lado de</td><td>The café is next to the bank.</td></tr>
<tr><td>opposite</td><td>enfrente de</td><td>The school is opposite the park.</td></tr>
<tr><td>between</td><td>entre</td><td>It's between the pharmacy and the bakery.</td></tr>
<tr><td>on the corner of</td><td>en la esquina de</td><td>on the corner of Corrientes and Callao</td></tr>
<tr><td>behind / in front of</td><td>detrás de / delante de</td><td>The car park is behind the hotel.</td></tr>
</tbody></table>

<h2>Pedir indicaciones</h2>
<p class="ex">Excuse me, how do I get to the station? <span class="es">Disculpe, ¿cómo llego a la estación?</span></p>
<p class="ex">Is there a pharmacy near here? <span class="es">¿Hay una farmacia cerca?</span></p>
<p class="ex">Go straight on for two blocks and turn right. <span class="es">Seguí derecho dos cuadras y doblá a la derecha.</span></p>
<div class="warn"><strong>Error típico:</strong> "cuadra" no es <em>square</em>. En inglés se dice <strong>block</strong>. <em>Square</em> es una plaza.</div>
HTML,
    'vocab' => [
        ['turn left', 'doblar a la izquierda'], ['turn right', 'doblar a la derecha'], ['go straight on', 'seguir derecho'], ['cross', 'cruzar'],
        ['block', 'cuadra'], ['traffic lights', 'semáforo'], ['next to', 'al lado de'], ['opposite', 'enfrente de'],
        ['between', 'entre'], ['pharmacy', 'farmacia'], ['bank', 'banco'], ['train station', 'estación de tren'],
        ['hospital', 'hospital'], ['square', 'plaza'], ['bridge', 'puente'], ['map', 'mapa'],
    ],
    'vocab_icons' => [
        'turn left' => '⬅️', 'turn right' => '➡️', 'go straight on' => '⬆️', 'cross' => '🚸', 'block' => '🏙️', 'traffic lights' => '🚦', 'next to' => '↔️', 'opposite' => '🔁',
        'between' => '↕️', 'pharmacy' => '💊', 'bank' => '🏦', 'train station' => '🚉', 'hospital' => '🏥', 'square' => '⛲', 'bridge' => '🌉', 'map' => '🗺️',
    ],
    'readings' => [
        'way' => [
            'kind' => 'chat',
            'title' => 'How to get to my place',
            'text' => "Hi Sam! Here are the directions to my flat for tonight.\n\nTake the subway, line D, and get off at Plaza Italia. Go out of the station and walk along Santa Fe Avenue towards the zoo. Don't take a taxi there: the traffic is terrible on Fridays!\n\nGo straight on for three blocks. At the traffic lights, turn right into Gurruchaga Street. My building is on the left, between a pharmacy and a small bookshop. It's opposite a big green school.\n\nRing the bell for flat 5B. If the bell doesn't work, call me and I'll come down. Bring a jacket, it's cold on the balcony!",
        ],
    ],
    'exercises' => [
        ['mcq', '___ the street at the traffic lights. It\'s safer.', ['Crossing', 'Cross', 'You crosses'], 1, 'Imperativo: verbo en forma base sin sujeto.'],
        ['mcq', '___ park here. It\'s a bus stop.', ["Don't", 'Not', "Doesn't"], 0, 'Imperativo negativo: Don\'t + verbo.'],
        ['fill', 'The bank is ___ to the supermarket. They\'re side by side.', ['next'], 'Next to = al lado de.'],
        ['mcq', 'The café is ___ the bookshop and the bank.', ['between', 'opposite', 'next'], 0, 'Between = entre dos cosas.'],
        ['mcq', 'Excuse me, how do I ___ to the station?', ['go', 'get', 'arrive'], 1, 'Expresión fija: How do I get to…?'],
        ['fill', 'Go straight on for two ___ and turn left.', ['blocks'], 'Cuadra = block. En plural: blocks.'],
        ['mcq', "It's a beautiful day. ___ go to the park!", ["Let's", 'Lets', 'Let'], 0, 'Let\'s + verbo para proponer algo juntos.'],
        ['order', 'Ordená la pregunta.', 'Is there a pharmacy near here?', 'Is there + a + lugar + near here?'],
        ['order', 'Ordená la indicación.', 'Turn left at the traffic lights.', 'Imperativo + dirección + referencia.'],

        ['image', '🚦', ['bridge', 'traffic lights', 'square'], 1, 'Traffic lights: semáforo (siempre en plural).'],
        ['image', '💊', ['bank', 'hospital', 'pharmacy'], 2, 'Pharmacy: farmacia. En EE. UU. también drugstore.'],
        ['image', '⬅️', ['turn left', 'turn right', 'go straight on'], 0, 'Turn left: doblar a la izquierda.'],
        ['match', 'Uní cada preposición con su significado.', [['next to', 'al lado de'], ['opposite', 'enfrente de'], ['between', 'entre'], ['behind', 'detrás de'], ['in front of', 'delante de']], 'Preposiciones de lugar más frecuentes.'],

        ['listen', '¿Hacia dónde tiene que doblar?', 'Go straight on and take the second street on the right.', ['La primera a la izquierda', 'La segunda a la derecha', 'Seguir derecho'], 1, '"Take the second street on the right."'],
        ['listen', '¿Dónde está el hotel?', "The hotel is on the corner, opposite the square. You can't miss it.", ['Enfrente de la plaza', 'Al lado del banco', 'Detrás de la estación'], 0, '"Opposite the square": enfrente de la plaza.'],
        ['dictation', 'Escuchá y escribí la indicación.', "Don't turn right, turn left.", "Imperativo negativo: Don't + verbo."],

        ['reading', 'way', 'Where does Sam get off the subway?', ['At Plaza Italia', 'At the zoo', 'At Gurruchaga'], 0, '"Get off at Plaza Italia."'],
        ['reading', 'way', 'Why shouldn\'t Sam take a taxi?', ['It\'s expensive.', 'The traffic is terrible on Fridays.', 'There are no taxis.'], 1, '"Don\'t take a taxi there: the traffic is terrible on Fridays!"'],
        ['reading', 'way', 'Where is the building?', ['Opposite a pharmacy', 'Between a pharmacy and a bookshop', 'Next to the zoo'], 1, '"Between a pharmacy and a small bookshop."'],
        ['reading', 'way', 'True or false? Sam must turn left at the traffic lights.', ['True', 'False'], 1, 'False: "At the traffic lights, turn right."'],

        ['speak', 'Pedí indicaciones en voz alta.', 'Excuse me, how do I get to the train station?', 'Excuse me: la x suena /ks/ y la z /z/: /ɪkˈskjuːz miː/.'],
        ['speak', 'Dá una indicación en voz alta.', 'Go straight on for two blocks and turn right.', 'Straight tiene el sonido /eɪ/ como en "eight".'],

        ['write', 'Un amigo extranjero viene a tu casa desde la estación o la parada más cercana. Escribile cómo llegar, con referencias del barrio.',
            ['min' => 40, 'must' => [['\b(turn|go|take|cross|walk|get off)\b', 'Usás imperativos (turn, go, take…)'], ["\bdon't\b", 'Incluís un imperativo negativo (don\'t…)'], ['\b(next to|opposite|between|behind|in front of|on the corner)\b', 'Usás una preposición de lugar'], ['\b(left|right|straight)\b', 'Indicás direcciones (left, right, straight on)']],
             'model' => "Get off the bus at the stop in front of the hospital. Cross the avenue at the traffic lights and walk along Rivadavia Street. Go straight on for two blocks. Don't turn into the small street with the market; turn left at the next corner. My house is the blue one, next to a bakery and opposite a little square. Ring the bell twice!"],
            'El imperativo no lleva sujeto: Turn left, no You turn left.'],
    ],
];
