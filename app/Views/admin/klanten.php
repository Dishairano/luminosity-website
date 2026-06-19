<?php use App\Core\View; ?>
<div class="toolbar">
  <form method="get" action="/admin/klanten" class="row" style="flex:1;max-width:360px">
    <input class="input" name="q" placeholder="Zoek op naam, e-mail, bedrijf" value="<?= View::e($zoek) ?>">
    <button class="btn btn-ghost btn-sm" style="flex:0">Zoek</button>
  </form>
</div>
<div class="card">
  <table>
    <thead><tr><th>#</th><th>Naam</th><th>E-mail</th><th>Bedrijf</th><th>Status</th><th>Sinds</th></tr></thead>
    <tbody>
    <?php foreach ($klanten as $k): ?>
      <tr>
        <td><?= (int)$k['id'] ?></td>
        <td><a href="/admin/klanten/<?= (int)$k['id'] ?>"><?= View::e($k['naam']) ?></a></td>
        <td><?= View::e($k['email']) ?></td>
        <td><?= View::e($k['bedrijf']) ?: '—' ?></td>
        <td><span class="badge b-<?= View::e($k['status']) ?>"><?= View::e($k['status']) ?></span></td>
        <td class="muted small"><?= View::e(substr($k['aangemaakt'],0,10)) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$klanten): ?><tr><td colspan="6" class="muted">Geen klanten gevonden.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
