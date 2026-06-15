<?php

namespace App\Models;

use App\Core\Model;

final class Abonnement extends Model
{
    public static function vind(int $id): ?array
    {
        return self::rij('SELECT * FROM abonnementen WHERE id = ?', [$id]);
    }

    /** Abonnement + plangegevens (join). */
    public static function vindMetPlan(int $id): ?array
    {
        return self::rij(
            'SELECT a.*, p.naam AS plan_naam, p.code AS plan_code, p.prijs_maand, p.prijs_jaar
             FROM abonnementen a JOIN plannen p ON p.id = a.plan_id WHERE a.id = ?', [$id]
        );
    }

    /** Het (meest recente) abonnement van een klant, met plan. */
    public static function vanKlant(int $klantId): ?array
    {
        return self::rij(
            'SELECT a.*, p.naam AS plan_naam, p.code AS plan_code, p.prijs_maand, p.prijs_jaar
             FROM abonnementen a JOIN plannen p ON p.id = a.plan_id
             WHERE a.klant_id = ? ORDER BY a.id DESC LIMIT 1', [$klantId]
        );
    }

    public static function maak(int $klantId, int $planId, string $periode): int
    {
        return self::voerUit(
            'INSERT INTO abonnementen (klant_id, plan_id, periode, status) VALUES (?,?,?,?)',
            [$klantId, $planId, $periode, 'niet_betaald']
        );
    }

    public static function activeer(int $id, string $volgendeFactuur): void
    {
        self::voerUit(
            'UPDATE abonnementen SET status=?, start_datum=COALESCE(start_datum, CURDATE()), volgende_factuur=? WHERE id=?',
            ['actief', $volgendeFactuur, $id]
        );
    }

    public static function zegOp(int $id): void
    {
        self::voerUit("UPDATE abonnementen SET status='opgezegd', opgezegd_op=NOW() WHERE id=?", [$id]);
    }

    public static function zetStatus(int $id, string $status): void
    {
        self::voerUit('UPDATE abonnementen SET status=? WHERE id=?', [$status, $id]);
    }

    /** @return array[] alle abonnementen met klant + plan, voor het admin-overzicht */
    public static function alleMetDetails(): array
    {
        return self::rijen(
            'SELECT a.*, k.naam AS klant_naam, k.email AS klant_email, p.naam AS plan_naam
             FROM abonnementen a
             JOIN klanten k ON k.id = a.klant_id
             JOIN plannen p ON p.id = a.plan_id
             ORDER BY a.id DESC'
        );
    }

    public static function aantalActief(): int
    {
        return (int) self::waarde("SELECT COUNT(*) FROM abonnementen WHERE status='actief'");
    }
}
