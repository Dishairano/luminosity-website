<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Abonnement;
use App\Models\Factuur;
use App\Models\Klant;
use App\Models\Plan;
use App\Services\Facturatie;

/** WHMCS-achtig adminpaneel in Luminosity-stijl. */
final class AdminController extends Controller
{
    public function dashboard(Request $r): void
    {
        $admin = $this->vereisAdmin();
        $this->view('admin/dashboard', [
            'titel' => 'Dashboard', 'admin' => $admin, 'actief' => 'dashboard',
            'stats' => [
                'klanten'        => Klant::aantal(),
                'nieuw_maand'    => Klant::aantalSinds(date('Y-m-01')),
                'abonnementen'   => Abonnement::aantalActief(),
                'omzet_maand'    => Factuur::omzetMaand(),
                'omzet_totaal'   => Factuur::omzetTotaal(),
                'open_facturen'  => Factuur::aantalOpen(),
                'openstaand'     => Factuur::openstaandBedrag(),
            ],
            'recente_facturen' => Factuur::recent(6),
        ], 'admin/layout');
    }

    // --- Klanten ---
    public function klanten(Request $r): void
    {
        $this->vereisAdmin();
        $zoek = (string) $r->query('q', '');
        $this->view('admin/klanten', [
            'titel' => 'Klanten', 'actief' => 'klanten',
            'klanten' => Klant::alle($zoek), 'zoek' => $zoek,
        ], 'admin/layout');
    }

    public function klant(Request $r, string $id): void
    {
        $this->vereisAdmin();
        $klant = Klant::vind((int) $id);
        if (!$klant) { $this->redirect('/admin/klanten'); }
        $this->view('admin/klant', [
            'titel' => $klant['naam'], 'actief' => 'klanten',
            'klant' => $klant,
            'abonnement' => Abonnement::vanKlant((int) $id),
            'facturen' => Factuur::vanKlant((int) $id),
        ], 'admin/layout');
    }

    public function klantStatus(Request $r, string $id): void
    {
        $this->vereisAdmin();
        $this->checkCsrf($r);
        $status = $r->post('status') === 'geschorst' ? 'geschorst' : 'actief';
        Klant::zetStatus((int) $id, $status);
        $this->flash('ok', 'Klantstatus bijgewerkt.');
        $this->redirect('/admin/klanten/' . $id);
    }

    // --- Abonnementen ---
    public function abonnementen(Request $r): void
    {
        $this->vereisAdmin();
        $this->view('admin/abonnementen', [
            'titel' => 'Abonnementen', 'actief' => 'abonnementen',
            'abonnementen' => Abonnement::alleMetDetails(),
        ], 'admin/layout');
    }

    public function abonnementStatus(Request $r, string $id): void
    {
        $this->vereisAdmin();
        $this->checkCsrf($r);
        $status = $r->post('status');
        if (in_array($status, ['actief', 'opgezegd', 'niet_betaald', 'proef'], true)) {
            Abonnement::zetStatus((int) $id, $status);
            $this->flash('ok', 'Abonnementstatus bijgewerkt.');
        }
        $this->redirect('/admin/abonnementen');
    }

    // --- Facturen ---
    public function facturen(Request $r): void
    {
        $this->vereisAdmin();
        $this->view('admin/facturen', [
            'titel' => 'Facturen', 'actief' => 'facturen',
            'facturen' => Factuur::alleMetKlant(),
        ], 'admin/layout');
    }

    public function factuur(Request $r, string $id): void
    {
        $this->vereisAdmin();
        $factuur = Factuur::vindMetKlant((int) $id);
        if (!$factuur) { $this->redirect('/admin/facturen'); }
        $this->view('admin/factuur', [
            'titel' => 'Factuur ' . $factuur['nummer'], 'actief' => 'facturen',
            'factuur' => $factuur, 'regels' => Factuur::regels((int) $id),
        ], 'admin/layout');
    }

    public function factuurBetaald(Request $r, string $id): void
    {
        $this->vereisAdmin();
        $this->checkCsrf($r);
        Facturatie::verwerkBetaling((int) $id);
        $this->flash('ok', 'Factuur op betaald gezet en abonnement geactiveerd.');
        $this->redirect('/admin/facturen/' . $id);
    }

    public function nieuweFactuurForm(Request $r): void
    {
        $this->vereisAdmin();
        $this->view('admin/factuur-nieuw', [
            'titel' => 'Nieuwe factuur', 'actief' => 'facturen',
            'klanten' => Klant::alle(),
        ], 'admin/layout');
    }

    public function nieuweFactuur(Request $r): void
    {
        $this->vereisAdmin();
        $this->checkCsrf($r);
        $klantId = (int) $r->post('klant_id');
        $oms = $_POST['omschrijving'] ?? [];
        $aantal = $_POST['aantal'] ?? [];
        $prijs = $_POST['prijs'] ?? [];
        $regels = [];
        for ($i = 0; $i < count($oms); $i++) {
            $o = trim((string) ($oms[$i] ?? ''));
            if ($o === '') continue;
            $regels[] = ['omschrijving' => $o, 'aantal' => max(1, (int) ($aantal[$i] ?? 1)), 'prijs' => (float) str_replace(',', '.', (string) ($prijs[$i] ?? 0))];
        }
        if (!$klantId || !$regels) {
            $this->flash('fout', 'Kies een klant en voeg minstens één regel toe.');
            $this->redirect('/admin/facturen/nieuw');
        }
        $id = Factuur::maak($klantId, null, $regels);
        $this->flash('ok', 'Factuur aangemaakt.');
        $this->redirect('/admin/facturen/' . $id);
    }

    // --- Plannen ---
    public function plannen(Request $r): void
    {
        $this->vereisAdmin();
        $this->view('admin/plannen', [
            'titel' => 'Abonnementen (plannen)', 'actief' => 'plannen',
            'plannen' => Plan::alle(),
        ], 'admin/layout');
    }

    public function planOpslaan(Request $r, string $id): void
    {
        $this->vereisAdmin();
        $this->checkCsrf($r);
        Plan::werkBij((int) $id, [
            'naam' => (string) $r->post('naam'),
            'beschrijving' => (string) $r->post('beschrijving'),
            'prijs_maand' => (float) str_replace(',', '.', (string) $r->post('prijs_maand', '0')),
            'prijs_jaar' => (float) str_replace(',', '.', (string) $r->post('prijs_jaar', '0')),
            'features' => (string) $r->post('features', '[]'),
            'populair' => $r->post('populair') ? 1 : 0,
            'actief' => $r->post('actief') ? 1 : 0,
            'sortering' => (int) $r->post('sortering', '0'),
        ]);
        $this->flash('ok', 'Plan opgeslagen.');
        $this->redirect('/admin/plannen');
    }

    // --- Gedeelde printbare factuur (klant + admin) ---
    public function factuurPrint(Request $r, string $id): void
    {
        $this->vereisAdmin();
        $factuur = Factuur::vindMetKlant((int) $id);
        if (!$factuur) { $this->redirect('/admin/facturen'); }
        $this->view('factuur-print', ['factuur' => $factuur, 'regels' => Factuur::regels((int) $id)], null);
    }
}
