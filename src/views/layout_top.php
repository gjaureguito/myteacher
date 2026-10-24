<?php
$u = current_user();
$flashes = take_flashes();
$title = ($pageTitle ? $pageTitle . ' — ' : '') . app_name();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$publicPage = in_array($path, ['/', '/index.php', '/terms.php', '/privacy.php', '/program.php', '/library.php', '/reading.php', '/situations.php'], true);
if (http_response_code() >= 400) $publicPage = false;
$canonical = app_url($path === '/index.php' ? '/' : $path);
if ($path === '/reading.php') $canonical .= '?slug=' . rawurlencode((string) ($_GET['slug'] ?? ''));
$description = match ($path) {
    '/library.php' => 'Lecturas originales en inglés, con audio, comprensión, explicaciones y escritura guiada. Explorá muestras A1–C2 sin registrarte.',
    '/program.php' => 'Explorá el programa de InglésPro: objetivos de uso, unidades publicadas y talleres de lectura y escritura de A1 a C2.',
    '/reading.php' => $lab['goal'] ?? 'Leé, escuchá, comprendé y escribí en inglés con un taller de práctica autónoma.',
    default => 'Aprendé inglés para la vida real con MyTeacher. Situaciones cotidianas con audio, práctica de pronunciación y ejercicios con corrección. Empezá gratis.',
};
?><!doctype html>
<html lang="es-AR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<?php if ($publicPage): ?>
<link rel="sitemap" type="application/xml" href="/sitemap.php">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:image" content="<?= e(app_url('/assets/images/learning-together-v1.webp')) ?>">
<meta property="og:image:alt" content="Imagen ilustrativa de personas compartiendo una lectura.">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:locale" content="es_AR">
<?php else: ?>
<meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<meta name="csrf" content="<?= e(csrf_token()) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;650;700;750;800&family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/app.css?v=1">
<link rel="stylesheet" href="/assets/redesign.css?v=6">
<link rel="stylesheet" href="/assets/pro.css?v=9">
<link rel="stylesheet" href="/assets/scenes.css?v=2">
<link rel="stylesheet" href="/assets/modern.css?v=1">
<meta name="theme-color" content="#102b32">
<script>(function(){try{var t=localStorage.getItem('ip_theme');if(!t)t=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';document.documentElement.dataset.theme=t;}catch(e){}})();</script>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%23862c39'/><rect x='6' y='18' width='20' height='6' fill='%23e4c49a'/><text x='16' y='22' font-family='Arial' font-weight='700' font-size='15' text-anchor='middle' fill='white'>En</text></svg>">
</head>
<body class="site-modern<?= $u ? ' has-bottom-nav' : '' ?>">
<a class="skip" href="#main">Saltar al contenido</a>
<header class="topbar">
  <div class="wrap topbar-in">
    <a class="brand" href="<?= $u ? '/dashboard.php' : '/' ?>"><span class="brand-mark" aria-hidden="true">m<span>t</span></span><?= e(app_name()) ?></a>
<?php $navCur = fn(string $p) => $path === $p ? ' aria-current="page"' : ''; ?>
    <nav class="nav" aria-label="Principal">
      <?php if ($u): ?>
        <a href="/dashboard.php"<?= $navCur('/dashboard.php') ?>>Mis niveles</a>
        <a href="/review.php"<?= $navCur('/review.php') ?>>Repaso</a>
        <a href="/stats.php"<?= $navCur('/stats.php') ?>>Estadísticas</a>
        <a href="/placement.php"<?= $navCur('/placement.php') ?>>Test de nivel</a>
        <a href="/account.php"<?= $navCur('/account.php') ?>>Mi cuenta</a>
        <?php if (is_admin($u)): ?><a href="/admin/">Admin</a><?php endif; ?>
        <form method="post" action="/logout.php" class="inline"><?= csrf_field() ?><button class="linkbtn">Salir</button></form>
      <?php else: ?>
        <a href="/program.php"<?= $navCur('/program.php') ?>>El programa</a>
        <a href="/library.php"<?= $navCur('/library.php') ?>>Biblioteca</a>
        <a href="/situations.php"<?= $navCur('/situations.php') ?>>Situaciones</a>
        <a href="/#precio">Precio</a>
        <a href="/login.php">Ingresar</a>
        <a class="btn btn-sm" href="/register.php">Empezar gratis <span aria-hidden="true">↗</span></a>
      <?php endif; ?>
    </nav>
      <span class="tools">
        <button type="button" class="tool-btn" id="tool-sound" aria-pressed="true" aria-label="Efectos de sonido" title="Efectos de sonido">
          <svg class="on" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3A4.5 4.5 0 0 0 14 7.97v8.05A4.47 4.47 0 0 0 16.5 12zM14 3.23v2.06a7 7 0 0 1 0 13.42v2.06A9 9 0 0 0 14 3.23z"/></svg>
          <svg class="off" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M16.5 12A4.5 4.5 0 0 0 14 7.97v2.21l2.45 2.45c.03-.2.05-.41.05-.63zM19 12c0 .94-.2 1.82-.54 2.64l1.51 1.51A8.8 8.8 0 0 0 21 12a9 9 0 0 0-7-8.77v2.06A7 7 0 0 1 19 12zM4.27 3 3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06a9 9 0 0 0 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4 9.91 6.09 12 8.18V4z"/></svg>
        </button>
        <button type="button" class="tool-btn" id="tool-theme" aria-pressed="false" aria-label="Modo oscuro" title="Modo claro / oscuro">
          <svg class="off" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.39 5.39 0 0 1-4.4 2.26 5.4 5.4 0 0 1-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/></svg>
          <svg class="on" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zM2 13h2a1 1 0 0 0 0-2H2a1 1 0 0 0 0 2zm18 0h2a1 1 0 0 0 0-2h-2a1 1 0 0 0 0 2zM11 2v2a1 1 0 0 0 2 0V2a1 1 0 0 0-2 0zm0 18v2a1 1 0 0 0 2 0v-2a1 1 0 0 0-2 0zM5.99 4.58a1 1 0 0 0-1.41 1.41l1.06 1.06a1 1 0 0 0 1.41-1.41L5.99 4.58zm12.37 12.37a1 1 0 0 0-1.41 1.41l1.06 1.06a1 1 0 0 0 1.41-1.41l-1.06-1.06zm1.06-10.96a1 1 0 0 0-1.41-1.41l-1.06 1.06a1 1 0 0 0 1.41 1.41l1.06-1.06zM7.05 18.36a1 1 0 0 0-1.41-1.41l-1.06 1.06a1 1 0 0 0 1.41 1.41l1.06-1.06z"/></svg>
        </button>
      </span>
  </div>
</header>
<main id="main">
<?php if ($flashes): ?>
  <div class="wrap flashes">
    <?php foreach ($flashes as $f): ?>
      <div class="flash flash-<?= e($f['type']) ?>" role="status"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
