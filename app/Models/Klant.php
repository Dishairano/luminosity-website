<?php

namespace App\Models;

use App\Core\Model;

final class Klant extends Model
{
    public static function vind(int $id): ?array
    {
        return self::rij('SELECT * FROM klanten WHERE id = ?', [$id]);
    }

    public static function vindOpEmail(string $email): ?array
    {
        return self::rij('SELECT * FROM klanten WHERE email = ?', [strtolower($email)]);
    }

    public static function maak(array $d): int
    {
        return self::voerUit(
            'INSERT INTO klanten (naam, email, wachtwoord_hash, land) VALUES (?,?,?,?)',
            [$d['naam'], strtolower($d['email']), $d['wachtwoord_hash'], $d['land'] ?? 'Nederland']
        );
    }

    public static function werkProfielBij(int $id, array $d): void
    {
        self::voerUit(
            'UPDATE klanten SET naam=?, bedrijf=?, btw_nummer=?, adres=?, postcode=?, stad=?, land=?, telefoon=? WHERE id=?',
            [$d['naam'], $d['bedrijf'], $d['btw_nummer'], $d['adres'], $d['postcode'],
             $d['stad'], $d['land'], $d['telefoon'], $id]
        );
    }

    public static function zetStatus(int $id, string $status): void
    {
        self::voerUit('UPDATE klanten SET status=? WHERE id=?', [$status, $id]);
    }

    public static function zetWachtwoord(int $id, string $hash): void
    {
        self::voerUit('UPDATE klanten SET wachtwoord_hash=? WHERE id=?', [$hash, $id]);
    }

    /** @return array[] alle klanten, optioneel met zoekterm */
    public static function alle(string $zoek = ''): array
    {
        if ($zoek !== '') {
            $q = '%' . $zoek . '%';
            return self::rijen(
                'SELECT * FROM klanten WHERE naam LIKE ? OR email LIKE ? OR bedrijf LIKE ? ORDER BY id DESC',
                [$q, $q, $q]
            );
        }
        return self::rijen('SELECT * FROM klanten ORDER BY id DESC');
    }

    public static function aantal(): int { return (int) self::waarde('SELECT COUNT(*) FROM klanten'); }

    public static function aantalSinds(string $datum): int
    {
        return (int) self::waarde('SELECT COUNT(*) FROM klanten WHERE aangemaakt >= ?', [$datum]);
    }
}
