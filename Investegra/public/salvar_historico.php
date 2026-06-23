<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    exit;
}

$id_usuario =
$_SESSION["id"];

/* ==========================
   CALCULAR PATRIMÔNIO
========================== */

$sql = "

SELECT

COALESCE(
SUM(
ativos.quantidade *
ativos.preco_medio
),
0
)

AS patrimonio

FROM ativos

INNER JOIN carteiras
ON ativos.carteira_id = carteiras.id

WHERE carteiras.usuario_id = ?

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$patrimonio =

$stmt
->get_result()
->fetch_assoc()["patrimonio"];

/* ==========================
   SALVAR HISTÓRICO
========================== */

$sql = "

INSERT INTO historico_patrimonio
(
usuario_id,
valor_total
)

VALUES
(
?,
?
)

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "id",
    $id_usuario,
    $patrimonio
);

$stmt->execute();