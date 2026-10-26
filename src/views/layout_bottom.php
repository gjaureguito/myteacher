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
<nav class="bottom-nav" aria-label="Accesos rápidos">
  <a href="/dashboard.php"<?= $cur(['/dashboard.php', '/level.php', '/unit.php']) ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6v-9h-6v9zm0-16v5h6V4h-6z"/></svg>Niveles</a>
  <a href="/review.php"<?= $cur('/review.php') ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 5V1L7 6l5 5V7a6 6 0 1 1-6 6H4a8 8 0 1 0 8-8z"/></svg>Repaso</a>
  <a href="/glossary.php"<?= $cur('/glossary.php') ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4 3h16v18H4zm3 4v2h10V7zm0 4v2h8v-2z"/></svg>Glosario</a>
  <a href="/stats.php"<?= $cur('/stats.php') ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M5 9.2h3V19H5V9.2zM10.6 5h2.8v14h-2.8V5zm5.6 8H19v6h-2.8v-6z"/></svg>Progreso</a>
  <a href="/library.php"<?= $cur(['/library.php', '/reading.php']) ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21 5c-1.1-.35-2.3-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c1.45-1.1 3.55-1.5 5.5-1.5s4.05.4 5.5 1.5c1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/></svg>Lecturas</a>
  <a href="/account.php"<?= $cur('/account.php') ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/></svg>Cuenta</a>
</nav>
<?php endif; ?>
<script src="/assets/edge-access.js?v=1" defer></script>
<script src="/assets/app.js?v=11" defer></script>
<script src="/assets/learning.js?v=1" defer></script>
<script src="/assets/pro.js?v=19" defer></script>
<script src="/assets/scenes.js?v=1" defer></script>
<script src="/assets/modern.js?v=1" defer></script>
<script src="/assets/visual.js?v=1" defer></script>
<script src="/assets/study-lab.js?v=1" defer></script>
</body>
</html>
