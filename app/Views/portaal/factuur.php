<?php use App\Core\View; ?>
<div class="toolbar">
  <h1 style="margin:0">Factuur <?= View::e($factuur['nummer']) ?></h1>
  <div class="row" style="flex:0">
    <a class="btn btn-ghost btn-sm" href="/portaal/facturen/<?= (int)$factuur['id'] ?>/print" target="_blank">Print / PDF</a>
    <?php if ($factuur['status'] === 'open'): ?>
      <a class="btn btn-primary btn-sm" href="/portaal/facturen/<?= (int)$factuur['id'] ?>/betalen">Nu betalen</a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <p>
    <span class="badge b-<?= View::e($factuur['status']) ?>"><?= View::e($factuur['status']) ?></span>
    <span class="muted small"> · Uitgegeven <?= View::e($factuur['uitgiftedatum']) ?> · Vervalt <?= View::e($factuur['vervaldatum']) ?></span>
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
    <div class="row"><span class="muted">Btw <?= rtrim(rtrim(number_format((float)$factuur['btw_percentage'],2),'0'),'.') ?>%</span><span style="text-align:right">€<?= number_format((float)$factuur['btw_bedrag'],2,',','.') ?></span></div>
    <div class="row" style="font-weight:700;font-size:1.1rem;border-top:1px solid var(--border);padding-top:8px;margin-top:6px">
      <span>Totaal</span><span style="text-align:right">€<?= number_format((float)$factuur['totaal'],2,',','.') ?></span></div>
  </div>
</div>
<a class="btn btn-ghost btn-sm" href="/portaal/facturen">← Alle facturen</a>
