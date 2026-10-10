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
| Verifica carteira
|--------------------------------------------------------------------------
*/

$carteira_id = filter_input(
    INPUT_GET,
    "carteira",
    FILTER_VALIDATE_INT
);

if (!$carteira_id) {
    header("Location: carteiras.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Busca a carteira
|--------------------------------------------------------------------------
|
| O usuário só pode acessar carteiras que pertencem a ele.
|
*/

$sqlCarteira = "
    SELECT id, nome, descricao
    FROM carteiras
    WHERE id = ?
      AND usuario_id = ?
    LIMIT 1
";

$stmtCarteira = $conexao->prepare($sqlCarteira);

if (!$stmtCarteira) {
    die("Erro ao preparar consulta da carteira.");
}

$stmtCarteira->bind_param(
    "ii",
    $carteira_id,
    $id_usuario
);

$stmtCarteira->execute();

$resultadoCarteira = $stmtCarteira->get_result();

$carteira = $resultadoCarteira->fetch_assoc();

$stmtCarteira->close();


if (!$carteira) {
    header("Location: carteiras.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Busca os ativos da carteira
|--------------------------------------------------------------------------
*/

$sqlAtivos = "
    SELECT
        a.id,
        a.ticker,
        a.quantidade,
        a.preco_medio,
        a.data_compra,
        a.observacao,
        c.nome AS categoria
    FROM ativos a
    LEFT JOIN categorias c
        ON a.categoria_id = c.id
    WHERE a.carteira_id = ?
    ORDER BY a.data_compra DESC, a.id DESC
";

$stmtAtivos = $conexao->prepare($sqlAtivos);

if (!$stmtAtivos) {
    die("Erro ao preparar consulta dos ativos.");
}

$stmtAtivos->bind_param(
    "i",
    $carteira_id
);

$stmtAtivos->execute();

$resultadoAtivos = $stmtAtivos->get_result();

$ativos = [];

while ($ativo = $resultadoAtivos->fetch_assoc()) {
    $ativos[] = $ativo;
}

$stmtAtivos->close();


/*
|--------------------------------------------------------------------------
| Busca as cotações atuais
|--------------------------------------------------------------------------
|
| Em vez de consultar a API uma vez para cada ativo,
| enviamos todos os tickers em uma única consulta.
|
*/

$tickers = [];

foreach ($ativos as $ativo) {

    if (!empty($ativo["ticker"])) {
        $tickers[] = strtoupper(trim($ativo["ticker"]));
    }
}

$cotacoes = obterCotacoes($tickers);


/*
|--------------------------------------------------------------------------
| Calcula os valores da carteira
|--------------------------------------------------------------------------
*/

$totalInvestido = 0;
$totalAtual = 0;
$totalResultado = 0;

foreach ($ativos as &$ativo) {

    $ticker = strtoupper(trim($ativo["ticker"]));

    $quantidade = (float) $ativo["quantidade"];

    $precoMedio = (float) $ativo["preco_medio"];


    /*
    |--------------------------------------------------------------------------
    | Valor originalmente investido
    |--------------------------------------------------------------------------
    */

    $valorInvestido = $quantidade * $precoMedio;

    $ativo["valor_investido"] = $valorInvestido;

    $totalInvestido += $valorInvestido;


    /*
    |--------------------------------------------------------------------------
    | Busca cotação atual
    |--------------------------------------------------------------------------
    */

    $cotacao = $cotacoes[$ticker] ?? null;

    $ativo["cotacao"] = $cotacao;


    /*
    |--------------------------------------------------------------------------
    | Caso a API tenha retornado a cotação
    |--------------------------------------------------------------------------
    */

    if (
        $cotacao !== null &&
        isset($cotacao["preco_atual"]) &&
        $cotacao["preco_atual"] !== null
    ) {

        $precoAtual = (float) $cotacao["preco_atual"];

        $valorAtual = $quantidade * $precoAtual;

        $resultado = $valorAtual - $valorInvestido;


        $ativo["preco_atual"] = $precoAtual;

        $ativo["valor_atual"] = $valorAtual;

        $ativo["resultado"] = $resultado;

        $ativo["variacao_percentual"] =
            $cotacao["variacao_percentual"] ?? null;


        $totalAtual += $valorAtual;

        $totalResultado += $resultado;

    } else {

        /*
        |--------------------------------------------------------------------------
        | API indisponível para este ativo
        |--------------------------------------------------------------------------
        */

        $ativo["preco_atual"] = null;

        $ativo["valor_atual"] = null;

        $ativo["resultado"] = null;

        $ativo["variacao_percentual"] = null;
    }
}

unset($ativo);


/*
|--------------------------------------------------------------------------
| Rentabilidade da carteira
|--------------------------------------------------------------------------
*/

$rentabilidade = null;

if ($totalInvestido > 0) {

    $rentabilidade =
        ($totalResultado / $totalInvestido) * 100;
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

function formatarQuantidade($valor)
{
    return number_format(
        $valor,
        4,
        ",",
        "."
    );
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

    return $valor >= 0 ? "+" : "";
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
        Ativos - <?= htmlspecialchars($carteira["nome"]) ?> | INVESTEGRA
    </title>

    <link
        rel="stylesheet"
        href="css/ativos.css"
    >

</head>

<body>

<div class="container">


    <!-- =========================================================
         CABEÇALHO
         ========================================================= -->

    <div class="topo">

        <div>

            <a
                href="carteiras.php"
                class="voltar"
            >
                ← Voltar para carteiras
            </a>

            <h1>
                <?= htmlspecialchars($carteira["nome"]) ?>
            </h1>

            <?php if (!empty($carteira["descricao"])): ?>

                <p class="descricao-carteira">
                    <?= htmlspecialchars($carteira["descricao"]) ?>
                </p>

            <?php endif; ?>

        </div>


        <a
            href="novo_ativo.php?carteira=<?= $carteira_id ?>"
            class="btn-principal"
        >
            + Novo ativo
        </a>

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

    <?php if (empty($ativos)): ?>

        <div class="estado-vazio">

            <h2>
                Nenhum ativo cadastrado
            </h2>

            <p>
                Adicione o primeiro ativo para começar a acompanhar
                sua carteira.
            </p>

            <a
                href="novo_ativo.php?carteira=<?= $carteira_id ?>"
                class="btn-principal"
            >
                + Adicionar ativo
            </a>

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
                                Preço médio
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

</body>

</html>