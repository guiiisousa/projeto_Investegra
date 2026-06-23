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

/* ==========================
   CATEGORIA DOMINANTE
========================== */

$sql = "

SELECT

categoria,
COUNT(*) AS total

FROM ativos

INNER JOIN carteiras
ON ativos.carteira_id = carteiras.id

WHERE carteiras.usuario_id = ?

GROUP BY categoria

ORDER BY total DESC

LIMIT 1

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$categoriaDominante =
$stmt
->get_result()
->fetch_assoc();

/* ==========================
   ATIVO MAIS VALIOSO
========================== */

$sql = "

SELECT

ticker,

(
quantidade *
preco_medio
)

AS total

FROM ativos

INNER JOIN carteiras
ON ativos.carteira_id = carteiras.id

WHERE carteiras.usuario_id = ?

ORDER BY total DESC

LIMIT 1

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$maiorAtivo =
$stmt
->get_result()
->fetch_assoc();

/* ==========================
   PATRIMÔNIO MÉDIO
========================== */

$mediaPatrimonio = 0;

if($total_ativos > 0){

    $mediaPatrimonio =
    $patrimonio /
    $total_ativos;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Dashboard - INVESTEGRA</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    background:
    linear-gradient(
        180deg,
        #111827,
        #1f2937
    );

    color:white;

    padding:30px 20px;
}

.sidebar h1{

    text-align:center;

    color:#00c17c;

    margin-bottom:40px;

    font-size:30px;
}

.sidebar ul{

    list-style:none;
}

.sidebar li{

    margin-bottom:15px;
}

.sidebar a{

    display:block;

    padding:14px;

    border-radius:12px;

    color:white;

    text-decoration:none;

    transition:.3s;
}

.sidebar a:hover{

    background:#374151;

    transform:translateX(5px);
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

    font-size:32px;
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

    border-radius:22px;

    box-shadow:
    0 8px 20px rgba(0,0,0,.06);

    transition:.3s;
}

.card:hover{

    transform:translateY(-6px);
}

.card h3{

    color:#6b7280;

    font-size:14px;

    margin-bottom:10px;
}

.valor{

    color:#00a86b;

    font-size:30px;

    font-weight:bold;
}

/* ======================
   RESUMO
====================== */

.resumo{

    background:white;

    padding:35px;

    border-radius:24px;

    box-shadow:
    0 8px 20px rgba(0,0,0,.06);
}

.resumo h3{

    margin-bottom:20px;

    color:#111827;

    font-size:28px;
}

.resumo p{

    color:#6b7280;

    line-height:2;
}

.destaque{

    color:#00a86b;

    font-weight:bold;
}

/* ======================
   AÇÕES
====================== */

.acoes{

    margin-top:30px;

    display:flex;

    gap:15px;

    flex-wrap:wrap;
}

.botao{

    background:#00a86b;

    color:white;

    padding:14px 20px;

    border-radius:12px;

    text-decoration:none;

    font-weight:600;

    transition:.3s;
}

.botao:hover{

    background:#00915d;
}

/* ======================
   INSIGHTS
====================== */

.insights{

    margin-top:30px;

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));

    gap:20px;
}

.insight{

    background:
    linear-gradient(
        135deg,
        #111827,
        #1f2937
    );

    color:white;

    padding:25px;

    border-radius:20px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.12);
}

.insight h4{

    margin-bottom:12px;

    color:#00c17c;

    font-size:18px;
}

.insight p{

    line-height:1.8;

    color:#d1d5db;
}

/* ======================
   GRÁFICO
====================== */

.grafico-dashboard{

    margin-top:30px;

    background:white;

    padding:30px;

    border-radius:24px;

    box-shadow:
    0 8px 20px rgba(0,0,0,.06);
}

.grafico-dashboard h3{

    margin-bottom:20px;

    color:#111827;
}

/* ======================
   RESPONSIVIDADE
====================== */

@media(max-width:900px){

    body{

        flex-direction:column;
    }

    .sidebar{

        width:100%;
    }

    .main{

        padding:20px;
    }

    .header{

        flex-direction:column;

        align-items:flex-start;

        gap:15px;
    }
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
            <a href="historico.php">
                📈 Histórico
            </a>
        </li>

        <li>
            <a href="indicadores.php">
                📊 Indicadores
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

                Bem-vindo ao painel financeiro inteligente

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

        <h3>

            Resumo Geral

        </h3>

        <p>

            Você possui
            <span class="destaque">

                <?= $total_carteiras ?>

            </span>

            carteira(s) cadastrada(s)
            e

            <span class="destaque">

                <?= $total_ativos ?>

            </span>

            ativo(s) registrado(s).

        </p>

        <p>

            Seu patrimônio atual estimado é de

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

    <div class="insights">

        <div class="insight">

            <h4>

                Categoria Dominante

            </h4>

            <p>

                <?=
                $categoriaDominante["categoria"]
                ??
                "Sem categoria"
                ?>

            </p>

        </div>

        <div class="insight">

            <h4>

                Ativo Mais Valioso

            </h4>

            <p>

                <?=
                $maiorAtivo["ticker"]
                ??
                "Nenhum"
                ?>

            </p>

        </div>

        <div class="insight">

            <h4>

                Patrimônio Médio por Ativo

            </h4>

            <p>

                R$
                <?= number_format(
                    $mediaPatrimonio,
                    2,
                    ",",
                    "."
                ) ?>

            </p>

        </div>

    </div>

    <div class="grafico-dashboard">

        <h3>

            Visão Geral da Plataforma

        </h3>

        <canvas id="graficoPatrimonio"></canvas>

    </div>

</div>

<script>

const ctx =
document.getElementById(
"graficoPatrimonio"
);

new Chart(ctx,{

    type:"bar",

    data:{

        labels:[

            "Carteiras",
            "Ativos",
            "Patrimônio"

        ],

        datasets:[{

            label:"Dados",

            data:[

                <?= $total_carteiras ?>,
                <?= $total_ativos ?>,
                <?= $patrimonio ?>

            ],

            borderRadius:10

        }]
    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                display:false
            }
        }
    }
});

</script>

</body>
</html>