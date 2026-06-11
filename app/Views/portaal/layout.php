<?php use App\Core\View; use App\Core\Csrf; use App\Core\Controller; $flash = Controller::neemFlash(); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= View::e($titel ?? 'Mijn Luminosity') ?></title>
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/assets/css/portaal.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><text y='26' font-size='26'>☀</text></svg>">
</head>
<body class="portaal">
  <header class="pbar">
    <a class="brand" href="/portaal">☀ Luminosity<span>OS</span></a>
    <nav>
      <a href="/portaal"><?= ($actief ?? '') === 'dashboard' ? '●' : '' ?> Dashboard</a>
      <a href="/portaal/downloads">Downloads</a>
      <a href="/portaal/facturen">Facturen</a>
      <a href="/portaal/profiel">Profiel</a>
      <a href="/prijzen">Abonnementen</a>
    </nav>
    <form method="post" action="/uitloggen" class="inline"><?= Csrf::field() ?>
      <button class="btn btn-ghost btn-sm">Uitloggen</button>
    </form>
  </header>

  <main class="pwrap">
    <?php foreach ($flash as $f): ?>
      <div class="flash flash-<?= View::e($f['type']) ?>"><?= View::e($f['bericht']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </main>

  <footer class="pfoot muted small">Luminosity OS · klantportaal</footer>
</body>
</html>
