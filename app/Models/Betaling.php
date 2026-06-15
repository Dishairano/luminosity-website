<?php

namespace App\Models;

use App\Core\Model;

final class Betaling extends Model
{
    public static function maak(int $factuurId, float $bedrag, ?string $molliePaymentId, string $status = 'open'): int
    {
        return self::voerUit(
            'INSERT INTO betalingen (factuur_id, mollie_payment_id, bedrag, status) VALUES (?,?,?,?)',
            [$factuurId, $molliePaymentId, $bedrag, $status]
        );
    }

    public static function vindOpMollie(string $paymentId): ?array
    {
        return self::rij('SELECT * FROM betalingen WHERE mollie_payment_id = ?', [$paymentId]);
    }

    public static function zetStatus(string $molliePaymentId, string $status, ?string $methode = null): void
    {
        self::voerUit(
            'UPDATE betalingen SET status=?, methode=COALESCE(?, methode) WHERE mollie_payment_id=?',
            [$status, $methode, $molliePaymentId]
        );
    }
}
