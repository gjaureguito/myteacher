<?php
require dirname(__DIR__, 2) . '/src/bootstrap.php';
require_admin();
require_once ROOT . '/src/seed.php';

/** Fila de la base → formato compacto editable (el mismo de content/*.php). */
function exercise_to_compact(array $ex): array
{
    if (!empty($ex['compact_json'])) return json_decode($ex['compact_json'], true) ?: [];
    $d = json_decode($ex['data_json'], true) ?: [];
    $a = json_decode($ex['answer_json'], true) ?: [];
    switch ($ex['type']) {
        case 'mcq': return ['mcq', $ex['prompt'], $d['options'], $a['index'], $ex['explanation']];
        case 'fill': return ['fill', $ex['prompt'], $a['accept'], $ex['explanation']];
        case 'order': return ['order', $ex['prompt'], $a['accept'][0], $ex['explanation']];
        case 'listen': return ['listen', $ex['prompt'], $d['say'], $d['options'], $a['index'], $ex['explanation']];
        case 'dictation': return ['dictation', $ex['prompt'], $d['say'], $ex['explanation']];
    }
    return [];
}

/** Textos de lectura usados por los ejercicios de la unidad (para poder reeditarlos). */
function unit_readings(array $rows): array
{
    $out = [];
    foreach ($rows as $r) {
        $d = json_decode($r['data_json'], true) ?: [];
        if (!empty($d['passage'])) $out[$d['passage']] = ['title' => $d['title'] ?? '', 'text' => $d['text'] ?? '', 'kind' => $d['kind'] ?? 'text'];
    }
    return $out;
}

$id = (int) ($_GET['id'] ?? 0);
$unit = $id ? q_one('SELECT * FROM units WHERE id = ?', [$id]) : null;
if ($id && !$unit) redirect('/admin/units.php');
$levels = q_all('SELECT code, name FROM levels ORDER BY position');
$errors = [];

$exRows = $unit ? q_all('SELECT * FROM exercises WHERE unit_id = ? ORDER BY position', [$id]) : [];
$exJson = json_encode(array_map('exercise_to_compact', $exRows), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$form = $unit ?? ['level_code' => 'a1', 'slug' => '', 'position' => 1, 'title' => '', 'summary' => '', 'theory_html' => '', 'vocab_json' => '[]', 'is_published' => 0];
$form['vocab_json'] = json_encode(json_decode($form['vocab_json'], true), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

if (is_post()) {
    csrf_check();
    $form = [
        'level_code' => post_str('level_code', 4),
        'slug' => preg_replace('/[^a-z0-9-]/', '', strtolower(post_str('slug', 120))),
        'position' => max(1, (int) ($_POST['position'] ?? 1)),
        'title' => post_str('title', 160),
        'summary' => post_str('summary', 500),
        'theory_html' => (string) ($_POST['theory_html'] ?? ''),
        'vocab_json' => (string) ($_POST['vocab_json'] ?? '[]'),
        'is_published' => empty($_POST['is_published']) ? 0 : 1,
    ];
    $newExJson = (string) ($_POST['exercises_json'] ?? '[]');

    if (!q_val('SELECT code FROM levels WHERE code = ?', [$form['level_code']])) $errors[] = 'Nivel inválido.';
    if ($form['slug'] === '') $errors[] = 'El identificador (slug) es obligatorio.';
    if ($form['title'] === '') $errors[] = 'El título es obligatorio.';
    if (q_val('SELECT id FROM units WHERE slug = ? AND id <> ?', [$form['slug'], $id])) $errors[] = 'Ya existe otra unidad con ese slug.';
    $vocab = json_decode($form['vocab_json'], true);
    if (!is_array($vocab)) $errors[] = 'El vocabulario no es JSON válido.';
    $exList = json_decode($newExJson, true);
    $rows = [];
    if (!is_array($exList)) {
        $errors[] = 'Los ejercicios no son JSON válido.';
    } else {
        foreach ($exList as $i => $ex) {
            try { $rows[] = exercise_rows($ex, unit_readings($exRows)); }
            catch (Throwable $e) { $errors[] = 'Ejercicio ' . ($i + 1) . ': formato incorrecto.'; }
        }
    }

    if (!$errors) {
        $vj = json_encode($vocab, JSON_UNESCAPED_UNICODE);
        if ($unit) {
            q('UPDATE units SET level_code=?, slug=?, position=?, title=?, summary=?, theory_html=?, vocab_json=?, is_published=?, source_hash=\'admin\', updated_at=? WHERE id=?',
                [$form['level_code'], $form['slug'], $form['position'], $form['title'], $form['summary'], $form['theory_html'], $vj, $form['is_published'], now_utc(), $id]);
        } else {
            q("INSERT INTO units (level_code, slug, position, title, summary, theory_html, vocab_json, is_published, source_hash, updated_at) VALUES (?,?,?,?,?,?,?,?,'admin',?)",
                [$form['level_code'], $form['slug'], $form['position'], $form['title'], $form['summary'], $form['theory_html'], $vj, $form['is_published'], now_utc()]);
            $id = (int) db()->lastInsertId();
        }
        // Actualización en el lugar por posición: se conserva el progreso de los ejercicios que no cambiaron
        if (json_encode($exList) !== json_encode(json_decode($exJson, true))) {
            sync_exercises($id, $exList, unit_readings($exRows));
        }
        flash('ok', 'Unidad guardada.');
        redirect('/admin/unit.php?id=' . $id);
    }
    $exJson = $newExJson;
}

render('admin_unit', compact('form', 'levels', 'errors', 'exJson', 'id'), $id ? 'Editar unidad' : 'Nueva unidad');
