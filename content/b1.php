<?php
return [
    'code' => 'b1',
    'name' => 'Intermedio',
    'description' => 'Hablar de experiencias, condiciones, procesos y obligaciones con soltura.',
    'position' => 3,
    'units' => [
        [
            'slug' => 'b1-present-perfect',
            'title' => 'Presente perfecto vs pasado simple',
            'summary' => 'Have done o did: experiencias, resultados actuales, for, since, already, yet.',
            'theory' => <<<'HTML'
<h2>El puente entre pasado y presente</h2>
<p>El presente perfecto (<strong>have/has + participio</strong>) conecta una acción pasada con el presente. El pasado simple, en cambio, la deja cerrada en un momento terminado.</p>
<table class="tbl">
<thead><tr><th>Presente perfecto</th><th>Pasado simple</th></tr></thead>
<tbody>
<tr><td>No importa cuándo: I've been to Chile.</td><td>Momento concreto: I went to Chile in 2022.</td></tr>
<tr><td>Período que sigue abierto: this week, today</td><td>Período cerrado: last week, yesterday</td></tr>
<tr><td>Resultado actual: I've lost my keys (no las tengo).</td><td>Relato: I lost my keys and found them later.</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> nunca uses el presente perfecto con un tiempo pasado terminado. <em>I have seen him yesterday</em> es incorrecto: <em>I saw him yesterday</em>.</div>

<h2>For y since</h2>
<p>Para algo que empezó en el pasado y <strong>sigue</strong>:</p>
<p class="ex">I've worked here for five years. <span class="es">Trabajo acá hace cinco años.</span></p>
<p class="ex">I've worked here since 2020. <span class="es">Trabajo acá desde 2020.</span></p>
<p><strong>for</strong> + duración (two hours, a long time) · <strong>since</strong> + punto de inicio (Monday, 2020, I was a child).</p>
<div class="tip">En español decimos "trabajo acá hace cinco años" en presente. En inglés eso es presente perfecto, nunca <em>I work here for five years</em>.</div>

<h2>Already, yet, just, ever, never</h2>
<p class="ex">I've already finished. <span class="es">Ya terminé.</span></p>
<p class="ex">Have you finished yet? / I haven't finished yet. <span class="es">¿Ya terminaste? / Todavía no terminé.</span></p>
<p class="ex">She has just left. <span class="es">Se acaba de ir.</span></p>
<p class="ex">Have you ever eaten sushi? <span class="es">¿Alguna vez comiste sushi?</span></p>
HTML,
            'vocab' => [
                ['already', 'ya'], ['yet', 'todavía / ya (preg.)'], ['just', 'recién, acabar de'], ['ever', 'alguna vez'],
                ['since', 'desde'], ['so far', 'hasta ahora'], ['been to', 'haber estado en'], ['lately', 'últimamente'],
            ],
            'exercises' => [
                ['mcq', 'I ___ to Brazil three times.', ['went', 'have been', 'was'], 1, 'Experiencia sin momento concreto: presente perfecto.'],
                ['mcq', 'I ___ to Brazil in 2019.', ['have been', 'went', 'have gone'], 1, 'Año concreto: pasado simple.'],
                ['mcq', 'She has lived in Rosario ___ 2018.', ['for', 'since', 'from'], 1, 'Punto de inicio: since.'],
                ['mcq', 'We have known each other ___ ten years.', ['since', 'for', 'during'], 1, 'Duración: for.'],
                ['fill', 'Have you finished your report ___? (todavía/ya, en pregunta)', ['yet'], 'Yet al final en preguntas y negativas.'],
                ['fill', 'He has ___ (write) three books.', ['written'], 'Participio irregular de write: written.'],
                ['mcq', '___ you ever ridden a horse?', ['Did', 'Have', 'Has'], 1, 'Ever + presente perfecto: Have you ever…?'],
                ['order', 'Ordená la oración.', 'I have just sent you the email.', 'Just va entre have y el participio.'],
                ['listen', '¿Cuánto hace que vive en Mendoza?', "I moved to Mendoza when I was twenty and I've lived here for fifteen years.", ['Veinte años', 'Quince años', 'Cinco años'], 1, 'For fifteen years.'],
                ['dictation', 'Escuchá y escribí.', "I haven't seen that film yet.", 'Haven\'t + participio + yet.'],
            ],
        ],
        [
            'slug' => 'b1-conditionals-0-1',
            'title' => 'Condicionales cero y primero',
            'summary' => 'Verdades generales y situaciones reales posibles: if, unless, as soon as.',
            'theory' => <<<'HTML'
<h2>Condicional cero: siempre es así</h2>
<p>Para verdades generales y hechos científicos: <strong>if + presente, presente</strong>.</p>
<p class="ex">If you heat ice, it melts. <span class="es">Si calentás hielo, se derrite.</span></p>

<h2>Primer condicional: algo posible en el futuro</h2>
<p><strong>if + presente simple, will + verbo</strong>.</p>
<p class="ex">If it rains tomorrow, we'll cancel the barbecue. <span class="es">Si mañana llueve, suspendemos el asado.</span></p>
<div class="warn"><strong>Error típico:</strong> nunca va will en la parte del if. <em>If it will rain</em> es incorrecto aunque hable del futuro.</div>
<p>El orden se puede invertir; sin coma cuando el if va segundo:</p>
<p class="ex">We'll cancel the barbecue if it rains.</p>

<h2>Otros conectores con la misma regla</h2>
<table class="tbl">
<thead><tr><th>Conector</th><th>Significado</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>unless</td><td>a menos que (= if not)</td><td>I won't go unless you come.</td></tr>
<tr><td>when</td><td>cuando (seguro que pasa)</td><td>I'll call you when I arrive.</td></tr>
<tr><td>as soon as</td><td>apenas, en cuanto</td><td>I'll tell her as soon as I see her.</td></tr>
<tr><td>before / after / until</td><td>antes / después / hasta</td><td>Wait here until I come back.</td></tr>
</tbody></table>
<div class="tip">En español decimos "cuando llegue" (subjuntivo). En inglés va presente simple: <em>when I arrive</em>, nunca <em>when I will arrive</em>.</div>
HTML,
            'vocab' => [
                ['unless', 'a menos que'], ['as soon as', 'en cuanto'], ['melt', 'derretirse'], ['miss (the bus)', 'perder (el colectivo)'],
                ['hurry up', 'apurarse'], ['get a discount', 'conseguir un descuento'], ['in case', 'por si'], ['until', 'hasta (que)'],
            ],
            'exercises' => [
                ['mcq', 'If you mix red and white, you ___ pink.', ['get', 'will got', 'got'], 0, 'Verdad general: condicional cero.'],
                ['mcq', 'If she ___ the train, she\'ll be late.', ['will miss', 'misses', 'missed'], 1, 'En el if va presente simple.'],
                ['fill', 'If I see Martín, I ___ give him your message.', ['will', "'ll"], 'Resultado futuro: will.'],
                ['mcq', 'I won\'t go ___ you come with me.', ['if', 'unless', 'when'], 1, 'A menos que: unless.'],
                ['mcq', 'Call me as soon as you ___ home.', ['will get', 'get', 'got'], 1, 'Después de as soon as: presente.'],
                ['mcq', 'If you don\'t hurry, you ___ the bus.', ['miss', "'ll miss", 'missed'], 1, 'Primer condicional: will miss.'],
                ['order', 'Ordená la oración.', 'We will stay at home if it rains.', 'Resultado primero, condición después (sin coma).'],
                ['order', 'Ordená la oración.', 'I will text you when I arrive.', 'When + presente simple.'],
                ['listen', '¿En qué condición van a la playa?', "If the weather is good on Saturday, we'll go to the beach.", ['Si tienen plata', 'Si hace buen tiempo', 'Si no trabajan'], 1, 'If the weather is good.'],
                ['dictation', 'Escuchá y escribí.', 'If you study every day, you will pass the exam.', 'If + presente, will + verbo.'],
            ],
        ],
        [
            'slug' => 'b1-passive-voice',
            'title' => 'La voz pasiva',
            'summary' => 'Cuando importa la acción y no quién la hace: be + participio, by.',
            'theory' => <<<'HTML'
<h2>¿Cuándo usarla?</h2>
<p>La voz pasiva pone el foco en <strong>lo que se hace</strong>, no en quién lo hace. Es muy común en noticias, procesos e informes.</p>
<p class="ex">Activa: Someone stole my bike. <span class="es">Alguien robó mi bici.</span></p>
<p class="ex">Pasiva: My bike was stolen. <span class="es">Me robaron la bici.</span></p>
<div class="tip">Muchas veces el español usa "se" o la tercera plural: <em>se habla inglés</em> → <em>English is spoken</em>; <em>me robaron</em> → <em>I was robbed</em>.</div>

<h2>Estructura: be + participio</h2>
<table class="tbl">
<thead><tr><th>Tiempo</th><th>Pasiva</th></tr></thead>
<tbody>
<tr><td>Presente simple</td><td>Cars are made in this factory.</td></tr>
<tr><td>Pasado simple</td><td>The bridge was built in 1890.</td></tr>
<tr><td>Presente perfecto</td><td>The meeting has been cancelled.</td></tr>
<tr><td>Futuro</td><td>The results will be published tomorrow.</td></tr>
<tr><td>Con modal</td><td>This form must be signed.</td></tr>
</tbody></table>
<p>Solo cambia el verbo <em>be</em>; el participio queda igual.</p>

<h2>By: quién lo hizo</h2>
<p>Solo lo agregamos si el autor es información importante.</p>
<p class="ex">Don Quixote was written by Cervantes. <span class="es">El Quijote fue escrito por Cervantes.</span></p>
<div class="warn"><strong>Error típico:</strong> olvidar el verbo be. <em>The house built in 1950</em> → <em>The house was built in 1950</em>.</div>
HTML,
            'vocab' => [
                ['build (built)', 'construir'], ['steal (stolen)', 'robar'], ['invent', 'inventar'], ['deliver', 'entregar'],
                ['translate', 'traducir'], ['grow', 'cultivar'], ['found', 'fundar'], ['damage', 'dañar'],
            ],
            'exercises' => [
                ['mcq', 'English ___ all over the world.', ['speaks', 'is spoken', 'is speaking'], 1, 'Presente pasivo: is + spoken.'],
                ['mcq', 'The Obelisk ___ in 1936.', ['built', 'was built', 'has built'], 1, 'Pasado pasivo: was built.'],
                ['fill', 'Your order ___ delivered tomorrow. (futuro)', ['will be'], 'Futuro pasivo: will be + participio.'],
                ['fill', 'The meeting has ___ cancelled.', ['been'], 'Presente perfecto pasivo: has been.'],
                ['mcq', 'This book was written ___ a famous journalist.', ['from', 'by', 'of'], 1, 'Autor de la acción: by.'],
                ['mcq', 'Coffee ___ in Colombia and Brazil.', ['is grown', 'grows by', 'is growing'], 0, 'Proceso general: presente pasivo.'],
                ['mcq', 'All forms must ___ in English.', ['complete', 'be completed', 'completed'], 1, 'Modal + be + participio.'],
                ['order', 'Pasá a voz pasiva y ordená.', 'My car was stolen last night.', 'Was + stolen.'],
                ['listen', '¿Qué le pasó a la casa?', 'The house was badly damaged by the storm last week.', ['La vendieron', 'La dañó la tormenta', 'La pintaron'], 1, 'Damaged by the storm.'],
                ['dictation', 'Escuchá y escribí.', 'The museum is visited by thousands of people every year.', 'Is visited by.'],
            ],
        ],
        [
            'slug' => 'b1-modals-obligation',
            'title' => 'Obligación, prohibición y consejo',
            'summary' => 'Must, have to, don\'t have to, mustn\'t y should: matices que cambian el sentido.',
            'theory' => <<<'HTML'
<h2>Obligación: must y have to</h2>
<p>Las dos expresan obligación, con un matiz:</p>
<p class="ex">I must call my mum. <span class="es">Tengo que llamar a mi mamá (lo siento yo).</span></p>
<p class="ex">I have to wear a uniform at work. <span class="es">Tengo que usar uniforme (es una regla externa).</span></p>
<p><strong>Have to</strong> se conjuga en todos los tiempos: <em>I had to work yesterday. She'll have to wait.</em> Must no tiene pasado.</p>

<h2>La trampa: mustn't ≠ don't have to</h2>
<table class="tbl">
<thead><tr><th>Forma</th><th>Significado</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>mustn't</td><td>está prohibido</td><td>You mustn't smoke here.</td></tr>
<tr><td>don't have to</td><td>no es necesario</td><td>You don't have to come, it's optional.</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> traducir "no tenés que" por <em>mustn't</em> cuando querés decir que no hace falta. <em>You mustn't pay</em> significa que está prohibido pagar.</div>

<h2>Consejo: should</h2>
<p class="ex">You should see a doctor. <span class="es">Deberías ir al médico.</span></p>
<p class="ex">You shouldn't eat so late. <span class="es">No deberías comer tan tarde.</span></p>
<p>Más fuerte: <strong>had better</strong> (mejor que… o habrá consecuencias): <em>You'd better leave now or you'll miss the flight.</em></p>
HTML,
            'vocab' => [
                ['must', 'deber (obligación)'], ['have to', 'tener que'], ['should', 'debería'], ['allowed to', 'permitido'],
                ['seatbelt', 'cinturón de seguridad'], ['rule', 'regla'], ['optional', 'opcional'], ["had better", 'más vale que'],
            ],
            'exercises' => [
                ['mcq', 'You ___ use your phone while driving. It\'s illegal.', ["don't have to", "mustn't", "shouldn't to"], 1, 'Prohibición: mustn\'t.'],
                ['mcq', 'Tomorrow is Sunday, so I ___ get up early.', ["mustn't", "don't have to", "haven't"], 1, 'No es necesario: don\'t have to.'],
                ['mcq', 'Yesterday I ___ work until 10 pm.', ['must', 'had to', 'have to'], 1, 'Must no tiene pasado: had to.'],
                ['mcq', 'You look tired. You ___ go to bed early.', ['should', 'must to', 'have'], 0, 'Consejo: should.'],
                ['fill', 'Does she ___ to wear a uniform at school?', ['have'], 'Pregunta con have to: Does she have to…?'],
                ['mcq', 'You\'d better ___ an umbrella.', ['take', 'to take', 'taking'], 0, 'Had better + verbo base.'],
                ['order', 'Ordená la oración.', 'You should drink more water.', 'Should + verbo base.'],
                ['listen', '¿Qué es obligatorio en el trabajo nuevo?', "In my new job I have to wear a suit, but I don't have to work on Fridays.", ['Trabajar los viernes', 'Usar traje', 'Usar uniforme deportivo'], 1, 'Have to wear a suit.'],
                ['dictation', 'Escuchá y escribí.', "You mustn't touch the paintings in the museum.", 'Mustn\'t = prohibición.'],
            ],
        ],
        // B1: ampliación original, unidades 5 a 12
        ['slug' => 'b1-past-perfect', 'exercises' => []],
        ['slug' => 'b1-second-conditional', 'exercises' => []],
        ['slug' => 'b1-reported-speech', 'exercises' => []],
        ['slug' => 'b1-present-perfect-continuous', 'exercises' => []],
        ['slug' => 'b1-relative-clauses', 'exercises' => []],
        ['slug' => 'b1-gerunds-infinitives', 'exercises' => []],
        ['slug' => 'b1-comparatives-advanced', 'exercises' => []],
        ['slug' => 'b1-causative-have', 'exercises' => []],
        ['slug' => 'b1-third-conditional', 'exercises' => []],
        ['slug' => 'b1-wish-present', 'exercises' => []],
        ['slug' => 'b1-modal-deduction', 'exercises' => []],
        ['slug' => 'b1-future-perfect', 'exercises' => []],
        ['slug' => 'b1-narrative-tenses', 'exercises' => []],
        ['slug' => 'b1-reported-requests', 'exercises' => []],
        ['slug' => 'b1-formal-email', 'exercises' => []],
        ['slug' => 'b1-meetings', 'exercises' => []],
        ['slug' => 'b1-phrasal-verbs-work', 'exercises' => []],
        ['slug' => 'b1-collocations-work', 'exercises' => []],
        ['slug' => 'b1-for-since-review', 'exercises' => []],
        ['slug' => 'b1-conditionals-comparison', 'exercises' => []],
        ['slug' => 'b1-articles-specific', 'exercises' => []],
        ['slug' => 'b1-quantifiers-advanced', 'exercises' => []],
        ['slug' => 'b1-polite-indirect-questions', 'exercises' => []],
        ['slug' => 'b1-opinion-reasons', 'exercises' => []],
        ['slug' => 'b1-result-linkers', 'exercises' => []],
        ['slug' => 'b1-customer-requests', 'exercises' => []],
        ['slug' => 'b1-reading-workplace-notice', 'exercises' => []],
        ['slug' => 'b1-reading-travel-review', 'exercises' => []],
        ['slug' => 'b1-listening-train-changes', 'exercises' => []],
        ['slug' => 'b1-listening-phone-message', 'exercises' => []],
        ['slug' => 'b1-writing-complaint-email', 'exercises' => []],
        ['slug' => 'b1-writing-job-application', 'exercises' => []],
        ['slug' => 'b1-speaking-expressing-disagreement', 'exercises' => []],
        ['slug' => 'b1-speaking-negotiating-plans', 'exercises' => []],
        ['slug' => 'b1-reading-instructions-safety', 'exercises' => []],
        ['slug' => 'b1-integrated-scenarios-two', 'exercises' => []],
    ],
];
