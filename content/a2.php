<?php
return [
    'code' => 'a2',
    'name' => 'Elemental',
    'description' => 'Contar lo que pasó, comparar, hacer planes y describir situaciones en curso.',
    'position' => 2,
    'units' => [
        [
            'slug' => 'a2-past-simple',
            'title' => 'Pasado simple: regulares e irregulares',
            'summary' => 'Contar experiencias terminadas: -ed, verbos irregulares y el auxiliar did.',
            'theory' => <<<'HTML'
<h2>Acciones terminadas</h2>
<p>El pasado simple cuenta acciones <strong>terminadas en un momento concreto</strong> del pasado: yesterday, last week, in 2019, two days ago.</p>
<p class="ex">I visited my grandmother last Sunday. <span class="es">Visité a mi abuela el domingo pasado.</span></p>

<h2>Verbos regulares: -ed</h2>
<table class="tbl">
<thead><tr><th>Caso</th><th>Regla</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>general</td><td>+ ed</td><td>work → worked</td></tr>
<tr><td>termina en -e</td><td>+ d</td><td>live → lived</td></tr>
<tr><td>consonante + y</td><td>y → ied</td><td>study → studied</td></tr>
<tr><td>CVC corto</td><td>dobla consonante</td><td>stop → stopped</td></tr>
</tbody></table>
<div class="tip">La -ed tiene tres pronunciaciones: /t/ en <em>worked</em>, /d/ en <em>played</em> y /ɪd/ solo después de t o d: <em>wanted, needed</em>. Decir "work-ed" con dos sílabas es un error muy común.</div>

<h2>Irregulares más usados</h2>
<p>No siguen regla: hay que memorizarlos. Estos son los que más vas a necesitar:</p>
<p class="ex">go → went, have → had, do → did, make → made, get → got, see → saw, come → came, take → took, buy → bought, eat → ate</p>

<h2>Negativas y preguntas con did</h2>
<p>Igual que con does en presente: cuando aparece <strong>did</strong>, el verbo vuelve a la forma base.</p>
<p class="ex">I didn't go to work yesterday. <span class="es">Ayer no fui a trabajar.</span></p>
<p class="ex">Did you see the match? <span class="es">¿Viste el partido?</span></p>
<div class="warn"><strong>Error típico:</strong> <em>Did you went?</em> o <em>I didn't saw</em>. El pasado ya está en did: <em>Did you go? I didn't see.</em></div>
<p>El verbo to be no usa did: <em>I was tired. Were you at home?</em></p>
HTML,
            'vocab' => [
                ['yesterday', 'ayer'], ['last week', 'la semana pasada'], ['ago', 'hace (tiempo)'], ['went', 'fue / fui (go)'],
                ['bought', 'compró (buy)'], ['brought', 'trajo (bring)'], ['thought', 'pensó (think)'], ['trip', 'viaje'],
            ],
            'exercises' => [
                ['fill', 'We ___ (play) football last Saturday.', ['played'], 'Regular: play + ed.'],
                ['fill', 'She ___ (study) all night for the exam.', ['studied'], 'Consonante + y → ied.'],
                ['fill', 'They ___ (go) to Bariloche in July.', ['went'], 'Go es irregular: went.'],
                ['mcq', 'I ___ the film. It was boring.', ["didn't like", "didn't liked", "don't liked"], 0, 'Con didn\'t, el verbo va en forma base.'],
                ['mcq', '___ you call your mother yesterday?', ['Do', 'Did', 'Were'], 1, 'Pregunta en pasado: Did.'],
                ['mcq', 'Where ___ you last night?', ['did', 'was', 'were'], 2, 'To be en pasado: were con you.'],
                ['mcq', 'I bought this phone two years ___.', ['before', 'ago', 'last'], 1, 'Ago va después del período de tiempo.'],
                ['order', 'Ordená la pregunta.', 'What did you have for dinner?', 'Wh- + did + sujeto + verbo base.'],
                ['listen', '¿Qué hizo el fin de semana?', 'At the weekend I stayed at home and cooked for my family.', ['Salió de viaje', 'Se quedó en casa y cocinó', 'Fue a trabajar'], 1, 'Stayed at home and cooked.'],
                ['dictation', 'Escuchá y escribí.', 'I didn\'t see her at the party.', 'Didn\'t + see en forma base.'],
            ],
        ],
        [
            'slug' => 'a2-comparatives',
            'title' => 'Comparativos y superlativos',
            'summary' => 'Más grande, el más grande, tan… como. Reglas por cantidad de sílabas.',
            'theory' => <<<'HTML'
<h2>La regla depende de las sílabas</h2>
<table class="tbl">
<thead><tr><th>Adjetivo</th><th>Comparativo</th><th>Superlativo</th></tr></thead>
<tbody>
<tr><td>1 sílaba: cheap</td><td>cheaper than</td><td>the cheapest</td></tr>
<tr><td>CVC: big</td><td>bigger</td><td>the biggest</td></tr>
<tr><td>termina en -y: easy</td><td>easier</td><td>the easiest</td></tr>
<tr><td>2+ sílabas: expensive</td><td>more expensive</td><td>the most expensive</td></tr>
<tr><td>irregular: good</td><td>better</td><td>the best</td></tr>
<tr><td>irregular: bad</td><td>worse</td><td>the worst</td></tr>
<tr><td>irregular: far</td><td>further / farther</td><td>the furthest</td></tr>
</tbody></table>
<p class="ex">Buenos Aires is bigger than Montevideo. <span class="es">Buenos Aires es más grande que Montevideo.</span></p>
<p class="ex">This is the most beautiful beach in Brazil. <span class="es">Esta es la playa más linda de Brasil.</span></p>
<div class="warn"><strong>Error típico:</strong> nunca se combinan las dos formas. No <em>more bigger</em> ni <em>the most cheapest</em>.</div>

<h2>Igualdad: as… as</h2>
<p class="ex">My car is as fast as yours. <span class="es">Mi auto es tan rápido como el tuyo.</span></p>
<p class="ex">It isn't as cold as yesterday. <span class="es">No hace tanto frío como ayer.</span></p>

<h2>Than, no that</h2>
<p>Para comparar se usa <strong>than</strong>. <em>That</em> significa "eso / que" y es otra palabra. Confundirlas es muy común por cómo suenan.</p>
HTML,
            'vocab' => [
                ['cheap', 'barato'], ['expensive', 'caro'], ['crowded', 'lleno de gente'], ['quiet', 'tranquilo'],
                ['safe', 'seguro'], ['better', 'mejor'], ['worse', 'peor'], ['than', 'que (comparación)'],
            ],
            'exercises' => [
                ['fill', 'Salta is ___ (hot) than Ushuaia.', ['hotter'], 'CVC: dobla la t → hotter.'],
                ['fill', 'This exercise is ___ (easy) than the last one.', ['easier'], 'Y → ier.'],
                ['mcq', 'An apartment in Palermo is ___ than one in the suburbs.', ['expensiver', 'more expensive', 'most expensive'], 1, 'Tres sílabas: more expensive.'],
                ['mcq', 'This is the ___ pizza I have ever eaten.', ['better', 'goodest', 'best'], 2, 'Superlativo de good: the best.'],
                ['mcq', 'Today the traffic is ___ than yesterday.', ['worse', 'badder', 'worst'], 0, 'Comparativo de bad: worse.'],
                ['mcq', 'My brother is taller ___ me.', ['that', 'than', 'as'], 1, 'Comparación: than.'],
                ['mcq', 'Spanish is not ___ difficult as Chinese.', ['so', 'as', 'than'], 1, 'Igualdad (negativa): not as… as.'],
                ['order', 'Ordená la oración.', 'It is the most important day of the year.', 'Superlativo largo: the most + adjetivo.'],
                ['listen', '¿Qué opina del barrio nuevo?', 'The new neighbourhood is quieter but it is further from my office.', ['Más ruidoso y más cerca', 'Más tranquilo pero más lejos', 'Igual de tranquilo'], 1, 'Quieter = más tranquilo, further = más lejos.'],
                ['dictation', 'Escuchá y escribí.', 'My new job is better than my old one.', 'Better than.'],
            ],
        ],
        [
            'slug' => 'a2-future-going-to-will',
            'title' => 'El futuro: going to y will',
            'summary' => 'Planes, decisiones espontáneas, predicciones y promesas.',
            'theory' => <<<'HTML'
<h2>Going to: planes e intenciones</h2>
<p>Usamos <strong>be going to + verbo</strong> para algo que <strong>ya decidimos antes</strong> de hablar.</p>
<p class="ex">I'm going to study English this year. <span class="es">Este año voy a estudiar inglés.</span></p>
<p>También para predicciones con <strong>evidencia presente</strong>:</p>
<p class="ex">Look at those clouds! It's going to rain. <span class="es">¡Mirá esas nubes! Va a llover.</span></p>

<h2>Will: decisiones en el momento, promesas y opiniones</h2>
<p class="ex">The phone is ringing. — I'll answer it. <span class="es">Suena el teléfono. — Yo atiendo.</span></p>
<p class="ex">I'll call you tomorrow, I promise. <span class="es">Te llamo mañana, te lo prometo.</span></p>
<p class="ex">I think Argentina will win. <span class="es">Creo que Argentina va a ganar.</span></p>
<p>Negativa: <strong>won't</strong> (will not).</p>

<h2>La diferencia en un ejemplo</h2>
<table class="tbl">
<thead><tr><th>Situación</th><th>Forma</th></tr></thead>
<tbody>
<tr><td>Ya compraste los pasajes</td><td>We're going to travel to Salta.</td></tr>
<tr><td>Lo decidís mientras hablás</td><td>OK, we'll travel to Salta!</td></tr>
</tbody></table>
<div class="tip">Para citas y horarios ya arreglados también se usa el presente continuo: <em>I'm meeting Sofía at six.</em></div>
<div class="warn"><strong>Error típico:</strong> <em>I will to go</em>. Después de will va el verbo sin to: <em>I will go</em>.</div>
HTML,
            'vocab' => [
                ['plan', 'plan, planear'], ['promise', 'prometer'], ['probably', 'probablemente'], ['tonight', 'esta noche'],
                ['next month', 'el mes que viene'], ['book (a table)', 'reservar'], ["won't", 'no (futuro)'], ['soon', 'pronto'],
            ],
            'exercises' => [
                ['mcq', 'I\'ve bought the paint. I ___ the kitchen this weekend.', ["'ll paint", "'m going to paint", 'paint'], 1, 'Ya hay un plan (compró la pintura): going to.'],
                ['mcq', '"It\'s cold in here." — "I ___ close the window."', ["'m going to", "'ll", 'am'], 1, 'Decisión espontánea: will.'],
                ['mcq', 'Watch out! You ___ fall!', ['will', 'are going to', 'go to'], 1, 'Predicción con evidencia visible: going to.'],
                ['mcq', 'I promise I ___ tell anyone.', ["won't", "don't", "am not going"], 0, 'Promesa: will / won\'t.'],
                ['fill', 'She ___ going to start a new job in March.', ['is', "'s"], 'Be going to: she is going to.'],
                ['mcq', 'I think it ___ be a great year.', ['is going', 'will', 'goes to'], 1, 'Opinión sobre el futuro con I think: will.'],
                ['order', 'Ordená la oración.', 'We are going to visit our cousins next month.', 'Sujeto + are going to + verbo.'],
                ['order', 'Ordená la pregunta.', 'What are you going to do tonight?', 'Wh- + are + sujeto + going to.'],
                ['listen', '¿Qué va a hacer Lucas?', "Lucas is going to sell his car because he's moving to Spain.", ['Comprar un auto', 'Vender su auto', 'Viajar en auto'], 1, 'Sell = vender.'],
                ['dictation', 'Escuchá y escribí.', "Don't worry, I'll help you with your homework.", 'I\'ll = I will.'],
            ],
        ],
        [
            'slug' => 'a2-past-continuous',
            'title' => 'Pasado continuo y pasado simple',
            'summary' => 'Lo que estaba pasando cuando otra cosa ocurrió: was/were + -ing, when y while.',
            'theory' => <<<'HTML'
<h2>Una acción en desarrollo en el pasado</h2>
<p>El pasado continuo describe algo que <strong>estaba en curso</strong> en un momento del pasado. Se forma con <strong>was/were + verbo-ing</strong>.</p>
<p class="ex">At 9 pm I was having dinner. <span class="es">A las 9 estaba cenando.</span></p>

<h2>La combinación clave: interrupción</h2>
<p>Con el pasado simple marcamos la acción corta que <strong>interrumpe</strong> a la larga.</p>
<p class="ex">I was driving to work when the car broke down. <span class="es">Iba manejando al trabajo cuando se rompió el auto.</span></p>
<p class="ex">While she was cooking, the phone rang. <span class="es">Mientras cocinaba, sonó el teléfono.</span></p>
<table class="tbl">
<thead><tr><th>Conector</th><th>Suele ir con</th></tr></thead>
<tbody>
<tr><td>when</td><td>la acción corta (pasado simple)</td></tr>
<tr><td>while</td><td>la acción larga (pasado continuo)</td></tr>
</tbody></table>

<h2>Dos acciones simultáneas</h2>
<p class="ex">While I was working, my kids were playing. <span class="es">Mientras yo trabajaba, mis hijos jugaban.</span></p>

<h2>Verbos que no van en -ing</h2>
<p>Los verbos de estado (know, like, want, believe, own) normalmente no se usan en forma continua.</p>
<div class="warn"><strong>Error típico:</strong> <em>I was knowing the answer</em>. Lo correcto es <em>I knew the answer</em>.</div>
HTML,
            'vocab' => [
                ['while', 'mientras'], ['suddenly', 'de repente'], ['break down', 'romperse (máquina)'], ['ring (rang)', 'sonar'],
                ['fall asleep', 'quedarse dormido'], ['happen', 'pasar, ocurrir'], ['accident', 'accidente'], ['at that moment', 'en ese momento'],
            ],
            'exercises' => [
                ['mcq', 'What ___ you doing at 10 last night?', ['was', 'were', 'did'], 1, 'You + were.'],
                ['fill', 'I ___ (sleep) when you called.', ['was sleeping'], 'Acción larga interrumpida: was sleeping.'],
                ['mcq', 'She was walking home when it ___ to rain.', ['was starting', 'started', 'starts'], 1, 'La acción corta va en pasado simple.'],
                ['mcq', '___ I was reading, the lights went out.', ['When', 'While', 'During'], 1, 'While + acción larga.'],
                ['mcq', 'I ___ him for ten years before we got married.', ['was knowing', 'knew', 'were knowing'], 1, 'Know es verbo de estado: no va en -ing.'],
                ['fill', 'They ___ (watch) TV while I was studying.', ['were watching'], 'Dos acciones simultáneas en curso.'],
                ['order', 'Ordená la oración.', 'He was cooking when the guests arrived.', 'Larga en continuo, corta en simple.'],
                ['listen', '¿Qué estaba haciendo cuando pasó el accidente?', 'I was waiting for the bus when I saw the accident.', ['Manejando', 'Esperando el colectivo', 'Caminando al trabajo'], 1, 'Waiting for the bus.'],
                ['dictation', 'Escuchá y escribí.', 'While we were having lunch, it started to rain.', 'While + pasado continuo.'],
            ],
        ],
        // Ampliación original A2: unidades 5–9
        ['slug' => 'a2-present-perfect-experiences', 'exercises' => []],
        ['slug' => 'a2-obligations-advice', 'exercises' => []],
        ['slug' => 'a2-first-conditional-plans', 'exercises' => []],
        ['slug' => 'a2-relative-clauses-people-places', 'exercises' => []],
        ['slug' => 'a2-workplace-problem-solving', 'exercises' => []],
        // A2: unidades 10 a 55 originales
        ['slug' => 'a2-present-perfect-for-since', 'exercises' => []],
        ['slug' => 'a2-present-perfect-already-yet', 'exercises' => []],
        ['slug' => 'a2-present-perfect-past-simple', 'exercises' => []],
        ['slug' => 'a2-used-to-habits', 'exercises' => []],
        ['slug' => 'a2-past-time-sequencing', 'exercises' => []],
        ['slug' => 'a2-past-continuous-interruptions', 'exercises' => []],
        ['slug' => 'a2-future-arrangements', 'exercises' => []],
        ['slug' => 'a2-predictions-evidence', 'exercises' => []],
        ['slug' => 'a2-offers-promises-will', 'exercises' => []],
        ['slug' => 'a2-permission-requests', 'exercises' => []],
        ['slug' => 'a2-possibility-might-may', 'exercises' => []],
        ['slug' => 'a2-ability-past-could', 'exercises' => []],
        ['slug' => 'a2-must-vs-have-to', 'exercises' => []],
        ['slug' => 'a2-shouldnt-advice', 'exercises' => []],
        ['slug' => 'a2-zero-conditional', 'exercises' => []],
        ['slug' => 'a2-first-conditional-unless', 'exercises' => []],
        ['slug' => 'a2-second-conditional-intro', 'exercises' => []],
        ['slug' => 'a2-verbs-ing-or-to', 'exercises' => []],
        ['slug' => 'a2-infinitive-purpose', 'exercises' => []],
        ['slug' => 'a2-make-do-have', 'exercises' => []],
        ['slug' => 'a2-passive-present-intro', 'exercises' => []],
        ['slug' => 'a2-passive-past-intro', 'exercises' => []],
        ['slug' => 'a2-reported-speech-said', 'exercises' => []],
        ['slug' => 'a2-reported-questions-intro', 'exercises' => []],
        ['slug' => 'a2-defining-relatives', 'exercises' => []],
        ['slug' => 'a2-possessive-pronouns', 'exercises' => []],
        ['slug' => 'a2-quantifiers-few-little', 'exercises' => []],
        ['slug' => 'a2-too-enough', 'exercises' => []],
        ['slug' => 'a2-comparatives-as-as', 'exercises' => []],
        ['slug' => 'a2-comparatives-changes', 'exercises' => []],
        ['slug' => 'a2-adverbs-manner', 'exercises' => []],
        ['slug' => 'a2-adjective-order', 'exercises' => []],
        ['slug' => 'a2-articles-general-specific', 'exercises' => []],
        ['slug' => 'a2-countable-uncountable-review', 'exercises' => []],
        ['slug' => 'a2-prepositions-movement', 'exercises' => []],
        ['slug' => 'a2-phrasal-verbs-daily', 'exercises' => []],
        ['slug' => 'a2-phrasal-verbs-travel', 'exercises' => []],
        ['slug' => 'a2-linkers-although-however', 'exercises' => []],
        ['slug' => 'a2-linkers-result-purpose', 'exercises' => []],
        ['slug' => 'a2-indirect-questions', 'exercises' => []],
        ['slug' => 'a2-tags-short-responses', 'exercises' => []],
        ['slug' => 'a2-writing-personal-email', 'exercises' => []],
        ['slug' => 'a2-writing-formal-request', 'exercises' => []],
        ['slug' => 'a2-listening-announcements', 'exercises' => []],
        ['slug' => 'a2-reading-short-articles', 'exercises' => []],
        ['slug' => 'a2-a2-final-review', 'exercises' => []],
    ],
];
