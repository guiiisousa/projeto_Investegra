<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];

$id_carteira = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


if (!$id_carteira) {

    header("Location: carteiras.php?erro=1");
    exit;

}


/*
 * Buscar a carteira.
 *
 * IMPORTANTE:
 * O usuario_id também é verificado.
 *
 * Assim o usuário não consegue editar
 * a carteira de outra pessoa.
 */

$sql = "
    SELECT
        id,
        nome,
        descricao
    FROM carteiras
    WHERE id = ?
    AND usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_carteira,
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows !== 1) {

    $stmt->close();

    header("Location: carteiras.php?erro=1");
    exit;

}


$carteira = $resultado->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar Carteira - INVESTEGRA</title>

    <link
        rel="stylesheet"
        href="css/carteiras.css"
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

            <a
                href="carteiras.php"
                class="ativo"
            >
                Carteiras
            </a>

            <a href="#">
                Simulações
            </a>

            <a href="#">
                Análises
            </a>

            <a href="#">
                Recomendações
            </a>

            <a href="#">
                Histórico
            </a>

            <a href="#">
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


        <div class="formulario-container">


            <h1>
                Editar carteira
            </h1>

            <p class="subtitulo">
                Altere as informações da sua carteira.
            </p>


            <form
                action="atualizar_carteira.php"
                method="POST"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?= $carteira["id"] ?>"
                >


                <label for="nome">
                    Nome da carteira
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars(
                        $carteira["nome"]
                    ) ?>"
                    maxlength="100"
                    required
                >


                <label for="descricao">
                    Descrição
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    rows="5"
                ><?= htmlspecialchars(
                    $carteira["descricao"] ?? ""
                ) ?></textarea>


                <div class="botoes-formulario">

                    <a
                        href="carteiras.php"
                        class="botao-secundario"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="botao"
                    >
                        Salvar alterações
                    </button>

                </div>


            </form>


        </div>


    </main>

</div>

</body>

</html>