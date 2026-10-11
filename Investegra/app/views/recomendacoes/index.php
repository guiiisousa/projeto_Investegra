<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Recomendações - INVESTEGRA</title>

    <link rel="stylesheet"
          href="css/recomendacoes.css">
    <link rel="stylesheet"
          href="css/nav-reveal.css">

</head>

<body>

<div class="layout">

<aside class="sidebar">

    <a href="dashboard.php" class="logo-link" aria-label="Ir para o Dashboard">
        <div class="logo">INVESTEGRA</div>
    </a>

    <nav>
        <a href="resultado_quiz.php" class="perfil-nav" aria-label="Perfil">Perfil</a>
        <a href="carteiras.php">Carteiras</a>
        <a href="simulacao.php">Simulações</a>
        <a href="analises.php">Análises</a>
        <a href="recomendacoes.php" class="ativo">Recomendações</a>
        <a href="historico.php">Histórico</a>
        <a href="indicadores.php">Indicadores</a>
        <a href="alertas.php">Alertas</a>
    </nav>

</aside>

<div class="container">

    <header class="topo">

        <div>

            <h1>Recomendações</h1>

            <p>
                Sugestões baseadas no seu perfil e na composição da carteira
            </p>

        </div>

        <a href="dashboard.php"
           class="voltar">

            Voltar ao dashboard

        </a>

    </header>


    <div class="aviso">

        <strong>Importante:</strong>

        As recomendações apresentadas pelo INVESTEGRA
        possuem caráter informativo e educacional.
        Elas não representam garantia de rentabilidade
        nem substituem uma análise profissional.

    </div>


    <main class="lista-recomendacoes">

        <?php if (count($recomendacoes) == 0): ?>

            <div class="sem-recomendacoes">

                <h2>Nenhuma recomendação disponível</h2>

                <p>
                    Complete seu perfil e cadastre seus ativos
                    para que o sistema possa realizar análises.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($recomendacoes as $recomendacao): ?>

                <article class="recomendacao">

                    <div class="recomendacao-topo">

                        <h2>
                            <?= htmlspecialchars(
                                $recomendacao["titulo"]
                            ) ?>
                        </h2>

                        <span class="categoria">

                            <?= htmlspecialchars(
                                $recomendacao["categoria"]
                            ) ?>

                        </span>

                    </div>


                    <p class="descricao">

                        <?= htmlspecialchars(
                            $recomendacao["descricao"]
                        ) ?>

                    </p>


                    <small>

                        Gerada em

                        <?= date(
                            "d/m/Y H:i",
                            strtotime(
                                $recomendacao["criado_em"]
                            )
                        ) ?>

                    </small>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </main>

</div>

</div>

</body>

</html>
