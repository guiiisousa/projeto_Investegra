<?php

session_start();

require_once "conexao.php";


if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = $_SESSION["id"];


$id_ativo = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


if (!$id_ativo) {

    header("Location: carteiras.php?erro=1");
    exit;

}


/*
 * Primeiro descobrimos a carteira
 * e verificamos se pertence ao usuário.
 */

$sql = "
    SELECT
        a.carteira_id
    FROM ativos a
    INNER JOIN carteiras c
        ON a.carteira_id = c.id
    WHERE a.id = ?
    AND c.usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_ativo,
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows !== 1) {

    $stmt->close();

    header("Location: carteiras.php?erro=1");
    exit;

}


$dados = $resultado->fetch_assoc();

$id_carteira = $dados["carteira_id"];

$stmt->close();


/*
 * Excluir ativo.
 */

$sql = "
    DELETE FROM ativos
    WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id_ativo
);


if ($stmt->execute()) {

    $stmt->close();


    require_once "registrar_historico.php";

    registrarHistoricoPatrimonio(
        $conexao,
        $id_usuario
    );


    header(
        "Location: ativos.php?carteira="
        . $id_carteira
        . "&sucesso=1"
    );

    exit;

}


$stmt->close();

header(
    "Location: ativos.php?carteira="
    . $id_carteira
    . "&erro=1"
);

exit;

?>