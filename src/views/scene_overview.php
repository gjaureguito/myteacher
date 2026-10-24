<section class="scene-overview" aria-labelledby="scene-overview-title">
<h2 id="scene-overview-title">Tu inglés en situaciones reales</h2>
<a class="mt-text-link" href="/situations.php">Explorar todas las situaciones ↗</a>
<p class="muted">Tu progreso multimedia, guardado en tu cuenta. Cada escena tiene cinco actividades y hasta 100 puntos.</p>
<div class="scene-overview-grid">
<?php foreach (scenes() as $scene):
$sceneUnit = q_one('SELECT * FROM units WHERE slug = ? AND is_published = 1',[$scene['unit']]);
if (!$sceneUnit) continue;
$sceneDone = (int)array_sum(array_column(scene_progress((int)$u['id'],$scene['id']),'solved')); ?>
<a class="scene-overview-card" href="/unit.php?id=<?= (int)$sceneUnit['id'] ?>#t-practice"><span><?= e($scene['level']) ?></span><h3><?= e($scene['title']) ?></h3><p><?= $sceneDone ?>/5 resueltos · <?= $sceneDone*20 ?>/100 puntos</p><progress max="5" value="<?= $sceneDone ?>" aria-label="Progreso <?= e($scene['title']) ?>"></progress><p class="fine"><?= can_access_unit($u,$sceneUnit) ? 'Abrir lección →' : 'Requiere suscripción' ?></p></a>
<?php endforeach; ?>
</div></section>
