<?php $colors = level_colors(); ?>
<section class="hero">
  <div class="wrap hero-in">
    <div class="hero-copy">
      <h1>Inglés de A1 a C2, explicado en castellano y practicado todos los días.</h1>
      <p class="lead">Teoría clara con los errores típicos de quien habla español, ejercicios que se corrigen al instante y audio en cada oración. Empezás gratis y seguís a tu ritmo.</p>
      <div class="hero-cta">
        <a class="btn btn-lg" href="/register.php">Crear cuenta y probar gratis</a>
        <a class="btn btn-ghost btn-lg" href="#niveles">Ver los niveles</a>
      </div>
      <p class="fine">Demo sin tarjeta: primera unidad de cada nivel y test de nivel.</p>
    </div>

    <div class="try" aria-label="Probá un ejercicio">
      <p class="try-label">Probá ahora</p>
      <p class="try-q">My brother <span class="gap-demo" id="demo-gap">____</span> to work by bus every day.</p>
      <div class="options" id="demo-opts">
        <button class="opt" data-ok="0">go</button>
        <button class="opt" data-ok="1">goes</button>
        <button class="opt" data-ok="0">going</button>
      </div>
      <p class="try-fb" id="demo-fb" aria-live="polite"></p>
      <button class="linkbtn speak" data-say="My brother goes to work by bus every day.">🔊 Escuchar la oración</button>
    </div>
  </div>
</section>

<section class="wrap block" id="niveles">
  <h2>Seis niveles, del primer "hello" al inglés de negocios</h2>
  <p class="muted narrow-text">Seguimos el Marco Común Europeo (MCER), el mismo que usan Cambridge, el TOEFL y las empresas para medir el nivel.</p>
  <ol class="ladder ladder-home">
    <?php foreach ($levels as $i => $lv): ?>
      <li class="rung" style="--lv: <?= e($colors[$lv['code']] ?? '#2747D9') ?>; --step: <?= $i ?>">
        <div>
          <span class="rung-code"><?= e(strtoupper($lv['code'])) ?></span>
          <span class="rung-body">
            <strong><?= e($lv['name']) ?></strong>
            <span class="muted"><?= e($lv['description']) ?></span>
          </span>
          <span class="rung-pct"><?= (int) $lv['n_units'] ?> unidades</span>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
</section>

<section class="band">
  <div class="wrap block">
    <h2>Cada unidad tiene tres partes</h2>
    <div class="parts">
      <article>
        <h3>Teoría</h3>
        <p>Explicaciones cortas en castellano, tablas y ejemplos con traducción. Cada tema marca el error que más cometemos los hispanohablantes.</p>
        <p class="sample"><span class="hl">She doesn't work</span> — no <s>She doesn't works</s></p>
      </article>
      <article>
        <h3>Vocabulario</h3>
        <p>Tarjetas para repasar las palabras de la unidad, con pronunciación en audio para escucharlas cuantas veces quieras.</p>
        <p class="sample"><span class="hl">commute</span> = viajar al trabajo</p>
      </article>
      <article>
        <h3>Práctica</h3>
        <p>Opción múltiple, completar, ordenar oraciones, comprensión auditiva y dictado. Ves al instante si acertaste y por qué.</p>
        <p class="sample"><?= (int) $counts['exercises'] ?> ejercicios en <?= (int) $counts['units'] ?> unidades</p>
      </article>
    </div>
  </div>
</section>

<section class="wrap block" id="precio">
  <div class="pricing">
    <div>
      <h2>Un precio, todo incluido</h2>
      <p>Los seis niveles, todas las unidades, el test de nivel y el seguimiento de tu progreso. Pagás mes a mes con Mercado Pago y cancelás cuando quieras desde tu cuenta.</p>
    </div>
    <div class="price-box">
      <p class="price"><?= money_ars(plan_price()) ?><span>/mes</span></p>
      <a class="btn btn-block" href="/register.php">Empezar con la demo gratis</a>
      <p class="fine">Débito automático mensual. Sin permanencia.</p>
    </div>
  </div>
</section>

<section class="wrap block">
  <div class="narrow">
  <h2>Preguntas frecuentes</h2>
  <details><summary>¿Qué incluye la demo gratuita?</summary><p>Al crear tu cuenta accedés a la primera unidad completa de cada nivel (teoría, vocabulario y práctica) y al test de nivel. No pedimos tarjeta.</p></details>
  <details><summary>¿Cómo se cobra la suscripción?</summary><p>Con Mercado Pago, como débito automático mensual de <?= money_ars(plan_price()) ?> con tarjeta o dinero en cuenta. Mercado Pago te avisa cada cobro.</p></details>
  <details><summary>¿Puedo cancelar cuando quiera?</summary><p>Sí. Desde "Mi cuenta" cancelás en un clic y seguís usando la plataforma hasta que termine el mes que ya pagaste.</p></details>
  <details><summary>¿Necesito saber algo de inglés?</summary><p>No. El nivel A1 empieza desde cero. Si ya sabés algo, el test de nivel te dice por dónde arrancar.</p></details>
  <details><summary>¿Funciona en el celular?</summary><p>Sí, en cualquier navegador moderno de celular, tablet o computadora. El audio usa la voz en inglés de tu dispositivo.</p></details>
  </div>
</section>
