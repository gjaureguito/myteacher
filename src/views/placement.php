<section class="wrap page narrow">
<?php if ($result): ?>
  <h1>Tu nivel recomendado: <?= e(strtoupper($result['level'])) ?></h1>
  <p class="lead"><?= e($result['name']) ?>. Acertaste <?= $result['score'] ?> de <?= $result['total'] ?> preguntas.</p>
  <table class="tbl">
    <thead><tr><th>Nivel</th><th>Aciertos</th></tr></thead>
    <tbody>
      <?php foreach ($result['by'] as $lv => $b): ?>
        <tr><td><?= e(strtoupper($lv)) ?></td><td><?= $b['ok'] ?>/<?= $b['n'] ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="muted">Empezá por ese nivel. Si la primera unidad te resulta fácil, pasá directo al siguiente.</p>
  <p><a class="btn" href="/level.php?code=<?= e($result['level']) ?>">Ir a <?= e(strtoupper($result['level'])) ?></a>
     <a class="btn btn-ghost" href="/placement.php">Repetir el test</a></p>
<?php else: ?>
  <h1>Test de nivel</h1>
  <p class="lead"><?= $welcome ? 'Antes de empezar, averigüemos tu nivel. ' : '' ?><?= count($questions) ?> preguntas, de A1 a C2. Si no sabés una, dejala en blanco: adivinar distorsiona el resultado.</p>
  <form method="post" class="placement">
    <?= csrf_field() ?>
    <?php foreach ($questions as $i => $qq): ?>
      <fieldset class="pq">
        <legend><span class="ex-n"><?= $i + 1 ?></span> <?= e($qq[1]) ?></legend>
        <?php foreach ($qq[2] as $k => $opt): ?>
          <label class="radio"><input type="radio" name="q<?= $i ?>" value="<?= $k ?>"> <?= e($opt) ?></label>
        <?php endforeach; ?>
      </fieldset>
    <?php endforeach; ?>
    <button class="btn">Ver mi nivel</button>
  </form>
<?php endif; ?>
</section>
