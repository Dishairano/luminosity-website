<?php

namespace App\Core;

/**
 * Eenvoudige CSRF-bescherming voor formulieren (POST).
 */
final class Csrf
{
    public static function token(): string
    {
        Auth::start();
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function check(?string $token): bool
    {
        Auth::start();
        return is_string($token) && !empty($_SESSION['_csrf'])
            && hash_equals($_SESSION['_csrf'], $token);
    }
}
