<?php
declare(strict_types=1);

/*
 * Carga el contenido de content/*.php en la base.
 * Formato compacto de ejercicios (ver content/a1.php). Claves opcionales en cualquier ejercicio:
 *   'skill' => grammar|vocabulary|listening|reading|writing|speaking   (si falta, se deduce del tipo)
 *   'hint'  => pista que se muestra después del primer error
 *
 *   ['mcq',       consigna, [opciones], índiceCorrecto, explicación]
 *   ['fill',      consigna con ___, [respuestas aceptadas], explicación]
 *   ['order',     consigna, 'oración correcta', explicación]          → palabras mezcladas
 *   ['listen',    consigna, 'texto que se escucha', [opciones], índice, explicación]
 *   ['dictation', consigna, 'texto que se escucha', explicación]       → escribir lo que se oye
 *   ['reading',   claveDelTexto, pregunta, [opciones], índice, explicación]   → usa $unit['readings'][clave]
 *   ['image',     emoji, [opciones], índice, explicación]              → vocabulario con imagen
 *   ['match',     consigna, [[izq, der], …], explicación]              → unir pares
 *   ['write',     consigna, ['min'=>n, 'must'=>[[regex, etiqueta], …], 'model'=>texto], explicación]
 *   ['speak',     consigna, 'oración para decir en voz alta', explicación]
 *
 * La sincronización es no destructiva: actualiza por posición (conserva los id y el progreso),
 * agrega lo nuevo y respeta las unidades editadas desde el admin (source_hash = 'admin').
 */

function content_files(): array
{
    $files = glob(ROOT . '/content/[a-c][1-2].php'); // a1.php … c2.php (se ignoran los que empiezan con _)
    sort($files);
    return $files;
}

/** Archivos por unidad: content/units/<slug>.php amplían (o definen por completo) una unidad del nivel. */
function unit_files(): array
{
    $files = glob(ROOT . '/content/units/*.php') ?: [];
    sort($files);
    return $files;
}

/**
 * Carga un nivel y fusiona content/units/<slug>.php en cada unidad:
 *  - title, summary, theory, icon: completan o reemplazan
 *  - theory_extra: se agrega al final de la teoría
 *  - vocab: se agrega; vocab_icons ['palabra' => '🍎'] pone imagen a cualquier palabra
 *  - readings: se fusionan; exercises: se agregan al final (no cambia la posición de los existentes)
 */
function load_level(string $file): array
{
    $lv = require $file;
    foreach ($lv['units'] as &$u) {
        $extra = ROOT . '/content/units/' . $u['slug'] . '.php';
        if (!is_file($extra)) continue;
        $x = require $extra;
        foreach (['title', 'summary', 'theory', 'icon'] as $k) if (isset($x[$k])) $u[$k] = $x[$k];
        if (!empty($x['theory_extra'])) $u['theory'] = ($u['theory'] ?? '') . "\n" . $x['theory_extra'];
        $u['vocab'] = array_merge($u['vocab'] ?? [], $x['vocab'] ?? []);
        foreach ($u['vocab'] as &$v) {
            if (!isset($v[2])) $v[2] = '';
            if (empty($v[3]) && isset($x['vocab_icons'][$v[0]])) $v[3] = $x['vocab_icons'][$v[0]];
        }
        unset($v);
        $u['readings'] = array_merge($u['readings'] ?? [], $x['readings'] ?? []);
        $u['exercises'] = array_merge($u['exercises'] ?? [], $x['exercises'] ?? []);
    }
    unset($u);
    return $lv;
}

function content_signature(): string
{
    $parts = [];
    foreach ([...content_files(), ...unit_files()] as $f) $parts[] = basename($f) . ':' . filesize($f) . ':' . filemtime($f);
    return md5(implode('|', $parts) . '|seed-v2');
}

function default_skill(string $type): string
{
    return match ($type) {
        'listen', 'dictation' => 'listening',
        'reading' => 'reading',
        'write' => 'writing',
        'speak' => 'speaking',
        'image', 'match' => 'vocabulary',
        default => 'grammar',
    };
}

function skill_labels(): array
{
    return [
        'listening' => ['Listening', 'Comprensión auditiva', '🎧'],
        'reading' => ['Reading', 'Comprensión lectora', '📖'],
        'writing' => ['Writing', 'Producción escrita', '✍️'],
        'speaking' => ['Speaking', 'Pronunciación y expresión oral', '🗣️'],
        'grammar' => ['Grammar', 'Gramática y uso', '🧩'],
        'vocabulary' => ['Vocabulary', 'Vocabulario', '🔤'],
    ];
}

function det_shuffle(array $items, string $seedText): array
{
    $orig = $items;
    $seed = crc32($seedText);
    for ($i = count($items) - 1; $i > 0; $i--) {
        $seed = ($seed * 1103515245 + 12345) & 0x7fffffff;
        $j = $seed % ($i + 1);
        [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
    }
    if ($items === $orig && count($items) > 1) $items = array_reverse($items);
    return $items;
}

/** Compacto → [type, prompt, data, answer, explanation, skill, hint]. */
function exercise_rows(array $ex, array $readings = []): array
{
    $type = $ex[0];
    $skill = $ex['skill'] ?? default_skill($type);
    $hint = (string) ($ex['hint'] ?? '');
    switch ($type) {
        case 'mcq':
            $r = [$ex[1], ['options' => $ex[2]], ['index' => $ex[3]], $ex[4] ?? '']; break;
        case 'fill':
            $r = [$ex[1], [], ['accept' => (array) $ex[2]], $ex[3] ?? '']; break;
        case 'order':
            $words = preg_split('/\s+/', trim($ex[2]));
            $r = [$ex[1], ['words' => det_shuffle($words, $ex[2])], ['accept' => [$ex[2]]], $ex[3] ?? '']; break;
        case 'listen':
            $r = [$ex[1], ['say' => $ex[2], 'options' => $ex[3]], ['index' => $ex[4]], $ex[5] ?? '']; break;
        case 'dictation':
            $r = [$ex[1], ['say' => $ex[2]], ['accept' => [$ex[2]]], $ex[3] ?? '']; break;
        case 'reading':
            $rd = $readings[$ex[1]] ?? null;
            if (!$rd) throw new InvalidArgumentException("Texto de lectura inexistente: {$ex[1]}");
            $r = [$ex[2], ['options' => $ex[3], 'passage' => $ex[1], 'title' => $rd['title'], 'text' => $rd['text'], 'kind' => $rd['kind'] ?? 'text'],
                ['index' => $ex[4]], $ex[5] ?? '']; break;
        case 'image':
            $r = [$ex['prompt'] ?? '¿Qué palabra corresponde a la imagen?', ['image' => $ex[1], 'options' => $ex[2]], ['index' => $ex[3]], $ex[4] ?? '']; break;
        case 'match':
            $left = array_column($ex[2], 0); $right = array_column($ex[2], 1);
            $r = [$ex[1], ['left' => $left, 'right' => det_shuffle($right, implode('|', $right))], ['pairs' => $right], $ex[3] ?? '']; break;
        case 'write':
            $spec = $ex[2];
            $r = [$ex[1], ['min' => (int) ($spec['min'] ?? 20), 'criteria' => array_column($spec['must'] ?? [], 1)],
                ['must' => $spec['must'] ?? [], 'min' => (int) ($spec['min'] ?? 20), 'model' => $spec['model'] ?? ''], $ex[3] ?? '']; break;
        case 'speak':
            $r = [$ex[1], ['say' => $ex[2]], ['accept' => [$ex[2]]], $ex[3] ?? '']; break;
        default:
            throw new InvalidArgumentException("Tipo de ejercicio desconocido: $type");
    }
    return [$type, $r[0], $r[1], $r[2], $r[3], $skill, $hint];
}

function unit_content_hash(array $u): string
{
    return md5(json_encode($u, JSON_UNESCAPED_UNICODE));
}

/** Inserta o actualiza en el lugar los ejercicios de una unidad, por posición. */
function sync_exercises(int $uid, array $list, array $readings): void
{
    $existing = [];
    foreach (q_all('SELECT * FROM exercises WHERE unit_id = ?', [$uid]) as $row) $existing[(int) $row['position']] = $row;
    foreach (array_values($list) as $i => $ex) {
        $pos = $i + 1;
        [$type, $prompt, $data, $answer, $expl, $skill, $hint] = exercise_rows($ex, $readings);
        $vals = [$type, $prompt, json_encode($data, JSON_UNESCAPED_UNICODE), json_encode($answer, JSON_UNESCAPED_UNICODE), $expl, $skill, $hint,
            json_encode($ex, JSON_UNESCAPED_UNICODE)];
        if (isset($existing[$pos])) {
            $row = $existing[$pos];
            $same = [$row['type'], $row['prompt'], $row['data_json'], $row['answer_json'], $row['explanation'], $row['skill'], $row['hint'], $row['compact_json']] === $vals;
            if (!$same) {
                q('UPDATE exercises SET type=?, prompt=?, data_json=?, answer_json=?, explanation=?, skill=?, hint=?, compact_json=? WHERE id=?',
                    [...$vals, $row['id']]);
                // Si cambió la respuesta, el estado anterior ya no es válido
                if ($row['answer_json'] !== $vals[3]) q('DELETE FROM exercise_status WHERE exercise_id = ?', [$row['id']]);
            }
            unset($existing[$pos]);
        } else {
            q('INSERT INTO exercises (unit_id, position, type, prompt, data_json, answer_json, explanation, skill, hint, compact_json) VALUES (?,?,?,?,?,?,?,?,?,?)',
                [$uid, $pos, ...$vals]);
        }
    }
    foreach ($existing as $row) { // el contenido se achicó
        q('DELETE FROM exercise_status WHERE exercise_id = ?', [$row['id']]);
        q('DELETE FROM exercises WHERE id = ?', [$row['id']]);
    }
}

/**
 * $force = true → también pisa unidades editadas desde el admin.
 * Devuelve contadores para el panel.
 */
function seed_content(bool $force): array
{
    $stats = ['levels' => 0, 'units_new' => 0, 'units_updated' => 0];
    $pdo = db();
    $pdo->beginTransaction();
    try {
        foreach (content_files() as $file) {
            $lv = load_level($file);
            if (q_val('SELECT code FROM levels WHERE code = ?', [$lv['code']])) {
                q('UPDATE levels SET name = ?, description = ?, position = ? WHERE code = ?',
                    [$lv['name'], $lv['description'], $lv['position'], $lv['code']]);
            } else {
                q('INSERT INTO levels (code, name, description, position) VALUES (?, ?, ?, ?)',
                    [$lv['code'], $lv['name'], $lv['description'], $lv['position']]);
            }
            $stats['levels']++;

            foreach ($lv['units'] as $pos => $u) {
                $hash = unit_content_hash($u);
                $existing = q_one('SELECT id, source_hash FROM units WHERE slug = ?', [$u['slug']]);
                if ($existing && $existing['source_hash'] === $hash) continue;
                if ($existing && $existing['source_hash'] === 'admin' && !$force) continue;

                $vocab = json_encode($u['vocab'] ?? [], JSON_UNESCAPED_UNICODE);
                $fields = [$lv['code'], $pos + 1, $u['title'], $u['summary'], $u['theory'], $vocab, (string) ($u['icon'] ?? ''), $hash, now_utc()];
                if ($existing) {
                    $uid = (int) $existing['id'];
                    q('UPDATE units SET level_code=?, position=?, title=?, summary=?, theory_html=?, vocab_json=?, icon=?, source_hash=?, updated_at=? WHERE id=?',
                        [...$fields, $uid]);
                    $stats['units_updated']++;
                } else {
                    q('INSERT INTO units (level_code, position, title, summary, theory_html, vocab_json, icon, source_hash, updated_at, slug) VALUES (?,?,?,?,?,?,?,?,?,?)',
                        [...$fields, $u['slug']]);
                    $uid = (int) $pdo->lastInsertId();
                    $stats['units_new']++;
                }
                sync_exercises($uid, $u['exercises'], $u['readings'] ?? []);
            }
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    return $stats;
}
