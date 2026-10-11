<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard - INVESTEGRA
    </title>


    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >


    <!-- Chart.js -->

    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>


    <!-- Porcentagens do gráfico -->

    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"
    ></script>


    <style>

        /*
        =====================================================
        PEQUENOS AJUSTES DO DASHBOARD
        =====================================================
        */

        .indicador-diversificacao {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            padding: 15px 0;

            border-bottom:
                1px solid #eee;

        }


        .indicador-diversificacao
        span {

            color: #777;

        }


        .indicador-diversificacao
        strong {

            font-size: 15px;

        }


        .concentracao-alta {

            color: #d33 !important;

        }


        .concentracao-media {

            color: #c58a00 !important;

        }


        .concentracao-baixa {

            color: #00a86b !important;

        }


        .perfil-conservador {

            color: #3b82f6 !important;

        }


        .perfil-moderado {

            color: #c58a00 !important;

        }


        .perfil-arrojado {

            color: #d33 !important;

        }


        .texto-concentracao {

            margin-top: 15px;

            color: #777;

            line-height: 1.5;

            font-size: 14px;

        }


        .historico-item
        a {

            text-decoration: none;

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


            <a href="analise_carteira.php">
                Análises
            </a>


            <a href="recomendacoes.php">
                Recomendações
            </a>


            <a href="historico.php">
                Histórico
            </a>


            <a href="indicadores.php">
                Indicadores
            </a>


            <a href="alertas.php">
                Alertas
            </a>

        </nav>


        <a
            href="logout.php"
            class="logout"
        >
            Sair
        </a>

    </aside>


    <!-- =================================================
         CONTEÚDO
    ================================================== -->

    <main class="conteudo">


        <header>

            <h1>

                Olá,
                <?= htmlspecialchars(
                    $nome_usuario
                ) ?>!

            </h1>


            <p>

                Aqui está um resumo dos
                seus investimentos.

            </p>

        </header>


        <!-- =================================================
             CARDS PRINCIPAIS
        ================================================== -->

        <section class="cards">


            <div class="card">

                <span>
                    Patrimônio Total
                </span>


                <strong>

                    R$

                    <?= number_format(
                        $patrimonio,
                        2,
                        ",",
                        "."
                    ) ?>

                </strong>

            </div>


            <div class="card">

                <span>
                    Carteiras
                </span>


                <strong>
                    <?= $total_carteiras ?>
                </strong>

            </div>


            <div class="card">

                <span>
                    Ativos
                </span>


                <strong>
                    <?= $total_ativos ?>
                </strong>

            </div>


            <div class="card">

                <span>
                    Perfil de risco
                </span>


                <strong
                    class="<?= $classe_perfil ?>"
                >

                    <?= htmlspecialchars(
                        $perfil_risco
                    ) ?>

                </strong>

            </div>


        </section>


        <!-- =================================================
             PRIMEIRA LINHA
        ================================================== -->

        <section class="dashboard-grid">


            <!-- EVOLUÇÃO -->

            <div class="painel">

                <div class="painel-topo">

                    <h2>
                        Evolução do patrimônio
                    </h2>


                    <a href="historico.php">
                        Ver histórico
                    </a>

                </div>


                <?php if (
                    count($historico_grafico) > 0
                ): ?>


                    <div class="grafico-container">

                        <canvas
                            id="graficoPatrimonio"
                        ></canvas>

                    </div>


                    <?php if (
                        $variacao_ultimo !== null
                    ): ?>

                        <p class="texto-concentracao">

                            Variação em relação
                            ao registro anterior:

                            <strong
                                class="<?=
                                    $variacao_ultimo >= 0
                                        ? 'positivo-dashboard'
                                        : 'negativo-dashboard'
                                ?>"
                            >

                                <?= $variacao_ultimo >= 0
                                    ? "+"
                                    : ""
                                ?>

                                <?= number_format(
                                    $variacao_ultimo,
                                    2,
                                    ",",
                                    "."
                                ) ?>%

                            </strong>

                        </p>

                    <?php endif; ?>


                <?php else: ?>


                    <p class="sem-registros">

                        Ainda não existem
                        registros suficientes
                        para exibir a evolução.

                    </p>


                <?php endif; ?>

            </div>


            <!-- DISTRIBUIÇÃO -->

            <div class="painel">

                <div class="painel-topo">

                    <h2>
                        Distribuição da carteira
                    </h2>


                    <a href="analise_carteira.php">
                        Ver análise
                    </a>

                </div>


                <?php if (
                    count($distribuicao) > 0
                ): ?>


                    <div class="grafico-distribuicao">

                        <canvas
                            id="graficoDistribuicao"
                        ></canvas>

                    </div>


                    <div class="legenda-categorias">

                        <?php foreach (
                            $distribuicao
                            as $categoria
                        ): ?>

                            <div
                                class="categoria-item"
                            >

                                <span>

                                    <?= htmlspecialchars(
                                        $categoria[
                                            "categoria"
                                        ]
                                    ) ?>

                                </span>


                                <strong>

                                    <?= number_format(
                                        $categoria[
                                            "percentual"
                                        ],
                                        1,
                                        ",",
                                        "."
                                    ) ?>%

                                </strong>

                            </div>

                        <?php endforeach; ?>

                    </div>


                <?php else: ?>


                    <p class="sem-registros">

                        Você ainda não possui
                        ativos cadastrados.

                    </p>


                <?php endif; ?>

            </div>


        </section>


        <!-- =================================================
             PATRIMÔNIO POR CARTEIRA
        ================================================== -->

        <section class="painel painel-barras">


            <div class="painel-topo">

                <h2>
                    Patrimônio por carteira
                </h2>


                <a href="carteiras.php">
                    Ver carteiras
                </a>

            </div>


            <?php if (
                count($patrimonio_carteiras) > 0
            ): ?>


                <div class="grafico-barras-container">

                    <canvas
                        id="graficoCarteiras"
                    ></canvas>

                </div>


            <?php else: ?>


                <p class="sem-registros">

                    Você ainda não possui
                    carteiras cadastradas.

                </p>


            <?php endif; ?>


        </section>


        <!-- =================================================
             ANÁLISE DE DIVERSIFICAÇÃO
        ================================================== -->

        <section class="painel">


            <div class="painel-topo">

                <h2>
                    Diversificação
                </h2>


                <a href="analise_carteira.php">
                    Analisar carteira
                </a>

            </div>


            <?php if (
                $maior_categoria !== null
            ): ?>


                <div class="indicador-diversificacao">

                    <span>
                        Situação
                    </span>


                    <strong
                        class="<?= $classe_diversificacao ?>"
                    >

                        <?= $situacao_diversificacao ?>

                    </strong>

                </div>


                <div class="indicador-diversificacao">

                    <span>
                        Maior categoria
                    </span>


                    <strong>

                        <?= htmlspecialchars(
                            $maior_categoria
                        ) ?>

                    </strong>

                </div>


                <div class="indicador-diversificacao">

                    <span>
                        Participação da maior categoria
                    </span>


                    <strong
                        class="<?= $classe_diversificacao ?>"
                    >

                        <?= number_format(
                            $maior_percentual,
                            1,
                            ",",
                            "."
                        ) ?>%

                    </strong>

                </div>


                <p class="texto-concentracao">

                    Este indicador mostra quanto do
                    patrimônio está concentrado na
                    categoria com maior participação.
                    Ele serve como uma visão inicial
                    da composição da carteira.

                </p>


            <?php else: ?>


                <p class="sem-registros">

                    Cadastre ativos para visualizar
                    a distribuição da carteira.

                </p>


            <?php endif; ?>


        </section>


        <!-- =================================================
             HISTÓRICO RECENTE
        ================================================== -->

        <section class="painel historico-recente">


            <div class="painel-topo">

                <h2>
                    Histórico recente
                </h2>


                <a href="historico.php">
                    Ver histórico completo
                </a>

            </div>


            <?php if (
                count($ultimos_registros) > 0
            ): ?>


                <div class="historico-lista">


                    <?php foreach (
                        $ultimos_registros
                        as $registro
                    ): ?>


                        <div
                            class="historico-item"
                        >

                            <span>

                                <?= date(
                                    "d/m/Y H:i",
                                    strtotime(
                                        $registro[
                                            "data_registro"
                                        ]
                                    )
                                ) ?>

                            </span>


                            <strong>

                                R$

                                <?= number_format(
                                    $registro[
                                        "valor_total"
                                    ],
                                    2,
                                    ",",
                                    "."
                                ) ?>

                            </strong>

                        </div>


                    <?php endforeach; ?>


                </div>


            <?php else: ?>


                <p class="sem-registros">

                    Ainda não existem registros
                    de patrimônio.

                </p>


            <?php endif; ?>


        </section>


    </main>

</div>


<!-- =====================================================
     SCRIPTS DOS GRÁFICOS
===================================================== -->

<script>


/*
|--------------------------------------------------------------------------
| Dados vindos do PHP
|--------------------------------------------------------------------------
*/

const labelsHistorico =
    <?= json_encode(
        $labels_historico,
        JSON_UNESCAPED_UNICODE
    ) ?>;


const valoresHistorico =
    <?= json_encode(
        $valores_historico
    ) ?>;


const labelsCategorias =
    <?= json_encode(
        $labels_categorias,
        JSON_UNESCAPED_UNICODE
    ) ?>;


const valoresCategorias =
    <?= json_encode(
        $valores_categorias
    ) ?>;


const percentuaisCategorias =
    <?= json_encode(
        $percentuais_categorias
    ) ?>;


const labelsCarteiras =
    <?= json_encode(
        $labels_carteiras,
        JSON_UNESCAPED_UNICODE
    ) ?>;


const valoresCarteiras =
    <?= json_encode(
        $valores_carteiras
    ) ?>;


/*
|--------------------------------------------------------------------------
| Formatação de moeda
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| GRÁFICO DE EVOLUÇÃO
|--------------------------------------------------------------------------
*/

const elementoPatrimonio =
    document.getElementById(
        "graficoPatrimonio"
    );


if (
    elementoPatrimonio &&
    labelsHistorico.length > 0
) {

    new Chart(
        elementoPatrimonio,
        {

            type: "line",


            data: {

                labels:
                    labelsHistorico,


                datasets: [

                    {

                        label:
                            "Patrimônio",


                        data:
                            valoresHistorico,


                        tension:
                            0.3,


                        fill:
                            true,


                        borderWidth:
                            3,


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


/*
|--------------------------------------------------------------------------
| GRÁFICO DE DISTRIBUIÇÃO
|--------------------------------------------------------------------------
*/

const elementoDistribuicao =
    document.getElementById(
        "graficoDistribuicao"
    );


if (
    elementoDistribuicao &&
    labelsCategorias.length > 0
) {

    new Chart(
        elementoDistribuicao,
        {

            type: "doughnut",


            data: {

                labels:
                    labelsCategorias,


                datasets: [

                    {

                        data:
                            valoresCategorias,


                        borderWidth:
                            2

                    }

                ]

            },


            plugins: [
                ChartDataLabels
            ],


            options: {

                responsive:
                    true,


                maintainAspectRatio:
                    false,


                plugins: {

                    legend: {

                        display:
                            false

                    },


                    datalabels: {

                        formatter:
                            function (
                                valor,
                                contexto
                            ) {

                                return (
                                    percentuaisCategorias[
                                        contexto.dataIndex
                                    ] + "%"
                                );

                            },


                        font: {

                            weight:
                                "bold",

                            size:
                                13

                        }

                    },


                    tooltip: {

                        callbacks: {

                            label:
                                function (
                                    contexto
                                ) {

                                    const indice =
                                        contexto.dataIndex;


                                    return (
                                        labelsCategorias[
                                            indice
                                        ] +
                                        ": " +
                                        percentuaisCategorias[
                                            indice
                                        ] +
                                        "% — " +
                                        formatarMoeda(
                                            contexto.raw
                                        )
                                    );

                                }

                        }

                    }

                }

            }

        }
    );

}


/*
|--------------------------------------------------------------------------
| GRÁFICO DE CARTEIRAS
|--------------------------------------------------------------------------
*/

const elementoCarteiras =
    document.getElementById(
        "graficoCarteiras"
    );


if (
    elementoCarteiras &&
    labelsCarteiras.length > 0
) {

    new Chart(
        elementoCarteiras,
        {

            type: "bar",


            data: {

                labels:
                    labelsCarteiras,


                datasets: [

                    {

                        label:
                            "Patrimônio",


                        data:
                            valoresCarteiras,


                        borderWidth:
                            1

                    }

                ]

            },


            options: {

                responsive:
                    true,


                maintainAspectRatio:
                    false,


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

                                    return formatarMoeda(
                                        contexto.raw
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