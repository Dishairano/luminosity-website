<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Plan;

/** Publieke prijzen-/abonnementenpagina. */
final class PrijzenController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('prijzen', [
            'titel'    => 'Abonnementen — Luminosity OS',
            'plannen'  => Plan::actief(),
            'ingelogd' => Auth::klantId() !== null,
        ]);
    }
}
