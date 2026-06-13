<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model voor de logboek-tabel (komt overeen met het ERD uit het ontwerp:
 * id, invoertekst, commando, status, tijdstip). Slaat elke geprobeerde
 * opdracht op zodat bezoekers (en wij) kunnen terugkijken.
 */
final class LogEntry
{
    /**
     * Voegt een regel toe. Geeft stil-veilig op als er geen database is.
     */
    public static function schrijf(string $invoertekst, string $commando, string $status): void
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return;
        }
        $stmt = $pdo->prepare(
            'INSERT INTO logboek (invoertekst, commando, status) VALUES (:i, :c, :s)'
        );
        $stmt->execute([':i' => $invoertekst, ':c' => $commando, ':s' => $status]);
    }

    /**
     * @return array<int, array{id:int,invoertekst:string,commando:string,status:string,tijdstip:string}>
     */
    public static function recent(int $limiet = 10): array
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return [];
        }
        $limiet = max(1, min(50, $limiet));
        $stmt = $pdo->query(
            "SELECT id, invoertekst, commando, status, tijdstip
             FROM logboek ORDER BY id DESC LIMIT $limiet"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
