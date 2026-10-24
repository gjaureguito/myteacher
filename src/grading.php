<?php
declare(strict_types=1);

/**
 * Corrige un ejercicio del lado servidor. Las respuestas nunca se envían al navegador antes de responder.
 * Devuelve ['ok' => bool, 'detail' => array] (detalle para escritura y speaking).
 */
function grade_detail(array $ex, $given): array
{
    $answer = json_decode($ex['answer_json'], true) ?: [];
    switch ($ex['type']) {
        case 'mcq':
        case 'listen':
        case 'reading':
        case 'image':
            return ['ok' => is_numeric($given) && (int) $given === (int) ($answer['index'] ?? -1), 'detail' => []];

        case 'fill':
        case 'order':
        case 'dictation':
            if (!is_string($given)) return ['ok' => false, 'detail' => []];
            $g = norm_answer($given);
            if ($ex['type'] === 'dictation') $g = strip_punct($g);
            foreach ($answer['accept'] ?? [] as $acc) {
                $a = norm_answer((string) $acc);
                if ($ex['type'] === 'dictation') $a = strip_punct($a);
                if ($g === $a) return ['ok' => true, 'detail' => []];
            }
            if ($ex['type'] === 'dictation') return ['ok' => false, 'detail' => dictation_diff($answer['accept'] ?? [], $given)];
            return ['ok' => false, 'detail' => []];

        case 'match':
            $pairs = $answer['pairs'] ?? [];
            if (!is_array($given) || count($given) !== count($pairs)) return ['ok' => false, 'detail' => ['right' => 0, 'total' => count($pairs)]];
            $right = 0; $marks = [];
            foreach ($pairs as $i => $p) {
                $hit = norm_answer((string) ($given[$i] ?? '')) === norm_answer((string) $p);
                $marks[] = $hit; if ($hit) $right++;
            }
            return ['ok' => $right === count($pairs), 'detail' => ['right' => $right, 'total' => count($pairs), 'marks' => $marks]];

        case 'write':
            $text = is_string($given) ? trim($given) : '';
            $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text));
            $min = (int) ($answer['min'] ?? 20);
            $criteria = [['label' => "Al menos $min palabras (escribiste $words)", 'ok' => $words >= $min]];
            foreach ($answer['must'] ?? [] as [$re, $label]) {
                $criteria[] = ['label' => $label, 'ok' => (bool) @preg_match('/' . str_replace('/', '\/', $re) . '/iu', $text)];
            }
            $ok = !in_array(false, array_column($criteria, 'ok'), true);
            return ['ok' => $ok, 'detail' => ['criteria' => $criteria, 'words' => $words, 'model' => $answer['model'] ?? '']];

        case 'speak':
            $target = (string) ($answer['accept'][0] ?? '');
            if (is_array($given) && !empty($given['self'])) {
                return ['ok' => true, 'detail' => ['score' => null, 'self' => true]];
            }
            $heard = is_array($given) ? (string) ($given['transcript'] ?? '') : (string) $given;
            $score = word_similarity($target, $heard);
            return ['ok' => $score >= 80, 'detail' => ['score' => $score, 'heard' => mb_substr($heard, 0, 300), 'missing' => missing_words($target, $heard)]];
    }
    return ['ok' => false, 'detail' => []];
}

function grade_exercise(array $ex, $given): bool
{
    return grade_detail($ex, $given)['ok'];
}

function strip_punct(string $s): string
{
    return trim(preg_replace('/\s+/u', ' ', preg_replace("/[^\p{L}\p{N}' ]+/u", '', $s)));
}

function speech_words(string $s): array
{
    $s = mb_strtolower(str_replace(['’', '‘'], "'", $s));
    $s = strip_punct($s);
    return $s === '' ? [] : explode(' ', $s);
}

/** Porcentaje de palabras en orden (subsecuencia común más larga). */
function word_similarity(string $target, string $heard): int
{
    $a = speech_words($target); $b = speech_words($heard);
    if (!$a || !$b) return 0;
    $prev = array_fill(0, count($b) + 1, 0);
    foreach ($a as $wa) {
        $cur = [0];
        foreach ($b as $j => $wb) $cur[] = $wa === $wb ? $prev[$j] + 1 : max($prev[$j + 1], $cur[$j]);
        $prev = $cur;
    }
    return (int) round(100 * end($prev) / max(count($a), count($b)));
}

/**
 * Compara el dictado palabra por palabra contra la respuesta aceptada más parecida.
 * Devuelve las palabras del alumno marcadas (ok / bad) y huecos (miss) donde falta algo,
 * sin revelar el texto de las palabras que faltan.
 */
function dictation_diff(array $accepts, string $given): array
{
    $mine = [];
    foreach (preg_split('/\s+/u', trim($given)) ?: [] as $tok) {
        $n = speech_words($tok);
        if ($n) $mine[] = ['w' => $tok, 'n' => implode(' ', $n)];
    }
    $best = null;
    foreach ($accepts as $acc) {
        $target = speech_words((string) $acc);
        if (!$target) continue;
        $ops = word_alignment($target, array_column($mine, 'n'));
        $right = count(array_filter($ops, fn($o) => $o[0] === 'ok'));
        if ($best === null || $right > $best['right']) $best = ['ops' => $ops, 'right' => $right, 'total' => count($target)];
    }
    if ($best === null) return [];

    // Arma la vista: una palabra mal escrita en lugar de otra cuenta como un solo error, no como error + hueco.
    $tokens = []; $bad = []; $miss = 0;
    $flush = function () use (&$tokens, &$bad, &$miss) {
        foreach ($bad as $w) { $tokens[] = ['t' => 'bad', 'w' => $w]; if ($miss > 0) $miss--; }
        for (; $miss > 0; $miss--) $tokens[] = ['t' => 'miss'];
        $bad = [];
    };
    foreach ($best['ops'] as [$op, $j]) {
        if ($op === 'ok') { $flush(); $tokens[] = ['t' => 'ok', 'w' => $mine[$j]['w']]; }
        elseif ($op === 'extra') $bad[] = $mine[$j]['w'];
        else $miss++;
    }
    $flush();
    return ['words' => $tokens, 'right' => $best['right'], 'total' => $best['total']];
}

/** Alineación por subsecuencia común más larga: ok (coincide), extra (sobra en lo escrito), miss (falta). */
function word_alignment(array $a, array $b): array
{
    $n = count($a); $m = count($b);
    $L = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));
    for ($i = $n - 1; $i >= 0; $i--)
        for ($j = $m - 1; $j >= 0; $j--)
            $L[$i][$j] = $a[$i] === $b[$j] ? $L[$i + 1][$j + 1] + 1 : max($L[$i + 1][$j], $L[$i][$j + 1]);
    $ops = []; $i = 0; $j = 0;
    while ($i < $n && $j < $m) {
        if ($a[$i] === $b[$j]) { $ops[] = ['ok', $j]; $i++; $j++; }
        elseif ($L[$i][$j + 1] >= $L[$i + 1][$j]) { $ops[] = ['extra', $j]; $j++; }
        else { $ops[] = ['miss', null]; $i++; }
    }
    for (; $j < $m; $j++) $ops[] = ['extra', $j];
    for (; $i < $n; $i++) $ops[] = ['miss', null];
    return $ops;
}

function missing_words(string $target, string $heard): array
{
    $b = speech_words($heard);
    return array_values(array_slice(array_diff(speech_words($target), $b), 0, 5));
}

/** Texto de la respuesta correcta para mostrar después de responder. */
function correct_answer_text(array $ex): string
{
    $answer = json_decode($ex['answer_json'], true) ?: [];
    $data = json_decode($ex['data_json'], true) ?: [];
    if ($ex['type'] === 'match') {
        $out = [];
        foreach ($data['left'] ?? [] as $i => $l) $out[] = $l . ' → ' . ($answer['pairs'][$i] ?? '');
        return implode(' · ', $out);
    }
    if ($ex['type'] === 'write') return (string) ($answer['model'] ?? '');
    if (isset($answer['index'])) return (string) ($data['options'][$answer['index']] ?? '');
    return (string) ($answer['accept'][0] ?? '');
}

/** Pista genérica si el ejercicio no trae una propia. */
function generic_hint(string $type): string
{
    return match ($type) {
        'fill' => 'Mirá el sujeto y el tiempo verbal de la oración antes de completar.',
        'order' => 'Empezá por el sujeto o la palabra interrogativa y buscá el verbo.',
        'listen', 'dictation' => 'Escuchalo de nuevo en velocidad lenta (🐢) y prestá atención a las terminaciones.',
        'reading' => 'Volvé al texto y buscá la oración que contiene la información. La respuesta tiene que estar en el texto.',
        'match' => 'Empezá por los pares que estás seguro y dejá los dudosos para el final.',
        'image' => 'Pensá en la palabra más concreta para lo que muestra la imagen.',
        'write' => 'Revisá cada criterio de la lista: los que están en rojo todavía faltan.',
        'speak' => 'Escuchá el modelo, decilo más despacio y articulá cada palabra.',
        default => 'Releé la consigna y descartá primero las opciones que seguro no son.',
    };
}

/** Datos para mostrar un ejercicio ya resuelto (solo se envían si el alumno ya lo resolvió). */
function done_payload(array $ex): array
{
    $answer = json_decode($ex['answer_json'] ?? '{}', true) ?: [];
    $mine = isset($ex['last_answer']) && $ex['last_answer'] !== null ? json_decode($ex['last_answer'], true) : null;
    return [
        'answer' => correct_answer_text($ex),
        'index' => isset($answer['index']) ? (int) $answer['index'] : null,
        'pairs' => $ex['type'] === 'match' ? ($answer['pairs'] ?? []) : null,
        'mine' => $mine,
        'explanation' => (string) ($ex['explanation'] ?? ''),
    ];
}
