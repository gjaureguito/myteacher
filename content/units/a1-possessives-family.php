<?php
return [
    'icon' => '👨‍👩‍👧',
    'vocab' => [
        ['mother', 'madre'], ['father', 'padre'], ['uncle', 'tío'], ['aunt', 'tía'],
        ['niece', 'sobrina'], ['nephew', 'sobrino'], ['baby', 'bebé'], ['pet', 'mascota'],
    ],
    'vocab_icons' => [
        'parents' => '👫', 'brother' => '👦', 'sister' => '👧', 'husband' => '🤵', 'wife' => '👰', 'son / daughter' => '👶', 'grandparents' => '👴', 'cousin' => '🧑‍🤝‍🧑',
        'mother' => '👩', 'father' => '👨', 'uncle' => '🧔', 'aunt' => '👩‍🦰', 'niece' => '👧', 'nephew' => '👦', 'baby' => '🍼', 'pet' => '🐶',
    ],
    'readings' => [
        'family' => [
            'kind' => 'email',
            'title' => 'My family in Bariloche',
            'text' => "Dear Emma,\n\nThanks for your photos! Now it's my turn. In my photo you can see my family at my grandmother's house in Bariloche. My grandmother's name is Rosa and she's eighty. She has got a beautiful garden.\n\nMy parents are next to her. My father is a doctor and my mother is an architect. I have got one brother, Nico. His wife is Japanese and their daughter, Aiko, is two. She's my niece and she's very funny!\n\nThe dog in the photo isn't ours; it's my uncle's dog. Its name is Toby. We haven't got any pets because we live in a small flat.\n\nWrite soon,\nValentina",
        ],
    ],
    'exercises' => [
        ['image', '🍼', ['pet', 'baby', 'cousin'], 1, 'Baby: bebé. El plural es babies.'],
        ['image', '🐶', ['pet', 'uncle', 'nephew'], 0, 'Pet: mascota. My pet is a dog.'],
        ['match', 'Uní cada familiar con su pareja femenina.', [['father', 'mother'], ['brother', 'sister'], ['husband', 'wife'], ['uncle', 'aunt'], ['nephew', 'niece']], 'Pares masculino/femenino de la familia.'],
        ['match', 'Uní cada pronombre con su posesivo.', [['I', 'my'], ['you', 'your'], ['he', 'his'], ['she', 'her'], ['we', 'our'], ['they', 'their']], 'El posesivo siempre va antes del sustantivo: my sister, their house.'],

        ['listen', '¿Cuántos hermanos tiene?', "I've got two sisters, but I haven't got any brothers.", ['Dos hermanas', 'Dos hermanos', 'Un hermano y una hermana'], 0, '"I\'ve got two sisters, but I haven\'t got any brothers."'],
        ['listen', '¿De quién es el auto?', "This isn't my car. It's my cousin's. Mine is in the garage.", ['Es suyo.', 'Es de su primo.', 'Es de su tío.'], 1, '"It\'s my cousin\'s": es del primo. El \'s indica posesión.'],
        ['dictation', 'Escuchá y escribí la oración.', "My sister's husband is from Chile.", "El 's indica posesión: el esposo de mi hermana."],

        ['reading', 'family', 'How old is Valentina\'s grandmother?', ['Seventy', 'Eighty', 'Eighteen'], 1, '"She\'s eighty."'],
        ['reading', 'family', 'Who is Aiko?', ["Valentina's daughter", "Valentina's niece", "Valentina's cousin"], 1, 'Aiko es la hija de Nico, el hermano de Valentina: "She\'s my niece".'],
        ['reading', 'family', 'True or false? Toby is Valentina\'s dog.', ['True', 'False'], 1, 'False: "it\'s my uncle\'s dog".'],
        ['reading', 'family', 'Why haven\'t they got any pets?', ['Because they live in a small flat.', 'Because her father is allergic.', 'Because they travel a lot.'], 0, '"We haven\'t got any pets because we live in a small flat."'],

        ['speak', 'Hablá de tu familia: decí esta oración en voz alta.', "I've got one brother and two cousins.", 'Unís I\'ve got: /aɪv ɡɒt/. La v se escucha suave.'],
        ['speak', 'Preguntá en voz alta.', 'Have you got any brothers or sisters?', 'Pregunta de sí o no: la entonación sube al final.'],

        ['write', 'Describí a tu familia (o a una familia que conozcas): quiénes son, cómo se llaman, cuántos años tienen y a qué se dedican.',
            ['min' => 40, 'must' => [["\b(have|has|'ve|'s) got\b|\b(have|has)\b", 'Usás have got / has got (o have)'], ['\b(my|his|her|their|our)\b.*\b(my|his|her|their|our)\b', 'Usás al menos dos posesivos'], ["\b\w+'s\b(?! (got|a|an|the|very|from|my|not)\b)", "Usás el 's de posesión (my mother's…)"]],
             'model' => "There are five people in my family. My parents are Carlos and Marta. My father is a teacher and my mother is a nurse. I've got two sisters. Their names are Sofía and Lara. Sofía is married and her husband's name is Diego. They have got a baby son. We haven't got a pet, but my grandparents' house has got a big garden with two cats."],
            'Recordá que his es de él y her es de ella, según quién tiene la cosa, no la cosa.'],
    ],
];
