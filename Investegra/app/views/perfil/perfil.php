<?php
$escape = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
};

$experiencia = (string) ($experiencia ?? "");
$horizonte = (string) ($horizonte ?? "");
$tolerancia = (string) ($tolerancia ?? "");
$objetivo = (string) ($objetivo ?? "");
$analise_ia = (string) ($analise_ia ?? "");

$experienciaLabel = [
    "Nenhuma" => "Nenhuma",
    "Baixa" => "Baixa",
    "Media" => "Média",
    "Alta" => "Alta",
][$experiencia] ?? $experiencia;

$horizonteLabel = [
    "Curto" => "Curto prazo",
    "Medio" => "Médio prazo",
    "Longo" => "Longo prazo",
][$horizonte] ?? $horizonte;

$toleranciaLabel = [
    "Baixa" => "Baixa",
    "Media" => "Média",
    "Alta" => "Alta",
][$tolerancia] ?? $tolerancia;

$objetivoLabel = [
    "Preservacao" => "Preservação do patrimônio",
    "Reserva" => "Formação de reserva",
    "Crescimento" => "Crescimento do patrimônio",
    "Longo_Prazo" => "Investimento de longo prazo",
][$objetivo] ?? str_replace("_", " ", $objetivo);

$perfil = $perfil !== "" ? $perfil : "Não definido";
$valorOuPadrao = static function ($value) use ($escape): string {
    return $value !== "" && $value !== null ? $escape($value) : "Não informado";
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7f4">
    <title>Meu perfil | Investegra</title>
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="css/nav-reveal.css?v=2">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <a href="dashboard.php" class="logo-link" aria-label="Ir para o Dashboard">
                <div class="logo">INVESTEGRA</div>
            </a>
            <nav aria-label="Navegação principal">
                <?php $perfilNavAtivo = true; require BASE_PATH . "/app/views/partials/perfil_nav.php"; ?>
                <a href="carteiras.php">Carteiras</a>
                <a href="simulacao.php">Simulações</a>
                <a href="analise_carteira.php">Análises</a>
                <a href="recomendacoes.php">Recomendações</a>
                <a href="historico.php">Histórico</a>
                <a href="indicadores.php">Indicadores</a>
                <a href="alertas.php">Alertas</a>
            </nav>
            <a href="logout.php" class="logout">Sair</a>
        </aside>

        <main class="profile-page">
            <section class="profile-heading" aria-labelledby="page-title">
                <p class="eyebrow">SUA CONTA</p>
                <h1 id="page-title">Meu perfil</h1>
                <p class="heading-copy">Suas informações e seu perfil de investidor em um só lugar.</p>
                <?php if (!$quizConcluido): ?>
                    <div class="quiz-reminder" role="status">
                        <p>Complete o quiz para descobrir seu perfil de investidor.</p>
                        <a href="questionario_perfil.php">Fazer quiz</a>
                    </div>
                <?php endif; ?>
            </section>

        <section class="profile-card identity-card" aria-label="Identificação do usuário">
            <div class="avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24" focusable="false">
                    <path d="M12 12a4.25 4.25 0 1 0 0-8.5 4.25 4.25 0 0 0 0 8.5Zm0 2c-4.2 0-7.5 2.15-7.5 4.9V21h15v-2.1c0-2.75-3.3-4.9-7.5-4.9Z"/>
                </svg>
            </div>
            <div class="identity-copy">
                <span class="identity-label">Perfil da conta</span>
                <h2><?= $valorOuPadrao($nome ?? "") ?></h2>
                <p>Investidor <?= $escape($perfil) ?></p>
            </div>
            <span class="profile-badge"><?= $escape($perfil) ?></span>
        </section>

        <section class="profile-card details-card" aria-labelledby="details-title">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">INFORMAÇÕES</p>
                    <h2 id="details-title">Seus dados</h2>
                </div>
                <span class="section-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0v1H5v-1Z"/>
                    </svg>
                </span>
            </div>

            <dl class="details-grid">
                <div class="detail-item">
                    <dt>Idade</dt>
                    <dd><?= $idade !== "" && $idade !== null ? $escape($idade) . " anos" : "Não informada" ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Experiência com investimentos</dt>
                    <dd><?= $valorOuPadrao($experienciaLabel) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Renda mensal</dt>
                    <dd><?= ($renda_formatada ?? "0,00") !== "0,00" ? "R$ " . $escape($renda_formatada) : "Não informada" ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Horizonte de investimento</dt>
                    <dd><?= $valorOuPadrao($horizonteLabel) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Tolerância ao risco</dt>
                    <dd><?= $valorOuPadrao($toleranciaLabel) ?></dd>
                </div>
                <div class="detail-item">
                    <dt>Objetivo financeiro</dt>
                    <dd><?= $valorOuPadrao($objetivoLabel) ?></dd>
                </div>
                <div class="detail-item detail-item-wide">
                    <dt>Sobre você</dt>
                    <dd><?= $valorOuPadrao($descricao ?? "") ?></dd>
                </div>
            </dl>
        </section>

        <section class="profile-card risk-card" aria-labelledby="risk-title">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">SEU RESULTADO</p>
                    <h2 id="risk-title">Perfil de investidor</h2>
                </div>
                <span class="risk-score"><?= (int) ($score ?? 0) ?><small> pontos</small></span>
            </div>
            <div class="risk-summary">
                <span class="risk-dot" aria-hidden="true"></span>
                <strong><?= $escape($perfil) ?></strong>
            </div>
            <p class="risk-description"><?= $valorOuPadrao($descricao_perfil ?? "") ?></p>
            <a class="result-button" href="resultado.php">
                Ver resultado completo
                <span aria-hidden="true">&rarr;</span>
            </a>
            <?php if (trim($analise_ia) !== ""): ?>
                <div class="ai-analysis">
                    <h3>Análise personalizada</h3>
                    <p><?= nl2br($escape($analise_ia)) ?></p>
                </div>
            <?php endif; ?>
        </section>

        <section class="logout-card" aria-labelledby="logout-title">
            <div>
                <p class="eyebrow">SESSÃO</p>
                <h2 id="logout-title">Quer sair da sua conta?</h2>
                <p>Você poderá entrar novamente quando quiser.</p>
            </div>
            <a class="logout-button" href="logout.php">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h5v-2H5V5h5V3Zm5.6 4.6L14.2 9l2 2H9v2h7.2l-2 2 1.4 1.4L20 12l-4.4-4.4Z"/>
                </svg>
                Sair da conta
            </a>
        </section>

            <footer class="page-footer">Investegra <span aria-hidden="true">&middot;</span> Seu futuro, bem investido.</footer>
        </main>
    </div>
</body>
</html>
