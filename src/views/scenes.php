<?php foreach (scenes() as $scene): if ($scene['unit'] !== $unit['slug']) continue;
$states = scene_progress((int)$u['id'],$scene['id']);
$done = array_sum(array_column($states,'solved'));
$solvedIds = array_column(array_filter($states,fn($s)=>(int)$s['solved']===1),'question_id'); ?>
<article class="scene" data-scene="<?= e($scene['id']) ?>">
  <header class="scene-heading"><p><?= e($scene['level']) ?> · REAL LIFE ENGLISH</p><h2><?= e($scene['title']) ?></h2><p>Escuchá, seguí el diálogo y practicá con la misma situación.</p></header>
  <div class="scene-workspace">
  <aside class="scene-reference" aria-label="Diálogo de referencia" tabindex="0">
    <?php if (!empty($scene['visual'])): ?><figure class="scene-visual-placeholder" role="img" aria-label="<?= e($scene['alt']) ?>"><div style="min-height:170px;display:grid;place-items:center;font-size:5rem;background:#e7eff8;border-radius:12px" aria-hidden="true"><?= e($scene['visual']) ?></div><figcaption>Escenario didáctico · Diálogo original.</figcaption></figure><?php else: ?><figure><img src="/assets/images/scene-<?= e($scene['id']) ?>.jpg" alt="<?= e($scene['alt']) ?>" loading="lazy" width="1000" height="667"><figcaption>Fotografía de contexto · Unsplash. Diálogo didáctico original.</figcaption></figure><?php endif; ?>
    <div><div class="scene-controls"><button type="button" class="btn" data-scene-play>▶ Escuchar diálogo</button><button type="button" class="btn btn-ghost" data-scene-stop>Detener</button><label>Velocidad <select data-scene-rate><option value="0.9">Normal</option><option value="0.65">Lenta</option></select></label></div>
    <p class="fine" data-audio-status aria-live="polite">Audio en inglés con la voz de tu dispositivo.</p>
    <label><input type="checkbox" data-scene-translation> Mostrar traducción al español</label>
    <ol class="scene-transcript" lang="en">
    <?php foreach ($scene['lines'] as $line): ?><li data-line><strong><?= e($line['speaker']) ?></strong> <span data-en><?= e($line['en']) ?></span><p lang="es" data-es hidden><?= e($line['es']) ?></p><button type="button" class="speak linkbtn" data-say="<?= e($line['en']) ?>" aria-label="Escuchar esta frase">🔊</button></li><?php endforeach; ?>
    </ol></div>
  </aside>
  <div class="scene-practice" role="region" aria-label="Ejercicios de la escena" tabindex="0">
  <h3 class="scene-practice-title">Practicá con el diálogo</h3>
  <p class="scene-practice-hint">Avanzá con los ejercicios; la conversación queda a tu izquierda.</p>
  <p class="scene-score" aria-live="polite">Dominio de la escena: <strong data-scene-score><?= (int)$done ?>/5 · <?= (int)$done*20 ?>/100 puntos</strong></p>
  <progress data-scene-progress max="5" value="<?= (int)$done ?>" aria-label="Ejercicios multimedia resueltos"></progress>
  <p class="fine">Cada ejercicio resuelto suma 20 puntos una sola vez. Podés reintentar; tus aciertos se guardan en tu cuenta.</p>
  <div class="scene-questions">
  <?php foreach ($scene['questions'] as $i=>$question): ?><form data-scene-question="<?= $i ?>" class="scene-question">
    <h3><?= $i+1 ?>. <?= e($question['prompt']) ?></h3>
    <?php if ($question['type']==='mcq'): ?><fieldset><legend class="fine">Elegí una respuesta</legend><?php foreach ($question['options'] as $k=>$option): ?><label><input type="radio" name="answer" value="<?= $k ?>" required> <?= e($option) ?></label><?php endforeach; ?></fieldset>
    <?php elseif ($question['type']==='order'): ?><div data-built aria-live="polite"></div><div data-pool><?php foreach ($question['words'] as $word): ?><button type="button" class="btn btn-ghost btn-sm" data-word><?= e($word) ?></button><?php endforeach; ?></div><p class="fine">Tocá las palabras para ordenarlas; tocá una palabra elegida para devolverla.</p>
    <?php else: ?><label>Palabra que falta <input name="answer" required maxlength="100" autocomplete="off"></label><?php endif; ?>
    <button class="btn btn-sm" type="submit">Comprobar</button><p data-result aria-live="polite"><?= in_array($i,$solvedIds) ? '✓ Ya resuelto · podés repasar.' : '' ?></p>
  </form><?php endforeach; ?></div>
  </div>
  </div>
</article>
<?php endforeach; ?>
