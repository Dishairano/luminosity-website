<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Factuur extends Model
{
    public static function vind(int $id): ?array
    {
        return self::rij('SELECT * FROM facturen WHERE id = ?', [$id]);
    }

    public static function vindMetKlant(int $id): ?array
    {
        return self::rij(
            'SELECT f.*, k.naam AS klant_naam, k.email AS klant_email, k.bedrijf, k.adres,
                    k.postcode, k.stad, k.land, k.btw_nummer
             FROM facturen f JOIN klanten k ON k.id = f.klant_id WHERE f.id = ?', [$id]
        );
    }

    public static function regels(int $factuurId): array
    {
        return self::rijen('SELECT * FROM factuurregels WHERE factuur_id = ? ORDER BY id', [$factuurId]);
    }

    public static function vanKlant(int $klantId): array
    {
        return self::rijen('SELECT * FROM facturen WHERE klant_id = ? ORDER BY id DESC', [$klantId]);
    }

    public static function alleMetKlant(): array
    {
        return self::rijen(
            'SELECT f.*, k.naam AS klant_naam, k.email AS klant_email
             FROM facturen f JOIN klanten k ON k.id = f.klant_id ORDER BY f.id DESC'
        );
    }

    /**
     * Maakt een factuur met regels aan. $regels = [['omschrijving','aantal','prijs'], ...].
     * Berekent subtotaal/btw/totaal. Geeft het factuur-id terug.
     */
    public static function maak(int $klantId, ?int $abonnementId, array $regels, float $btwPct = 21.0, int $vervalDagen = 14): int
    {
        $pdo = Database::pdo();
        if (!$pdo) return 0;

        $subtotaal = 0.0;
        foreach ($regels as $r) {
            $subtotaal += (float)$r['aantal'] * (float)$r['prijs'];
        }
        $btw = round($subtotaal * $btwPct / 100, 2);
        $totaal = round($subtotaal + $btw, 2);

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO facturen (nummer, klant_id, abonnement_id, status, subtotaal, btw_percentage, btw_bedrag, totaal, uitgiftedatum, vervaldatum)
                 VALUES (?,?,?,?,?,?,?,?,CURDATE(), DATE_ADD(CURDATE(), INTERVAL ? DAY))'
            );
            $stmt->execute(['TIJDELIJK', $klantId, $abonnementId, 'open', $subtotaal, $btwPct, $btw, $totaal, $vervalDagen]);
            $id = (int)$pdo->lastInsertId();

            // Definitief factuurnummer op basis van het id.
            $nummer = sprintf('LUM-%d-%05d', (int)date('Y'), $id);
            $pdo->prepare('UPDATE facturen SET nummer=? WHERE id=?')->execute([$nummer, $id]);

            $rstmt = $pdo->prepare(
                'INSERT INTO factuurregels (factuur_id, omschrijving, aantal, prijs, regeltotaal) VALUES (?,?,?,?,?)'
            );
            foreach ($regels as $r) {
                $rt = round((float)$r['aantal'] * (float)$r['prijs'], 2);
                $rstmt->execute([$id, $r['omschrijving'], (int)$r['aantal'], (float)$r['prijs'], $rt]);
            }
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return 0;
        }
    }

    public static function markeerBetaald(int $id, ?string $molliePaymentId = null): void
    {
        self::voerUit(
            "UPDATE facturen SET status='betaald', betaald_op=NOW(), mollie_payment_id=COALESCE(?, mollie_payment_id) WHERE id=?",
            [$molliePaymentId, $id]
        );
    }

    public static function zetStatus(int $id, string $status): void
    {
        self::voerUit('UPDATE facturen SET status=? WHERE id=?', [$status, $id]);
    }

    public static function zetMolliePayment(int $id, string $paymentId): void
    {
        self::voerUit('UPDATE facturen SET mollie_payment_id=? WHERE id=?', [$paymentId, $id]);
    }

    // --- Statistieken voor het admin-dashboard ---
    public static function omzetTotaal(): float
    {
        return (float) self::waarde("SELECT COALESCE(SUM(totaal),0) FROM facturen WHERE status='betaald'");
    }

    public static function omzetMaand(): float
    {
        return (float) self::waarde(
            "SELECT COALESCE(SUM(totaal),0) FROM facturen
             WHERE status='betaald' AND betaald_op >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        );
    }

    public static function aantalOpen(): int
    {
        return (int) self::waarde("SELECT COUNT(*) FROM facturen WHERE status='open'");
    }

    public static function openstaandBedrag(): float
    {
        return (float) self::waarde("SELECT COALESCE(SUM(totaal),0) FROM facturen WHERE status='open'");
    }

    public static function recent(int $n = 6): array
    {
        $n = max(1, min(20, $n));
        return self::rijen(
            "SELECT f.*, k.naam AS klant_naam FROM facturen f
             JOIN klanten k ON k.id=f.klant_id ORDER BY f.id DESC LIMIT $n"
        );
    }
}
