<?php
return [
    'icon' => '📸',
    'title' => 'Presente continuo: lo que está pasando ahora',
    'summary' => 'Am / is / are + -ing para acciones en curso. Diferencia entre "ahora" y "siempre".',
    'theory' => <<<'HTML'
<h2>¿Para qué sirve?</h2>
<p>El presente continuo describe <strong>lo que está pasando en este momento</strong> o en un período actual. En español equivale a <em>estar + -ando / -iendo</em>.</p>
<p class="ex">I am cooking dinner. <span class="es">Estoy cocinando la cena.</span></p>
<p class="ex">They're watching a film. <span class="es">Están mirando una película.</span></p>

<h2>Cómo se forma</h2>
<table class="tbl">
<thead><tr><th>Afirmativa</th><th>Negativa</th><th>Pregunta</th></tr></thead>
<tbody>
<tr><td>I am working</td><td>I'm not working</td><td>Am I working?</td></tr>
<tr><td>He / she / it is working</td><td>She isn't working</td><td>Is she working?</td></tr>
<tr><td>You / we / they are working</td><td>We aren't working</td><td>Are they working?</td></tr>
</tbody></table>

<h2>Reglas de ortografía del -ing</h2>
<table class="tbl">
<thead><tr><th>Verbo</th><th>Regla</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>la mayoría</td><td>+ ing</td><td>read → reading</td></tr>
<tr><td>termina en -e</td><td>se va la e</td><td>write → writing, dance → dancing</td></tr>
<tr><td>consonante-vocal-consonante corto</td><td>se duplica la última</td><td>run → running, sit → sitting</td></tr>
<tr><td>termina en -ie</td><td>ie → y</td><td>lie → lying</td></tr>
</tbody></table>

<h2>Ahora o siempre</h2>
<p>Compará el presente simple (hábitos) con el continuo (ahora):</p>
<p class="ex">I usually walk to work, but today I'm taking a taxi. <span class="es">Normalmente camino al trabajo, pero hoy me tomo un taxi.</span></p>
<p class="ex">What are you doing? — I'm studying. <span class="es">¿Qué estás haciendo? — Estoy estudiando.</span></p>
<div class="tip">Palabras que acompañan al continuo: <strong>now, right now, at the moment, today, this week, Look!, Listen!</strong></div>
<div class="warn"><strong>Error típico:</strong> olvidarse del verbo to be. No se dice <em>I working</em> sino <em>I am working</em>. Y algunos verbos de estado casi nunca van en continuo: <em>I want, I like, I know, I need</em> (no <em>I'm wanting</em>).</div>
HTML,
    'vocab' => [
        ['study', 'estudiar'], ['run', 'correr'], ['swim', 'nadar'], ['read', 'leer'],
        ['write', 'escribir'], ['sleep', 'dormir'], ['wait', 'esperar'], ['rain', 'llover'],
        ['dance', 'bailar'], ['laugh', 'reírse'], ['drive', 'manejar'], ['talk on the phone', 'hablar por teléfono'],
        ['right now', 'ahora mismo'], ['at the moment', 'en este momento'], ['wear', 'tener puesto / usar ropa'], ['carry', 'llevar (en la mano)'],
    ],
    'vocab_icons' => [
        'study' => '📚', 'run' => '🏃', 'swim' => '🏊', 'read' => '📖', 'write' => '✍️', 'sleep' => '😴', 'wait' => '⏳', 'rain' => '🌧️',
        'dance' => '💃', 'laugh' => '😂', 'drive' => '🚗', 'talk on the phone' => '📱', 'right now' => '⚡', 'at the moment' => '⌚', 'wear' => '🧥', 'carry' => '🛍️',
    ],
    'readings' => [
        'messages' => [
            'kind' => 'chat',
            'title' => 'Where are you?',
            'text' => "Sofi: Hi Leo! Where are you? The film is starting in ten minutes!\n\nLeo: Sorry! I'm on the bus. It's raining a lot and the traffic is terrible. The bus isn't moving at all.\n\nSofi: Oh no! Are you coming or not?\n\nLeo: Yes, I'm coming! I'm reading the news on my phone and waiting. A man next to me is sleeping and two kids are laughing at a video.\n\nSofi: Haha. Well, I'm buying the popcorn now. Your sister is here too. She's wearing that red jacket you love.\n\nLeo: Great! The bus is moving again. See you in fifteen minutes!",
        ],
    ],
    'exercises' => [
        ['mcq', 'Look! It ___ .', ['rains', 'is raining', 'rain'], 1, 'Look! indica que pasa ahora: presente continuo, it is raining.'],
        ['mcq', 'She ___ TV at the moment.', ['watches', 'is watching', 'watching'], 1, 'At the moment pide continuo y necesita el verbo to be: is watching.'],
        ['fill', 'They ___ (play) football in the park right now.', ['are playing', "'re playing"], 'They + are + verbo -ing.'],
        ['fill', 'I ___ (not / work) today. It\'s a holiday.', ["'m not working", 'am not working'], 'Negativa: I am not / I\'m not + -ing.'],
        ['mcq', 'What ___ you doing?', ['do', 'are', 'is'], 1, 'Pregunta en continuo: What + are + you + -ing?'],
        ['mcq', 'Spelling: swim → ___', ['swiming', 'swimming', 'swimeing'], 1, 'Consonante-vocal-consonante: se duplica la m → swimming.'],
        ['mcq', 'I usually ___ to work, but today I\'m driving.', ['walk', 'am walking', 'walking'], 0, 'Usually indica hábito: presente simple, walk.'],
        ['order', 'Ordená la pregunta.', 'Is your brother sleeping now?', 'Pregunta: Is + sujeto + -ing.'],
        ['order', 'Ordená la oración.', "We aren't watching the match.", 'Sujeto + aren\'t + -ing + complemento.'],

        ['image', '🏊', ['run', 'swim', 'dance'], 1, 'Swim: nadar. En continuo: swimming.'],
        ['image', '🌧️', ['rain', 'sleep', 'wait'], 0, 'Rain: llover. It\'s raining: está lloviendo.'],
        ['image', '📱', ['talk on the phone', 'write', 'read'], 0, 'Talk on the phone: hablar por teléfono.'],
        ['match', 'Uní cada verbo con su forma -ing.', [['write', 'writing'], ['run', 'running'], ['study', 'studying'], ['lie', 'lying'], ['dance', 'dancing']], 'Write/dance pierden la e, run duplica la n, lie cambia ie por y.'],

        ['listen', '¿Qué está haciendo Pablo?', "Pablo can't talk now. He's having a shower.", ['Está comiendo.', 'Se está duchando.', 'Está manejando.'], 1, '"He\'s having a shower": se está duchando.'],
        ['listen', '¿Por qué no contesta Ana?', "Ana isn't answering because she's driving to Mar del Plata.", ['Está durmiendo.', 'Está manejando.', 'Está en una reunión.'], 1, '"She\'s driving to Mar del Plata."'],
        ['dictation', 'Escuchá y escribí la oración.', 'The children are playing in the garden.', 'Children es plural: are playing.'],

        ['reading', 'messages', 'Where is Leo?', ['At the cinema', 'On the bus', 'At home'], 1, '"I\'m on the bus."'],
        ['reading', 'messages', 'Why is Leo late?', ['Because it is raining and there is a lot of traffic.', 'Because he is sleeping.', 'Because he is buying popcorn.'], 0, '"It\'s raining a lot and the traffic is terrible."'],
        ['reading', 'messages', 'True or false? A man on the bus is laughing.', ['True', 'False'], 1, 'False: el hombre está durmiendo; los chicos se están riendo.'],
        ['reading', 'messages', 'What is Sofi doing now?', ['She is waiting for the bus.', 'She is buying popcorn.', 'She is watching the film.'], 1, '"I\'m buying the popcorn now."'],

        ['speak', 'Contá qué estás haciendo: decí la oración en voz alta.', "I'm studying English at the moment.", 'Unís I\'m: /aɪm/. Marcá bien la terminación -ing /ɪŋ/ sin agregar una g fuerte.'],
        ['speak', 'Preguntá en voz alta.', 'What are you doing right now?', 'What are you se pronuncia unido: /wɒtəjə/.'],

        ['write', 'Mirá a tu alrededor (o imaginá un lugar: un bar, una plaza, tu oficina) y describí qué está haciendo la gente en este momento.',
            ['min' => 40, 'must' => [["\b(am|is|are|'m|'s|'re) \w+ing\b", 'Usás el presente continuo (am/is/are + -ing)'], ["\b(isn't|aren't|'m not|is not|are not)\b \w+ing", 'Incluís una negativa en continuo'], ['\b(now|right now|at the moment|today)\b', 'Usás now, right now o at the moment']],
             'model' => "Right now I'm sitting in a café near my office. It's raining outside, so the place is full. A waiter is carrying two coffees and a young couple is talking and laughing. A man next to me is working on his laptop. He isn't drinking his coffee; he's writing an email very fast. Two kids are drawing on paper napkins and their mother is reading a magazine."],
            'El continuo siempre necesita am/is/are antes del verbo con -ing.'],
    ],
];
