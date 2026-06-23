<?php

include("conexao.php");
include("salvar_historico.php");

$id =
$_GET["id"];

$sql =
"SELECT carteira_id
FROM ativos
WHERE id = ?";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$ativo =
$stmt->get_result()->fetch_assoc();

$carteira_id =
$ativo["carteira_id"];

$sql =
"DELETE FROM ativos WHERE id = ?";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

header(
"Location: ativos.php?carteira=" .
$carteira_id
);

exit;