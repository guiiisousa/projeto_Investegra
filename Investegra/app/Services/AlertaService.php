<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Ativo;
use App\Models\Carteira;

class AlertaService
{
    /* Recalcula os alertas não lidos do usuário. */
    public static function gerar(int $usuarioId): void
    {
        $totalCarteiras = Carteira::contar($usuarioId);
        $totalAtivos = Ativo::contar($usuarioId);

        Alerta::apagarNaoLidos($usuarioId);

        if ($totalCarteiras == 0) {
            Alerta::create(
                $usuarioId,
                "Nenhuma carteira cadastrada",
                "Você ainda não possui uma carteira cadastrada no INVESTEGRA.",
                "Baixo"
            );
            return;
        }

        if ($totalAtivos == 0) {
            Alerta::create(
                $usuarioId,
                "Carteira sem ativos",
                "Sua carteira ainda não possui ativos cadastrados.",
                "Baixo"
            );
            return;
        }

        if (Ativo::totalCategorias($usuarioId) == 1) {
            Alerta::create(
                $usuarioId,
                "Baixa diversificação",
                "Todos os seus ativos estão concentrados em uma única categoria. Avalie a diversificação da carteira.",
                "Medio"
            );
        }

        $maiorCategoria = Ativo::maiorCategoria($usuarioId, true);
        $patrimonio = Ativo::patrimonio($usuarioId);

        if ($patrimonio > 0 && $maiorCategoria) {

            $percentual = ($maiorCategoria["valor"] / $patrimonio) * 100;

            if ($percentual >= 70) {
                Alerta::create(
                    $usuarioId,
                    "Alta concentração",
                    "A categoria " . $maiorCategoria["categoria"]
                    . " representa aproximadamente "
                    . number_format($percentual, 1, ",", ".")
                    . "% do patrimônio da carteira.",
                    "Alto"
                );
            }
        }
    }
}
