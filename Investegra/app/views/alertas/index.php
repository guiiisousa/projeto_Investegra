<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Alertas - INVESTEGRA</title>

    <link rel="stylesheet"
          href="css/alertas.css">
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
        <?php require BASE_PATH . "/app/views/partials/perfil_nav.php"; ?>
        <a href="carteiras.php">Carteiras</a>
        <a href="simulacao.php">Simulações</a>
        <a href="analises.php">Análises</a>
        <a href="recomendacoes.php">Recomendações</a>
        <a href="historico.php">Histórico</a>
        <a href="indicadores.php">Indicadores</a>
        <a href="alertas.php" class="ativo">Alertas</a>
    </nav>

</aside>

<div class="container">

    <header class="topo">

        <div>
            <h1>Alertas</h1>

            <p>
                Informações importantes sobre sua carteira
            </p>
        </div>

    </header>


    <main class="lista-alertas">

        <?php if (count($alertas) == 0): ?>

            <div class="sem-alertas">

                <h2>Nenhum alerta</h2>

                <p>
                    No momento não foram identificadas
                    situações que necessitem de atenção.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($alertas as $alerta): ?>

                <div class="alerta <?= strtolower($alerta["nivel"]) ?>">

                    <div class="alerta-topo">

                        <h2>
                            <?= htmlspecialchars($alerta["titulo"]) ?>
                        </h2>

                        <span class="nivel">

                            <?= htmlspecialchars($alerta["nivel"]) ?>

                        </span>

                    </div>


                    <p>
                        <?= htmlspecialchars($alerta["mensagem"]) ?>
                    </p>


                    <small>

                        <?= date(
                            "d/m/Y H:i",
                            strtotime($alerta["criado_em"])
                        ) ?>

                    </small>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </main>

</div>

</div>

</body>

</html>
