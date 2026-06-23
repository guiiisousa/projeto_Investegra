<?php

session_start();

include("conexao.php");

$carteira_id =
$_POST["carteira_id"];

$ticker =
strtoupper(
    trim($_POST["ticker"])
);

$categoria =
$_POST["categoria"];

$quantidade =
$_POST["quantidade"];

$preco_medio =
$_POST["preco_medio"];

$data_compra =
$_POST["data_compra"];

$observacao =
trim($_POST["observacao"]);

$sql = "

INSERT INTO ativos
(

carteira_id,
ticker,
categoria,
quantidade,
preco_medio,
data_compra,
observacao

)

VALUES
(

?,
?,
?,
?,
?,
?,
?

)

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(

    "issidss",

    $carteira_id,
    $ticker,
    $categoria,
    $quantidade,
    $preco_medio,
    $data_compra,
    $observacao

);

$stmt->execute();

/* ==========================
   HISTÓRICO
========================== */

include("salvar_historico.php");

header(

"Location: ativos.php?carteira=" .
$carteira_id

);

exit;