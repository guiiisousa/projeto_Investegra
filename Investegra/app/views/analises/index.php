<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Análises - INVESTEGRA
    </title>

    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >

</head>

<body>

<div class="layout">


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

            <a href="simulacao.php">
                Simulações
            </a>

            <a
                href="analises.php"
                class="ativo"
            >
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


    <main class="conteudo">


        <header>

            <h1>
                Análises da carteira
            </h1>

            <p>
                Visualize a distribuição dos seus investimentos.
            </p>

        </header>


        <!-- RESUMO -->

        <section class="cards">


            <div class="card">

                <span>
                    Patrimônio total
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
                    Ativos
                </span>

                <strong>
                    <?= $total_ativos ?>
                </strong>

            </div>


            <div class="card">

                <span>
                    Categorias
                </span>

                <strong>
                    <?= count($categorias) ?>
                </strong>

            </div>


            <div class="card">

                <span>
                    Carteira principal
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $carteira_principal[
                            "nome"
                        ]
                        ?? "Nenhuma"
                    ) ?>

                </strong>

            </div>


        </section>


        <!-- GRÁFICO -->

        <section class="painel painel-grafico">


            <h2>
                Distribuição por categoria
            </h2>


            <br>


            <?php if (
                count($categorias) === 0
            ): ?>

                <p class="sem-registros">

                    Não existem ativos suficientes
                    para gerar a análise.

                </p>


            <?php else: ?>


                <div class="grafico-categorias-container">
                    <canvas id="graficoCategorias"></canvas>
                </div>


            <?php endif; ?>


        </section>


        <br>


        <!-- TABELA -->

        <section class="painel">


            <h2>
                Distribuição detalhada
            </h2>


            <br>


            <?php if (
                count($categorias) > 0
            ): ?>


                <div class="historico-lista">


                    <?php foreach (
                        $categorias
                        as $categoria
                    ): ?>


                        <?php

                        $percentual = 0;

                        if ($patrimonio > 0) {

                            $percentual =
                                (
                                    $categoria[
                                        "valor"
                                    ]
                                    /
                                    $patrimonio
                                ) * 100;

                        }

                        ?>


                        <div
                            class="historico-item"
                        >

                            <span>

                                <?= htmlspecialchars(
                                    $categoria[
                                        "categoria"
                                    ]
                                ) ?>

                            </span>


                            <strong>

                                R$

                                <?= number_format(
                                    $categoria[
                                        "valor"
                                    ],
                                    2,
                                    ",",
                                    "."
                                ) ?>

                                <small>

                                    (
                                    <?= number_format(
                                        $percentual,
                                        1,
                                        ",",
                                        "."
                                    ) ?>%
                                    )

                                </small>

                            </strong>

                        </div>


                    <?php endforeach; ?>


                </div>


            <?php endif; ?>


        </section>


    </main>

</div>

<style>
    .painel-grafico .grafico-categorias-container {
        position: relative;
        width: 100%;
        max-width: 420px;
        height: 280px;
        margin: 0 auto;
    }
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

const labels =
    <?= json_encode($labels) ?>;


const valores =
    <?= json_encode($valores) ?>;


if (labels.length > 0) {

    new Chart(

        document.getElementById(
            "graficoCategorias"
        ),

        {

            type: "doughnut",

            data: {

                labels: labels,

                datasets: [

                    {

                        data: valores

                    }

                ]

            },

            options: {
                responsive: true,
                maintainAspectRatio: false
            }

        }

    );

}

</script>

</body>

</html>