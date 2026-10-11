<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Historico;

class HistoricoController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        $historico = Historico::todos($usuarioId);

        /* Estatísticas gerais */
        $patrimonio_atual = 0;
        $maior_patrimonio = 0;
        $menor_patrimonio = 0;

        if (count($historico) > 0) {

            $valores = [];

            foreach ($historico as $registro) {
                $valores[] = (float) $registro["valor_total"];
            }

            $patrimonio_atual = $valores[count($valores) - 1];
            $maior_patrimonio = max($valores);
            $menor_patrimonio = min($valores);
        }

        /* Variação total */
        $variacao_total = 0;
        $percentual_total = 0;

        if (count($historico) >= 2) {

            $primeiro = (float) $historico[0]["valor_total"];
            $ultimo = (float) $historico[count($historico) - 1]["valor_total"];

            $variacao_total = $ultimo - $primeiro;

            if ($primeiro != 0) {
                $percentual_total = ($variacao_total / $primeiro) * 100;
            }
        }

        /* Variação de cada registro */
        $historico_completo = [];
        $valor_anterior = null;

        foreach ($historico as $registro) {

            $valor_atual = (float) $registro["valor_total"];

            $variacao = null;
            $percentual = null;

            if ($valor_anterior !== null && $valor_anterior != 0) {
                $variacao = $valor_atual - $valor_anterior;
                $percentual = ($variacao / $valor_anterior) * 100;
            }

            $historico_completo[] = [
                "data" => $registro["data_registro"],
                "valor" => $valor_atual,
                "variacao" => $variacao,
                "percentual" => $percentual,
            ];

            $valor_anterior = $valor_atual;
        }

        $this->view("historico/index", compact(
            "historico", "historico_completo",
            "patrimonio_atual", "maior_patrimonio", "menor_patrimonio",
            "variacao_total", "percentual_total"
        ));
    }
}
