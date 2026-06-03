<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Dunne PDO-wrapper als singleton. Houdt één verbinding aan per request en
 * geeft die terug via Database::pdo(). Faalt stil-veilig: als de database niet
 * bereikbaar is, blijft de site werken (alleen het logboek wordt dan niet
 * weggeschreven).
 */
final class Database
{
    private static ?PDO $instance = null;
    private static bool $failed = false;

    private function __construct() {}

    public static function pdo(): ?PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        if (self::$failed) {
            return null;
        }

        $cfg = Config::get('db');
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $cfg['host'],
            $cfg['name'],
            $cfg['charset']
        );

        try {
            self::$instance = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            self::$failed = true;
            return null;
        }

        return self::$instance;
    }
}
