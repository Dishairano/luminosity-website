<?php

namespace App\Models;

use App\Core\Model;

final class Plan extends Model
{
    public static function vind(int $id): ?array
    {
        return self::rij('SELECT * FROM plannen WHERE id = ?', [$id]);
    }

    public static function vindOpCode(string $code): ?array
    {
        return self::rij('SELECT * FROM plannen WHERE code = ?', [$code]);
    }

    /** @return array[] actieve plannen, op sortering */
    public static function actief(): array
    {
        return self::rijen('SELECT * FROM plannen WHERE actief = 1 ORDER BY sortering, prijs_maand');
    }

    public static function alle(): array
    {
        return self::rijen('SELECT * FROM plannen ORDER BY sortering, prijs_maand');
    }

    public static function maak(array $d): int
    {
        return self::voerUit(
            'INSERT INTO plannen (code, naam, beschrijving, prijs_maand, prijs_jaar, features, populair, actief, sortering)
             VALUES (?,?,?,?,?,?,?,?,?)',
            [$d['code'], $d['naam'], $d['beschrijving'], $d['prijs_maand'], $d['prijs_jaar'],
             $d['features'], $d['populair'], $d['actief'], $d['sortering']]
        );
    }

    public static function werkBij(int $id, array $d): void
    {
        self::voerUit(
            'UPDATE plannen SET naam=?, beschrijving=?, prijs_maand=?, prijs_jaar=?, features=?, populair=?, actief=?, sortering=? WHERE id=?',
            [$d['naam'], $d['beschrijving'], $d['prijs_maand'], $d['prijs_jaar'],
             $d['features'], $d['populair'], $d['actief'], $d['sortering'], $id]
        );
    }

    /** Decodeert de features-JSON naar een lijst regels. */
    public static function features(array $plan): array
    {
        $f = json_decode($plan['features'] ?? '[]', true);
        return is_array($f) ? $f : [];
    }
}
