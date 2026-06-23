<?php

session_start();

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

$carteira_id =
$_GET["carteira"];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Novo Ativo</title>

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

    box-shadow:
    0 10px 25px rgba(0,0,0,.08);
}

h1{

    text-align:center;

    color:#1f2937;

    margin-bottom:30px;
}

label{

    display:block;

    margin-bottom:8px;

    font-weight:600;

    color:#374151;
}

input,
select,
textarea{

    width:100%;

    padding:14px;

    margin-bottom:20px;

    border:1px solid #d1d5db;

    border-radius:12px;
}

textarea{

    resize:none;

    height:120px;
}

input:focus,
select:focus,
textarea:focus{

    outline:none;

    border-color:#00a86b;

    box-shadow:
    0 0 0 4px rgba(0,168,107,.12);
}

button{

    width:100%;

    padding:15px;

    border:none;

    border-radius:12px;

    background:#00a86b;

    color:white;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;
}

button:hover{

    background:#00915d;
}

</style>

</head>

<body>

<div class="container">

    <h1>

        Cadastrar Ativo

    </h1>

    <form
    action="salvar_ativo.php"
    method="POST">

        <input
        type="hidden"
        name="carteira_id"
        value="<?= $carteira_id ?>">

        <label>

            Ticker

        </label>

        <input
        type="text"
        name="ticker"
        placeholder="Ex: VALE3"
        required>

        <label>

            Categoria

        </label>

        <select
        name="categoria"
        required>

            <option value="Ação">

                Ação

            </option>

            <option value="FII">

                FII

            </option>

            <option value="ETF">

                ETF

            </option>

            <option value="Criptomoeda">

                Criptomoeda

            </option>

            <option value="Renda Fixa">

                Renda Fixa

            </option>

        </select>

        <label>

            Quantidade

        </label>

        <input
        type="number"
        name="quantidade"
        required>

        <label>

            Preço Médio

        </label>

        <input
        type="number"
        step="0.01"
        name="preco_medio"
        required>

        <label>

            Data da Compra

        </label>

        <input
        type="date"
        name="data_compra"
        value="<?= date("Y-m-d") ?>"
        required>

        <label>

            Observações

        </label>

        <textarea
        name="observacao"
        placeholder="Observações sobre o ativo..."></textarea>

        <button type="submit">

            Salvar Ativo

        </button>

    </form>

</div>

</body>
</html>