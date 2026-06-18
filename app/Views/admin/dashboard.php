<?php use App\Core\View; $s = $stats; ?>
<div class="grid" style="margin-bottom:20px">
  <div class="stat"><div class="n accent">€<?= number_format($s['omzet_maand'],2,',','.') ?></div><div class="l">Omzet deze maand</div></div>
  <div class="stat"><div class="n">€<?= number_format($s['omzet_totaal'],2,',','.') ?></div><div class="l">Omzet totaal</div></div>
  <div class="stat"><div class="n"><?= (int)$s['abonnementen'] ?></div><div class="l">Actieve abonnementen</div></div>
  <div class="stat"><div class="n"><?= (int)$s['klanten'] ?></div><div class="l">Klanten (<?= (int)$s['nieuw_maand'] ?> nieuw)</div></div>
  <div class="stat"><div class="n"><?= (int)$s['open_facturen'] ?></div><div class="l">Open facturen · €<?= number_format($s['openstaand'],2,',','.') ?></div></div>
</div>

<div class="card">
  <div class="toolbar"><h3 style="margin:0">Recente facturen</h3><a class="btn btn-ghost btn-sm" href="/admin/facturen">Alle facturen</a></div>
  <?php if ($recente_facturen): ?>
    <table>
      <thead><tr><th>Nummer</th><th>Klant</th><th>Bedrag</th><th>Status</th><th>Datum</th></tr></thead>
      <tbody>
      <?php foreach ($recente_facturen as $f): ?>
        <tr>
          <td><a href="/admin/facturen/<?= (int)$f['id'] ?>"><code><?= View::e($f['nummer']) ?></code></a></td>
          <td><?= View::e($f['klant_naam']) ?></td>
          <td>€<?= number_format((float)$f['totaal'],2,',','.') ?></td>
          <td><span class="badge b-<?= View::e($f['status']) ?>"><?= View::e($f['status']) ?></span></td>
          <td class="muted small"><?= View::e($f['uitgiftedatum']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?><p class="muted">Nog geen facturen.</p><?php endif; ?>
</div>
