<?php

session_start();

include("conexao.php");

$id_usuario =
$_SESSION["id"];

$nome =
trim($_POST["nome"]);

$email =
trim($_POST["email"]);

$perfil_risco =
$_POST["perfil_risco"];

$sql = "
UPDATE usuarios
SET

nome = ?,
email = ?,
perfil_risco = ?

WHERE id = ?
";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "sssi",
    $nome,
    $email,
    $perfil_risco,
    $id_usuario
);

$stmt->execute();

$_SESSION["nome"] =
$nome;

header(
"Location: perfil.php"
);

exit;