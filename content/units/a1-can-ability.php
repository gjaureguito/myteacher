<?php
return [
    'icon' => '🎸',
    'title' => 'Can y can\'t: habilidades, permisos y pedidos',
    'summary' => 'Decir lo que sabés o podés hacer, pedir permiso y pedir favores con can y could.',
    'theory' => <<<'HTML'
<h2>Una palabra, tres usos</h2>
<p><strong>Can</strong> expresa habilidad (<em>saber / poder</em>), permiso y posibilidad. Es igual para todas las personas: no lleva -s y va seguido del verbo en forma base.</p>
<p class="ex">I can swim. <span class="es">Sé nadar.</span></p>
<p class="ex">Can I open the window? <span class="es">¿Puedo abrir la ventana?</span></p>
<p class="ex">You can park here. <span class="es">Podés estacionar acá.</span></p>

<h2>Cómo se forma</h2>
<table class="tbl">
<thead><tr><th>Afirmativa</th><th>Negativa</th><th>Pregunta</th><th>Respuesta corta</th></tr></thead>
<tbody>
<tr><td>She can drive.</td><td>She can't drive.</td><td>Can she drive?</td><td>Yes, she can. / No, she can't.</td></tr>
<tr><td>They can come.</td><td>They cannot come.</td><td>Can they come?</td><td>Yes, they can. / No, they can't.</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> agregar <em>to</em> o <em>-s</em>. No se dice <em>He cans</em> ni <em>I can to swim</em>: es <strong>He can swim</strong>.</div>

<h2>Pronunciación: can vs can't</h2>
<p>En la afirmativa <em>can</em> suena débil, casi /kən/. En la negativa <em>can't</em> se acentúa y la vocal es larga en inglés británico /kɑːnt/ o abierta en americano /kænt/. Prestá atención a la fuerza de la palabra más que a la t final.</p>
<p class="ex">I can cook, but I can't bake. <span class="es">Sé cocinar, pero no sé hornear.</span></p>

<h2>Pedidos amables: can y could</h2>
<p>Para pedir algo usamos <strong>Can you…?</strong> o, más formal, <strong>Could you…?</strong></p>
<p class="ex">Can you help me, please? <span class="es">¿Me podés ayudar, por favor?</span></p>
<p class="ex">Could you repeat that, please? <span class="es">¿Podría repetir eso, por favor?</span></p>
<div class="tip">Para hablar de habilidades en el pasado usá <strong>could / couldn't</strong>: <em>I could read when I was four.</em></div>
HTML,
    'vocab' => [
        ['play the guitar', 'tocar la guitarra'], ['ride a bike', 'andar en bici'], ['speak', 'hablar (un idioma)'], ['sing', 'cantar'],
        ['draw', 'dibujar'], ['cook', 'cocinar'], ['ski', 'esquiar'], ['play chess', 'jugar al ajedrez'],
        ['help', 'ayudar'], ['borrow', 'pedir prestado'], ['open', 'abrir'], ['close', 'cerrar'],
        ['repeat', 'repetir'], ['park', 'estacionar'], ['well', 'bien'], ['at all', 'para nada'],
    ],
    'vocab_icons' => [
        'play the guitar' => '🎸', 'ride a bike' => '🚲', 'speak' => '🗣️', 'sing' => '🎤', 'draw' => '🎨', 'cook' => '👩‍🍳', 'ski' => '⛷️', 'play chess' => '♟️',
        'help' => '🆘', 'borrow' => '🤲', 'open' => '🔓', 'close' => '🔒', 'repeat' => '🔁', 'park' => '🅿️', 'well' => '👍', 'at all' => '🙅',
    ],
    'readings' => [
        'club' => [
            'kind' => 'ad',
            'title' => 'Volunteers wanted at Club Andino',
            'text' => "Can you help children enjoy the mountains? Our club needs volunteers for the winter holidays in July.\n\nWe are looking for people who can ski or snowboard well. You don't need to be an instructor, but you must be patient and friendly. You can't smoke near the children and you can't bring pets.\n\nCan you speak English or Portuguese? Great! A lot of our visitors are from Brazil and the United States.\n\nVolunteers can stay at the club for free and they can use the equipment on their free afternoons. Can't ski? No problem: you can help in the kitchen or at reception. Send us an email at volunteers@clubandino.example.",
        ],
    ],
    'exercises' => [
        ['mcq', 'My grandmother ___ use a smartphone very well.', ['cans', 'can', 'can to'], 1, 'Can no cambia con she/he y va seguido del verbo sin to.'],
        ['mcq', '___ you speak French?', ['Do', 'Can', 'Are'], 1, 'Para preguntar por una habilidad: Can you…?'],
        ['fill', 'I\'m sorry, I ___ come to your party. I have to work.', ["can't", 'cannot', 'can not'], 'Negativa de can: can\'t / cannot.'],
        ['mcq', 'Can your sister drive? — Yes, she ___ .', ['does', 'is', 'can'], 2, 'La respuesta corta repite el auxiliar: Yes, she can.'],
        ['mcq', 'When I was five, I ___ ride a bike.', ['can', 'could', 'can to'], 1, 'Habilidad en el pasado: could.'],
        ['fill', '___ you repeat that, please? I didn\'t understand.', ['Could', 'Can'], 'Pedido amable: Could you…? / Can you…?'],
        ['mcq', 'Choose the correct sentence.', ['He can plays chess.', 'He can play chess.', 'He cans play chess.'], 1, 'Can + verbo base: He can play chess.'],
        ['order', 'Ordená la pregunta.', 'Can I borrow your pen?', 'Pedir permiso: Can + I + verbo base.'],
        ['order', 'Ordená la oración.', "They can't swim very well.", 'Sujeto + can\'t + verbo + complemento.'],

        ['image', '🎸', ['sing', 'play the guitar', 'draw'], 1, 'Play the guitar: tocar la guitarra. Con instrumentos usamos the.'],
        ['image', '⛷️', ['ski', 'ride a bike', 'cook'], 0, 'Ski: esquiar.'],
        ['image', '♟️', ['play chess', 'play the guitar', 'park'], 0, 'Play chess: jugar al ajedrez. Con juegos no usamos the.'],
        ['match', 'Uní cada pedido con su respuesta más lógica.', [['Can I sit here?', 'Sure, go ahead.'], ['Can you help me?', 'Of course. What do you need?'], ['Could you close the door?', 'Yes, no problem.'], ['Can I pay by card?', "Sorry, cash only."]], 'Respuestas típicas a pedidos y permisos.'],

        ['listen', '¿Qué sabe hacer Martina?', "Martina can sing really well, but she can't play any instruments.", ['Tocar la guitarra', 'Cantar', 'Bailar'], 1, '"She can sing really well, but she can\'t play any instruments."'],
        ['listen', '¿Qué pide la persona?', 'Excuse me, could you tell me where the toilets are?', ['Dónde están los baños', 'La hora', 'Una birome'], 0, '"Could you tell me where the toilets are?"'],
        ['dictation', 'Escuchá y escribí la oración.', "I can't find my keys.", "Can't + verbo base: I can't find."],

        ['reading', 'club', 'When does the club need volunteers?', ['In January', 'In July', 'All year'], 1, '"Volunteers for the winter holidays in July."'],
        ['reading', 'club', 'True or false? You must be a ski instructor.', ['True', 'False'], 1, 'False: "You don\'t need to be an instructor".'],
        ['reading', 'club', 'Which languages are useful?', ['English and Portuguese', 'French and Italian', 'Only Spanish'], 0, '"Can you speak English or Portuguese?"'],
        ['reading', 'club', "What can you do if you can't ski?", ['Teach snowboarding', 'Help in the kitchen or at reception', 'Bring your pet'], 1, '"You can help in the kitchen or at reception."'],

        ['speak', 'Hablá de tus habilidades: decí la oración en voz alta.', "I can cook, but I can't sing.", 'Can débil /kən/, can\'t fuerte. Esa diferencia es lo que más se escucha.'],
        ['speak', 'Hacé un pedido amable en voz alta.', 'Could you help me with my bags, please?', 'Could you se une: /kʊdʒu/.'],

        ['write', 'Escribí un email corto para ser voluntario/a en el Club Andino: qué sabés hacer, qué idiomas hablás y qué no sabés hacer.',
            ['min' => 40, 'must' => [['\bcan (\w+)\b', 'Usás can + verbo'], ["\b(can't|cannot|can not)\b", 'Incluís algo que no sabés hacer (can\'t)'], ['\b(dear|hello|hi)\b', 'Empezás con un saludo'], ['\b(regards|thanks|thank you|best wishes|bye)\b', 'Cerrás el email con una despedida']],
             'model' => "Hello,\n\nMy name is Tomás and I'm twenty-four. I'd like to be a volunteer this July. I can ski quite well and I can also cook. I speak Spanish and English, and I can understand a little Portuguese. I can't snowboard, but I can learn! I love working with children.\n\nBest wishes,\nTomás"],
            'Después de can el verbo va siempre en forma base, sin to ni -s.'],
    ],
];
