<link rel="stylesheet" href="/assets/book.css?v=1">
<section class="wrap page book-page">
  <nav class="crumbs" aria-label="Ruta"><a href="/dashboard.php">Mis niveles</a> / <a href="/level.php?code=<?= e($code) ?>"><?= e(strtoupper($code)) ?></a> / <a href="/book.php?code=<?= e($code) ?>">Libro de estudio</a></nav>
  <header class="book-cover">
    <p class="eyebrow">MYTEACHER · LIBRO DE ESTUDIO <?= e(strtoupper($code)) ?></p>
    <h1><?= e($chapterUnit ? $chapterUnit['title'] : $book['title']) ?></h1>
    <p><?= e($chapter ? $chapter['goal'] : $book['intro']) ?></p>
    <div class="book-badges"><span>Teoría desarrollada</span><span>Ejemplos analizados</span><span>Práctica con soluciones</span></div>
  </header>
  <div class="book-layout">
    <aside class="book-index">
      <h2>Índice del nivel</h2>
      <a href="/book.php?code=<?= e($code) ?>" <?= !$chapterUnit ? 'aria-current="page"' : '' ?>>Guía de estudio y proyecto</a>
      <ol><?php foreach ($units as $item): $open = can_access_unit($u, $item); ?>
        <li><a href="/book.php?code=<?= e($code) ?>&amp;chapter=<?= e($item['slug']) ?>" <?= $chapterUnit && $item['id'] === $chapterUnit['id'] ? 'aria-current="page"' : '' ?>><?= e($item['title']) ?><?php if (!$open): ?><small>Con suscripción</small><?php endif; ?></a></li>
      <?php endforeach; ?></ol>
      <h3>Otros niveles</h3><div class="book-levels"><?php foreach (['a1','a2','b1','b2','c1','c2'] as $other): ?><a href="/book.php?code=<?= e($other) ?>" <?= $other === $code ? 'aria-current="page"' : '' ?>><?= e(strtoupper($other)) ?></a><?php endforeach; ?></div>
    </aside>
    <article class="book-paper">
      <?php if ($chapterUnit): ?>
        <p class="eyebrow">CAPÍTULO <?= (int) $chapterUnit['position'] ?></p>
        <section class="theory book-theory"><h2>La base del capítulo</h2><?= $chapterUnit['theory_html'] /* trusted published curriculum, same as unit view */ ?></section>
        <?php if ($chapter): require __DIR__ . '/study_chapter.php'; endif; ?>
        <?php $words = json_decode($chapterUnit['vocab_json'], true) ?: []; if ($words): ?>
        <section class="book-section"><h2>Vocabulario para usar en contexto</h2><p>Elegí cinco expresiones e inventá una oración que las conecte con tu vida o con el tema del capítulo.</p><dl class="book-glossary"><?php foreach ($words as $word): ?><div><dt lang="en"><?= e($word[0]) ?></dt><dd><?= e($word[1]) ?></dd></div><?php endforeach; ?></dl></section>
        <?php endif; ?>
        <section class="book-section book-practice"><h2>Aplicá lo aprendido en la plataforma</h2><p>Ahora resolvé la práctica de la unidad: gramática, vocabulario, comprensión, escritura y expresión oral. Los ejercicios de la unidad conservan tu progreso y sus correcciones.</p><a class="btn" href="/unit.php?id=<?= (int) $chapterUnit['id'] ?>#t-practice">Abrir práctica de este capítulo</a></section>
        <nav class="book-pagination" aria-label="Capítulos"><?php if ($previous): ?><a href="/book.php?code=<?= e($code) ?>&amp;chapter=<?= e($previous['slug']) ?>">← <?= e($previous['title']) ?></a><?php endif; ?><?php if ($next): ?><a href="/book.php?code=<?= e($code) ?>&amp;chapter=<?= e($next['slug']) ?>"><?= e($next['title']) ?> →</a><?php else: ?><a href="/book.php?code=<?= e($code) ?>#project">Proyecto de cierre →</a><?php endif; ?></nav>
      <?php else: ?>
        <h2>Qué vas a aprender</h2><ul><?php foreach ($book['outcomes'] as $outcome): ?><li><?= e($outcome) ?></li><?php endforeach; ?></ul>
        <h2>Antes de empezar</h2><p><?= e($book['before']) ?></p>
        <h2>Cómo trabajar cada capítulo</h2><ol class="book-method"><li><strong>Leé para comprender.</strong> Identificá qué comunica la estructura y qué situaciones resuelve.</li><li><strong>Observá el ejemplo.</strong> Explicá por qué se eligió cada forma y comparala con otra posible.</li><li><strong>Practicá sin mirar.</strong> Resolvé las cuatro actividades en papel; después consultá las soluciones comentadas.</li><li><strong>Producí.</strong> Escribí y hablá usando ideas propias. Compará tu texto con el modelo y revisá errores.</li><li><strong>Comprobá y retomá.</strong> Completá la práctica interactiva y volvé después a lo que todavía no podés explicar.</li></ol>
        <p class="book-note">Organizá el estudio en sesiones de lectura, práctica y revisión. El avance depende de lo que podés comprender y producir; no de cuántas páginas recorrés.</p>
        <?php if ($units): ?><a class="btn" href="/book.php?code=<?= e($code) ?>&amp;chapter=<?= e($units[0]['slug']) ?>">Empezar el primer capítulo →</a><?php else: ?><p>Este nivel todavía no tiene capítulos publicados.</p><?php endif; ?>
        <?php require __DIR__ . '/study_picture.php'; ?>
        <section class="book-section" id="project"><p class="eyebrow">INTEGRAR LO APRENDIDO</p><h2>Proyecto de cierre</h2><p><?= e($book['project']) ?></p><h3>Lista de revisión del nivel</h3><ul><?php foreach ($book['review'] as $question): ?><li><?= e($question) ?></li><?php endforeach; ?></ul><p>Guardá un primer borrador y una versión revisada. Marcá qué cambiaste y por qué. Si no podés justificar una forma, retomá el capítulo correspondiente.</p></section>
        <p class="fine">Este libro desarrolla las unidades publicadas del programa. Sus capítulos y proyectos son material de estudio autónomo; no constituyen por sí solos una evaluación completa ni una certificación de nivel.</p>
      <?php endif; ?>
    </article>
  </div>
</section>
