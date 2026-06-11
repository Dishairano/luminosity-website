<?php use App\Core\View; use App\Core\Csrf; ?>
<h1>Hoi, <?= View::e(explode(' ', $klant['naam'])[0]) ?> 👋</h1>

<div class="card">
  <h3>Je abonnement</h3>
  <?php if ($abonnement): ?>
    <p style="font-size:1.2rem;margin:.2rem 0">
      <strong><?= View::e($abonnement['plan_naam']) ?></strong>
      <span class="badge b-<?= View::e($abonnement['status']) ?>"><?= View::e(str_replace('_',' ',$abonnement['status'])) ?></span>
    </p>
    <p class="muted small">
      Periode: <?= View::e($abonnement['periode']) ?>
      <?php if ($abonnement['volgende_factuur'] && $abonnement['status']==='actief'): ?>
        · Volgende factuur: <?= View::e($abonnement['volgende_factuur']) ?>
      <?php endif; ?>
    </p>
    <div class="row" style="margin-top:12px;max-width:420px">
      <a class="btn btn-ghost btn-sm" href="/prijzen" style="flex:0">Wijzig plan</a>
      <?php if ($abonnement['status'] === 'actief'): ?>
        <form method="post" action="/portaal/abonnement/opzeggen" class="inline" style="flex:0"
              onsubmit="return confirm('Abonnement opzeggen?')">
          <?= Csrf::field() ?><button class="btn btn-danger btn-sm">Opzeggen</button>
        </form>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <p class="muted">Je hebt nog geen abonnement. <a href="/prijzen">Bekijk de abonnementen →</a></p>
  <?php endif; ?>
</div>

<div class="card">
  <div class="toolbar"><h3 style="margin:0">Recente facturen</h3><a class="btn btn-ghost btn-sm" href="/portaal/facturen">Alle facturen</a></div>
  <?php if ($facturen): ?>
    <table>
      <thead><tr><th>Nummer</th><th>Datum</th><th>Bedrag</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($facturen as $f): ?>
        <tr>
          <td><code><?= View::e($f['nummer']) ?></code></td>
          <td><?= View::e($f['uitgiftedatum']) ?></td>
          <td>€<?= number_format((float)$f['totaal'],2,',','.') ?></td>
          <td><span class="badge b-<?= View::e($f['status']) ?>"><?= View::e($f['status']) ?></span></td>
          <td><a href="/portaal/facturen/<?= (int)$f['id'] ?>">Bekijken</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?><p class="muted">Nog geen facturen.</p><?php endif; ?>
</div>
