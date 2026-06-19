<?php use App\Core\View; use App\Core\Csrf; $k = $klant; ?>
<a class="btn btn-ghost btn-sm" href="/admin/klanten">← Klanten</a>
<div class="card" style="margin-top:14px">
  <div class="toolbar">
    <h3 style="margin:0"><?= View::e($k['naam']) ?> <span class="badge b-<?= View::e($k['status']) ?>"><?= View::e($k['status']) ?></span></h3>
    <form method="post" action="/admin/klanten/<?= (int)$k['id'] ?>/status" class="inline">
      <?= Csrf::field() ?>
      <?php if ($k['status'] === 'actief'): ?>
        <input type="hidden" name="status" value="geschorst"><button class="btn btn-danger btn-sm">Schorsen</button>
      <?php else: ?>
        <input type="hidden" name="status" value="actief"><button class="btn btn-primary btn-sm">Heractiveren</button>
      <?php endif; ?>
    </form>
  </div>
  <div class="grid">
    <div><div class="l muted small">E-mail</div><?= View::e($k['email']) ?></div>
    <div><div class="l muted small">Bedrijf</div><?= View::e($k['bedrijf']) ?: '—' ?></div>
    <div><div class="l muted small">Btw</div><?= View::e($k['btw_nummer']) ?: '—' ?></div>
    <div><div class="l muted small">Telefoon</div><?= View::e($k['telefoon']) ?: '—' ?></div>
    <div><div class="l muted small">Adres</div><?= View::e(trim(($k['adres']??'').' '.($k['postcode']??'').' '.($k['stad']??''))) ?: '—' ?></div>
    <div><div class="l muted small">Land</div><?= View::e($k['land']) ?></div>
  </div>
</div>

<div class="card">
  <h3>Abonnement</h3>
  <?php if ($abonnement): ?>
    <p><strong><?= View::e($abonnement['plan_naam']) ?></strong> (<?= View::e($abonnement['periode']) ?>)
      <span class="badge b-<?= View::e($abonnement['status']) ?>"><?= View::e(str_replace('_',' ',$abonnement['status'])) ?></span></p>
  <?php else: ?><p class="muted">Geen abonnement.</p><?php endif; ?>
</div>

<div class="card">
  <h3>Facturen</h3>
  <?php if ($facturen): ?>
    <table><thead><tr><th>Nummer</th><th>Bedrag</th><th>Status</th><th>Datum</th></tr></thead><tbody>
    <?php foreach ($facturen as $f): ?>
      <tr><td><a href="/admin/facturen/<?= (int)$f['id'] ?>"><code><?= View::e($f['nummer']) ?></code></a></td>
      <td>€<?= number_format((float)$f['totaal'],2,',','.') ?></td>
      <td><span class="badge b-<?= View::e($f['status']) ?>"><?= View::e($f['status']) ?></span></td>
      <td class="muted small"><?= View::e($f['uitgiftedatum']) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table>
  <?php else: ?><p class="muted">Geen facturen.</p><?php endif; ?>
</div>
