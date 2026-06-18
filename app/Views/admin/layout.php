<?php use App\Core\View; use App\Core\Csrf; use App\Core\Controller; $flash = Controller::neemFlash(); $a = $actief ?? ''; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= View::e($titel ?? 'Admin') ?> — Luminosity Admin</title>
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/assets/css/portaal.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><text y='26' font-size='26'>☀</text></svg>">
</head>
<body class="admin">
  <aside class="asidebar">
    <a class="brand" href="/admin">☀ Luminosity<span>Admin</span></a>
    <nav>
      <a href="/admin" class="<?= $a==='dashboard'?'on':'' ?>">▦ Dashboard</a>
      <a href="/admin/klanten" class="<?= $a==='klanten'?'on':'' ?>">👤 Klanten</a>
      <a href="/admin/abonnementen" class="<?= $a==='abonnementen'?'on':'' ?>">↻ Abonnementen</a>
      <a href="/admin/facturen" class="<?= $a==='facturen'?'on':'' ?>">🧾 Facturen</a>
      <a href="/admin/plannen" class="<?= $a==='plannen'?'on':'' ?>">◆ Plannen</a>
    </nav>
    <form method="post" action="/admin/uitloggen" class="aside-foot"><?= Csrf::field() ?>
      <button class="btn btn-ghost btn-sm">Uitloggen</button>
    </form>
  </aside>

  <main class="acontent">
    <h1 class="apagetitel"><?= View::e($titel ?? '') ?></h1>
    <?php foreach ($flash as $f): ?>
      <div class="flash flash-<?= View::e($f['type']) ?>"><?= View::e($f['bericht']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </main>
</body>
</html>
