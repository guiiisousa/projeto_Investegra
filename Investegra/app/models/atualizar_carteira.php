<?php

include("conexao.php");

$id =
$_POST["id"];

$nome =
$_POST["nome"];

$descricao =
$_POST["descricao"];

$sql = "
UPDATE carteiras
SET

nome = ?,
descricao = ?

WHERE id = ?
";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "ssi",
    $nome,
    $descricao,
    $id
);

$stmt->execute();

header(
"Location: carteiras.php"
);

exit;