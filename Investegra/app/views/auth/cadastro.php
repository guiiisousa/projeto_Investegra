<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - INVESTEGRA</title>
    <link rel="stylesheet" href="/css/cadastro.css">
</head>
<body>

    <div class="container">

        <div class="logo">
            <h1>INVESTEGRA</h1>
            <p>Plataforma de Gestão de Investimentos</p>
        </div>

        <h2>Criar conta</h2>

        <?php if ($mensagem !== ""): ?>
            <div class="mensagem erro">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <form action="processando_cadastro.php" method="POST">

            <div class="campo">
                <input type="text" name="nome" placeholder="Nome completo" required>
            </div>

            <div class="campo">
                <input type="email" name="email" placeholder="Digite seu e-mail" required>
            </div>

            <div class="campo">
                <input type="text" name="documento" placeholder="CPF ou CNPJ" required>
            </div>

            <div class="campo">
                <select name="tipo_usuario" required>
                    <option value="PF">Pessoa Física</option>
                    <option value="PJ">Pessoa Jurídica</option>
                </select>
            </div>

            <div class="campo senha-container">
                <input type="password" id="senha" name="senha" placeholder="Crie uma senha" required>
                <button type="button" id="btnSenha" class="mostrar-senha"
                        onclick="alternarSenha('senha', 'btnSenha')">🐵</button>
            </div>

            <div class="campo senha-container">
                <input type="password" id="confirmar_senha" name="confirmar_senha"
                       placeholder="Confirme a senha" required>
                <button type="button" id="btnConfirmar" class="mostrar-senha"
                        onclick="alternarSenha('confirmar_senha', 'btnConfirmar')">🐵</button>
            </div>

            <button type="submit">Criar conta</button>

        </form>

        <div class="footer-link">
            Já possui conta?
            <a href="login.php">Entrar</a>
        </div>

    </div>

    <script src="/js/cadastro.js"></script>

</body>
</html>
