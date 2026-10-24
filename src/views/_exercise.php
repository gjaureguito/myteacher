<?php
/**
 * Renderiza una lista de ejercicios (unidad o cuaderno de errores).
 * Espera $exList: filas con id, type, prompt, data_json, skill, solved (y opcionalmente unit_title/unit_id).
 * $groupBySkill: muestra encabezados de sección cuando cambia la habilidad.
 */
$labels = skill_labels();
$prevPassage = null;
$prevSkill = null;
$n = 0;
?>
<ol class="exercises">
<?php foreach ($exList as $ex):
  $data = json_decode($ex['data_json'], true) ?: [];
  $type = $ex['type'];
  $skill = $ex['skill'] ?? default_skill($type);
  [$skEn, $skEs, $skIco] = $labels[$skill] ?? $labels['grammar'];
  $n++;
?>
  <?php if (!empty($groupBySkill) && $skill !== $prevSkill): $prevSkill = $skill; ?>
    <li class="ex-section" data-skill="<?= e($skill) ?>"><span class="ex-section-ico" aria-hidden="true"><?= $skIco ?></span><div><strong lang="en"><?= e($skEn) ?></strong><span><?= e($skEs) ?></span></div></li>
  <?php endif; ?>

  <?php if (!empty($ex['unit_title'])): ?>
    <li class="ex-origin"><a href="/unit.php?id=<?= (int) $ex['unit_id'] ?>#t-practice"><?= e(strtoupper($ex['level_code'] ?? '')) ?> · <?= e($ex['unit_title']) ?></a></li>
  <?php endif; ?>

  <?php if ($type === 'reading' && ($data['passage'] ?? '') !== $prevPassage): $prevPassage = $data['passage']; ?>
    <li class="passage-card">
      <div class="passage-head">
        <span class="passage-kind"><?= e(match ($data['kind'] ?? 'text') { 'email' => '✉️ Email', 'chat' => '💬 Chat', 'ad' => '📰 Aviso', 'article' => '📰 Artículo', 'story' => '📚 Relato', default => '📖 Texto' }) ?></span>
        <button type="button" class="btn btn-ghost btn-sm speak" data-say="<?= e($data['title'] . '. ' . preg_replace('/\s+/', ' ', $data['text'])) ?>" data-rate="0.9">🔊 Escuchar el texto</button>
      </div>
      <h3 lang="en"><?= e($data['title']) ?></h3>
      <div class="passage-text" lang="en">
        <?php foreach (preg_split('/\n\s*\n/', trim($data['text'])) as $para): ?><p><?= nl2br(e(trim($para))) ?></p><?php endforeach; ?>
      </div>
    </li>
  <?php endif; ?>

  <li class="ex<?= $ex['solved'] ? ' is-solved' : '' ?>" data-id="<?= (int) $ex['id'] ?>" data-type="<?= e($type) ?>" data-skill="<?= e($skill) ?>"<?php if ($ex['solved'] && isset($ex['answer_json'])): ?> data-done="<?= e(json_encode(done_payload($ex), JSON_UNESCAPED_UNICODE)) ?>"<?php endif; ?>>
    <div class="ex-top">
      <span class="ex-n"><?= $n ?></span>
      <span class="skill-badge skill-<?= e($skill) ?>"><?= $skIco ?> <?= e($skEn) ?></span>
      <span class="done-badge" hidden>✓ Completado</span>
      <button type="button" class="linkbtn redo" hidden>Repetir</button>
      <button type="button" class="linkbtn show-solution">Ver solución</button>
    </div>
    <p class="ex-prompt">
      <?php if ($type === 'fill'): ?>
        <?= preg_replace('/_{3,}/', '<input class="gap" aria-label="Completar" autocomplete="off" autocapitalize="off" spellcheck="false">', e($ex['prompt']), 1) ?>
      <?php else: ?>
        <?= e($ex['prompt']) ?>
      <?php endif; ?>
    </p>

    <?php if (in_array($type, ['listen', 'dictation', 'speak'], true)): ?>
      <p class="ex-audio"><button class="btn btn-ghost btn-sm speak" data-say="<?= e($data['say']) ?>">🔊 Escuchar</button>
         <button class="btn btn-ghost btn-sm speak" data-say="<?= e($data['say']) ?>" data-rate="0.65">🐢 Más lento</button></p>
    <?php endif; ?>

    <?php if ($type === 'image'): ?>
      <div class="ex-image" aria-hidden="true"><?= e($data['image']) ?></div>
    <?php endif; ?>

    <?php if (in_array($type, ['mcq', 'listen', 'reading', 'image'], true)): ?>
      <div class="options">
        <?php foreach ($data['options'] as $k => $opt): ?>
          <button class="opt" data-value="<?= $k ?>"><?= e($opt) ?></button>
        <?php endforeach; ?>
      </div>
    <?php elseif ($type === 'order'): ?>
      <div class="built" aria-live="polite"></div>
      <div class="chips">
        <?php foreach ($data['words'] as $w): ?><button class="chip"><?= e($w) ?></button><?php endforeach; ?>
      </div>
      <p><button class="btn btn-sm check">Comprobar</button> <button class="linkbtn reset">Reiniciar</button></p>
    <?php elseif ($type === 'dictation'): ?>
      <p><input class="dict" placeholder="Escribí lo que escuchás" autocomplete="off" autocapitalize="off" spellcheck="false"></p>
      <p><button class="btn btn-sm check">Comprobar</button></p>
    <?php elseif ($type === 'fill'): ?>
      <p><button class="btn btn-sm check">Comprobar</button></p>
    <?php elseif ($type === 'match'): ?>
      <div class="match" data-n="<?= count($data['left']) ?>">
        <div class="match-col match-left">
          <?php foreach ($data['left'] as $i => $l): ?><button type="button" class="match-item" data-side="l" data-i="<?= $i ?>" lang="en"><?= e($l) ?></button><?php endforeach; ?>
        </div>
        <div class="match-col match-right">
          <?php foreach ($data['right'] as $i => $r): ?><button type="button" class="match-item" data-side="r" data-text="<?= e($r) ?>"><?= e($r) ?></button><?php endforeach; ?>
        </div>
      </div>
      <p class="fine">Tocá un elemento de la izquierda y después su pareja de la derecha. Tocá un par para deshacerlo.</p>
      <p><button class="btn btn-sm check">Comprobar</button> <button class="linkbtn reset">Reiniciar</button></p>
    <?php elseif ($type === 'write'): ?>
      <textarea class="write-box" rows="6" lang="en" spellcheck="true" placeholder="Write here in English…"></textarea>
      <div class="write-meta"><span class="wc"><b>0</b> / <?= (int) $data['min'] ?> palabras</span></div>
      <ul class="criteria">
        <li data-c="0">Al menos <?= (int) $data['min'] ?> palabras</li>
        <?php foreach ($data['criteria'] ?? [] as $i => $c): ?><li data-c="<?= $i + 1 ?>"><?= e($c) ?></li><?php endforeach; ?>
      </ul>
      <p><button class="btn btn-sm check">Corregir mi texto</button></p>
    <?php elseif ($type === 'speak'): ?>
      <p class="say-target" lang="en">“<?= e($data['say']) ?>”</p>
      <p><button type="button" class="btn btn-sm record">🎙️ Grabar mi voz</button> <button type="button" class="linkbtn self-check" hidden>Lo dije en voz alta</button></p>
    <?php endif; ?>
    <div class="feedback" aria-live="polite"></div>
    <details class="exercise-solution"><summary>Ver solución explicada</summary><div class="solution-content" aria-live="polite">La solución se consulta al abrir esta sección.</div><p class="fine">Consultar no suma aciertos ni intentos.</p></details>
  </li>
<?php endforeach; ?>
</ol>
