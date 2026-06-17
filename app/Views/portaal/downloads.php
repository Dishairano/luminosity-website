<?php use App\Core\View; ?>
<h1>Downloads</h1>
<p class="muted">Je bent ingelogd als <strong><?= View::e($klant['naam']) ?></strong> · abonnement:
  <span class="badge b-actief"><?= View::e($planNaam) ?></span></p>

<!-- Placeholder: OS komt binnenkort -->
<div class="card" style="border-color:var(--accent);text-align:center">
  <div style="font-size:2.4rem">☀</div>
  <h2 style="margin:6px 0">Luminosity OS — binnenkort beschikbaar</h2>
  <p class="muted" style="max-width:560px;margin:0 auto 14px">
    We werken aan het volledige besturingssysteem. Zodra het klaar is, kun je de ISO hier downloaden.
    Als abonnee krijg je <strong>als eerste toegang</strong> en bericht bij de lancering.
  </p>
  <span class="badge b-proef">Status: in ontwikkeling</span>
</div>

<!-- Wat je krijgt bij de lancering, per abonnement -->
<div class="card">
  <h3>Wat je bij de lancering krijgt</h3>
  <table>
    <thead><tr><th>Functie</th><th>Gratis</th><th>Plus</th><th>Pro</th></tr></thead>
    <tbody>
      <tr><td>Nieuwste stabiele ISO</td><td>✓</td><td>✓</td><td>✓</td></tr>
      <tr><td>Alle eerdere versies</td><td>—</td><td>✓</td><td>✓</td></tr>
      <tr><td>Cloud-AI-uitleg (in de OS)</td><td>—</td><td>✓</td><td>✓</td></tr>
      <tr><td>Licentiesleutel + prioriteitssupport</td><td>—</td><td>—</td><td>✓</td></tr>
    </tbody>
  </table>
  <?php if ($rang < 2): ?>
    <p style="margin-top:14px"><a class="btn btn-ghost btn-sm" href="/prijzen">Upgrade voor meer →</a></p>
  <?php endif; ?>
</div>

<?php if ($licentie): ?>
<div class="card" style="border-color:var(--accent)">
  <h3>🔑 Jouw Pro-licentiesleutel</h3>
  <p class="muted small">Deze sleutel staat al voor je klaar en activeert straks de Pro-functies in Luminosity OS.</p>
  <p style="font-size:1.4rem;font-weight:700;letter-spacing:2px"><code><?= View::e($licentie) ?></code></p>
</div>
<?php endif; ?>

<div class="card">
  <h3>Nu al uitproberen</h3>
  <p class="muted">De taal-engine werkt vandaag al in je browser — typ een opdracht in gewone taal en zie hoe
    Luminosity die vertaalt, controleert en veilig uitvoert.</p>
  <a class="btn btn-primary btn-sm" href="/#probeer">Probeer de engine →</a>
</div>
