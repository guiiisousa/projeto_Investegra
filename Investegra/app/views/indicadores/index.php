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

            <a href="historico.php">
                Histórico
            </a>

            <a
                href="indicadores.php"
                class="ativo"
            >
                Indicadores
            </a>

            <a 
                href="alertas.php"
                class="alertas"
            >
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