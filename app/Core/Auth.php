<?php

namespace App\Core;

/**
 * Sessie-gebaseerde authenticatie met aparte 'guards' voor klanten en admins,
 * zodat een ingelogde klant geen toegang krijgt tot het adminpaneel en andersom.
 */
final class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    // --- generiek ---
    public static function login(string $guard, int $id): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION[$guard . '_id'] = $id;
    }

    public static function id(string $guard): ?int
    {
        self::start();
        return $_SESSION[$guard . '_id'] ?? null;
    }

    public static function check(string $guard): bool
    {
        return self::id($guard) !== null;
    }

    public static function logout(string $guard): void
    {
        self::start();
        unset($_SESSION[$guard . '_id']);
    }

    // --- gemak ---
    public static function klantId(): ?int { return self::id('klant'); }
    public static function adminId(): ?int { return self::id('admin'); }
}
