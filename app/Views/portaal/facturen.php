<?php use App\Core\View; ?>
<h1>Facturen</h1>
<div class="card">
<?php if ($facturen): ?>
  <table>
    <thead><tr><th>Nummer</th><th>Datum</th><th>Vervalt</th><th>Bedrag</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($facturen as $f): ?>
      <tr>
        <td><code><?= View::e($f['nummer']) ?></code></td>
        <td><?= View::e($f['uitgiftedatum']) ?></td>
        <td><?= View::e($f['vervaldatum']) ?></td>
        <td>€<?= number_format((float)$f['totaal'],2,',','.') ?></td>
        <td><span class="badge b-<?= View::e($f['status']) ?>"><?= View::e($f['status']) ?></span></td>
        <td>
          <a href="/portaal/facturen/<?= (int)$f['id'] ?>">Bekijken</a>
          <?php if ($f['status'] === 'open'): ?>
            · <a href="/portaal/facturen/<?= (int)$f['id'] ?>/betalen">Betalen</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?><p class="muted">Je hebt nog geen facturen.</p><?php endif; ?>
</div>
