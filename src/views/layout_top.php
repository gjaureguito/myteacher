<?php
$u = current_user();
$flashes = take_flashes();
$title = ($pageTitle ? $pageTitle . ' — ' : '') . app_name();
?><!doctype html>
<html lang="es-AR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="Aprendé inglés de A1 a C2 con teoría clara en español, práctica corregida al instante y audio en cada ejercicio.">
<meta name="csrf" content="<?= e(csrf_token()) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400&family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/app.css?v=1">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%232747D9'/><rect x='6' y='18' width='20' height='6' fill='%23FFD23F'/><text x='16' y='22' font-family='Arial' font-weight='700' font-size='15' text-anchor='middle' fill='white'>En</text></svg>">
</head>
<body>
<a class="skip" href="#main">Saltar al contenido</a>
<header class="topbar">
  <div class="wrap topbar-in">
    <a class="brand" href="<?= $u ? '/dashboard.php' : '/' ?>"><span class="brand-mark">En</span><?= e(app_name()) ?></a>
    <nav class="nav" aria-label="Principal">
      <?php if ($u): ?>
        <a href="/dashboard.php">Mis niveles</a>
        <a href="/placement.php">Test de nivel</a>
        <a href="/account.php">Mi cuenta</a>
        <?php if (is_admin($u)): ?><a href="/admin/">Admin</a><?php endif; ?>
        <form method="post" action="/logout.php" class="inline"><?= csrf_field() ?><button class="linkbtn">Salir</button></form>
      <?php else: ?>
        <a href="/#niveles">Niveles</a>
        <a href="/#precio">Precio</a>
        <a href="/login.php">Ingresar</a>
        <a class="btn btn-sm" href="/register.php">Probar gratis</a>
      <?php endif; ?>
    </nav>
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
