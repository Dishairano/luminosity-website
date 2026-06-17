<?php use App\Core\View; $f = $factuur; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<title>Factuur <?= View::e($f['nummer']) ?></title>
<style>
  body{font-family:"Segoe UI",Arial,sans-serif;color:#1a1a1a;max-width:760px;margin:30px auto;padding:0 24px}
  .top{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #ffd166;padding-bottom:16px}
  .brand{font-size:1.6rem;font-weight:700}
  .brand span{color:#d9a400}
  h1{font-size:1.3rem;margin:18px 0 4px}
  .meta{color:#555;font-size:.9rem}
  table{width:100%;border-collapse:collapse;margin-top:20px}
  th,td{text-align:left;padding:9px 8px;border-bottom:1px solid #e3e3e3}
  th{font-size:.8rem;text-transform:uppercase;color:#777}
  .r{text-align:right}
  .tot{max-width:260px;margin-left:auto;margin-top:12px}
  .tot div{display:flex;justify-content:space-between;padding:3px 0}
  .tot .grand{font-weight:700;font-size:1.1rem;border-top:2px solid #1a1a1a;margin-top:6px;padding-top:6px}
  .blok{display:flex;justify-content:space-between;margin-top:20px;font-size:.92rem}
  .status{display:inline-block;padding:3px 10px;border-radius:999px;font-size:.78rem;background:#eee}
  .betaald{background:#d8f3df;color:#1f7a3f}
  @media print{ .noprint{display:none} body{margin:0} }
</style>
</head>
<body onload="if(location.hash!=='#nop'){window.print()}">
  <div class="top">
    <div class="brand">☀ Luminosity<span>OS</span></div>
    <div class="meta r">
      <strong>Factuur</strong><br>
      <?= View::e($f['nummer']) ?><br>
      Datum: <?= View::e($f['uitgiftedatum']) ?><br>
      Vervalt: <?= View::e($f['vervaldatum']) ?>
    </div>
  </div>

  <div class="blok">
    <div>
      <strong>Aan</strong><br>
      <?= View::e($f['klant_naam']) ?><br>
      <?php if (!empty($f['bedrijf'])): ?><?= View::e($f['bedrijf']) ?><br><?php endif; ?>
      <?php if (!empty($f['adres'])): ?><?= View::e($f['adres']) ?><br><?php endif; ?>
      <?= View::e(trim(($f['postcode']??'').' '.($f['stad']??''))) ?><br>
      <?= View::e($f['land']) ?>
      <?php if (!empty($f['btw_nummer'])): ?><br>Btw: <?= View::e($f['btw_nummer']) ?><?php endif; ?>
    </div>
    <div class="r">
      <span class="status <?= $f['status']==='betaald'?'betaald':'' ?>"><?= View::e($f['status']) ?></span><br>
      <span class="meta">Luminosity OS<br>luminosity-os.nl</span>
    </div>
  </div>

  <table>
    <thead><tr><th>Omschrijving</th><th>Aantal</th><th class="r">Prijs</th><th class="r">Totaal</th></tr></thead>
    <tbody>
    <?php foreach ($regels as $r): ?>
      <tr><td><?= View::e($r['omschrijving']) ?></td><td><?= (int)$r['aantal'] ?></td>
      <td class="r">€<?= number_format((float)$r['prijs'],2,',','.') ?></td>
      <td class="r">€<?= number_format((float)$r['regeltotaal'],2,',','.') ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <div class="tot">
    <div><span>Subtotaal</span><span>€<?= number_format((float)$f['subtotaal'],2,',','.') ?></span></div>
    <div><span>Btw <?= rtrim(rtrim(number_format((float)$f['btw_percentage'],2),'0'),'.') ?>%</span><span>€<?= number_format((float)$f['btw_bedrag'],2,',','.') ?></span></div>
    <div class="grand"><span>Totaal</span><span>€<?= number_format((float)$f['totaal'],2,',','.') ?></span></div>
  </div>

  <p class="meta" style="margin-top:30px">Bedankt voor je vertrouwen in Luminosity OS.</p>
  <p class="noprint"><button onclick="window.print()">Print / opslaan als PDF</button></p>
</body>
</html>
