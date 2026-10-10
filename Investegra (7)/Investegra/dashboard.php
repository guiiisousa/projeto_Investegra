<?php

session_start();

require_once "conexao.php";


/*
|--------------------------------------------------------------------------
| VERIFICAÇÃO DE LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}

$id_usuario = $_SESSION["id"];


/*
|--------------------------------------------------------------------------
| DADOS DO USUÁRIO
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        nome,
        perfil_risco,
        score_risco
    FROM usuarios
    WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$usuario = $resultado->fetch_assoc();

$stmt->close();


if (!$usuario) {

    session_destroy();

    header("Location: login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| TOTAL DE CARTEIRAS
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT COUNT(*) AS total
    FROM carteiras
    WHERE usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$total_carteiras =
    (int) $resultado->fetch_assoc()["total"];

$stmt->close();


/*
|--------------------------------------------------------------------------
| TOTAL DE ATIVOS
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT COUNT(*) AS total
    FROM ativos a
    INNER JOIN carteiras c
        ON a.carteira_id = c.id
    WHERE c.usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$total_ativos =
    (int) $resultado->fetch_assoc()["total"];

$stmt->close();


/*
|--------------------------------------------------------------------------
| PATRIMÔNIO TOTAL
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        COALESCE(
            SUM(
                a.quantidade * a.preco_medio
            ),
            0
        ) AS patrimonio
    FROM ativos a
    INNER JOIN carteiras c
        ON a.carteira_id = c.id
    WHERE c.usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$patrimonio =
    (float) $resultado->fetch_assoc()["patrimonio"];

$stmt->close();


/*
|--------------------------------------------------------------------------
| HISTÓRICO DO PATRIMÔNIO
|--------------------------------------------------------------------------
|
| Pegamos até 12 registros para o gráfico.
|
*/

$sql = "
    SELECT
        valor_total,
        data_registro
    FROM historico_patrimonio
    WHERE usuario_id = ?
    ORDER BY data_registro DESC
    LIMIT 12
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$historico =
    $resultado->fetch_all(
        MYSQLI_ASSOC
    );

$stmt->close();


/*
|--------------------------------------------------------------------------
| Inverter para deixar o gráfico em ordem cronológica.
|--------------------------------------------------------------------------
*/

$historico_grafico =
    array_reverse($historico);


/*
|--------------------------------------------------------------------------
| DISTRIBUIÇÃO POR CATEGORIA
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        COALESCE(
            c.nome,
            'Sem categoria'
        ) AS categoria,

        COALESCE(
            SUM(
                a.quantidade *
                a.preco_medio
            ),
            0
        ) AS valor

    FROM carteiras ca

    INNER JOIN ativos a
        ON a.carteira_id = ca.id

    LEFT JOIN categorias c
        ON a.categoria_id = c.id

    WHERE ca.usuario_id = ?

    GROUP BY
        c.id,
        c.nome

    ORDER BY valor DESC
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$distribuicao =
    $resultado->fetch_all(
        MYSQLI_ASSOC
    );

$stmt->close();


/*
|--------------------------------------------------------------------------
| CALCULAR PERCENTUAIS
|--------------------------------------------------------------------------
*/

foreach ($distribuicao as &$categoria) {

    $valor =
        (float) $categoria["valor"];

    if ($patrimonio > 0) {

        $categoria["percentual"] =
            (
                $valor /
                $patrimonio
            ) * 100;

    } else {

        $categoria["percentual"] = 0;

    }

}

unset($categoria);


/*
|--------------------------------------------------------------------------
| PATRIMÔNIO POR CARTEIRA
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        ca.nome,

        COALESCE(
            SUM(
                a.quantidade *
                a.preco_medio
            ),
            0
        ) AS patrimonio

    FROM carteiras ca

    LEFT JOIN ativos a
        ON a.carteira_id = ca.id

    WHERE ca.usuario_id = ?

    GROUP BY
        ca.id,
        ca.nome

    ORDER BY patrimonio DESC
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$patrimonio_carteiras =
    $resultado->fetch_all(
        MYSQLI_ASSOC
    );

$stmt->close();


/*
|--------------------------------------------------------------------------
| MAIOR CATEGORIA
|--------------------------------------------------------------------------
*/

$maior_categoria = null;
$maior_percentual = 0;

if (count($distribuicao) > 0) {

    $maior_categoria =
        $distribuicao[0]["categoria"];

    $maior_percentual =
        (float) $distribuicao[0]["percentual"];

}


/*
|--------------------------------------------------------------------------
| CLASSIFICAÇÃO DA CONCENTRAÇÃO
|--------------------------------------------------------------------------
*/

if ($maior_percentual >= 70) {

    $situacao_diversificacao =
        "Alta concentração";

    $classe_diversificacao =
        "concentracao-alta";

} elseif ($maior_percentual >= 50) {

    $situacao_diversificacao =
        "Concentração moderada";

    $classe_diversificacao =
        "concentracao-media";

} elseif ($maior_percentual > 0) {

    $situacao_diversificacao =
        "Distribuição diversificada";

    $classe_diversificacao =
        "concentracao-baixa";

} else {

    $situacao_diversificacao =
        "Sem dados";

    $classe_diversificacao =
        "";

}


/*
|--------------------------------------------------------------------------
| DADOS PARA O GRÁFICO DE HISTÓRICO
|--------------------------------------------------------------------------
*/

$labels_historico = [];
$valores_historico = [];

foreach (
    $historico_grafico
    as $registro
) {

    $labels_historico[] =
        date(
            "d/m",
            strtotime(
                $registro["data_registro"]
            )
        );

    $valores_historico[] =
        (float) $registro["valor_total"];

}


/*
|--------------------------------------------------------------------------
| DADOS PARA CATEGORIAS
|--------------------------------------------------------------------------
*/

$labels_categorias = [];
$valores_categorias = [];
$percentuais_categorias = [];

foreach (
    $distribuicao
    as $categoria
) {

    $labels_categorias[] =
        $categoria["categoria"];

    $valores_categorias[] =
        (float) $categoria["valor"];

    $percentuais_categorias[] =
        round(
            (float) $categoria["percentual"],
            1
        );

}


/*
|--------------------------------------------------------------------------
| DADOS PARA CARTEIRAS
|--------------------------------------------------------------------------
*/

$labels_carteiras = [];
$valores_carteiras = [];

foreach (
    $patrimonio_carteiras
    as $carteira
) {

    $labels_carteiras[] =
        $carteira["nome"];

    $valores_carteiras[] =
        (float) $carteira["patrimonio"];

}


/*
|--------------------------------------------------------------------------
| ÚLTIMOS REGISTROS
|--------------------------------------------------------------------------
*/

$ultimos_registros =
    array_slice(
        $historico,
        0,
        5
    );


/*
|--------------------------------------------------------------------------
| VARIAÇÃO DO ÚLTIMO REGISTRO
|--------------------------------------------------------------------------
*/

$variacao_ultimo = null;

if (count($historico_grafico) >= 2) {

    $ultimo =
        (float) $historico_grafico[
            count($historico_grafico) - 1
        ]["valor_total"];

    $anterior =
        (float) $historico_grafico[
            count($historico_grafico) - 2
        ]["valor_total"];


    if ($anterior != 0) {

        $variacao_ultimo =
            (
                ($ultimo - $anterior)
                / $anterior
            ) * 100;

    }

}


/*
|--------------------------------------------------------------------------
| VARIÁVEIS DO USUÁRIO
|--------------------------------------------------------------------------
*/

$nome_usuario =
    $usuario["nome"];

$perfil_risco =
    $usuario["perfil_risco"];


/*
|--------------------------------------------------------------------------
| CLASSE DO PERFIL
|--------------------------------------------------------------------------
*/

$classe_perfil = "";

if ($perfil_risco === "Conservador") {

    $classe_perfil =
        "perfil-conservador";

} elseif ($perfil_risco === "Moderado") {

    $classe_perfil =
        "perfil-moderado";

} elseif ($perfil_risco === "Arrojado") {

    $classe_perfil =
        "perfil-arrojado";

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
        Dashboard - INVESTEGRA
    </title>


    <link
        rel="stylesheet"
        href="css/dashboard.css"
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

        <div class="logo">
            INVESTEGRA
        </div>


        <nav>

            <a
                href="dashboard.php"
                class="ativo"
            >
                Dashboard
            </a>


            <a href="perfil.php">
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