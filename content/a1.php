<?php
return [
    'code' => 'a1',
    'name' => 'Principiante',
    'description' => 'Presentarte, hablar de tu rutina, tu familia y lo que hay a tu alrededor.',
    'position' => 1,
    'units' => [
        [
            'slug' => 'a1-verb-to-be',
            'title' => 'El verbo to be y los pronombres',
            'summary' => 'Ser y estar en una sola palabra: am, is, are. Afirmativa, negativa y preguntas.',
            'theory' => <<<'HTML'
<h2>Un verbo, dos significados</h2>
<p>En español distinguimos <em>ser</em> y <em>estar</em>. En inglés las dos ideas se expresan con <strong>to be</strong>. El contexto aclara cuál es.</p>
<p class="ex">I am a teacher. <span class="es">Soy profesor.</span></p>
<p class="ex">I am tired. <span class="es">Estoy cansado.</span></p>

<h2>Pronombres personales y conjugación</h2>
<table class="tbl">
<thead><tr><th>Pronombre</th><th>To be</th><th>Contracción</th><th>Significado</th></tr></thead>
<tbody>
<tr><td>I</td><td>am</td><td>I'm</td><td>yo soy / estoy</td></tr>
<tr><td>you</td><td>are</td><td>you're</td><td>vos sos / ustedes son</td></tr>
<tr><td>he</td><td>is</td><td>he's</td><td>él es</td></tr>
<tr><td>she</td><td>is</td><td>she's</td><td>ella es</td></tr>
<tr><td>it</td><td>is</td><td>it's</td><td>es (cosa, animal, clima)</td></tr>
<tr><td>we</td><td>are</td><td>we're</td><td>nosotros somos</td></tr>
<tr><td>they</td><td>are</td><td>they're</td><td>ellos son</td></tr>
</tbody></table>
<div class="tip"><strong>It</strong> no tiene equivalente en español: se usa para cosas, animales y para hablar del clima o la hora. <em>It's cold. It's ten o'clock.</em></div>

<h2>Negativa: agregá not</h2>
<p class="ex">She is not at home. / She isn't at home. <span class="es">Ella no está en casa.</span></p>
<p class="ex">We aren't from Rosario. <span class="es">No somos de Rosario.</span></p>
<p>Con <em>I</em> no existe "amn't": se dice <strong>I'm not</strong>.</p>

<h2>Preguntas: invertí el orden</h2>
<p>El verbo pasa adelante del sujeto. No hace falta ningún auxiliar.</p>
<p class="ex">Are you Argentinian? — Yes, I am. <span class="es">¿Sos argentino? — Sí.</span></p>
<p class="ex">Is it expensive? — No, it isn't. <span class="es">¿Es caro? — No.</span></p>
<div class="warn"><strong>Error típico:</strong> en las respuestas cortas afirmativas no se contrae. Se dice <em>Yes, I am</em>, nunca <em>Yes, I'm</em>.</div>

<h2>Edad, hambre y frío también van con to be</h2>
<p>Donde en español usamos <em>tener</em>, el inglés usa <strong>to be</strong>:</p>
<p class="ex">I am thirty years old. <span class="es">Tengo treinta años.</span></p>
<p class="ex">Are you hungry? <span class="es">¿Tenés hambre?</span></p>
HTML,
            'vocab' => [
                ['teacher', 'profesor/a'], ['tired', 'cansado/a'], ['hungry', 'con hambre'], ['cold', 'frío / con frío'],
                ['from', 'de (origen)'], ['married', 'casado/a'], ['busy', 'ocupado/a'], ['happy', 'feliz, contento/a'],
            ],
            'exercises' => [
                ['mcq', 'They ___ my neighbours.', ['is', 'am', 'are'], 2, 'They siempre va con are.'],
                ['mcq', 'It ___ very hot today.', ['are', 'is', 'am'], 1, 'Para el clima usamos it + is.'],
                ['fill', 'I ___ twenty-five years old.', ['am', "'m"], 'La edad se expresa con to be: I am 25 years old.'],
                ['fill', 'She ___ not a doctor, she is a nurse.', ['is'], 'She + is. La negativa agrega not después del verbo.'],
                ['mcq', '___ you married?', ['Is', 'Are', 'Do'], 1, 'En preguntas con to be, el verbo va adelante: Are you…?'],
                ['mcq', 'Is your brother at work? — Yes, he ___.', ["he's", 'is', 'are'], 1, 'En respuestas cortas afirmativas no se contrae: Yes, he is.'],
                ['order', 'Ordená la pregunta.', 'Where are you from?', 'Pregunta con palabra interrogativa: Where + are + sujeto.'],
                ['order', 'Ordená la oración negativa.', "We aren't hungry now.", 'Sujeto + aren\'t + adjetivo.'],
                ['listen', 'Escuchá y elegí lo que dice.', "I'm not tired, I'm busy.", ["I'm tired and busy.", "I'm not tired, I'm busy.", "I'm not busy, I'm tired."], 1, 'Prestá atención a la contracción I\'m y a not.'],
                ['dictation', 'Escuchá y escribí la oración.', 'My name is Laura and I am a teacher.', 'Fijate en is y am, dos formas del mismo verbo.'],
            ],
        ],
        [
            'slug' => 'a1-present-simple',
            'title' => 'Presente simple: rutinas y hábitos',
            'summary' => 'Hablar de lo que hacés habitualmente, con do/does y la -s de la tercera persona.',
            'theory' => <<<'HTML'
<h2>¿Para qué sirve?</h2>
<p>El presente simple expresa <strong>hábitos, rutinas y verdades generales</strong>: lo que pasa siempre o con frecuencia.</p>
<p class="ex">I work from Monday to Friday. <span class="es">Trabajo de lunes a viernes.</span></p>
<p class="ex">Water boils at 100 degrees. <span class="es">El agua hierve a 100 grados.</span></p>

<h2>La regla de la -s</h2>
<p>Con <strong>he, she, it</strong> el verbo lleva una -s. Es el error más frecuente de los principiantes, así que vale la pena automatizarlo.</p>
<table class="tbl">
<thead><tr><th>Terminación del verbo</th><th>Regla</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>la mayoría</td><td>+ s</td><td>work → works</td></tr>
<tr><td>-s, -sh, -ch, -x, -o</td><td>+ es</td><td>watch → watches, go → goes</td></tr>
<tr><td>consonante + y</td><td>y → ies</td><td>study → studies</td></tr>
<tr><td>irregular</td><td>—</td><td>have → has</td></tr>
</tbody></table>

<h2>Negativas y preguntas: el auxiliar do / does</h2>
<p>El inglés necesita un auxiliar. Cuando aparece <strong>does</strong>, la -s se va del verbo principal: la tercera persona ya está marcada en el auxiliar.</p>
<p class="ex">She doesn't eat meat. <span class="es">Ella no come carne.</span></p>
<p class="ex">Do you speak English? <span class="es">¿Hablás inglés?</span></p>
<p class="ex">Does he live in Mendoza? <span class="es">¿Vive en Mendoza?</span></p>
<div class="warn"><strong>Error típico:</strong> <em>She doesn't works</em> o <em>Does he lives?</em> Con does, el verbo va en forma base: <em>She doesn't work. Does he live?</em></div>

<h2>Adverbios de frecuencia</h2>
<p>Van <strong>antes del verbo principal</strong>, pero <strong>después de to be</strong>.</p>
<p class="ex">I usually have breakfast at eight. <span class="es">Normalmente desayuno a las ocho.</span></p>
<p class="ex">He is always late. <span class="es">Siempre llega tarde.</span></p>
<p>De más a menos: always, usually, often, sometimes, rarely, never.</p>
HTML,
            'vocab' => [
                ['wake up', 'despertarse'], ['get up', 'levantarse'], ['have breakfast', 'desayunar'], ['commute', 'viajar al trabajo'],
                ['usually', 'normalmente'], ['often', 'a menudo'], ['never', 'nunca'], ['weekend', 'fin de semana'],
            ],
            'exercises' => [
                ['mcq', 'My father ___ in a bank.', ['work', 'works', 'working'], 1, 'Tercera persona (my father = he): works.'],
                ['fill', 'She ___ (watch) TV every night.', ['watches'], 'Watch termina en -ch, entonces suma -es.'],
                ['fill', 'He ___ (study) medicine at university.', ['studies'], 'Consonante + y → ies.'],
                ['mcq', 'They ___ like football.', ["doesn't", "don't", 'not'], 1, 'They usa don\'t.'],
                ['mcq', '___ your sister drive?', ['Do', 'Does', 'Is'], 1, 'Your sister = she → Does.'],
                ['mcq', 'Does he ___ in Córdoba?', ['lives', 'live', 'living'], 1, 'Después de does, el verbo va sin -s.'],
                ['order', 'Ubicá el adverbio de frecuencia.', 'I usually take the bus to work.', 'El adverbio va antes del verbo principal.'],
                ['order', 'Ordená la oración.', 'She is never late for class.', 'Con to be, el adverbio va después del verbo.'],
                ['listen', '¿Qué hace Marta los sábados?', 'On Saturdays Marta plays tennis with her friends.', ['Trabaja', 'Juega al tenis', 'Va al cine'], 1, 'Plays tennis: juega al tenis.'],
                ['dictation', 'Escuchá y escribí.', 'He gets up at seven and has breakfast.', 'Fijate en las -s de gets y has.'],
            ],
        ],
        [
            'slug' => 'a1-there-is-articles',
            'title' => 'There is / there are, artículos y plurales',
            'summary' => 'Describir lugares: hay un/una, hay muchos, a / an / the y plurales irregulares.',
            'theory' => <<<'HTML'
<h2>"Hay" en inglés</h2>
<p>Para decir que algo existe en un lugar usamos <strong>there is</strong> (singular) y <strong>there are</strong> (plural).</p>
<p class="ex">There is a pharmacy on the corner. <span class="es">Hay una farmacia en la esquina.</span></p>
<p class="ex">There are three bedrooms in the flat. <span class="es">Hay tres dormitorios en el departamento.</span></p>
<p>Negativa: <em>There isn't / There aren't</em>. Pregunta: <em>Is there…? / Are there…?</em></p>
<p class="ex">Are there any restaurants near here? <span class="es">¿Hay restaurantes cerca?</span></p>

<h2>A, an y the</h2>
<table class="tbl">
<thead><tr><th>Artículo</th><th>Uso</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>a</td><td>uno cualquiera, antes de sonido consonante</td><td>a car, a university</td></tr>
<tr><td>an</td><td>uno cualquiera, antes de sonido vocal</td><td>an apple, an hour</td></tr>
<tr><td>the</td><td>uno específico, ya conocido</td><td>the car in the garage</td></tr>
</tbody></table>
<div class="tip">Lo que importa es el <strong>sonido</strong>, no la letra: <em>a university</em> (suena "iu") pero <em>an hour</em> (la h es muda).</div>
<div class="warn"><strong>Error típico:</strong> para hablar en general no se usa the. <em>I like dogs</em> (me gustan los perros en general), no <em>I like the dogs</em>. Las profesiones sí llevan a: <em>She's a lawyer</em>.</div>

<h2>Plurales</h2>
<p>La mayoría suma -s. Los terminados en -s, -sh, -ch, -x suman -es (<em>bus → buses</em>). Algunos son irregulares y conviene memorizarlos:</p>
<p class="ex">man → men, woman → women, child → children, person → people, foot → feet, tooth → teeth</p>

<h2>Some y any</h2>
<p><strong>Some</strong> en afirmativas, <strong>any</strong> en negativas y preguntas.</p>
<p class="ex">There are some eggs, but there isn't any milk. <span class="es">Hay huevos, pero no hay leche.</span></p>
HTML,
            'vocab' => [
                ['corner', 'esquina'], ['flat / apartment', 'departamento'], ['bedroom', 'dormitorio'], ['kitchen', 'cocina'],
                ['near', 'cerca'], ['children', 'niños, hijos'], ['people', 'gente, personas'], ['neighbourhood', 'barrio'],
            ],
            'exercises' => [
                ['mcq', 'There ___ a bank in my street.', ['is', 'are', 'have'], 0, 'A bank es singular: there is.'],
                ['mcq', 'There ___ many people in the park.', ['is', 'are', 'has'], 1, 'People es plural: there are.'],
                ['mcq', 'I need ___ umbrella.', ['a', 'an', 'the'], 1, 'Umbrella empieza con sonido vocal: an.'],
                ['mcq', 'She is ___ university student.', ['an', 'a', 'the'], 1, 'University suena "iu", sonido consonante: a.'],
                ['fill', 'One child, two ___.', ['children'], 'Plural irregular.'],
                ['fill', 'Is there ___ coffee left? (some/any)', ['any'], 'En preguntas usamos any.'],
                ['mcq', 'I love ___. (los gatos, en general)', ['the cats', 'cats', 'a cats'], 1, 'Para generalizar, plural sin artículo.'],
                ['order', 'Ordená la pregunta.', 'Are there any shops near here?', 'Are there + any + sustantivo plural.'],
                ['listen', '¿Cuántos dormitorios tiene la casa?', 'The house has a big kitchen and there are two bedrooms upstairs.', ['Uno', 'Dos', 'Tres'], 1, 'There are two bedrooms.'],
                ['dictation', 'Escuchá y escribí.', 'There is a supermarket next to the school.', 'Next to = al lado de.'],
            ],
        ],
        [
            'slug' => 'a1-possessives-family',
            'title' => 'Posesivos, have got y la familia',
            'summary' => 'My, your, his, her… el genitivo sajón (\'s) y cómo hablar de tu familia.',
            'theory' => <<<'HTML'
<h2>Adjetivos posesivos</h2>
<table class="tbl">
<thead><tr><th>Pronombre</th><th>Posesivo</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>I</td><td>my</td><td>my car</td></tr>
<tr><td>you</td><td>your</td><td>your house</td></tr>
<tr><td>he</td><td>his</td><td>his wife</td></tr>
<tr><td>she</td><td>her</td><td>her husband</td></tr>
<tr><td>it</td><td>its</td><td>its name</td></tr>
<tr><td>we</td><td>our</td><td>our kids</td></tr>
<tr><td>they</td><td>their</td><td>their parents</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> en español <em>su</em> sirve para él, ella y ellos. En inglés depende del <strong>poseedor</strong>: <em>Juan and his sister</em>, <em>Ana and her brother</em>. Y el posesivo no cambia con el plural: <em>my books</em>, nunca <em>mys books</em>.</div>
<div class="tip"><strong>its</strong> (posesivo) no lleva apóstrofo. <strong>it's</strong> es la contracción de <em>it is</em>.</div>

<h2>El genitivo sajón: 's</h2>
<p>Para decir "de alguien" agregamos <strong>'s</strong> al poseedor, que va primero.</p>
<p class="ex">my mother's car <span class="es">el auto de mi mamá</span></p>
<p class="ex">my parents' house <span class="es">la casa de mis padres</span></p>
<p>Si el plural ya termina en -s, solo se agrega el apóstrofo.</p>

<h2>Have got</h2>
<p>En inglés británico es muy común <strong>have got</strong> para posesión y familia. Equivale a <em>have</em>.</p>
<p class="ex">I've got two brothers. = I have two brothers. <span class="es">Tengo dos hermanos.</span></p>
<p class="ex">Has she got any children? <span class="es">¿Tiene hijos?</span></p>
<p class="ex">He hasn't got a car. <span class="es">No tiene auto.</span></p>
HTML,
            'vocab' => [
                ['parents', 'padres'], ['brother', 'hermano'], ['sister', 'hermana'], ['husband', 'esposo'],
                ['wife', 'esposa'], ['son / daughter', 'hijo / hija'], ['grandparents', 'abuelos'], ['cousin', 'primo/a'],
            ],
            'exercises' => [
                ['mcq', 'Pedro lives with ___ wife.', ['her', 'his', 'their'], 1, 'El poseedor es Pedro (he): his.'],
                ['mcq', 'Carla and ___ brother are students.', ['his', 'her', 'its'], 1, 'El poseedor es Carla (she): her.'],
                ['mcq', 'The dog is wagging ___ tail.', ["it's", 'its', 'his'], 1, 'Its, sin apóstrofo, es el posesivo.'],
                ['fill', 'This is the car of my sister = This is my ___ car.', ["sister's"], 'Genitivo sajón: sister\'s.'],
                ['fill', 'I ___ got three cousins.', ['have', "'ve"], 'I have got / I\'ve got.'],
                ['mcq', '___ he got any brothers?', ['Have', 'Has', 'Does'], 1, 'He → has got.'],
                ['order', 'Ordená la oración.', "My parents' house is very old.", 'Parents ya termina en -s: solo apóstrofo.'],
                ['order', 'Ordená la pregunta.', 'Have you got any children?', 'Have + sujeto + got.'],
                ['listen', '¿Cuántos hermanos tiene?', "I've got one sister and two brothers.", ['Dos', 'Tres', 'Cuatro'], 1, 'One sister + two brothers = tres.'],
                ['dictation', 'Escuchá y escribí.', 'Their daughter is a nurse.', 'Their = de ellos.'],
            ],
        ],
        // Nuevas unidades originales: contenido completo en content/units/<slug>.php
        ['slug' => 'a1-greetings-introductions', 'exercises' => []],
        ['slug' => 'a1-daily-schedule', 'exercises' => []],
        ['slug' => 'a1-shopping-requests', 'exercises' => []],
        // Unidades 5–10: definidas por completo en content/units/<slug>.php
        ['slug' => 'a1-present-continuous', 'exercises' => []],
        ['slug' => 'a1-can-ability', 'exercises' => []],
        ['slug' => 'a1-food-countable', 'exercises' => []],
        ['slug' => 'a1-was-were', 'exercises' => []],
        ['slug' => 'a1-directions-imperatives', 'exercises' => []],
        ['slug' => 'a1-likes-free-time', 'exercises' => []],
    ],
];
