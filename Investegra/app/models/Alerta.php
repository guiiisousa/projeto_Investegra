<?php

namespace App\Models;

use App\Core\Model;

class Alerta extends Model
{
    public static function all(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT id, titulo, mensagem, nivel, lido, criado_em
             FROM alertas
             WHERE usuario_id = ?
             ORDER BY criado_em DESC",
            "i",
            [$usuarioId]
        );
    }

    /* Mensagens dos alertas ainda não lidos. */
    public static function mensagensNaoLidas(int $usuarioId): array
    {
        $linhas = self::fetchAll(
            "SELECT mensagem
             FROM alertas
             WHERE usuario_id = ? AND lido = 0
             ORDER BY criado_em DESC",
            "i",
            [$usuarioId]
        );

        return array_column($linhas, "mensagem");
    }

    public static function apagarNaoLidos(int $usuarioId): bool
    {
        return self::execute(
            "DELETE FROM alertas WHERE usuario_id = ? AND lido = 0",
            "i",
            [$usuarioId]
        );
    }

    public static function create(int $usuarioId, string $titulo, string $mensagem, string $nivel): bool
    {
        return self::execute(
            "INSERT INTO alertas (usuario_id, titulo, mensagem, nivel)
             VALUES (?, ?, ?, ?)",
            "isss",
            [$usuarioId, $titulo, $mensagem, $nivel]
        );
    }
}
