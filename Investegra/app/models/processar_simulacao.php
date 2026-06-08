<?php

session_start();

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

$aporteInicial =
(float) $_POST["aporte_inicial"];

$aporteMensal =
(float) $_POST["aporte_mensal"];

$rentabilidadeAnual =
(float) $_POST["rentabilidade"];

$anos =
(int) $_POST["anos"];

/*
Transforma taxa anual em mensal
*/

$taxaMensal =
pow(
    1 + ($rentabilidadeAnual / 100),
    1 / 12
) - 1;

$meses =
$anos * 12;

$montante =
$aporteInicial;

for(
    $i = 1;
    $i <= $meses;
    $i++
){

    $montante =
    ($montante + $aporteMensal)
    *
    (1 + $taxaMensal);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Resultado da Simulação</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    background:#f4f6f9;

    display:flex;

    justify-content:center;

    align-items:center;

    min-height:100vh;
}

.container{

    width:650px;

    background:white;

    padding:40px;

    border-radius:24px;

    text-align:center;

    box-shadow:
    0 10px 25px rgba(0,0,0,.08);
}

h1{

    color:#1f2937;

    margin-bottom:25px;
}

.resultado{

    font-size:42px;

    color:#00a86b;

    font-weight:bold;

    margin-bottom:25px;
}

.info{

    color:#6b7280;

    line-height:1.8;

    margin-bottom:30px;
}

.botao{

    display:inline-block;

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:14px 25px;

    border-radius:12px;

    font-weight:bold;
}

.botao:hover{

    background:#00915d;
}

</style>

</head>

<body>

<div class="container">

    <h1>

        Resultado da Simulação

    </h1>

    <div class="resultado">

        R$
        <?= number_format(
            $montante,
            2,
            ",",
            "."
        ) ?>

    </div>

    <div class="info">

        Aporte Inicial:
        R$
        <?= number_format(
            $aporteInicial,
            2,
            ",",
            "."
        ) ?>

        <br><br>

        Aporte Mensal:
        R$
        <?= number_format(
            $aporteMensal,
            2,
            ",",
            "."
        ) ?>

        <br><br>

        Rentabilidade:
        <?= $rentabilidadeAnual ?>%

        <br><br>

        Prazo:
        <?= $anos ?> anos

    </div>

    <a
    href="simulacao.php"
    class="botao">

        Nova Simulação

    </a>

</div>

</body>
</html>