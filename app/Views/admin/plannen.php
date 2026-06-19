<?php use App\Core\View; use App\Core\Csrf; use App\Models\Plan; ?>
<p class="muted small">Beheer de abonnementen (de "betaalde AI's"). Features als JSON-lijst, één regel per feature.</p>
<?php foreach ($plannen as $p): ?>
  <form method="post" action="/admin/plannen/<?= (int)$p['id'] ?>" class="card">
    <?= Csrf::field() ?>
    <div class="toolbar">
      <h3 style="margin:0"><?= View::e($p['naam']) ?> <code class="muted">(<?= View::e($p['code']) ?>)</code></h3>
      <button class="btn btn-primary btn-sm">Opslaan</button>
    </div>
    <div class="row">
      <div class="field"><label>Naam</label><input class="input" name="naam" value="<?= View::e($p['naam']) ?>"></div>
      <div class="field"><label>Sortering</label><input class="input" name="sortering" value="<?= (int)$p['sortering'] ?>"></div>
    </div>
    <div class="field"><label>Beschrijving</label><input class="input" name="beschrijving" value="<?= View::e($p['beschrijving']) ?>"></div>
    <div class="row">
      <div class="field"><label>Prijs / maand (€)</label><input class="input" name="prijs_maand" value="<?= number_format((float)$p['prijs_maand'],2,',','') ?>"></div>
      <div class="field"><label>Prijs / jaar (€)</label><input class="input" name="prijs_jaar" value="<?= number_format((float)$p['prijs_jaar'],2,',','') ?>"></div>
    </div>
    <div class="field"><label>Features (JSON-array)</label>
      <textarea class="input" name="features" rows="4"><?= View::e($p['features']) ?></textarea></div>
    <label class="muted small"><input type="checkbox" name="populair" <?= $p['populair']?'checked':'' ?>> Populair &nbsp;
      <input type="checkbox" name="actief" <?= $p['actief']?'checked':'' ?>> Actief</label>
  </form>
<?php endforeach; ?>
