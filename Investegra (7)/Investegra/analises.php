<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];


/*
 * Patrimônio total.
 */

$sql = "
    SELECT
        COALESCE(
            SUM(
                a.quantidade *
                a.preco_medio
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
    $resultado->fetch_assoc()["patrimonio"];

$stmt->close();


/*
 * Quantidade total de ativos.
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
    $resultado->fetch_assoc()["total"];

$stmt->close();


/*
 * Distribuição por categoria.
 */

$sql = "
    SELECT
        COALESCE(
            cat.nome,
            'Sem categoria'
        ) AS categoria,

        SUM(
            a.quantidade *
            a.preco_medio
        ) AS valor

    FROM ativos a

    INNER JOIN carteiras c
        ON a.carteira_id = c.id

    LEFT JOIN categorias cat
        ON a.categoria_id = cat.id

    WHERE c.usuario_id = ?

    GROUP BY cat.id, cat.nome

    ORDER BY valor DESC
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$categorias =
    $resultado->fetch_all(
        MYSQLI_ASSOC
    );

$stmt->close();


/*
 * Carteira com maior patrimônio.
 */

$sql = "
    SELECT
        c.nome,
        COALESCE(
            SUM(
                a.quantidade *
                a.preco_medio
            ),
            0
        ) AS valor

    FROM carteiras c

    LEFT JOIN ativos a
        ON a.carteira_id = c.id

    WHERE c.usuario_id = ?

    GROUP BY c.id, c.nome

    ORDER BY valor DESC

    LIMIT 1
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$carteira_principal =
    $resultado->fetch_assoc();

$stmt->close();


/*
 * Preparar dados do gráfico.
 */

$labels = [];

$valores = [];

foreach (
    $categorias as $categoria
) {

    $labels[] =
        $categoria["categoria"];

    $valores[] =
        (float) $categoria["valor"];

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
        Análises - INVESTEGRA
    </title>

    <link
        rel="stylesheet"
        href="css/dashboard.css"
    >

</head>

<body>

<div class="layout">


    <aside class="sidebar">

        <div class="logo">
            INVESTEGRA
        </div>


        <nav>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="#">
                Perfil
            </a>

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

        <section class="painel">


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


                <canvas
                    id="graficoCategorias"
                ></canvas>


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

                responsive: true

            }

        }

    );

}

</script>

</body>

</html>