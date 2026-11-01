<section class="wrap page">
  <h1>Contanos tu experiencia</h1>
  <p>¿Qué te ayudó a aprender? ¿Qué mejorarías? Leemos todas las devoluciones. En la portada mostramos una selección de testimonios, después de revisarlos.</p>
  <?php if($review): ?><p class="pill">Estado: <?= e(['pending'=>'Pendiente de revisión','approved'=>'Publicada','hidden'=>'No publicada'][$review['status']] ?? 'Pendiente') ?></p><?php endif; ?>
  <?php foreach($errors as $error): ?><p class="flash flash-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
  <form method="post" class="form panel">
    <?= csrf_field() ?>
    <p>Se mostrará solamente tu primer nombre: <strong><?= e($name) ?></strong>. No publicaremos tu apellido ni tu email. Evitá incluirlos en el comentario.</p>
    <label>Tu opinión<textarea name="body" rows="6" minlength="20" maxlength="1500" required><?= e($body) ?></textarea></label>
    <label class="check"><input type="checkbox" name="consent" value="1" required><span>Autorizo a MyTeacher a publicar este comentario y solo mi primer nombre, sin apellido ni email.</span></label>
    <p class="fine">Podés retirar tu opinión cuando quieras. Si la editás, deja de mostrarse hasta una nueva aprobación.</p>
    <button class="btn">Enviar para revisión</button>
  </form>
  <?php if($review): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="withdraw"><button class="btn btn-ghost">Retirar mi opinión</button></form><?php endif; ?>
  <p><a href="/account.php">Volver a Mi cuenta</a></p>
</section>
