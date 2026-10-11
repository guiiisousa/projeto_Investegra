<?php

namespace App\Services;

/* Cálculos de juros simples e compostos. */
class SimulacaoService
{
    /*
     * Valida os dados do formulário.
     * Devolve [mensagem de erro|null, dados normalizados].
     */
    public static function validar(array $post): array
    {
        $dados = [
            "tipo_juros" => $post["tipo_juros"] ?? "",
            "aporte_inicial" => (float) ($post["aporte_inicial"] ?? 0),
            "aporte_mensal" => (float) ($post["aporte_mensal"] ?? 0),
            "rentabilidade" => (float) ($post["rentabilidade"] ?? 0),
            "prazo_anos" => (int) ($post["prazo_anos"] ?? 0),
        ];

        if ($dados["tipo_juros"] !== "simples" && $dados["tipo_juros"] !== "compostos") {
            return ["Tipo de juros inválido.", $dados];
        }

        if ($dados["aporte_inicial"] < 0) {
            return ["O aporte inicial não pode ser negativo.", $dados];
        }

        if ($dados["aporte_mensal"] < 0) {
            return ["O aporte mensal não pode ser negativo.", $dados];
        }

        if ($dados["rentabilidade"] < 0) {
            return ["A rentabilidade não pode ser negativa.", $dados];
        }

        if ($dados["prazo_anos"] <= 0 || $dados["prazo_anos"] > 100) {
            return ["O prazo informado é inválido.", $dados];
        }

        return [null, $dados];
    }

    /* Montante final e indicadores da simulação. */
    public static function calcular(array $d): array
    {
        $taxaAnual = $d["rentabilidade"] / 100;
        $meses = $d["prazo_anos"] * 12;

        $montante = 0;

        if ($d["tipo_juros"] === "simples") {

            $montanteInicial = $d["aporte_inicial"] * (1 + ($taxaAnual * $d["prazo_anos"]));

            $montanteAportes = 0;

            for ($mes = 1; $mes <= $meses; $mes++) {
                $anosRestantes = ($meses - $mes + 1) / 12;
                $montanteAportes += $d["aporte_mensal"] * (1 + ($taxaAnual * $anosRestantes));
            }

            $montante = $montanteInicial + $montanteAportes;
        }

        if ($d["tipo_juros"] === "compostos") {

            $taxaMensal = pow(1 + $taxaAnual, 1 / 12) - 1;

            $montante = $d["aporte_inicial"];

            for ($mes = 1; $mes <= $meses; $mes++) {
                $montante = ($montante * (1 + $taxaMensal)) + $d["aporte_mensal"];
            }
        }

        $totalAportado = $d["aporte_inicial"] + ($d["aporte_mensal"] * $meses);
        $rendimento = $montante - $totalAportado;

        $rentabilidadeResultado = $totalAportado > 0
            ? ($rendimento / $totalAportado) * 100
            : 0;

        return [
            "meses" => $meses,
            "montante" => $montante,
            "total_aportado" => $totalAportado,
            "rendimento" => $rendimento,
            "rentabilidade_resultado" => $rentabilidadeResultado,
        ];
    }

    /* Pontos (mês x valor) usados no gráfico de evolução. */
    public static function serieGrafico(array $resultado): array
    {
        $dados = [];

        $tipoJuros = $resultado["tipo_juros"];
        $aporteInicial = (float) $resultado["aporte_inicial"];
        $aporteMensal = (float) $resultado["aporte_mensal"];
        $rentabilidade = (float) $resultado["rentabilidade_anual"];
        $meses = (int) $resultado["prazo_anos"] * 12;

        $dados[] = ["mes" => 0, "valor" => $aporteInicial];

        if ($tipoJuros === "compostos") {

            $taxaMensal = pow(1 + ($rentabilidade / 100), 1 / 12) - 1;

            $valor = $aporteInicial;

            for ($mes = 1; $mes <= $meses; $mes++) {

                $valor = ($valor * (1 + $taxaMensal)) + $aporteMensal;

                if (self::exibirPonto($mes, $meses)) {
                    $dados[] = ["mes" => $mes, "valor" => $valor];
                }
            }

            return $dados;
        }

        $taxaAnual = $rentabilidade / 100;

        for ($mes = 1; $mes <= $meses; $mes++) {

            $anosInvestidos = $mes / 12;

            $valorInicial = $aporteInicial * (1 + ($taxaAnual * $anosInvestidos));

            $valorAportes = 0;

            for ($aporteMes = 1; $aporteMes <= $mes; $aporteMes++) {
                $anosRestantes = ($mes - $aporteMes + 1) / 12;
                $valorAportes += $aporteMensal * (1 + ($taxaAnual * $anosRestantes));
            }

            if (self::exibirPonto($mes, $meses)) {
                $dados[] = ["mes" => $mes, "valor" => $valorInicial + $valorAportes];
            }
        }

        return $dados;
    }

    /* Todos os 24 primeiros meses, depois só cada ano e o último mês. */
    private static function exibirPonto(int $mes, int $meses): bool
    {
        return $mes <= 24 || $mes % 12 === 0 || $mes === $meses;
    }

    /* Resumo enviado ao assistente de IA da página. */
    public static function dadosParaIA(array $resultado): array
    {
        return [
            "tipo_juros" => $resultado["tipo_juros"] ?? "",
            "aporte_inicial" => (float) ($resultado["aporte_inicial"] ?? 0),
            "aporte_mensal" => (float) ($resultado["aporte_mensal"] ?? 0),
            "rentabilidade_anual" => (float) ($resultado["rentabilidade_anual"] ?? 0),
            "prazo_anos" => (int) ($resultado["prazo_anos"] ?? 0),
            "montante" => (float) ($resultado["montante"] ?? 0),
            "total_aportado" => (float) ($resultado["total_aportado"] ?? 0),
            "rendimento" => (float) ($resultado["rendimento"] ?? 0),
            "rentabilidade_resultado" => (float) ($resultado["rentabilidade_resultado"] ?? 0),
        ];
    }
}
