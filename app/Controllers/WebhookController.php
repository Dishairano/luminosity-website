<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Betaling;
use App\Models\Factuur;
use App\Services\Facturatie;
use App\Services\Mollie;

/** Ontvangt Mollie-webhooks (betaalstatus-updates). */
final class WebhookController extends Controller
{
    public function mollie(Request $r): void
    {
        $paymentId = $r->post('id');
        if (!$paymentId) { http_response_code(200); echo 'ok'; return; }

        $st = (new Mollie())->status($paymentId);
        Betaling::zetStatus($paymentId, $st['status'] ?? 'open', $st['method'] ?? null);

        if (($st['status'] ?? '') === 'paid') {
            $bet = Betaling::vindOpMollie($paymentId);
            $factuur = $bet ? Factuur::vind((int) $bet['factuur_id']) : null;
            if ($factuur) {
                Facturatie::verwerkBetaling((int) $factuur['id'], $paymentId);
            }
        }
        http_response_code(200);
        echo 'ok';
    }
}
