<?php
return [
    'code' => 'c1',
    'name' => 'Avanzado',
    'description' => 'Énfasis, matices y registro formal para el trabajo, el estudio y exámenes internacionales.',
    'position' => 5,
    'units' => [
        [
            'slug' => 'c1-inversion',
            'title' => 'Inversión para dar énfasis',
            'summary' => 'Never have I…, No sooner… than, Not only… but also: el orden que suena formal y enfático.',
            'theory' => <<<'HTML'
<h2>¿Qué es la inversión?</h2>
<p>Cuando una expresión <strong>negativa o restrictiva</strong> abre la oración, el sujeto y el auxiliar se invierten como en una pregunta. El efecto es enfático y formal: aparece en discursos, ensayos y exámenes como el CAE.</p>
<p class="ex">I have never seen such chaos. → Never have I seen such chaos.</p>
<p class="ex">She not only sings but also writes her songs. → Not only does she sing, but she also writes her songs.</p>
<p>Si no hay auxiliar, se agrega <strong>do/does/did</strong>, como en las preguntas.</p>

<h2>Estructuras más usadas</h2>
<table class="tbl">
<thead><tr><th>Expresión</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>Never / Rarely / Seldom</td><td>Rarely do we see such talent.</td></tr>
<tr><td>Hardly… when / No sooner… than</td><td>No sooner had I sat down than the phone rang.</td></tr>
<tr><td>Not only… but also</td><td>Not only is it cheap, but it's also fast.</td></tr>
<tr><td>Under no circumstances</td><td>Under no circumstances should you open this door.</td></tr>
<tr><td>Only then / Only after</td><td>Only after the meeting did I understand the problem.</td></tr>
<tr><td>Not until</td><td>Not until I got home did I realise I'd lost my wallet.</td></tr>
<tr><td>Little</td><td>Little did they know what was coming.</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> invertir con Hardly pero usar than, o con No sooner y usar when. La pareja es fija: <em>hardly… when</em>, <em>no sooner… than</em>.</div>
<div class="tip">Con <em>Only after / Not until</em> se invierte la <strong>segunda</strong> cláusula, no la primera: <em>Not until I got home did I realise…</em></div>
HTML,
            'vocab' => [
                ['seldom', 'rara vez'], ['under no circumstances', 'bajo ninguna circunstancia'], ['realise', 'darse cuenta'], ['outcome', 'resultado'],
                ['unprecedented', 'sin precedentes'], ['in no way', 'de ninguna manera'], ['at no time', 'en ningún momento'], ['such', 'tal, semejante'],
            ],
            'exercises' => [
                ['mcq', 'Never ___ such a beautiful sunset.', ['I have seen', 'have I seen', 'I saw'], 1, 'Never al inicio → have I seen.'],
                ['mcq', 'No sooner had we arrived ___ it started to rain.', ['when', 'than', 'that'], 1, 'No sooner… than.'],
                ['mcq', 'Hardly had she left ___ the doorbell rang.', ['than', 'when', 'then'], 1, 'Hardly… when.'],
                ['mcq', 'Rarely ___ such dedication in a new employee.', ['we see', 'do we see', 'we do see'], 1, 'Sin auxiliar: se agrega do.'],
                ['fill', 'Not only ___ he late, but he also forgot the documents.', ['was'], 'Not only + auxiliar (was) + sujeto.'],
                ['mcq', 'Not until the next day ___ the truth.', ['I discovered', 'did I discover', 'I did discover'], 1, 'Se invierte la cláusula principal.'],
                ['order', 'Ordená la oración.', 'Under no circumstances should you share your password.', 'Under no circumstances + should + sujeto.'],
                ['order', 'Ordená la oración.', 'Little did they know what was about to happen.', 'Little + did + sujeto + verbo base.'],
                ['listen', '¿Qué expresa el hablante?', 'At no time did the company warn its customers about the risk.', ['La empresa avisó varias veces', 'La empresa nunca avisó', 'Los clientes no quisieron escuchar'], 1, 'At no time = en ningún momento.'],
                ['dictation', 'Escuchá y escribí.', 'Seldom have I met someone so generous.', 'Seldom + have + sujeto.'],
            ],
        ],
        [
            'slug' => 'c1-mixed-conditionals-wish',
            'title' => 'Condicionales mixtos y wish',
            'summary' => 'Mezclar pasado y presente en hipótesis, y expresar deseos y lamentos con wish e if only.',
            'theory' => <<<'HTML'
<h2>Condicionales mixtos</h2>
<p>Combinan tiempos cuando la condición y el resultado están en momentos distintos.</p>
<table class="tbl">
<thead><tr><th>Condición</th><th>Resultado</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>pasado irreal (had + p.p.)</td><td>presente (would + verbo)</td><td>If I had taken that job, I would be living in Madrid now.</td></tr>
<tr><td>presente irreal (pasado)</td><td>pasado (would have + p.p.)</td><td>If I weren't so shy, I would have spoken to her.</td></tr>
</tbody></table>
<p class="ex">If I had studied medicine, I would be a doctor now. <span class="es">Si hubiera estudiado medicina, ahora sería médico.</span></p>

<h2>Wish e if only</h2>
<table class="tbl">
<thead><tr><th>Deseo sobre</th><th>Estructura</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>el presente</td><td>wish + pasado</td><td>I wish I spoke French. (no lo hablo)</td></tr>
<tr><td>el pasado</td><td>wish + past perfect</td><td>I wish I had listened to you. (no te escuché)</td></tr>
<tr><td>queja por algo que molesta</td><td>wish + would</td><td>I wish you would stop interrupting.</td></tr>
</tbody></table>
<p><strong>If only</strong> funciona igual pero con más intensidad: <em>If only I had known!</em></p>
<div class="warn"><strong>Error típico:</strong> <em>I wish I would have…</em> para el pasado. Correcto: <em>I wish I had…</em>. Y <em>wish + would</em> no se usa con uno mismo: no <em>I wish I would</em>.</div>
<div class="tip">Para deseos realistas sobre el futuro se usa <em>hope</em>: <em>I hope you get the job</em> (no <em>I wish you get</em>).</div>
HTML,
            'vocab' => [
                ['if only', 'ojalá, si tan solo'], ['regret', 'lamentar'], ['take for granted', 'dar por sentado'], ['turn down', 'rechazar (oferta)'],
                ['shy', 'tímido'], ['in hindsight', 'en retrospectiva'], ['by now', 'a esta altura'], ['would rather', 'preferiría'],
            ],
            'exercises' => [
                ['mcq', 'If I had saved more money, I ___ a house now.', ['would have', 'would have had', 'had'], 0, 'Condición pasada, resultado presente: would + verbo.'],
                ['mcq', 'If she weren\'t afraid of flying, she ___ to Japan last year.', ['would go', 'would have gone', 'went'], 1, 'Condición presente, resultado pasado.'],
                ['mcq', 'I wish I ___ taller.', ['am', 'were', 'would be'], 1, 'Deseo sobre el presente: wish + pasado (were).'],
                ['mcq', 'I wish I ___ that email. It caused so many problems.', ["hadn't sent", "didn't send", "wouldn't send"], 0, 'Lamento sobre el pasado: past perfect.'],
                ['mcq', 'I wish my neighbours ___ so much noise.', ["wouldn't make", "don't make", "hadn't make"], 0, 'Queja por algo molesto: wish + would.'],
                ['mcq', 'I ___ you have a great trip!', ['wish', 'hope', 'would'], 1, 'Deseo realista: hope.'],
                ['fill', 'If only I ___ (know) earlier! I would have helped.', ['had known'], 'If only + past perfect para el pasado.'],
                ['order', 'Ordená la oración.', 'If I had accepted the offer I would be rich now.', 'Condicional mixto pasado → presente.'],
                ['listen', '¿Qué lamenta el hablante?', "I wish I had studied English when I was younger. It would be much easier now.", ['No haber viajado', 'No haber estudiado inglés de joven', 'Haber cambiado de trabajo'], 1, 'Wish + had studied.'],
                ['dictation', 'Escuchá y escribí.', 'I wish you would listen to me for once.', 'Wish + would para quejas.'],
            ],
        ],
        [
            'slug' => 'c1-advanced-passive-causative',
            'title' => 'Pasivas avanzadas y causativo',
            'summary' => 'It is said that…, he is believed to…, have something done y get something done.',
            'theory' => <<<'HTML'
<h2>Pasivas impersonales</h2>
<p>Se usan para presentar información sin atribuirla a una fuente concreta: muy comunes en periodismo y textos académicos.</p>
<table class="tbl">
<thead><tr><th>Estructura</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>It is said / believed / thought that…</td><td>It is believed that the painting is a fake.</td></tr>
<tr><td>Sujeto + is said to + verbo</td><td>The painting is believed to be a fake.</td></tr>
<tr><td>Sujeto + is said to have + p.p.</td><td>He is said to have left the country.</td></tr>
</tbody></table>
<p class="ex">The minister is reported to have resigned. <span class="es">Se informa que el ministro renunció.</span></p>
<div class="tip">Usá <strong>to have + participio</strong> cuando la acción es anterior al momento en que "se dice": <em>is said to have been</em>.</div>

<h2>Causativo: have / get something done</h2>
<p>Cuando otra persona hace algo <strong>por nosotros</strong> (generalmente un servicio):</p>
<p class="ex">I had my hair cut yesterday. <span class="es">Ayer me corté el pelo (me lo cortaron).</span></p>
<p class="ex">We're getting the kitchen painted. <span class="es">Nos están pintando la cocina.</span></p>
<p>También para experiencias negativas: <em>She had her phone stolen on the subway.</em></p>
<div class="warn"><strong>Error típico:</strong> decir <em>I cut my hair</em> cuando fuiste a la peluquería: significa que te lo cortaste vos.</div>
<p>Variante activa: <strong>have someone do</strong> / <strong>get someone to do</strong>: <em>I'll have my assistant send it. I got my brother to fix it.</em></p>
HTML,
            'vocab' => [
                ['allegedly', 'supuestamente'], ['resign', 'renunciar'], ['estimate', 'estimar'], ['fake', 'falsificación'],
                ['service (the car)', 'hacer el service'], ['repair', 'reparar'], ['rumoured', 'según rumores'], ['spokesperson', 'vocero/a'],
            ],
            'exercises' => [
                ['mcq', 'The suspect is believed ___ the country last week.', ['to leave', 'to have left', 'leaving'], 1, 'Acción anterior: to have + participio.'],
                ['mcq', 'It ___ that the company will close two factories.', ['says', 'is said', 'has said'], 1, 'Pasiva impersonal: It is said that…'],
                ['fill', 'The castle is thought ___ be 800 years old.', ['to'], 'Sujeto + is thought to + verbo.'],
                ['mcq', 'I need to ___ my car serviced.', ['make', 'have', 'do'], 1, 'Causativo: have something done.'],
                ['mcq', 'She had her bag ___ at the airport.', ['steal', 'stolen', 'stealing'], 1, 'Have + objeto + participio.'],
                ['mcq', 'I got my sister ___ the children.', ['look after', 'to look after', 'looked after'], 1, 'Get someone to do.'],
                ['order', 'Ordená la oración.', 'We are having our house painted next week.', 'Have + objeto + participio.'],
                ['listen', '¿Qué se informa sobre el artista?', 'The artist is reported to have sold the painting for two million dollars.', ['Que va a vender el cuadro', 'Que habría vendido el cuadro', 'Que compró el cuadro'], 1, 'To have sold: acción ya ocurrida.'],
                ['dictation', 'Escuchá y escribí.', 'It is estimated that thousands of people attended the concert.', 'It is estimated that…'],
            ],
        ],
        [
            'slug' => 'c1-discourse-markers',
            'title' => 'Conectores y cohesión en la escritura',
            'summary' => 'However, whereas, despite, therefore: conectar ideas con precisión en mails, informes y ensayos.',
            'theory' => <<<'HTML'
<h2>Por qué importan</h2>
<p>A nivel C1 se evalúa cómo <strong>conectás</strong> las ideas, no solo la gramática. Elegir bien el conector cambia la lógica del texto.</p>

<h2>Contraste</h2>
<table class="tbl">
<thead><tr><th>Conector</th><th>Cómo se usa</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>however</td><td>entre oraciones, con coma</td><td>Sales rose. However, profits fell.</td></tr>
<tr><td>although / even though</td><td>+ oración</td><td>Although it was late, we kept working.</td></tr>
<tr><td>despite / in spite of</td><td>+ sustantivo o -ing</td><td>Despite the rain, the event went ahead.</td></tr>
<tr><td>whereas / while</td><td>compara dos hechos</td><td>He loves cities, whereas she prefers the countryside.</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> <em>Despite it was raining</em>. Despite no va con oración: <em>Despite the rain</em> o <em>Despite the fact that it was raining</em>.</div>

<h2>Causa y consecuencia</h2>
<p class="ex">The flight was cancelled due to the storm. <span class="es">El vuelo se canceló debido a la tormenta.</span></p>
<p class="ex">Costs increased; therefore, prices had to rise. <span class="es">Los costos subieron; por lo tanto, hubo que aumentar precios.</span></p>
<p><em>due to / owing to</em> + sustantivo · <em>since / as</em> + oración · <em>therefore / consequently / as a result</em> para la consecuencia.</p>

<h2>Agregar y ordenar</h2>
<p><em>Moreover, furthermore, in addition</em> agregan argumentos. <em>Firstly, subsequently, finally</em> ordenan. <em>In conclusion, overall</em> cierran.</p>
<div class="tip">En inglés formal se evita empezar oraciones con <em>And</em> o <em>But</em>; usá <em>Moreover</em> o <em>However</em>.</div>
HTML,
            'vocab' => [
                ['however', 'sin embargo'], ['whereas', 'mientras que'], ['despite', 'a pesar de'], ['therefore', 'por lo tanto'],
                ['owing to', 'debido a'], ['moreover', 'además'], ['nevertheless', 'no obstante'], ['subsequently', 'posteriormente'],
            ],
            'exercises' => [
                ['mcq', '___ the bad weather, the match was played.', ['Although', 'Despite', 'However'], 1, 'Despite + sustantivo.'],
                ['mcq', '___ it was expensive, we bought it.', ['Despite', 'Although', 'In spite of'], 1, 'Although + oración.'],
                ['mcq', 'The product is excellent. ___, it is too expensive for most customers.', ['However', 'Therefore', 'Moreover'], 0, 'Contraste entre oraciones: However.'],
                ['mcq', 'Demand fell sharply; ___, the factory reduced production.', ['nevertheless', 'consequently', 'whereas'], 1, 'Consecuencia: consequently.'],
                ['mcq', 'My brother is outgoing, ___ I am quite shy.', ['whereas', 'despite', 'therefore'], 0, 'Comparación de dos hechos: whereas.'],
                ['fill', 'The event was cancelled ___ to a lack of funding. (debido)', ['due', 'owing'], 'Due to / owing to + sustantivo.'],
                ['mcq', 'In spite of ___ hard, he failed the exam.', ['he studied', 'studying', 'study'], 1, 'In spite of + -ing.'],
                ['order', 'Ordená la oración.', 'Despite the fact that it was raining we went out.', 'Despite the fact that + oración.'],
                ['listen', '¿Qué relación expresa el conector?', 'The company invested heavily in marketing; nevertheless, sales remained flat.', ['Causa', 'Contraste', 'Adición'], 1, 'Nevertheless = no obstante (contraste).'],
                ['dictation', 'Escuchá y escribí.', 'Moreover, the new system will reduce costs.', 'Moreover = además.'],
            ],
        ],
        // C1: desarrollo avanzado, unidades 5 a 14
        ['slug' => 'c1-stance-hedging', 'exercises' => []],
        ['slug' => 'c1-critical-source-evaluation', 'exercises' => []],
        ['slug' => 'c1-nuanced-concession', 'exercises' => []],
        ['slug' => 'c1-nominalisation-policy', 'exercises' => []],
        ['slug' => 'c1-synthesis-multiple-views', 'exercises' => []],
        ['slug' => 'c1-advanced-reported-claims', 'exercises' => []],
        ['slug' => 'c1-register-diplomacy', 'exercises' => []],
        ['slug' => 'c1-counterfactual-analysis', 'exercises' => []],
        ['slug' => 'c1-editorial-rhetoric', 'exercises' => []],
        ['slug' => 'c1-integrated-argumentation', 'exercises' => []],
        ['slug' => 'c1-evaluating-methodology', 'exercises' => []],
        ['slug' => 'c1-legal-register', 'exercises' => []],
        ['slug' => 'c1-advanced-mediation', 'exercises' => []],
        ['slug' => 'c1-academic-paraphrasing', 'exercises' => []],
        ['slug' => 'c1-editorial-bias', 'exercises' => []],
        ['slug' => 'c1-research-synthesis', 'exercises' => []],
        ['slug' => 'c1-risk-communication', 'exercises' => []],
        ['slug' => 'c1-negotiation-frameworks', 'exercises' => []],
        ['slug' => 'c1-public-speaking-nuance', 'exercises' => []],
        ['slug' => 'c1-progress-checkpoint', 'exercises' => []],
        ['slug' => 'c1-policy-brief', 'exercises' => []],
        ['slug' => 'c1-argument-fallacies', 'exercises' => []],
        ['slug' => 'c1-economics-commentary', 'exercises' => []],
        ['slug' => 'c1-scientific-peer-review', 'exercises' => []],
        ['slug' => 'c1-strategic-planning', 'exercises' => []],
        ['slug' => 'c1-diplomatic-refusal', 'exercises' => []],
        ['slug' => 'c1-technology-governance', 'exercises' => []],
        ['slug' => 'c1-literary-subtext', 'exercises' => []],
        ['slug' => 'c1-advanced-summary', 'exercises' => []],
        ['slug' => 'c1-integrated-checkpoint-two', 'exercises' => []],
        ['slug' => 'c1-rhetorical-framing', 'exercises' => []],
        ['slug' => 'c1-research-design-tradeoffs', 'exercises' => []],
        ['slug' => 'c1-diplomatic-escalation', 'exercises' => []],
        ['slug' => 'c1-literary-unreliable-narrator', 'exercises' => []],
        ['slug' => 'c1-data-visualisation-critique', 'exercises' => []],
        ['slug' => 'c1-policy-implementation', 'exercises' => []],
        ['slug' => 'c1-multisource-synthesis', 'exercises' => []],
        ['slug' => 'c1-technical-incident-brief', 'exercises' => []],
        ['slug' => 'c1-advanced-debate-rebuttal', 'exercises' => []],
        ['slug' => 'c1-integrated-analysis-three', 'exercises' => []],
        ['slug' => 'c1-public-health-policy', 'exercises' => []],
        ['slug' => 'c1-regulatory-impact', 'exercises' => []],
        ['slug' => 'c1-advanced-literary-style', 'exercises' => []],
        ['slug' => 'c1-technology-ethics', 'exercises' => []],
        ['slug' => 'c1-statistical-caution', 'exercises' => []],
        ['slug' => 'c1-cross-cultural-leadership', 'exercises' => []],
        ['slug' => 'c1-argumentative-essay', 'exercises' => []],
        ['slug' => 'c1-negotiation-impasse', 'exercises' => []],
        ['slug' => 'c1-evidence-based-briefing', 'exercises' => []],
        ['slug' => 'c1-comprehensive-checkpoint', 'exercises' => []],
    ],
];
