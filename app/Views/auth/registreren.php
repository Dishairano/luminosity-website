<?php use App\Core\View; use App\Core\Csrf; ?>
<h1>Account aanmaken</h1>
<?php if (!empty($fouten)): ?>
  <div class="flash flash-fout"><?php foreach ($fouten as $f) echo View::e($f) . '<br>'; ?></div>
<?php endif; ?>
<form method="post" action="/registreren">
  <?= Csrf::field() ?>
  <?php if (!empty($plan)): ?><input type="hidden" name="plan" value="<?= View::e($plan) ?>"><?php endif; ?>
  <div class="field"><label>Naam</label>
    <input class="input" name="naam" value="<?= View::e($oud['naam'] ?? '') ?>" required></div>
  <div class="field"><label>E-mailadres</label>
    <input class="input" type="email" name="email" value="<?= View::e($oud['email'] ?? '') ?>" required></div>
  <div class="field"><label>Wachtwoord (min. 8 tekens)</label>
    <input class="input" type="password" name="wachtwoord" required></div>
  <button class="btn btn-primary">Account aanmaken</button>
</form>
<p class="authalt">Al een account? <a href="/inloggen<?= !empty($plan) ? '' : '' ?>">Inloggen</a></p>
