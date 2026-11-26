<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Novo Ativo - INVESTEGRA</title>

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


    <!-- =========================
         SIDEBAR
    ========================== -->

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


    <!-- =========================
         CONTEÚDO
    ========================== -->

    <main class="conteudo">


        <div class="formulario-container">


            <a
                href="ativos.php?carteira=<?= $id_carteira ?>"
                class="voltar"
            >
                ← Voltar para ativos
            </a>


            <h1>
                Novo ativo
            </h1>


            <p class="subtitulo">

                Carteira:

                <strong>
                    <?= htmlspecialchars(
                        $carteira["nome"]
                    ) ?>
                </strong>

            </p>


            <form
                action="processando_ativo.php"
                method="POST"
                id="formularioAtivo"
            >


                <input
                    type="hidden"
                    name="carteira_id"
                    value="<?= $id_carteira ?>"
                >


                <label for="categoria_id">
                    Categoria
                </label>


                <select
                    id="categoria_id"
                    name="categoria_id"
                    required
                >

                    <option value="">
                        Selecione uma categoria
                    </option>


                    <?php foreach (
                        $categorias as $categoria
                    ): ?>

                        <option
                            value="<?= $categoria["id"] ?>"
                        >

                            <?= htmlspecialchars(
                                $categoria["nome"]
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <input type="hidden" name="ticker" id="ticker">

                <div id="campos_ativos_mercado" hidden>
                    <label for="busca_ativo">Ativo</label>

                    <div class="campo-ativo">
                        <input
                            type="text"
                            id="busca_ativo"
                            placeholder="Digite o ticker ou nome do ativo..."
                            autocomplete="off"
                            maxlength="50"
                            disabled
                        >

                        <div
                            id="resultados_ativos"
                            class="resultados-ativos"
                        ></div>
                    </div>

                    <p
                        id="ativo_selecionado"
                        class="ativo-selecionado"
                    ></p>

                    <label for="quantidade">
                        Quantidade
                    </label>

                    <input
                        type="number"
                        id="quantidade"
                        name="quantidade"
                        step="1"
                        min="1"
                        placeholder="Ex.: 10"
                        disabled
                    >
                </div>

                <div id="campos_ativo_cripto" hidden>
                    <label for="busca_cripto">Criptomoeda</label>
                    <input
                        type="text"
                        id="busca_cripto"
                        placeholder="Digite o ticker, por exemplo BTC"
                        maxlength="12"
                        autocomplete="off"
                        disabled
                    >
                    <p id="cripto_selecionada" class="ativo-selecionado"></p>

                    <label for="quantidade_cripto">Quantidade</label>
                    <input
                        type="number"
                        id="quantidade_cripto"
                        name="quantidade"
                        min="0.00000001"
                        step="0.00000001"
                        placeholder="Ex.: 0,01"
                        disabled
                    >
                </div>

                <div id="campos_renda_fixa" hidden>
                    <label for="produto_renda_fixa">Produto de renda fixa</label>
                    <select id="produto_renda_fixa" name="produto_renda_fixa" disabled>
                        <option value="">Selecione o produto</option>
                        <option value="CDB">CDB</option>
                        <option value="LCI">LCI</option>
                        <option value="LCA">LCA</option>
                        <option value="TESOURO">Tesouro Direto</option>
                        <option value="DEBENTURE">Debênture</option>
                        <option value="CRI">CRI</option>
                        <option value="CRA">CRA</option>
                        <option value="POUPANCA">Poupança</option>
                        <option value="OUTRO">Outro</option>
                    </select>

                    <label for="renda_fixa_nome">Identificação do produto / emissor</label>
                    <input
                        type="text"
                        id="renda_fixa_nome"
                        name="renda_fixa_nome"
                        maxlength="100"
                        placeholder="Ex.: CDB Banco X ou Tesouro Selic 2029"
                        disabled
                    >

                    <label for="renda_fixa_indexador">Indexador</label>
                    <select
                        id="renda_fixa_indexador"
                        name="renda_fixa_indexador"
                        disabled
                    >
                        <option value="">Selecione o indexador</option>
                        <option value="Prefixado">Prefixado</option>
                        <option value="CDI">CDI</option>
                        <option value="IPCA+">IPCA+</option>
                        <option value="SELIC">Selic</option>
                        <option value="TR">TR</option>
                        <option value="Outro">Outro</option>
                    </select>

                    <label for="renda_fixa_taxa">Taxa contratada (% a.a. ou % do indexador)</label>
                    <input
                        type="number"
                        id="renda_fixa_taxa"
                        name="renda_fixa_taxa"
                        min="0"
                        step="0.01"
                        placeholder="Ex.: 110 para 110% do CDI"
                        disabled
                    >

                    <label for="renda_fixa_vencimento">Data de vencimento (opcional)</label>
                    <input
                        type="date"
                        id="renda_fixa_vencimento"
                        name="renda_fixa_vencimento"
                        disabled
                    >
                </div>

                <div id="campos_comuns" hidden>
                    <label for="preco_medio" id="rotulo_preco_medio">
                        Preço de compra
                    </label>
                    <input
                        type="number"
                        id="preco_medio"
                        name="preco_medio"
                        step="0.01"
                        min="0.01"
                        placeholder="Cotação atual será preenchida"
                        disabled
                    >

                    <label for="data_compra" id="rotulo_data_compra">
                        Data de compra
                    </label>
                    <input
                        type="date"
                        id="data_compra"
                        name="data_compra"
                        disabled
                    >

                    <label for="observacao">
                        Observação
                    </label>
                    <textarea
                        id="observacao"
                        name="observacao"
                        rows="5"
                        placeholder="Observações sobre este ativo..."
                        disabled
                    ></textarea>
                </div>


                <!-- =========================
                     BOTÕES
                ========================== -->

                <div class="botoes-formulario">


                    <a
                        href="ativos.php?carteira=<?= $id_carteira ?>"
                        class="botao-secundario"
                    >
                        Cancelar
                    </a>


                    <button
                        type="submit"
                        class="botao"
                    >
                        Adicionar ativo
                    </button>


                </div>


            </form>


        </div>


    </main>


</div>


<script>

/*
 * ==========================================
 * BUSCA INTELIGENTE DE ATIVOS
 * ==========================================
 */


const campoBusca =
    document.getElementById(
        "busca_ativo"
    );


const campoTicker =
    document.getElementById(
        "ticker"
    );


const resultados =
    document.getElementById(
        "resultados_ativos"
    );


const ativoSelecionado =
    document.getElementById(
        "ativo_selecionado"
    );

const campoPrecoMedio =
    document.getElementById(
        "preco_medio"
    );

const campoDataCompra =
    document.getElementById(
        "data_compra"
    );

const campoCategoria =
    document.getElementById(
        "categoria_id"
    );

const camposMercado =
    document.getElementById("campos_ativos_mercado");
const camposCripto =
    document.getElementById("campos_ativo_cripto");
const camposRendaFixa =
    document.getElementById("campos_renda_fixa");
const camposComuns =
    document.getElementById("campos_comuns");
const buscaCripto =
    document.getElementById("busca_cripto");
const criptoSelecionada =
    document.getElementById("cripto_selecionada");
const quantidadeMercado =
    document.getElementById("quantidade");
const quantidadeCripto =
    document.getElementById("quantidade_cripto");
const produtoRendaFixa =
    document.getElementById("produto_renda_fixa");
const nomeRendaFixa =
    document.getElementById("renda_fixa_nome");
const indexadorRendaFixa =
    document.getElementById("renda_fixa_indexador");
const taxaRendaFixa =
    document.getElementById("renda_fixa_taxa");
const rotuloPreco =
    document.getElementById("rotulo_preco_medio");
const rotuloData =
    document.getElementById("rotulo_data_compra");


let temporizador = null;
let temporizadorCripto = null;
let idConsultaCotacao = 0;
let idBusca = 0;

function alternarGrupo(grupo, visivel) {
    grupo.hidden = !visivel;
    grupo.querySelectorAll("input, select, textarea").forEach(function (campo) {
        campo.disabled = !visivel;
    });
}

function atualizarCamposCategoria() {
    const categoria =
        campoCategoria.selectedOptions[0].textContent.trim();
    const ehMercado =
        ["Ações", "FIIs", "ETFs"].includes(categoria);
    const ehCripto =
        categoria === "Criptomoedas";
    const ehRendaFixa =
        categoria === "Renda Fixa";
    const categoriaSelecionada =
        ehMercado || ehCripto || ehRendaFixa;

    alternarGrupo(camposMercado, ehMercado);
    alternarGrupo(camposCripto, ehCripto);
    alternarGrupo(camposRendaFixa, ehRendaFixa);
    alternarGrupo(camposComuns, categoriaSelecionada);

    document.getElementById("busca_ativo").required = ehMercado;
    quantidadeMercado.required = ehMercado;
    buscaCripto.required = ehCripto;
    quantidadeCripto.required = ehCripto;
    produtoRendaFixa.required = ehRendaFixa;
    nomeRendaFixa.required = ehRendaFixa;
    indexadorRendaFixa.required = ehRendaFixa;
    taxaRendaFixa.required = ehRendaFixa;
    campoPrecoMedio.required = categoriaSelecionada;

    rotuloPreco.textContent = ehRendaFixa
        ? "Valor aplicado"
        : "Preço de compra";
    rotuloData.textContent = ehRendaFixa
        ? "Data da aplicação"
        : "Data de compra";
    campoPrecoMedio.placeholder = ehRendaFixa
        ? "Informe o valor aplicado"
        : "Cotação atual será preenchida";
    quantidadeCripto.disabled = !ehCripto;
    quantidadeCripto.step = "0.00000001";
    quantidadeCripto.min = "0.00000001";
    quantidadeMercado.step = "1";
    quantidadeMercado.min = "1";
}

function formatarVariacao(variacao) {
    if (variacao === null || variacao === undefined || variacao === "") {
        return "Variação do dia indisponível";
    }

    const valor = Number(variacao);
    if (!Number.isFinite(valor)) {
        return "Variação do dia indisponível";
    }

    return "Variação do dia: "
        + (valor > 0 ? "+" : "")
        + valor.toLocaleString("pt-BR", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
        + "%";
}

campoCategoria.addEventListener("change", function () {
    idConsultaCotacao += 1;
    idBusca += 1;
    clearTimeout(temporizador);
    clearTimeout(temporizadorCripto);
    campoTicker.value = "";
    campoPrecoMedio.value = "";
    campoBusca.value = "";
    buscaCripto.value = "";
    quantidadeMercado.value = "";
    quantidadeCripto.value = "";
    ativoSelecionado.textContent = "";
    criptoSelecionada.textContent = "";
    ativoSelecionado.classList.remove("mostrar", "erro");
    criptoSelecionada.classList.remove("mostrar", "erro");
    resultados.replaceChildren();
    resultados.classList.remove("visivel");
    produtoRendaFixa.value = "";
    atualizarCamposCategoria();
});

produtoRendaFixa.addEventListener("change", function () {
    campoTicker.value = produtoRendaFixa.value;
});

atualizarCamposCategoria();


/*
 * Quando o usuário digita.
 */

campoBusca.addEventListener(
    "input",
    function () {


        const termo =
            this.value.trim();
        const buscaAtual =
            ++idBusca;


        /*
         * Se o usuário alterar o texto
         * depois de selecionar um ativo,
         * precisamos apagar o ticker escolhido.
         */

        campoTicker.value = "";

        idConsultaCotacao += 1;
        campoPrecoMedio.value = "";
        campoPrecoMedio.placeholder = "Digite o preço de compra";

        ativoSelecionado.textContent = "";
        ativoSelecionado.classList.remove("erro");
        resultados.replaceChildren();
        resultados.classList.remove("visivel");

        clearTimeout(
            temporizador
        );


        /*
         * Menos de 2 caracteres:
         * não fazemos consulta.
         */

        if (termo.length < 2) {

            resultados.innerHTML = "";

            resultados.classList.remove(
                "visivel"
            );

            return;
        }


        /*
         * Aguarda 350 ms antes de consultar.
         */

        temporizador = setTimeout(
            async function () {


                resultados.innerHTML = `
                    <div class="carregando-ativos">
                        Buscando ativos...
                    </div>
                `;

                resultados.classList.add(
                    "visivel"
                );


                try {


                    const resposta =
                        await fetch(
                            "api/buscar_ativos.php?termo=" +
                            encodeURIComponent(termo) +
                            "&categoria=" +
                            encodeURIComponent(
                                campoCategoria.selectedOptions[0]
                                    .textContent.trim()
                            )
                        );


                    const dados =
                        await resposta.json();

                    if (buscaAtual !== idBusca) {
                        return;
                    }

                    resultados.innerHTML = "";


                    if (
                        !dados.sucesso ||
                        !dados.resultados ||
                        dados.resultados.length === 0
                    ) {

                        resultados.innerHTML = `
                            <div class="resultado-vazio">
                                Nenhum ativo encontrado.
                            </div>
                        `;

                        return;
                    }


                    /*
                     * Criar cada resultado.
                     */

                    dados.resultados.forEach(
                        function (ativo) {


                            const item =
                                document.createElement(
                                    "button"
                                );


                            item.type =
                                "button";


                            item.className =
                                "resultado-ativo";


                            item.innerHTML = `

                                <strong>
                                    ${escapeHTML(
                                        ativo.ticker
                                    )}
                                </strong>

                                <span>
                                    ${escapeHTML(
                                        ativo.nome
                                    )}
                                </span>

                            `;


                            /*
                             * Ao clicar no resultado.
                             */

                            item.addEventListener(
                                "click",
                                async function () {
                                    const consultaAtual =
                                        ++idConsultaCotacao;
                                    idBusca += 1;


                                    /*
                                     * Texto visível.
                                     */

                                    campoBusca.value =
                                        ativo.ticker +
                                        " — " +
                                        ativo.nome;


                                    /*
                                     * Valor enviado ao PHP.
                                     */

                                    campoTicker.value =
                                        ativo.ticker;

                                    if (
                                        !campoDataCompra.value
                                    ) {
                                        const hoje =
                                            new Date();
                                        const ano =
                                            hoje.getFullYear();
                                        const mes =
                                            String(
                                                hoje.getMonth() + 1
                                            ).padStart(2, "0");
                                        const dia =
                                            String(
                                                hoje.getDate()
                                            ).padStart(2, "0");

                                        campoDataCompra.value =
                                            ano + "-" + mes + "-" + dia;
                                    }


                                    /*
                                     * Consulta a cotação atual
                                     * e usa o valor no preço de compra.
                                     */

                                    campoPrecoMedio.value =
                                        "";
                                    campoPrecoMedio.placeholder =
                                        "Buscando cotação atual...";
                                    ativoSelecionado.textContent =
                                        "Ativo selecionado: " +
                                        ativo.ticker +
                                        " — Buscando cotação atual...";
                                    ativoSelecionado.classList.add(
                                        "mostrar"
                                    );
                                    ativoSelecionado.classList.remove(
                                        "erro"
                                    );

                                    resultados.innerHTML = "";

                                    resultados.classList.remove(
                                        "visivel"
                                    );

                                    try {
                                        const categoria =
                                            campoCategoria.selectedOptions[0]
                                                .textContent.trim();
                                        const endpointCotacao =
                                            categoria === "FIIs"
                                                ? "api/cotacao_fii.php?ticker="
                                                : categoria === "ETFs"
                                                    ? "api/cotacao_etf.php?ticker="
                                                : "api/cotacao.php?ticker=";
                                        const cotacaoResposta =
                                            await fetch(
                                                endpointCotacao +
                                                encodeURIComponent(
                                                    ativo.ticker
                                                )
                                            );
                                        const cotacao =
                                            await cotacaoResposta.json();

                                        if (
                                            consultaAtual
                                            !== idConsultaCotacao
                                        ) {
                                            return;
                                        }

                                        if (
                                            !cotacaoResposta.ok
                                            || !cotacao.sucesso
                                            || !cotacao.ativo
                                        ) {
                                            throw new Error(
                                                cotacao.mensagem
                                                || "Não foi possível consultar a cotação atual."
                                            );
                                        }

                                        const precoAtual =
                                            Number(
                                                cotacao.ativo.preco_atual
                                            );

                                        if (
                                            !Number.isFinite(precoAtual)
                                            || precoAtual <= 0
                                        ) {
                                            throw new Error(
                                                "A Brapi retornou uma cotação inválida para esse ativo."
                                            );
                                        }

                                        campoPrecoMedio.value =
                                            precoAtual.toFixed(2);
                                        campoPrecoMedio.placeholder =
                                            "Cotação atual preenchida";

                                        ativoSelecionado.textContent =
                                            "Ativo selecionado: " +
                                            ativo.ticker +
                                            " — Cotação atual: R$ " +
                                            precoAtual.toLocaleString(
                                                "pt-BR",
                                                {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2,
                                                }
                                            ) +
                                            " — " +
                                            formatarVariacao(
                                                cotacao.ativo.variacao_percentual
                                            );
                                    } catch (erro) {
                                        if (
                                            consultaAtual
                                            !== idConsultaCotacao
                                        ) {
                                            return;
                                        }

                                        console.error(
                                            "Erro ao consultar cotação do ativo.",
                                            erro
                                        );
                                        campoPrecoMedio.placeholder =
                                            "Digite o preço de compra";
                                        ativoSelecionado.textContent =
                                            "Ativo selecionado: " +
                                            ativo.ticker +
                                            " — " +
                                            (
                                                erro instanceof Error
                                                    ? erro.message
                                                    : "Informe o preço de compra manualmente."
                                            );
                                        ativoSelecionado.classList.add(
                                            "erro"
                                        );
                                    }

                                }
                            );


                            resultados.appendChild(
                                item
                            );

                        }
                    );


                } catch (erro) {

                    if (buscaAtual === idBusca) {
                        resultados.innerHTML = `
                            <div class="resultado-erro">
                                Não foi possível buscar os ativos.
                            </div>
                        `;
                    }

                }


            },
            350
        );

    }
);

buscaCripto.addEventListener("input", function () {
    const ticker = this.value.trim().toUpperCase();
    const consultaAtual = ++idConsultaCotacao;
    campoTicker.value = /^[A-Z0-9]{2,12}$/.test(ticker) ? ticker : "";
    campoPrecoMedio.value = "";
    campoPrecoMedio.placeholder = "Buscando cotação atual...";
    criptoSelecionada.textContent = "";
    criptoSelecionada.classList.remove("erro");
    clearTimeout(temporizadorCripto);

    if (!campoTicker.value) {
        campoPrecoMedio.placeholder = "Cotação atual será preenchida";
        criptoSelecionada.classList.remove("mostrar");
        return;
    }

    criptoSelecionada.textContent =
        "Buscando cotação de " + ticker + "...";
    criptoSelecionada.classList.add("mostrar");

    temporizadorCripto = setTimeout(async function () {
        try {
            const resposta = await fetch(
                "api/cotacao_cripto.php?ticker=" + encodeURIComponent(ticker)
            );
            const dados = await resposta.json();

            if (consultaAtual !== idConsultaCotacao) {
                return;
            }
            if (!resposta.ok || !dados.sucesso || !dados.ativo) {
                throw new Error(
                    dados.mensagem
                    || "Não foi possível consultar a cotação desta criptomoeda."
                );
            }

            const precoAtual = Number(dados.ativo.preco_atual);
            if (!Number.isFinite(precoAtual) || precoAtual <= 0) {
                throw new Error("A Brapi retornou uma cotação inválida.");
            }

            campoPrecoMedio.value = precoAtual.toFixed(2);
            campoPrecoMedio.placeholder = "Cotação atual preenchida";
            if (!campoDataCompra.value) {
                const hoje = new Date();
                campoDataCompra.value =
                    hoje.getFullYear()
                    + "-"
                    + String(hoje.getMonth() + 1).padStart(2, "0")
                    + "-"
                    + String(hoje.getDate()).padStart(2, "0");
            }
            criptoSelecionada.textContent =
                (dados.ativo.nome || ticker)
                + " — Cotação atual: R$ "
                + precoAtual.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                })
                + " — "
                + formatarVariacao(dados.ativo.variacao_percentual);
        } catch (erro) {
            if (consultaAtual !== idConsultaCotacao) {
                return;
            }

            console.error("Erro ao consultar cotação da criptomoeda.", erro);
            campoPrecoMedio.placeholder = "Informe o preço de compra manualmente";
            criptoSelecionada.textContent =
                erro instanceof Error
                    ? erro.message
                    : "Não foi possível consultar a cotação desta criptomoeda.";
            criptoSelecionada.classList.add("erro");
        }
    }, 350);
});

/*
 * ==========================================
 * IMPEDIR ENVIO SEM SELEÇÃO
 * ==========================================
 */


document
    .getElementById("formularioAtivo")
    .addEventListener(
        "submit",
        function (evento) {
            if (campoTicker.value.trim() === "") {
                evento.preventDefault();
                const categoria =
                    campoCategoria.selectedOptions[0].textContent.trim();
                if (categoria === "Renda Fixa") {
                    alert("Selecione o produto de renda fixa.");
                    produtoRendaFixa.focus();
                } else if (categoria === "Criptomoedas") {
                    alert("Informe o ticker da criptomoeda.");
                    buscaCripto.focus();
                } else {
                    alert("Selecione um ativo nos resultados da busca.");
                    campoBusca.focus();
                }
            }

        }
    );


/*
 * ==========================================
 * ESC FECHA AS SUGESTÕES
 * ==========================================
 */

document.addEventListener(
    "keydown",
    function (evento) {

        if (evento.key === "Escape") {

            resultados.innerHTML = "";

            resultados.classList.remove(
                "visivel"
            );

        }

    }
);


/*
 * ==========================================
 * ESCAPAR HTML
 * ==========================================
 *
 * Evita inserir diretamente conteúdo
 * retornado pela API como HTML.
 */

function escapeHTML(texto) {

    const div =
        document.createElement("div");

    div.textContent =
        texto ?? "";

    return div.innerHTML;
}

</script>


</body>

</html>