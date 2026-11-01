<?php $testimonials = q_all("SELECT display_name,body FROM testimonials WHERE status='approved' ORDER BY reviewed_at DESC LIMIT 12"); ?>
<?php if($testimonials): ?>
<section class="mh-band" id="opiniones" aria-labelledby="opiniones-title">
  <div class="wrap">
    <div class="mh-head"><p class="mh-eyebrow">EXPERIENCIAS DE ALUMNOS</p><h2 id="opiniones-title">Así viven MyTeacher</h2><p>Testimonios seleccionados, publicados con autorización de sus autores y revisados por MyTeacher.</p></div>
    <div class="mh-features">
    <?php foreach($testimonials as $testimonial): ?>
      <article><blockquote style="margin:0;overflow-wrap:anywhere;white-space:pre-line"><?= e($testimonial['body']) ?></blockquote><p><strong><?= e(testimonial_first_name($testimonial['display_name'])) ?></strong></p></article>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
