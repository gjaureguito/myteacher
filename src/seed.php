<?php
declare(strict_types=1);

/*
 * Carga el contenido de content/*.php en la base.
 * Formato compacto de ejercicios (ver content/a1.php):
 *   ['mcq',       consigna, [opciones], índiceCorrecto, explicación]
 *   ['fill',      consigna con ___, [respuestas aceptadas], explicación]
 *   ['order',     consigna, 'oración correcta', explicación]          → palabras mezcladas
 *   ['listen',    consigna, 'texto que se escucha', [opciones], índice, explicación]
 *   ['dictation', consigna, 'texto que se escucha', explicación]       → escribir lo que se oye
 *
 * $force = true → actualiza unidades existentes (pisa ediciones hechas desde el admin).
 */

function content_files(): array
{
    $files = glob(ROOT . '/content/[a-c][1-2].php'); // a1.php … c2.php (se ignoran los que empiezan con _)
    sort($files);
    return $files;
}

function exercise_rows(array $ex): array
{
    $type = $ex[0];
    switch ($type) {
        case 'mcq':
            return [$type, $ex[1], ['options' => $ex[2]], ['index' => $ex[3]], $ex[4] ?? ''];
        case 'fill':
            return [$type, $ex[1], [], ['accept' => (array) $ex[2]], $ex[3] ?? ''];
        case 'order':
            $words = preg_split('/\s+/', trim($ex[2]));
            $shuffled = $words;
            // Mezcla determinística que nunca deja el orden original
            $seed = crc32($ex[2]);
            for ($i = count($shuffled) - 1; $i > 0; $i--) {
                $seed = ($seed * 1103515245 + 12345) & 0x7fffffff;
                $j = $seed % ($i + 1);
                [$shuffled[$i], $shuffled[$j]] = [$shuffled[$j], $shuffled[$i]];
            }
            if ($shuffled === $words && count($words) > 1) $shuffled = array_reverse($words);
            return [$type, $ex[1], ['words' => $shuffled], ['accept' => [$ex[2]]], $ex[3] ?? ''];
        case 'listen':
            return [$type, $ex[1], ['say' => $ex[2], 'options' => $ex[3]], ['index' => $ex[4]], $ex[5] ?? ''];
        case 'dictation':
            return [$type, $ex[1], ['say' => $ex[2]], ['accept' => [$ex[2]]], $ex[3] ?? ''];
    }
    throw new InvalidArgumentException("Tipo de ejercicio desconocido: $type");
}

function seed_content(bool $force): array
{
    $stats = ['levels' => 0, 'units_new' => 0, 'units_updated' => 0];
    $pdo = db();
    $pdo->beginTransaction();
    try {
        foreach (content_files() as $file) {
            $lv = require $file;
            if (q_val('SELECT code FROM levels WHERE code = ?', [$lv['code']])) {
                q('UPDATE levels SET name = ?, description = ?, position = ? WHERE code = ?',
                    [$lv['name'], $lv['description'], $lv['position'], $lv['code']]);
            } else {
                q('INSERT INTO levels (code, name, description, position) VALUES (?, ?, ?, ?)',
                    [$lv['code'], $lv['name'], $lv['description'], $lv['position']]);
            }
            $stats['levels']++;

            foreach ($lv['units'] as $pos => $u) {
                $existing = q_one('SELECT id FROM units WHERE slug = ?', [$u['slug']]);
                if ($existing && !$force) continue;

                $vocab = json_encode($u['vocab'] ?? [], JSON_UNESCAPED_UNICODE);
                if ($existing) {
                    $uid = (int) $existing['id'];
                    q('UPDATE units SET level_code=?, position=?, title=?, summary=?, theory_html=?, vocab_json=?, updated_at=? WHERE id=?',
                        [$lv['code'], $pos + 1, $u['title'], $u['summary'], $u['theory'], $vocab, now_utc(), $uid]);
                    // Se reemplazan los ejercicios; el progreso de ejercicios borrados se pierde
                    q('DELETE FROM exercise_status WHERE exercise_id IN (SELECT id FROM exercises WHERE unit_id = ?)', [$uid]);
                    q('DELETE FROM exercises WHERE unit_id = ?', [$uid]);
                    $stats['units_updated']++;
                } else {
                    q('INSERT INTO units (level_code, slug, position, title, summary, theory_html, vocab_json, updated_at) VALUES (?,?,?,?,?,?,?,?)',
                        [$lv['code'], $u['slug'], $pos + 1, $u['title'], $u['summary'], $u['theory'], $vocab, now_utc()]);
                    $uid = (int) $pdo->lastInsertId();
                    $stats['units_new']++;
                }

                foreach ($u['exercises'] as $i => $ex) {
                    [$type, $prompt, $data, $answer, $expl] = exercise_rows($ex);
                    q('INSERT INTO exercises (unit_id, position, type, prompt, data_json, answer_json, explanation) VALUES (?,?,?,?,?,?,?)',
                        [$uid, $i + 1, $type, $prompt, json_encode($data, JSON_UNESCAPED_UNICODE), json_encode($answer, JSON_UNESCAPED_UNICODE), $expl]);
                }
            }
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    return $stats;
}
