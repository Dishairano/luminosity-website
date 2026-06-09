<?php use App\Core\View; ?>

<section class="hero">
    <div class="hero-inner">
        <span class="eyebrow"><span class="dot"></span> Lokaal · Offline · Geen cloud-AI</span>
        <h1>Je computer besturen in <span class="accent">gewone taal</span>.</h1>
        <p class="lead">
            Luminosity OS is een op Arch Linux gebaseerd besturingssysteem. Je typt wat je wilt —
            <em>"installeer firefox"</em>, <em>"maak een back-up"</em>, <em>"hoeveel ruimte heb ik nog"</em> —
            en een lokale, offline taal-engine zet dat om naar het juiste Linux-commando, legt uit wat er
            gebeurt en vraagt eerst om bevestiging.
        </p>
        <div class="hero-cta">
            <a class="btn btn-primary" href="#probeer">Probeer de engine →</a>
            <a class="btn btn-ghost" href="/prijzen">Bekijk abonnementen</a>
        </div>
        <div class="hero-trust">
            <span><b>100%</b> offline</span>
            <span><b>0</b> API-sleutels</span>
            <span><b>Binnenkort</b> als OS</span>
            <span><b>Nu al</b> in de browser</span>
        </div>
    </div>

    <!-- Nagebootste schil als productbeeld -->
    <div class="mockup" aria-hidden="true">
        <div class="mockup-bar">
            <span class="dot r"></span><span class="dot y"></span><span class="dot g"></span>
            <span class="ttl">☀ Luminosity-schil</span>
            <span class="widgets">💾 71% · 9 GB&nbsp;&nbsp;🕐 17:06</span>
        </div>
        <div class="mockup-body">
            <p class="mockup-prompt">› <b>installeer firefox</b></p>
            <div class="mockup-card">
                <div class="t">Klaar om te installeren</div>
                <div class="cmd">$ sudo pacman -S firefox</div>
                <div class="ex">Ik installeer 'firefox' met de pakketbeheerder pacman.</div>
                <div class="acts"><span class="mini ok">Uitvoeren</span><span class="mini no">Annuleren</span></div>
            </div>
        </div>
    </div>
</section>

<section class="techband" style="text-align:center">
    <ul>
        <li>Arch Linux</li><li>·</li><li>Hyprland</li><li>·</li><li>Quickshell (QML)</li>
        <li>·</li><li>Python-engine</li><li>·</li><li>nmcli &amp; rsync</li>
    </ul>
</section>

<section id="features" class="features">
    <h2>Wat maakt het bijzonder?</h2>
    <p class="section-intro">Geen terminalkennis nodig, geen cloud, geen verrassingen. Luminosity vertaalt, legt uit en wacht op jouw akkoord.</p>
    <div class="grid">
        <article class="card">
            <div class="ic">🗣️</div>
            <h3>Gewone taal → commando</h3>
            <p>Een patroon-engine herkent je bedoeling en bouwt het juiste commando. Volledig offline, zonder cloud-AI.</p>
        </article>
        <article class="card">
            <div class="ic">🛡️</div>
            <h3>Veiligheid eerst</h3>
            <p>Gevaarlijke commando's (zoals <code>rm -rf /</code>) worden geblokkeerd; risicovolle acties vragen extra bevestiging.</p>
        </article>
        <article class="card">
            <div class="ic">✅</div>
            <h3>Uitleg &amp; bevestiging</h3>
            <p>Je ziet altijd eerst wát er gaat gebeuren. Pas na jouw akkoord voert het systeem iets uit.</p>
        </article>
        <article class="card">
            <div class="ic">🌐</div>
            <h3>Netwerk &amp; back-up</h3>
            <p>Wifi beheren en back-ups maken/terugzetten — alles via dezelfde veilige pipeline.</p>
        </article>
    </div>
</section>

<section id="hoe">
    <h2>Hoe werkt het?</h2>
    <p class="section-intro">Van zin tot uitgevoerd commando in vier veilige stappen.</p>
    <div class="steps">
        <div class="step"><h3>Typ in gewone taal</h3><p>Bijv. <em>"verbind met wifi Thuis"</em>. De engine herkent je bedoeling via patronen.</p></div>
        <div class="step"><h3>Veiligheidscheck</h3><p>De veiligheidslaag blokkeert gevaarlijke commando's en waarschuwt bij risico.</p></div>
        <div class="step"><h3>Uitleg &amp; bevestiging</h3><p>Je ziet het commando en een begrijpelijke uitleg, en bevestigt zelf.</p></div>
        <div class="step"><h3>Uitvoeren &amp; loggen</h3><p>Pas dán draait het. Alles komt in je logboek, met een ‘ongedaan maken’-suggestie.</p></div>
    </div>
</section>

<section id="probeer" class="try">
    <h2>Probeer het zelf</h2>
    <p class="muted">
        Typ een opdracht in gewone taal. De engine toont het commando, de uitleg en het veiligheidsoordeel.
        Met <strong>Uitvoeren</strong> draait het commando in een geïsoleerde wegwerp-sandbox (geen netwerk,
        strikte limieten) — nooit op deze server zelf.
    </p>

    <div class="shell">
        <div class="shell-top">
            <span>☀ Luminosity</span>
            <span id="status" class="status">klaar</span>
        </div>

        <form id="try-form" class="shell-input" autocomplete="off">
            <input id="invoer" type="text" placeholder="Bijv. installeer firefox" aria-label="Opdracht in gewone taal">
            <button type="submit" class="btn btn-primary">Vertaal</button>
        </form>

        <div class="suggesties">
            <button class="chip">installeer firefox</button>
            <button class="chip">update mijn systeem</button>
            <button class="chip">hoeveel ruimte heb ik nog</button>
            <button class="chip">verwijder spotify</button>
            <button class="chip">rm -rf /</button>
        </div>

        <div id="kaart" class="kaart hidden">
            <h4 id="k-titel"></h4>
            <pre id="k-commando" class="commando"></pre>
            <p id="k-uitleg"></p>
            <p id="k-veiligheid" class="veiligheid"></p>
            <div class="kaart-acties">
                <button id="btn-uitvoeren" class="btn btn-run hidden">Uitvoeren in sandbox</button>
                <span id="k-hint" class="muted"></span>
            </div>
            <pre id="k-resultaat" class="resultaat hidden"></pre>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="inner">
        <h2>Meer dan gratis? Kies een abonnement.</h2>
        <p class="section-intro" style="margin:0 auto 24px">De taal-engine is en blijft gratis. Wil je cloud-AI, hogere limieten of API-toegang? Bekijk Plus en Pro.</p>
        <a class="btn btn-primary btn-big" href="/prijzen">Bekijk abonnementen →</a>
    </div>
</section>

<section id="download" class="download">
    <div class="inner">
        <span class="eyebrow"><span class="dot" style="background:var(--amber)"></span> Binnenkort</span>
        <h2>Luminosity OS komt eraan</h2>
        <p class="muted">We bouwen het volledige besturingssysteem. Nu kun je de taal-engine al in je browser
            proberen; straks draait dezelfde ervaring als echte OS. Maak een account aan, dan krijg je als
            eerste bericht én vroege toegang bij de lancering.</p>
        <p style="margin:22px 0 6px">
            <a class="btn btn-primary btn-big" href="/registreren">Account aanmaken voor vroege toegang →</a>
        </p>
        <p class="muted small">Abonnees krijgen bij lancering als eerste de download. <a href="/inloggen">Al een account? Inloggen →</a></p>
    </div>
</section>

<?php if (!empty($logboek)): ?>
<section class="logboek">
    <h2>Laatst geprobeerd</h2>
    <table>
        <thead><tr><th>Invoer</th><th>Commando</th><th>Status</th><th>Tijd</th></tr></thead>
        <tbody>
        <?php foreach ($logboek as $r): ?>
            <tr>
                <td><?= View::e($r['invoertekst']) ?></td>
                <td><code><?= View::e($r['commando'] ?: '—') ?></code></td>
                <td><span class="badge badge-<?= View::e($r['status']) ?>"><?= View::e($r['status']) ?></span></td>
                <td class="muted small"><?= View::e($r['tijdstip']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php endif; ?>

<script src="/assets/js/app.js" defer></script>
