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
    "carteira_id",
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


/*
 * Validação básica
 */

if (
    !$id_carteira ||
    $ticker === "" ||
    !$categoria_id ||
    $quantidade === false ||
    $quantidade <= 0 ||
    $preco_medio === false ||
    $preco_medio < 0
) {

    header(
        "Location: novo_ativo.php?carteira="
        . $id_carteira
        . "&erro=1"
    );

    exit;

}


/*
 * Se não houver data, salvar NULL.
 */

if ($data_compra === "") {
    $data_compra = null;
}


/*
 * Verificar se a carteira pertence
 * ao usuário.
 */

$sql = "
    SELECT id
    FROM carteiras
    WHERE id = ?
    AND usuario_id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_carteira,
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows !== 1) {

    $stmt->close();

    header("Location: carteiras.php?erro=1");
    exit;

}

$stmt->close();


/*
 * Verificar se a categoria existe.
 */

$sql = "
    SELECT id
    FROM categorias
    WHERE id = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $categoria_id
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows !== 1) {

    $stmt->close();

    header(
        "Location: novo_ativo.php?carteira="
        . $id_carteira
        . "&erro=1"
    );

    exit;

}

$stmt->close();


/*
 * Inserir ativo.
 */

$sql = "
    INSERT INTO ativos
    (
        carteira_id,
        categoria_id,
        ticker,
        quantidade,
        preco_medio,
        data_compra,
        observacao
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "iisddss",
    $id_carteira,
    $categoria_id,
    $ticker,
    $quantidade,
    $preco_medio,
    $data_compra,
    $observacao
);


if ($stmt->execute()) {

    $stmt->close();


    /*
     * Registrar o novo patrimônio
     * no histórico.
     */

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