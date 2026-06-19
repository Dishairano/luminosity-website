<?php use App\Core\View; use App\Core\Csrf; ?>
<div class="card">
  <table>
    <thead><tr><th>#</th><th>Klant</th><th>Plan</th><th>Periode</th><th>Status</th><th>Volgende factuur</th><th>Wijzig</th></tr></thead>
    <tbody>
    <?php foreach ($abonnementen as $a): ?>
      <tr>
        <td><?= (int)$a['id'] ?></td>
        <td><a href="/admin/klanten/<?= (int)$a['klant_id'] ?>"><?= View::e($a['klant_naam']) ?></a><div class="muted small"><?= View::e($a['klant_email']) ?></div></td>
        <td><?= View::e($a['plan_naam']) ?></td>
        <td><?= View::e($a['periode']) ?></td>
        <td><span class="badge b-<?= View::e($a['status']) ?>"><?= View::e(str_replace('_',' ',$a['status'])) ?></span></td>
        <td class="muted small"><?= View::e($a['volgende_factuur'] ?: '—') ?></td>
        <td>
          <form method="post" action="/admin/abonnementen/<?= (int)$a['id'] ?>/status" class="row" style="gap:6px">
            <?= Csrf::field() ?>
            <select class="input" name="status" style="padding:5px 8px">
              <?php foreach (['actief','opgezegd','niet_betaald','proef'] as $st): ?>
                <option value="<?= $st ?>" <?= $a['status']===$st?'selected':'' ?>><?= str_replace('_',' ',$st) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-ghost btn-sm" style="flex:0">OK</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$abonnementen): ?><tr><td colspan="7" class="muted">Nog geen abonnementen.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
