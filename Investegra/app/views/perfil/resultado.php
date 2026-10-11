<?php
$escape = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
};

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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f4f7f6">
    <title>Resultado do perfil | Investegra</title>
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="css/nav-reveal.css">
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
                <p class="eyebrow">SEU RESULTADO</p>
                <h1 id="page-title">Resultado do seu perfil</h1>
                <p class="heading-copy">Olá, <?= $escape($nome) ?>. Confira o resultado do seu questionário de investidor.</p>
            </section>

            <section class="profile-card risk-card" aria-labelledby="risk-title">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">PERFIL DE RISCO</p>
                        <h2 id="risk-title">Seu perfil de investidor</h2>
                    </div>
                    <span class="risk-score"><?= (int) $score ?><small> pontos</small></span>
                </div>
                <div class="risk-summary">
                    <span class="risk-dot" aria-hidden="true"></span>
                    <strong><?= $escape($perfil) ?></strong>
                </div>
                <p class="risk-description"><?= $escape($descricao_perfil) ?></p>
            </section>

            <section class="profile-card details-card" aria-labelledby="details-title">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">QUESTIONÁRIO</p>
                        <h2 id="details-title">Informações consideradas</h2>
                    </div>
                </div>
                <dl class="details-grid">
                    <div class="detail-item">
                        <dt>Idade</dt>
                        <dd><?= $idade !== "" && $idade !== null ? $escape($idade) . " anos" : "Não informada" ?></dd>
                    </div>
                    <div class="detail-item">
                        <dt>Experiência</dt>
                        <dd><?= $experienciaLabel !== "" ? $escape($experienciaLabel) : "Não informada" ?></dd>
                    </div>
                    <div class="detail-item">
                        <dt>Renda mensal aproximada</dt>
                        <dd><?= ($renda_formatada ?? "0,00") !== "0,00" ? "R$ " . $escape($renda_formatada) : "Não informada" ?></dd>
                    </div>
                    <div class="detail-item">
                        <dt>Horizonte de investimento</dt>
                        <dd><?= $horizonteLabel !== "" ? $escape($horizonteLabel) : "Não informado" ?></dd>
                    </div>
                    <div class="detail-item">
                        <dt>Tolerância ao risco</dt>
                        <dd><?= $toleranciaLabel !== "" ? $escape($toleranciaLabel) : "Não informada" ?></dd>
                    </div>
                    <div class="detail-item">
                        <dt>Objetivo financeiro</dt>
                        <dd><?= $objetivoLabel !== "" ? $escape($objetivoLabel) : "Não informado" ?></dd>
                    </div>
                    <?php if (trim((string) $descricao) !== ""): ?>
                        <div class="detail-item detail-item-wide">
                            <dt>Sobre você</dt>
                            <dd><?= nl2br($escape($descricao)) ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </section>

            <section class="profile-card ai-analysis" aria-labelledby="analysis-title">
                <h3 id="analysis-title">Análise complementar</h3>
                <?php if (trim((string) $analise_ia) !== ""): ?>
                    <p><?= nl2br($escape($analise_ia)) ?></p>
                <?php else: ?>
                    <p>A análise complementar ainda não está disponível. Seu perfil foi calculado e salvo normalmente.</p>
                <?php endif; ?>
                <p class="analysis-notice">
                    Esta análise tem finalidade educacional. Não constitui recomendação de compra ou venda de ativos.
                </p>
            </section>

            <div class="result-actions">
                <a class="result-button result-button-secondary" href="meu_perfil.php">Voltar ao meu perfil</a>
                <a class="result-button" href="questionario_perfil.php">Refazer questionário</a>
            </div>

            <footer class="page-footer">Investegra <span aria-hidden="true">&middot;</span> Seu futuro, bem investido.</footer>
        </main>
    </div>
</body>
</html>
