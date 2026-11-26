<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistente IA | INVESTEGRA</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/nav-reveal.css">
    <link rel="stylesheet" href="css/assistente.css?v=2">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <a href="dashboard.php" class="logo-link" aria-label="Ir para o Dashboard">
            <div class="logo">INVESTEGRA</div>
        </a>
        <nav>
            <?php
            $chatNavAtivo = true;
            require BASE_PATH . "/app/views/partials/perfil_nav.php";
            ?>
            <a href="carteiras.php">Carteiras</a>
            <a href="simulacao.php">Simulações</a>
            <a href="analises.php">Análises</a>
            <a href="recomendacoes.php">Recomendações</a>
            <a href="historico.php">Histórico</a>
        </nav>
    </aside>

    <main class="conteudo">
        <header>
            <h1>Assistente de investimentos</h1>
            <p>Tire dúvidas sobre investimentos, carteira e estratégias de forma educacional.</p>
        </header>

        <section
            class="assistente-ia"
            data-contexto="geral"
            data-dados="<?= htmlspecialchars(json_encode($dadosIA, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>"
        >
            <div class="assistente-header">
                <div class="assistente-icon" aria-hidden="true">IA</div>
                <div>
                    <h3>Assistente INVESTEGRA</h3>
                    <p>Respostas educacionais com base nos seus dados</p>
                </div>
            </div>
            <div class="assistente-mensagens" id="assistenteMensagens">
                <div class="mensagem mensagem-assistente">
                    <div class="mensagem-avatar">IA</div>
                    <div class="mensagem-conteudo">
                        Olá! Posso ajudar com dúvidas sobre investimentos, perfil de risco e conceitos financeiros.
                    </div>
                </div>
            </div>
            <div class="assistente-input-area">
                <textarea
                    id="assistenteInput"
                    rows="1"
                    maxlength="1000"
                    placeholder="Digite sua dúvida..."
                    aria-label="Digite sua dúvida"
                ></textarea>
                <button type="button" id="assistenteEnviar" aria-label="Enviar pergunta">→</button>
            </div>
            <div class="assistente-aviso">
                As respostas são educacionais e não representam recomendações financeiras.
            </div>
        </section>
    </main>
</div>
<script src="js/assistente.js?v=2"></script>
</body>
</html>
