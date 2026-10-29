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
<link rel="stylesheet" href="/assets/pro.css?v=14">
<link rel="stylesheet" href="/assets/scenes.css?v=3">
<link rel="stylesheet" href="/assets/modern.css?v=1">
<link rel="stylesheet" href="/assets/visual.css?v=1">
<link rel="stylesheet" href="/assets/study-lab.css?v=1">
<link rel="stylesheet" href="/assets/academy.css?v=7">
<link rel="stylesheet" href="/assets/premium-foundation.css?v=1">
<?php if ($path === '/' || $path === '/index.php'): ?><link rel="stylesheet" href="/assets/home.css?v=1"><?php endif; ?>
<meta name="theme-color" content="#edf7ff">
<script>(function(){try{if(localStorage.getItem('mt_motion')==='off')document.documentElement.dataset.motion='off';var t=localStorage.getItem('ip_theme');if(!t)t=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';document.documentElement.dataset.theme=t;}catch(e){}})();</script>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%23862c39'/><rect x='6' y='18' width='20' height='6' fill='%23e4c49a'/><text x='16' y='22' font-family='Arial' font-weight='700' font-size='15' text-anchor='middle' fill='white'>En</text></svg>">
<style>.mt-selection-menu{position:fixed;z-index:9999;display:grid;gap:4px;min-width:235px;padding:7px;background:var(--surface,#fff);color:inherit;border:1px solid #a9baca;border-radius:12px;box-shadow:0 12px 32px #0003}.mt-selection-menu[hidden]{display:none}.mt-selection-menu button{background:transparent;color:inherit;text-align:left;border:0;border-radius:7px;padding:9px;cursor:pointer}.mt-selection-menu button:hover{background:rgba(80,130,190,.15)}.dictionary-result{margin-top:18px;padding:17px;border:1px solid #bdcede;border-radius:14px}.dictionary-definition{white-space:pre-wrap;line-height:1.7}.dictionary-result label{display:block;margin-top:12px;font-weight:600}.dictionary-result input,.dictionary-result textarea,.dictionary-result select{display:block;width:100%;box-sizing:border-box;background:var(--surface,#fff);color:inherit;border:1px solid #9db6ca;border-radius:9px;padding:10px}</style><link rel="stylesheet" href="/assets/sidebar.css?v=5">
<link rel="stylesheet" href="/assets/nav.css?v=2">
</head>
<body class="site-modern<?= $u ? ' has-bottom-nav' : '' ?>">
<a class="skip" href="#main">Saltar al contenido</a>
<?php
$initials = '';
if ($u) {
    foreach (preg_split('/\s+/', trim((string) $u['name'])) ?: [] as $part) {
        if ($part !== '' && mb_strlen($initials) < 2) $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
    if ($initials === '') $initials = mb_strtoupper(mb_substr((string) $u['email'], 0, 1));
    $firstName = explode(' ', trim((string) $u['name']))[0] ?: 'Mi cuenta';
    $fullAccess = has_full_access($u);
}
$navCur = fn(string $p) => $path === $p ? ' aria-current="page"' : '';
?>
<header class="mtn-header<?= $u ? ' is-app' : '' ?>">
  <div class="mtn-bar">
<?php if ($u): ?>
    <button type="button" class="mt-sidebar-toggle mtn-icon-btn" aria-label="Mostrar u ocultar el menú" aria-controls="mt-sidebar" aria-expanded="true"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
<?php endif; ?>
    <a class="mtn-brand" href="<?= $u ? '/dashboard.php' : '/' ?>"><span class="mtn-brand-mark" aria-hidden="true">mt</span><span><?= e(app_name()) ?></span></a>
<?php if (!$u): ?>
    <nav class="mtn-nav" id="mtn-nav" aria-label="Principal">
      <a href="/program.php"<?= $navCur('/program.php') ?>>El programa</a>
      <a href="/library.php"<?= $navCur('/library.php') ?>>Biblioteca</a>
      <a href="/situations.php"<?= $navCur('/situations.php') ?>>Situaciones</a>
      <a href="/#precio">Precio</a>
      <span class="mtn-nav-sep" aria-hidden="true"></span>
      <a class="mtn-login" href="/login.php"<?= $navCur('/login.php') ?>>Ingresar</a>
    </nav>
<?php endif; ?>
    <div class="mtn-end">
      <span class="tools mtn-tools">
        <button type="button" class="tool-btn mtn-icon-btn mtn-sound" id="tool-sound" aria-pressed="true" aria-label="Efectos de sonido" title="Efectos de sonido">
          <svg class="on" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9v6h4l5 4V5L8 9H4z"/><path d="M16.5 8.5a5 5 0 0 1 0 7M19 6a8.5 8.5 0 0 1 0 12"/></svg>
          <svg class="off" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9v6h4l5 4V5L8 9H4z"/><path d="m17 9 5 6M22 9l-5 6"/></svg>
        </button>
        <button type="button" class="tool-btn mtn-icon-btn" id="tool-theme" aria-pressed="false" aria-label="Modo oscuro" title="Modo claro / oscuro">
          <svg class="off" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/></svg>
          <svg class="on" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>
      </span>
<?php if ($u): ?>
      <a class="mtn-user" href="/account.php" aria-label="Mi cuenta: <?= e($u['name']) ?>"<?= $navCur('/account.php') ?>>
        <span class="mtn-avatar" aria-hidden="true"><?= e($initials) ?></span>
        <span class="mtn-user-text"><b><?= e($firstName) ?></b><small><?= $fullAccess ? (is_admin($u) ? 'Administrador' : 'Plan completo') : 'Demo gratuita' ?></small></span>
      </a>
<?php else: ?>
      <a class="mtn-cta" href="/register.php">Empezar<span class="mtn-cta-long">&nbsp;gratis</span></a>
      <button type="button" class="mtn-icon-btn mtn-menu-btn" aria-label="Abrir menú" aria-controls="mtn-nav" aria-expanded="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
<?php endif; ?>
    </div>
  </div>
</header>
<?php if ($u): ?>
<div class="mt-sidebar-backdrop" data-sidebar-close hidden></div>
<aside id="mt-sidebar" class="mt-sidebar mtn-sidebar" aria-label="Navegación de estudiante">
  <a class="mtn-side-user" href="/account.php">
    <span class="mtn-avatar" aria-hidden="true"><?= e($initials) ?></span>
    <span><b><?= e($u['name']) ?></b><small><?= e($u['email']) ?></small></span>
  </a>
  <nav class="mt-sidebar-links" aria-label="Secciones de MyTeacher">
    <p class="mt-sidebar-heading">APRENDIZAJE</p>
    <a href="/dashboard.php"<?= $navCur('/dashboard.php') ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg><span>Mis niveles</span></a>
<?php
    // Desglose de los libros dentro del menú: Libros de estudio > nivel > capítulos.
    require_once ROOT . '/src/study_book.php';
    $menuBookCode = $path === '/book.php' ? (is_string($_GET['code'] ?? null) ? strtolower($_GET['code']) : 'a1') : '';
    $menuBookChapter = $path === '/book.php' && is_string($_GET['chapter'] ?? null) ? $_GET['chapter'] : '';
    $menuBooks = [];
    foreach (q_all('SELECT id, slug, title, level_code, position FROM units WHERE is_published = 1 ORDER BY position, id') as $menuUnit) {
        $menuBooks[$menuUnit['level_code']][] = $menuUnit;
    }
?>
    <details class="mt-side-group" data-side-group="books"<?= $path === '/book.php' ? ' open' : '' ?>>
      <summary<?= $path === '/book.php' ? ' class="is-current"' : '' ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg><span>Libros de estudio</span><svg class="mt-side-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></summary>
      <div class="mt-side-tree">
<?php foreach (['a1', 'a2', 'b1', 'b2', 'c1', 'c2'] as $menuCode): $menuLevel = study_level($menuCode); $menuIsLevel = $menuBookCode === $menuCode; ?>
        <details class="mt-side-level" name="mt-book-levels"<?= $menuIsLevel ? ' open' : '' ?>>
          <summary<?= $menuIsLevel ? ' class="is-current"' : '' ?>><b><?= e(strtoupper($menuCode)) ?></b><span title="<?= e($menuLevel['title'] ?? '') ?>"><?= e($menuLevel['title'] ?? '') ?></span><svg class="mt-side-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></summary>
          <ol>
            <li><a href="/book.php?code=<?= e($menuCode) ?>"<?= $menuIsLevel && $menuBookChapter === '' ? ' aria-current="page"' : '' ?>><span>Guía de estudio y proyecto</span></a></li>
<?php foreach ($menuBooks[$menuCode] ?? [] as $menuUnit): $menuOpen = can_access_unit($u, $menuUnit); ?>
            <li><a href="/book.php?code=<?= e($menuCode) ?>&amp;chapter=<?= e($menuUnit['slug']) ?>"<?= $menuIsLevel && $menuBookChapter === $menuUnit['slug'] ? ' aria-current="page"' : '' ?><?= $menuOpen ? '' : ' class="is-locked" title="Con suscripción"' ?>><em><?= (int) $menuUnit['position'] ?></em><span><?= e($menuUnit['title']) ?></span><?php if (!$menuOpen): ?><svg class="mt-side-lock" viewBox="0 0 24 24" aria-label="Con suscripción"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg><?php endif; ?></a></li>
<?php endforeach; ?>
          </ol>
        </details>
<?php endforeach; ?>
      </div>
    </details>
    <details class="mt-side-group mt-side-group-extra" data-side-group="gramatica"<?= $path === '/grammar.php' ? ' open' : '' ?>>
      <summary<?= $path === '/grammar.php' ? ' class="is-current"' : '' ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 19 6-14 6 14M6 15h8M17 8h5M19.5 8v11"/></svg><span>Gramática</span><svg class="mt-side-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></summary>
      <div class="mt-side-tree mt-side-quicklinks">
      <?php foreach (['a1','a2','b1','b2','c1','c2'] as $gc): $gu = $menuBooks[$gc][0] ?? null; ?>
      <?php if ($gu): ?><a href="/grammar.php?id=<?= (int) $gu['id'] ?>"><?= e(strtoupper($gc)) ?> · Primera unidad</a><?php endif; ?>
<?php endforeach; ?>
      </div>
    </details>
    <details class="mt-side-group mt-side-group-extra" data-side-group="practicas"<?= $path === '/review.php' ? ' open' : '' ?>>
      <summary><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 12 5 5L20 6"/></svg><span>Prácticas</span><svg class="mt-side-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></summary>
      <div class="mt-side-tree mt-side-quicklinks">
      <a href="/review.php?m=errores">Corregir errores</a>
      <a href="/review.php?m=refuerzo">Reforzar aprendizajes</a>
      <a href="/dashboard.php">Prácticas por nivel</a>
      </div>
    </details>
    <a href="/glossary.php"<?= $navCur('/glossary.php') ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h5"/></svg><span>Mi glosario</span></a>
    <p class="mt-sidebar-heading">RECURSOS</p>
    <a href="/library.php"<?= $navCur('/library.php') ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 5.5C3.5 4.5 5.5 4 7.5 4S11 4.5 12 5.5V20c-1-1-2.5-1.5-4.5-1.5S3.5 19 2 20zM22 5.5C20.5 4.5 18.5 4 16.5 4S13 4.5 12 5.5V20c1-1 2.5-1.5 4.5-1.5s4 .5 5.5 1.5z"/></svg><span>Biblioteca</span></a>
    <details class="mt-side-group mt-side-group-extra" data-side-group="video-learning">
      <summary><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m10 9 5 3-5 3z"/></svg><span>Video Learning</span><svg class="mt-side-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></summary>
      <div class="mt-side-tree mt-side-quicklinks">
      <?php for ($vl = 1; $vl <= 7; $vl++): ?>
      <a href="/video-learning.php?lesson=<?= $vl ?>">Video A1 · Lección <?= $vl ?></a>
<?php endfor; ?>
      </div>
    </details>
    <details class="mt-side-group mt-side-group-extra" data-side-group="dialogos-reales"<?= $path === '/life-dialogues.php' ? ' open' : '' ?>>
      <summary<?= $path === '/life-dialogues.php' ? ' class="is-current"' : '' ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9 9 0 0 1-4-.9L3 21l1.8-5A8.4 8.4 0 1 1 21 11.5z"/></svg><span>Diálogos reales</span><svg class="mt-side-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></summary>
      <div class="mt-side-tree">
<?php
    // Usar el catálogo real de diálogos sin duplicar situaciones ni inventar enlaces.
    $dialogueMenu = [];
    try {
        foreach (q_all('SELECT slug, level_code, title FROM life_dialogues ORDER BY level_code, slug') as $dialogueRow) {
            $dialogueMenu[strtoupper((string) $dialogueRow['level_code'])][] = $dialogueRow;
        }
    } catch (Throwable $menuError) {
        // La tabla puede no existir hasta la primera visita al simulador.
    }
    $activeDialogueLevel = strtoupper((string) ($_GET['level'] ?? ''));
    $activeDialogueSlug = (string) ($_GET['scene'] ?? '');
    if ($activeDialogueSlug && !$activeDialogueLevel) {
        foreach ($dialogueMenu as $code => $items) {
            foreach ($items as $item) if ($item['slug'] === $activeDialogueSlug) $activeDialogueLevel = $code;
        }
    }
?>
<?php foreach (['A1','A2','B1','B2','C1','C2'] as $dialogueLevel): ?>
        <details class="mt-side-level" name="mt-dialogue-levels"<?= $path === '/life-dialogues.php' && $activeDialogueLevel === $dialogueLevel ? ' open' : '' ?>>
          <summary><b><?= e($dialogueLevel) ?></b><span>Nivel <?= e($dialogueLevel) ?></span><svg class="mt-side-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></summary>
          <ol>
            <li><a href="/life-dialogues.php?level=<?= e($dialogueLevel) ?>">Todas las situaciones</a></li>
<?php foreach ($dialogueMenu[$dialogueLevel] ?? [] as $dialogueItem): ?>
            <li><a href="/life-dialogues.php?level=<?= e($dialogueLevel) ?>&amp;scene=<?= rawurlencode((string) $dialogueItem['slug']) ?>"<?= $path === '/life-dialogues.php' && $activeDialogueSlug === $dialogueItem['slug'] ? ' aria-current="page"' : '' ?>><span><?= e($dialogueItem['title']) ?></span></a></li>
<?php endforeach; ?>
          </ol>
        </details>
<?php endforeach; ?>
      </div>
    </details>
    <a href="/tutor.php"<?= $navCur('/tutor.php') ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 8 6 0M8 5v3c0 5-2 8-5 10M5 12c1 2 4 4 7 5M13 20l5-12 5 12M15 16h6"/></svg><span>Diccionario</span></a>
    <a href="/stats.php"<?= $navCur('/stats.php') ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20V8"/></svg><span>Estadísticas</span></a>
    <a href="/placement.php"<?= $navCur('/placement.php') ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg><span>Test de nivel</span></a>
    <p class="mt-sidebar-heading">CUENTA</p>
    <a href="/account.php"<?= $navCur('/account.php') ?>><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span>Mi cuenta</span></a>
    <?php if (is_admin($u)): ?><a href="/admin/"><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h7"/></svg><span>Administración</span></a><?php endif; ?>
    <form method="post" action="/logout.php"><?= csrf_field() ?><button type="submit"><svg class="mt-side-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg><span>Cerrar sesión</span></button></form>
  </nav>
<?php if (!$fullAccess): ?>
  <div class="mtn-upsell">
    <b>Estás en la demo gratuita</b>
    <span>Desbloqueá todas las unidades, situaciones y talleres.</span>
    <a href="/account.php">Ver plan completo →</a>
  </div>
<?php endif; ?>
</aside>
<?php endif; ?>
<main id="main">
<?php if ($flashes): ?>
  <div class="wrap flashes">
    <?php foreach ($flashes as $f): ?>
      <div class="flash flash-<?= e($f['type']) ?>" role="status"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
