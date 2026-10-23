<?php
declare(strict_types=1);

/** Supplemental, versioned workshops: never reseed or replace stored exercises. */
function learning_lab(string $slug): ?array
{
    if (!preg_match('/^[a-z0-9-]+$/D', $slug)) return null;
    $file = ROOT . '/content/labs/' . $slug . '.json';
    if (!is_file($file)) return null;
    return json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
}

function learning_tracks(): array
{
    return [
        'a1' => ['Foundations', 'Construí tu primera conversación.', 'Presentarte, describir tu entorno y hablar de tu vida cotidiana.'],
        'a2' => ['Everyday connections', 'Contá lo que pasó. Planeá lo que viene.', 'Narrar experiencias, comparar opciones y desenvolverte en situaciones habituales.'],
        'b1' => ['Independent thinking', 'Pasá de responder a desarrollar ideas.', 'Relacionar experiencias, explicar procesos y expresar condiciones y obligaciones.'],
        'b2' => ['Ideas in conversation', 'Dale estructura y precisión a tu voz.', 'Explorar hipótesis, transmitir mensajes y construir descripciones más complejas.'],
        'c1' => ['Critical expression', 'Leé entre líneas. Escribí con intención.', 'Organizar argumentos, graduar el énfasis y conectar evidencia con conclusiones.'],
        'c2' => ['Precision & nuance', 'Elegí cada palabra por lo que hace.', 'Trabajar el registro, las colocaciones y los límites de una afirmación académica.'],
    ];
}
