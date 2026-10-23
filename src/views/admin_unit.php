<section class="wrap page">
  <h1><?= $id ? 'Editar unidad' : 'Nueva unidad' ?></h1>
  <?php require __DIR__ . '/admin_nav.php'; ?>
  <?php if ($errors): ?><div class="flash flash-error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>

  <form method="post" class="form">
    <?= csrf_field() ?>
    <div class="grid-2">
      <label>Nivel
        <select name="level_code">
          <?php foreach ($levels as $l): ?><option value="<?= e($l['code']) ?>" <?= $form['level_code'] === $l['code'] ? 'selected' : '' ?>><?= e(strtoupper($l['code']) . ' ' . $l['name']) ?></option><?php endforeach; ?>
        </select></label>
      <label>Posición en el nivel <small>La posición 1 es la que se ve en la demo.</small>
        <input type="number" name="position" min="1" value="<?= (int) $form['position'] ?>"></label>
    </div>
    <label>Título<input name="title" value="<?= e($form['title']) ?>" required></label>
    <label>Identificador (slug) <small>Único, en minúsculas y con guiones. Ej.: b1-reported-questions</small>
      <input name="slug" value="<?= e($form['slug']) ?>" required></label>
    <label>Resumen<input name="summary" value="<?= e($form['summary']) ?>"></label>
    <label>Teoría (HTML)
      <small>Usá &lt;h2&gt;, &lt;p&gt;, &lt;table class="tbl"&gt;, &lt;div class="tip"&gt;, &lt;div class="warn"&gt; y ejemplos con audio: &lt;p class="ex"&gt;Frase en inglés &lt;span class="es"&gt;traducción&lt;/span&gt;&lt;/p&gt;</small>
      <textarea name="theory_html" class="code"><?= e($form['theory_html']) ?></textarea></label>
    <label>Vocabulario (JSON) <small>Lista de pares: [["word", "traducción"], …]</small>
      <textarea name="vocab_json" class="code" style="min-height:140px"><?= e($form['vocab_json']) ?></textarea></label>
    <label>Ejercicios (JSON)
      <small>
        ["mcq", "consigna", ["op1","op2","op3"], índiceCorrecto, "explicación"] ·
        ["fill", "texto con ___", ["respuesta", "alternativa"], "explicación"] ·
        ["order", "consigna", "oración correcta", "explicación"] ·
        ["listen", "consigna", "texto que se escucha", ["op1","op2"], índice, "explicación"] ·
        ["dictation", "consigna", "texto que se escucha", "explicación"]. El índice empieza en 0.
        Si cambiás los ejercicios se reinicia el progreso de esta unidad.
      </small>
      <textarea name="exercises_json" class="code"><?= e($exJson) ?></textarea></label>
    <label class="check"><input type="checkbox" name="is_published" value="1" <?= $form['is_published'] ? 'checked' : '' ?>> Publicada (visible para los alumnos)</label>
    <p><button class="btn">Guardar unidad</button> <a class="btn btn-ghost" href="/admin/units.php">Volver</a></p>
  </form>
</section>
