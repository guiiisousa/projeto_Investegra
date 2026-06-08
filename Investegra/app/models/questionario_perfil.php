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

<title>Perfil de Investidor</title>

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

    width:700px;

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

.pergunta{

    margin-bottom:30px;
}

.pergunta h3{

    margin-bottom:12px;

    color:#111827;
}

label{

    display:block;

    margin-bottom:8px;

    color:#4b5563;
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

<h1>Questionário de Perfil</h1>

<form action="salvar_perfil.php" method="POST">

    <div class="pergunta">

        <h3>
            1. Como você reage a perdas financeiras?
        </h3>

        <label>
            <input type="radio" name="p1" value="1" required>
            Fico muito desconfortável
        </label>

        <label>
            <input type="radio" name="p1" value="2">
            Aceito pequenas perdas
        </label>

        <label>
            <input type="radio" name="p1" value="3">
            Aceito oscilações para ganhar mais
        </label>

    </div>

    <div class="pergunta">

        <h3>
            2. Qual seu objetivo principal?
        </h3>

        <label>
            <input type="radio" name="p2" value="1" required>
            Preservar patrimônio
        </label>

        <label>
            <input type="radio" name="p2" value="2">
            Crescimento equilibrado
        </label>

        <label>
            <input type="radio" name="p2" value="3">
            Maximizar ganhos
        </label>

    </div>

    <div class="pergunta">

        <h3>
            3. Quanto tempo pretende investir?
        </h3>

        <label>
            <input type="radio" name="p3" value="1" required>
            Menos de 2 anos
        </label>

        <label>
            <input type="radio" name="p3" value="2">
            Entre 2 e 5 anos
        </label>

        <label>
            <input type="radio" name="p3" value="3">
            Mais de 5 anos
        </label>

    </div>

    <button type="submit">

        Descobrir Perfil

    </button>

</form>

</div>

</body>

</html>