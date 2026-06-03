<?php

namespace App\Core;

/**
 * Laadt config/config.php één keer in en geeft secties terug via Config::get('db').
 */
final class Config
{
    private static array $data = [];

    public static function load(string $file): void
    {
        self::$data = require $file;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$data[$key] ?? $default;
    }
}
