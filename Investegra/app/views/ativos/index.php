<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Ativos - <?= htmlspecialchars($carteira["nome"]) ?> | INVESTEGRA
    </title>

    <link
        rel="stylesheet"
        href="css/ativos.css?v=7"
    >
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >

</head>

<body>

<div class="layout">

<aside class="sidebar">

    <a href="dashboard.php" class="logo-link" aria-label="Ir para o Dashboard">
        <div class="logo">
            INVESTEGRA
        </div>
    </a>

    <nav>
        <?php require BASE_PATH . "/app/views/partials/perfil_nav.php"; ?>
        <a href="carteiras.php" class="ativo">Carteiras</a>
        <a href="simulacao.php">Simulações</a>
        <a href="analises.php">Análises</a>
        <a href="recomendacoes.php">Recomendações</a>
        <a href="historico.php">Histórico</a>
    </nav>

    <a href="logout.php" class="logout">Sair</a>

</aside>

<main class="conteudo">

<div class="container">


    <!-- =========================================================
         CABEÇALHO
         ========================================================= -->

    <div class="topo">

        <div>

            <h1>
                <?= htmlspecialchars($carteira["nome"]) ?>
            </h1>

            <?php if (!empty($carteira["descricao"])): ?>

                <p class="descricao-carteira">
                    <?= htmlspecialchars($carteira["descricao"]) ?>
                </p>

            <?php endif; ?>

        </div>

        <div class="acoes-topo">
            <a
                href="carteiras.php"
                class="btn-voltar-carteiras"
            >
                ← Voltar para carteiras
            </a>
            <a
                href="novo_ativo.php?carteira=<?= $carteira_id ?>"
                class="btn-principal btn-adicionar-ativo"
            >
                + Adicionar ativo
            </a>
        </div>

    </div>


    <!-- =========================================================
         RESUMO
         ========================================================= -->

    <div class="resumo">


        <div class="card-resumo">

            <span>
                Ativos
            </span>

            <strong>
                <?= count($ativos) ?>
            </strong>

        </div>


        <div class="card-resumo">

            <span>
                Total investido
            </span>

            <strong>
                <?= formatarMoeda($totalInvestido) ?>
            </strong>

        </div>


        <div class="card-resumo">

            <span>
                Valor atual
            </span>

            <strong>
                <?= formatarMoeda($totalAtual) ?>
            </strong>

        </div>


        <div class="card-resumo">

            <span>
                Resultado
            </span>

            <strong class="<?= classeResultado($totalResultado) ?>">
                <?= sinalResultado($totalResultado) ?>
                <?= formatarMoeda($totalResultado) ?>
            </strong>

        </div>


        <div class="card-resumo">

            <span>
                Rentabilidade
            </span>

            <strong class="<?= classeResultado($rentabilidade ?? 0) ?>">
                <?= sinalResultado($rentabilidade ?? 0) ?>
                <?= number_format($rentabilidade ?? 0, 2, ",", ".") ?>%
            </strong>

        </div>

    </div>


    <!-- =========================================================
         GRÁFICOS DA CARTEIRA
         ========================================================= -->

    <section class="graficos-carteira">
        <article class="painel-grafico-carteira">
            <div class="titulo-grafico-carteira">
                <div>
                    <h2>Evolução da carteira</h2>
                    <p>Variação do patrimônio registrada pelas alterações</p>
                </div>
            </div>

            <?php if (empty($labelsEvolucaoCarteira)): ?>
                <p class="grafico-carteira-vazio">
                    Não há registros suficientes para mostrar a evolução.
                </p>
            <?php else: ?>
                <div class="grafico-carteira-container">
                    <canvas id="graficoEvolucaoCarteira"></canvas>
                </div>
            <?php endif; ?>
        </article>

        <article class="painel-grafico-carteira">
            <div class="titulo-grafico-carteira">
                <div>
                    <h2>Distribuição por categoria</h2>
                    <p>Valor investido em cada categoria</p>
                </div>
            </div>

            <?php if (empty($labelsCategoriasCarteira)): ?>
                <p class="grafico-carteira-vazio">
                    Adicione ativos para visualizar a distribuição.
                </p>
            <?php else: ?>
                <div class="grafico-carteira-container grafico-categoria-carteira">
                    <canvas id="graficoCategoriasCarteira"></canvas>
                </div>
            <?php endif; ?>
        </article>
    </section>


    <!-- =========================================================
         LISTA DE ATIVOS
         ========================================================= -->

    <section class="secao-ativos">

        <div class="secao-ativos-titulo">
            <h2>Ativos da carteira</h2>
            <span><?= count($ativos) ?> <?= count($ativos) === 1 ? "ativo" : "ativos" ?></span>
        </div>

    <?php if (empty($ativos)): ?>

        <div class="estado-vazio">

            <h2>
                Nenhum ativo cadastrado
            </h2>

            <p>
                Adicione o primeiro ativo para começar a acompanhar
                sua carteira.
            </p>

        </div>

    <?php else: ?>


        <div class="lista-ativos">


            <?php foreach ($ativos as $ativo): ?>

                <?php

                $ehRendaFixa = ($ativo["categoria"] ?? "") === "Renda Fixa";
                $ehCripto = ($ativo["categoria"] ?? "") === "Criptomoedas";
                $ticker = strtoupper(trim($ativo["ticker"]));
                $passoQuantidade = $ehCripto ? "0.00000001" : "1";
                $minimoQuantidade = $ehCripto ? "0.00000001" : "1";
                $quantidadeDisponivel = number_format(
                    (float) $ativo["quantidade"],
                    8,
                    ".",
                    ""
                );
                $nomeExibicao = $ehRendaFixa
                    ? ($ativo["renda_fixa_nome"] ?: $ticker)
                    : $ticker;

                $cotacao = $ativo["cotacao"];

                $precoAtual = $ativo["preco_atual"];

                $valorInvestido =
                    $ativo["valor_investido"];

                $precoMovimentacao = $precoAtual !== null
                    ? (float) $precoAtual
                    : (float) $ativo["preco_medio"];

                $valorAtual =
                    $ativo["valor_atual"];

                $resultado =
                    $ativo["resultado"];

                $variacaoPercentual =
                    $ativo["variacao_percentual"];

                ?>


                <div class="card-ativo">


                    <!-- =================================================
                         IDENTIFICAÇÃO
                         ================================================= -->

                    <div class="cabecalho-ativo">

                        <div>

                            <span class="ticker">
                                <?= htmlspecialchars($nomeExibicao) ?>
                            </span>

                            <?php if ($ehRendaFixa): ?>
                                <span class="nome-ativo">
                                    <?= htmlspecialchars($ticker) ?>
                                </span>
                            <?php elseif (!empty($cotacao["nome"])): ?>

                                <span class="nome-ativo">

                                    <?= htmlspecialchars(
                                        $cotacao["nome"]
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </div>


                        <?php if (!empty($ativo["categoria"])): ?>

                            <span class="categoria">

                                <?= htmlspecialchars(
                                    $ativo["categoria"]
                                ) ?>

                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- =================================================
                         INFORMAÇÕES
                         ================================================= -->

                    <div class="informacoes-ativo<?= $ehRendaFixa ? " renda-fixa" : "" ?>">


                        <?php if (!$ehRendaFixa): ?>
                            <div class="informacao">
                                <span>Quantidade</span>
                                <strong>
                                    <?= formatarQuantidade($ativo["quantidade"]) ?>
                                </strong>
                            </div>
                        <?php endif; ?>


                        <div class="informacao">

                            <span>
                                <?= $ehRendaFixa ? "Valor aplicado" : "Preço médio" ?>
                            </span>

                            <strong>

                                <?= formatarMoeda(
                                    $ativo["preco_medio"]
                                ) ?>

                            </strong>

                        </div>


                        <?php if (!$ehRendaFixa): ?>
                            <div class="informacao destaque">
                                <span>Cotação atual</span>
                                <?php if ($precoAtual !== null): ?>
                                    <strong><?= formatarMoeda($precoAtual) ?></strong>
                                <?php else: ?>
                                    <strong class="indisponivel">Indisponível</strong>
                                <?php endif; ?>
                            </div>

                            <div class="informacao">
                                <span>Valor investido</span>
                                <strong><?= formatarMoeda($valorInvestido) ?></strong>
                            </div>
                        <?php endif; ?>


                        <div class="informacao destaque">

                            <span>
                                <?= $ehRendaFixa
                                    ? "Saldo atual"
                                    : ($precoAtual === null
                                        ? "Valor estimado pelo preço médio"
                                        : "Valor atual") ?>
                            </span>
                            <strong><?= formatarMoeda($valorAtual) ?></strong>

                        </div>


                        <?php if (!$ehRendaFixa): ?>
                        <div class="informacao">

                            <span>
                                Resultado
                            </span>

                            <strong class="<?= classeResultado($resultado) ?>">
                                <?= sinalResultado($resultado) ?>
                                <?= formatarMoeda($resultado) ?>
                            </strong>

                        </div>


                        <div class="informacao">

                            <span>
                                Variação do dia
                            </span>

                            <?php if ($variacaoPercentual !== null): ?>
                                <strong class="<?= classeResultado($variacaoPercentual) ?>">
                                    <?= sinalResultado($variacaoPercentual) ?>
                                    <?= number_format($variacaoPercentual, 2, ",", ".") ?>%
                                </strong>
                            <?php else: ?>
                                <strong class="indisponivel">Indisponível</strong>
                            <?php endif; ?>

                        </div>
                        <?php endif; ?>


                    </div>

                    <?php if ($ehRendaFixa): ?>
                        <div class="observacao">
                            <strong>Indexador:</strong>
                            <?= htmlspecialchars($ativo["renda_fixa_indexador"] ?? "") ?>
                            <span> · </span>
                            <strong>Taxa:</strong>
                            <?= number_format((float) $ativo["renda_fixa_taxa"], 2, ",", ".") ?>%
                            <?php if (!empty($ativo["renda_fixa_vencimento"])): ?>
                                <span> · </span>
                                <strong>Vencimento:</strong>
                                <?= date("d/m/Y", strtotime($ativo["renda_fixa_vencimento"])) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>


                    <!-- =================================================
                         DATA DE COMPRA
                         ================================================= -->

                    <?php if (!empty($ativo["data_compra"])): ?>

                        <div class="data-compra">

                            <?= $ehRendaFixa ? "Aplicação realizada em:" : "Compra realizada em:" ?>

                            <?= date(
                                "d/m/Y",
                                strtotime(
                                    $ativo["data_compra"]
                                )
                            ) ?>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         OBSERVAÇÃO
                         ================================================= -->

                    <?php if (!empty($ativo["observacao"])): ?>

                        <div class="observacao">

                            <strong>
                                Observação:
                            </strong>

                            <?= htmlspecialchars(
                                $ativo["observacao"]
                            ) ?>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         AÇÕES
                         ================================================= -->

                    <div class="acoes-ativo">
                        <details
                            class="movimentacao-ativo movimentacao-compra"
                            name="movimentacao-<?= (int) $ativo["id"] ?>"
                        >
                            <summary class="btn-comprar">Compra</summary>
                            <form action="comprar_ativo.php" method="POST" class="form-movimentacao">
                                <input type="hidden" name="id" value="<?= (int) $ativo["id"] ?>">
                                <?php if ($ehRendaFixa): ?>
                                    <label>
                                        Valor a adicionar (R$)
                                        <input type="number" name="preco_medio" min="0.01" step="0.01" required>
                                    </label>
                                    <label>
                                        Data do aporte
                                        <input type="date" name="data_compra" value="<?= date('Y-m-d') ?>" required>
                                    </label>
                                <?php else: ?>
                                <label>
                                    Quantidade
                                    <input type="number" name="quantidade" min="<?= $minimoQuantidade ?>" step="<?= $passoQuantidade ?>" required>
                                </label>
                                <label>
                                    Preço de compra
                                    <input type="number" name="preco_medio" min="0.01" step="0.01" value="<?= htmlspecialchars(number_format($precoMovimentacao, 2, ".", "")) ?>" required>
                                </label>
                                <label>
                                    Data da compra
                                    <input type="date" name="data_compra" value="<?= date('Y-m-d') ?>" required>
                                </label>
                                <?php endif; ?>
                                <button type="submit" class="btn-confirmar-compra">Confirmar compra</button>
                            </form>
                        </details>

                        <details
                            class="movimentacao-ativo movimentacao-venda"
                            name="movimentacao-<?= (int) $ativo["id"] ?>"
                        >
                            <summary class="btn-vender">Venda</summary>
                            <form action="vender_ativo.php" method="POST" class="form-movimentacao" onsubmit="return confirm('<?= $ehRendaFixa ? "Confirmar este resgate?" : "Confirmar a venda desta quantidade?" ?>');">
                                <input type="hidden" name="id" value="<?= (int) $ativo["id"] ?>">
                                <?php if ($ehRendaFixa): ?>
                                    <label>
                                        Valor a retirar (R$)
                                        <input type="number" name="preco_medio" min="0.01" max="<?= number_format($valorInvestido, 2, ".", "") ?>" step="0.01" required>
                                    </label>
                                    <label>
                                        Data do resgate
                                        <input type="date" name="data_venda" value="<?= date('Y-m-d') ?>" required>
                                    </label>
                                    <div class="saldo-disponivel">
                                        Disponível: <strong><?= formatarMoeda($valorInvestido) ?></strong>
                                    </div>
                                <?php else: ?>
                                <label>
                                    Quantidade
                                    <input type="number" name="quantidade" min="<?= $minimoQuantidade ?>" max="<?= $quantidadeDisponivel ?>" step="<?= $passoQuantidade ?>" required>
                                </label>
                                <label>
                                    Preço de venda
                                    <input type="number" name="preco_medio" min="0.01" step="0.01" value="<?= htmlspecialchars(number_format($precoMovimentacao, 2, ".", "")) ?>" required>
                                </label>
                                <label>
                                    Data da venda
                                    <input type="date" name="data_venda" value="<?= date('Y-m-d') ?>" required>
                                </label>
                                <div class="saldo-disponivel">
                                    Disponível: <strong><?= formatarQuantidade($ativo["quantidade"]) ?></strong>
                                </div>
                                <?php endif; ?>
                                <button type="submit" class="btn-confirmar-venda">Confirmar venda</button>
                            </form>
                        </details>
                    </div>


                </div>

            <?php endforeach; ?>


        </div>

    <?php endif; ?>

    </section>


    <!-- =========================================================
         HISTÓRICO DA CARTEIRA
         ========================================================= -->

    <section class="secao-historico-carteira">

        <div class="secao-historico-titulo">
            <h2>Histórico da carteira</h2>
            <span><?= count($historicoCarteira) ?> <?= count($historicoCarteira) === 1 ? "registro" : "registros" ?></span>
        </div>

        <?php if (empty($historicoCarteira)): ?>
            <div class="historico-carteira-vazio">
                Nenhuma alteração foi registrada nesta carteira.
            </div>
        <?php else: ?>
            <div class="tabela-historico-carteira-container">
                <table class="tabela-historico-carteira">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Ação</th>
                            <th>Descrição</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historicoCarteira as $registro): ?>
                            <?php
                                $detalhes = !empty($registro["detalhes"])
                                    ? json_decode($registro["detalhes"], true)
                                    : [];
                                $rotulos = [
                                    "ticker" => "Ativo",
                                    "nome" => "Nome",
                                    "descricao" => "Descrição",
                                    "quantidade" => "Quantidade",
                                    "preco_medio" => "Preço de compra",
                                    "data_compra" => "Data da compra",
                                    "observacao" => "Observação",
                                    "renda_fixa_nome" => "Produto / emissor",
                                    "renda_fixa_indexador" => "Indexador",
                                    "renda_fixa_taxa" => "Taxa contratada",
                                    "renda_fixa_vencimento" => "Vencimento",
                                ];
                                $acaoExibida = $registro["entidade"] === "Ativo"
                                    ? ($registro["acao"] === "criou"
                                        ? "Compra"
                                        : ($registro["acao"] === "comprou"
                                            ? "Compra"
                                            : ($registro["acao"] === "excluiu"
                                            ? "Venda"
                                            : ($registro["acao"] === "vendeu"
                                                ? "Venda"
                                                : ucfirst($registro["acao"])))))
                                    : ucfirst($registro["acao"]);
                            ?>
                            <tr>
                                <td>
                                    <time datetime="<?= htmlspecialchars($registro["data_registro"]) ?>">
                                        <?= date("d/m/Y", strtotime($registro["data_registro"])) ?>
                                        <small><?= date("H:i", strtotime($registro["data_registro"])) ?></small>
                                    </time>
                                </td>
                                <td>
                                    <span class="acao-historico acao-<?= htmlspecialchars($registro["acao"]) ?>">
                                        <?= htmlspecialchars($acaoExibida) ?>
                                    </span>
                                </td>
                                <td class="descricao-historico">
                                    <?= htmlspecialchars($registro["descricao"]) ?>
                                </td>
                                <td>
                                    <?php if (isset($detalhes["antes"], $detalhes["depois"]) && is_array($detalhes["antes"]) && is_array($detalhes["depois"])): ?>
                                        <div class="detalhes-tabela-historico">
                                            <?php foreach ($detalhes["depois"] as $chave => $valor): ?>
                                                <?php
                                                    $antes = $detalhes["antes"][$chave] ?? null;
                                                    if ($antes == $valor) {
                                                        continue;
                                                    }
                                                    $rotulo = $rotulos[$chave] ?? ucfirst(str_replace("_", " ", $chave));
                                                ?>
                                                <span>
                                                    <?= htmlspecialchars($rotulo) ?>:
                                                    <b><?= htmlspecialchars((string) $antes) ?></b>
                                                    → <b><?= htmlspecialchars(is_scalar($valor) ? (string) $valor : "") ?></b>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif (is_array($detalhes)): ?>
                                        <div class="detalhes-tabela-historico">
                                            <?php foreach ($detalhes as $chave => $valor): ?>
                                                <?php
                                                    if ($valor === null || $valor === "") {
                                                        continue;
                                                    }
                                                    $rotulo = $rotulos[$chave] ?? ucfirst(str_replace("_", " ", $chave));
                                                ?>
                                                <span>
                                                    <?= htmlspecialchars($rotulo) ?>:
                                                    <b><?= htmlspecialchars(is_scalar($valor) ? (string) $valor : "") ?></b>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>


    <!-- =========================================================
         AVISO
         ========================================================= -->

    <?php if (!empty($ativos)): ?>

        <div class="aviso-cotacao">

            <strong>
                Dados de mercado:
            </strong>

            As cotações exibidas são obtidas por meio de uma
            API externa e podem sofrer variações durante o pregão.
            Caso uma cotação não esteja disponível, o sistema
            apresenta o valor como indisponível.

        </div>

    <?php endif; ?>

</div>

</main>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labelsEvolucaoCarteira = <?= json_encode($labelsEvolucaoCarteira, JSON_UNESCAPED_UNICODE) ?>;
const valoresEvolucaoCarteira = <?= json_encode($valoresEvolucaoCarteira) ?>;
const labelsCategoriasCarteira = <?= json_encode($labelsCategoriasCarteira, JSON_UNESCAPED_UNICODE) ?>;
const valoresCategoriasCarteira = <?= json_encode($valoresCategoriasCarteira) ?>;

const formatarMoedaGrafico = valor =>
    "R$ " + Number(valor).toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

const canvasEvolucao = document.getElementById("graficoEvolucaoCarteira");
if (canvasEvolucao && labelsEvolucaoCarteira.length > 0) {
    new Chart(canvasEvolucao, {
        type: "line",
        data: {
            labels: labelsEvolucaoCarteira,
            datasets: [{
                label: "Patrimônio",
                data: valoresEvolucaoCarteira,
                borderColor: "#00a86b",
                backgroundColor: "rgba(0, 168, 107, 0.12)",
                borderWidth: 3,
                tension: 0.32,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: "#00a86b"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: contexto => " " + formatarMoedaGrafico(contexto.parsed.y)
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: "#75807c" }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: "#75807c",
                        callback: valor => formatarMoedaGrafico(valor)
                    },
                    grid: { color: "#edf1ef" }
                }
            }
        }
    });
}

const canvasCategorias = document.getElementById("graficoCategoriasCarteira");
if (canvasCategorias && labelsCategoriasCarteira.length > 0) {
    const totalCategoriasCarteira = valoresCategoriasCarteira.reduce(
        (total, valor) => total + Number(valor),
        0
    );

    new Chart(canvasCategorias, {
        type: "doughnut",
        data: {
            labels: labelsCategoriasCarteira,
            datasets: [{
                data: valoresCategoriasCarteira,
                backgroundColor: [
                    "#00a86b",
                    "#2878c8",
                    "#f39c12",
                    "#8e44ad",
                    "#d64545",
                    "#17a2b8"
                ],
                borderColor: "#ffffff",
                borderWidth: 3,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "68%",
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: "#75807c",
                        usePointStyle: true,
                        padding: 14,
                        boxWidth: 8
                    }
                },
                tooltip: {
                    callbacks: {
                        label: contexto => {
                            const valor = Number(contexto.parsed);
                            const percentual = totalCategoriasCarteira > 0
                                ? (valor / totalCategoriasCarteira) * 100
                                : 0;
                            return " " + contexto.label + ": "
                                + formatarMoedaGrafico(valor)
                                + " (" + percentual.toLocaleString("pt-BR", {
                                    minimumFractionDigits: 1,
                                    maximumFractionDigits: 1
                                }) + "%)";
                        }
                    }
                }
            }
        },
        plugins: [{
            id: "centroCategoriaCarteira",
            beforeDraw: chart => {
                const area = chart.chartArea;
                if (!area) {
                    return;
                }

                const ctx = chart.ctx;
                ctx.save();
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillStyle = "#75807c";
                ctx.font = "600 12px Arial";
                ctx.fillText(
                    "TOTAL INVESTIDO",
                    (area.left + area.right) / 2,
                    (area.top + area.bottom) / 2 - 10
                );
                ctx.fillStyle = "#202925";
                ctx.font = "700 16px Arial";
                ctx.fillText(
                    formatarMoedaGrafico(totalCategoriasCarteira),
                    (area.left + area.right) / 2,
                    (area.top + area.bottom) / 2 + 12
                );
                ctx.restore();
            }
        }]
    });
}
</script>
</body>

</html>