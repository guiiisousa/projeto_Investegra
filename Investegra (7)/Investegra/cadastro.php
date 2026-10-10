<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cadastro - INVESTEGRA</title>

    <link
        rel="stylesheet"
        href="css/cadastro.css"
    >

</head>

<body>

<div class="container">

    <div class="card">

        <div class="logo">
            INVESTEGRA
        </div>

        <h1>Criar conta</h1>

        <p class="subtitulo">
            Crie sua conta para começar a organizar seus investimentos.
        </p>

        <form
            action="processando_cadastro.php"
            method="POST"
        >

            <label for="nome">
                Nome
            </label>

            <input
                type="text"
                id="nome"
                name="nome"
                required
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


            <label for="documento">
                CPF/CNPJ
            </label>

            <input
                type="text"
                id="documento"
                name="documento"
                required
            >


            <label for="tipo_usuario">
                Tipo de usuário
            </label>

            <select
                id="tipo_usuario"
                name="tipo_usuario"
                required
            >

                <option value="PF">
                    Pessoa Física
                </option>

                <option value="PJ">
                    Pessoa Jurídica
                </option>

            </select>


            <label for="senha">
                Senha
            </label>

            <input
                type="password"
                id="senha"
                name="senha"
                required
            >


            <label for="confirmar_senha">
                Confirmar senha
            </label>

            <input
                type="password"
                id="confirmar_senha"
                name="confirmar_senha"
                required
            >


            <button type="submit">
                Criar conta
            </button>

        </form>


        <p class="rodape">

            Já possui uma conta?

            <a href="login.php">
                Entrar
            </a>

        </p>

    </div>

</div>

</body>

</html>