<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Histórico de Simulações - INVESTEGRA
    </title>


    <link
        rel="stylesheet"
        href="css/historico_simulacoes.css"
    >
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >

</head>


<body>


<div class="layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">

        <a href="dashboard.php" class="logo-link" aria-label="Ir para o Dashboard">
            <div class="logo">
                INVESTEGRA
            </div>
        </a>


        <nav>

            <?php require BASE_PATH . "/app/views/partials/perfil_nav.php"; ?>

            <a href="carteiras.php">
                Carteiras
            </a>

            <a href="ativos.php">
                Ativos
            </a>

            <a href="simulacao.php">
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

            <a
                href="historico_simulacoes.php"
                class="ativo"
            >
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
                    Histórico de Simulações
                </h1>

                <p>
                    Consulte as simulações realizadas
                    anteriormente.
                </p>

            </div>


            <a
                href="simulacao.php"
                class="btn-nova"
            >
                + Nova simulação
            </a>

        </div>


        <?php if ($totalSimulacoes > 0): ?>


        <!-- =================================================
             RESUMO
        ================================================== -->

        <section class="resumo-grid">


            <div class="resumo-card">

                <span>
                    Simulações realizadas
                </span>

                <strong>
                    <?php
                    echo $totalSimulacoes;
                    ?>
                </strong>

            </div>


            <div class="resumo-card">

                <span>
                    Maior montante estimado
                </span>

                <strong>

                    R$

                    <?php

                    echo number_format(
                        $maiorMontante,
                        2,
                        ",",
                        "."
                    );

                    ?>

                </strong>

            </div>


            <div class="resumo-card">

                <span>
                    Menor montante estimado
                </span>

                <strong>

                    R$

                    <?php

                    echo number_format(
                        $menorMontante,
                        2,
                        ",",
                        "."
                    );

                    ?>

                </strong>

            </div>


            <div class="resumo-card">

                <span>
                    Média dos montantes
                </span>

                <strong>

                    R$

                    <?php

                    echo number_format(
                        $mediaMontante,
                        2,
                        ",",
                        "."
                    );

                    ?>

                </strong>

            </div>


        </section>


        <!-- =================================================
             GRÁFICO
        ================================================== -->

        <section class="card-grafico">

            <div class="card-titulo">

                <h2>
                    Comparação das simulações
                </h2>

                <p>
                    Montante final estimado de cada simulação.
                </p>

            </div>


            <div class="grafico-barras">


                <?php

                /*
                |--------------------------------------------------------------------------
                | LIMITAMOS VISUALMENTE A LARGURA DAS BARRAS
                |--------------------------------------------------------------------------
                */

                foreach (
                    $simulacoes
                    as $indice => $simulacao
                ) {


                    $montante =
                        (float)
                        $simulacao["montante"];


                    if (
                        $maiorMontante > 0
                    ) {

                        $percentual =
                            (
                                $montante /
                                $maiorMontante
                            ) * 100;

                    } else {

                        $percentual = 0;

                    }


                    $tipo =
                        $simulacao["tipo_juros"]
                        === "compostos"
                        ? "Compostos"
                        : "Simples";


                    $data =
                        date(
                            "d/m/Y",
                            strtotime(
                                $simulacao["criado_em"]
                            )
                        );

                    ?>

                    <div class="barra-item">


                        <div class="barra-info">

                            <div>

                                <strong>

                                    Simulação
                                    #
                                    <?php
                                    echo $simulacao["id"];
                                    ?>

                                </strong>

                                <span>

                                    <?php
                                    echo $tipo;
                                    ?>

                                    ·

                                    <?php
                                    echo $data;
                                    ?>

                                </span>

                            </div>


                            <strong>

                                R$

                                <?php

                                echo number_format(
                                    $montante,
                                    2,
                                    ",",
                                    "."
                                );

                                ?>

                            </strong>

                        </div>


                        <div class="barra-fundo">

                            <div
                                class="barra-valor"
                                style="
                                    width:
                                    <?php
                                    echo max(
                                        2,
                                        $percentual
                                    );
                                    ?>%;
                                "
                            ></div>

                        </div>


                    </div>

                    <?php

                }

                ?>


            </div>

        </section>


        <!-- =================================================
             LISTA
        ================================================== -->

        <section class="card-historico">

            <div class="card-titulo">

                <h2>
                    Simulações realizadas
                </h2>

                <p>
                    Informações detalhadas de cada simulação.
                </p>

            </div>


            <div class="tabela-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Data
                            </th>

                            <th>
                                Tipo
                            </th>

                            <th>
                                Aporte inicial
                            </th>

                            <th>
                                Aporte mensal
                            </th>

                            <th>
                                Rentabilidade
                            </th>

                            <th>
                                Prazo
                            </th>

                            <th>
                                Montante
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php

                        foreach (
                            $simulacoes
                            as $simulacao
                        ):

                        ?>

                        <tr>

                            <td>

                                <?php

                                echo date(
                                    "d/m/Y H:i",
                                    strtotime(
                                        $simulacao["criado_em"]
                                    )
                                );

                                ?>

                            </td>


                            <td>

                                <span
                                    class="badge
                                    <?php

                                    echo
                                        $simulacao["tipo_juros"]
                                        === "compostos"
                                        ? "badge-compostos"
                                        : "badge-simples";

                                    ?>"
                                >

                                    <?php

                                    echo
                                        $simulacao["tipo_juros"]
                                        === "compostos"
                                        ? "Compostos"
                                        : "Simples";

                                    ?>

                                </span>

                            </td>


                            <td>

                                R$

                                <?php

                                echo number_format(
                                    $simulacao[
                                        "aporte_inicial"
                                    ],
                                    2,
                                    ",",
                                    "."
                                );

                                ?>

                            </td>


                            <td>

                                R$

                                <?php

                                echo number_format(
                                    $simulacao[
                                        "aporte_mensal"
                                    ],
                                    2,
                                    ",",
                                    "."
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo number_format(
                                    $simulacao[
                                        "rentabilidade"
                                    ],
                                    2,
                                    ",",
                                    "."
                                );

                                ?>

                                %

                            </td>


                            <td>

                                <?php

                                echo $simulacao[
                                    "prazo_anos"
                                ];

                                ?>

                                <?php

                                echo
                                    $simulacao[
                                        "prazo_anos"
                                    ] == 1
                                    ? " ano"
                                    : " anos";

                                ?>

                            </td>


                            <td class="montante">

                                R$

                                <?php

                                echo number_format(
                                    $simulacao[
                                        "montante"
                                    ],
                                    2,
                                    ",",
                                    "."
                                );

                                ?>

                            </td>

                        </tr>


                        <?php

                        endforeach;

                        ?>

                    </tbody>

                </table>

            </div>

        </section>


        <?php else: ?>


        <!-- =================================================
             ESTADO VAZIO
        ================================================== -->

        <section class="estado-vazio">

            <div class="estado-icone">
                📊
            </div>


            <h2>
                Nenhuma simulação realizada
            </h2>


            <p>
                Quando você realizar uma simulação,
                ela aparecerá aqui automaticamente.
            </p>


            <a
                href="simulacao.php"
                class="btn-nova"
            >
                Fazer primeira simulação
            </a>

        </section>


        <?php endif; ?>


    </main>

</div>


</body>

</html>