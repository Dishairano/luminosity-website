<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= View::e($titel ?? 'Admin') ?></title>
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/assets/css/portaal.css">
</head>
<body class="authpage">
  <div class="authcard">
    <div class="brand brand-lg">☀ Luminosity<span>Admin</span></div>
    <?= $content ?>
  </div>
</body>
</html>
