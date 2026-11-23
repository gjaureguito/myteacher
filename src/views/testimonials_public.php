<?php $testimonials = q_all("SELECT display_name,body FROM testimonials WHERE status='approved' ORDER BY reviewed_at DESC LIMIT 12"); ?>
<section class="mh-band mh-testimonials" id="opiniones" aria-labelledby="opiniones-title">
  <div class="wrap">
    <div class="mh-head">
      <p class="mh-eyebrow">EXPERIENCIAS DE ALUMNOS</p>
      <h2 id="opiniones-title">Opiniones reales sobre MyTeacher</h2>
      <p>Publicamos comentarios de alumnos con su autorización y después de revisarlos.</p>
    </div>
    <div class="mh-testimonials-layout">
      <figure class="mh-testimonials-visual">
        <img src="/assets/images/beneficios-estudiantes-ia.png" alt="Escenas ilustrativas de personas practicando inglés en casa y desde el celular" loading="lazy" decoding="async">
        <figcaption>Imágenes ilustrativas generadas con IA; no representan a los autores de las opiniones.</figcaption>
      </figure>
      <div class="mh-testimonials-content">
<?php if ($testimonials): ?>
        <div class="mh-testimonials-grid">
<?php foreach ($testimonials as $testimonial): ?>
          <article class="mh-testimonial-card">
            <blockquote><?= e($testimonial['body']) ?></blockquote>
            <p><strong><?= e(testimonial_first_name($testimonial['display_name'])) ?></strong></p>
          </article>
<?php endforeach; ?>
        </div>
<?php else: ?>
        <div class="mh-testimonials-empty">
          <p class="mh-testimonials-empty-title">Todavía no hay opiniones publicadas.</p>
          <p>Queremos mostrar experiencias reales. Los alumnos pueden compartir su opinión desde su cuenta; después de revisarla y obtener su autorización, aparecerá acá.</p>
          <ul>
            <li>Estudiar inglés a tu ritmo, desde A1 hasta C2.</li>
            <li>Practicar con diálogos y audio de situaciones cotidianas.</li>
            <li>Entender las correcciones y seguir tu progreso.</li>
          </ul>
          <a class="mh-btn mh-btn-primary" href="/register.php">Probar MyTeacher gratis</a>
        </div>
<?php endif; ?>
      </div>
    </div>
  </div>
</section>
<style>
.mh-testimonials-layout{display:grid;grid-template-columns:minmax(0,.95fr) minmax(0,1.05fr);gap:clamp(20px,4vw,44px);align-items:start}
.mh-testimonials-visual{position:relative;margin:0;overflow:hidden;border-radius:24px;background:#dfeaf5;box-shadow:0 18px 48px rgba(25,55,91,.12)}
.mh-testimonials-visual img{display:block;width:100%;height:auto;aspect-ratio:2.69;object-fit:cover}
.mh-testimonials-visual figcaption{position:absolute;right:12px;bottom:12px;max-width:calc(100% - 24px);padding:8px 11px;border-radius:14px;background:rgba(255,255,255,.96);color:#294260;font-size:.78rem;line-height:1.4}
.mh-testimonials-content{min-width:0}
.mh-testimonials-grid{display:grid;gap:14px}
.mh-testimonial-card,.mh-testimonials-empty{padding:clamp(20px,3vw,28px);border:1px solid #e1e9f1;border-radius:20px;background:#fff;box-shadow:0 8px 24px rgba(25,55,91,.06)}
.mh-testimonial-card blockquote{margin:0;color:#173251;font-size:1.05rem;line-height:1.7;overflow-wrap:anywhere;white-space:pre-line}
.mh-testimonial-card p{margin:16px 0 0;color:#294260}
.mh-testimonials-empty-title{margin:0 0 10px;color:#173251;font-size:1.18rem;font-weight:750}
.mh-testimonials-empty p:not(.mh-testimonials-empty-title){margin:0;color:#53677f;line-height:1.65}
.mh-testimonials-empty ul{padding-left:1.2rem;color:#294260;line-height:1.8}
.mh-testimonials-empty .mh-btn{margin-top:8px}
@media(max-width:760px){.mh-testimonials-layout{grid-template-columns:1fr}.mh-testimonials-visual{border-radius:18px}.mh-testimonials-visual img{aspect-ratio:1.9}.mh-testimonials-visual figcaption{right:8px;bottom:8px;max-width:calc(100% - 16px);padding:7px 9px;font-size:.72rem}}
</style>
