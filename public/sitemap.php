<?php
require dirname(__DIR__) . '/src/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach (['/', '/program.php', '/library.php', '/situations.php', '/terms.php', '/privacy.php'] as $path): ?>
<url><loc><?= e(app_url($path)) ?></loc></url>
<?php endforeach; ?>
<?php foreach (q_all('SELECT slug FROM units WHERE position=1 AND is_published=1 ORDER BY level_code') as $item): if (!learning_lab($item['slug'])) continue; ?>
<url><loc><?= e(app_url('/reading.php?slug=' . rawurlencode($item['slug']))) ?></loc></url>
<?php endforeach; ?>
</urlset>
