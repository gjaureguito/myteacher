<?php
$testimonials = q_all("SELECT display_name,body FROM testimonials WHERE status='approved' ORDER BY reviewed_at DESC LIMIT 12");
$portraitPositions = ['0%', '33.333%', '66.667%', '100%'];
$benefitExamples = [
    ['title' => 'Practicá a tu ritmo', 'text' => 'Volvé a tus unidades cuando tengas un momento y retomá desde donde quedaste.'],
    ['title' => 'Entendé tus errores', 'text' => 'Revisá las explicaciones y usalas para saber qué practicar después.'],
    ['title' => 'Escuchá inglés de la vida real', 'text' => 'Acercate al idioma con diálogos, audio y situaciones cotidianas.'],
];
?>
<section class="mh-opinions-band" id="opiniones" aria-labelledby="opiniones-title">
  <div class="wrap">
    <header class="mh-opinions-head">
      <p class="mh-eyebrow">EXPERIENCIAS DE ALUMNOS</p>
      <h2 id="opiniones-title">Opiniones y aprendizajes de MyTeacher</h2>
      <p>Las opiniones de alumnos se publican con su autorización y después de revisarlas.</p>
    </header>

<?php if ($testimonials): ?>
    <div class="mh-opinions-row" aria-label="Opiniones de alumnos">
<?php foreach ($testimonials as $testimonial): ?>
      <article class="mh-opinion-card">
        <blockquote><?= e($testimonial['body']) ?></blockquote>
        <footer><strong><?= e(testimonial_first_name($testimonial['display_name'])) ?></strong><span>Alumno de MyTeacher</span></footer>
      </article>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="mh-opinions-empty">
      <strong>Todavía no hay opiniones reales publicadas.</strong>
      <span>Cuando los alumnos compartan su experiencia y la autoricen, sus comentarios aparecerán acá.</span>
    </div>
    <div class="mh-opinions-row mh-benefit-row" aria-label="Beneficios para practicar inglés">
<?php foreach ($benefitExamples as $i => $benefit): ?>
      <article class="mh-benefit-card">
        <div class="mh-benefit-photo" style="background-position: <?= e($portraitPositions[$i]) ?>" role="img" aria-label="Retrato fotográfico de muestra generado con inteligencia artificial"></div>
        <span class="mh-benefit-label">Beneficio de la plataforma</span>
        <h3><?= e($benefit['title']) ?></h3>
        <p><?= e($benefit['text']) ?></p>
      </article>
<?php endforeach; ?>
    </div>
    <p class="mh-opinions-note">Los retratos son fotográficos y generados con IA; no son fotos de alumnos ni representan opiniones reales.</p>
<?php endif; ?>

    <div class="mh-opinions-cta"><a class="mh-btn mh-btn-primary" href="/register.php">Probar MyTeacher gratis</a></div>
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
.mh-opinions-empty{display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:8px 14px;margin:0 0 18px;padding:14px 18px;border:1px solid #d7e5f4;border-radius:14px;background:rgba(255,255,255,.7);color:#294260;text-align:center}
.mh-opinions-empty strong{color:#173251}
.mh-opinions-empty span{line-height:1.5}
.mh-benefit-photo{width:104px;height:104px;margin:0 auto 16px;border:4px solid #e7f0fa;border-radius:50%;background-image:url('/assets/images/beneficios-estudiantes-ia.png');background-repeat:no-repeat;background-size:400% 100%;box-shadow:0 6px 18px rgba(25,55,91,.14)}
.mh-benefit-label{display:block;margin-bottom:8px;color:#2369ae;font-size:.76rem;font-weight:800;letter-spacing:.07em;text-align:center;text-transform:uppercase}
.mh-benefit-card h3{margin:0 0 8px;color:#173251;font-size:1.08rem;text-align:center}
.mh-benefit-card p{margin:0;color:#53677f;line-height:1.6;text-align:center}
.mh-opinions-note{margin:14px 0 0;color:#53677f;font-size:.82rem;line-height:1.5;text-align:center}
.mh-opinions-cta{display:flex;justify-content:center;margin-top:24px}
@media(max-width:900px){.mh-opinions-row{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.mh-opinions-band{padding:3rem 0}.mh-opinions-head{margin-bottom:22px}.mh-opinions-row{grid-template-columns:1fr;gap:12px}.mh-opinion-card,.mh-benefit-card{padding:20px;border-radius:18px}.mh-benefit-photo{width:92px;height:92px}}
</style>
