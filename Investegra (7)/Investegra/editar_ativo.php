<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];


$id_ativo = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


if (!$id_ativo) {

    header("Location: carteiras.php?erro=1");
    exit;

}


/*
 * Buscar ativo e verificar proprietário.
 */

$sql = "
    SELECT
        a.id,
        a.carteira_id,
        a.categoria_id,
        a.ticker,
        a.quantidade,
        a.preco_medio,
        a.data_compra,
        a.observacao,
        c.nome AS carteira_nome
    FROM ativos a
    INNER JOIN carteiras c
        ON a.carteira_id = c.id
    WHERE a.id = ?
    AND c.usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_ativo,
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows !== 1) {

    $stmt->close();

    header("Location: carteiras.php?erro=1");
    exit;

}


$ativo = $resultado->fetch_assoc();

$stmt->close();


/*
 * Buscar categorias.
 */

$sql = "
    SELECT
        id,
        nome
    FROM categorias
    ORDER BY nome
";

$resultado = $conexao->query($sql);

$categorias = $resultado->fetch_all(MYSQLI_ASSOC);

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
        Editar Ativo - INVESTEGRA
    </title>

    <link
        rel="stylesheet"
        href="css/ativos.css"
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


            <a
                href="ativos.php?carteira=<?= $ativo["carteira_id"] ?>"
                class="voltar"
            >
                ← Voltar para ativos
            </a>


            <h1>
                Editar ativo
            </h1>


            <p class="subtitulo">

                Carteira:
                <strong>
                    <?= htmlspecialchars(
                        $ativo["carteira_nome"]
                    ) ?>
                </strong>

            </p>


            <form
                action="atualizar_ativo.php"
                method="POST"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?= $ativo["id"] ?>"
                >


                <label for="ticker">
                    Ticker
                </label>

                <input
                    type="text"
                    id="ticker"
                    name="ticker"
                    value="<?= htmlspecialchars(
                        $ativo["ticker"]
                    ) ?>"
                    maxlength="20"
                    required
                >


                <label for="categoria_id">
                    Categoria
                </label>

                <select
                    id="categoria_id"
                    name="categoria_id"
                    required
                >

                    <?php foreach (
                        $categorias as $categoria
                    ): ?>

                        <option
                            value="<?= $categoria["id"] ?>"
                            <?= (
                                $categoria["id"]
                                == $ativo["categoria_id"]
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            <?= htmlspecialchars(
                                $categoria["nome"]
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <label for="quantidade">
                    Quantidade
                </label>

                <input
                    type="number"
                    id="quantidade"
                    name="quantidade"
                    step="0.0001"
                    min="0.0001"
                    value="<?= htmlspecialchars(
                        $ativo["quantidade"]
                    ) ?>"
                    required
                >


                <label for="preco_medio">
                    Preço médio
                </label>

                <input
                    type="number"
                    id="preco_medio"
                    name="preco_medio"
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars(
                        $ativo["preco_medio"]
                    ) ?>"
                    required
                >


                <label for="data_compra">
                    Data de compra
                </label>

                <input
                    type="date"
                    id="data_compra"
                    name="data_compra"
                    value="<?= htmlspecialchars(
                        $ativo["data_compra"] ?? ""
                    ) ?>"
                >


                <label for="observacao">
                    Observação
                </label>

                <textarea
                    id="observacao"
                    name="observacao"
                    rows="5"
                ><?= htmlspecialchars(
                    $ativo["observacao"] ?? ""
                ) ?></textarea>


                <div class="botoes-formulario">


                    <a
                        href="ativos.php?carteira=<?= $ativo["carteira_id"] ?>"
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