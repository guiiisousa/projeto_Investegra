<?php

session_start();

require_once "conexao.php";


/*
|--------------------------------------------------------------------------
| VERIFICAR LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];


/*
|--------------------------------------------------------------------------
| VERIFICAR MÉTODO
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: simulacao.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| RECEBER DADOS
|--------------------------------------------------------------------------
*/

$tipo_juros =
    $_POST["tipo_juros"] ?? "";

$aporte_inicial =
    (float) ($_POST["aporte_inicial"] ?? 0);

$aporte_mensal =
    (float) ($_POST["aporte_mensal"] ?? 0);

$rentabilidade =
    (float) ($_POST["rentabilidade"] ?? 0);

$prazo_anos =
    (int) ($_POST["prazo_anos"] ?? 0);


/*
|--------------------------------------------------------------------------
| VALIDAÇÕES
|--------------------------------------------------------------------------
*/

if (
    $tipo_juros !== "simples" &&
    $tipo_juros !== "compostos"
) {

    die("Tipo de juros inválido.");

}


if ($aporte_inicial < 0) {

    die("O aporte inicial não pode ser negativo.");

}


if ($aporte_mensal < 0) {

    die("O aporte mensal não pode ser negativo.");

}


if ($rentabilidade < 0) {

    die("A rentabilidade não pode ser negativa.");

}


if ($prazo_anos <= 0 || $prazo_anos > 100) {

    die("O prazo informado é inválido.");

}


/*
|--------------------------------------------------------------------------
| CONVERSÕES
|--------------------------------------------------------------------------
*/

$taxa_anual =
    $rentabilidade / 100;

$meses =
    $prazo_anos * 12;


/*
|--------------------------------------------------------------------------
| CÁLCULO
|--------------------------------------------------------------------------
*/

$montante = 0;


/*
|--------------------------------------------------------------------------
| JUROS SIMPLES
|--------------------------------------------------------------------------
*/

if ($tipo_juros === "simples") {


    /*
    | Rendimento sobre o aporte inicial
    */

    $montante_inicial =
        $aporte_inicial *
        (
            1 +
            ($taxa_anual * $prazo_anos)
        );


    /*
    | Rendimento dos aportes mensais
    */

    $montante_aportes = 0;


    for (
        $mes = 1;
        $mes <= $meses;
        $mes++
    ) {


        /*
        | Quantidade de anos que o aporte
        | permanecerá investido.
        */

        $anos_restantes =
            ($meses - $mes + 1) / 12;


        $montante_aportes +=
            $aporte_mensal *
            (
                1 +
                ($taxa_anual * $anos_restantes)
            );

    }


    $montante =
        $montante_inicial +
        $montante_aportes;

}


/*
|--------------------------------------------------------------------------
| JUROS COMPOSTOS
|--------------------------------------------------------------------------
*/

if ($tipo_juros === "compostos") {


    /*
    | Conversão da taxa anual para
    | uma taxa mensal equivalente.
    */

    $taxa_mensal =
        pow(
            1 + $taxa_anual,
            1 / 12
        ) - 1;


    /*
    | Começamos com o aporte inicial.
    */

    $montante =
        $aporte_inicial;


    /*
    | Evolução mês a mês.
    */

    for (
        $mes = 1;
        $mes <= $meses;
        $mes++
    ) {

        $montante =
            (
                $montante *
                (1 + $taxa_mensal)
            )
            +
            $aporte_mensal;

    }

}


/*
|--------------------------------------------------------------------------
| TOTAL APORTADO
|--------------------------------------------------------------------------
*/

$total_aportado =
    $aporte_inicial +
    (
        $aporte_mensal *
        $meses
    );


/*
|--------------------------------------------------------------------------
| RENDIMENTO
|--------------------------------------------------------------------------
*/

$rendimento =
    $montante -
    $total_aportado;


/*
|--------------------------------------------------------------------------
| RENTABILIDADE SOBRE O TOTAL APORTADO
|--------------------------------------------------------------------------
*/

if ($total_aportado > 0) {

    $rentabilidade_resultado =
        (
            $rendimento /
            $total_aportado
        ) * 100;

} else {

    $rentabilidade_resultado = 0;

}


/*
|--------------------------------------------------------------------------
| SALVAR NO BANCO
|--------------------------------------------------------------------------
*/

$sql = "
    INSERT INTO simulacoes
    (
        usuario_id,
        tipo_juros,
        aporte_inicial,
        aporte_mensal,
        rentabilidade,
        prazo_anos,
        montante
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
";


$stmt =
    $conexao->prepare($sql);


if (!$stmt) {

    die(
        "Erro ao preparar a simulação: "
        . $conexao->error
    );

}


$stmt->bind_param(
    "isdddid",
    $id_usuario,
    $tipo_juros,
    $aporte_inicial,
    $aporte_mensal,
    $rentabilidade,
    $prazo_anos,
    $montante
);


if (!$stmt->execute()) {

    $stmt->close();

    die(
        "Erro ao salvar a simulação: "
        . $stmt->error
    );

}


$id_simulacao =
    $stmt->insert_id;


$stmt->close();


/*
|--------------------------------------------------------------------------
| GUARDAR RESULTADO TEMPORARIAMENTE
|--------------------------------------------------------------------------
*/

$_SESSION["resultado_simulacao"] = [

    "id" =>
        $id_simulacao,

    "tipo_juros" =>
        $tipo_juros,

    "aporte_inicial" =>
        $aporte_inicial,

    "aporte_mensal" =>
        $aporte_mensal,

    "rentabilidade_anual" =>
        $rentabilidade,

    "prazo_anos" =>
        $prazo_anos,

    "meses" =>
        $meses,

    "total_aportado" =>
        $total_aportado,

    "rendimento" =>
        $rendimento,

    "montante" =>
        $montante,

    "rentabilidade_resultado" =>
        $rentabilidade_resultado

];


/*
|--------------------------------------------------------------------------
| VOLTAR PARA A SIMULAÇÃO
|--------------------------------------------------------------------------
*/

header(
    "Location: simulacao.php"
);

exit;

?>