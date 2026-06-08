<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["id"];

/* ==========================
   TOTAL DE CARTEIRAS
========================== */

$sql = "
SELECT COUNT(*) AS total
FROM carteiras
WHERE usuario_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$total_carteiras =
$stmt->get_result()
->fetch_assoc()["total"];

/* ==========================
   TOTAL DE ATIVOS
========================== */

$sql = "
SELECT COUNT(*) AS total

FROM ativos

INNER JOIN carteiras
ON ativos.carteira_id = carteiras.id

WHERE carteiras.usuario_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$total_ativos =
$stmt->get_result()
->fetch_assoc()["total"];

/* ==========================
   PATRIMÔNIO TOTAL
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

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$patrimonio =
$stmt->get_result()
->fetch_assoc()["patrimonio"];

/* ==========================
   PERFIL DE RISCO
========================== */

$sql = "
SELECT perfil_risco
FROM usuarios
WHERE id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$perfil =
$stmt->get_result()
->fetch_assoc()["perfil_risco"] ?? "Não definido";

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Dashboard - INVESTEGRA</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    display:flex;

    min-height:100vh;

    background:#f4f6f9;
}

/* ======================
   SIDEBAR
====================== */

.sidebar{

    width:260px;

    background:#1f2937;

    color:white;

    padding:30px 20px;
}

.sidebar h1{

    text-align:center;

    color:#00c17c;

    margin-bottom:40px;
}

.sidebar ul{

    list-style:none;
}

.sidebar li{

    margin-bottom:15px;
}

.sidebar a{

    display:block;

    padding:12px;

    border-radius:10px;

    color:white;

    text-decoration:none;

    transition:.3s;
}

.sidebar a:hover{

    background:#374151;
}

/* ======================
   MAIN
====================== */

.main{

    flex:1;

    padding:40px;
}

/* ======================
   HEADER
====================== */

.header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:35px;
}

.header h2{

    color:#111827;

    margin-bottom:5px;
}

.header p{

    color:#6b7280;
}

/* ======================
   CARDS
====================== */

.cards{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));

    gap:20px;

    margin-bottom:30px;
}

.card{

    background:white;

    padding:25px;

    border-radius:18px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);

    transition:.3s;
}

.card:hover{

    transform:translateY(-5px);
}

.card h3{

    color:#6b7280;

    font-size:14px;

    margin-bottom:10px;
}

.valor{

    color:#00a86b;

    font-size:28px;

    font-weight:bold;
}

/* ======================
   RESUMO
====================== */

.resumo{

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);
}

.resumo h3{

    margin-bottom:15px;

    color:#111827;
}

.resumo p{

    color:#6b7280;

    line-height:1.8;
}

.destaque{

    color:#00a86b;

    font-weight:bold;
}

/* ======================
   AÇÕES RÁPIDAS
====================== */

.acoes{

    margin-top:25px;

    display:flex;

    gap:15px;

    flex-wrap:wrap;
}

.botao{

    background:#00a86b;

    color:white;

    padding:12px 18px;

    border-radius:10px;

    text-decoration:none;

    font-weight:600;

    transition:.3s;
}

.botao:hover{

    background:#00915d;
}

</style>

</head>

<body>

<div class="sidebar">

    <h1>INVESTEGRA</h1>

    <ul>

        <li>
            <a href="dashboard.php">
                🏠 Dashboard
            </a>
        </li>

        <li>
            <a href="perfil.php">
                👤 Meu Perfil
            </a>
        </li>

        <li>
            <a href="carteiras.php">
                💼 Carteiras
            </a>
        </li>

        <li>
            <a href="simulacao.php">
                🧮 Simulações
            </a>
        </li>

        <li>
            <a href="analises.php">
                📊 Análises
            </a>
        </li>

        <li>
            <a href="recomendacoes.php">
                💡 Recomendações
            </a>
        </li>

        <li>
            <a href="logout.php">
                🚪 Sair
            </a>
        </li>

    </ul>

</div>

<div class="main">

    <div class="header">

        <div>

            <h2>

                Olá,
                <?= htmlspecialchars($_SESSION["nome"]) ?>

            </h2>

            <p>

                Bem-vindo ao painel de investimentos

            </p>

        </div>

    </div>

    <div class="cards">

        <div class="card">

            <h3>Patrimônio Total</h3>

            <div class="valor">

                R$
                <?= number_format(
                    $patrimonio,
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="card">

            <h3>Carteiras Criadas</h3>

            <div class="valor">

                <?= $total_carteiras ?>

            </div>

        </div>

        <div class="card">

            <h3>Ativos Registrados</h3>

            <div class="valor">

                <?= $total_ativos ?>

            </div>

        </div>

        <div class="card">

            <h3>Perfil de Risco</h3>

            <div class="valor">

                <?= htmlspecialchars($perfil) ?>

            </div>

        </div>

    </div>

    <div class="resumo">

        <h3>Resumo Geral</h3>

        <p>

            Você possui
            <span class="destaque">
                <?= $total_carteiras ?>
            </span>

            carteira(s) cadastrada(s) e

            <span class="destaque">
                <?= $total_ativos ?>
            </span>

            ativo(s) registrado(s).

        </p>

        <p>

            Seu patrimônio estimado atualmente é de

            <span class="destaque">

                R$
                <?= number_format(
                    $patrimonio,
                    2,
                    ",",
                    "."
                ) ?>

            </span>

        </p>

        <p>

            Perfil de risco atual:

            <span class="destaque">

                <?= htmlspecialchars($perfil) ?>

            </span>

        </p>

        <div class="acoes">

            <a
            href="carteiras.php"
            class="botao">

                Gerenciar Carteiras

            </a>

            <a
            href="perfil.php"
            class="botao">

                Editar Perfil

            </a>

        </div>

    </div>

</div>

</body>
</html>