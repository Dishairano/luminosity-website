<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($titel ?? 'Luminosity OS') ?></title>
    <meta name="description" content="Luminosity OS — een Arch-gebaseerd besturingssysteem dat je volledig in gewone taal bedient.">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><text y='26' font-size='26'>☀</text></svg>">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="/">☀ Luminosity<span>OS</span></a>
        <nav>
            <a href="/#features">Features</a>
            <a href="/#hoe">Hoe het werkt</a>
            <a href="/#download">Download</a>
            <a href="/prijzen">Abonnementen</a>
            <a href="https://github.com/Dishairano/Luminosity-AI-Codebase" target="_blank" rel="noopener">GitHub</a>
            <a href="/inloggen" class="btn btn-ghost" style="padding:8px 16px;margin-left:6px">Inloggen</a>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer class="footer">
        <div class="col">
            <a class="brand" href="/">☀ Luminosity<span>OS</span></a>
            <p class="muted">Linux in gewone taal. Een lokale, offline taal-engine — geen cloud-AI, geen API-sleutel.</p>
        </div>
        <div class="col">
            <h4>Product</h4>
            <a href="/#features">Features</a>
            <a href="/#hoe">Hoe het werkt</a>
            <a href="/prijzen">Abonnementen</a>
            <a href="/#download">Download ISO</a>
        </div>
        <div class="col">
            <h4>Account &amp; meer</h4>
            <a href="/inloggen">Inloggen</a>
            <a href="/registreren">Account aanmaken</a>
            <a href="https://github.com/Dishairano/Luminosity-AI-Codebase" target="_blank" rel="noopener">GitHub</a>
        </div>
    </footer>
    <div class="footer-bottom">
        <span>© 2026 Luminosity OS · Gemaakt door Kenji, Vinicius &amp; Dishairano</span>
        <span>Gebouwd op Arch Linux · Hyprland · Quickshell</span>
    </div>
</body>
</html>
