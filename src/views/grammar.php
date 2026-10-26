<link rel="stylesheet" href="/assets/grammar.css?v=2">
<section class="wrap page grammar-page">
 <p class="crumbs"><a href="/dashboard.php">Mis niveles</a> / <a href="/level.php?code=<?= e($unit['level_code']) ?>"><?= e(strtoupper($unit['level_code'])) ?></a> / Cuaderno de gramática</p>
 <header class="grammar-heading"><p class="eyebrow">MYTEACHER · COMPRENDER Y PRACTICAR</p><h1><?= e($unit['title']) ?></h1><p>Leé primero la teoría completa y luego resolvé los ejercicios que aparecen debajo. Podés volver a consultar cualquier regla cuando lo necesites.</p></header>
 <form method="get" class="grammar-selector"><label>Elegí un tema<select name="id"><?php foreach ($units as $item): ?><option value="<?= (int)$item['id'] ?>" <?= $item['id']===$unit['id']?'selected':'' ?>><?= e(strtoupper($item['level_code']).' · '.$item['title']) ?></option><?php endforeach; ?></select></label><button class="btn btn-sm">Abrir tema</button><a href="/book.php?code=<?= e($unit['level_code']) ?>&amp;chapter=<?= e($unit['slug']) ?>">Leer el capítulo completo →</a></form>
 <div class="grammar-spread">
  <aside class="grammar-reference" aria-label="Explicación y ejemplos">
   <div class="grammar-label">A · ESTUDIÁ EL CONCEPTO</div>
   <?php if ($chapter): ?><h2>Qué vas a poder expresar</h2><p><?= e($chapter['goal']) ?></p><p><?= e($chapter['explain']) ?></p><h3>La diferencia que importa</h3><p><?= e($chapter['contrast']) ?></p><?php endif; ?>
   <?php if ($visual): ?><h3>Por qué funciona</h3><p><?= e($visual['foundation']) ?></p><div class="grammar-models"><?php foreach ($visual['steps'] as [$sentence,$label,$reason]): ?><section><h4><?= e($label) ?></h4><p lang="en"><?= e($sentence) ?></p><p><?= e($reason) ?></p></section><?php endforeach; ?></div><?php endif; ?>
   <section class="theory grammar-full-theory" aria-label="Reglas y tablas del capítulo"><h3>Reglas, ejemplos y tablas del capítulo</h3><?= $unit['theory_html'] ?></section>
  </aside>
  <article class="grammar-practice" aria-label="Ejercicios de gramática">
   <div class="grammar-label">B · PONÉ LA REGLA A PRUEBA</div>
   <p class="grammar-progress"><?= (int)$solved ?> de <?= count($exercises) ?> ejercicios de gramática resueltos al abrir el cuaderno. Los aciertos de esta sección se guardan y se comparten con la unidad.</p>
   <?php $sectionNumber=0; $headings=['fill'=>['Completá las oraciones','Escribí la forma que falta. Prestá atención al sujeto, al tiempo y al contexto.'],'mcq'=>['Elegí la forma adecuada','Leé la oración completa antes de elegir. Después comprobá el porqué de la respuesta.'],'order'=>['Construí la oración','Ordená las palabras y revisá la posición del auxiliar, el sujeto y el verbo.']]; foreach (['fill','mcq','order',...array_diff(array_keys($groups),['fill','mcq','order'])] as $kind): if(empty($groups[$kind])) continue; $heading=$headings[$kind]??['Aplicá lo aprendido','Resolvé y consultá la explicación.']; ?>
   <section class="grammar-task"><h2><?= ++$sectionNumber ?>. <?= e($heading[0]) ?></h2><p><?= e($heading[1]) ?></p><?php $exList=$groups[$kind];$groupBySkill=false;require __DIR__.'/_exercise.php'; ?></section>
   <?php endforeach; ?>
   <?php if ($chapter): ?><section class="grammar-task"><h2><?= ++$sectionNumber ?>. Transformá, corregí y justificá</h2><p>Respondé antes de desplegar el solucionario. Estas respuestas abiertas son de autoestudio y no suman puntos.</p>
   <?php foreach ($chapter['practice'] as $i=>[$prompt,$answer,$reason]): ?><div class="grammar-open"><label><span><?= $i+1 ?>. <?= e($prompt) ?></span><textarea rows="2" lang="en" spellcheck="false" placeholder="Escribí tu respuesta…"></textarea></label><details><summary>Solución y explicación</summary><p lang="en"><?= e($answer) ?></p><p><?= e($reason) ?></p></details></div><?php endforeach; ?>
   <p class="fine">Compará el sentido y la estructura; puede haber otras respuestas válidas. Los borradores no se guardan: copialos antes de salir.</p></section><?php endif; ?>
   <div class="grammar-return"><a class="btn" href="/unit.php?id=<?= (int)$unit['id'] ?>#t-practice">Seguir con todas las habilidades →</a><p>Retomá vocabulario, comprensión, escritura y expresión oral en la práctica de la unidad.</p></div>
  </article>
 </div>
</section>
