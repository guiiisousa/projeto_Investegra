<?php

session_start();

include("conexao.php");

$id =
$_GET["id"];

$sql =
"SELECT * FROM ativos WHERE id = ?";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$ativo =
$stmt
->get_result()
->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<title>Editar Ativo</title>

<style>

body{

    background:#f4f6f9;

    display:flex;

    justify-content:center;

    align-items:center;

    min-height:100vh;

    font-family:'Segoe UI',sans-serif;
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

    margin-bottom:30px;

    color:#1f2937;
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

        Editar Ativo

    </h1>

    <form
    action="atualizar_ativo.php"
    method="POST">

        <input
        type="hidden"
        name="id"
        value="<?= $ativo["id"] ?>">

        <input
        type="hidden"
        name="carteira_id"
        value="<?= $ativo["carteira_id"] ?>">

        <input
        type="text"
        name="ticker"
        value="<?= htmlspecialchars($ativo["ticker"]) ?>"
        required>

        <select
        name="categoria"
        required>

            <option
            value="Ação"
            <?= $ativo["categoria"] == "Ação" ? "selected" : "" ?>>

                Ação

            </option>

            <option
            value="FII"
            <?= $ativo["categoria"] == "FII" ? "selected" : "" ?>>

                FII

            </option>

            <option
            value="ETF"
            <?= $ativo["categoria"] == "ETF" ? "selected" : "" ?>>

                ETF

            </option>

            <option
            value="Criptomoeda"
            <?= $ativo["categoria"] == "Criptomoeda" ? "selected" : "" ?>>

                Criptomoeda

            </option>

        </select>

        <input
        type="number"
        name="quantidade"
        value="<?= $ativo["quantidade"] ?>"
        required>

        <input
        type="number"
        step="0.01"
        name="preco_medio"
        value="<?= $ativo["preco_medio"] ?>"
        required>

        <input
        type="date"
        name="data_compra"
        value="<?= $ativo["data_compra"] ?>"
        required>

        <textarea
        name="observacao"><?= htmlspecialchars($ativo["observacao"]) ?></textarea>

        <button type="submit">

            Atualizar Ativo

        </button>

    </form>

</div>

</body>
</html>