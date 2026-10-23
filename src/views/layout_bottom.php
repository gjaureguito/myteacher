</main>
<footer class="footer">
  <div class="wrap footer-in">
    <span>© <?= date('Y') ?> <?= e(app_name()) ?> · Language & Learning</span>
    <nav aria-label="Legales">
      <a href="/program.php">Programa</a>
      <a href="/library.php">Biblioteca</a>
      <a href="/terms.php">Términos</a>
      <a href="/privacy.php">Privacidad</a>
      <?php if ($c = env('CONTACT_EMAIL')): ?><a href="mailto:<?= e($c) ?>">Contacto</a><?php endif; ?>
    </nav>
  </div>
</footer>
<script src="/assets/app.js?v=3" defer></script>
<script src="/assets/learning.js?v=1" defer></script>
</body>
</html>
