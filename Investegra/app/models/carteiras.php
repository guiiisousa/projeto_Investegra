<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["id"];

$sql = "
SELECT *
FROM carteiras
WHERE usuario_id = ?
ORDER BY criado_em DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
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

<title>Carteiras - INVESTEGRA</title>

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

    padding:12px 20px;

    border-radius:10px;

    text-decoration:none;

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

.lista{

    display:flex;

    flex-direction:column;

    gap:20px;
}

.carteira{

    background:white;

    border-radius:20px;

    padding:25px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);
}

.carteira h3{

    color:#111827;

    margin-bottom:10px;
}

.descricao{

    color:#6b7280;

    margin-bottom:15px;
}

.patrimonio{

    color:#00a86b;

    font-weight:bold;

    margin-bottom:20px;
}

.acoes{

    display:flex;

    gap:20px;

    flex-wrap:wrap;
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

.ativos{

    color:#2563eb;
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

    color:#111827;

    margin-bottom:10px;
}

.vazio p{

    color:#6b7280;
}

</style>

</head>

<body>

<div class="topo">

    <div class="titulo">

        <h1>Minhas Carteiras</h1>

        <p>
            Gerencie suas carteiras de investimentos
        </p>

    </div>

    <div class="botoes">

        <a
        href="dashboard.php"
        class="botao botao-secundario">

            Dashboard

        </a>

        <a
        href="nova_carteira.php"
        class="botao">

            + Nova Carteira

        </a>

    </div>

</div>

<div class="lista">

<?php

if($resultado->num_rows == 0){

?>

    <div class="vazio">

        <h3>

            Nenhuma carteira cadastrada

        </h3>

        <p>

            Crie sua primeira carteira para começar a organizar seus investimentos.

        </p>

    </div>

<?php

}else{

while($carteira = $resultado->fetch_assoc()){

    $sqlPatrimonio = "

    SELECT

    COALESCE(
    SUM(
    quantidade * preco_medio
    ),
    0
    )

    AS total

    FROM ativos

    WHERE carteira_id = ?

    ";

    $stmtPatrimonio =
    $conn->prepare($sqlPatrimonio);

    $stmtPatrimonio->bind_param(
        "i",
        $carteira["id"]
    );

    $stmtPatrimonio->execute();

    $patrimonio =
    $stmtPatrimonio
    ->get_result()
    ->fetch_assoc()["total"];

?>

    <div class="carteira">

        <h3>

            <?= htmlspecialchars($carteira["nome"]) ?>

        </h3>

        <div class="descricao">

            <?= htmlspecialchars($carteira["descricao"]) ?>

        </div>

        <div class="patrimonio">

            Patrimônio:
            R$
            <?= number_format(
                $patrimonio,
                2,
                ",",
                "."
            ) ?>

        </div>

        <div class="acoes">

            <a
            class="editar"
            href="editar_carteira.php?id=<?= $carteira["id"] ?>">

                Editar

            </a>

            <a
            class="excluir"
            href="excluir_carteira.php?id=<?= $carteira["id"] ?>"
            onclick="return confirm('Deseja realmente excluir esta carteira?')">

                Excluir

            </a>

            <a
            class="ativos"
            href="ativos.php?carteira=<?= $carteira["id"] ?>">

                Ver Ativos

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