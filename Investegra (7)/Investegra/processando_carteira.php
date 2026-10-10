<?php

session_start();

require_once "conexao.php";


/*
 * Verificar login
 */

if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: carteiras.php");
    exit;

}


$id_usuario = $_SESSION["id"];

$nome = trim($_POST["nome"] ?? "");

$descricao = trim(
    $_POST["descricao"] ?? ""
);


/*
 * Validação
 */

if ($nome === "") {

    header("Location: nova_carteira.php?erro=nome");
    exit;

}


if (strlen($nome) > 100) {

    header("Location: nova_carteira.php?erro=nome");
    exit;

}


/*
 * Inserir carteira
 */

$sql = "
    INSERT INTO carteiras
    (
        nome,
        descricao,
        usuario_id
    )
    VALUES (?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssi",
    $nome,
    $descricao,
    $id_usuario
);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: carteiras.php?sucesso=1");
    exit;

}


$stmt->close();

header("Location: carteiras.php?erro=1");
exit;

?>