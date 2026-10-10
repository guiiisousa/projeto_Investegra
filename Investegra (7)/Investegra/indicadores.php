<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];


/*
 * Patrimônio.
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
 * Carteiras.
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
    $resultado->fetch_assoc()["total"];

$stmt->close();


/*
 * Ativos.
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
 * Maior ativo.
 */

$sql = "
    SELECT
        a.ticker,
        (
            a.quantidade *
            a.preco_medio
        ) AS valor

    FROM ativos a

    INNER JOIN carteiras c
        ON a.carteira_id = c.id

    WHERE c.usuario_id = ?

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

$maior_ativo =
    $resultado->fetch_assoc();

$stmt->close();


/*
 * Maior categoria.
 */

$sql = "
    SELECT
        cat.nome AS categoria,

        SUM(
            a.quantidade *
            a.preco_medio
        ) AS valor

    FROM ativos a

    INNER JOIN carteiras c
        ON a.carteira_id = c.id

    INNER JOIN categorias cat
        ON a.categoria_id = cat.id

    WHERE c.usuario_id = ?

    GROUP BY cat.id, cat.nome

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

$maior_categoria =
    $resultado->fetch_assoc();

$stmt->close();


/*
 * Média por carteira.
 */

$media_carteira = 0;

if ($total_carteiras > 0) {

    $media_carteira =
        $patrimonio /
        $total_carteiras;

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
        Indicadores - INVESTEGRA
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

            <a href="analises.php">
                Análises
            </a>

            <a href="recomendacoes.php">
                Recomendações
            </a>

            <a href="historico.php">
                Histórico
            </a>

            <a
                href="indicadores.php"
                class="ativo"
            >
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
                Indicadores
            </h1>

            <p>
                Principais indicadores da sua carteira.
            </p>

        </header>


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
                    Ativos cadastrados
                </span>

                <strong>
                    <?= $total_ativos ?>
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
                    Média por carteira
                </span>

                <strong>

                    R$
                    <?= number_format(
                        $media_carteira,
                        2,
                        ",",
                        "."
                    ) ?>

                </strong>

            </div>


        </section>


        <section class="painel">


            <h2>
                Principais indicadores
            </h2>


            <div class="informacao">

                <span>
                    Ativo de maior valor
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $maior_ativo["ticker"]
                        ?? "Nenhum"
                    ) ?>

                </strong>

            </div>


            <div class="informacao">

                <span>
                    Maior categoria
                </span>

                <strong>

                    <?= htmlspecialchars(
                        $maior_categoria[
                            "categoria"
                        ]
                        ?? "Nenhuma"
                    ) ?>

                </strong>

            </div>


            <div class="informacao">

                <span>
                    Valor do maior ativo
                </span>

                <strong>

                    R$

                    <?= number_format(
                        $maior_ativo["valor"]
                        ?? 0,
                        2,
                        ",",
                        "."
                    ) ?>

                </strong>

            </div>


            <div class="informacao">

                <span>
                    Valor da maior categoria
                </span>

                <strong>

                    R$

                    <?= number_format(
                        $maior_categoria[
                            "valor"
                        ]
                        ?? 0,
                        2,
                        ",",
                        "."
                    ) ?>

                </strong>

            </div>


        </section>


    </main>

</div>

</body>

</html>