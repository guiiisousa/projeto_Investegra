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
        href="css/carteiras.css?v=2"
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
        <a href="alertas.php">Alertas</a>
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


        <?php if (!empty($carteiras)): ?>
            <a
                href="nova_carteira.php"
                class="btn-principal"
            >
                + Nova carteira
            </a>
        <?php endif; ?>

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

                $cotacoesDisponiveis =
                    $carteira["cotacoes_disponiveis"];


                /*
                |--------------------------------------------------------------------------
                | Rentabilidade
                |--------------------------------------------------------------------------
                */

                $rentabilidade = null;

                if ($totalInvestido > 0) {

                    /*
                     * Só calculamos se houver pelo menos
                     * uma cotação disponível.
                     */

                    if ($cotacoesDisponiveis > 0) {

                        $rentabilidade =
                            (
                                $resultado
                                / $totalInvestido
                            ) * 100;
                    }
                }

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

                            <strong>
                                <?= formatarMoeda(
                                    $totalInvestido
                                ) ?>
                            </strong>

                        </div>


                        <div class="informacao-carteira destaque">

                            <span>
                                Valor atual
                            </span>

                            <?php if ($cotacoesDisponiveis > 0): ?>

                                <strong>
                                    <?= formatarMoeda(
                                        $totalAtual
                                    ) ?>
                                </strong>

                            <?php else: ?>

                                <strong class="indisponivel">
                                    Indisponível
                                </strong>

                            <?php endif; ?>

                        </div>


                        <div class="informacao-carteira">

                            <span>
                                Resultado
                            </span>

                            <?php if ($cotacoesDisponiveis > 0): ?>

                                <strong
                                    class="<?= classeResultado($resultado) ?>"
                                >

                                    <?= sinalResultado(
                                        $resultado
                                    ) ?>

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


                        <div class="informacao-carteira">

                            <span>
                                Rentabilidade
                            </span>

                            <?php if ($rentabilidade !== null): ?>

                                <strong
                                    class="<?= classeResultado($rentabilidade) ?>"
                                >

                                    <?= sinalResultado(
                                        $rentabilidade
                                    ) ?>

                                    <?= formatarPercentual(
                                        $rentabilidade
                                    ) ?>

                                </strong>

                            <?php else: ?>

                                <strong class="indisponivel">
                                    —
                                </strong>

                            <?php endif; ?>

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


                                    <div class="linha-ativo">


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


                                        <div class="ativo-dado">

                                            <span>
                                                Quantidade
                                            </span>

                                            <strong>

                                                <?= number_format(
                                                    (int) $ativo["quantidade"],
                                                    0,
                                                    "",
                                                    "."
                                                ) ?>

                                            </strong>

                                        </div>


                                        <div class="ativo-dado">

                                            <span>
                                                Preço de compra
                                            </span>

                                            <strong>

                                                <?= formatarMoeda(
                                                    $ativo["preco_medio"]
                                                ) ?>

                                            </strong>

                                        </div>


                                        <div class="ativo-dado">

                                            <span>
                                                Cotação
                                            </span>

                                            <?php if (
                                                $ativo["preco_atual"]
                                                !== null
                                            ): ?>

                                                <strong>

                                                    <?= formatarMoeda(
                                                        $ativo["preco_atual"]
                                                    ) ?>

                                                </strong>

                                            <?php else: ?>

                                                <strong class="indisponivel">
                                                    Indisponível
                                                </strong>

                                            <?php endif; ?>

                                        </div>


                                        <div class="ativo-dado">

                                            <span>
                                                Valor atual
                                            </span>

                                            <?php if (
                                                $ativo["valor_atual"]
                                                !== null
                                            ): ?>

                                                <strong>

                                                    <?= formatarMoeda(
                                                        $ativo["valor_atual"]
                                                    ) ?>

                                                </strong>

                                            <?php else: ?>

                                                <strong class="indisponivel">
                                                    —
                                                </strong>

                                            <?php endif; ?>

                                        </div>


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
                                            "data_compra" => "Data da compra",
                                            "observacao" => "Observação",
                                        ];
                                    ?>
                                    <article class="item-historico-carteira">
                                        <span class="tipo-historico-carteira">
                                            <?= htmlspecialchars(ucfirst($atividade["acao"])) ?>
                                        </span>
                                        <div class="conteudo-historico-carteira">
                                            <strong><?= htmlspecialchars($atividade["descricao"]) ?></strong>
                                            <?php if (is_array($detalhes)): ?>
                                                <?php if (isset($detalhes["antes"]) && isset($detalhes["depois"])): ?>
                                                    <div class="detalhes-historico">
                                                        <?php foreach ($detalhes["depois"] as $chave => $valor): ?>
                                                            <?php
                                                                $antes = $detalhes["antes"][$chave] ?? null;
                                                                $rotulo = $rotulosDetalhes[$chave] ?? ucfirst(str_replace("_", " ", $chave));
                                                                if ($antes == $valor) {
                                                                    continue;
                                                                }
                                                            ?>
                                                            <span>
                                                                <?= htmlspecialchars($rotulo) ?>:
                                                                <b><?= htmlspecialchars((string) $antes) ?></b>
                                                                →
                                                                <b><?= htmlspecialchars(is_scalar($valor) ? (string) $valor : "") ?></b>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php elseif (!empty($detalhes)): ?>
                                                    <div class="detalhes-historico">
                                                        <?php foreach ($detalhes as $chave => $valor): ?>
                                                            <?php
                                                                if (!$chave || $valor === null || $valor === "") {
                                                                    continue;
                                                                }
                                                                $rotulo = $rotulosDetalhes[$chave] ?? ucfirst(str_replace("_", " ", $chave));
                                                            ?>
                                                            <span>
                                                                <?= htmlspecialchars($rotulo) ?>:
                                                                <b><?= htmlspecialchars(is_scalar($valor) ? (string) $valor : "") ?></b>
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
                        $cotacoesDisponiveis < $totalAtivos
                    ): ?>

                        <div class="aviso-cotacao">

                            Algumas cotações não estão disponíveis
                            no momento. Os ativos sem cotação atual
                            não foram incluídos no valor atual da
                            carteira.

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