<?php
return [
    'icon' => '🎧',
    'title' => 'Gustos y tiempo libre: like + -ing y pronombres objeto',
    'summary' => 'Hablar de lo que te gusta y no te gusta hacer, invitar y usar me, him, her, us, them.',
    'theory' => <<<'HTML'
<h2>Like, love, hate + -ing</h2>
<p>Después de verbos de gusto, la actividad va con <strong>-ing</strong>.</p>
<p class="ex">I love reading on the train. <span class="es">Me encanta leer en el tren.</span></p>
<p class="ex">She doesn't like getting up early. <span class="es">No le gusta levantarse temprano.</span></p>
<table class="tbl">
<thead><tr><th>Más positivo</th><th></th><th></th><th></th><th>Más negativo</th></tr></thead>
<tbody><tr><td>love</td><td>really like</td><td>don't mind</td><td>don't like</td><td>hate / can't stand</td></tr></tbody>
</table>
<div class="warn"><strong>Error típico:</strong> traducir "me gusta" palabra por palabra. No se dice <em>Me like</em> ni <em>It likes me</em>: el que siente el gusto es el sujeto. <strong>I like music. My sister likes dancing.</strong></div>

<h2>Pronombres objeto</h2>
<p>Van después del verbo o de una preposición.</p>
<table class="tbl">
<thead><tr><th>Sujeto</th><th>Objeto</th><th>Ejemplo</th></tr></thead>
<tbody>
<tr><td>I</td><td>me</td><td>Call me tomorrow.</td></tr>
<tr><td>you</td><td>you</td><td>I can help you.</td></tr>
<tr><td>he / she / it</td><td>him / her / it</td><td>I like him. I know her. I love it.</td></tr>
<tr><td>we</td><td>us</td><td>Come with us!</td></tr>
<tr><td>they</td><td>them</td><td>I see them every day.</td></tr>
</tbody></table>

<h2>Invitar y responder</h2>
<p class="ex">Would you like to come to the cinema? <span class="es">¿Querés venir al cine?</span></p>
<p class="ex">I'd love to! / Sorry, I can't. I'm busy. <span class="es">¡Me encantaría! / Perdón, no puedo. Estoy ocupado.</span></p>
<div class="tip"><strong>Would you like + to</strong> es para invitar. <strong>Do you like + -ing</strong> es para preguntar por gustos generales.</div>
HTML,
    'vocab' => [
        ['go to the gym', 'ir al gimnasio'], ['listen to music', 'escuchar música'], ['go out', 'salir'], ['hang out with friends', 'juntarse con amigos'],
        ['play video games', 'jugar videojuegos'], ['take photos', 'sacar fotos'], ['go hiking', 'hacer senderismo'], ['travel', 'viajar'],
        ['paint', 'pintar'], ['garden', 'jardinería / jardín'], ['don\'t mind', 'no me molesta'], ['can\'t stand', 'no soporto'],
        ['series', 'serie'], ['band', 'banda'], ['hobby', 'pasatiempo'], ['invite', 'invitar'],
    ],
    'vocab_icons' => [
        'go to the gym' => '🏋️', 'listen to music' => '🎧', 'go out' => '🌆', 'hang out with friends' => '🍻', 'play video games' => '🎮', 'take photos' => '📷', 'go hiking' => '🥾', 'travel' => '✈️',
        'paint' => '🖌️', 'garden' => '🌱', "don't mind" => '😐', "can't stand" => '🤢', 'series' => '📺', 'band' => '🎸', 'hobby' => '🧩', 'invite' => '💌',
    ],
    'readings' => [
        'profiles' => [
            'kind' => 'article',
            'title' => 'Find your weekend partner',
            'text' => "Javier, 29: I love going hiking and taking photos of the mountains. I don't mind getting up early on Saturdays. I can't stand shopping centres! I'm looking for someone to go to the hills with me on Sundays.\n\nNora, 34: I really like cooking and I love trying new restaurants. My friends say I'm a great cook. I don't like sports very much, but I enjoy walking by the river. Would you like to join me for a food tour of San Telmo?\n\nAlex, 22: I play video games and I listen to a lot of music. My favourite band is Tan Biónica; I love them! I hate going to bed early. If you like concerts, write to me and let's go together.",
        ],
    ],
    'exercises' => [
        ['mcq', 'I love ___ in the sea.', ['swim', 'swimming', 'to swimming'], 1, 'Love + -ing: love swimming.'],
        ['mcq', 'My brother ___ like football.', ["don't", "doesn't", 'isn\'t'], 1, 'He / my brother + doesn\'t + like.'],
        ['fill', 'Can you help ___ ? I can\'t open this bottle.', ['me'], 'Después del verbo va el pronombre objeto: help me.'],
        ['mcq', 'Where are Ana and Luis? I can\'t see ___ .', ['they', 'them', 'their'], 1, 'Objeto de they: them.'],
        ['mcq', 'That\'s Mr Díaz. Do you know ___ ?', ['he', 'his', 'him'], 2, 'Objeto de he: him.'],
        ['mcq', '___ to come to my party on Saturday?', ['Do you like', 'Would you like', 'Are you like'], 1, 'Para invitar: Would you like to…?'],
        ['fill', 'We\'re going to the cinema. Do you want to come with ___ ?', ['us'], 'Objeto de we: us.'],
        ['order', 'Ordená la oración.', "She doesn't like cooking at all.", 'Sujeto + doesn\'t like + -ing + at all.'],
        ['order', 'Ordená la invitación.', 'Would you like to go out tonight?', 'Would you like + to + verbo.'],

        ['image', '🥾', ['go hiking', 'go to the gym', 'travel'], 0, 'Go hiking: hacer senderismo. Muchas actividades van con go + -ing.'],
        ['image', '📷', ['paint', 'take photos', 'play video games'], 1, 'Take photos: sacar fotos (no "make photos").'],
        ['image', '🎮', ['listen to music', 'play video games', 'go out'], 1, 'Play video games: jugar videojuegos.'],
        ['match', 'Uní cada pronombre sujeto con su pronombre objeto.', [['I', 'me'], ['he', 'him'], ['she', 'her'], ['we', 'us'], ['they', 'them']], 'Los pronombres objeto van después del verbo o de una preposición.'],

        ['listen', '¿Qué no le gusta hacer a Ema?', "Ema loves dancing, but she hates running. She says it's boring.", ['Bailar', 'Correr', 'Leer'], 1, '"She hates running."'],
        ['listen', '¿Acepta la invitación?', "I'd love to come, but I can't on Friday. What about Saturday?", ['Sí, el viernes', 'No puede el viernes y propone el sábado', 'No quiere ir'], 1, '"I can\'t on Friday. What about Saturday?"'],
        ['dictation', 'Escuchá y escribí la oración.', 'I really like listening to music.', 'Like + -ing: listening to music.'],

        ['reading', 'profiles', "What does Javier hate?", ['Getting up early', 'Shopping centres', 'Taking photos'], 1, '"I can\'t stand shopping centres!"'],
        ['reading', 'profiles', 'Who likes cooking?', ['Javier', 'Nora', 'Alex'], 1, 'Nora: "I really like cooking".'],
        ['reading', 'profiles', 'True or false? Nora loves sports.', ['True', 'False'], 1, 'False: "I don\'t like sports very much".'],
        ['reading', 'profiles', "Who would be a good partner for a concert?", ['Javier', 'Nora', 'Alex'], 2, 'Alex: "If you like concerts, write to me".'],

        ['speak', 'Contá tus gustos: decí la oración en voz alta.', "I love travelling, but I can't stand flying.", 'Can\'t stand: acentuá stand. Travelling: /ˈtrævəlɪŋ/.'],
        ['speak', 'Invitá a alguien en voz alta.', 'Would you like to come to the cinema with me?', 'Would you se une: /wʊdʒu/.'],

        ['write', 'Escribí tu perfil para la sección "Find your weekend partner": qué te encanta hacer, qué no te gusta, qué no soportás y una invitación al final.',
            ['min' => 40, 'must' => [["\b(love|like|enjoy)s? \w+ing\b", 'Usás like/love/enjoy + -ing'], ["\b(don't|doesn't|hate|can't stand)\b", 'Decís algo que no te gusta'], ['\bwould you like to\b', 'Terminás con una invitación (Would you like to…?)'], ['\b(me|him|her|us|them)\b', 'Usás un pronombre objeto']],
             'model' => "Hi! I'm Flor, I'm 27 and I live in La Plata. I love going hiking and I really enjoy taking photos of nature. I don't mind getting up early, but I can't stand crowded places. I also like playing board games with my friends; they always beat me! Would you like to come with me to the Sierra de la Ventana next month?"],
            'Después de like, love, hate, enjoy y can\'t stand, el verbo va con -ing.'],
    ],
];
