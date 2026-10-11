<?php

namespace App\Core;

abstract class Model
{
    protected static function db(): \mysqli
    {
        return Database::connection();
    }

    private static function preparar(string $sql, string $tipos, array $params): \mysqli_stmt
    {
        $stmt = self::db()->prepare($sql);

        if (!$stmt) {
            die("Erro ao preparar consulta.");
        }

        if ($tipos !== "") {
            $stmt->bind_param($tipos, ...$params);
        }

        return $stmt;
    }

    protected static function fetchAll(string $sql, string $tipos = "", array $params = []): array
    {
        $stmt = self::preparar($sql, $tipos, $params);
        $stmt->execute();
        $linhas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $linhas;
    }

    protected static function fetchOne(string $sql, string $tipos = "", array $params = []): ?array
    {
        $linhas = self::fetchAll($sql, $tipos, $params);

        return $linhas[0] ?? null;
    }

    protected static function execute(string $sql, string $tipos = "", array $params = []): bool
    {
        $stmt = self::preparar($sql, $tipos, $params);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
