<?php
return [
    'code' => 'b2',
    'name' => 'Intermedio alto',
    'description' => 'Hipótesis, discurso indirecto y estructuras complejas para conversar y escribir con precisión.',
    'position' => 4,
    'units' => [
        [
            'slug' => 'b2-conditionals-2-3',
            'title' => 'Condicionales segundo y tercero',
            'summary' => 'Situaciones imaginarias en presente y arrepentimientos sobre el pasado.',
            'theory' => <<<'HTML'
<h2>Segundo condicional: imaginario o improbable</h2>
<p><strong>if + pasado simple, would + verbo</strong>. Habla del presente o del futuro, pero de algo hipotético.</p>
<p class="ex">If I had more time, I would learn to play the guitar. <span class="es">Si tuviera más tiempo, aprendería a tocar la guitarra.</span></p>
<p>Con <em>to be</em>, en registro cuidado se usa <strong>were</strong> para todas las personas:</p>
<p class="ex">If I were you, I'd accept the offer. <span class="es">Yo que vos, aceptaría la oferta.</span></p>
<div class="tip">El "pasado" del if no indica tiempo pasado: marca distancia de la realidad, igual que el subjuntivo "tuviera" en español.</div>

<h2>Tercer condicional: el pasado que no fue</h2>
<p><strong>if + past perfect, would have + participio</strong>. Imagina un pasado distinto: arrepentimientos y críticas.</p>
<p class="ex">If I had left earlier, I wouldn't have missed the flight. <span class="es">Si hubiera salido antes, no habría perdido el vuelo.</span></p>

<h2>Comparación</h2>
<table class="tbl">
<thead><tr><th>Tipo</th><th>Estructura</th><th>Realidad</th></tr></thead>
<tbody>
<tr><td>Primero</td><td>If it rains, I'll stay.</td><td>Es posible.</td></tr>
<tr><td>Segundo</td><td>If it rained, I'd stay.</td><td>Es poco probable.</td></tr>
<tr><td>Tercero</td><td>If it had rained, I'd have stayed.</td><td>No llovió.</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> <em>If I would have known</em>. Would nunca va en la parte del if: <em>If I had known</em>.</div>
<p>También se usan <em>could</em> y <em>might</em> en lugar de would: <em>If you trained more, you could win.</em></p>
HTML,
            'vocab' => [
                ['regret', 'arrepentirse'], ['offer', 'oferta, propuesta'], ['lottery', 'lotería'], ['afford', 'poder pagar'],
                ['if I were you', 'yo que vos'], ['take the chance', 'aprovechar la oportunidad'], ['in time', 'a tiempo'], ['otherwise', 'de lo contrario'],
            ],
            'exercises' => [
                ['mcq', 'If I ___ the lottery, I would travel around the world.', ['win', 'won', 'had won'], 1, 'Segundo condicional: if + pasado.'],
                ['mcq', 'If I were you, I ___ that job.', ['will take', "'d take", 'took'], 1, 'Consejo hipotético: would.'],
                ['mcq', 'If she had studied harder, she ___ the exam.', ['would pass', 'would have passed', 'passed'], 1, 'Tercer condicional: would have + participio.'],
                ['fill', 'If we ___ (know) about the strike, we would have taken a taxi.', ['had known'], 'Past perfect en el if.'],
                ['mcq', 'I can\'t afford it. If it ___ cheaper, I\'d buy it.', ['is', 'was', 'had been'], 1, 'Situación presente imaginaria: pasado (was/were).'],
                ['mcq', 'We ___ the match if our goalkeeper hadn\'t been injured.', ['would win', 'would have won', 'won'], 1, 'Pasado irreal: would have won.'],
                ['order', 'Ordená la oración.', 'I would have called you if I had had your number.', 'Had had es correcto: past perfect de have.'],
                ['listen', '¿Qué habría pasado?', "If I hadn't missed the train, I would have arrived on time for the interview.", ['Llegó a tiempo', 'Perdió el tren y llegó tarde', 'No fue a la entrevista'], 1, 'Hadn\'t missed implica que sí lo perdió.'],
                ['dictation', 'Escuchá y escribí.', 'If I had more money, I would buy a house by the sea.', 'Segundo condicional.'],
            ],
        ],
        [
            'slug' => 'b2-reported-speech',
            'title' => 'Discurso indirecto',
            'summary' => 'Contar lo que otro dijo: cambios de tiempo verbal, say vs tell y preguntas indirectas.',
            'theory' => <<<'HTML'
<h2>Un paso atrás en el tiempo</h2>
<p>Cuando el verbo introductor está en pasado (<em>said, told</em>), el tiempo verbal suele retroceder un paso.</p>
<table class="tbl">
<thead><tr><th>Directo</th><th>Indirecto</th></tr></thead>
<tbody>
<tr><td>"I work in a bank."</td><td>She said she worked in a bank.</td></tr>
<tr><td>"I'm leaving."</td><td>He said he was leaving.</td></tr>
<tr><td>"I've finished."</td><td>She said she had finished.</td></tr>
<tr><td>"I saw it."</td><td>He said he had seen it.</td></tr>
<tr><td>"I'll call."</td><td>She said she would call.</td></tr>
<tr><td>"I can help."</td><td>He said he could help.</td></tr>
</tbody></table>
<p>También cambian las referencias: <em>today → that day, tomorrow → the next day, here → there</em>.</p>
<div class="tip">Si lo dicho sigue siendo verdad, no es obligatorio retroceder: <em>She said she lives in Córdoba</em> (todavía vive ahí).</div>

<h2>Say o tell</h2>
<p><strong>tell</strong> necesita a quién: <em>She told me…</em>. <strong>say</strong> no lo lleva directo: <em>She said (to me)…</em></p>
<div class="warn"><strong>Error típico:</strong> <em>She said me</em> o <em>She told that</em>. Correcto: <em>She told me that… / She said that…</em></div>

<h2>Preguntas indirectas</h2>
<p>El orden pasa a ser de <strong>afirmación</strong> y desaparece el auxiliar do/did.</p>
<p class="ex">"Where do you live?" → He asked me where I lived.</p>
<p class="ex">"Are you coming?" → She asked if I was coming.</p>
<p>Lo mismo con preguntas corteses: <em>Could you tell me where the station is?</em> (no <em>where is the station</em>).</p>
HTML,
            'vocab' => [
                ['ask', 'preguntar, pedir'], ['tell', 'decir (a alguien)'], ['explain', 'explicar'], ['admit', 'admitir'],
                ['deny', 'negar'], ['warn', 'advertir'], ['the next day', 'al día siguiente'], ['whether', 'si (en preguntas)'],
            ],
            'exercises' => [
                ['mcq', '"I am tired." → She said she ___ tired.', ['is', 'was', 'has been'], 1, 'Presente → pasado.'],
                ['mcq', '"I will help you." → He said he ___ help me.', ['will', 'would', 'can'], 1, 'Will → would.'],
                ['fill', '"I have lost my keys." → She said she ___ lost her keys.', ['had'], 'Presente perfecto → past perfect.'],
                ['mcq', 'He ___ me that he was leaving.', ['said', 'told', 'asked'], 1, 'Tell + persona.'],
                ['mcq', '"Where do you work?" → She asked me where ___.', ['do I work', 'I worked', 'did I work'], 1, 'Orden de afirmación, sin auxiliar.'],
                ['mcq', '"Are you married?" → He asked ___ I was married.', ['that', 'if', 'what'], 1, 'Preguntas de sí/no: if / whether.'],
                ['mcq', 'Could you tell me where ___?', ['is the bank', 'the bank is', 'does the bank'], 1, 'Pregunta indirecta: orden de afirmación.'],
                ['order', 'Ordená la oración.', 'She told me she would call the next day.', 'Tell + persona + would.'],
                ['listen', '¿Qué dijo el gerente?', 'The manager told us that the office would be closed the next day.', ['Que la oficina abría temprano', 'Que la oficina estaría cerrada al día siguiente', 'Que había reunión'], 1, 'Would be closed the next day.'],
                ['dictation', 'Escuchá y escribí.', 'He asked me if I had finished the report.', 'Asked + if + past perfect.'],
            ],
        ],
        [
            'slug' => 'b2-relative-clauses',
            'title' => 'Oraciones de relativo',
            'summary' => 'Who, which, that, whose, where: cuándo se usan, cuándo se omiten y cuándo llevan comas.',
            'theory' => <<<'HTML'
<h2>Pronombres relativos</h2>
<table class="tbl">
<thead><tr><th>Pronombre</th><th>Se refiere a</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>who</td><td>personas</td><td>the woman who lives next door</td></tr>
<tr><td>which</td><td>cosas</td><td>the car which I bought</td></tr>
<tr><td>that</td><td>personas o cosas (solo especificativas)</td><td>the book that you lent me</td></tr>
<tr><td>whose</td><td>posesión (cuyo)</td><td>the man whose dog bit me</td></tr>
<tr><td>where</td><td>lugares</td><td>the town where I grew up</td></tr>
</tbody></table>

<h2>Especificativas: sin comas</h2>
<p>Definen <strong>cuál</strong>; sin ellas la oración pierde sentido.</p>
<p class="ex">The people who live upstairs are very noisy. <span class="es">Los que viven arriba son muy ruidosos.</span></p>
<p>Si el relativo es el <strong>objeto</strong>, se puede omitir:</p>
<p class="ex">The film (that) we saw was great. <span class="es">La película que vimos estuvo buenísima.</span></p>

<h2>Explicativas: con comas</h2>
<p>Agregan información extra. No se puede usar <em>that</em> ni omitir el pronombre.</p>
<p class="ex">My sister, who lives in Spain, is a nurse. <span class="es">Mi hermana, que vive en España, es enfermera.</span></p>
<div class="warn"><strong>Error típico:</strong> <em>My father, that is a lawyer, …</em>. Con comas va who o which, nunca that.</div>
<div class="tip">which puede referirse a toda una idea anterior: <em>He passed the exam, which surprised everyone.</em></div>
HTML,
            'vocab' => [
                ['whose', 'cuyo/a'], ['grow up', 'criarse'], ['upstairs', 'arriba'], ['lend (lent)', 'prestar'],
                ['colleague', 'colega'], ['landlord', 'propietario, dueño'], ['hometown', 'ciudad natal'], ['nearby', 'cercano'],
            ],
            'exercises' => [
                ['mcq', 'The woman ___ called you is my boss.', ['which', 'who', 'whose'], 1, 'Persona: who.'],
                ['mcq', 'This is the house ___ I was born.', ['which', 'where', 'who'], 1, 'Lugar: where.'],
                ['mcq', 'That\'s the man ___ car was stolen.', ['who', 'whose', 'which'], 1, 'Posesión: whose.'],
                ['mcq', 'Buenos Aires, ___ is the capital, has 15 million people.', ['that', 'which', 'where'], 1, 'Explicativa (con comas) sobre una cosa: which.'],
                ['mcq', '¿En cuál se puede omitir el relativo?', ['The man who called me', 'The book that I read', 'My mother, who is a doctor'], 1, 'Se omite cuando es objeto en especificativas.'],
                ['fill', 'He didn\'t call me, ___ made me angry.', ['which'], 'Which retoma toda la idea anterior.'],
                ['order', 'Ordená la oración.', 'The colleague who helped me is from Uruguay.', 'Who identifica al colega.'],
                ['listen', '¿Dónde se crió?', 'Rafaela, which is where I grew up, is a small city in Santa Fe.', ['Rosario', 'Rafaela', 'Santa Rosa'], 1, 'Where I grew up = donde me crié.'],
                ['dictation', 'Escuchá y escribí.', 'The restaurant where we had dinner was very expensive.', 'Where para lugares.'],
            ],
        ],
        [
            'slug' => 'b2-gerund-infinitive',
            'title' => 'Gerundio, infinitivo y used to',
            'summary' => 'Enjoy doing o want to do, los verbos que cambian de significado y used to vs be used to.',
            'theory' => <<<'HTML'
<h2>Verbos seguidos de -ing</h2>
<p>enjoy, avoid, finish, mind, suggest, consider, keep, can't stand, look forward to</p>
<p class="ex">I enjoy cooking for my friends. <span class="es">Disfruto cocinar para mis amigos.</span></p>
<p>También después de <strong>preposiciones</strong>: <em>I'm interested in learning. Thanks for coming.</em></p>

<h2>Verbos seguidos de to + infinitivo</h2>
<p>want, decide, hope, plan, agree, refuse, manage, afford, offer, promise</p>
<p class="ex">We decided to move to Neuquén. <span class="es">Decidimos mudarnos a Neuquén.</span></p>

<h2>Cambian de significado</h2>
<table class="tbl">
<thead><tr><th>Verbo</th><th>+ -ing</th><th>+ to</th></tr></thead>
<tbody>
<tr><td>stop</td><td>dejar de: I stopped smoking.</td><td>parar para: I stopped to smoke.</td></tr>
<tr><td>remember</td><td>recordar algo hecho: I remember locking the door.</td><td>acordarse de hacer: Remember to lock the door.</td></tr>
<tr><td>try</td><td>probar: Try restarting it.</td><td>intentar: I tried to open it.</td></tr>
</tbody></table>

<h2>Used to, be used to, get used to</h2>
<p class="ex">I used to live in Lima. <span class="es">Antes vivía en Lima (ya no).</span></p>
<p class="ex">I'm used to getting up early. <span class="es">Estoy acostumbrado a madrugar.</span></p>
<p class="ex">I'm getting used to the cold. <span class="es">Me estoy acostumbrando al frío.</span></p>
<div class="warn"><strong>Error típico:</strong> <em>I'm used to get up</em>. En <em>be/get used to</em> el <em>to</em> es preposición: va -ing.</div>
HTML,
            'vocab' => [
                ['avoid', 'evitar'], ['look forward to', 'esperar con ganas'], ["can't stand", 'no soportar'], ['afford', 'poder pagar'],
                ['refuse', 'negarse'], ['manage to', 'lograr'], ['get used to', 'acostumbrarse a'], ['suggest', 'sugerir'],
            ],
            'exercises' => [
                ['mcq', 'I enjoy ___ in the mountains.', ['to hike', 'hiking', 'hike'], 1, 'Enjoy + -ing.'],
                ['mcq', 'We can\'t afford ___ a new car.', ['buying', 'to buy', 'buy'], 1, 'Afford + to.'],
                ['mcq', 'I look forward to ___ from you.', ['hear', 'hearing', 'to hear'], 1, 'Look forward to: to es preposición → -ing.'],
                ['mcq', 'He stopped ___ ten years ago. He doesn\'t smoke now.', ['to smoke', 'smoking', 'smoke'], 1, 'Stop + -ing = dejar de.'],
                ['mcq', 'Please remember ___ the lights when you leave.', ['turning off', 'to turn off', 'turn off'], 1, 'Acordarse de hacer algo: remember + to.'],
                ['mcq', 'I ___ play tennis when I was a teenager.', ['am used to', 'used to', 'get used to'], 1, 'Hábito pasado: used to + verbo.'],
                ['fill', 'She isn\'t used to ___ (drive) on the left.', ['driving'], 'Be used to + -ing.'],
                ['order', 'Ordená la oración.', 'He managed to finish the project on time.', 'Manage + to.'],
                ['listen', '¿Qué le cuesta al hablante?', "I moved to London a year ago, but I still can't get used to the weather.", ['La comida', 'El clima', 'El transporte'], 1, 'Get used to the weather.'],
                ['dictation', 'Escuchá y escribí.', 'I used to work in a bank, but now I teach English.', 'Used to = antes, ya no.'],
            ],
        ],
    ],
];
