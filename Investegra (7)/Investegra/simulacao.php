<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";


/*
|--------------------------------------------------------------------------
| RESULTADO DA SIMULAÇÃO
|--------------------------------------------------------------------------
*/

$resultado = null;

if (isset($_SESSION["resultado_simulacao"])) {

    $resultado = $_SESSION["resultado_simulacao"];

    

    unset($_SESSION["resultado_simulacao"]);
}


/*
|--------------------------------------------------------------------------
| PREPARAR DADOS DO GRÁFICO
|--------------------------------------------------------------------------
*/

$dadosGrafico = [];

if ($resultado !== null) {

    $tipoJuros = $resultado["tipo_juros"];

    $aporteInicial =
        (float) $resultado["aporte_inicial"];

    $aporteMensal =
        (float) $resultado["aporte_mensal"];

    $rentabilidade =
        (float) $resultado["rentabilidade_anual"];

    $prazoAnos =
        (int) $resultado["prazo_anos"];

    $meses =
        $prazoAnos * 12;


    /*
    |--------------------------------------------------------------------------
    | JUROS COMPOSTOS
    |--------------------------------------------------------------------------
    */

    if ($tipoJuros === "compostos") {

        $taxaMensal =
            pow(
                1 + ($rentabilidade / 100),
                1 / 12
            ) - 1;

        $valor = $aporteInicial;


        $dadosGrafico[] = [
            "mes" => 0,
            "valor" => $valor
        ];


        for ($mes = 1; $mes <= $meses; $mes++) {

            $valor =
                ($valor * (1 + $taxaMensal))
                + $aporteMensal;


            /*
            | Para evitar um gráfico com centenas
            | de pontos em prazos muito longos,
            | mostramos todos os meses até 24 meses
            | e depois apenas os anos.
            */

            if (
                $mes <= 24 ||
                $mes % 12 === 0 ||
                $mes === $meses
            ) {

                $dadosGrafico[] = [
                    "mes" => $mes,
                    "valor" => $valor
                ];

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | JUROS SIMPLES
    |--------------------------------------------------------------------------
    */

    else {

        $taxaAnual =
            $rentabilidade / 100;


        $dadosGrafico[] = [
            "mes" => 0,
            "valor" => $aporteInicial
        ];


        for ($mes = 1; $mes <= $meses; $mes++) {

            $anosInvestidos =
                $mes / 12;


            /*
            | Valor do aporte inicial
            */

            $valorInicial =
                $aporteInicial *
                (
                    1 +
                    ($taxaAnual * $anosInvestidos)
                );


            /*
            | Valor acumulado dos aportes
            */

            $valorAportes = 0;


            for (
                $aporteMes = 1;
                $aporteMes <= $mes;
                $aporteMes++
            ) {

                $anosRestantes =
                    ($mes - $aporteMes + 1) / 12;


                $valorAportes +=
                    $aporteMensal *
                    (
                        1 +
                        ($taxaAnual * $anosRestantes)
                    );

            }


            $valor =
                $valorInicial +
                $valorAportes;


            if (
                $mes <= 24 ||
                $mes % 12 === 0 ||
                $mes === $meses
            ) {

                $dadosGrafico[] = [
                    "mes" => $mes,
                    "valor" => $valor
                ];

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| CONTEXTO DO ASSISTENTE
|--------------------------------------------------------------------------
*/

$contextoIA = "simulacao";

$dadosIA = null;

if ($resultado !== null) {

    $dadosIA = [
        "tipo_juros" => $resultado["tipo_juros"] ?? "",
        "aporte_inicial" => (float) ($resultado["aporte_inicial"] ?? 0),
        "aporte_mensal" => (float) ($resultado["aporte_mensal"] ?? 0),
        "rentabilidade_anual" => (float) ($resultado["rentabilidade_anual"] ?? 0),
        "prazo_anos" => (int) ($resultado["prazo_anos"] ?? 0),
        "montante" => (float) ($resultado["montante"] ?? 0),
        "total_aportado" => (float) ($resultado["total_aportado"] ?? 0),
        "rendimento" => (float) ($resultado["rendimento"] ?? 0),
        "rentabilidade_resultado" => (float) ($resultado["rentabilidade_resultado"] ?? 0)
    ];
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Simulações - INVESTEGRA
    </title>


    <link
        rel="stylesheet"
        href="css/simulacao.css"
    >

    <link
        rel="stylesheet"
        href="css/assistente.css"
    >

</head>


<body>


<div class="layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">

        <div class="logo">
            INVESTEGRA
        </div>


        <nav>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="perfil.php">
                Perfil
            </a>

            <a href="carteiras.php">
                Carteiras
            </a>

            <a href="ativos.php">
                Ativos
            </a>

            <a
                href="simulacao.php"
                class="ativo"
            >
                Simulações
            </a>

            <a href="analises.php">
                Análises
            </a>

            <a href="recomendacoes.php">
                Recomendações
            </a>

            <a href="alertas.php">
                Alertas
            </a>

            <a href="historico_simulacoes.php">
                Histórico
            </a>

            <a href="logout.php">
                Sair
            </a>

        </nav>

    </aside>


    <!-- =====================================================
         CONTEÚDO
    ====================================================== -->

    <main class="conteudo">


        <div class="cabecalho">

            <div>

                <h1>
                    Simulação de Investimentos
                </h1>

                <p>
                    Estime a evolução de um investimento
                    ao longo do tempo.
                </p>

            </div>


            <a
                href="historico_simulacoes.php"
                class="btn-historico"
            >
                Ver histórico
            </a>

        </div>


        <!-- =================================================
             FORMULÁRIO + INFORMAÇÕES
        ================================================== -->

        <section class="simulacao-container">


            <!-- FORMULÁRIO -->

            <div class="card-simulacao">

                <div class="card-titulo">

                    <h2>
                        Nova simulação
                    </h2>

                    <p>
                        Informe os parâmetros do investimento.
                    </p>

                </div>


                <form
                    action="processar_simulacao.php"
                    method="POST"
                >


                    <!-- TIPO -->

                    <div class="campo">

                        <label for="tipo_juros">
                            Tipo de juros
                        </label>

                        <select
                            name="tipo_juros"
                            id="tipo_juros"
                            required
                        >

                            <option value="compostos">
                                Juros compostos
                            </option>

                            <option value="simples">
                                Juros simples
                            </option>

                        </select>

                    </div>


                    <!-- APORTE INICIAL -->

                    <div class="campo">

                        <label for="aporte_inicial">
                            Aporte inicial
                        </label>

                        <div class="campo-prefixo">

                            <span>
                                R$
                            </span>

                            <input
                                type="number"
                                name="aporte_inicial"
                                id="aporte_inicial"
                                min="0"
                                step="0.01"
                                placeholder="1000.00"
                                required
                            >

                        </div>

                    </div>


                    <!-- APORTE MENSAL -->

                    <div class="campo">

                        <label for="aporte_mensal">
                            Aporte mensal
                        </label>

                        <div class="campo-prefixo">

                            <span>
                                R$
                            </span>

                            <input
                                type="number"
                                name="aporte_mensal"
                                id="aporte_mensal"
                                min="0"
                                step="0.01"
                                placeholder="200.00"
                                required
                            >

                        </div>

                    </div>


                    <!-- RENTABILIDADE -->

                    <div class="campo">

                        <label for="rentabilidade">
                            Rentabilidade anual esperada
                        </label>

                        <div class="campo-prefixo">

                            <span>
                                %
                            </span>

                            <input
                                type="number"
                                name="rentabilidade"
                                id="rentabilidade"
                                min="0"
                                step="0.01"
                                placeholder="10"
                                required
                            >

                        </div>

                        <small class="campo-ajuda">
                            Taxa anual utilizada apenas
                            para fins de simulação.
                        </small>

                    </div>


                    <!-- PRAZO -->

                    <div class="campo">

                        <label for="prazo_anos">
                            Prazo do investimento
                        </label>

                        <div class="campo-prefixo">

                            <span>
                                Anos
                            </span>

                            <input
                                type="number"
                                name="prazo_anos"
                                id="prazo_anos"
                                min="1"
                                max="100"
                                placeholder="10"
                                required
                            >

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="btn-simular"
                    >
                        Simular investimento
                    </button>

                </form>

            </div>


            <!-- INFORMAÇÕES -->

            <div class="card-informacao">

                <h2>
                    Como funciona?
                </h2>


                <p>
                    A simulação estima a evolução de um
                    investimento utilizando os parâmetros
                    informados pelo usuário.
                </p>


                <div class="info-item">

                    <strong>
                        Aporte inicial
                    </strong>

                    <span>
                        Valor aplicado no início da simulação.
                    </span>

                </div>


                <div class="info-item">

                    <strong>
                        Aporte mensal
                    </strong>

                    <span>
                        Valor acrescentado ao investimento
                        todos os meses.
                    </span>

                </div>


                <div class="info-item">

                    <strong>
                        Rentabilidade
                    </strong>

                    <span>
                        Taxa anual utilizada no cálculo.
                    </span>

                </div>


                <div class="info-item">

                    <strong>
                        Prazo
                    </strong>

                    <span>
                        Período utilizado na simulação.
                    </span>

                </div>


                <div class="aviso-simulacao">

                    <strong>
                        ⚠ Atenção
                    </strong>

                    <p>
                        Os resultados são estimativas matemáticas
                        e não representam garantia de rentabilidade
                        futura.
                    </p>

                </div>

            </div>

        </section>


        <!-- =================================================
             RESULTADO
        ================================================== -->

        <?php if ($resultado !== null): ?>


        <section class="resultado-container">


            <div class="card-resultado">


                <div class="card-titulo">

                    <h2>
                        Resultado da simulação
                    </h2>

                    <p>

                        <?php

                        echo $resultado["tipo_juros"] === "compostos"
                            ? "Juros compostos"
                            : "Juros simples";

                        ?>

                    </p>

                </div>


                <!-- VALOR PRINCIPAL -->

                <div class="resultado-principal">

                    <span>
                        Montante final estimado
                    </span>

                    <strong>

                        R$

                        <?php

                        echo number_format(
                            $resultado["montante"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                    </strong>

                </div>


                <!-- INDICADORES -->

                <div class="resultado-grid">


                    <div class="resultado-item">

                        <span>
                            Total aportado
                        </span>

                        <strong>

                            R$

                            <?php

                            echo number_format(
                                $resultado["total_aportado"],
                                2,
                                ",",
                                "."
                            );

                            ?>

                        </strong>

                    </div>


                    <div class="resultado-item">

                        <span>
                            Rendimento estimado
                        </span>

                        <strong>

                            R$

                            <?php

                            echo number_format(
                                $resultado["rendimento"],
                                2,
                                ",",
                                "."
                            );

                            ?>

                        </strong>

                    </div>


                    <div class="resultado-item">

                        <span>
                            Rentabilidade total
                        </span>

                        <strong>

                            <?php

                            echo number_format(
                                $resultado["rentabilidade_resultado"],
                                2,
                                ",",
                                "."
                            );

                            ?>

                            %

                        </strong>

                    </div>


                    <div class="resultado-item">

                        <span>
                            Prazo
                        </span>

                        <strong>

                            <?php
                            echo $resultado["prazo_anos"];
                            ?>

                            <?php

                            echo
                                $resultado["prazo_anos"] == 1
                                ? " ano"
                                : " anos";

                            ?>

                        </strong>

                    </div>

                </div>


                <!-- DETALHES -->

                <div class="resultado-detalhes">

                    <p>

                        <strong>
                            Aporte inicial:
                        </strong>

                        R$

                        <?php

                        echo number_format(
                            $resultado["aporte_inicial"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                    </p>


                    <p>

                        <strong>
                            Aporte mensal:
                        </strong>

                        R$

                        <?php

                        echo number_format(
                            $resultado["aporte_mensal"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                    </p>


                    <p>

                        <strong>
                            Rentabilidade considerada:
                        </strong>

                        <?php

                        echo number_format(
                            $resultado["rentabilidade_anual"],
                            2,
                            ",",
                            "."
                        );

                        ?>

                        % ao ano

                    </p>

                </div>

            </div>


            <!-- =================================================
                 GRÁFICO
            ================================================== -->

            <div class="card-grafico">

                <div class="grafico-cabecalho">

                    <div>

                        <h2>
                            Evolução estimada
                        </h2>

                        <p>
                            Crescimento do patrimônio ao longo
                            do período simulado.
                        </p>

                    </div>

                </div>


                <div class="grafico-wrapper">

                    <svg
                        class="grafico-svg"
                        viewBox="0 0 900 380"
                        preserveAspectRatio="none"
                    >

                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | CONFIGURAÇÕES DO GRÁFICO
                        |--------------------------------------------------------------------------
                        */

                        $largura = 900;

                        $altura = 380;

                        $margemEsquerda = 75;

                        $margemDireita = 25;

                        $margemSuperior = 30;

                        $margemInferior = 55;


                        $areaLargura =
                            $largura
                            - $margemEsquerda
                            - $margemDireita;


                        $areaAltura =
                            $altura
                            - $margemSuperior
                            - $margemInferior;


                        $maiorValor = 0;


                        foreach (
                            $dadosGrafico
                            as $ponto
                        ) {

                            if (
                                $ponto["valor"]
                                > $maiorValor
                            ) {

                                $maiorValor =
                                    $ponto["valor"];

                            }

                        }


                        if ($maiorValor <= 0) {
                            $maiorValor = 1;
                        }


                        /*
                        | Arredondamento do eixo Y
                        */

                        $escala =
                            pow(
                                10,
                                floor(
                                    log10($maiorValor)
                                )
                            );


                        $maximoEixo =
                            ceil(
                                $maiorValor / $escala
                            ) * $escala;


                        if (
                            $maximoEixo <= $maiorValor
                        ) {

                            $maximoEixo *= 1.1;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | LINHAS HORIZONTAIS
                        |--------------------------------------------------------------------------
                        */

                        for ($i = 0; $i <= 4; $i++) {

                            $valorEixo =
                                $maximoEixo
                                * ($i / 4);


                            $y =
                                $margemSuperior
                                +
                                $areaAltura
                                -
                                (
                                    $areaAltura
                                    * ($i / 4)
                                );

                            ?>

                            <line
                                x1="<?php echo $margemEsquerda; ?>"
                                y1="<?php echo $y; ?>"
                                x2="<?php echo $largura - $margemDireita; ?>"
                                y2="<?php echo $y; ?>"
                                class="linha-grade"
                            />

                            <text
                                x="<?php echo $margemEsquerda - 10; ?>"
                                y="<?php echo $y + 4; ?>"
                                text-anchor="end"
                                class="texto-eixo"
                            >
                                R$ <?php
                                echo number_format(
                                    $valorEixo,
                                    0,
                                    ",",
                                    "."
                                );
                                ?>
                            </text>

                            <?php
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | LINHA DO GRÁFICO
                        |--------------------------------------------------------------------------
                        */

                        $pontos = [];

                        $quantidadePontos =
                            count($dadosGrafico);


                        foreach (
                            $dadosGrafico
                            as $indice => $ponto
                        ) {

                            if (
                                $quantidadePontos > 1
                            ) {

                                $x =
                                    $margemEsquerda
                                    +
                                    (
                                        $areaLargura
                                        *
                                        (
                                            $indice /
                                            ($quantidadePontos - 1)
                                        )
                                    );

                            } else {

                                $x =
                                    $margemEsquerda;

                            }


                            $y =
                                $margemSuperior
                                +
                                $areaAltura
                                -
                                (
                                    $areaAltura
                                    *
                                    (
                                        $ponto["valor"]
                                        /
                                        $maximoEixo
                                    )
                                );


                            $pontos[] =
                                $x . "," . $y;

                        }


                        $polyline =
                            implode(
                                " ",
                                $pontos
                            );

                        ?>


                        <polyline
                            points="<?php echo $polyline; ?>"
                            class="linha-grafico"
                        />


                        <?php

                        /*
                        |--------------------------------------------------------------------------
                        | PONTOS
                        |--------------------------------------------------------------------------
                        */

                        foreach (
                            $dadosGrafico
                            as $indice => $ponto
                        ) {

                            if (
                                $quantidadePontos > 1
                            ) {

                                $x =
                                    $margemEsquerda
                                    +
                                    (
                                        $areaLargura
                                        *
                                        (
                                            $indice /
                                            ($quantidadePontos - 1)
                                        )
                                    );

                            } else {

                                $x =
                                    $margemEsquerda;

                            }


                            $y =
                                $margemSuperior
                                +
                                $areaAltura
                                -
                                (
                                    $areaAltura
                                    *
                                    (
                                        $ponto["valor"]
                                        /
                                        $maximoEixo
                                    )
                                );


                            /*
                            | Mostra apenas alguns
                            | rótulos para não poluir.
                            */

                            $mostrarRotulo =
                                $indice === 0
                                ||
                                $indice ===
                                $quantidadePontos - 1
                                ||
                                (
                                    $ponto["mes"] <= 24
                                    &&
                                    $ponto["mes"] % 6 === 0
                                )
                                ||
                                (
                                    $ponto["mes"] > 24
                                    &&
                                    $ponto["mes"] % 12 === 0
                                );

                            ?>

                            <circle
                                cx="<?php echo $x; ?>"
                                cy="<?php echo $y; ?>"
                                r="4"
                                class="ponto-grafico"
                            />

                            <?php

                            if ($mostrarRotulo) {

                                ?>

                                <text
                                    x="<?php echo $x; ?>"
                                    y="<?php echo $altura - 20; ?>"
                                    text-anchor="middle"
                                    class="texto-eixo"
                                >

                                    <?php

                                    if (
                                        $ponto["mes"] == 0
                                    ) {

                                        echo "Início";

                                    } elseif (
                                        $ponto["mes"] < 12
                                    ) {

                                        echo
                                            $ponto["mes"]
                                            . "m";

                                    } else {

                                        echo
                                            number_format(
                                                $ponto["mes"] / 12,
                                                0
                                            )
                                            . " ano"
                                            .
                                            (
                                                $ponto["mes"] / 12 != 1
                                                ? "s"
                                                : ""
                                            );

                                    }

                                    ?>

                                </text>

                                <?php

                            }

                        }

                        ?>

                    </svg>

                </div>


                <div class="legenda-grafico">

                    <span>
                        ● Evolução estimada
                    </span>

                    <span>
                        <?php

                        echo $resultado["tipo_juros"]
                            === "compostos"
                            ? "Juros compostos"
                            : "Juros simples";

                        ?>
                    </span>

                </div>

            </div>


        </section>


        <?php endif; ?>


        <!-- =================================================
             ASSISTENTE
        ================================================== -->

        <?php

        include "components/assistente_ia.php";

        ?>


    </main>

</div>


</body>

</html>