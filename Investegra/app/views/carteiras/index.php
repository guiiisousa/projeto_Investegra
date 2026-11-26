<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Minhas Carteiras | INVESTEGRA
    </title>

    <link
        rel="stylesheet"
        href="css/carteiras.css?v=4"
    >
    <link
        rel="stylesheet"
        href="css/nav-reveal.css"
    >

</head>

<body>

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

<div class="container">


    <!-- =========================================================
         CABEÇALHO
         ========================================================= -->

    <div class="topo">

        <div>

            <h1>
                Minhas Carteiras
            </h1>

            <p>
                Gerencie e acompanhe seus investimentos.
            </p>

        </div>


        <div class="topo-acoes">
            <?php require BASE_PATH . "/app/views/partials/toggle_valores.php"; ?>
            <?php if (!empty($carteiras)): ?>
                <a
                    href="nova_carteira.php"
                    class="btn-principal"
                >
                    + Nova carteira
                </a>
            <?php endif; ?>
        </div>

    </div>


    <!-- =========================================================
         CARTEIRAS
         ========================================================= -->

    <?php if (empty($carteiras)): ?>

        <div class="estado-vazio">

            <h2>
                Nenhuma carteira cadastrada
            </h2>

            <p>
                Crie sua primeira carteira para começar
                a organizar seus investimentos.
            </p>

            <a
                href="nova_carteira.php"
                class="btn-principal"
            >
                + Criar carteira
            </a>

        </div>

    <?php else: ?>


        <div class="lista-carteiras">


            <?php foreach ($carteiras as $carteira): ?>

                <?php

                $totalInvestido =
                    $carteira["total_investido"];

                $totalAtual =
                    $carteira["total_atual"];

                $resultado =
                    $carteira["resultado"];

                $totalAtivos =
                    $carteira["total_ativos"];

                $ativosSemCotacao =
                    $carteira["ativos_sem_cotacao"];


                /*
                |--------------------------------------------------------------------------
                | Rentabilidade
                |--------------------------------------------------------------------------
                */

                $rentabilidade = $totalInvestido > 0
                    ? ($resultado / $totalInvestido) * 100
                    : 0;

                ?>


                <div class="card-carteira">


                    <!-- =================================================
                         CABEÇALHO
                         ================================================= -->

                    <div class="cabecalho-carteira">

                        <div>

                            <h2>

                                <?= htmlspecialchars(
                                    $carteira["nome"]
                                ) ?>

                            </h2>


                            <?php if (!empty($carteira["descricao"])): ?>

                                <p>

                                    <?= htmlspecialchars(
                                        $carteira["descricao"]
                                    ) ?>

                                </p>

                            <?php endif; ?>

                        </div>


                        <div class="acoes-carteira">

                            <a
                                href="editar_carteira.php?id=<?= $carteira["id"] ?>"
                                class="btn-editar"
                            >
                                Editar
                            </a>


                            <a
                                href="excluir_carteira.php?id=<?= $carteira["id"] ?>"
                                class="btn-excluir"
                                onclick="return confirm('Tem certeza que deseja excluir esta carteira?');"
                            >
                                Excluir
                            </a>

                        </div>

                    </div>


                    <!-- =================================================
                         RESUMO
                         ================================================= -->

                    <div class="resumo-carteira">


                        <div class="informacao-carteira">

                            <span>
                                Ativos
                            </span>

                            <strong>
                                <?= $totalAtivos ?>
                            </strong>

                        </div>


                        <div class="informacao-carteira">

                            <span>
                                Total investido
                            </span>

                            <strong data-sensitive-value>
                                <?= formatarMoeda(
                                    $totalInvestido
                                ) ?>
                            </strong>

                        </div>


                        <div class="informacao-carteira destaque">

                            <span>
                                Valor atual
                            </span>

                            <strong data-sensitive-value>
                                <?= formatarMoeda($totalAtual) ?>
                            </strong>

                        </div>


                        <div class="informacao-carteira">

                            <span>
                                Resultado
                            </span>

                            <strong
                                class="<?= classeResultado($resultado) ?>"
                                data-sensitive-value
                            >
                                <?= sinalResultado($resultado) ?>
                                <?= formatarMoeda($resultado) ?>
                            </strong>

                        </div>


                        <div class="informacao-carteira">

                            <span>
                                Rentabilidade
                            </span>

                            <strong
                                class="<?= classeResultado($rentabilidade) ?>"
                                data-sensitive-value
                            >
                                <?= sinalResultado($rentabilidade) ?>
                                <?= formatarPercentual($rentabilidade) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- =================================================
                         ATIVOS DA CARTEIRA
                         ================================================= -->

                    <?php if (!empty($carteira["ativos"])): ?>

                        <div class="ativos-carteira">

                            <h3>
                                Ativos
                            </h3>


                            <div class="lista-ativos-carteira">


                                <?php foreach (
                                    $carteira["ativos"]
                                    as $ativo
                                ): ?>

                                    <?php
                                        $ehRendaFixaAtivo = strcasecmp(
                                            trim((string) ($ativo["categoria"] ?? "")),
                                            "Renda Fixa"
                                        ) === 0;
                                    ?>

                                    <div class="linha-ativo<?= $ehRendaFixaAtivo ? " renda-fixa" : "" ?>">


                                        <div class="ativo-identificacao">

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $ativo["ticker"]
                                                ) ?>

                                            </strong>

                                            <?php if (
                                                !empty(
                                                    $ativo["categoria"]
                                                )
                                            ): ?>

                                                <span>

                                                    <?= htmlspecialchars(
                                                        $ativo["categoria"]
                                                    ) ?>

                                                </span>

                                            <?php endif; ?>

                                        </div>


                                        <?php if ($ehRendaFixaAtivo): ?>
                                            <div class="ativo-dado">
                                                <span>Valor aplicado</span>
                                                <strong data-sensitive-value>
                                                    <?= formatarMoeda($ativo["valor_investido"]) ?>
                                                </strong>
                                            </div>
                                            <div class="ativo-dado">
                                                <span>Saldo atual</span>
                                                <strong data-sensitive-value>
                                                    <?= formatarMoeda($ativo["valor_atual"]) ?>
                                                </strong>
                                            </div>
                                        <?php else: ?>
                                            <div class="ativo-dado">
                                                <span>Quantidade</span>
                                                <strong data-sensitive-value>
                                                    <?= formatarQuantidade($ativo["quantidade"]) ?>
                                                </strong>
                                            </div>
                                            <div class="ativo-dado">
                                                <span>Preço médio</span>
                                                <strong data-sensitive-value>
                                                    <?= formatarMoeda($ativo["preco_medio"]) ?>
                                                </strong>
                                            </div>
                                            <div class="ativo-dado">
                                                <span>Valor investido</span>
                                                <strong data-sensitive-value>
                                                    <?= formatarMoeda($ativo["valor_investido"]) ?>
                                                </strong>
                                            </div>
                                            <div class="ativo-dado">
                                                <span>
                                                    <?= $ativo["preco_atual"] === null
                                                        ? "Valor estimado pelo preço médio"
                                                        : "Valor atual" ?>
                                                </span>
                                                <strong data-sensitive-value>
                                                    <?= formatarMoeda($ativo["valor_atual"]) ?>
                                                </strong>
                                            </div>
                                        <?php endif; ?>


                                        <a
                                            href="ativos.php?carteira=<?= $carteira["id"] ?>"
                                            class="btn-ver"
                                        >
                                            Ver ativos
                                        </a>


                                    </div>


                                <?php endforeach; ?>


                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         HISTÓRICO DA CARTEIRA
                         ================================================= -->

                    <div class="historico-carteira">
                        <h3>Histórico da carteira</h3>

                        <?php if (empty($carteira["historico"])): ?>
                            <p class="historico-carteira-vazio">
                                Nenhuma alteração registrada nesta carteira.
                            </p>
                        <?php else: ?>
                            <div class="lista-historico-carteira">
                                <?php foreach ($carteira["historico"] as $atividade): ?>
                                    <?php
                                        $detalhes = !empty($atividade["detalhes"])
                                            ? json_decode($atividade["detalhes"], true)
                                            : null;
                                        $rotulosDetalhes = [
                                            "ticker" => "Ativo",
                                            "nome" => "Nome",
                                            "descricao" => "Descrição",
                                            "quantidade" => "Quantidade",
                                            "preco_medio" => "Preço de compra",
                                            "quantidade_comprada" => "Quantidade comprada",
                                            "preco_compra" => "Preço de compra",
                                            "quantidade_vendida" => "Quantidade vendida",
                                            "preco_venda" => "Preço de venda",
                                            "valor_adicionado" => "Valor adicionado",
                                            "valor_resgatado" => "Valor resgatado",
                                            "data_compra" => "Data da compra",
                                            "data_venda" => "Data da venda",
                                            "observacao" => "Observação",
                                        ];
                                        $ehRendaFixaDetalhe = is_array($detalhes)
                                            && (
                                                !empty($detalhes["renda_fixa"])
                                                || !empty($detalhes["renda_fixa_nome"])
                                            );
                                        $acaoExibida = ucfirst($atividade["acao"]);
                                        if ($atividade["entidade"] === "Ativo") {
                                            if ($atividade["acao"] === "criou") {
                                                $acaoExibida = "Compra";
                                            } elseif ($atividade["acao"] === "excluiu") {
                                                $acaoExibida = "Remoção";
                                            } elseif (in_array($atividade["acao"], ["comprou", "vendeu"], true)) {
                                                $acaoExibida = $atividade["acao"] === "comprou"
                                                    ? "Compra"
                                                    : "Venda";
                                            }
                                        }
                                    ?>
                                    <article class="item-historico-carteira">
                                        <span class="tipo-historico-carteira">
                                            <?= htmlspecialchars($acaoExibida) ?>
                                        </span>
                                        <div class="conteudo-historico-carteira">
                                            <strong><?= htmlspecialchars($atividade["descricao"]) ?></strong>
                                            <?php if (is_array($detalhes)): ?>
                                                <?php if (isset($detalhes["antes"]) && isset($detalhes["depois"])): ?>
                                                    <div class="detalhes-historico" data-sensitive-value>
                                                        <?php foreach ($detalhes["depois"] as $chave => $valor): ?>
                                                            <?php
                                                                $antes = $detalhes["antes"][$chave] ?? null;
                                                                $rotulo = $rotulosDetalhes[$chave] ?? ucfirst(str_replace("_", " ", $chave));
                                                                if ($ehRendaFixaDetalhe && $chave === "preco_medio") {
                                                                    $rotulo = "Valor aplicado";
                                                                }
                                                                if ($antes == $valor) {
                                                                    continue;
                                                                }
                                                                $antesExibicao = $chave === "preco_medio"
                                                                    ? formatarMoeda((float) $antes)
                                                                    : (string) $antes;
                                                                $valorExibicao = $chave === "preco_medio"
                                                                    ? formatarMoeda((float) $valor)
                                                                    : (string) $valor;
                                                            ?>
                                                            <span>
                                                                <?= htmlspecialchars($rotulo) ?>:
                                                                <b><?= htmlspecialchars($antesExibicao) ?></b>
                                                                →
                                                                <b><?= htmlspecialchars(is_scalar($valor) ? $valorExibicao : "") ?></b>
                                                            </span>
                                                        <?php endforeach; ?>
                                                        <?php foreach ($detalhes as $chave => $valor): ?>
                                                            <?php
                                                                if (
                                                                    in_array($chave, ["antes", "depois", "renda_fixa"], true)
                                                                    || !array_key_exists($chave, $rotulosDetalhes)
                                                                    || $valor === null
                                                                    || $valor === ""
                                                                ) {
                                                                    continue;
                                                                }
                                                                $rotulo = $rotulosDetalhes[$chave];
                                                                $valorExibicao = in_array(
                                                                    $chave,
                                                                    ["preco_compra", "preco_venda", "valor_adicionado", "valor_resgatado"],
                                                                    true
                                                                )
                                                                    ? formatarMoeda((float) $valor)
                                                                    : (string) $valor;
                                                            ?>
                                                            <span>
                                                                <?= htmlspecialchars($rotulo) ?>:
                                                                <b><?= htmlspecialchars($valorExibicao) ?></b>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php elseif (!empty($detalhes)): ?>
                                                    <div class="detalhes-historico" data-sensitive-value>
                                                        <?php foreach ($detalhes as $chave => $valor): ?>
                                                            <?php
                                                                if (!$chave || $valor === null || $valor === "") {
                                                                    continue;
                                                                }
                                                                $rotulo = $rotulosDetalhes[$chave] ?? ucfirst(str_replace("_", " ", $chave));
                                                                if ($ehRendaFixaDetalhe && $chave === "preco_medio") {
                                                                    $rotulo = "Valor aplicado";
                                                                }
                                                                $valorExibicao = in_array(
                                                                    $chave,
                                                                    ["preco_medio", "preco_compra", "preco_venda", "valor_adicionado", "valor_resgatado"],
                                                                    true
                                                                ) && is_numeric($valor)
                                                                    ? formatarMoeda((float) $valor)
                                                                    : (string) $valor;
                                                            ?>
                                                            <span>
                                                                <?= htmlspecialchars($rotulo) ?>:
                                                                <b><?= htmlspecialchars(is_scalar($valor) ? $valorExibicao : "") ?></b>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <time datetime="<?= htmlspecialchars($atividade["data_registro"]) ?>">
                                            <?= date("d/m/Y H:i", strtotime($atividade["data_registro"])) ?>
                                        </time>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>


                    <!-- =================================================
                         AVISO DE COTAÇÕES
                         ================================================= -->

                    <?php if (
                        $totalAtivos > 0 &&
                        $ativosSemCotacao > 0
                    ): ?>

                        <div class="aviso-cotacao">
                            Algumas cotações não estão disponíveis.
                            Para esses ativos, o valor atual foi estimado pelo preço médio;
                            Renda Fixa considera o saldo aplicado.
                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         BOTÃO
                         ================================================= -->

                    <div class="rodape-carteira">

                        <a
                            href="ativos.php?carteira=<?= $carteira["id"] ?>"
                            class="btn-principal"
                        >
                            Ver carteira
                        </a>

                    </div>


                </div>

            <?php endforeach; ?>


        </div>

    <?php endif; ?>


</div>


</body>

</html>