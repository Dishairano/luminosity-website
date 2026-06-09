<?php use App\Core\View; ?>
<section class="hero">
    <div class="hero-inner">
        <h1>404</h1>
        <p class="lead">De pagina <code><?= View::e($pad ?? '') ?></code> bestaat niet.</p>
        <a class="btn btn-primary" href="/">← Terug naar de startpagina</a>
    </div>
</section>
