<?php

namespace App\Services;

use App\Models\Abonnement;
use App\Models\Factuur;
use App\Models\Plan;

/** Bedrijfslogica rond abonnementen en facturen (gedeeld door klant/admin/webhook). */
final class Facturatie
{
    /**
     * Start een abonnement voor een klant op een plan + periode en maakt de
     * eerste (open) factuur aan. Geeft [abonnement_id, factuur_id] terug.
     */
    public static function startAbonnement(int $klantId, array $plan, string $periode): array
    {
        $abId = Abonnement::maak($klantId, (int) $plan['id'], $periode);
        $prijs = $periode === 'jaar' ? (float) $plan['prijs_jaar'] : (float) $plan['prijs_maand'];
        $oms = sprintf('Luminosity %s — %s', $plan['naam'], $periode === 'jaar' ? 'jaarabonnement' : 'maandabonnement');
        $factuurId = Factuur::maak($klantId, $abId, [
            ['omschrijving' => $oms, 'aantal' => 1, 'prijs' => $prijs],
        ]);
        return [$abId, $factuurId];
    }

    /**
     * Verwerkt een geslaagde betaling: zet de factuur op betaald en activeert
     * het bijbehorende abonnement (met een nieuwe volgende-factuurdatum).
     */
    public static function verwerkBetaling(int $factuurId, ?string $molliePaymentId = null): void
    {
        $factuur = Factuur::vind($factuurId);
        if (!$factuur || $factuur['status'] === 'betaald') {
            return;
        }
        Factuur::markeerBetaald($factuurId, $molliePaymentId);

        if (!empty($factuur['abonnement_id'])) {
            $ab = Abonnement::vind((int) $factuur['abonnement_id']);
            if ($ab) {
                $interval = $ab['periode'] === 'jaar' ? '+1 year' : '+1 month';
                $volgende = date('Y-m-d', strtotime($interval));
                Abonnement::activeer((int) $ab['id'], $volgende);
            }
        }
    }
}
