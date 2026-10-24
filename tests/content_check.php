<?php
/**
 * Valida el contenido sin base de datos: php tests/content_check.php
 * Revisa índices de respuesta, textos de lectura, criterios de escritura (el modelo debe cumplirlos),
 * pares de unir sin repetir, vocabulario con imagen y slugs únicos.
 */
define('ROOT', dirname(__DIR__));
function norm_answer(string $s): string { return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $s))); }
require ROOT . '/src/seed.php';
require ROOT . '/src/grading.php';

$errors = []; $slugs = []; $totals = ['units' => 0, 'exercises' => 0, 'vocab' => 0, 'readings' => 0, 'words' => 0]; $bySkill = [];
foreach (content_files() as $file) {
    $lv = load_level($file);
    foreach ($lv['units'] as $u) {
        $s = $u['slug'];
        $tag = "[$s]";
        if (isset($slugs[$s])) $errors[] = "$tag slug repetido";
        $slugs[$s] = true;
        foreach (['title', 'summary', 'theory'] as $k) if (empty($u[$k])) $errors[] = "$tag falta $k";
        $totals['units']++;
        $totals['vocab'] += count($u['vocab'] ?? []);
        $words = array_map(fn($v) => $v[0], $u['vocab'] ?? []);
        if (count($words) !== count(array_unique($words))) $errors[] = "$tag vocabulario repetido";
        foreach ($u['readings'] ?? [] as $k => $r) {
            $totals['readings']++;
            $totals['words'] += str_word_count($r['text']);
            if (str_word_count($r['text']) < 60) $errors[] = "$tag lectura $k muy corta";
        }
        foreach ($u['exercises'] as $i => $ex) {
            $n = $i + 1;
            try {
                [$type, $prompt, $data, $answer, $expl, $skill] = exercise_rows($ex, $u['readings'] ?? []);
            } catch (Throwable $e) { $errors[] = "$tag #$n " . $e->getMessage(); continue; }
            $totals['exercises']++;
            $bySkill[$skill] = ($bySkill[$skill] ?? 0) + 1;
            if (trim((string) $prompt) === '') $errors[] = "$tag #$n consigna vacía";
            if (isset($answer['index'])) {
                $opts = $data['options'] ?? [];
                if (!isset($opts[$answer['index']])) $errors[] = "$tag #$n índice fuera de rango";
                if (count($opts) !== count(array_unique($opts))) $errors[] = "$tag #$n opciones repetidas";
            }
            if ($type === 'fill' && !preg_match('/_{3,}/', $prompt)) $errors[] = "$tag #$n fill sin ___";
            if ($type === 'match') {
                $l = $data['left']; $r = $answer['pairs'];
                if (count($l) < 3 || count($l) !== count(array_unique($l)) || count($r) !== count(array_unique($r))) $errors[] = "$tag #$n pares inválidos";
            }
            if ($type === 'write') {
                foreach ($answer['must'] as [$re, $label]) {
                    if (@preg_match('/' . str_replace('/', '\/', $re) . '/iu', '') === false) $errors[] = "$tag #$n regex inválida: $re";
                }
                $g = grade_detail(['type' => 'write', 'answer_json' => json_encode($answer)], $answer['model']);
                if (!$g['ok']) $errors[] = "$tag #$n el modelo no cumple: " . implode('; ', array_column(array_filter($g['detail']['criteria'], fn($c) => !$c['ok']), 'label'));
            }
            if ($type === 'speak' && word_similarity($data['say'], $data['say']) !== 100) $errors[] = "$tag #$n speak inválido";
            if ($type !== 'write' && trim((string) $expl) === '') $errors[] = "$tag #$n sin explicación";
        }
    }
}
ksort($bySkill);
echo json_encode(['totals' => $totals, 'skills' => $bySkill], JSON_UNESCAPED_UNICODE), "\n";
if ($errors) { echo implode("\n", $errors), "\n"; exit(1); }
echo "OK\n";
