<?php

include("conexao.php");

$id =
$_POST["id"];

$carteira_id =
$_POST["carteira_id"];

$ticker =
strtoupper($_POST["ticker"]);

$quantidade =
$_POST["quantidade"];

$preco_medio =
$_POST["preco_medio"];

$sql = "
UPDATE ativos
SET

ticker = ?,
quantidade = ?,
preco_medio = ?
categoria = ?

WHERE id = ?
";

$stmt =
$conn->prepare($sql);

$categoria =
$_POST["categoria"];

$stmt->bind_param(
    "sidsi",
    $ticker,
    $quantidade,
    $preco_medio,
    $categoria, 
    $id
);

$stmt->execute();

header(
"Location: ativos.php?carteira=" .
$carteira_id
);

exit;