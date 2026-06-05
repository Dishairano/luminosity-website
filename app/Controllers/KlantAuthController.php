<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Klant;

/** Registratie, inloggen en uitloggen voor klanten. */
final class KlantAuthController extends Controller
{
    public function toonRegistreren(Request $r): void
    {
        if (Auth::klantId()) { $this->redirect('/portaal'); }
        $this->view('auth/registreren', ['titel' => 'Account aanmaken', 'plan' => $r->query('plan')], 'portaal/auth-layout');
    }

    public function registreren(Request $r): void
    {
        $this->checkCsrf($r);
        $naam = (string) $r->post('naam');
        $email = strtolower((string) $r->post('email'));
        $ww = (string) $r->post('wachtwoord');
        $fouten = [];

        if (mb_strlen($naam) < 2) $fouten[] = 'Vul je naam in.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $fouten[] = 'Ongeldig e-mailadres.';
        if (mb_strlen($ww) < 8) $fouten[] = 'Wachtwoord moet minstens 8 tekens zijn.';
        if (!$fouten && Klant::vindOpEmail($email)) $fouten[] = 'Er bestaat al een account met dit e-mailadres.';

        if ($fouten) {
            $this->view('auth/registreren', [
                'titel' => 'Account aanmaken', 'fouten' => $fouten,
                'oud' => ['naam' => $naam, 'email' => $email], 'plan' => $r->post('plan'),
            ], 'portaal/auth-layout');
            return;
        }

        $id = Klant::maak(['naam' => $naam, 'email' => $email, 'wachtwoord_hash' => password_hash($ww, PASSWORD_DEFAULT)]);
        Auth::login('klant', $id);
        $plan = $r->post('plan');
        $this->redirect($plan ? '/portaal/abonneren/' . urlencode($plan) : '/portaal');
    }

    public function toonInloggen(Request $r): void
    {
        if (Auth::klantId()) { $this->redirect('/portaal'); }
        $this->view('auth/inloggen', ['titel' => 'Inloggen'], 'portaal/auth-layout');
    }

    public function inloggen(Request $r): void
    {
        $this->checkCsrf($r);
        $email = strtolower((string) $r->post('email'));
        $ww = (string) $r->post('wachtwoord');
        $klant = Klant::vindOpEmail($email);

        if (!$klant || !password_verify($ww, $klant['wachtwoord_hash'])) {
            $this->view('auth/inloggen', ['titel' => 'Inloggen', 'fout' => 'Onjuist e-mailadres of wachtwoord.', 'email' => $email], 'portaal/auth-layout');
            return;
        }
        if ($klant['status'] === 'geschorst') {
            $this->view('auth/inloggen', ['titel' => 'Inloggen', 'fout' => 'Dit account is geschorst. Neem contact op.', 'email' => $email], 'portaal/auth-layout');
            return;
        }
        Auth::login('klant', (int) $klant['id']);
        $this->redirect('/portaal');
    }

    public function uitloggen(Request $r): void
    {
        $this->checkCsrf($r);
        Auth::logout('klant');
        $this->redirect('/');
    }
}
