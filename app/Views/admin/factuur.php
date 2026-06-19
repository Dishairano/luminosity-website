<?php use App\Core\View; use App\Core\Csrf; ?>
<a class="btn btn-ghost btn-sm" href="/admin/facturen">← Facturen</a>
<div class="card" style="margin-top:14px">
  <div class="toolbar">
    <h3 style="margin:0">Factuur <?= View::e($factuur['nummer']) ?>
      <span class="badge b-<?= View::e($factuur['status']) ?>"><?= View::e($factuur['status']) ?></span></h3>
    <div class="row" style="flex:0">
      <a class="btn btn-ghost btn-sm" href="/admin/facturen/<?= (int)$factuur['id'] ?>/print" target="_blank">Print / PDF</a>
      <?php if ($factuur['status'] !== 'betaald'): ?>
        <form method="post" action="/admin/facturen/<?= (int)$factuur['id'] ?>/betaald" class="inline">
          <?= Csrf::field() ?><button class="btn btn-primary btn-sm">Markeer als betaald</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <p class="muted small">
    Klant: <a href="/admin/klanten/<?= (int)$factuur['klant_id'] ?>"><?= View::e($factuur['klant_naam']) ?></a>
    (<?= View::e($factuur['klant_email']) ?>) · Uitgegeven <?= View::e($factuur['uitgiftedatum']) ?> · Vervalt <?= View::e($factuur['vervaldatum']) ?>
  </p>
  <table style="margin-top:8px">
    <thead><tr><th>Omschrijving</th><th>Aantal</th><th>Prijs</th><th style="text-align:right">Totaal</th></tr></thead>
    <tbody>
    <?php foreach ($regels as $r): ?>
      <tr><td><?= View::e($r['omschrijving']) ?></td><td><?= (int)$r['aantal'] ?></td>
      <td>€<?= number_format((float)$r['prijs'],2,',','.') ?></td>
      <td style="text-align:right">€<?= number_format((float)$r['regeltotaal'],2,',','.') ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <div style="max-width:280px;margin-left:auto;margin-top:14px">
    <div class="row"><span class="muted">Subtotaal</span><span style="text-align:right">€<?= number_format((float)$factuur['subtotaal'],2,',','.') ?></span></div>
    <div class="row"><span class="muted">Btw</span><span style="text-align:right">€<?= number_format((float)$factuur['btw_bedrag'],2,',','.') ?></span></div>
    <div class="row" style="font-weight:700;font-size:1.1rem"><span>Totaal</span><span style="text-align:right">€<?= number_format((float)$factuur['totaal'],2,',','.') ?></span></div>
  </div>
</div>
