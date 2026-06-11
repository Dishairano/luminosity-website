<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= View::e($titel ?? 'Luminosity') ?></title>
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/assets/css/portaal.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><text y='26' font-size='26'>☀</text></svg>">
</head>
<body class="authpage">
  <div class="authcard">
    <a class="brand brand-lg" href="/">☀ Luminosity<span>OS</span></a>
    <?= $content ?>
  </div>
</body>
</html>
