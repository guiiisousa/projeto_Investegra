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


$id_ativo = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

$ticker = strtoupper(
    trim($_POST["ticker"] ?? "")
);

$categoria_id = filter_input(
    INPUT_POST,
    "categoria_id",
    FILTER_VALIDATE_INT
);

$quantidade = filter_input(
    INPUT_POST,
    "quantidade",
    FILTER_VALIDATE_FLOAT
);

$preco_medio = filter_input(
    INPUT_POST,
    "preco_medio",
    FILTER_VALIDATE_FLOAT
);

$data_compra = trim(
    $_POST["data_compra"] ?? ""
);

$observacao = trim(
    $_POST["observacao"] ?? ""
);


if (
    !$id_ativo ||
    $ticker === "" ||
    !$categoria_id ||
    $quantidade === false ||
    $quantidade <= 0 ||
    $preco_medio === false ||
    $preco_medio < 0
) {

    header("Location: carteiras.php?erro=1");
    exit;

}


if ($data_compra === "") {
    $data_compra = null;
}


/*
 * Descobrir a carteira do ativo
 * e verificar propriedade.
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
 * Atualizar ativo.
 */

$sql = "
    UPDATE ativos
    SET
        ticker = ?,
        categoria_id = ?,
        quantidade = ?,
        preco_medio = ?,
        data_compra = ?,
        observacao = ?
    WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "siddssi",
    $ticker,
    $categoria_id,
    $quantidade,
    $preco_medio,
    $data_compra,
    $observacao,
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