<?php
$tracks = learning_tracks();
$liveScenes = [];
foreach (scenes() as $item) {
    $su = q_one('SELECT id FROM units WHERE slug=? AND is_published=1', [$item['unit']]);
    if ($su) $liveScenes[] = $item + ['unit_id' => (int) $su['id']];
}
$sceneCount = count($liveScenes);
$sceneActivities = array_sum(array_map(fn($s) => count($s['questions']), $liveScenes));

// En la portada mostramos dos situaciones por categoría principal; el catálogo completo vive en /situations.php.
$featuredScenes = [];
$perCategory = [];
foreach ($liveScenes as $item) {
    $cat = $item['category'] ?? 'Vida diaria';
    if (!in_array($cat, ['Vida diaria', 'Viajes', 'Trabajo'], true)) continue;
    if (($perCategory[$cat] ?? 0) >= 2) continue;
    $perCategory[$cat] = ($perCategory[$cat] ?? 0) + 1;
    $featuredScenes[] = $item;
}

$user = current_user();
$startUrl = $user ? '/dashboard.php' : '/register.php';
$startLabel = $user ? 'Ir a mis niveles' : 'Empezar gratis';
?>
<div class="mh">

<section class="mh-hero" aria-labelledby="mh-title">
  <div class="wrap mh-hero-grid">
    <div class="mh-hero-copy">
      <p class="mh-eyebrow">Inglés para la vida real · A1 a C2</p>
      <h1 id="mh-title">Aprendé el inglés que <span class="mh-hl">vas a usar</span>.</h1>
      <p class="mh-lead">Teoría clara en castellano, situaciones reales con audio y ejercicios que te explican por qué te equivocaste. Todo en un mismo lugar, a tu ritmo.</p>
      <div class="mh-actions">
        <a class="mh-btn mh-btn-primary" href="<?= $startUrl ?>"><?= $startLabel ?> <span aria-hidden="true">→</span></a>
        <a class="mh-btn mh-btn-ghost" href="#como-funciona">Ver cómo funciona</a>
      </div>
      <p class="mh-note">Cuenta gratuita sin tarjeta · Desde el celular o la computadora</p>
    </div>

    <div class="mh-practice" data-mh-practice>
      <div class="mh-practice-top">
        <span class="mh-chip" data-mh-level>Práctica · A1</span>
        <span class="mh-dots" aria-hidden="true"><i class="on"></i><i></i><i></i></span>
      </div>
      <p class="mh-sentence" lang="en" data-mh-sentence>My brother <span class="mh-blank">___</span> to work by bus.</p>
      <div class="mh-options" data-mh-options role="group" aria-label="Opciones de respuesta">
        <button type="button" class="mh-opt">go</button><button type="button" class="mh-opt">goes</button><button type="button" class="mh-opt">going</button>
      </div>
      <div class="mh-feedback" data-mh-feedback aria-live="polite"><strong>Elegí una respuesta.</strong> Así se practica en MyTeacher: respondés y ves la explicación.</div>
      <div class="mh-practice-foot">
        <span>Probalo acá, sin registrarte</span>
        <button type="button" class="mh-next" data-mh-next hidden>Siguiente pregunta →</button>
      </div>
    </div>
  </div>
</section>

<section class="mh-stats" aria-label="MyTeacher en números">
  <div class="wrap mh-stats-row">
    <p><strong><?= (int) $counts['units'] ?></strong><span>unidades publicadas</span></p>
    <p><strong><?= number_format((int) $counts['exercises'], 0, ',', '.') ?></strong><span>ejercicios con corrección</span></p>
    <p><strong><?= $sceneCount ?></strong><span>situaciones con audio</span></p>
    <p><strong><?= count($levels) ?></strong><span>niveles, de A1 a C2</span></p>
  </div>
</section>

<section class="mh-band" id="como-funciona" aria-labelledby="mh-how">
  <div class="wrap">
    <div class="mh-head">
      <p class="mh-eyebrow">Cómo funciona</p>
      <h2 id="mh-how">De «lo entiendo» a «lo puedo usar».</h2>
      <p class="mh-lead">No necesitás saber inglés para dar el primer paso. Trabajás una habilidad a la vez, con explicaciones en castellano y práctica en inglés.</p>
    </div>
    <ol class="mh-steps">
      <li><span class="mh-step-n">Paso 1</span><h3>Encontrá tu punto de partida</h3><p>Creá tu cuenta gratis y empezá desde A1 o hacé el test orientativo.</p></li>
      <li><span class="mh-step-n">Paso 2</span><h3>Practicá con una situación</h3><p>Leé el diálogo, escuchá el audio y consultá la traducción cuando la necesites.</p></li>
      <li><span class="mh-step-n">Paso 3</span><h3>Comprobá y seguí avanzando</h3><p>Resolvé, revisá la explicación y retomá tu progreso donde lo dejaste.</p></li>
    </ol>
    <div class="mh-features">
      <article><span class="mh-tag">Listening + speaking</span><h3>Escuchá. Repetí. Ganá confianza.</h3><p>Audio a dos velocidades, transcripción por frase y práctica de pronunciación en navegadores compatibles.</p></article>
      <article><span class="mh-tag">Corrección explicada</span><h3>Entendé el error y probá otra vez.</h3><p>Cada respuesta muestra por qué es correcta. Tus aciertos y tu progreso quedan guardados en tu cuenta.</p></article>
      <article><span class="mh-tag">Libros de estudio</span><h3>Teoría que podés seguir.</h3><p>Explicaciones en castellano, ejemplos paso a paso y soluciones comentadas. <a href="/book.php">Ver los libros →</a></p></article>
    </div>
  </div>
</section>

<section class="mh-band mh-alt" id="situaciones" aria-labelledby="mh-sit">
  <div class="wrap">
    <div class="mh-head">
      <p class="mh-eyebrow">Real Life English</p>
      <h2 id="mh-sit">Tu próximo «I can» empieza acá.</h2>
      <p class="mh-lead">Pedí un café, encontrá la estación o discutí un proyecto. Cada situación trae diálogo, audio y actividades.</p>
    </div>
    <div class="mh-filters" role="group" aria-label="Filtrar situaciones">
      <button type="button" class="is-active" aria-pressed="true" data-goal="all">Todas</button>
      <button type="button" aria-pressed="false" data-goal="Vida diaria">Mi día a día</button>
      <button type="button" aria-pressed="false" data-goal="Viajes">Viajes</button>
      <button type="button" aria-pressed="false" data-goal="Trabajo">Trabajo</button>
    </div>
    <div class="mh-scenes" data-scene-catalog>
<?php foreach ($featuredScenes as $item): $lines = array_slice($item['lines'] ?? [], 0, 2); ?>
      <a class="mh-scene" data-category="<?= e($item['category'] ?? 'Vida diaria') ?>" href="<?= $user ? '/unit.php?id=' . $item['unit_id'] . '#t-practice' : '/situations.php#lesson-' . e($item['id']) ?>">
        <span class="mh-scene-meta"><span class="mh-chip"><?= e($item['level']) ?></span><span><?= e($item['category'] ?? 'Vida diaria') ?> · <?= count($item['questions']) ?> actividades</span></span>
        <span class="mh-bubbles" lang="en">
<?php foreach ($lines as $i => $line): ?>
          <span class="mh-bubble<?= $i % 2 ? ' me' : '' ?>"><b><?= e($line['speaker'] ?? '') ?></b><?= e($line['en'] ?? '') ?></span>
<?php endforeach; ?>
        </span>
        <span class="mh-scene-title"><?= e($item['title']) ?></span>
        <span class="mh-scene-goal"><?= e($item['goal'] ?? 'Escuchar, comprender y responder en inglés.') ?></span>
        <span class="mh-scene-foot">Diálogo + audio + práctica <span aria-hidden="true">→</span></span>
      </a>
<?php endforeach; ?>
    </div>
    <div class="mh-more">
      <p data-catalog-count aria-live="polite"><?= count($featuredScenes) ?> situaciones destacadas de <?= $sceneCount ?>.</p>
      <a class="mh-btn mh-btn-ghost" href="/situations.php">Ver las <?= $sceneCount ?> situaciones →</a>
    </div>
  </div>
</section>

<section class="mh-band" id="niveles" aria-labelledby="mh-lv">
  <div class="wrap">
    <div class="mh-head">
      <p class="mh-eyebrow">Un recorrido, distintos puntos de partida</p>
      <h2 id="mh-lv">Hay un próximo paso para tu inglés.</h2>
    </div>
    <div class="mh-ladder">
<?php foreach ($levels as $lv): $track = $tracks[$lv['code']] ?? ['', '']; ?>
      <a class="mh-rung" href="/program.php#program-<?= e($lv['code']) ?>"><code><?= e(strtoupper($lv['code'])) ?></code><b><?= e($lv['name']) ?></b><span><?= e($track[1]) ?></span></a>
<?php endforeach; ?>
    </div>
    <p class="mh-small">Los niveles organizan el catálogo de práctica A1–C2. El recorrido no es una certificación de dominio del idioma.</p>
  </div>
</section>

<section class="mh-band mh-alt" id="precio" aria-labelledby="mh-price">
  <div class="wrap">
    <div class="mh-head">
      <p class="mh-eyebrow">Precio</p>
      <h2 id="mh-price">Probalo primero. Elegí cómo seguir.</h2>
      <p class="mh-lead">Creá tu cuenta sin tarjeta y conocé la experiencia antes de contratar.</p>
    </div>
    <div class="mh-plans">
      <article class="mh-plan">
        <p class="mh-eyebrow">Para dar el primer paso</p>
        <h3>Cuenta gratuita</h3>
        <p class="mh-price">$0 <small>sin tarjeta</small></p>
        <ul>
          <li>Primera unidad de cada nivel</li>
          <li>Test de nivel orientativo</li>
          <li><?= (int) $sampleCount ?> lecturas abiertas de la biblioteca</li>
          <li>Práctica y seguimiento en la demo</li>
        </ul>
        <a class="mh-btn mh-btn-ghost" href="<?= $startUrl ?>"><?= $user ? 'Ir a mis niveles' : 'Crear cuenta gratis' ?></a>
      </article>
      <article class="mh-plan mh-plan-pro">
        <p class="mh-eyebrow">El recorrido completo</p>
        <h3>MyTeacher Completo</h3>
        <p class="mh-price"><?= money_ars(plan_price()) ?> <small>ARS / mes</small></p>
        <ul>
          <li><?= (int) $counts['units'] ?> unidades y <?= number_format((int) $counts['exercises'], 0, ',', '.') ?> ejercicios de base</li>
          <li><?= $sceneCount ?> situaciones multimedia con <?= $sceneActivities ?> actividades</li>
          <li><?= (int) $workshopCount ?> talleres de lectura y escritura</li>
          <li>Audio, pronunciación y soluciones explicadas</li>
          <li>Progreso y estadísticas en tu cuenta</li>
        </ul>
        <a class="mh-btn mh-btn-primary" href="<?= $user ? '/account.php' : '/register.php' ?>">Quiero el programa completo →</a>
        <p class="mh-small">Cobro mensual por Mercado Pago. Cancelás desde Mi cuenta y conservás el acceso hasta el final del período pago.</p>
      </article>
    </div>
  </div>
</section>

<section class="mh-band" id="preguntas" aria-labelledby="mh-faq">
  <div class="wrap mh-faq">
    <div class="mh-head">
      <p class="mh-eyebrow">Preguntas frecuentes</p>
      <h2 id="mh-faq">Todo claro desde el principio.</h2>
    </div>
    <div>
      <details open><summary>¿Puedo empezar si no sé nada de inglés?</summary><p>Sí. A1 comienza con presentaciones, rutinas y situaciones simples. Las explicaciones están en castellano y podés activar la traducción en los diálogos multimedia.</p></details>
      <details><summary>¿Qué incluye la cuenta gratuita?</summary><p>La primera unidad de cada nivel, el test orientativo y las lecturas abiertas de la biblioteca. No necesitás tarjeta ni se inicia una suscripción al registrarte.</p></details>
      <details><summary>¿Cómo se corrigen los ejercicios?</summary><p>Las preguntas cerradas se corrigen automáticamente y muestran explicaciones. La escritura guiada usa modelos y listas de revisión; no equivale a una corrección docente.</p></details>
      <details><summary>¿Hay clases en vivo o profesores particulares?</summary><p>El programa actual es de estudio autónomo. Incluye práctica con audio, pronunciación en navegadores compatibles y actividades con corrección. No incluye clases en vivo ni tutoría docente.</p></details>
      <details><summary>¿Puedo practicar desde el celular?</summary><p>Sí. La página se adapta a tu pantalla. El audio y el reconocimiento de voz dependen de las funciones y permisos del navegador. En Chrome para Windows, la ayuda del micrófono ofrece abrir la misma página en Edge.</p></details>
      <details><summary>¿Cómo cancelo la suscripción?</summary><p>Desde Mi cuenta. Conservás el acceso hasta terminar el período ya pagado.</p></details>
    </div>
  </div>
</section>

<section class="mh-closing">
  <div class="wrap mh-closing-in">
    <h2>Tu próxima conversación en inglés empieza hoy.</h2>
    <a class="mh-btn mh-btn-light" href="<?= $startUrl ?>"><?= $startLabel ?> →</a>
  </div>
</section>

</div>
<script src="/assets/home.js?v=1" defer></script>
