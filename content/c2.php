<?php
return [
    'code' => 'c2',
    'name' => 'Maestría',
    'description' => 'Precisión casi nativa: estructuras formales, énfasis, colocaciones y control del registro.',
    'position' => 6,
    'units' => [
        [
            'slug' => 'c2-subjunctive-formal',
            'title' => 'Subjuntivo y estructuras formales',
            'summary' => 'I suggest that he be…, were it not for…, should you need…: el inglés de contratos y discursos.',
            'theory' => <<<'HTML'
<h2>El subjuntivo en inglés</h2>
<p>Es casi invisible porque usa la <strong>forma base</strong> del verbo para todas las personas. Aparece después de verbos y expresiones de exigencia o recomendación.</p>
<p class="ex">The board insisted that he resign immediately. <span class="es">El directorio exigió que renunciara de inmediato.</span></p>
<p class="ex">It is essential that every employee be informed. <span class="es">Es fundamental que se informe a cada empleado.</span></p>
<p>Disparadores: <em>suggest, recommend, insist, demand, propose, it is essential/vital/imperative that</em>.</p>
<div class="tip">El inglés británico informal suele usar <em>should</em>: <em>I suggest that he should resign</em>. Las dos formas son correctas; el subjuntivo suena más formal.</div>
<div class="warn"><strong>Error típico:</strong> agregar -s por costumbre: <em>I recommend that she takes</em>. En subjuntivo es <em>that she take</em>.</div>

<h2>Condicionales con inversión</h2>
<p>En registro formal se elimina <em>if</em> y se invierte:</p>
<table class="tbl">
<thead><tr><th>Neutro</th><th>Formal</th></tr></thead>
<tbody>
<tr><td>If you need help, …</td><td>Should you need help, …</td></tr>
<tr><td>If I were in your position, …</td><td>Were I in your position, …</td></tr>
<tr><td>If we had known, …</td><td>Had we known, …</td></tr>
<tr><td>If it weren't for her support, …</td><td>Were it not for her support, …</td></tr>
</tbody></table>
<p class="ex">Should you have any questions, please do not hesitate to contact us. <span class="es">Ante cualquier consulta, no dude en contactarnos.</span></p>

<h2>Fórmulas fijas</h2>
<p>Restos del subjuntivo antiguo: <em>God save the King, be that as it may, so be it, come what may, suffice it to say</em>.</p>
HTML,
            'vocab' => [
                ['imperative', 'imprescindible'], ['be that as it may', 'sea como fuere'], ['come what may', 'pase lo que pase'], ['suffice it to say', 'basta con decir'],
                ['hesitate', 'dudar'], ['comply', 'cumplir'], ['forthwith', 'de inmediato (formal)'], ['stipulate', 'estipular'],
            ],
            'exercises' => [
                ['mcq', 'The doctor recommended that he ___ smoking.', ['stops', 'stop', 'stopped'], 1, 'Subjuntivo: forma base sin -s.'],
                ['mcq', 'It is vital that the report ___ submitted by Friday.', ['is', 'be', 'will be'], 1, 'Subjuntivo de be: be.'],
                ['mcq', '___ you require further information, please contact us.', ['If', 'Should', 'Would'], 1, 'Inversión formal: Should you…'],
                ['mcq', '___ it not been for your help, we would have failed.', ['Had', 'Were', 'Should'], 0, 'Had it not been for = If it hadn\'t been for (pasado irreal).'],
                ['fill', '___ I known about the problem, I would have acted sooner.', ['Had'], 'Had + sujeto + participio = If I had known.'],
                ['mcq', '___ I in your shoes, I would reconsider.', ['Was', 'Were', 'Am'], 1, 'Were I = If I were.'],
                ['mcq', 'They demanded that the company ___ the workers immediately.', ['compensates', 'compensate', 'would compensate'], 1, 'Demand that + forma base.'],
                ['order', 'Ordená la oración.', 'Should you have any questions please contact our team.', 'Inversión con should.'],
                ['listen', '¿Qué exigió el sindicato?', 'The union insisted that the agreement be signed before the end of the month.', ['Que se postergara la firma', 'Que el acuerdo se firmara antes de fin de mes', 'Que se cancelara el acuerdo'], 1, 'Insisted that… be signed.'],
                ['dictation', 'Escuchá y escribí.', 'Had we left earlier, we would have caught the train.', 'Had + sujeto = If we had.'],
            ],
        ],
        [
            'slug' => 'c2-cleft-fronting',
            'title' => 'Oraciones hendidas y fronting',
            'summary' => 'What I need is…, It was Ana who…, All I did was…: mover el foco de la oración.',
            'theory' => <<<'HTML'
<h2>Mover el foco</h2>
<p>En español movemos palabras con libertad para enfatizar. El inglés tiene un orden más rígido, así que usa estructuras especiales: las <strong>cleft sentences</strong>.</p>

<h2>It-cleft: It is / was … that / who</h2>
<p class="ex">Ana broke the vase. → It was Ana who broke the vase. <span class="es">Fue Ana la que rompió el florero.</span></p>
<p class="ex">It was in 2001 that the crisis began. <span class="es">Fue en 2001 cuando empezó la crisis.</span></p>

<h2>Wh-cleft: What… is / was</h2>
<p class="ex">I need a holiday. → What I need is a holiday. <span class="es">Lo que necesito son unas vacaciones.</span></p>
<p class="ex">What surprised me was his reaction. <span class="es">Lo que me sorprendió fue su reacción.</span></p>
<p>Con acciones se usa <em>do</em>: <em>What she did was (to) call the police.</em></p>

<h2>All, the thing, the reason</h2>
<table class="tbl">
<thead><tr><th>Estructura</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>All I … is / was</td><td>All I want is some peace and quiet.</td></tr>
<tr><td>The thing that … is</td><td>The thing that worries me is the cost.</td></tr>
<tr><td>The reason why … is that</td><td>The reason why I left is that I was bored.</td></tr>
<tr><td>The place where … is</td><td>The place where we met is now a bank.</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> <em>The reason is because…</em> es redundante en registro cuidado. Preferí <em>The reason is that…</em></div>

<h2>Fronting</h2>
<p>Poner al inicio un elemento que normalmente va al final, para contraste o estilo:</p>
<p class="ex">Strange as it may seem, I enjoy doing my taxes. <span class="es">Por raro que parezca, disfruto hacer los impuestos.</span></p>
<p class="ex">Talented she certainly is; reliable she is not.</p>
HTML,
            'vocab' => [
                ['vase', 'florero'], ['peace and quiet', 'paz y tranquilidad'], ['worry', 'preocupar'], ['strange as it may seem', 'por raro que parezca'],
                ['reliable', 'confiable'], ['outcome', 'resultado'], ['bewilder', 'desconcertar'], ['emphasis', 'énfasis'],
            ],
            'exercises' => [
                ['mcq', 'It was my sister ___ found the solution.', ['which', 'who', 'what'], 1, 'It-cleft con persona: who (o that).'],
                ['mcq', '___ I really need is a good night\'s sleep.', ['That', 'What', 'Which'], 1, 'Wh-cleft: What I need is…'],
                ['mcq', 'What he did ___ resign without notice.', ['is', 'was', 'did'], 1, 'Pasado: What he did was…'],
                ['fill', '___ I want is to be left alone. (Todo lo que)', ['All'], 'All I want is…'],
                ['mcq', 'The reason I called is ___ I need your advice.', ['because', 'that', 'why'], 1, 'Registro cuidado: the reason is that.'],
                ['mcq', '___ as it may seem, he has never left Argentina.', ['Strange', 'Strangely', 'How strange'], 0, 'Fronting: adjetivo + as + it may seem.'],
                ['order', 'Ordená la oración.', 'It was in Rosario that I met my wife.', 'It was + lugar + that.'],
                ['order', 'Ordená la oración.', 'What worries me is the lack of time.', 'Wh-cleft.'],
                ['listen', '¿Qué es lo que más le molestó?', 'What annoyed me most was not the delay but the lack of information.', ['La demora', 'La falta de información', 'El precio'], 1, 'Not the delay but the lack of information.'],
                ['dictation', 'Escuchá y escribí.', 'It was the manager who made the final decision.', 'It-cleft para enfatizar quién.'],
            ],
        ],
        [
            'slug' => 'c2-collocations-idioms',
            'title' => 'Colocaciones y expresiones idiomáticas',
            'summary' => 'Las combinaciones de palabras que hacen sonar natural: make vs do, heavy rain, take into account.',
            'theory' => <<<'HTML'
<h2>¿Qué es una colocación?</h2>
<p>Son combinaciones que los nativos usan juntas por costumbre. Gramaticalmente otras serían posibles, pero suenan raras. A nivel C2 la diferencia entre "correcto" y "natural" está acá.</p>
<p class="ex">heavy rain, not strong rain · a strong coffee, not a powerful coffee · make a mistake, not do a mistake</p>

<h2>Make y do</h2>
<table class="tbl">
<thead><tr><th>make (crear, producir)</th><th>do (tareas, actividades)</th></tr></thead>
<tbody>
<tr><td>make a decision, a mistake, progress</td><td>do business, research, your best</td></tr>
<tr><td>make an effort, a complaint, a profit</td><td>do the shopping, homework, a favour</td></tr>
<tr><td>make room, sense, a difference</td><td>do damage, harm, justice</td></tr>
</tbody></table>

<h2>Verbo + sustantivo en registro profesional</h2>
<p class="ex">reach an agreement, meet a deadline, raise awareness, pose a threat, draw a conclusion, take into account, run a risk</p>
<div class="warn"><strong>Error típico (calco del español):</strong> <em>take a decision</em> se usa en inglés británico, pero <em>make a decision</em> es universal. Y nunca <em>realise a project</em> en el sentido de "realizar": se dice <em>carry out a project</em>.</div>

<h2>Expresiones idiomáticas útiles</h2>
<table class="tbl">
<thead><tr><th>Expresión</th><th>Significado</th></tr></thead>
<tbody>
<tr><td>a blessing in disguise</td><td>un mal que resulta en bien</td></tr>
<tr><td>to cut corners</td><td>ahorrar a costa de la calidad</td></tr>
<tr><td>to be on the same page</td><td>estar de acuerdo, alineados</td></tr>
<tr><td>to get the hang of it</td><td>agarrarle la mano</td></tr>
<tr><td>the elephant in the room</td><td>el tema evidente que nadie menciona</td></tr>
</tbody></table>
HTML,
            'vocab' => [
                ['meet a deadline', 'cumplir un plazo'], ['raise awareness', 'concientizar'], ['pose a threat', 'representar una amenaza'], ['carry out', 'llevar a cabo'],
                ['cut corners', 'hacer las cosas a medias'], ['get the hang of', 'agarrarle la mano'], ['bear in mind', 'tener en cuenta'], ['a blessing in disguise', 'no hay mal que por bien no venga'],
            ],
            'exercises' => [
                ['mcq', 'We need to ___ a decision before Friday.', ['do', 'make', 'have'], 1, 'Make a decision.'],
                ['mcq', 'Could you ___ me a favour?', ['make', 'do', 'give'], 1, 'Do someone a favour.'],
                ['mcq', 'There was ___ rain all night.', ['strong', 'heavy', 'big'], 1, 'Heavy rain.'],
                ['mcq', 'The team worked overtime to ___ the deadline.', ['reach', 'meet', 'arrive'], 1, 'Meet a deadline.'],
                ['mcq', 'The researchers ___ a large-scale study.', ['realised', 'carried out', 'made up'], 1, 'Carry out a study (no realise).'],
                ['mcq', 'Losing that job was a blessing in disguise. That means…', ['Fue una desgracia total', 'Al final resultó algo bueno', 'Fue una sorpresa esperada'], 1, 'Un mal que termina en bien.'],
                ['fill', 'It took me a few weeks to get the ___ of the new software.', ['hang'], 'Get the hang of something.'],
                ['order', 'Ordená la oración.', 'We must take all the risks into account.', 'Take something into account.'],
                ['listen', '¿Qué critica el hablante?', 'The building collapsed because the company cut corners to save money.', ['Que la obra fue cara', 'Que ahorraron a costa de la calidad', 'Que tardaron mucho'], 1, 'Cut corners.'],
                ['dictation', 'Escuchá y escribí.', 'Let\'s make sure we are all on the same page.', 'On the same page = alineados.'],
            ],
        ],
        [
            'slug' => 'c2-hedging-register',
            'title' => 'Matización, registro y estilo académico',
            'summary' => 'It would appear that…, tends to, arguably: escribir con cautela y adaptar el tono a cada contexto.',
            'theory' => <<<'HTML'
<h2>Hedging: afirmar con cautela</h2>
<p>En textos académicos y profesionales se evita sonar absoluto. El <strong>hedging</strong> suaviza afirmaciones sin quitarles fuerza argumentativa.</p>
<table class="tbl">
<thead><tr><th>Recurso</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>verbos de apariencia</td><td>It would appear that the policy has failed.</td></tr>
<tr><td>modales de probabilidad</td><td>This may / might / could indicate a trend.</td></tr>
<tr><td>adverbios</td><td>arguably, presumably, apparently, to some extent</td></tr>
<tr><td>tendencia</td><td>Young people tend to use social media more.</td></tr>
<tr><td>cuantificadores</td><td>Most / Many experts agree…, not all</td></tr>
</tbody></table>
<p class="ex">Directo: This proves the theory is wrong.</p>
<p class="ex">Matizado: These findings would seem to suggest that the theory may need revising.</p>

<h2>Registro: formal, neutro, informal</h2>
<table class="tbl">
<thead><tr><th>Informal</th><th>Neutro</th><th>Formal</th></tr></thead>
<tbody>
<tr><td>find out</td><td>discover</td><td>ascertain</td></tr>
<tr><td>get</td><td>receive</td><td>obtain</td></tr>
<tr><td>go up</td><td>increase</td><td>escalate</td></tr>
<tr><td>a lot of</td><td>many</td><td>a considerable number of</td></tr>
<tr><td>Sorry for…</td><td>I apologise for…</td><td>Please accept our apologies for…</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> mezclar registros en un mismo texto: <em>We regret to inform you that the meeting's been called off, guys.</em> Mantené un único tono.</div>
<div class="tip">Los phrasal verbs (<em>put off, find out</em>) son perfectos en el habla y en mails neutros; en un informe formal preferí su equivalente latino (<em>postpone, discover</em>).</div>
HTML,
            'vocab' => [
                ['arguably', 'posiblemente, se podría decir'], ['to some extent', 'en cierta medida'], ['findings', 'hallazgos'], ['ascertain', 'determinar, averiguar'],
                ['presumably', 'presumiblemente'], ['call off', 'cancelar'], ['postpone', 'posponer'], ['tend to', 'tender a'],
            ],
            'exercises' => [
                ['mcq', '¿Cuál es la versión más matizada?', ['This proves that diet causes the disease.', 'This may suggest a link between diet and the disease.', 'Diet causes the disease.'], 1, 'May suggest + a link: cautela académica.'],
                ['mcq', 'Equivalente formal de "find out":', ['look up', 'ascertain', 'figure'], 1, 'Ascertain es formal.'],
                ['mcq', 'Equivalente formal de "put off" (una reunión):', ['postpone', 'call up', 'set back'], 0, 'Postpone.'],
                ['mcq', 'It would ___ that the results were misinterpreted.', ['seem', 'think', 'look'], 0, 'It would seem/appear that…'],
                ['fill', 'Older users ___ to prefer email over messaging apps. (tienden)', ['tend'], 'Tend to = tendencia, no absoluto.'],
                ['mcq', '¿Cuál mantiene un registro formal coherente?', ["We're sorry the order's late, it'll get there soon.", 'Please accept our apologies for the delay in delivering your order.', 'Sorry for the delay, we will deliver it ASAP.'], 1, 'Registro formal consistente.'],
                ['order', 'Ordená la oración.', 'These results would seem to suggest a different conclusion.', 'Hedging con would seem to.'],
                ['listen', '¿Qué grado de certeza expresa?', 'The data would appear to indicate that, to some extent, the campaign was effective.', ['Certeza total', 'Cautela: probablemente fue efectiva en parte', 'Rechazo: no fue efectiva'], 1, 'Would appear + to some extent = cautela.'],
                ['dictation', 'Escuchá y escribí.', 'Arguably, this is the most important finding of the study.', 'Arguably = se podría sostener que.'],
            ],
        ],
        ['slug' => 'c2-pragmatic-implicature', 'exercises' => []],
        ['slug' => 'c2-epistemic-stance', 'exercises' => []],
        ['slug' => 'c2-rhetorical-analysis', 'exercises' => []],
        ['slug' => 'c2-legal-ambiguity', 'exercises' => []],
        ['slug' => 'c2-literary-irony', 'exercises' => []],
        ['slug' => 'c2-academic-synthesis', 'exercises' => []],
        ['slug' => 'c2-diplomatic-negotiation', 'exercises' => []],
        ['slug' => 'c2-scientific-causality', 'exercises' => []],
        ['slug' => 'c2-register-shifts', 'exercises' => []],
        ['slug' => 'c2-first-checkpoint', 'exercises' => []],
        ['slug' => 'c2-ambiguity-in-policy', 'exercises' => []],
        ['slug' => 'c2-discourse-presupposition', 'exercises' => []],
        ['slug' => 'c2-subtle-humour', 'exercises' => []],
        ['slug' => 'c2-cross-disciplinary-synthesis', 'exercises' => []],
        ['slug' => 'c2-counterargument-steelmanning', 'exercises' => []],
        ['slug' => 'c2-forensic-reading', 'exercises' => []],
        ['slug' => 'c2-editorial-tone', 'exercises' => []],
        ['slug' => 'c2-advanced-mediation', 'exercises' => []],
        ['slug' => 'c2-science-uncertainty', 'exercises' => []],
        ['slug' => 'c2-integrated-checkpoint-two', 'exercises' => []],
        ['slug' => 'c2-intertextual-allusion', 'exercises' => []],
        ['slug' => 'c2-argumentative-precision', 'exercises' => []],
        ['slug' => 'c2-legal-interpretation', 'exercises' => []],
        ['slug' => 'c2-literary-voice', 'exercises' => []],
        ['slug' => 'c2-interdisciplinary-evaluation', 'exercises' => []],
        ['slug' => 'c2-rhetorical-equivocation', 'exercises' => []],
        ['slug' => 'c2-epistemic-responsibility', 'exercises' => []],
        ['slug' => 'c2-discourse-reformulation', 'exercises' => []],
        ['slug' => 'c2-diplomatic-crisis-brief', 'exercises' => []],
        ['slug' => 'c2-synthesis-checkpoint-three', 'exercises' => []],
        ['slug' => 'c2-metaphor-critique', 'exercises' => []],
        ['slug' => 'c2-judicial-reasoning', 'exercises' => []],
        ['slug' => 'c2-research-replication', 'exercises' => []],
        ['slug' => 'c2-policy-tradeoffs', 'exercises' => []],
        ['slug' => 'c2-cultural-subtext', 'exercises' => []],
        ['slug' => 'c2-linguistic-ambiguity', 'exercises' => []],
        ['slug' => 'c2-investigative-reporting', 'exercises' => []],
        ['slug' => 'c2-philosophical-objection', 'exercises' => []],
        ['slug' => 'c2-conference-discussion', 'exercises' => []],
        ['slug' => 'c2-integrated-checkpoint-four', 'exercises' => []],
        ['slug' => 'c2-euphemism-framing', 'exercises' => []],
        ['slug' => 'c2-ethical-deliberation', 'exercises' => []],
        ['slug' => 'c2-comparative-criticism', 'exercises' => []],
        ['slug' => 'c2-scientific-disagreement', 'exercises' => []],
        ['slug' => 'c2-diplomatic-reframing', 'exercises' => []],
        ['slug' => 'c2-satire-context', 'exercises' => []],
        ['slug' => 'c2-policy-language', 'exercises' => []],
        ['slug' => 'c2-statistical-interpretation', 'exercises' => []],
        ['slug' => 'c2-cross-cultural-inference', 'exercises' => []],
        ['slug' => 'c2-integrated-evaluation-five', 'exercises' => []],
        ['slug' => 'c2-discourse-power', 'exercises' => []],
        ['slug' => 'c2-critical-literature', 'exercises' => []],
        ['slug' => 'c2-evidence-boundaries', 'exercises' => []],
        ['slug' => 'c2-sophisticated-concession', 'exercises' => []],
        ['slug' => 'c2-public-speaking-mastery', 'exercises' => []],
        ['slug' => 'c2-final-assessment', 'exercises' => []],
    ],
];
