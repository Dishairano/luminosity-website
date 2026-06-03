# Luminosity – productwebsite & klantportaal

Proefexamen Software Development (MBO-3, klas 2A) — **viermansproject** (met toestemming):
**Dishairano · Kenji · Vinicius · Giovanni**.

Wij bouwen de **website voor Luminosity**, een bedrijf dat een Linux-besturingssysteem maakt dat je in
gewone taal bedient. Het besturingssysteem maakt het bedrijf zelf; **wij bouwen de website**: een
marketingsite + een klantportaal (accounts, abonnementen, betalen via Mollie, facturen) + een adminpaneel.

## Techniek
- **PHP (eigen MVC-framework)** — front controller + router, controllers, models, views
- **MariaDB** database
- **Mollie** voor online betalingen
- Frontend: HTML / CSS / JavaScript

## Mappen
```
app/
  Core/         framework (Router, Controller, View, Auth, Csrf, Model, Database)
  Controllers/  o.a. KlantAuth, Portaal, AdminAuth, Admin, Webhook
  Models/       Klant, Admin, Plan, Abonnement, Factuur, Betaling
  Views/        marketing, portaal (klant), admin
  Services/     Mollie, Facturatie
config/         configuratie
public/         index.php (front controller) + assets
sql/            database-schema (portal.sql)
examen/         alle inleverstukken (voorstellen, planning, ontwerp, testrapport, reflectie)
```

## Lokaal draaien
```bash
composer dump-autoload          # genereert vendor/autoload.php (geen externe deps)
# importeer sql/schema.sql en sql/portal.sql in een MariaDB-database 'luminosity'
php -S 127.0.0.1:8080 -t public
```

## Examen
Alle deliverables staan in [`examen/`](examen/README.md): de 4 examenvoorstellen, de planning, de
ontwerpdocumenten (ERD, flowcharts, klassediagram, wireframes, sitemap), de testrapporten en de reflectie.

## Taakverdeling
Iedereen doet **alle werkprocessen**; ieder bezit een eigen deel: Dishairano = accounts/login + MVC-core,
Kenji = frontend/huisstijl, Vinicius = abonnementen/betalingen, Giovanni = adminpaneel.
