</main>
<footer class="footer">
  <div class="wrap footer-in">
    <span>© <?= date('Y') ?> <?= e(app_name()) ?> · Inglés para la vida real</span>
    <nav aria-label="Legales">
      <a href="/program.php">Programa</a>
      <a href="/library.php">Biblioteca</a>
      <a href="/situations.php">Situaciones</a>
      <a href="/terms.php">Términos</a>
      <a href="/privacy.php">Privacidad</a>
      <?php if ($c = env('CONTACT_EMAIL')): ?><a href="mailto:<?= e($c) ?>">Contacto</a><?php endif; ?>
    </nav>
  </div>
</footer>
<?php if (current_user()): $bp = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH); $cur = fn($p) => in_array($bp, (array) $p, true) ? ' aria-current="page"' : ''; ?>
<nav class="mtn-tabbar" aria-label="Accesos rápidos">
  <a href="/dashboard.php"<?= $cur(['/dashboard.php', '/level.php', '/unit.php']) ?>><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg><span>Niveles</span></a>
  <a href="/book.php"<?= $cur(['/book.php', '/grammar.php']) ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/></svg><span>Estudiar</span></a>
  <a href="/review.php"<?= $cur('/review.php') ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12a8 8 0 1 1-2.3-5.6"/><path d="M20 4v4h-4"/></svg><span>Repaso</span></a>
  <a href="/stats.php"<?= $cur('/stats.php') ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20V11M12 20V5M19 20v-7"/></svg><span>Progreso</span></a>
  <a href="/account.php"<?= $cur('/account.php') ?>><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span>Cuenta</span></a>
</nav>
<?php endif; ?>
<script src="/assets/sidebar.js?v=1" defer></script>
<script src="/assets/nav.js?v=1" defer></script>
<script src="/assets/voice.js?v=4" defer></script>
<script src="/assets/app.js?v=12" defer></script>
<script src="/assets/learning.js?v=1" defer></script>
<script src="/assets/pro.js?v=20" defer></script>
<script src="/assets/scenes.js?v=1" defer></script>
<script src="/assets/modern.js?v=1" defer></script>
<script src="/assets/visual.js?v=1" defer></script>
<script src="/assets/study-lab.js?v=1" defer></script>
<?php if (current_user()): ?><script src="/assets/selection-glossary.js?v=2" defer></script><?php endif; ?>
</body>
</html>
