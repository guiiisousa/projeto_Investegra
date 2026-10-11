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
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >
    <link
        rel="stylesheet"
        href="css/editar-carteira.css?v=1"
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