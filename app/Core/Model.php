<?php

namespace App\Core;

use PDO;

/**
 * Basis-model met kleine query-helpers bovenop de PDO-verbinding.
 */
abstract class Model
{
    protected static function db(): ?PDO
    {
        return Database::pdo();
    }

    /** Eén rij (of null). */
    protected static function rij(string $sql, array $args = []): ?array
    {
        $pdo = self::db();
        if (!$pdo) return null;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    /** Alle rijen. */
    protected static function rijen(string $sql, array $args = []): array
    {
        $pdo = self::db();
        if (!$pdo) return [];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchAll() ?: [];
    }

    /** Eén scalaire waarde. */
    protected static function waarde(string $sql, array $args = []): mixed
    {
        $pdo = self::db();
        if (!$pdo) return null;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchColumn();
    }

    /** Uitvoeren (INSERT/UPDATE/DELETE); geeft het nieuwe id terug bij insert. */
    protected static function voerUit(string $sql, array $args = []): int
    {
        $pdo = self::db();
        if (!$pdo) return 0;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        return (int) $pdo->lastInsertId();
    }
}
