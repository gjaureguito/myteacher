<link rel="stylesheet" href="/assets/certificates.css?v=1">
<section class="wrap page mt-achievements">
  <p class="eyebrow">TU INGLÉS, PASO A PASO</p>
  <h1>Mi ruta y certificados</h1>
  <p>Reconocé lo que ya aprendiste y elegí tu próximo paso. Cada unidad completada suma conceptos a tu recorrido.</p>
  <p class="muted">Los conceptos se marcan como aprendidos al resolver todos los ejercicios de su unidad. Al completar todas las unidades publicadas de un nivel, recibís una constancia de finalización de MyTeacher.</p>
  <nav class="mt-path-links" aria-label="Niveles de mi ruta"><?php foreach ($pathLevels as $level): ?><a href="#path-<?= e($level['code']) ?>"><?= e(strtoupper($level['code'])) ?></a><?php endforeach; ?></nav>
  <?php foreach ($pathLevels as $level): $certificate = $certificates[$level['code']] ?? null; ?>
  <section class="mt-path-level" id="path-<?= e($level['code']) ?>">
    <header><div><p class="eyebrow">NIVEL <?= e(strtoupper($level['code'])) ?></p><h2><?= e($level['name']) ?></h2></div><span class="pill"><?= $level['complete'] ? 'Completado' : ($level['solved'] ? 'En curso' : 'Por comenzar') ?></span></header>
    <p><?= e($level['description']) ?></p>
    <progress max="<?= max(1, $level['total']) ?>" value="<?= $level['solved'] ?>" aria-label="Avance de <?= e(strtoupper($level['code'])) ?>"></progress>
    <p><?= $level['solved'] ?> de <?= $level['total'] ?> ejercicios resueltos · <?= count(array_filter($level['units'], fn($unit) => $unit['complete'])) ?> de <?= count($level['units']) ?> unidades completadas</p>
    <ol class="mt-concept-list">
    <?php foreach ($level['units'] as $unit): $allowed = can_access_unit($u, $unit); ?>
      <li class="<?= $unit['complete'] ? 'is-learned' : '' ?>"><span class="mt-concept-status"><?= $unit['complete'] ? '✓ Aprendido' : ($unit['solved'] ? 'En aprendizaje' : 'Por aprender') ?></span><h3><?= e($unit['title']) ?></h3><p><?= e($unit['summary']) ?></p><small><?= $unit['solved'] ?> / <?= $unit['total'] ?> ejercicios<?= !$unit['total'] ? ' · Contenido en preparación' : '' ?></small><a href="<?= $allowed ? '/unit.php?id='.(int)$unit['id'] : '/account.php' ?>"><?= $allowed ? ($unit['complete'] ? 'Repasar conceptos' : 'Continuar aprendiendo') : 'Ver acceso al nivel' ?> →</a></li>
    <?php endforeach; ?>
    </ol>
    <?php if (!$level['units']): ?><p>Este nivel todavía no tiene unidades publicadas.</p><?php endif; ?>
    <div class="mt-certificate-box">
    <?php if ($certificate): ?>
      <div><h3>Tu certificado <?= e(strtoupper($level['code'])) ?> está disponible</h3><p><?= e($certificate['student_name']) ?> · Emitido el <?= e(substr($certificate['issued_at'], 0, 10)) ?></p><small>Código: <?= e($certificate['reference']) ?></small></div>
      <a class="btn" href="/certificate.php?level=<?= e($level['code']) ?>">Descargar certificado PDF</a>
    <?php else: ?>
      <div><h3>Tu próximo logro: certificado <?= e(strtoupper($level['code'])) ?></h3><p>Se habilita cuando resolvés todos los ejercicios de todas las unidades publicadas de este nivel.</p></div>
    <?php endif; ?>
    </div>
  </section>
  <?php endforeach; ?>
  <p class="muted">Los certificados conservan la fecha y el programa de su emisión, aunque después se agreguen ejercicios o reinicies la práctica. Son constancias de MyTeacher, no acreditaciones oficiales del MCER.</p>
</section>
