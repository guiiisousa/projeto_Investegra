<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];

$id_carteira = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


if (!$id_carteira) {

    header("Location: carteiras.php?erro=1");
    exit;

}


/*
 * Excluir somente se a carteira
 * pertencer ao usuário logado.
 */

$sql = "
    DELETE FROM carteiras
    WHERE id = ?
    AND usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_carteira,
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