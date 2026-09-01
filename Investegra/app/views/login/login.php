<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Login - INVESTEGRA</title>
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>

    <div class="container">

        <div class="logo">
            <h1>INVESTEGRA</h1>
            <p>Plataforma de Gestão de Investimentos</p>
        </div>

        <h2>Entrar</h2>

        <form action="processando_login.php" method="POST">

            <div class="campo">
                <input
                    type="email"
                    name="email"
                    placeholder="Digite seu e-mail"
                    required>
            </div>

            <div class="campo senha-container">

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha"
                    required>

                <button
                    type="button"
                    id="btnSenha"
                    class="mostrar-senha"
                    onclick="alternarSenha()">

                    🐵
                </button>

            </div>

            <button type="submit">
                Entrar
            </button>

        </form>

        <div class="footer-link">
            Ainda não possui conta?
            <a href="cadastro.php">Cadastre-se</a>
        </div>

    </div>

</body>
</html>