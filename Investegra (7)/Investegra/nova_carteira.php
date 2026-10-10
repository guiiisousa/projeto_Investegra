<?php

session_start();

if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

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

    <title>Nova Carteira - INVESTEGRA</title>

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
                Nova carteira
            </h1>

            <p class="subtitulo">
                Crie uma carteira para organizar seus investimentos.
            </p>


            <form
                action="processando_carteira.php"
                method="POST"
            >


                <label for="nome">
                    Nome da carteira
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    placeholder="Ex.: Minha carteira"
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
                    placeholder="Descreva o objetivo desta carteira..."
                ></textarea>


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
                        Criar carteira
                    </button>

                </div>


            </form>


        </div>


    </main>

</div>

</body>

</html>