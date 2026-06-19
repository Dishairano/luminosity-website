<?php use App\Core\View; use App\Core\Csrf; ?>
<a class="btn btn-ghost btn-sm" href="/admin/facturen">← Facturen</a>
<form method="post" action="/admin/facturen/nieuw" class="card" style="margin-top:14px;max-width:720px">
  <?= Csrf::field() ?>
  <div class="field"><label>Klant</label>
    <select class="input" name="klant_id" required>
      <option value="">— kies klant —</option>
      <?php foreach ($klanten as $k): ?>
        <option value="<?= (int)$k['id'] ?>"><?= View::e($k['naam']) ?> (<?= View::e($k['email']) ?>)</option>
      <?php endforeach; ?>
    </select>
  </div>
  <h3>Regels</h3>
  <table id="regels">
    <thead><tr><th>Omschrijving</th><th style="width:80px">Aantal</th><th style="width:120px">Prijs (€)</th></tr></thead>
    <tbody>
      <?php for ($i=0;$i<3;$i++): ?>
      <tr>
        <td><input class="input" name="omschrijving[]" placeholder="bijv. Luminosity Pro — maand"></td>
        <td><input class="input" name="aantal[]" value="1"></td>
        <td><input class="input" name="prijs[]" placeholder="0,00"></td>
      </tr>
      <?php endfor; ?>
    </tbody>
  </table>
  <p class="muted small">Btw (21%) wordt automatisch berekend. Lege regels worden genegeerd.</p>
  <button class="btn btn-primary">Factuur aanmaken</button>
</form>
