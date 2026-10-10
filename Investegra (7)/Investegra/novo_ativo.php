<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];


$id_carteira = filter_input(
    INPUT_GET,
    "carteira",
    FILTER_VALIDATE_INT
);


if (!$id_carteira) {

    header("Location: carteiras.php?erro=1");
    exit;

}


/*
 * Verificar se a carteira pertence ao usuário.
 */

$sql = "
    SELECT
        id,
        nome
    FROM carteiras
    WHERE id = ?
    AND usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_carteira,
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows !== 1) {

    $stmt->close();

    header("Location: carteiras.php?erro=1");
    exit;

}


$carteira = $resultado->fetch_assoc();

$stmt->close();


/*
 * Buscar categorias.
 */

$sql = "
    SELECT
        id,
        nome
    FROM categorias
    ORDER BY nome
";

$resultado = $conexao->query($sql);

$categorias = $resultado->fetch_all(
    MYSQLI_ASSOC
);

?>

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
        href="css/ativos.css"
    >

</head>


<body>


<div class="layout">


    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <div class="logo">
            INVESTEGRA
        </div>


        <nav>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="#">
                Perfil
            </a>

            <a
                href="carteiras.php"
                class="ativo"
            >
                Carteiras
            </a>

            <a href="#">
                Simulações
            </a>

            <a href="#">
                Análises
            </a>

            <a href="#">
                Recomendações
            </a>

            <a href="#">
                Histórico
            </a>

            <a href="#">
                Indicadores
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


                <!-- =========================
                     BUSCA DO ATIVO
                ========================== -->

                <label for="busca_ativo">
                    Ativo
                </label>


                <div class="campo-ativo">


                    <input
                        type="text"
                        id="busca_ativo"
                        placeholder="Digite o ticker ou nome da empresa..."
                        autocomplete="off"
                        maxlength="50"
                        required
                    >


                    <!--
                        Este campo é o que realmente
                        será enviado ao PHP.
                    -->

                    <input
                        type="hidden"
                        name="ticker"
                        id="ticker"
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


                <!-- =========================
                     CATEGORIA
                ========================== -->

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


                <!-- =========================
                     QUANTIDADE
                ========================== -->

                <label for="quantidade">
                    Quantidade
                </label>


                <input
                    type="number"
                    id="quantidade"
                    name="quantidade"
                    step="0.0001"
                    min="0.0001"
                    placeholder="Ex.: 10"
                    required
                >


                <!-- =========================
                     PREÇO MÉDIO
                ========================== -->

                <label for="preco_medio">
                    Preço médio
                </label>


                <input
                    type="number"
                    id="preco_medio"
                    name="preco_medio"
                    step="0.01"
                    min="0"
                    placeholder="Ex.: 35.50"
                    required
                >


                <!-- =========================
                     DATA
                ========================== -->

                <label for="data_compra">
                    Data de compra
                </label>


                <input
                    type="date"
                    id="data_compra"
                    name="data_compra"
                >


                <!-- =========================
                     OBSERVAÇÃO
                ========================== -->

                <label for="observacao">
                    Observação
                </label>


                <textarea
                    id="observacao"
                    name="observacao"
                    rows="5"
                    placeholder="Observações sobre este ativo..."
                ></textarea>


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


let temporizador = null;


/*
 * Quando o usuário digita.
 */

campoBusca.addEventListener(
    "input",
    function () {


        const termo =
            this.value.trim();


        /*
         * Se o usuário alterar o texto
         * depois de selecionar um ativo,
         * precisamos apagar o ticker escolhido.
         */

        campoTicker.value = "";

        ativoSelecionado.textContent = "";

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
                            encodeURIComponent(termo)
                        );


                    const dados =
                        await resposta.json();


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
                                function () {


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


                                    /*
                                     * Mensagem de confirmação.
                                     */

                                    ativoSelecionado.textContent =
                                        "Ativo selecionado: " +
                                        ativo.ticker;


                                    ativoSelecionado.classList.add(
                                        "mostrar"
                                    );


                                    /*
                                     * Fechar sugestões.
                                     */

                                    resultados.innerHTML = "";

                                    resultados.classList.remove(
                                        "visivel"
                                    );

                                }
                            );


                            resultados.appendChild(
                                item
                            );

                        }
                    );


                } catch (erro) {


                    resultados.innerHTML = `

                        <div class="resultado-erro">
                            Não foi possível buscar os ativos.
                        </div>

                    `;

                }


            },
            350
        );

    }
);


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


            if (
                campoTicker.value.trim() === ""
            ) {

                evento.preventDefault();


                alert(
                    "Selecione um ativo na lista de sugestões antes de continuar."
                );


                campoBusca.focus();

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