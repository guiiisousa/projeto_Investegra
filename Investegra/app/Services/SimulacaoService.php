<?php

namespace App\Services;

/* Cálculos das simulações de juros, CDI e reinvestimento. */
class SimulacaoService
{
    /*
     * Valida os dados do formulário.
     * Devolve [mensagem de erro|null, dados normalizados].
     */
    public static function validar(array $post): array
    {
        $tipoInvestimentoRaw = $post["tipo_investimento"] ?? null;
        $tipoInvestimento = is_string($tipoInvestimentoRaw)
            ? $tipoInvestimentoRaw
            : "";

        if (
            $tipoInvestimento === ""
            && in_array($post["tipo_juros"] ?? null, ["simples", "compostos"], true)
        ) {
            $tipoInvestimento = $post["tipo_juros"];
        }

        $aporteInicialRaw = $post["aporte_inicial"] ?? null;
        $aporteMensalRaw = $post["aporte_mensal"] ?? null;
        $rentabilidadeRaw = $post["rentabilidade"] ?? null;
        $taxaCdiRaw = $post["taxa_cdi_anual"] ?? null;
        $percentualCdiRaw = $post["percentual_cdi"] ?? null;
        $prazoDiasRaw = $post["prazo_dias"] ?? null;
        $prazoDias = is_scalar($prazoDiasRaw)
            ? filter_var($prazoDiasRaw, FILTER_VALIDATE_INT)
            : false;
        $taxaCdi = is_numeric($taxaCdiRaw) && is_finite((float) $taxaCdiRaw)
            ? (float) $taxaCdiRaw
            : -1;
        $prazoRaw = $post["prazo_quantidade"] ?? $post["prazo_anos"] ?? null;
        $prazoUnidadeRaw = $post["prazo_unidade"]
            ?? (array_key_exists("prazo_anos", $post) ? "anos" : null);
        [$erroPrazo, $prazoMeses, $prazoAnos] = self::normalizarPrazo(
            $prazoRaw,
            $prazoUnidadeRaw
        );
        $percentualCdi = is_numeric($percentualCdiRaw) && is_finite((float) $percentualCdiRaw)
            ? (float) $percentualCdiRaw
            : -1;
        $rentabilidade = is_numeric($rentabilidadeRaw) && is_finite((float) $rentabilidadeRaw)
            ? (float) $rentabilidadeRaw
            : -1;
        $aporteInicial = is_numeric($aporteInicialRaw) && is_finite((float) $aporteInicialRaw)
            ? (float) $aporteInicialRaw
            : -1;
        $aporteMensal = is_numeric($aporteMensalRaw) && is_finite((float) $aporteMensalRaw)
            ? (float) $aporteMensalRaw
            : -1;

        $dados = [
            "tipo_investimento" => $tipoInvestimento,
            "tipo_juros" => $tipoInvestimento === "simples"
                ? "simples"
                : "compostos",
            "aporte_inicial" => $aporteInicial,
            "aporte_mensal" => $tipoInvestimento === "cdi" ? 0 : $aporteMensal,
            "rentabilidade" => $tipoInvestimento === "cdi"
                ? $taxaCdi
                : $rentabilidade,
            "taxa_cdi_anual" => $taxaCdi,
            "percentual_cdi" => $percentualCdi,
            "prazo_dias" => $prazoDias === false ? 0 : (int) $prazoDias,
            "prazo_anos" => $prazoAnos,
            "prazo_meses" => $prazoMeses,
            "usar_cdi" => $tipoInvestimento === "cdi",
        ];

        if (!in_array(
            $tipoInvestimento,
            ["simples", "compostos", "cdi", "milhao"],
            true
        )) {
            return ["Tipo de investimento inválido.", $dados];
        }

        if ($dados["aporte_inicial"] < 0) {
            return ["O aporte inicial não pode ser negativo.", $dados];
        }

        if ($tipoInvestimento === "cdi") {
            if ($dados["aporte_inicial"] <= 0) {
                return ["Informe um valor aplicado maior que zero para a simulação CDI.", $dados];
            }

            if ($dados["taxa_cdi_anual"] < 0) {
                return ["Informe uma taxa CDI anual válida e não negativa.", $dados];
            }

            if ($dados["percentual_cdi"] < 0) {
                return ["Informe um percentual do CDI válido e não negativo.", $dados];
            }

            if ($dados["prazo_dias"] < 1 || $dados["prazo_dias"] > 36500) {
                return ["O prazo CDI deve estar entre 1 e 36.500 dias.", $dados];
            }

            $dados["prazo_anos"] = (int) ceil($dados["prazo_dias"] / 365);

            return [null, $dados];
        }

        if ($dados["aporte_mensal"] < 0) {
            return ["O aporte mensal não pode ser negativo.", $dados];
        }

        if ($dados["rentabilidade"] < 0) {
            return ["Informe uma taxa anual válida e não negativa.", $dados];
        }

        if ($tipoInvestimento !== "milhao") {
            if ($erroPrazo !== null) {
                return [$erroPrazo, $dados];
            }
        }

        return [null, $dados];
    }

    public static function calcularCdi(array $dados): array
    {
        $diasCorridos = (int) $dados["prazo_dias"];
        $diasUteis = max(1, (int) round($diasCorridos * 252 / 365));
        $taxaCdiDiaria = pow(
            1 + ((float) $dados["taxa_cdi_anual"] / 100),
            1 / 252
        ) - 1;
        $taxaAplicadaDiaria = $taxaCdiDiaria
            * ((float) $dados["percentual_cdi"] / 100);
        $valorAplicado = (float) $dados["aporte_inicial"];
        $valorBruto = $valorAplicado * pow(1 + $taxaAplicadaDiaria, $diasUteis);
        $rendimentoBruto = $valorBruto - $valorAplicado;
        $aliquotaIof = self::aliquotaIof($diasCorridos);
        $iof = $rendimentoBruto * ($aliquotaIof / 100);
        $baseIr = max(0, $rendimentoBruto - $iof);
        $aliquotaIr = self::aliquotaIr($diasCorridos);
        $ir = $baseIr * ($aliquotaIr / 100);
        $valorLiquido = $valorBruto - $iof - $ir;
        $rendimentoLiquido = $valorLiquido - $valorAplicado;
        $rentabilidadeLiquida = ($rendimentoLiquido / $valorAplicado) * 100;

        foreach ([
            $taxaCdiDiaria,
            $taxaAplicadaDiaria,
            $valorBruto,
            $rendimentoBruto,
            $iof,
            $ir,
            $valorLiquido,
            $rentabilidadeLiquida,
        ] as $valor) {
            if (!is_finite($valor)) {
                return ["A projeção CDI gerou valores fora do limite numérico.", null];
            }
        }

        return [null, [
            "aporte_inicial" => $valorAplicado,
            "taxa_cdi_anual" => (float) $dados["taxa_cdi_anual"],
            "percentual_cdi" => (float) $dados["percentual_cdi"],
            "prazo_dias" => $diasCorridos,
            "dias_uteis_estimados" => $diasUteis,
            "taxa_cdi_diaria" => $taxaCdiDiaria,
            "valor_bruto" => $valorBruto,
            "rendimento_bruto" => $rendimentoBruto,
            "aliquota_iof" => $aliquotaIof,
            "iof" => $iof,
            "aliquota_ir" => $aliquotaIr,
            "ir" => $ir,
            "valor_liquido" => $valorLiquido,
            "rendimento_liquido" => $rendimentoLiquido,
            "rentabilidade_liquida" => $rentabilidadeLiquida,
        ]];
    }

    private static function aliquotaIof(int $diasCorridos): float
    {
        $aliquotas = [
            1 => 96, 2 => 93, 3 => 90, 4 => 86, 5 => 83,
            6 => 80, 7 => 76, 8 => 73, 9 => 70, 10 => 66,
            11 => 63, 12 => 60, 13 => 56, 14 => 53, 15 => 50,
            16 => 46, 17 => 43, 18 => 40, 19 => 36, 20 => 33,
            21 => 30, 22 => 26, 23 => 23, 24 => 20, 25 => 16,
            26 => 13, 27 => 10, 28 => 6, 29 => 3, 30 => 0,
        ];

        return $aliquotas[$diasCorridos] ?? 0;
    }

    private static function aliquotaIr(int $diasCorridos): float
    {
        if ($diasCorridos <= 180) {
            return 22.5;
        }

        if ($diasCorridos <= 360) {
            return 20;
        }

        if ($diasCorridos <= 720) {
            return 17.5;
        }

        return 15;
    }

    /* Montante final e indicadores da simulação. */
    public static function calcular(array $d): array
    {
        $taxaAnual = $d["rentabilidade"] / 100;
        $meses = (int) $d["prazo_meses"];

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
            "prazo_meses" => $meses,
            "montante" => $montante,
            "total_aportado" => $totalAportado,
            "rendimento" => $rendimento,
            "rentabilidade_resultado" => $rentabilidadeResultado,
            "meses_para_milhao" => self::mesesParaMeta($d, 1000000),
        ];
    }

    public static function mesesParaMeta(array $dados, float $meta): ?int
    {
        if ($meta <= 0) {
            return 0;
        }

        $aporteInicial = (float) $dados["aporte_inicial"];
        $aporteMensal = (float) $dados["aporte_mensal"];
        $taxaAnual = (float) $dados["rentabilidade"] / 100;

        if ($aporteInicial >= $meta) {
            return 0;
        }

        $taxaMensal = pow(1 + $taxaAnual, 1 / 12) - 1;
        $montante = $aporteInicial;

        for ($mes = 1; $mes <= 1200; $mes++) {
            if ($dados["tipo_juros"] === "simples") {
                $montante = $aporteInicial * (1 + ($taxaAnual * $mes / 12))
                    + ($aporteMensal * $mes)
                    + ($aporteMensal * $taxaAnual * $mes * ($mes + 1) / 24);
            } else {
                $montante = ($montante * (1 + $taxaMensal)) + $aporteMensal;
            }

            if ($montante >= $meta) {
                return $mes;
            }
        }

        return null;
    }

    public static function calcularBolaDeNeve(array $post): array
    {
        $prazoRaw = $post["prazo_quantidade"] ?? $post["prazo_anos"] ?? null;
        $prazoUnidadeRaw = $post["prazo_unidade"]
            ?? (array_key_exists("prazo_anos", $post) ? "anos" : null);
        [$erroPrazo, $prazoMeses, $prazoAnos] = self::normalizarPrazo(
            $prazoRaw,
            $prazoUnidadeRaw
        );
        $tickerRaw = $post["ticker"] ?? null;
        $ticker = is_string($tickerRaw) ? strtoupper(trim($tickerRaw)) : "";
        $quantidadeRaw = $post["quantidade"] ?? null;
        $precoRaw = $post["preco_medio"] ?? null;
        $dyRaw = $post["dy_mensal"] ?? null;
        $quantidade = is_numeric($quantidadeRaw) && is_finite((float) $quantidadeRaw)
            ? (float) $quantidadeRaw
            : -1;
        $preco = is_numeric($precoRaw) && is_finite((float) $precoRaw)
            ? (float) $precoRaw
            : -1;
        $dyMensal = is_numeric($dyRaw) && is_finite((float) $dyRaw)
            ? (float) $dyRaw
            : -1;

        if ($erroPrazo !== null) {
            return [$erroPrazo, null];
        }

        if (!preg_match('/^[A-Z0-9][A-Z0-9.-]{0,19}$/', $ticker)) {
            return ["Selecione um FII válido na busca por nome ou ticker.", null];
        }

        if ($quantidade < 0) {
            return ["Informe uma quantidade de cotas válida e não negativa.", null];
        }

        if ($preco <= 0) {
            return ["Informe um preço médio maior que zero.", null];
        }

        if ($dyMensal < 0) {
            return ["O dividend yield mensal deve ser não negativo.", null];
        }

        $quantidadeInicial = $quantidade;
        $saldoProventos = 0.0;
        $proventosRecebidos = 0.0;
        $cotasReinvestidas = 0.0;
        $taxa = $dyMensal / 100;
        $valorInicial = $quantidadeInicial * $preco;

        if (!is_finite($valorInicial)) {
            return ["A projeção gerou valores fora do limite numérico.", null];
        }

        for ($mes = 1; $mes <= $prazoMeses; $mes++) {
            $provento = $quantidade * $preco * $taxa;
            if (!is_finite($provento)) {
                return ["A projeção gerou valores fora do limite numérico.", null];
            }

            $saldoProventos += $provento;
            $proventosRecebidos += $provento;

            if (!is_finite($saldoProventos) || !is_finite($proventosRecebidos)) {
                return ["A projeção gerou valores fora do limite numérico.", null];
            }

            $novasCotas = floor(($saldoProventos + 1e-8) / $preco);

            if ($novasCotas > 0) {
                $quantidade += $novasCotas;
                $cotasReinvestidas += $novasCotas;
                $saldoProventos -= $novasCotas * $preco;
            }

            if (!is_finite($quantidade) || !is_finite($saldoProventos)) {
                return ["A projeção gerou valores fora do limite numérico.", null];
            }
        }

        $valorFinal = ($quantidade * $preco) + $saldoProventos;
        $rendaInicial = $quantidadeInicial * $preco * $taxa;
        $rendaFinal = $quantidade * $preco * $taxa;

        foreach ([
            $valorFinal,
            $rendaInicial,
            $rendaFinal,
            $proventosRecebidos,
            $cotasReinvestidas,
        ] as $valor) {
            if (!is_finite($valor)) {
                return ["A projeção gerou valores fora do limite numérico.", null];
            }
        }

        $fii = [
            "ticker" => $ticker,
            "quantidade_inicial" => $quantidadeInicial,
            "quantidade_final" => $quantidade,
            "cotas_reinvestidas" => $cotasReinvestidas,
            "preco_referencia" => $preco,
            "dy_mensal" => $dyMensal,
            "renda_mensal_inicial" => $rendaInicial,
            "renda_mensal_final" => $rendaFinal,
            "saldo_proventos" => $saldoProventos,
            "proventos_recebidos" => $proventosRecebidos,
            "valor_inicial" => $valorInicial,
            "valor_final" => $valorFinal,
        ];

        return [null, [
            "ticker" => $ticker,
            "prazo_anos" => $prazoAnos,
            "prazo_meses" => $prazoMeses,
            "meses" => $prazoMeses,
            "total_inicial" => $valorInicial,
            "total_final" => $valorFinal,
            "total_proventos" => $proventosRecebidos,
            "total_cotas_reinvestidas" => $cotasReinvestidas,
            "fiis" => [$fii],
        ]];
    }

    /* Pontos (mês x valor) usados no gráfico de evolução. */
    public static function serieGrafico(array $resultado): array
    {
        $dados = [];

        $tipoJuros = $resultado["tipo_juros"];
        $aporteInicial = (float) $resultado["aporte_inicial"];
        $aporteMensal = (float) $resultado["aporte_mensal"];
        $rentabilidade = (float) $resultado["rentabilidade_anual"];
        $meses = (int) ($resultado["prazo_meses"]
            ?? ((int) $resultado["prazo_anos"] * 12));

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
            "usar_cdi" => !empty($resultado["usar_cdi"]),
            "aporte_inicial" => (float) ($resultado["aporte_inicial"] ?? 0),
            "aporte_mensal" => (float) ($resultado["aporte_mensal"] ?? 0),
            "rentabilidade_anual" => (float) ($resultado["rentabilidade_anual"] ?? 0),
            "prazo_anos" => (float) ($resultado["prazo_anos"] ?? 0),
            "prazo_meses" => (int) ($resultado["prazo_meses"] ?? 0),
            "montante" => (float) ($resultado["montante"] ?? 0),
            "total_aportado" => (float) ($resultado["total_aportado"] ?? 0),
            "rendimento" => (float) ($resultado["rendimento"] ?? 0),
            "rentabilidade_resultado" => (float) ($resultado["rentabilidade_resultado"] ?? 0),
            "meses_para_milhao" => $resultado["meses_para_milhao"] ?? null,
        ];
    }

    private static function normalizarPrazo($valorRaw, $unidadeRaw): array
    {
        $valor = is_scalar($valorRaw)
            ? filter_var($valorRaw, FILTER_VALIDATE_INT)
            : false;
        $unidade = is_string($unidadeRaw) ? $unidadeRaw : "";

        if ($valor === false || !in_array($unidade, ["anos", "meses"], true)) {
            return ["Informe um prazo válido em anos ou meses.", 0, 0.0];
        }

        if ($unidade === "anos") {
            if ($valor < 1 || $valor > 100) {
                return ["O prazo deve ser de 1 a 100 anos ou de 1 a 1.200 meses.", 0, 0.0];
            }

            $meses = $valor * 12;
        } else {
            if ($valor < 1 || $valor > 1200) {
                return ["O prazo deve ser de 1 a 100 anos ou de 1 a 1.200 meses.", 0, 0.0];
            }

            $meses = $valor;
        }

        return [null, $meses, $meses / 12];
    }
}
