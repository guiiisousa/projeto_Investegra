<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Simulacao;
use App\Services\SimulacaoService;

class SimulacaoController extends Controller
{
    public function index()
    {
        $this->requireLogin();

        /* O resultado vem da sessão e é exibido uma única vez. */
        $resultado = null;

        if (isset($_SESSION["resultado_simulacao"])) {
            $resultado = $_SESSION["resultado_simulacao"];
            unset($_SESSION["resultado_simulacao"]);
        }

        $dadosGrafico = [];
        $dadosIA = null;

        if ($resultado !== null) {
            $dadosGrafico = SimulacaoService::serieGrafico($resultado);
            $dadosIA = SimulacaoService::dadosParaIA($resultado);
        }

        $contextoIA = "simulacao";

        $this->view("simulacoes/index", compact(
            "resultado", "dadosGrafico", "contextoIA", "dadosIA"
        ));
    }

    public function processar()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("simulacao.php");
        }

        [$erro, $dados] = SimulacaoService::validar($_POST);

        if ($erro !== null) {
            $this->abort($erro);
        }

        $calculo = SimulacaoService::calcular($dados);

        $id = Simulacao::create(
            $usuarioId,
            $dados["tipo_juros"],
            $dados["aporte_inicial"],
            $dados["aporte_mensal"],
            $dados["rentabilidade"],
            $dados["prazo_anos"],
            $calculo["montante"]
        );

        if ($id === null) {
            $this->abort("Erro ao salvar a simulação.");
        }

        $_SESSION["resultado_simulacao"] = [
            "id" => $id,
            "tipo_juros" => $dados["tipo_juros"],
            "aporte_inicial" => $dados["aporte_inicial"],
            "aporte_mensal" => $dados["aporte_mensal"],
            "rentabilidade_anual" => $dados["rentabilidade"],
            "prazo_anos" => $dados["prazo_anos"],
            "meses" => $calculo["meses"],
            "total_aportado" => $calculo["total_aportado"],
            "rendimento" => $calculo["rendimento"],
            "montante" => $calculo["montante"],
            "rentabilidade_resultado" => $calculo["rentabilidade_resultado"],
        ];

        $this->redirect("simulacao.php");
    }

    public function historico()
    {
        $usuarioId = $this->requireLogin();

        $simulacoes = Simulacao::all($usuarioId);

        $totalSimulacoes = count($simulacoes);

        $maiorMontante = 0;
        $menorMontante = null;
        $totalMontantes = 0;

        foreach ($simulacoes as $simulacao) {

            $montante = (float) $simulacao["montante"];

            $totalMontantes += $montante;

            if ($montante > $maiorMontante) {
                $maiorMontante = $montante;
            }

            if ($menorMontante === null || $montante < $menorMontante) {
                $menorMontante = $montante;
            }
        }

        $mediaMontante = $totalSimulacoes > 0
            ? $totalMontantes / $totalSimulacoes
            : 0;

        $contextoIA = "historico_simulacoes";

        $this->view("simulacoes/historico", compact(
            "simulacoes", "totalSimulacoes", "maiorMontante",
            "menorMontante", "mediaMontante", "contextoIA"
        ));
    }
}
