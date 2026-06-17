<?php use App\Core\View; use App\Core\Csrf; use App\Models\Plan; $feat = Plan::features($plan); ?>
<h1>Abonneren op <?= View::e($plan['naam']) ?></h1>
<div class="card" style="max-width:520px">
  <p class="muted"><?= View::e($plan['beschrijving']) ?></p>
  <ul style="list-style:none;padding:0;color:#cfd3e0">
    <?php foreach ($feat as $f): ?><li>✓ <?= View::e($f) ?></li><?php endforeach; ?>
  </ul>
  <form method="post" action="/portaal/abonneren">
    <?= Csrf::field() ?>
    <input type="hidden" name="code" value="<?= View::e($plan['code']) ?>">
    <div class="field"><label>Facturatieperiode</label>
      <select class="input" name="periode">
        <option value="maand">Maandelijks — €<?= number_format((float)$plan['prijs_maand'],2,',','.') ?> / maand</option>
        <option value="jaar">Jaarlijks — €<?= number_format((float)$plan['prijs_jaar'],2,',','.') ?> / jaar</option>
      </select>
    </div>
    <button class="btn btn-primary">Doorgaan naar betaling →</button>
    <a class="btn btn-ghost" href="/prijzen">Terug</a>
  </form>
</div>
