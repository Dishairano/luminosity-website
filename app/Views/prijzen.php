<?php use App\Core\View; use App\Models\Plan; ?>
<section class="hero" style="padding-bottom:0">
  <div class="hero-inner">
    <h1>Kies je <span class="accent">Luminosity</span>-abonnement</h1>
    <p class="lead">De taal-engine is en blijft gratis. Wil je cloud-AI, hogere limieten of API-toegang? Kies een betaald plan.</p>
  </div>
</section>

<section style="max-width:1000px;margin:0 auto;padding:10px 6vw 70px">
  <div class="plans">
    <?php foreach ($plannen as $p): $feat = Plan::features($p); $betaald = $p['code'] !== 'gratis'; ?>
      <div class="plan <?= $p['populair'] ? 'pop' : '' ?>">
        <?php if ($p['populair']): ?><span class="pop-tag">Populair</span><?php endif; ?>
        <h3><?= View::e($p['naam']) ?></h3>
        <p class="muted small"><?= View::e($p['beschrijving']) ?></p>
        <div class="prijs">
          <?= $betaald ? '€' . number_format((float)$p['prijs_maand'], 2, ',', '.') : 'Gratis' ?>
          <?php if ($betaald): ?><small>/ maand</small><?php endif; ?>
        </div>
        <ul>
          <?php foreach ($feat as $f): ?><li><?= View::e($f) ?></li><?php endforeach; ?>
        </ul>
        <?php
          $href = $ingelogd ? '/portaal/abonneren/' . urlencode($p['code'])
                            : '/registreren?plan=' . urlencode($p['code']);
        ?>
        <a class="btn <?= $betaald ? 'btn-primary' : 'btn-ghost' ?>" href="<?= $href ?>">
          <?= $betaald ? 'Kies ' . View::e($p['naam']) : 'Start gratis' ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
  <p class="muted small" style="text-align:center;margin-top:24px">Maandelijks opzegbaar · facturen in je klantportaal · betalen met iDEAL/creditcard via Mollie</p>
</section>
