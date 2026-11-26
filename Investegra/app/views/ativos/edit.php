<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Editar Ativo - INVESTEGRA
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

            <a
                href="carteiras.php"
                class="ativo"
            >
                Carteiras
            </a>

            <a href="simulacao.php">
                Simulações
            </a>

            <a href="analises.php">
                Análises
            </a>

            <a href="recomendacoes.php">
                Recomendações
            </a>

            <a href="historico.php">
                Histórico
            </a>


        </nav>


        <a
            href="logout.php"
            class="logout"
        >
            Sair
        </a>

    </aside>


    <main class="conteudo">


        <div class="formulario-container">


            <a
                href="ativos.php?carteira=<?= $ativo["carteira_id"] ?>"
                class="voltar"
            >
                ← Voltar para ativos
            </a>


            <h1>
                Editar ativo
            </h1>


            <p class="subtitulo">

                Carteira:
                <strong>
                    <?= htmlspecialchars(
                        $ativo["carteira_nome"]
                    ) ?>
                </strong>

            </p>


            <form
                action="atualizar_ativo.php"
                method="POST"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?= $ativo["id"] ?>"
                >


                <label for="categoria_id">
                    Categoria
                </label>

                <select
                    id="categoria_id"
                    name="categoria_id"
                    required
                >

                    <?php foreach (
                        $categorias as $categoria
                    ): ?>

                        <option
                            value="<?= $categoria["id"] ?>"
                            <?= (
                                $categoria["id"]
                                == $ativo["categoria_id"]
                            )
                                ? "selected"
                                : ""
                            ?>
                        >

                            <?= htmlspecialchars(
                                $categoria["nome"]
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <div id="campos_mercado">
                    <label for="ticker">
                        Ticker
                    </label>
                    <input
                        type="text"
                        id="ticker"
                        name="ticker"
                        value="<?= htmlspecialchars($ativo["ticker"]) ?>"
                        maxlength="20"
                        required
                    >
                </div>

                <div id="campos_renda_fixa" hidden>
                    <label for="produto_renda_fixa">Produto de renda fixa</label>
                    <select id="produto_renda_fixa" name="produto_renda_fixa">
                        <option value="">Selecione o produto</option>
                        <?php
                        $produtosRendaFixa = [
                            "CDB" => "CDB",
                            "LCI" => "LCI",
                            "LCA" => "LCA",
                            "TESOURO" => "Tesouro Direto",
                            "DEBENTURE" => "Debênture",
                            "CRI" => "CRI",
                            "CRA" => "CRA",
                            "POUPANCA" => "Poupança",
                            "OUTRO" => "Outro",
                        ];
                        foreach ($produtosRendaFixa as $codigo => $nomeProduto):
                        ?>
                            <option
                                value="<?= htmlspecialchars($codigo) ?>"
                                <?= $ativo["ticker"] === $codigo ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($nomeProduto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="renda_fixa_nome">Identificação do produto / emissor</label>
                    <input
                        type="text"
                        id="renda_fixa_nome"
                        name="renda_fixa_nome"
                        maxlength="100"
                        value="<?= htmlspecialchars($ativo["renda_fixa_nome"] ?? "") ?>"
                    >

                    <label for="renda_fixa_indexador">Indexador</label>
                    <select id="renda_fixa_indexador" name="renda_fixa_indexador">
                        <?php foreach (["Prefixado", "CDI", "IPCA+", "SELIC", "TR", "Outro"] as $indexador): ?>
                            <option
                                value="<?= htmlspecialchars($indexador) ?>"
                                <?= ($ativo["renda_fixa_indexador"] ?? "") === $indexador ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($indexador) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="renda_fixa_taxa">Taxa contratada (% a.a. ou % do indexador)</label>
                    <input
                        type="number"
                        id="renda_fixa_taxa"
                        name="renda_fixa_taxa"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars((string) ($ativo["renda_fixa_taxa"] ?? "")) ?>"
                    >

                    <label for="renda_fixa_vencimento">Data de vencimento (opcional)</label>
                    <input
                        type="date"
                        id="renda_fixa_vencimento"
                        name="renda_fixa_vencimento"
                        value="<?= htmlspecialchars($ativo["renda_fixa_vencimento"] ?? "") ?>"
                    >
                </div>


                <div id="campo_quantidade">
                    <label for="quantidade">
                        Quantidade
                    </label>

                    <input
                        type="number"
                        id="quantidade"
                        name="quantidade"
                        step="1"
                        min="1"
                        value="<?= htmlspecialchars(number_format((float) $ativo["quantidade"], 8, ".", "")) ?>"
                        required
                    >
                </div>


                <label for="preco_medio" id="rotulo_preco_medio">
                    Preço de compra
                </label>

                <input
                    type="number"
                    id="preco_medio"
                    name="preco_medio"
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars(
                        $ativo["preco_medio"]
                    ) ?>"
                    required
                >


                <label for="data_compra" id="rotulo_data_compra">
                    Data de compra
                </label>

                <input
                    type="date"
                    id="data_compra"
                    name="data_compra"
                    value="<?= htmlspecialchars(
                        $ativo["data_compra"] ?? ""
                    ) ?>"
                >


                <label for="observacao">
                    Observação
                </label>

                <textarea
                    id="observacao"
                    name="observacao"
                    rows="5"
                ><?= htmlspecialchars(
                    $ativo["observacao"] ?? ""
                ) ?></textarea>


                <div class="botoes-formulario">


                    <a
                        href="ativos.php?carteira=<?= $ativo["carteira_id"] ?>"
                        class="botao-secundario"
                    >
                        Cancelar
                    </a>


                    <button
                        type="submit"
                        class="botao"
                    >
                        Salvar alterações
                    </button>


                </div>


            </form>


        </div>


    </main>

</div>

<script>
const categoriaAtivo = document.getElementById("categoria_id");
const camposMercadoAtivo = document.getElementById("campos_mercado");
const camposRendaFixaAtivo = document.getElementById("campos_renda_fixa");
const campoQuantidadeAtivo = document.getElementById("campo_quantidade");
const quantidadeAtivo = document.getElementById("quantidade");
const tickerAtivo = document.getElementById("ticker");
const produtoRendaFixaAtivo = document.getElementById("produto_renda_fixa");
const nomeRendaFixaAtivo = document.getElementById("renda_fixa_nome");
const indexadorRendaFixaAtivo = document.getElementById("renda_fixa_indexador");
const taxaRendaFixaAtivo = document.getElementById("renda_fixa_taxa");
const rotuloPrecoAtivo = document.getElementById("rotulo_preco_medio");
const rotuloDataAtivo = document.getElementById("rotulo_data_compra");

function atualizarFormularioAtivo() {
    const categoria =
        categoriaAtivo.selectedOptions[0].textContent.trim();
    const rendaFixa = categoria === "Renda Fixa";
    const cripto = categoria === "Criptomoedas";

    camposMercadoAtivo.hidden = rendaFixa;
    camposRendaFixaAtivo.hidden = !rendaFixa;
    campoQuantidadeAtivo.hidden = rendaFixa;

    tickerAtivo.disabled = rendaFixa;
    produtoRendaFixaAtivo.disabled = !rendaFixa;
    nomeRendaFixaAtivo.disabled = !rendaFixa;
    indexadorRendaFixaAtivo.disabled = !rendaFixa;
    taxaRendaFixaAtivo.disabled = !rendaFixa;
    document.getElementById("renda_fixa_vencimento").disabled = !rendaFixa;
    quantidadeAtivo.disabled = rendaFixa;

    produtoRendaFixaAtivo.required = rendaFixa;
    nomeRendaFixaAtivo.required = rendaFixa;
    indexadorRendaFixaAtivo.required = rendaFixa;
    taxaRendaFixaAtivo.required = rendaFixa;
    quantidadeAtivo.required = !rendaFixa;
    quantidadeAtivo.step = cripto ? "0.00000001" : "1";
    quantidadeAtivo.min = cripto ? "0.00000001" : "1";

    rotuloPrecoAtivo.textContent = rendaFixa
        ? "Valor aplicado"
        : "Preço de compra";
    rotuloDataAtivo.textContent = rendaFixa
        ? "Data da aplicação"
        : "Data de compra";
}

categoriaAtivo.addEventListener("change", atualizarFormularioAtivo);
categoriaAtivo.addEventListener("change", function () {
    tickerAtivo.value = "";
    produtoRendaFixaAtivo.value = "";
});
atualizarFormularioAtivo();
</script>

</body>

</html>