<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";

$id_usuario = $_SESSION["id"];


/*
|--------------------------------------------------------------------------
| Gerar alertas
|--------------------------------------------------------------------------
*/

require_once "gerar_alertas.php";

gerarAlertas($conexao, $id_usuario);


/*
|--------------------------------------------------------------------------
| Buscar alertas
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        titulo,
        mensagem,
        nivel,
        lido,
        criado_em
    FROM alertas
    WHERE usuario_id = ?
    ORDER BY criado_em DESC
";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Alertas - INVESTEGRA</title>

    <link rel="stylesheet"
          href="css/alertas.css">

</head>

<body>

<div class="container">

    <header class="topo">

        <div>
            <h1>Alertas</h1>

            <p>
                Informações importantes sobre sua carteira
            </p>
        </div>

        <a href="dashboard.php"
           class="voltar">
            Voltar ao dashboard
        </a>

    </header>


    <main class="lista-alertas">

        <?php if ($resultado->num_rows == 0): ?>

            <div class="sem-alertas">

                <h2>Nenhum alerta</h2>

                <p>
                    No momento não foram identificadas
                    situações que necessitem de atenção.
                </p>

            </div>

        <?php else: ?>

            <?php while ($alerta = $resultado->fetch_assoc()): ?>

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

            <?php endwhile; ?>

        <?php endif; ?>

    </main>

</div>

</body>

</html>

<?php

$stmt->close();

?>