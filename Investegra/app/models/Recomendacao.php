<?php

namespace App\Models;

use App\Core\Model;

class Recomendacao extends Model
{
    public static function all(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT id, titulo, descricao, categoria,
                    percentual_sugerido, criado_em
             FROM recomendacoes
             WHERE usuario_id = ?
             ORDER BY criado_em DESC",
            "i",
            [$usuarioId]
        );
    }

    public static function apagarTodas(int $usuarioId): bool
    {
        return self::execute(
            "DELETE FROM recomendacoes WHERE usuario_id = ?",
            "i",
            [$usuarioId]
        );
    }

    public static function create(
        int $usuarioId,
        string $titulo,
        string $descricao,
        string $categoria,
        ?float $percentual = null
    ): bool {
        return self::execute(
            "INSERT INTO recomendacoes
                (usuario_id, titulo, descricao, categoria, percentual_sugerido)
             VALUES (?, ?, ?, ?, ?)",
            "isssd",
            [$usuarioId, $titulo, $descricao, $categoria, $percentual]
        );
    }
}
