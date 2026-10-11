<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Histórico - INVESTEGRA
    </title>


    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >


    <style>

        /*
         * Pequenos ajustes exclusivos
         * desta página.
         */

        .cards-historico {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 20px;

        }


        .card-historico {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 3px 15px
                rgba(0, 0, 0, 0.05);

        }


        .card-historico span {

            display: block;

            color: #777;

            font-size: 14px;

            margin-bottom: 10px;

        }


        .card-historico strong {

            font-size: 22px;

            color: #00a86b;

        }


        .positivo {

            color: #00a86b !important;

        }


        .negativo {

            color: #d33 !important;

        }


        .grafico-historico {

            position: relative;

            width: 100%;

            height: 350px;

        }


        .tabela-container {

            width: 100%;

            overflow-x: auto;

        }


        .tabela-historico {

            width: 100%;

            border-collapse: collapse;

        }


        .tabela-historico th {

            text-align: left;

            padding: 14px 10px;

            color: #666;

            font-size: 14px;

            border-bottom:
                1px solid #ddd;

        }


        .tabela-historico td {

            padding: 15px 10px;

            border-bottom:
                1px solid #eee;

            color: #444;

        }


        .tabela-historico tr:last-child td {

            border-bottom: none;

        }


        .tabela-historico td strong {

            color: #00a86b;

        }


        .primeiro-registro {

            color: #999;

            font-size: 13px;

        }


        .botao-voltar {

            display: inline-block;

            margin-top: 5px;

            padding: 11px 18px;

            background: #00a86b;

            color: white;

            text-decoration: none;

            border-radius: 7px;

            font-weight: bold;

            transition: 0.2s;

        }


        .botao-voltar:hover {

            background: #008f5b;

        }


        @media (max-width: 1000px) {

            .cards-historico {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media (max-width: 650px) {

            .cards-historico {

                grid-template-columns: 1fr;

            }


            .grafico-historico {

                height: 280px;

            }

        }

    </style>

</head>


<body>

<div class="layout">


    <!-- =================================================
         SIDEBAR
    ================================================== -->

    <aside class="sidebar">

        <a href="dashboard.php" class="logo-link" aria-label="Ir para o Dashboard">
            <div class="logo">
                INVESTEGRA
            </div>
        </a>


        <nav>

            <a href="resultado_quiz.php" class="perfil-nav" aria-label="Perfil">
                Perfil
            </a>
            <a href="carteiras.php">
                Carteiras
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
            <a href="historico.php"
                class="ativo"
            >
                Histórico
            </a>
            <a href="indicadores.php">
                Indicadores
            </a>
            <a href="alertas.php">
                Alertas
            </a>
        </nav>


    </aside>


    <!-- =================================================
         CONTEÚDO
    ================================================== -->

    <main class="conteudo">


        <header>

            <h1>
                Histórico patrimonial
            </h1>

            <p>
                Acompanhe a evolução do seu patrimônio
                ao longo do tempo.
            </p>

        </header>


        <!-- =================================================
             CARDS
        ================================================== -->

        <section class="cards-historico">


            <div class="card-historico">

                <span>
                    Patrimônio atual
                </span>

                <strong>

                    R$

                    <?= number_format(
                        $patrimonio_atual,
                        2,
                        ",",
                        "."
                    ) ?>

                </strong>

            </div>


            <div class="card-historico">

                <span>
                    Maior patrimônio
                </span>

                <strong>

                    R$

                    <?= number_format(
                        $maior_patrimonio,
                        2,
                        ",",
                        "."
                    ) ?>

                </strong>

            </div>


            <div class="card-historico">

                <span>
                    Menor patrimônio
                </span>

                <strong>

                    R$

                    <?= number_format(
                        $menor_patrimonio,
                        2,
                        ",",
                        "."
                    ) ?>

                </strong>

            </div>


            <div class="card-historico">

                <span>
                    Variação no período
                </span>


                <?php if (
                    count($historico) >= 2
                ): ?>

                    <strong
                        class="<?=
                            $percentual_total >= 0
                                ? 'positivo'
                                : 'negativo'
                        ?>"
                    >

                        <?= $percentual_total >= 0
                            ? "+"
                            : ""
                        ?>

                        <?= number_format(
                            $percentual_total,
                            2,
                            ",",
                            "."
                        ) ?>%

                    </strong>

                <?php else: ?>

                    <strong>
                        --
                    </strong>

                <?php endif; ?>

            </div>


        </section>


        <!-- =================================================
             GRÁFICO
        ================================================== -->

        <section class="painel">


            <div class="painel-topo">

                <div>

                    <h2>
                        Evolução do patrimônio
                    </h2>

                </div>

            </div>


            <?php if (
                count($historico) > 0
            ): ?>


                <div class="grafico-historico">

                    <canvas
                        id="graficoHistorico"
                    ></canvas>

                </div>


            <?php else: ?>


                <p class="sem-registros">

                    Nenhum registro encontrado.

                    O histórico será criado
                    quando houver alterações
                    nos ativos da carteira.

                </p>


            <?php endif; ?>


        </section>


        <!-- =================================================
             RESUMO DA VARIAÇÃO
        ================================================== -->

        <?php if (
            count($historico) >= 2
        ): ?>


            <section class="painel">


                <h2>
                    Variação no período
                </h2>


                <br>


                <div class="informacao">

                    <span>
                        Alteração em valor
                    </span>


                    <strong
                        class="<?=
                            $variacao_total >= 0
                                ? 'positivo'
                                : 'negativo'
                        ?>"
                    >

                        <?= $variacao_total >= 0
                            ? "+"
                            : "-"
                        ?>

                        R$

                        <?= number_format(
                            abs(
                                $variacao_total
                            ),
                            2,
                            ",",
                            "."
                        ) ?>

                    </strong>

                </div>


                <div class="informacao">

                    <span>
                        Alteração percentual
                    </span>


                    <strong
                        class="<?=
                            $percentual_total >= 0
                                ? 'positivo'
                                : 'negativo'
                        ?>"
                    >

                        <?= $percentual_total >= 0
                            ? "+"
                            : ""
                        ?>

                        <?= number_format(
                            $percentual_total,
                            2,
                            ",",
                            "."
                        ) ?>%

                    </strong>

                </div>


            </section>


        <?php endif; ?>


        <!-- =================================================
             TABELA
        ================================================== -->

        <section class="painel">


            <div class="painel-topo">

                <div>

                    <h2>
                        Registros
                    </h2>

                </div>

            </div>


            <?php if (
                count($historico_completo) === 0
            ): ?>


                <p class="sem-registros">

                    Nenhum registro encontrado.

                </p>


            <?php else: ?>


                <div class="tabela-container">

                    <table class="tabela-historico">


                        <thead>

                            <tr>

                                <th>
                                    Data
                                </th>

                                <th>
                                    Patrimônio
                                </th>

                                <th>
                                    Variação
                                </th>

                                <th>
                                    Percentual
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach (
                                array_reverse(
                                    $historico_completo
                                ) as $registro
                            ): ?>


                                <tr>


                                    <td>

                                        <?= date(
                                            "d/m/Y H:i",
                                            strtotime(
                                                $registro["data"]
                                            )
                                        ) ?>

                                    </td>


                                    <td>

                                        <strong>

                                            R$

                                            <?= number_format(
                                                $registro["valor"],
                                                2,
                                                ",",
                                                "."
                                            ) ?>

                                        </strong>

                                    </td>


                                    <td>


                                        <?php if (
                                            $registro[
                                                "variacao"
                                            ] !== null
                                        ): ?>


                                            <span
                                                class="<?=
                                                    $registro[
                                                        "variacao"
                                                    ] >= 0
                                                        ? "positivo"
                                                        : "negativo"
                                                ?>"
                                            >

                                                <?= $registro[
                                                    "variacao"
                                                ] >= 0
                                                    ? "+"
                                                    : "-"
                                                ?>

                                                R$

                                                <?= number_format(
                                                    abs(
                                                        $registro[
                                                            "variacao"
                                                        ]
                                                    ),
                                                    2,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </span>


                                        <?php else: ?>


                                            <span
                                                class="primeiro-registro"
                                            >

                                                Primeiro registro

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                    <td>


                                        <?php if (
                                            $registro[
                                                "percentual"
                                            ] !== null
                                        ): ?>


                                            <span
                                                class="<?=
                                                    $registro[
                                                        "percentual"
                                                    ] >= 0
                                                        ? "positivo"
                                                        : "negativo"
                                                ?>"
                                            >

                                                <?= $registro[
                                                    "percentual"
                                                ] >= 0
                                                    ? "+"
                                                    : ""
                                                ?>

                                                <?= number_format(
                                                    $registro[
                                                        "percentual"
                                                    ],
                                                    2,
                                                    ",",
                                                    "."
                                                ) ?>%

                                            </span>


                                        <?php else: ?>


                                            <span
                                                class="primeiro-registro"
                                            >
                                                --
                                            </span>


                                        <?php endif; ?>


                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>

                </div>


            <?php endif; ?>


        </section>


        <a
            href="dashboard.php"
            class="botao-voltar"
        >
            Voltar ao Dashboard
        </a>


    </main>

</div>


<!-- =====================================================
     CHART.JS
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

const historico =
    <?= json_encode(
        $historico,
        JSON_UNESCAPED_UNICODE
    ) ?>;


const labels =
    historico.map(
        item => {

            return new Date(
                item.data_registro
            ).toLocaleDateString(
                "pt-BR"
            );

        }
    );


const valores =
    historico.map(
        item => Number(
            item.valor_total
        )
    );


function formatarMoeda(valor) {

    return "R$ " +
        Number(valor).toLocaleString(
            "pt-BR",
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );

}


const elemento =
    document.getElementById(
        "graficoHistorico"
    );


if (
    elemento &&
    historico.length > 0
) {

    new Chart(

        elemento,

        {

            type: "line",


            data: {

                labels: labels,


                datasets: [

                    {

                        label:
                            "Patrimônio",


                        data:
                            valores,


                        borderWidth:
                            3,


                        tension:
                            0.3,


                        fill:
                            true,


                        pointRadius:
                            4,


                        pointHoverRadius:
                            6

                    }

                ]

            },


            options: {

                responsive:
                    true,


                maintainAspectRatio:
                    false,


                interaction: {

                    intersect:
                        false,

                    mode:
                        "index"

                },


                plugins: {

                    legend: {

                        display:
                            false

                    },


                    tooltip: {

                        callbacks: {

                            label:
                                function (
                                    contexto
                                ) {

                                    return (
                                        "Patrimônio: " +
                                        formatarMoeda(
                                            contexto.parsed.y
                                        )
                                    );

                                }

                        }

                    }

                },


                scales: {

                    y: {

                        beginAtZero:
                            true,


                        ticks: {

                            callback:
                                function (
                                    valor
                                ) {

                                    return formatarMoeda(
                                        valor
                                    );

                                }

                        }

                    }

                }

            }

        }

    );

}

</script>


</body>

</html>