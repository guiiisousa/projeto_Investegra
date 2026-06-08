<?php

include("conexao.php");

$carteira_id =
$_POST["carteira_id"];

$ticker =
strtoupper($_POST["ticker"]);

$quantidade =
$_POST["quantidade"];

$preco_medio =
$_POST["preco_medio"];

$sql = "
INSERT INTO ativos
(
    carteira_id,
    ticker,
    quantidade,
    preco_medio, 
    categoria
)
VALUES
(
    ?,
    ?,
    ?,
    ?, 
    ?
)
";

$stmt =
$conn->prepare($sql);

$categoria =
$_POST["categoria"];

$stmt->bind_param(
    "isids",
    $carteira_id,
    $ticker,
    $quantidade,
    $preco_medio,
    $categoria
);

$stmt->execute();

header(
"Location: ativos.php?carteira=" .
$carteira_id
);

exit;