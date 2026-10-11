<?php

namespace App\Models;

use App\Core\Model;

class Historico extends Model
{
    /* Grava o patrimônio atual (soma de todas as carteiras) do usuário. */
    public static function registrarPatrimonio(int $usuarioId): void
    {
        $linha = self::fetchOne(
            "SELECT COALESCE(SUM(a.quantidade * a.preco_medio), 0) AS patrimonio
             FROM ativos a
             INNER JOIN carteiras c ON a.carteira_id = c.id
             WHERE c.usuario_id = ?",
            "i",
            [$usuarioId]
        );

        self::execute(
            "INSERT INTO historico_patrimonio (usuario_id, valor_total)
             VALUES (?, ?)",
            "id",
            [$usuarioId, $linha["patrimonio"]]
        );
    }

    /* Últimos registros (mais recentes primeiro). */
    public static function ultimos(int $usuarioId, int $limite = 12): array
    {
        return self::fetchAll(
            "SELECT valor_total, data_registro
             FROM historico_patrimonio
             WHERE usuario_id = ?
             ORDER BY data_registro DESC
             LIMIT " . (int) $limite,
            "i",
            [$usuarioId]
        );
    }

    /* Histórico completo em ordem cronológica. */
    public static function todos(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT valor_total, data_registro
             FROM historico_patrimonio
             WHERE usuario_id = ?
             ORDER BY data_registro ASC",
            "i",
            [$usuarioId]
        );
    }
}
