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