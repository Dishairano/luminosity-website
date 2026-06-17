<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Abonnement;
use App\Models\Factuur;
use App\Models\Klant;
use App\Models\Plan;
use App\Services\Facturatie;
use App\Services\Mollie;

/** Het klantpaneel: dashboard, abonneren, facturen, profiel. */
final class PortaalController extends Controller
{
    public function dashboard(Request $r): void
    {
        $klant = $this->vereisKlant();
        $this->view('portaal/dashboard', [
            'titel'      => 'Mijn Luminosity',
            'klant'      => $klant,
            'abonnement' => Abonnement::vanKlant((int) $klant['id']),
            'facturen'   => array_slice(Factuur::vanKlant((int) $klant['id']), 0, 5),
        ], 'portaal/layout');
    }

    // --- Downloads (achter login, per abonnement) ---
    public function downloads(Request $r): void
    {
        $klant = $this->vereisKlant();
        $ab = Abonnement::vanKlant((int) $klant['id']);
        $planCode = ($ab && $ab['status'] === 'actief') ? $ab['plan_code'] : 'gratis';
        $rang = ['gratis' => 0, 'plus' => 1, 'pro' => 2][$planCode] ?? 0;

        $this->view('portaal/downloads', [
            'titel'    => 'Downloads',
            'klant'    => $klant,
            'planCode' => $planCode,
            'planNaam' => ($ab && $ab['status'] === 'actief') ? $ab['plan_naam'] : 'Gratis',
            'rang'     => $rang,
            'nieuwste' => [
                'versie'  => '0.2.11',
                'bestand' => 'luminosity-0.2.11-x86_64.iso',
                'grootte' => '1,6 GB',
                'sha256'  => '08d07f81460797f3f1250209aacae010735fb72c33b8e1205c8e5e4807daf090',
            ],
            'ouder'    => ['0.2.10', '0.2.9', '0.2.8', '0.2.7', '0.2.6', '0.2.5', '0.2.4', '0.2.3', '0.1.0'],
            'licentie' => $rang >= 2 ? self::licentieSleutel((int) $klant['id']) : null,
        ], 'portaal/layout');
    }

    /** Deterministische licentiesleutel per account (geen opslag nodig). */
    private static function licentieSleutel(int $klantId): string
    {
        $h = strtoupper(substr(hash('sha256', 'LUMI-licentie-v1|' . $klantId), 0, 12));
        return 'LUMI-' . substr($h, 0, 4) . '-' . substr($h, 4, 4) . '-' . substr($h, 8, 4);
    }

    // --- Abonneren ---
    public function abonneren(Request $r, string $code): void
    {
        $klant = $this->vereisKlant();
        $plan = Plan::vindOpCode($code);
        if (!$plan || !$plan['actief']) { $this->redirect('/prijzen'); }

        if ($plan['code'] === 'gratis') {
            // Gratis plan: meteen activeren zonder factuur.
            $abId = Abonnement::maak((int) $klant['id'], (int) $plan['id'], 'maand');
            Abonnement::activeer($abId, date('Y-m-d', strtotime('+1 month')));
            $this->flash('ok', 'Je gebruikt nu het Gratis-abonnement.');
            $this->redirect('/portaal');
        }

        $this->view('portaal/abonneren', [
            'titel' => 'Abonneren op ' . $plan['naam'],
            'klant' => $klant, 'plan' => $plan,
        ], 'portaal/layout');
    }

    public function bevestigAbonnement(Request $r): void
    {
        $klant = $this->vereisKlant();
        $this->checkCsrf($r);
        $plan = Plan::vindOpCode((string) $r->post('code'));
        $periode = $r->post('periode') === 'jaar' ? 'jaar' : 'maand';
        if (!$plan || !$plan['actief']) { $this->redirect('/prijzen'); }

        [, $factuurId] = Facturatie::startAbonnement((int) $klant['id'], $plan, $periode);
        $this->redirect('/portaal/facturen/' . $factuurId . '/betalen');
    }

    // --- Facturen ---
    public function facturen(Request $r): void
    {
        $klant = $this->vereisKlant();
        $this->view('portaal/facturen', [
            'titel' => 'Facturen', 'klant' => $klant,
            'facturen' => Factuur::vanKlant((int) $klant['id']),
        ], 'portaal/layout');
    }

    public function factuur(Request $r, string $id): void
    {
        $klant = $this->vereisKlant();
        $factuur = Factuur::vindMetKlant((int) $id);
        if (!$factuur || (int) $factuur['klant_id'] !== (int) $klant['id']) { $this->redirect('/portaal/facturen'); }
        $this->view('portaal/factuur', [
            'titel' => 'Factuur ' . $factuur['nummer'], 'klant' => $klant,
            'factuur' => $factuur, 'regels' => Factuur::regels((int) $id),
        ], 'portaal/layout');
    }

    /** Start een betaling (Mollie of simulatie) voor een open factuur. */
    public function betalen(Request $r, string $id): void
    {
        $klant = $this->vereisKlant();
        $factuur = Factuur::vind((int) $id);
        if (!$factuur || (int) $factuur['klant_id'] !== (int) $klant['id']) { $this->redirect('/portaal/facturen'); }
        if ($factuur['status'] === 'betaald') { $this->redirect('/portaal/facturen/' . $id); }

        $mollie = new Mollie();
        $res = $mollie->betaalFactuur($factuur);
        if (!empty($res['payment_id'])) {
            Factuur::zetMolliePayment((int) $id, $res['payment_id']);
            \App\Models\Betaling::maak((int) $id, (float) $factuur['totaal'], $res['payment_id'], 'open');
        }
        $this->redirect($res['checkout']);
    }

    /** Mollie stuurt de klant hierheen terug; we controleren de status. */
    public function terugVanBetaling(Request $r, string $id): void
    {
        $klant = $this->vereisKlant();
        $factuur = Factuur::vind((int) $id);
        if (!$factuur || (int) $factuur['klant_id'] !== (int) $klant['id']) { $this->redirect('/portaal/facturen'); }

        if ($factuur['status'] !== 'betaald' && !empty($factuur['mollie_payment_id'])) {
            $st = (new Mollie())->status($factuur['mollie_payment_id']);
            if (($st['status'] ?? '') === 'paid') {
                Facturatie::verwerkBetaling((int) $id, $factuur['mollie_payment_id']);
                \App\Models\Betaling::zetStatus($factuur['mollie_payment_id'], 'paid', $st['method'] ?? null);
            }
        }
        $factuur = Factuur::vind((int) $id);
        $this->flash($factuur['status'] === 'betaald' ? 'ok' : 'info',
            $factuur['status'] === 'betaald' ? 'Betaling geslaagd — bedankt!' : 'Betaling nog niet bevestigd.');
        $this->redirect('/portaal/facturen/' . $id);
    }

    /** Gesimuleerde betaling (alleen als Mollie niet is geconfigureerd). */
    public function simuleer(Request $r, string $id): void
    {
        $klant = $this->vereisKlant();
        $factuur = Factuur::vind((int) $id);
        if (!$factuur || (int) $factuur['klant_id'] !== (int) $klant['id']) { $this->redirect('/portaal/facturen'); }
        if (!(new Mollie())->actief()) {
            Facturatie::verwerkBetaling((int) $id);
            $this->flash('ok', 'Betaling (gesimuleerd) geslaagd — bedankt!');
        }
        $this->redirect('/portaal/facturen/' . $id);
    }

    // --- Profiel ---
    public function profiel(Request $r): void
    {
        $klant = $this->vereisKlant();
        $this->view('portaal/profiel', ['titel' => 'Profiel', 'klant' => $klant], 'portaal/layout');
    }

    public function profielOpslaan(Request $r): void
    {
        $klant = $this->vereisKlant();
        $this->checkCsrf($r);
        Klant::werkProfielBij((int) $klant['id'], [
            'naam' => (string) $r->post('naam', $klant['naam']),
            'bedrijf' => $r->post('bedrijf'), 'btw_nummer' => $r->post('btw_nummer'),
            'adres' => $r->post('adres'), 'postcode' => $r->post('postcode'),
            'stad' => $r->post('stad'), 'land' => (string) $r->post('land', 'Nederland'),
            'telefoon' => $r->post('telefoon'),
        ]);
        $this->flash('ok', 'Profiel opgeslagen.');
        $this->redirect('/portaal/profiel');
    }

    public function factuurPrint(Request $r, string $id): void
    {
        $klant = $this->vereisKlant();
        $factuur = Factuur::vindMetKlant((int) $id);
        if (!$factuur || (int) $factuur['klant_id'] !== (int) $klant['id']) { $this->redirect('/portaal/facturen'); }
        $this->view('factuur-print', ['factuur' => $factuur, 'regels' => Factuur::regels((int) $id)], null);
    }

    public function zegOp(Request $r): void
    {
        $klant = $this->vereisKlant();
        $this->checkCsrf($r);
        $ab = Abonnement::vanKlant((int) $klant['id']);
        if ($ab && $ab['status'] === 'actief') {
            Abonnement::zegOp((int) $ab['id']);
            $this->flash('info', 'Je abonnement is opgezegd. Het blijft actief tot de einddatum.');
        }
        $this->redirect('/portaal');
    }
}
