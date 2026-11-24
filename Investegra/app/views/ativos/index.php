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
        href="css/ativos.css?v=3"
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
        <a href="alertas.php">Alertas</a>
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

            <?php if ($totalAtual > 0): ?>

                <strong>
                    <?= formatarMoeda($totalAtual) ?>
                </strong>

            <?php else: ?>

                <strong>
                    —
                </strong>

            <?php endif; ?>

        </div>


        <div class="card-resumo">

            <span>
                Resultado
            </span>

            <?php if ($totalInvestido > 0 && $totalAtual > 0): ?>

                <strong
                    class="<?= classeResultado($totalResultado) ?>"
                >

                    <?= sinalResultado($totalResultado) ?>

                    <?= formatarMoeda($totalResultado) ?>

                </strong>

            <?php else: ?>

                <strong>
                    —
                </strong>

            <?php endif; ?>

        </div>


        <div class="card-resumo">

            <span>
                Rentabilidade
            </span>

            <?php if ($rentabilidade !== null): ?>

                <strong
                    class="<?= classeResultado($rentabilidade) ?>"
                >

                    <?= sinalResultado($rentabilidade) ?>

                    <?= number_format(
                        $rentabilidade,
                        2,
                        ",",
                        "."
                    ) ?>%

                </strong>

            <?php else: ?>

                <strong>
                    —
                </strong>

            <?php endif; ?>

        </div>

    </div>


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

                $ticker = strtoupper(
                    trim($ativo["ticker"])
                );

                $cotacao = $ativo["cotacao"];

                $precoAtual = $ativo["preco_atual"];

                $valorInvestido =
                    $ativo["valor_investido"];

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
                                <?= htmlspecialchars($ticker) ?>
                            </span>

                            <?php if (!empty($cotacao["nome"])): ?>

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

                    <div class="informacoes-ativo">


                        <div class="informacao">

                            <span>
                                Quantidade
                            </span>

                            <strong>

                                <?= formatarQuantidade(
                                    $ativo["quantidade"]
                                ) ?>

                            </strong>

                        </div>


                        <div class="informacao">

                            <span>
                                Preço de compra
                            </span>

                            <strong>

                                <?= formatarMoeda(
                                    $ativo["preco_medio"]
                                ) ?>

                            </strong>

                        </div>


                        <div class="informacao destaque">

                            <span>
                                Cotação atual
                            </span>

                            <?php if ($precoAtual !== null): ?>

                                <strong>

                                    <?= formatarMoeda(
                                        $precoAtual
                                    ) ?>

                                </strong>

                            <?php else: ?>

                                <strong class="indisponivel">
                                    Indisponível
                                </strong>

                            <?php endif; ?>

                        </div>


                        <div class="informacao">

                            <span>
                                Valor investido
                            </span>

                            <strong>

                                <?= formatarMoeda(
                                    $valorInvestido
                                ) ?>

                            </strong>

                        </div>


                        <div class="informacao destaque">

                            <span>
                                Valor atual
                            </span>

                            <?php if ($valorAtual !== null): ?>

                                <strong>

                                    <?= formatarMoeda(
                                        $valorAtual
                                    ) ?>

                                </strong>

                            <?php else: ?>

                                <strong class="indisponivel">
                                    Indisponível
                                </strong>

                            <?php endif; ?>

                        </div>


                        <div class="informacao">

                            <span>
                                Resultado
                            </span>

                            <?php if ($resultado !== null): ?>

                                <strong
                                    class="<?= classeResultado($resultado) ?>"
                                >

                                    <?= sinalResultado($resultado) ?>

                                    <?= formatarMoeda(
                                        $resultado
                                    ) ?>

                                </strong>

                            <?php else: ?>

                                <strong class="indisponivel">
                                    —
                                </strong>

                            <?php endif; ?>

                        </div>


                        <div class="informacao">

                            <span>
                                Variação do dia
                            </span>

                            <?php if ($variacaoPercentual !== null): ?>

                                <strong
                                    class="<?= classeResultado($variacaoPercentual) ?>"
                                >

                                    <?= sinalResultado(
                                        $variacaoPercentual
                                    ) ?>

                                    <?= number_format(
                                        $variacaoPercentual,
                                        2,
                                        ",",
                                        "."
                                    ) ?>%

                                </strong>

                            <?php else: ?>

                                <strong class="indisponivel">
                                    —
                                </strong>

                            <?php endif; ?>

                        </div>


                    </div>


                    <!-- =================================================
                         DATA DE COMPRA
                         ================================================= -->

                    <?php if (!empty($ativo["data_compra"])): ?>

                        <div class="data-compra">

                            Compra realizada em:

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

                        <a
                            href="editar_ativo.php?id=<?= $ativo["id"] ?>&carteira=<?= $carteira_id ?>"
                            class="btn-editar"
                        >
                            Editar
                        </a>


                        <a
                            href="excluir_ativo.php?id=<?= $ativo["id"] ?>&carteira=<?= $carteira_id ?>"
                            class="btn-excluir"
                            onclick="return confirm('Tem certeza que deseja excluir este ativo?');"
                        >
                            Excluir
                        </a>

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
                                ];
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
                                        <?= htmlspecialchars(ucfirst($registro["acao"])) ?>
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

</body>

</html>