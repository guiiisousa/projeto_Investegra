<?php

session_start();

if (isset($_SESSION["id"])) {
    header("Location: dashboard.php");
    exit;
}

$mensagem = "";

if (isset($_GET["erro"])) {
    $mensagem = "E-mail ou senha incorretos.";
}

if (isset($_GET["cadastro"])) {
    $mensagem = "Cadastro realizado com sucesso. Faça login.";
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

    <title>Login - INVESTEGRA</title>

    <link
        rel="stylesheet"
        href="css/login.css"
    >

</head>

<body>

<div class="container">

    <div class="card">

        <div class="logo">
            INVESTEGRA
        </div>

        <h1>Bem-vindo</h1>

        <p class="subtitulo">
            Acesse sua conta para acompanhar seus investimentos.
        </p>


        <?php if ($mensagem !== ""): ?>

            <div class="mensagem">
                <?= htmlspecialchars($mensagem) ?>
            </div>

        <?php endif; ?>


        <form
            action="processando_login.php"
            method="POST"
        >

            <label for="email">
                E-mail
            </label>

            <input
                type="email"
                id="email"
                name="email"
                required
            >


            <label for="senha">
                Senha
            </label>

            <input
                type="password"
                id="senha"
                name="senha"
                required
            >


            <button type="submit">
                Entrar
            </button>

        </form>


        <p class="rodape">

            Ainda não possui uma conta?

            <a href="cadastro.php">
                Criar conta
            </a>

        </p>

    </div>

</div>

</body>

</html>