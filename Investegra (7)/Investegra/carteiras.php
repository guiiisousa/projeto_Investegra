<?php

session_start();

require_once "conexao.php";
require_once "api/brapi.php";


/*
|--------------------------------------------------------------------------
| Verifica login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["id"];


/*
|--------------------------------------------------------------------------
| Busca as carteiras do usuário
|--------------------------------------------------------------------------
*/

$sqlCarteiras = "
    SELECT
        c.id,
        c.nome,
        c.descricao,
        c.criado_em
    FROM carteiras c
    WHERE c.usuario_id = ?
    ORDER BY c.criado_em DESC
";

$stmtCarteiras = $conexao->prepare($sqlCarteiras);

if (!$stmtCarteiras) {
    die("Erro ao preparar consulta das carteiras.");
}

$stmtCarteiras->bind_param(
    "i",
    $id_usuario
);

$stmtCarteiras->execute();

$resultadoCarteiras = $stmtCarteiras->get_result();

$carteiras = [];

while ($carteira = $resultadoCarteiras->fetch_assoc()) {

    $carteira["ativos"] = [];

    $carteira["total_investido"] = 0;

    $carteira["total_atual"] = 0;

    $carteira["resultado"] = 0;

    $carteira["cotacoes_disponiveis"] = 0;

    $carteira["total_ativos"] = 0;

    $carteiras[$carteira["id"]] = $carteira;
}

$stmtCarteiras->close();


/*
|--------------------------------------------------------------------------
| Se o usuário não tiver carteiras
|--------------------------------------------------------------------------
*/

if (!empty($carteiras)) {

    /*
    |--------------------------------------------------------------------------
    | Busca todos os ativos das carteiras do usuário
    |--------------------------------------------------------------------------
    */

    $sqlAtivos = "
        SELECT
            a.id,
            a.carteira_id,
            a.ticker,
            a.quantidade,
            a.preco_medio,
            a.data_compra,
            c.nome AS categoria
        FROM ativos a
        INNER JOIN carteiras ca
            ON a.carteira_id = ca.id
        LEFT JOIN categorias c
            ON a.categoria_id = c.id
        WHERE ca.usuario_id = ?
        ORDER BY a.data_compra DESC, a.id DESC
    ";

    $stmtAtivos = $conexao->prepare($sqlAtivos);

    if (!$stmtAtivos) {
        die("Erro ao preparar consulta dos ativos.");
    }

    $stmtAtivos->bind_param(
        "i",
        $id_usuario
    );

    $stmtAtivos->execute();

    $resultadoAtivos = $stmtAtivos->get_result();

    $ativos = [];

    $tickers = [];

    while ($ativo = $resultadoAtivos->fetch_assoc()) {

        $ativo["ticker"] = strtoupper(
            trim($ativo["ticker"])
        );

        $ativo["quantidade"] =
            (float) $ativo["quantidade"];

        $ativo["preco_medio"] =
            (float) $ativo["preco_medio"];

        /*
        |--------------------------------------------------------------------------
        | Valor investido
        |--------------------------------------------------------------------------
        */

        $ativo["valor_investido"] =
            $ativo["quantidade"]
            * $ativo["preco_medio"];


        /*
        |--------------------------------------------------------------------------
        | Guarda o ativo
        |--------------------------------------------------------------------------
        */

        $ativos[] = $ativo;


        /*
        |--------------------------------------------------------------------------
        | Guarda ticker para consulta à brapi
        |--------------------------------------------------------------------------
        */

        if (!empty($ativo["ticker"])) {
            $tickers[] = $ativo["ticker"];
        }
    }

    $stmtAtivos->close();


    /*
    |--------------------------------------------------------------------------
    | Busca todas as cotações de uma vez
    |--------------------------------------------------------------------------
    */

    $cotacoes = obterCotacoes($tickers);


    /*
    |--------------------------------------------------------------------------
    | Distribui os ativos pelas carteiras
    |--------------------------------------------------------------------------
    */

    foreach ($ativos as $ativo) {

        $carteiraId = (int) $ativo["carteira_id"];

        if (!isset($carteiras[$carteiraId])) {
            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | Busca cotação
        |--------------------------------------------------------------------------
        */

        $ticker = $ativo["ticker"];

        $cotacao =
            $cotacoes[$ticker]
            ?? null;


        /*
        |--------------------------------------------------------------------------
        | Valor investido
        |--------------------------------------------------------------------------
        */

        $valorInvestido =
            $ativo["valor_investido"];


        /*
        |--------------------------------------------------------------------------
        | Inicializa dados
        |--------------------------------------------------------------------------
        */

        $ativo["cotacao"] = $cotacao;

        $ativo["preco_atual"] = null;

        $ativo["valor_atual"] = null;

        $ativo["resultado"] = null;


        /*
        |--------------------------------------------------------------------------
        | Se houver cotação atual
        |--------------------------------------------------------------------------
        */

        if (
            $cotacao !== null &&
            isset($cotacao["preco_atual"]) &&
            $cotacao["preco_atual"] !== null
        ) {

            $precoAtual =
                (float) $cotacao["preco_atual"];


            $valorAtual =
                $ativo["quantidade"]
                * $precoAtual;


            $resultado =
                $valorAtual
                - $valorInvestido;


            $ativo["preco_atual"] =
                $precoAtual;


            $ativo["valor_atual"] =
                $valorAtual;


            $ativo["resultado"] =
                $resultado;


            /*
            |--------------------------------------------------------------------------
            | Atualiza totais da carteira
            |--------------------------------------------------------------------------
            */

            $carteiras[$carteiraId]["total_atual"]
                += $valorAtual;


            $carteiras[$carteiraId]["resultado"]
                += $resultado;


            $carteiras[$carteiraId]["cotacoes_disponiveis"]++;
        }


        /*
        |--------------------------------------------------------------------------
        | Soma valor investido
        |--------------------------------------------------------------------------
        */

        $carteiras[$carteiraId]["total_investido"]
            += $valorInvestido;


        /*
        |--------------------------------------------------------------------------
        | Conta ativos
        |--------------------------------------------------------------------------
        */

        $carteiras[$carteiraId]["total_ativos"]++;


        /*
        |--------------------------------------------------------------------------
        | Guarda ativo dentro da carteira
        |--------------------------------------------------------------------------
        */

        $carteiras[$carteiraId]["ativos"][] =
            $ativo;
    }
}


/*
|--------------------------------------------------------------------------
| Funções auxiliares
|--------------------------------------------------------------------------
*/

function formatarMoeda($valor)
{
    return "R$ " . number_format(
        $valor,
        2,
        ",",
        "."
    );
}


function formatarPercentual($valor)
{
    return number_format(
        $valor,
        2,
        ",",
        "."
    ) . "%";
}


function classeResultado($valor)
{
    if ($valor === null) {
        return "";
    }

    return $valor >= 0
        ? "resultado-positivo"
        : "resultado-negativo";
}


function sinalResultado($valor)
{
    if ($valor === null) {
        return "";
    }

    return $valor >= 0
        ? "+"
        : "";
}

?>

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
        href="css/carteiras.css"
    >

</head>

<body>


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


        <a
            href="nova_carteira.php"
            class="btn-principal"
        >
            + Nova carteira
        </a>

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
                                                    $ativo["quantidade"],
                                                    4,
                                                    ",",
                                                    "."
                                                ) ?>

                                            </strong>

                                        </div>


                                        <div class="ativo-dado">

                                            <span>
                                                Preço médio
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