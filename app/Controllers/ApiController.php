<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\LogEntry;
use App\Services\Engine;
use App\Services\Sandbox;

/**
 * JSON-API voor de interactieve demo. Twee stappen, net als de echte schil:
 *   POST /api/verwerk    — vertaal + veiligheidscheck (voert NIETS uit)
 *   POST /api/uitvoeren  — voer het goedgekeurde commando uit in de sandbox
 *
 * Belangrijk veiligheidsprincipe: /api/uitvoeren vertrouwt NOOIT een commando
 * van de client. Het bepaalt het commando server-side opnieuw via de engine en
 * voert alleen uit als de engine het goedkeurt (mag_uitvoeren én niet leeg).
 */
final class ApiController extends Controller
{
    public function verwerk(Request $request): void
    {
        $invoer = (string)$request->json('invoer', '');
        $result = (new Engine())->verwerk($invoer);

        if (!$result['ok']) {
            $this->json(['ok' => false, 'fout' => $result['fout']], 422);
            return;
        }

        $b = $result['bevestiging'];
        LogEntry::schrijf($invoer, (string)($b['commando'] ?? ''), 'voorbereid');
        $this->json(['ok' => true, 'bevestiging' => $b]);
    }

    public function uitvoeren(Request $request): void
    {
        $invoer = (string)$request->json('invoer', '');

        // 1. Bepaal het commando opnieuw via de engine (niet via de client).
        $result = (new Engine())->verwerk($invoer);
        if (!$result['ok']) {
            $this->json(['ok' => false, 'fout' => $result['fout']], 422);
            return;
        }

        $b = $result['bevestiging'];
        $commando = trim((string)($b['commando'] ?? ''));

        // 2. Alleen uitvoeren als de engine het goedkeurt.
        if (empty($b['mag_uitvoeren']) || $commando === '') {
            LogEntry::schrijf($invoer, $commando, 'geblokkeerd');
            $this->json([
                'ok'        => true,
                'uitgevoerd'=> false,
                'resultaat' => ['gelukt' => false, 'uitvoer' => 'Deze opdracht is niet uitgevoerd.'],
            ]);
            return;
        }

        // 3. Uitvoeren in de wegwerp-sandbox.
        $res = (new Sandbox())->run($commando);
        LogEntry::schrijf($invoer, $commando, $res['gelukt'] ? 'geslaagd' : 'mislukt');

        $this->json([
            'ok'         => true,
            'uitgevoerd' => true,
            'commando'   => $commando,
            'resultaat'  => $res,
        ]);
    }

    public function logboek(Request $request): void
    {
        $this->json(['ok' => true, 'regels' => LogEntry::recent(10)]);
    }
}
