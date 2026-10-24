<?php
return [
    'icon' => '📜',
    'vocab' => [
        ['uniform', 'uniforme'], ['ID card', 'documento de identidad'], ['fine', 'multa'], ['permission', 'permiso'],
        ['wear a helmet', 'usar casco'], ['speed limit', 'límite de velocidad'], ['forbidden', 'prohibido'], ['necessary', 'necesario'],
    ],
    'vocab_icons' => [
        'must' => '❗', 'have to' => '📌', 'should' => '💡', 'allowed to' => '✅', 'seatbelt' => '🚗', 'rule' => '📏', 'optional' => '🔘', 'had better' => '⚠️',
        'uniform' => '👔', 'ID card' => '🪪', 'fine' => '💸', 'permission' => '🙋', 'wear a helmet' => '⛑️', 'speed limit' => '🚸', 'forbidden' => '⛔', 'necessary' => '✔️',
    ],
    'readings' => [
        'rules' => [
            'kind' => 'ad',
            'title' => 'Welcome to Hostel Patagonia: house rules',
            'text' => "We want everybody to enjoy their stay, so please read these rules.\n\nCheck-in is from 2 pm. You have to show your passport or ID card at reception. Breakfast is included and it's served from 7 to 10. You don't have to book it.\n\nYou mustn't smoke inside the building; there is a special area in the garden. Guests are allowed to use the kitchen until 11 pm, but you must wash your plates after you use them. Please don't leave food in the fridge without your name on it.\n\nThe Wi-Fi password is on the board next to the stairs. Quiet time starts at midnight, so you shouldn't play loud music after that. If you are going to trek in the national park, you'd better tell us your route before you leave. The weather here changes very quickly!",
        ],
    ],
    'exercises' => [
        ['image', '⛑️', ['wear a helmet', 'wear a uniform', 'speed limit'], 0, 'Wear a helmet: usar casco. Para ropa y accesorios usamos wear.'],
        ['image', '💸', ['fine', 'rule', 'permission'], 0, 'Fine: multa. "You have to pay a fine if you park here."'],
        ['match', 'Uní cada modal con su significado.', [['must', 'obligación fuerte o regla'], ["mustn't", 'prohibición'], ["don't have to", 'no es necesario'], ['should', 'consejo'], ["you'd better", 'advertencia: te conviene']], 'Ojo: mustn\'t (prohibido) y don\'t have to (no hace falta) significan cosas distintas.'],
        ['mcq', 'Choose the word: Smoking is ___ in all public buildings.', ['forbidden', 'optional', 'necessary'], 0, 'Forbidden: prohibido.', 'skill' => 'vocabulary'],

        ['listen', '¿Es obligatorio el uniforme?', "You don't have to wear a uniform, but you should dress smartly.", ['Sí, es obligatorio.', 'No es obligatorio, pero conviene vestirse bien.', 'Está prohibido.'], 1, '"You don\'t have to wear a uniform": no es necesario.'],
        ['listen', '¿Qué está prohibido en el museo?', "You can take photos, but you mustn't use the flash.", ['Sacar fotos', 'Usar el flash', 'Entrar con mochila'], 1, '"You mustn\'t use the flash."'],
        ['dictation', 'Escuchá y escribí la oración.', 'You should see a doctor.', 'Should + verbo base para dar un consejo.'],

        ['reading', 'rules', 'What do you have to show at reception?', ['A credit card', 'Your passport or ID card', 'Your booking email'], 1, '"You have to show your passport or ID card at reception."'],
        ['reading', 'rules', 'True or false? You have to book breakfast in advance.', ['True', 'False'], 1, 'False: "You don\'t have to book it."'],
        ['reading', 'rules', 'Where are you allowed to smoke?', ['In your room', 'In the kitchen', 'In a special area in the garden'], 2, '"There is a special area in the garden."'],
        ['reading', 'rules', 'Why should you tell the hostel your trekking route?', ['Because it\'s the law', 'Because the weather changes quickly', 'Because they sell maps'], 1, '"The weather here changes very quickly!"'],

        ['speak', 'Dá un consejo en voz alta.', "You should drink more water and you'd better rest.", "You'd better suena /jʊd ˈbetə/."],
        ['speak', 'Decí la regla en voz alta.', "You mustn't use your phone while you're driving.", "Mustn't se pronuncia /ˈmʌsnt/: la primera t no suena."],

        ['write', 'Escribí las reglas de tu casa, tu trabajo o tu club para alguien nuevo: qué es obligatorio, qué está prohibido, qué no hace falta y algún consejo.',
            ['min' => 70, 'must' => [['\b(must|have to|has to)\b', 'Usás must o have to'], ["\b(mustn't|must not|can't|aren't allowed to|isn't allowed to)\b", 'Incluís una prohibición'], ["\b(don't have to|doesn't have to)\b", "Usás don't have to (no es necesario)"], ["\b(should|had better|'d better)\b", 'Das un consejo (should / had better)']],
             'model' => "Welcome to our office! Here are some rules. You have to arrive before ten, but you don't have to wear a suit: jeans are fine. You must lock your computer when you leave your desk. You mustn't eat at your desk; there is a nice kitchen on the second floor. You're allowed to work from home on Fridays. My advice: you should bring a jacket because the air conditioning is very strong, and you'd better not park in the director's space!"],
            "Mustn't = está prohibido. Don't have to = no hace falta."],
    ],
];
