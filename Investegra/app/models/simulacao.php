<?php

namespace App\Models;

use App\Core\Model;

class Simulacao extends Model
{
    public static function all(int $usuarioId): array
    {
        return self::fetchAll(
            "SELECT id, tipo_juros, aporte_inicial, aporte_mensal,
                    rentabilidade, prazo_anos, montante, criado_em
             FROM simulacoes
             WHERE usuario_id = ?
             ORDER BY criado_em DESC",
            "i",
            [$usuarioId]
        );
    }

    /* Salva e devolve o id criado (ou null em caso de erro). */
    public static function create(
        int $usuarioId,
        string $tipoJuros,
        float $aporteInicial,
        float $aporteMensal,
        float $rentabilidade,
        int $prazoAnos,
        float $montante
    ): ?int {
        $ok = self::execute(
            "INSERT INTO simulacoes
                (usuario_id, tipo_juros, aporte_inicial, aporte_mensal,
                 rentabilidade, prazo_anos, montante)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            "isdddid",
            [
                $usuarioId, $tipoJuros, $aporteInicial, $aporteMensal,
                $rentabilidade, $prazoAnos, $montante,
            ]
        );

        return $ok ? (int) self::db()->insert_id : null;
    }
}
