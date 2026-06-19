<?php use App\Core\View; ?>
<div class="toolbar">
  <span class="muted small"><?= count($facturen) ?> facturen</span>
  <a class="btn btn-primary btn-sm" href="/admin/facturen/nieuw">+ Nieuwe factuur</a>
</div>
<div class="card">
  <table>
    <thead><tr><th>Nummer</th><th>Klant</th><th>Bedrag</th><th>Status</th><th>Uitgegeven</th><th>Vervalt</th></tr></thead>
    <tbody>
    <?php foreach ($facturen as $f): ?>
      <tr>
        <td><a href="/admin/facturen/<?= (int)$f['id'] ?>"><code><?= View::e($f['nummer']) ?></code></a></td>
        <td><?= View::e($f['klant_naam']) ?></td>
        <td>€<?= number_format((float)$f['totaal'],2,',','.') ?></td>
        <td><span class="badge b-<?= View::e($f['status']) ?>"><?= View::e($f['status']) ?></span></td>
        <td class="muted small"><?= View::e($f['uitgiftedatum']) ?></td>
        <td class="muted small"><?= View::e($f['vervaldatum']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$facturen): ?><tr><td colspan="6" class="muted">Nog geen facturen.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
