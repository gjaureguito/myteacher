<?php
return [
    'icon' => '🏠',
    'vocab' => [
        ['living room', 'living / sala'], ['bathroom', 'baño'], ['garden', 'jardín'], ['balcony', 'balcón'],
        ['sofa', 'sillón / sofá'], ['fridge', 'heladera'], ['supermarket', 'supermercado'], ['bus stop', 'parada de colectivo'],
    ],
    'vocab_icons' => [
        'corner' => '📐', 'flat / apartment' => '🏢', 'bedroom' => '🛏️', 'kitchen' => '🍳', 'near' => '📍', 'children' => '🧒', 'people' => '👥', 'neighbourhood' => '🏘️',
        'living room' => '🛋️', 'bathroom' => '🛁', 'garden' => '🌳', 'balcony' => '🌇', 'sofa' => '🛋️', 'fridge' => '🧊', 'supermarket' => '🛒', 'bus stop' => '🚏',
    ],
    'readings' => [
        'ad' => [
            'kind' => 'ad',
            'title' => 'Flat for rent in Palermo',
            'text' => "Bright two-bedroom flat on the fourth floor of a quiet building. There is a large living room with a balcony and there are two bathrooms. The kitchen is small but modern: there is a new fridge and a dishwasher. There isn't a garden, but there is a park on the corner.\n\nThe neighbourhood is great for families. There are two schools and a supermarket near the building, and there is a bus stop in front of the door. There aren't any parking spaces, sorry!\n\nThe rent is 650,000 pesos a month. Pets are welcome. Call Paula on 11 5555 0101.",
        ],
    ],
    'exercises' => [
        ['image', '🛁', ['kitchen', 'bathroom', 'bedroom'], 1, 'Bathroom: baño. Bath es la bañera.'],
        ['image', '🧊', ['sofa', 'fridge', 'balcony'], 1, 'Fridge: heladera. Es la forma corta de refrigerator.'],
        ['image', '🚏', ['bus stop', 'supermarket', 'corner'], 0, 'Bus stop: parada de colectivo.'],
        ['match', 'Uní cada singular con su plural.', [['child', 'children'], ['person', 'people'], ['woman', 'women'], ['box', 'boxes'], ['city', 'cities']], 'Child, person y woman son irregulares; box agrega -es y city cambia -y por -ies.'],

        ['listen', '¿Cuántos dormitorios tiene la casa?', "Our new house is lovely. There are three bedrooms, but there's only one bathroom.", ['Uno', 'Dos', 'Tres'], 2, '"There are three bedrooms". Baño hay uno solo.'],
        ['listen', '¿Qué hay cerca de la casa?', "There isn't a supermarket near my flat, but there's a small market on the corner.", ['Un supermercado', 'Un mercado chico en la esquina', 'Una parada de colectivo'], 1, '"There isn\'t a supermarket… but there\'s a small market on the corner."'],
        ['dictation', 'Escuchá y escribí la pregunta.', 'Is there a bank near here?', 'Pregunta con there is: Is there…? Se usa a con sustantivos singulares.'],

        ['reading', 'ad', 'How many bathrooms are there?', ['One', 'Two', 'Four'], 1, '"There are two bathrooms."'],
        ['reading', 'ad', 'True or false? The flat has a garden.', ['True', 'False'], 1, 'False: "There isn\'t a garden, but there is a park on the corner."'],
        ['reading', 'ad', 'What is in front of the building?', ['A school', 'A bus stop', 'A park'], 1, '"There is a bus stop in front of the door."'],
        ['reading', 'ad', 'Can you have a dog in the flat?', ['Yes, pets are welcome.', 'No, pets are not allowed.', 'The ad doesn\'t say.'], 0, '"Pets are welcome": se aceptan mascotas.', 'hint' => 'Leé el último párrafo.'],

        ['speak', 'Describí tu casa: decí esta oración en voz alta.', "There are two bedrooms and there's a small balcony.", 'There are se une: /ðeərɑː/. La th suave se hace con la lengua entre los dientes.'],
        ['speak', 'Preguntá en voz alta.', 'Is there a supermarket near here?', 'En preguntas de sí o no la entonación sube al final.'],

        ['write', 'Describí tu casa o departamento para un amigo extranjero: qué ambientes hay, qué muebles hay en tu cuarto y qué hay en el barrio.',
            ['min' => 40, 'must' => [["\bthere('s| is)\b", 'Usás there is / there\'s'], ['\bthere are\b', 'Usás there are con un plural'], ["\bthere (isn't|aren't|is not|are not)\b", 'Incluís una negativa (there isn\'t / aren\'t)'], ['\b(near|next to|in front of|on the corner|opposite)\b', 'Decís dónde están las cosas (near, next to…)']],
             'model' => "I live in a small flat in Caballito. There are two bedrooms, a kitchen and a living room. There isn't a garden, but there's a nice balcony with plants. In my bedroom there is a big bed, a desk and a lot of books. The neighbourhood is quiet. There are three cafés near my building and there is a subway station on the corner."],
            'Con sustantivos plurales usá there are; con singulares o incontables, there is.'],
    ],
];
