<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - INVESTEGRA</title>
    <link rel="stylesheet" href="/css/cadastro.css">
</head>
<body>
    <main class="container">
        <header class="logo">
            <h1>INVESTEGRA</h1>
            <p>Plataforma de Gestão de Investimentos</p>
        </header>

        <h2>Criar conta</h2>

        <?php if ($mensagem !== ""): ?>
            <div class="mensagem erro" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form action="processando_cadastro.php" method="POST">
            <div class="campo">
                <label class="visually-hidden" for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" placeholder="Nome completo" autocomplete="name" maxlength="100" required>
            </div>

            <div class="campo">
                <label class="visually-hidden" for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" autocomplete="email" maxlength="150" required>
            </div>

            <div class="campo">
                <label class="visually-hidden" for="documento">CPF ou CNPJ</label>
                <input type="text" id="documento" name="documento" placeholder="CPF ou CNPJ" autocomplete="off" maxlength="20" required>
            </div>

            <div class="campo">
                <label class="visually-hidden" for="tipo_usuario">Tipo de usuário</label>
                <select id="tipo_usuario" name="tipo_usuario" required>
                    <option value="PF">Pessoa Física</option>
                    <option value="PJ">Pessoa Jurídica</option>
                </select>
            </div>

            <div class="campo senha-container">
                <label class="visually-hidden" for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Crie uma senha" autocomplete="new-password" minlength="6" required>
                <button type="button" id="btnSenha" class="mostrar-senha" onclick="alternarSenha('senha', 'btnSenha')" aria-label="Mostrar senha">🐵</button>
            </div>

            <div class="campo senha-container">
                <label class="visually-hidden" for="confirmar_senha">Confirme a senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a senha" autocomplete="new-password" minlength="6" required>
                <button type="button" id="btnConfirmar" class="mostrar-senha" onclick="alternarSenha('confirmar_senha', 'btnConfirmar')" aria-label="Mostrar confirmação da senha">🐵</button>
            </div>

            <button type="submit">Criar conta</button>
        </form>

        <p class="footer-link">
            Já possui conta?
            <a href="login.php">Entrar</a>
        </p>
    </main>

    <script src="/js/cadastro.js"></script>
</body>
</html>
