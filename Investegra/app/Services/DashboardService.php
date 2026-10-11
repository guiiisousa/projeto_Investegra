<?php

namespace App\Services;

use App\Models\Ativo;
use App\Models\Carteira;
use App\Models\Historico;

class DashboardService
{
    /* Monta todos os dados exibidos no dashboard. */
    public static function dados(int $usuarioId, array $usuario): array
    {
        $total_carteiras = Carteira::contar($usuarioId);
        $total_ativos = Ativo::contar($usuarioId);
        $patrimonio = Ativo::patrimonio($usuarioId);

        $historico = Historico::ultimos($usuarioId, 12);
        $historico_grafico = array_reverse($historico);

        $distribuicao = Ativo::distribuicaoPorCategoria($usuarioId);

        foreach ($distribuicao as &$categoria) {
            $valor = (float) $categoria["valor"];

            $categoria["percentual"] = $patrimonio > 0
                ? ($valor / $patrimonio) * 100
                : 0;
        }
        unset($categoria);

        $patrimonio_carteiras = Carteira::patrimonioPorCarteira($usuarioId);

        /* Concentração da carteira */
        $maior_categoria = null;
        $maior_percentual = 0;

        if (count($distribuicao) > 0) {
            $maior_categoria = $distribuicao[0]["categoria"];
            $maior_percentual = (float) $distribuicao[0]["percentual"];
        }

        if ($maior_percentual >= 70) {
            $situacao_diversificacao = "Alta concentração";
            $classe_diversificacao = "concentracao-alta";
        } elseif ($maior_percentual >= 50) {
            $situacao_diversificacao = "Concentração moderada";
            $classe_diversificacao = "concentracao-media";
        } elseif ($maior_percentual > 0) {
            $situacao_diversificacao = "Distribuição diversificada";
            $classe_diversificacao = "concentracao-baixa";
        } else {
            $situacao_diversificacao = "Sem dados";
            $classe_diversificacao = "";
        }

        /* Dados dos gráficos */
        $labels_historico = [];
        $valores_historico = [];

        foreach ($historico_grafico as $registro) {
            $labels_historico[] = date("d/m", strtotime($registro["data_registro"]));
            $valores_historico[] = (float) $registro["valor_total"];
        }

        $labels_categorias = [];
        $valores_categorias = [];
        $percentuais_categorias = [];

        foreach ($distribuicao as $categoria) {
            $labels_categorias[] = $categoria["categoria"];
            $valores_categorias[] = (float) $categoria["valor"];
            $percentuais_categorias[] = round((float) $categoria["percentual"], 1);
        }

        $labels_carteiras = [];
        $valores_carteiras = [];

        foreach ($patrimonio_carteiras as $carteira) {
            $labels_carteiras[] = $carteira["nome"];
            $valores_carteiras[] = (float) $carteira["patrimonio"];
        }

        $ultimos_registros = array_slice($historico, 0, 5);

        /* Variação entre os dois últimos registros */
        $variacao_ultimo = null;

        if (count($historico_grafico) >= 2) {

            $ultimo = (float) $historico_grafico[count($historico_grafico) - 1]["valor_total"];
            $anterior = (float) $historico_grafico[count($historico_grafico) - 2]["valor_total"];

            if ($anterior != 0) {
                $variacao_ultimo = (($ultimo - $anterior) / $anterior) * 100;
            }
        }

        /* Usuário e perfil */
        $nome_usuario = $usuario["nome"];
        $perfil_risco = $usuario["perfil_risco"];

        $classe_perfil = "";

        if ($perfil_risco === "Conservador") {
            $classe_perfil = "perfil-conservador";
        } elseif ($perfil_risco === "Moderado") {
            $classe_perfil = "perfil-moderado";
        } elseif ($perfil_risco === "Arrojado") {
            $classe_perfil = "perfil-arrojado";
        }

        return compact(
            "total_carteiras", "total_ativos", "patrimonio",
            "historico_grafico", "distribuicao", "patrimonio_carteiras",
            "maior_categoria", "maior_percentual",
            "situacao_diversificacao", "classe_diversificacao",
            "labels_historico", "valores_historico",
            "labels_categorias", "valores_categorias", "percentuais_categorias",
            "labels_carteiras", "valores_carteiras",
            "ultimos_registros", "variacao_ultimo",
            "nome_usuario", "perfil_risco", "classe_perfil"
        );
    }
}
