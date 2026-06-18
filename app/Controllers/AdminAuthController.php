<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Admin;

/** Inloggen/uitloggen voor het adminpaneel. */
final class AdminAuthController extends Controller
{
    public function toon(Request $r): void
    {
        if (Auth::adminId()) { $this->redirect('/admin'); }
        $this->view('admin/inloggen', ['titel' => 'Adminpaneel — inloggen'], 'admin/auth-layout');
    }

    public function inloggen(Request $r): void
    {
        $this->checkCsrf($r);
        $email = strtolower((string) $r->post('email'));
        $ww = (string) $r->post('wachtwoord');
        $admin = Admin::vindOpEmail($email);

        if (!$admin || !password_verify($ww, $admin['wachtwoord_hash'])) {
            $this->view('admin/inloggen', ['titel' => 'Adminpaneel — inloggen', 'fout' => 'Onjuiste inloggegevens.', 'email' => $email], 'admin/auth-layout');
            return;
        }
        Auth::login('admin', (int) $admin['id']);
        $this->redirect('/admin');
    }

    public function uitloggen(Request $r): void
    {
        $this->checkCsrf($r);
        Auth::logout('admin');
        $this->redirect('/admin/inloggen');
    }
}
