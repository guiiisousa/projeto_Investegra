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
| Gerar recomendações
|--------------------------------------------------------------------------
*/

require_once "gerar_recomendacoes.php";

gerarRecomendacoes($conexao, $id_usuario);


/*
|--------------------------------------------------------------------------
| Buscar recomendações
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        titulo,
        descricao,
        categoria,
        percentual_sugerido,
        criado_em
    FROM recomendacoes
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

    <title>Recomendações - INVESTEGRA</title>

    <link rel="stylesheet"
          href="css/recomendacoes.css">

</head>

<body>

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

        <?php if ($resultado->num_rows == 0): ?>

            <div class="sem-recomendacoes">

                <h2>Nenhuma recomendação disponível</h2>

                <p>
                    Complete seu perfil e cadastre seus ativos
                    para que o sistema possa realizar análises.
                </p>

            </div>

        <?php else: ?>

            <?php while ($recomendacao = $resultado->fetch_assoc()): ?>

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

            <?php endwhile; ?>

        <?php endif; ?>

    </main>

</div>

</body>

</html>

<?php

$stmt->close();

?>