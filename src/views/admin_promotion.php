<section class="wrap page">
  <h1>Pruebas y opiniones</h1>
  <?php require __DIR__.'/admin_nav.php'; ?>
  <h2>Resultados de la promoción</h2>
  <p>Solo cuentas nuevas que recibieron la prueba. Una prueba por cuenta, sin reiniciarla al volver a ingresar.</p>
  <div class="stats"><?php foreach($metrics as $label=>$value): ?><div class="stat"><strong><?= (int)$value ?></strong><span><?= e($label) ?></span></div><?php endforeach; ?></div>
  <p>Conversión a pago único entre pruebas vencidas: <strong><?= $expired ? number_format(100*$expiredPaid/$expired,1,',','.') . '%' : 'Todavía no hay pruebas vencidas' ?></strong><?= $expired ? ' ('.$expiredPaid.' de '.$expired.')' : '' ?>.</p>
  <p class="fine">Pagos únicos: confirmados como aprobados por Mercado Pago; se excluyen reembolsados. Una suscripción autorizada no demuestra un cobro y se informa por separado. Estas cifras no incluyen cobros recurrentes conciliados. «Practicaron» incluye respuestas a ejercicios o situaciones. Las cuentas eliminadas dejan de contarse.</p>
  <h2>Opiniones para revisar</h2><p>Validá que el comentario corresponda a una experiencia real y que no incluya apellidos ni datos personales antes de publicarlo. Se muestra el texto original y solo el primer nombre autorizado; el email solo es visible acá.</p>
  <?php if(!$reviews): ?><p>Todavía no hay opiniones. Los alumnos pueden enviarlas desde Mi cuenta o su panel de aprendizaje.</p><?php endif; ?>
  <?php foreach($reviews as $review): ?>
    <article class="panel">
      <h3><?= e($review['display_name']) ?></h3><p><?= e($review['email']) ?> · <?= e($review['status']) ?></p>
      <blockquote style="white-space:pre-line;overflow-wrap:anywhere"><?= e($review['body']) ?></blockquote>
      <p class="fine">Autorización recibida: <?= e($review['consent_at']) ?> UTC.</p>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="user_id" value="<?= (int)$review['user_id'] ?>"><input type="hidden" name="review_hash" value="<?= e(hash('sha256',$review['display_name']."\n".$review['body']."\n".$review['consent_at'])) ?>"><button class="btn" name="action" value="approve">Aprobar y publicar</button> <button class="btn btn-ghost" name="action" value="hide">No publicar / ocultar</button></form>
    </article>
  <?php endforeach; ?>
  <?php if($page>1): ?><a class="btn btn-ghost" href="?page=<?= $page-1 ?>">Anterior</a><?php endif; ?>
  <?php if($more): ?><a class="btn btn-ghost" href="?page=<?= $page+1 ?>">Siguiente</a><?php endif; ?>
</section>
