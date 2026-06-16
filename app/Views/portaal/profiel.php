<?php use App\Core\View; use App\Core\Csrf; $k = $klant; ?>
<h1>Profiel &amp; factuurgegevens</h1>
<form method="post" action="/portaal/profiel" class="card" style="max-width:640px">
  <?= Csrf::field() ?>
  <div class="field"><label>Naam</label><input class="input" name="naam" value="<?= View::e($k['naam']) ?>" required></div>
  <div class="field"><label>E-mailadres</label><input class="input" value="<?= View::e($k['email']) ?>" disabled></div>
  <div class="row">
    <div class="field"><label>Bedrijf</label><input class="input" name="bedrijf" value="<?= View::e($k['bedrijf']) ?>"></div>
    <div class="field"><label>Btw-nummer</label><input class="input" name="btw_nummer" value="<?= View::e($k['btw_nummer']) ?>"></div>
  </div>
  <div class="field"><label>Adres</label><input class="input" name="adres" value="<?= View::e($k['adres']) ?>"></div>
  <div class="row">
    <div class="field"><label>Postcode</label><input class="input" name="postcode" value="<?= View::e($k['postcode']) ?>"></div>
    <div class="field"><label>Stad</label><input class="input" name="stad" value="<?= View::e($k['stad']) ?>"></div>
    <div class="field"><label>Land</label><input class="input" name="land" value="<?= View::e($k['land']) ?>"></div>
  </div>
  <div class="field"><label>Telefoon</label><input class="input" name="telefoon" value="<?= View::e($k['telefoon']) ?>"></div>
  <button class="btn btn-primary">Opslaan</button>
</form>
