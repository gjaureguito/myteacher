<section class="wrap page">
  <h1>Contenido</h1>
  <?php require __DIR__ . '/admin_nav.php'; ?>

  <div class="panel" style="margin-bottom: 24px">
    <h2>Sincronizar desde los archivos</h2>
    <p class="muted">El contenido base vive en <code>content/a1.php</code> … <code>content/c2.php</code>. Si agregás unidades nuevas en esos archivos y hacés deploy, sincronizá acá.</p>
    <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="mode" value="new">
      <button class="btn btn-sm">Agregar unidades nuevas</button></form>
    <form method="post" class="inline" onsubmit="return confirm('Esto pisa las ediciones hechas desde este panel y reinicia el progreso de esas unidades. ¿Seguir?')"><?= csrf_field() ?><input type="hidden" name="mode" value="force">
      <button class="btn btn-sm btn-ghost">Reemplazar todo con los archivos</button></form>
  </div>

  <p><a class="btn btn-sm" href="/admin/unit.php">Crear unidad nueva</a></p>
  <div class="table-scroll" role="region" aria-label="Unidades del programa" tabindex="0"><table class="tbl"><caption>Unidades, disponibilidad y ejercicios por nivel.</caption>
    <thead><tr><th>Nivel</th><th>#</th><th>Título</th><th>Ejercicios</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($units as $u): ?>
      <tr>
        <td><?= e(strtoupper($u['level_code'])) ?></td>
        <td><?= (int) $u['position'] ?><?= $u['position'] == 1 ? ' <em class="tag">Demo</em>' : '' ?></td>
        <td><?= e($u['title']) ?></td>
        <td><?= (int) $u['n_ex'] ?></td>
        <td><?= $u['is_published'] ? 'Publicada' : 'Borrador' ?></td>
        <td><a href="/admin/unit.php?id=<?= (int) $u['id'] ?>">Editar</a> · <a href="/unit.php?id=<?= (int) $u['id'] ?>">Ver</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
