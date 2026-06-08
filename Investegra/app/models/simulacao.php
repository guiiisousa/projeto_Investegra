<?php

session_start();

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Simulação de Investimentos</title>

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

    width:600px;

    background:white;

    padding:40px;

    border-radius:24px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.08);
}

h1{

    text-align:center;

    color:#1f2937;

    margin-bottom:10px;
}

.subtitulo{

    text-align:center;

    color:#6b7280;

    margin-bottom:30px;
}

label{

    display:block;

    margin-bottom:8px;

    font-weight:600;

    color:#374151;
}

input{

    width:100%;

    padding:14px;

    margin-bottom:20px;

    border:1px solid #d1d5db;

    border-radius:12px;
}

input:focus{

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

.voltar{

    display:block;

    text-align:center;

    margin-top:20px;

    text-decoration:none;

    color:#00a86b;

    font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

    <h1>Simulador de Investimentos</h1>

    <p class="subtitulo">

        Descubra quanto seu patrimônio poderá valer no futuro

    </p>

    <form
    action="processar_simulacao.php"
    method="POST">

        <label>

            Aporte Inicial (R$)

        </label>

        <input
        type="number"
        step="0.01"
        name="aporte_inicial"
        required>

        <label>

            Aporte Mensal (R$)

        </label>

        <input
        type="number"
        step="0.01"
        name="aporte_mensal"
        required>

        <label>

            Rentabilidade Anual (%)

        </label>

        <input
        type="number"
        step="0.01"
        name="rentabilidade"
        required>

        <label>

            Prazo (anos)

        </label>

        <input
        type="number"
        name="anos"
        required>

        <button type="submit">

            Simular

        </button>

    </form>

    <a
    href="dashboard.php"
    class="voltar">

        ← Voltar ao Dashboard

    </a>

</div>

</body>
</html>