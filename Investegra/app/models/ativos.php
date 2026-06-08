<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

if(!isset($_GET["carteira"])){

    die("Carteira não informada.");

}

$carteira_id = (int) $_GET["carteira"];

/* ==========================
   BUSCAR CARTEIRA
========================== */

$sql = "
SELECT *
FROM carteiras
WHERE id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $carteira_id
);

$stmt->execute();

$carteira =
$stmt->get_result()
->fetch_assoc();

if(!$carteira){

    die("Carteira não encontrada.");

}

/* ==========================
   BUSCAR ATIVOS
========================== */

$sql = "
SELECT *
FROM ativos
WHERE carteira_id = ?
ORDER BY ticker
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $carteira_id
);

$stmt->execute();

$resultado =
$stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Ativos - INVESTEGRA</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    background:#f4f6f9;

    min-height:100vh;

    padding:40px;
}

/* ======================
   TOPO
====================== */

.topo{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:35px;
}

.titulo h1{

    color:#1f2937;

    margin-bottom:5px;
}

.titulo p{

    color:#6b7280;
}

.botoes{

    display:flex;

    gap:10px;
}

.botao{

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:12px 20px;

    border-radius:10px;

    font-weight:600;

    transition:.3s;
}

.botao:hover{

    background:#00915d;
}

.botao-secundario{

    background:#374151;
}

.botao-secundario:hover{

    background:#1f2937;
}

/* ======================
   ATIVOS
====================== */

.lista{

    display:flex;

    flex-direction:column;

    gap:20px;
}

.ativo{

    background:white;

    border-radius:20px;

    padding:25px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);
}

.ativo h3{

    color:#111827;

    margin-bottom:15px;

    font-size:22px;
}

.info{

    color:#6b7280;

    margin-bottom:8px;
}

.valor{

    color:#00a86b;

    font-weight:bold;

    margin-top:10px;

    margin-bottom:20px;
}

.acoes{

    display:flex;

    gap:20px;
}

.acoes a{

    text-decoration:none;

    font-weight:600;
}

.editar{

    color:#00a86b;
}

.excluir{

    color:#ef4444;
}

.vazio{

    background:white;

    padding:40px;

    border-radius:20px;

    text-align:center;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);
}

.vazio h3{

    margin-bottom:10px;

    color:#111827;
}

.vazio p{

    color:#6b7280;
}

</style>

</head>

<body>

<div class="topo">

    <div class="titulo">

        <h1>

            Ativos da Carteira

        </h1>

        <p>

            <?= htmlspecialchars($carteira["nome"]) ?>

        </p>

    </div>

    <div class="botoes">

        <a
        href="carteiras.php"
        class="botao botao-secundario">

            Voltar

        </a>

        <a
        href="novo_ativo.php?carteira=<?= $carteira_id ?>"
        class="botao">

            + Novo Ativo

        </a>

    </div>

</div>

<div class="lista">

<?php

if($resultado->num_rows == 0){

?>

    <div class="vazio">

        <h3>

            Nenhum ativo cadastrado

        </h3>

        <p>

            Adicione o primeiro ativo desta carteira.

        </p>

    </div>

<?php

}else{

while($ativo = $resultado->fetch_assoc()){

    $valorTotal =
    $ativo["quantidade"] *
    $ativo["preco_medio"];

?>

    <div class="ativo">

        <h3>

            <?= htmlspecialchars($ativo["ticker"]) ?>

        </h3>

        <div class="info">

            Quantidade:
            <?= $ativo["quantidade"] ?>

        </div>

        <div class="info">

            Preço Médio:
            R$
            <?= number_format(
                $ativo["preco_medio"],
                2,
                ",",
                "."
            ) ?>

        </div>

        <div class="valor">

            Valor Investido:
            R$
            <?= number_format(
                $valorTotal,
                2,
                ",",
                "."
            ) ?>

        </div>

        <div class="acoes">

            <a
            class="editar"
            href="editar_ativo.php?id=<?= $ativo["id"] ?>">

                Editar

            </a>

            <a
            class="excluir"
            href="excluir_ativo.php?id=<?= $ativo["id"] ?>"
            onclick="return confirm('Deseja realmente excluir este ativo?')">

                Excluir

            </a>

        </div>

    </div>

<?php

}

}

?>

</div>

</body>
</html>