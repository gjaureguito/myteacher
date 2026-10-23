<?php
require dirname(__DIR__) . '/src/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach (['/', '/terms.php', '/privacy.php'] as $path): ?>
<url><loc><?= e(app_url($path)) ?></loc></url>
<?php endforeach; ?>
</urlset>
