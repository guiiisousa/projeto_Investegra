<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastro - INVESTEGRA</title>
    
</head>

<body>

<div class="container">

    <div class="logo">
        <h1>INVESTEGRA</h1>
        <p>Controle e acompanhe seus investimentos</p>
    </div>

    <h2>Criar Conta</h2>

    <form action="processando_cadastro.php" method="POST">

        <div class="campo">
            <input
                type="text"
                name="nome"
                placeholder="Nome completo"
                required>
        </div>

        <div class="campo">
            <input
                type="email"
                name="email"
                placeholder="E-mail"
                required>
        </div>

        <div class="campo">
            <input
                type="text"
                name="documento"
                placeholder="CPF ou CNPJ"
                required>
        </div>

        <div class="campo">
            <select name="tipo_usuario" required>

                <option value="">
                    Tipo de usuário
                </option>

                <option value="PF">
                    Pessoa Física
                </option>

                <option value="PJ">
                    Pessoa Jurídica
                </option>

            </select>
        </div>

        <div class="campo senha-container">

            <input
                type="password"
                id="senha"
                name="senha"
                placeholder="Senha"
                required>

            <button
                type="button"
                id="btnSenha"
                class="mostrar-senha"
                onclick="alternarSenha('senha','btnSenha')">

                🐵
            </button>

        </div>

        <div class="campo senha-container">

            <input
                type="password"
                id="confirmarSenha"
                name="confirmar_senha"
                placeholder="Confirmar senha"
                required>

            <button
                type="button"
                id="btnConfirmar"
                class="mostrar-senha"
                onclick="alternarSenha('confirmarSenha','btnConfirmar')">

                
            </button>

        </div>

        <button type="submit">
            Criar Conta
        </button>

    </form>

    <div class="footer-link">
        Já possui conta?
        <a href="login.php">Entrar</a>
    </div>

</div>
</body>
</html>