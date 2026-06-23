<?php

include("conexao.php");
include("salvar_historico.php");

$id =
$_POST["id"];

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

/* ==========================
   UPDATE
========================== */

$sql = "

UPDATE ativos

SET

ticker = ?,
categoria = ?,
quantidade = ?,
preco_medio = ?,
data_compra = ?,
observacao = ?

WHERE id = ?

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(

    "ssidssi",

    $ticker,
    $categoria,
    $quantidade,
    $preco_medio,
    $data_compra,
    $observacao,
    $id

);

$stmt->execute();

/* ==========================
   REDIRECIONAR
========================== */

header(

"Location: ativos.php?carteira=" .
$carteira_id

);

exit;