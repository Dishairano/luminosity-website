<?php

namespace App\Models;

use App\Core\Model;

final class Admin extends Model
{
    public static function vind(int $id): ?array
    {
        return self::rij('SELECT * FROM admins WHERE id = ?', [$id]);
    }

    public static function vindOpEmail(string $email): ?array
    {
        return self::rij('SELECT * FROM admins WHERE email = ?', [strtolower($email)]);
    }

    public static function maak(string $naam, string $email, string $hash, string $rol = 'beheerder'): int
    {
        return self::voerUit(
            'INSERT INTO admins (naam, email, wachtwoord_hash, rol) VALUES (?,?,?,?)',
            [$naam, strtolower($email), $hash, $rol]
        );
    }
}
