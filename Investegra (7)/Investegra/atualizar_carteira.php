<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: carteiras.php");
    exit;

}


$id_usuario = $_SESSION["id"];

$id_carteira = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

$nome = trim($_POST["nome"] ?? "");

$descricao = trim(
    $_POST["descricao"] ?? ""
);


if (!$id_carteira || $nome === "") {

    header("Location: carteiras.php?erro=1");
    exit;

}


if (strlen($nome) > 100) {

    header("Location: editar_carteira.php?id=" . $id_carteira . "&erro=1");
    exit;

}


/*
 * Atualização protegida pelo usuario_id.
 */

$sql = "
    UPDATE carteiras
    SET
        nome = ?,
        descricao = ?
    WHERE
        id = ?
        AND usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssii",
    $nome,
    $descricao,
    $id_carteira,
    $id_usuario
);


if ($stmt->execute()) {

    if ($stmt->affected_rows >= 0) {

        $stmt->close();

        header("Location: carteiras.php?sucesso=1");
        exit;

    }

}


$stmt->close();

header("Location: carteiras.php?erro=1");
exit;

?>