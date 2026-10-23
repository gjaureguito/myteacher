<?php
declare(strict_types=1);

/** Corrige un ejercicio del lado servidor. Las respuestas nunca se envían al navegador antes de responder. */
function grade_exercise(array $ex, $given): bool
{
    $answer = json_decode($ex['answer_json'], true) ?: [];
    switch ($ex['type']) {
        case 'mcq':
        case 'listen':
            return is_numeric($given) && (int) $given === (int) ($answer['index'] ?? -1);
        case 'fill':
        case 'order':
        case 'dictation':
            if (!is_string($given)) return false;
            $g = norm_answer($given);
            if ($ex['type'] === 'dictation') $g = strip_punct($g);
            foreach ($answer['accept'] ?? [] as $acc) {
                $a = norm_answer((string) $acc);
                if ($ex['type'] === 'dictation') $a = strip_punct($a);
                if ($g === $a) return true;
            }
            return false;
    }
    return false;
}

function strip_punct(string $s): string
{
    return trim(preg_replace('/\s+/u', ' ', preg_replace("/[^\p{L}\p{N}' ]+/u", '', $s)));
}

/** Texto de la respuesta correcta para mostrar después de responder. */
function correct_answer_text(array $ex): string
{
    $answer = json_decode($ex['answer_json'], true) ?: [];
    $data = json_decode($ex['data_json'], true) ?: [];
    if (isset($answer['index'])) return (string) ($data['options'][$answer['index']] ?? '');
    return (string) ($answer['accept'][0] ?? '');
}
