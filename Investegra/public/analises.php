<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

$id_usuario =
$_SESSION["id"];

/* ==========================
   DISTRIBUIÇÃO
========================== */

$sql = "

SELECT

a.categoria,

SUM(
a.quantidade *
a.preco_medio
)

AS total

FROM ativos a

INNER JOIN carteiras c
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

GROUP BY a.categoria

ORDER BY total DESC

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado =
$stmt->get_result();

$categorias = [];
$valores = [];

while(
    $linha =
    $resultado->fetch_assoc()
){

    $categorias[] =
    $linha["categoria"];

    $valores[] =
    (float)
    $linha["total"];
}

/* ==========================
   PATRIMÔNIO
========================== */

$sql = "

SELECT

COALESCE(
SUM(
a.quantidade *
a.preco_medio
),
0
)

AS patrimonio

FROM ativos a

INNER JOIN carteiras c
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

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
   TOTAL DE ATIVOS
========================== */

$sql = "

SELECT COUNT(*) AS total

FROM ativos a

INNER JOIN carteiras c
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$totalAtivos =

$stmt
->get_result()
->fetch_assoc()["total"];

/* ==========================
   TOTAL DE CATEGORIAS
========================== */

$totalCategorias =
count($categorias);

/* ==========================
   CATEGORIA DOMINANTE
========================== */

$categoriaDominante =
$categorias[0]
?? "N/A";

$valorDominante =
$valores[0]
?? 0;

/* ==========================
   CONCENTRAÇÃO
========================== */

$concentracao = 0;

if($patrimonio > 0){

    $concentracao =

    (
        $valorDominante /
        $patrimonio
    )

    * 100;
}

/* ==========================
   DIVERSIFICAÇÃO
========================== */

$diversificacao =
"Baixa";

if($totalCategorias >= 5){

    $diversificacao =
    "Alta";

}elseif($totalCategorias >= 3){

    $diversificacao =
    "Média";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Análises - INVESTEGRA</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    background:#f3f4f6;

    padding:40px;
}

/* ======================
   TOPO
====================== */

.topo{

    display:flex;

    justify-content:space-between;

    align-items:center;

    flex-wrap:wrap;

    gap:20px;

    margin-bottom:35px;
}

.topo h1{

    color:#111827;

    font-size:38px;
}

.botao{

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:14px 22px;

    border-radius:14px;

    font-weight:bold;

    transition:.3s;
}

.botao:hover{

    background:#00915d;

    transform:translateY(-2px);
}

/* ======================
   CARDS
====================== */

.cards{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));

    gap:20px;

    margin-bottom:35px;
}

.card{

    background:white;

    padding:30px;

    border-radius:24px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.card h3{

    color:#6b7280;

    margin-bottom:12px;
}

.valor{

    color:#00a86b;

    font-size:32px;

    font-weight:bold;
}

/* ======================
   INSIGHTS
====================== */

.insights{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(300px,1fr));

    gap:20px;

    margin-bottom:35px;
}

.insight{

    background:
    linear-gradient(
        135deg,
        #111827,
        #1f2937
    );

    color:white;

    padding:30px;

    border-radius:24px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.12);
}

.insight h2{

    color:#00c17c;

    margin-bottom:15px;
}

.insight p{

    color:#d1d5db;

    line-height:1.8;
}

/* ======================
   GRÁFICOS
====================== */

.graficos{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(400px,1fr));

    gap:25px;
}

.grafico{

    background:white;

    padding:35px;

    border-radius:28px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.grafico h2{

    color:#111827;

    margin-bottom:25px;
}

/* ======================
   RESPONSIVIDADE
====================== */

@media(max-width:900px){

    body{

        padding:20px;
    }

    .topo{

        flex-direction:column;

        align-items:flex-start;
    }

    .topo h1{

        font-size:30px;
    }
}

</style>

</head>

<body>

<div class="topo">

    <h1>

        📊 Central de Análises

    </h1>

    <a
    href="dashboard.php"
    class="botao">

        Dashboard

    </a>

</div>

<div class="cards">

    <div class="card">

        <h3>

            Patrimônio Total

        </h3>

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

        <h3>

            Total de Ativos

        </h3>

        <div class="valor">

            <?= $totalAtivos ?>

        </div>

    </div>

    <div class="card">

        <h3>

            Categorias

        </h3>

        <div class="valor">

            <?= $totalCategorias ?>

        </div>

    </div>

    <div class="card">

        <h3>

            Diversificação

        </h3>

        <div class="valor">

            <?= $diversificacao ?>

        </div>

    </div>

</div>

<div class="insights">

    <div class="insight">

        <h2>

            Categoria Dominante

        </h2>

        <p>

            <?= htmlspecialchars(
                $categoriaDominante
            ) ?>

        </p>

    </div>

    <div class="insight">

        <h2>

            Concentração

        </h2>

        <p>

            <?= number_format(
                $concentracao,
                1,
                ",",
                "."
            ) ?>%

            do patrimônio está
            concentrado na principal
            categoria da carteira.

        </p>

    </div>

    <div class="insight">

        <h2>

            Exposição Patrimonial

        </h2>

        <p>

            Sua carteira possui

            <?= $totalAtivos ?>

            ativo(s)
            distribuídos em

            <?= $totalCategorias ?>

            categoria(s).

        </p>

    </div>

</div>

<div class="graficos">

    <div class="grafico">

        <h2>

            Distribuição da Carteira

        </h2>

        <canvas id="graficoPizza"></canvas>

    </div>

    <div class="grafico">

        <h2>

            Exposição Patrimonial

        </h2>

        <canvas id="graficoBarra"></canvas>

    </div>

</div>

<script>

const graficoPizza =
document.getElementById(
"graficoPizza"
);

new Chart(graficoPizza,{

    type:"doughnut",

    data:{

        labels:
        <?= json_encode(
            $categorias
        ) ?>,

        datasets:[{

            data:
            <?= json_encode(
                $valores
            ) ?>

        }]
    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                position:"bottom"
            }
        }
    }
});

const graficoBarra =
document.getElementById(
"graficoBarra"
);

new Chart(graficoBarra,{

    type:"bar",

    data:{

        labels:
        <?= json_encode(
            $categorias
        ) ?>,

        datasets:[{

            data:
            <?= json_encode(
                $valores
            ) ?>,

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