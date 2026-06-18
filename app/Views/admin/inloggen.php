<?php use App\Core\View; use App\Core\Csrf; ?>
<h1>Adminpaneel</h1>
<?php if (!empty($fout)): ?><div class="flash flash-fout"><?= View::e($fout) ?></div><?php endif; ?>
<form method="post" action="/admin/inloggen">
  <?= Csrf::field() ?>
  <div class="field"><label>E-mailadres</label><input class="input" type="email" name="email" value="<?= View::e($email ?? '') ?>" required></div>
  <div class="field"><label>Wachtwoord</label><input class="input" type="password" name="wachtwoord" required></div>
  <button class="btn btn-primary">Inloggen</button>
</form>
