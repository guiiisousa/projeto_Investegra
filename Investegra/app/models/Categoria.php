<?php

namespace App\Models;

use App\Core\Model;

class Categoria extends Model
{
    public static function all(): array
    {
        return self::fetchAll("SELECT id, nome FROM categorias ORDER BY nome");
    }

    public static function exists(int $id): bool
    {
        return self::fetchOne(
            "SELECT id FROM categorias WHERE id = ?",
            "i",
            [$id]
        ) !== null;
    }

    public static function nomePorId(int $id): ?string
    {
        $categoria = self::fetchOne(
            "SELECT nome FROM categorias WHERE id = ?",
            "i",
            [$id]
        );

        return $categoria["nome"] ?? null;
    }
}
