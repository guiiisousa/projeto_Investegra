<?php

session_start();

include("conexao.php");

$nome =
trim($_POST["nome"]);

$descricao =
trim($_POST["descricao"]);

$usuario_id =
$_SESSION["id"];

$sql = "
INSERT INTO carteiras
(
    nome,
    descricao,
    usuario_id
)
VALUES
(
    ?,
    ?,
    ?
)
";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "ssi",
    $nome,
    $descricao,
    $usuario_id
);

$stmt->execute();

header(
"Location: carteiras.php"
);

exit;