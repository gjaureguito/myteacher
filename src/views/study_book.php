<link rel="stylesheet" href="/assets/book.css?v=2">
<section class="wrap page book-page">
  <nav class="crumbs" aria-label="Ruta"><a href="/dashboard.php">Mis niveles</a> / <a href="/level.php?code=<?= e($code) ?>"><?= e(strtoupper($code)) ?></a> / <a href="/book.php?code=<?= e($code) ?>">Libro de estudio</a></nav>
  <header class="book-cover">
    <p class="eyebrow">MYTEACHER · LIBRO DE ESTUDIO <?= e(strtoupper($code)) ?></p>
    <h1><?= e($chapterUnit ? $chapterUnit['title'] : $book['title']) ?></h1>
    <p><?= e($chapter ? $chapter['goal'] : $book['intro']) ?></p>
    <div class="book-badges"><span>Teoría desarrollada</span><span>Ejemplos analizados</span><span>Práctica con soluciones</span></div>
  </header>
  <div class="book-layout">
    <article class="book-paper">
      <?php if ($chapterUnit): ?>
        <p class="book-chapter-meta">Nivel <?= e(strtoupper($code)) ?> · Capítulo <?= (int)$chapterUnit['position'] ?> · Lectura y aplicación</p>
        <p class="eyebrow">CAPÍTULO <?= (int) $chapterUnit['position'] ?></p>
        <nav class="chapter-contents" aria-label="Índice de lectura del capítulo">
          <strong>Contenido de este capítulo</strong>
          <ol>
            <li><a href="#chapter-theory">Fundamentos y reglas</a></li>
            <?php if ($chapter): ?>
            <li><a href="#chapter-foundation">Explicación y diferencias</a></li>
            <li><a href="#chapter-example">Ejemplo comentado</a></li>
            <li><a href="#chapter-practice">Ejercicios con soluciones</a></li>
            <li><a href="#chapter-writing">Escritura y conversación</a></li>
            <li><a href="#chapter-check">Repaso final</a></li>
            <?php endif; ?>
            <li><a href="#chapter-platform-practice">Práctica interactiva</a></li>
          </ol>
        </nav>
        <?php require __DIR__ . '/_mic_notice.php'; ?>
<?php
        // Escenas locales asociadas al capítulo: no se genera IA durante la visita.
        $dialogueScenes = [
          'a1-greetings-introductions' => [
            'file' => 'a1-maya-tom.webp',
            'alt' => 'Maya y Tom se presentan en su primer día de clase de inglés.',
            'caption' => 'Maya y Tom se conocen durante su primer día de curso. Observá la escena antes de leer la conversación.',
          ],
        ];
        $scene = $dialogueScenes[$chapterUnit['slug']] ?? null;
        $scenePath = $scene ? ROOT . '/public/assets/images/dialogues/' . $scene['file'] : '';
        if ($scene && is_file($scenePath)): ?>
        <figure class="book-dialogue-scene">
          <img src="/assets/images/dialogues/<?= e($scene['file']) ?>" alt="<?= e($scene['alt']) ?>" width="788" height="479" loading="lazy" decoding="async">
          <figcaption><?= e($scene['caption']) ?></figcaption>
        </figure>
        <?php endif; ?>
<section class="theory book-theory" id="chapter-theory"><h2>Fundamentos: qué significa y cómo se utiliza</h2><p class="book-section-lead">Leé las explicaciones en orden. Identificá la regla, observá la estructura y compará los ejemplos antes de pasar a los ejercicios.</p><?= $chapterUnit['theory_html'] /* trusted published curriculum */ ?></section>
        <?php if ($chapter): require __DIR__ . '/study_chapter.php'; else: ?><section class="book-section"><h2>Comprobá lo aprendido</h2><p>Explicá con tus palabras cuándo se utiliza la estructura de este capítulo. Creá dos ejemplos propios y luego resolvé la práctica de la unidad.</p></section><?php endif; ?>
        <?php $words = json_decode($chapterUnit['vocab_json'], true) ?: []; if ($words): ?>
        <section class="book-section"><h2>Vocabulario para usar en contexto</h2><p>Elegí cinco expresiones e inventá una oración que las conecte con tu vida o con el tema del capítulo.</p><dl class="book-glossary"><?php foreach ($words as $word): ?><div><dt lang="en"><?= e($word[0]) ?></dt><dd><?= e($word[1]) ?></dd></div><?php endforeach; ?></dl></section>
        <?php endif; ?>
        <section class="book-section book-practice" id="chapter-platform-practice"><h2>Aplicá lo aprendido en la plataforma</h2><p>Ahora resolvé la práctica de la unidad: gramática, vocabulario, comprensión, escritura y expresión oral. Los ejercicios de la unidad conservan tu progreso y sus correcciones.</p><div class="book-next-actions"><a class="btn" href="/unit.php?id=<?= (int) $chapterUnit['id'] ?>#t-practice">Resolver ejercicios del capítulo</a><a class="btn btn-ghost" href="/grammar.php?id=<?= (int) $chapterUnit['id'] ?>">Consultar gramática</a></div></section>
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
