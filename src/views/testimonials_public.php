<?php
$testimonials = q_all("SELECT display_name,body FROM testimonials WHERE status='approved' ORDER BY reviewed_at DESC LIMIT 12");
?>
<section class="mh-opinions-band" id="opiniones" aria-labelledby="opiniones-title">
  <div class="wrap">
<?php if ($testimonials): ?>
    <header class="mh-opinions-head">
      <p class="mh-eyebrow">EXPERIENCIAS DE ALUMNOS</p>
      <h2 id="opiniones-title">Opiniones y aprendizajes de MyTeacher</h2>
      <p>Las opiniones de alumnos se publican con su autorización y después de revisarlas.</p>
    </header>
    <div class="mh-opinions-row" aria-label="Opiniones de alumnos">
<?php foreach ($testimonials as $testimonial): ?>
      <article class="mh-opinion-card">
        <blockquote><?= e($testimonial['body']) ?></blockquote>
        <footer><strong><?= e(testimonial_first_name($testimonial['display_name'])) ?></strong><span>Alumno de MyTeacher</span></footer>
      </article>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <header class="mh-opinions-head">
      <p class="mh-eyebrow">SIN RIESGO</p>
      <h2 id="opiniones-title">Probalo una semana. Decidí después.</h2>
      <p>No te pedimos que nos creas: usalo 7 días con acceso completo y fijate si te sirve.</p>
    </header>
    <div class="mh-opinions-row" aria-label="Condiciones de la prueba gratis">
      <article class="mh-benefit-card"><span class="mh-benefit-icon" aria-hidden="true">💳</span><h3>Sin tarjeta</h3><p>Te registrás con email y contraseña. No cargás ningún medio de pago para probar.</p></article>
      <article class="mh-benefit-card"><span class="mh-benefit-icon" aria-hidden="true">🔓</span><h3>Acceso completo</h3><p>Los 6 niveles, las situaciones con audio y los ejercicios con explicación, desde el primer día.</p></article>
      <article class="mh-benefit-card"><span class="mh-benefit-icon" aria-hidden="true">💾</span><h3>Sin cobro automático</h3><p>Al terminar no se te cobra nada. Si querés seguir, pagás por Mercado Pago y conservás tu progreso.</p></article>
    </div>
<?php endif; ?>

    <?php if (!current_user()): ?>
    <div class="mh-opinions-cta"><a class="mh-btn mh-btn-primary" href="/register.php">Empezar mi prueba gratis →</a></div>
<?php endif; ?>
  </div>
</section>
<style>
.mh-opinions-band{width:100%;padding:clamp(3.5rem,7vw,6rem) 0;background:#edf4fc;border-top:1px solid #dce8f5;border-bottom:1px solid #dce8f5}
.mh-opinions-head{max-width:760px;margin:0 auto 30px;text-align:center}
.mh-opinions-head>p:last-child{margin:0;color:#53677f;line-height:1.6}
.mh-opinions-row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;align-items:stretch}
.mh-opinion-card,.mh-benefit-card{min-width:0;padding:24px;border:1px solid #dce6f0;border-radius:22px;background:#fff;box-shadow:0 12px 32px rgba(25,55,91,.07)}
.mh-opinion-card blockquote{margin:0;color:#173251;font-size:1.04rem;line-height:1.7;overflow-wrap:anywhere;white-space:pre-line}
.mh-opinion-card footer{display:grid;gap:3px;margin-top:18px;color:#173251}
.mh-opinion-card footer span{color:#657991;font-size:.86rem}
.mh-benefit-icon{display:block;margin:0 auto 12px;font-size:2rem;line-height:1;text-align:center}
.mh-benefit-card h3{margin:0 0 8px;color:#173251;font-size:1.08rem;text-align:center}
.mh-benefit-card p{margin:0;color:#53677f;line-height:1.6;text-align:center}
.mh-opinions-cta{display:flex;justify-content:center;margin-top:24px}
@media(max-width:900px){.mh-opinions-row{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.mh-opinions-band{padding:3rem 0}.mh-opinions-head{margin-bottom:22px}.mh-opinions-row{grid-template-columns:1fr;gap:12px}.mh-opinion-card,.mh-benefit-card{padding:20px;border-radius:18px}}
</style>
