<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\LogEntry;

/**
 * De landingspagina: uitleg over Luminosity OS, de features, een downloadlink
 * naar de ISO-release en de interactieve "probeer het"-sectie.
 */
final class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('home', [
            'titel'   => 'Luminosity OS — je computer besturen in gewone taal',
            'logboek' => LogEntry::recent(8),
        ]);
    }
}
