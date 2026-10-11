<?php

namespace App\Services;

use App\Models\Ativo;
use App\Models\Recomendacao;
use App\Models\Usuario;

class RecomendacaoService
{
    /* Recria as recomendações do usuário a partir do perfil e da carteira. */
    public static function gerar(int $usuarioId): void
    {
        $usuario = Usuario::perfil($usuarioId);

        if (!$usuario) {
            return;
        }

        $perfil = $usuario["perfil_risco"];

        Recomendacao::apagarTodas($usuarioId);

        $quantidadeAtivos = Ativo::contar($usuarioId);
        $patrimonio = Ativo::patrimonio($usuarioId);

        if ($quantidadeAtivos == 0) {
            Recomendacao::create(
                $usuarioId,
                "Cadastre seus ativos",
                "Sua carteira ainda não possui ativos cadastrados. Cadastre os investimentos que fazem parte da sua carteira para que o sistema possa apresentar análises de composição e diversificação.",
                "Organização"
            );
            return;
        }

        if ($perfil == "Conservador") {
            Recomendacao::create(
                $usuarioId,
                "Perfil conservador",
                "Seu perfil de risco é classificado como conservador. Ao analisar novos investimentos, considere principalmente características relacionadas à preservação do patrimônio e ao controle da exposição ao risco.",
                "Perfil de risco"
            );
        } elseif ($perfil == "Moderado") {
            Recomendacao::create(
                $usuarioId,
                "Perfil moderado",
                "Seu perfil de risco é classificado como moderado. Uma carteira diversificada entre diferentes categorias pode ajudar a equilibrar risco e potencial de retorno.",
                "Perfil de risco"
            );
        } elseif ($perfil == "Arrojado") {
            Recomendacao::create(
                $usuarioId,
                "Perfil arrojado",
                "Seu perfil de risco é classificado como arrojado. Mesmo aceitando maior exposição ao risco, é importante acompanhar a composição da carteira e manter uma estratégia de diversificação.",
                "Perfil de risco"
            );
        }

        if (Ativo::totalCategorias($usuarioId) == 1) {
            Recomendacao::create(
                $usuarioId,
                "Avalie a diversificação",
                "Sua carteira possui ativos registrados em apenas uma categoria. Avalie, de acordo com seus objetivos e tolerância ao risco, a possibilidade de distribuir seus investimentos entre diferentes categorias.",
                "Diversificação"
            );
        }

        $maiorCategoria = Ativo::maiorCategoria($usuarioId, true);

        $concentrada = false;
        $percentual = 0;

        if ($patrimonio > 0 && $maiorCategoria) {

            $percentual = ($maiorCategoria["valor"] / $patrimonio) * 100;
            $concentrada = $percentual >= 70;

            if ($concentrada) {
                Recomendacao::create(
                    $usuarioId,
                    "Reduza a concentração",
                    "A categoria " . $maiorCategoria["categoria"]
                    . " representa aproximadamente "
                    . number_format($percentual, 1, ",", ".")
                    . "% do patrimônio registrado. Avalie se essa concentração está de acordo com seu perfil de risco e objetivos.",
                    "Concentração"
                );
            }
        }

        if ($perfil == "Conservador" && $concentrada) {
            Recomendacao::create(
                $usuarioId,
                "Revise a composição da carteira",
                "A concentração identificada merece atenção considerando seu perfil de risco conservador. Avalie a composição da carteira e sua compatibilidade com seus objetivos.",
                "Perfil de risco"
            );
        }

        if ($quantidadeAtivos < 3) {
            Recomendacao::create(
                $usuarioId,
                "Acompanhe a diversificação",
                "Sua carteira possui poucos ativos cadastrados. Avalie se a quantidade e a distribuição atual são adequadas aos seus objetivos.",
                "Diversificação"
            );
        }

        if ($usuario["horizonte_investimento"] == "Curto") {
            Recomendacao::create(
                $usuarioId,
                "Horizonte de curto prazo",
                "Como seu horizonte de investimento é de curto prazo, considere avaliar cuidadosamente a liquidez e a volatilidade dos investimentos antes de tomar decisões.",
                "Horizonte"
            );
        } elseif ($usuario["horizonte_investimento"] == "Longo") {
            Recomendacao::create(
                $usuarioId,
                "Horizonte de longo prazo",
                "Seu horizonte de investimento é de longo prazo. O acompanhamento periódico da carteira e da diversificação pode contribuir para manter a estratégia alinhada aos seus objetivos.",
                "Horizonte"
            );
        }
    }
}
