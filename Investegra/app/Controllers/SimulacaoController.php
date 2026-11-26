<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Simulacao;
use App\Models\Historico;
use App\Services\SimulacaoService;

class SimulacaoController extends Controller
{
    public function index()
    {
        $usuarioId = $this->requireLogin();

        if ((int) ($_SESSION["resultados_simulacao_usuario_id"] ?? 0) !== $usuarioId) {
            unset(
                $_SESSION["resultado_simulacao"],
                $_SESSION["resultado_cdi"],
                $_SESSION["resultado_meta_milhao"],
                $_SESSION["resultado_bola_neve"],
                $_SESSION["resultados_simulacao"]
            );
            $_SESSION["resultados_simulacao_usuario_id"] = $usuarioId;
        }

        /* O resultado vem da sessão e é exibido uma única vez. */
        $resultado = null;
        $resultadoCdi = $_SESSION["resultado_cdi"] ?? null;
        $resultadoMetaMilhao = $_SESSION["resultado_meta_milhao"] ?? null;
        $resultadoBolaNeve = $_SESSION["resultado_bola_neve"] ?? null;
        $resultadosDaSessao = $_SESSION["resultados_simulacao"] ?? [];
        if (!is_array($resultadosDaSessao)) {
            $resultadosDaSessao = [];
        }
        unset($_SESSION["resultado_cdi"]);
        unset($_SESSION["resultado_meta_milhao"]);
        unset($_SESSION["resultado_bola_neve"]);

        if (isset($_SESSION["resultado_simulacao"])) {
            $resultado = $_SESSION["resultado_simulacao"];
            unset($_SESSION["resultado_simulacao"]);
        }

        if ($resultado !== null) {
            $tipoInvestimentoSelecionado = $resultado["tipo_investimento"]
                ?? (!empty($resultado["usar_cdi"])
                    ? "cdi"
                    : $resultado["tipo_juros"]);
        } elseif ($resultadoCdi !== null) {
            $tipoInvestimentoSelecionado = "cdi";
        } elseif ($resultadoMetaMilhao !== null) {
            $tipoInvestimentoSelecionado = "milhao";
        } elseif ($resultadoBolaNeve !== null) {
            $tipoInvestimentoSelecionado = "bola_neve";
        } else {
            $tipoInvestimentoSelecionado = "compostos";
        }

        $dadosGrafico = [];
        $dadosIA = null;

        if ($resultado !== null) {
            $dadosGrafico = SimulacaoService::serieGrafico($resultado);
            $dadosIA = SimulacaoService::dadosParaIA($resultado);
        }

        $contextoIA = "simulacao";

        $this->view("simulacoes/index", compact(
            "resultado", "resultadoBolaNeve", "dadosGrafico",
            "resultadoCdi", "resultadoMetaMilhao",
            "tipoInvestimentoSelecionado",
            "contextoIA", "dadosIA",
            "resultadosDaSessao"
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

        if ($dados["tipo_investimento"] === "milhao") {
            $mesesParaMeta = SimulacaoService::mesesParaMeta(
                $dados,
                1000000
            );
            unset(
                $_SESSION["resultado_simulacao"],
                $_SESSION["resultado_cdi"],
                $_SESSION["resultado_bola_neve"]
            );
            $_SESSION["resultado_meta_milhao"] = [
                "meses_para_meta" => $mesesParaMeta,
                "aporte_inicial" => $dados["aporte_inicial"],
                "aporte_mensal" => $dados["aporte_mensal"],
                "rentabilidade" => $dados["rentabilidade"],
            ];
            $resumoMeta = $mesesParaMeta === null
                ? "Meta não atingida em até 100 anos."
                : ($mesesParaMeta === 0
                    ? "A meta já foi atingida no início."
                    : "Prazo estimado: " . $this->formatarPrazoMeses($mesesParaMeta));
            $this->adicionarResultadoSessao(
                $usuarioId,
                "Meta do primeiro milhão",
                $resumoMeta
            );

            $this->redirect("simulacao.php");
        }

        if ($dados["tipo_investimento"] === "cdi") {
            [$erro, $calculoCdi] = SimulacaoService::calcularCdi($dados);

            if ($erro !== null) {
                $this->abort($erro);
            }

            $id = Simulacao::create(
                $usuarioId,
                "compostos",
                $dados["aporte_inicial"],
                0,
                $dados["taxa_cdi_anual"],
                $dados["prazo_anos"],
                $calculoCdi["valor_liquido"]
            );

            if ($id === null) {
                $this->abort("Erro ao salvar a simulação CDI.");
            }

            Historico::registrarAtividade(
                $usuarioId,
                "criou",
                "Simulação CDI",
                "Valor líquido projetado: R$ " . number_format(
                    $calculoCdi["valor_liquido"],
                    2,
                    ",",
                    "."
                )
            );

            unset(
                $_SESSION["resultado_simulacao"],
                $_SESSION["resultado_meta_milhao"],
                $_SESSION["resultado_bola_neve"]
            );
            $_SESSION["resultado_cdi"] = $calculoCdi;
            $this->adicionarResultadoSessao(
                $usuarioId,
                "CDI líquido",
                "Valor líquido: R$ " . number_format(
                    $calculoCdi["valor_liquido"],
                    2,
                    ",",
                    "."
                ) . " · Prazo: " . $calculoCdi["prazo_dias"] . " dias corridos"
            );

            $this->redirect("simulacao.php");
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

        $tipoAtividade = $dados["usar_cdi"] ? "Simulação CDI" : "Simulação";

        Historico::registrarAtividade(
            $usuarioId,
            "criou",
            $tipoAtividade,
            "Nova simulação criada: R$ " . number_format(
                (float) $calculo["montante"],
                2,
                ",",
                "."
            )
        );

        unset(
            $_SESSION["resultado_cdi"],
            $_SESSION["resultado_meta_milhao"],
            $_SESSION["resultado_bola_neve"]
        );
        $_SESSION["resultado_simulacao"] = [
            "id" => $id,
            "tipo_investimento" => $dados["tipo_investimento"],
            "tipo_juros" => $dados["tipo_juros"],
            "aporte_inicial" => $dados["aporte_inicial"],
            "aporte_mensal" => $dados["aporte_mensal"],
            "rentabilidade_anual" => $dados["rentabilidade"],
            "prazo_anos" => $dados["prazo_anos"],
            "prazo_meses" => $dados["prazo_meses"],
            "meses" => $calculo["meses"],
            "total_aportado" => $calculo["total_aportado"],
            "rendimento" => $calculo["rendimento"],
            "montante" => $calculo["montante"],
            "rentabilidade_resultado" => $calculo["rentabilidade_resultado"],
            "usar_cdi" => $dados["usar_cdi"],
            "meses_para_milhao" => $calculo["meses_para_milhao"],
        ];
        $this->adicionarResultadoSessao(
            $usuarioId,
            $dados["tipo_juros"] === "simples" ? "Juros simples" : "Juros compostos",
            "Montante: R$ " . number_format(
                $calculo["montante"],
                2,
                ",",
                "."
            ) . " · Prazo: " . $this->formatarPrazoMeses($dados["prazo_meses"])
        );

        $this->redirect("simulacao.php");
    }

    public function processarBolaNeve()
    {
        $usuarioId = $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect("simulacao.php#simulacao-fiis");
        }

        [$erro, $resultado] = SimulacaoService::calcularBolaDeNeve(
            $_POST
        );

        if ($erro !== null) {
            $this->abort($erro);
        }

        unset(
            $_SESSION["resultado_cdi"],
            $_SESSION["resultado_meta_milhao"],
            $_SESSION["resultado_simulacao"]
        );
        $_SESSION["resultado_bola_neve"] = $resultado;
        $this->adicionarResultadoSessao(
            $usuarioId,
            "Bola de neve — " . $resultado["ticker"],
            "Valor projetado: R$ " . number_format(
                $resultado["total_final"],
                2,
                ",",
                "."
            ) . " · Prazo: " . $this->formatarPrazoMeses($resultado["prazo_meses"])
        );

        $this->redirect("simulacao.php#simulacao-fiis");
    }

    private function adicionarResultadoSessao(
        int $usuarioId,
        string $titulo,
        string $resumo
    ): void
    {
        if ((int) ($_SESSION["resultados_simulacao_usuario_id"] ?? 0) !== $usuarioId) {
            $_SESSION["resultados_simulacao"] = [];
            $_SESSION["resultados_simulacao_usuario_id"] = $usuarioId;
        }

        $resultados = $_SESSION["resultados_simulacao"] ?? [];
        if (!is_array($resultados)) {
            $resultados = [];
        }

        $resultados[] = [
            "titulo" => $titulo,
            "resumo" => $resumo,
        ];

        $_SESSION["resultados_simulacao"] = array_slice($resultados, -30);
    }

    private function formatarPrazoMeses(int $meses): string
    {
        $anos = intdiv($meses, 12);
        $mesesRestantes = $meses % 12;
        $partes = [];

        if ($anos > 0) {
            $partes[] = $anos . ($anos === 1 ? " ano" : " anos");
        }

        if ($mesesRestantes > 0) {
            $partes[] = $mesesRestantes
                . ($mesesRestantes === 1 ? " mês" : " meses");
        }

        return implode(" e ", $partes);
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
