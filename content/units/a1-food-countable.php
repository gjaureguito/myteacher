<?php
return [
    'icon' => '🍎',
    'title' => 'Comida: contables, incontables, some y any',
    'summary' => 'Hablar de comida y compras: a/an, some, any, how much y how many.',
    'theory' => <<<'HTML'
<h2>Contables e incontables</h2>
<p>Los sustantivos <strong>contables</strong> se pueden contar y tienen plural: <em>an apple, two apples</em>. Los <strong>incontables</strong> no tienen plural y no llevan a/an: <em>water, rice, bread, money</em>.</p>
<table class="tbl">
<thead><tr><th>Contables</th><th>Incontables</th></tr></thead>
<tbody>
<tr><td>an egg, three eggs</td><td>milk, cheese, butter</td></tr>
<tr><td>a tomato, tomatoes</td><td>rice, pasta, sugar</td></tr>
<tr><td>a sandwich, sandwiches</td><td>bread, meat, coffee</td></tr>
</tbody></table>
<div class="warn"><strong>Error típico:</strong> <em>breads, informations, advices</em> no existen. Para contar incontables usamos envases o porciones: <strong>a loaf of bread, a bottle of water, a piece of cake</strong>.</div>

<h2>Some y any</h2>
<p><strong>Some</strong> en afirmativas; <strong>any</strong> en negativas y preguntas. Funcionan con plurales e incontables.</p>
<p class="ex">There are some eggs in the fridge. <span class="es">Hay algunos huevos en la heladera.</span></p>
<p class="ex">We haven't got any milk. <span class="es">No tenemos nada de leche.</span></p>
<p class="ex">Is there any bread? <span class="es">¿Hay pan?</span></p>
<div class="tip">Para ofrecer o pedir algo usamos <strong>some</strong> también en preguntas: <em>Would you like some coffee? Can I have some water?</em></div>

<h2>How much y how many</h2>
<p class="ex">How many apples do you need? <span class="es">¿Cuántas manzanas necesitás? (contable)</span></p>
<p class="ex">How much sugar do you want? <span class="es">¿Cuánta azúcar querés? (incontable)</span></p>
<p class="ex">How much is it? <span class="es">¿Cuánto cuesta?</span></p>

<h2>Pedir en un restaurante</h2>
<p class="ex">I'd like a coffee and a piece of chocolate cake, please. <span class="es">Quisiera un café y una porción de torta de chocolate, por favor.</span></p>
<p class="ex">Could I have the bill, please? <span class="es">¿Me trae la cuenta, por favor?</span></p>
HTML,
    'vocab' => [
        ['apple', 'manzana'], ['bread', 'pan'], ['cheese', 'queso'], ['egg', 'huevo'],
        ['milk', 'leche'], ['rice', 'arroz'], ['meat', 'carne'], ['chicken', 'pollo'],
        ['tomato', 'tomate'], ['potato', 'papa'], ['fish', 'pescado'], ['water', 'agua'],
        ['a bottle of', 'una botella de'], ['a piece of', 'una porción / un pedazo de'], ['the bill', 'la cuenta'], ['vegetables', 'verduras'],
    ],
    'vocab_icons' => [
        'apple' => '🍎', 'bread' => '🍞', 'cheese' => '🧀', 'egg' => '🥚', 'milk' => '🥛', 'rice' => '🍚', 'meat' => '🥩', 'chicken' => '🍗',
        'tomato' => '🍅', 'potato' => '🥔', 'fish' => '🐟', 'water' => '💧', 'a bottle of' => '🍾', 'a piece of' => '🍰', 'the bill' => '🧾', 'vegetables' => '🥦',
    ],
    'readings' => [
        'menu' => [
            'kind' => 'text',
            'title' => 'Shopping list and a problem',
            'text' => "It's Saturday and Clara is making lunch for her friends. She opens the fridge. There is some cheese and there are some tomatoes, but there isn't any milk and there aren't any eggs. She needs eggs for the tortilla!\n\nShe writes a list: six eggs, a kilo of potatoes, a bottle of olive oil and some bread. She goes to the supermarket on the corner.\n\nAt the supermarket she asks: \"How much are the eggs?\" \"Two thousand pesos a dozen,\" says the man. Clara buys a dozen. There isn't any fresh bread, so she buys a loaf at the bakery next door. At home she cooks the tortilla and makes a big salad. Her friends bring some wine and a chocolate cake.",
        ],
    ],
    'exercises' => [
        ['mcq', 'There isn\'t ___ milk in the fridge.', ['some', 'any', 'a'], 1, 'En negativas usamos any.'],
        ['mcq', 'Can I have ___ water, please?', ['a', 'any', 'some'], 2, 'Para pedir u ofrecer usamos some, aunque sea pregunta.'],
        ['mcq', 'How ___ apples do you want?', ['much', 'many', 'any'], 1, 'Apples es contable plural: How many.'],
        ['mcq', 'How ___ rice do we need?', ['much', 'many', 'a'], 0, 'Rice es incontable: How much.'],
        ['fill', 'I\'d like ___ orange juice, please.', ['an'], 'Orange empieza con sonido vocálico: an orange juice.'],
        ['mcq', 'Choose the correct sentence.', ['I need two breads.', 'I need two loaves of bread.', 'I need a bread.'], 1, 'Bread es incontable: a loaf / two loaves of bread.'],
        ['fill', 'Are there ___ eggs? I want to make a cake.', ['any'], 'Pregunta con plural: any.'],
        ['order', 'Ordená el pedido.', "I'd like a piece of chocolate cake.", "I'd like + a piece of + sustantivo."],
        ['order', 'Ordená la pregunta.', 'How much is a bottle of water?', 'How much is…? = ¿Cuánto cuesta…?'],

        ['image', '🧀', ['bread', 'cheese', 'butter'], 1, 'Cheese: queso. Es incontable: some cheese.'],
        ['image', '🥦', ['vegetables', 'meat', 'rice'], 0, 'Vegetables: verduras. Es contable y casi siempre plural.'],
        ['image', '🧾', ['the menu', 'the bill', 'the table'], 1, 'The bill: la cuenta. En inglés americano se dice the check.'],
        ['match', 'Uní cada envase o porción con su alimento.', [['a bottle of', 'water'], ['a loaf of', 'bread'], ['a cup of', 'coffee'], ['a piece of', 'cake'], ['a kilo of', 'potatoes']], 'Así contamos los incontables: a bottle of water, a loaf of bread.'],

        ['listen', '¿Qué pide el cliente?', "I'd like a cheese sandwich and a glass of orange juice, please.", ['Un sándwich de jamón y un café', 'Un sándwich de queso y un jugo de naranja', 'Una ensalada y agua'], 1, '"A cheese sandwich and a glass of orange juice".'],
        ['listen', '¿Cuánto cuesta?', 'The coffee is three thousand pesos and the croissant is fifteen hundred.', ['4.500 en total', '3.000 en total', '1.500 en total'], 0, '3.000 + 1.500 = 4.500 pesos.'],
        ['dictation', 'Escuchá y escribí la pregunta.', 'Is there any sugar?', 'Pregunta con incontable: Is there any…?'],

        ['reading', 'menu', 'What is missing from Clara\'s fridge?', ['Cheese and tomatoes', 'Milk and eggs', 'Bread and wine'], 1, '"There isn\'t any milk and there aren\'t any eggs."'],
        ['reading', 'menu', 'How many eggs does Clara buy?', ['Six', 'Twelve', 'Two'], 1, 'Compra "a dozen": doce. En la lista había anotado seis.'],
        ['reading', 'menu', 'Where does she buy the bread?', ['At the supermarket', 'At the bakery', 'At home'], 1, '"She buys a loaf at the bakery next door."'],
        ['reading', 'menu', 'True or false? Her friends bring dessert.', ['True', 'False'], 0, 'True: traen vino y una torta de chocolate.'],

        ['speak', 'Pedí en un café: decí la oración en voz alta.', "I'd like a coffee and a croissant, please.", 'I\'d like suena /aɪd laɪk/. La d casi no se escucha.'],
        ['speak', 'Pedí la cuenta en voz alta.', 'Could I have the bill, please?', 'Bill tiene una i corta /bɪl/.'],

        ['write', 'Vas a hacer una cena para cuatro amigos. Escribí qué hay en tu heladera, qué falta y tu lista de compras con cantidades.',
            ['min' => 40, 'must' => [['\bsome\b', 'Usás some'], ['\bany\b', 'Usás any en una negativa o pregunta'], ['\b(a|two|three|a kilo|half a kilo) (bottles?|loaf|loaves|kilos?|packets?|pieces?|cans?|cups?) of\b', 'Indicás cantidades (a bottle of, a kilo of…)']],
             'model' => "I'm making pasta for four friends tonight. In my fridge there is some cheese and there are some tomatoes, but there isn't any cream and there aren't any onions. My shopping list: a kilo of pasta, two bottles of red wine, half a kilo of onions, a packet of butter and some fresh bread. Do I need any dessert? Yes! I want to buy a chocolate cake."],
            'Some en afirmativas, any en negativas y preguntas. Los incontables no llevan -s.'],
    ],
];
