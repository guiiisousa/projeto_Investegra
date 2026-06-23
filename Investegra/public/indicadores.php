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
   PATRIMÔNIO TOTAL
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
   TOTAL DE CARTEIRAS
========================== */

$sql = "

SELECT COUNT(*) AS total

FROM carteiras

WHERE usuario_id = ?

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$totalCarteiras =

$stmt
->get_result()
->fetch_assoc()["total"];

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
   PATRIMÔNIO MÉDIO
========================== */

$patrimonioMedio = 0;

if($totalCarteiras > 0){

    $patrimonioMedio =

    $patrimonio /
    $totalCarteiras;
}

/* ==========================
   ATIVO MAIS VALIOSO
========================== */

$sql = "

SELECT

a.ticker,

(
a.quantidade *
a.preco_medio
)

AS valor_total

FROM ativos a

INNER JOIN carteiras c
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

ORDER BY valor_total DESC

LIMIT 1

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$ativo =

$stmt
->get_result()
->fetch_assoc();

$ativoNome =
$ativo["ticker"] ?? "N/A";

$ativoValor =
$ativo["valor_total"] ?? 0;

/* ==========================
   DISTRIBUIÇÃO POR CATEGORIA
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

$resultadoCategorias =
$stmt->get_result();

$labelsCategorias = [];
$valoresCategorias = [];

while(
    $linha =
    $resultadoCategorias->fetch_assoc()
){

    $labelsCategorias[] =
    $linha["categoria"];

    $valoresCategorias[] =
    $linha["total"];
}

/* ==========================
   CATEGORIA DOMINANTE
========================== */

$categoriaDominante =
$labelsCategorias[0]
?? "N/A";

$valorDominante =
$valoresCategorias[0]
?? 0;


/* ==========================
   CARTEIRA DOMINANTE
========================== */

$sql = "

SELECT

c.nome,

SUM(
a.quantidade *
a.preco_medio
)

AS total

FROM carteiras c

INNER JOIN ativos a
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

GROUP BY c.id

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

$carteira =

$stmt
->get_result()
->fetch_assoc();

$carteiraDominante =
$carteira["nome"] ?? "N/A";

/* ==========================
   DIVERSIFICAÇÃO
========================== */

$diversificacao = "Baixa";

if($totalAtivos >= 10){

    $diversificacao = "Alta";

}elseif($totalAtivos >= 5){

    $diversificacao = "Média";
}

/* ==========================
   CONCENTRAÇÃO
========================== */

$concentracao = 0;

if($patrimonio > 0){

    $concentracao =

    ($valorDominante / $patrimonio)
    * 100;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Indicadores Financeiros</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    background:#f4f6f9;

    padding:40px;
}

.container{

    max-width:1300px;

    margin:auto;
}

.topo{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:35px;
}

.topo h1{

    color:#111827;

    font-size:36px;
}

.botao{

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:14px 22px;

    border-radius:12px;

    font-weight:bold;

    transition:.3s;
}

.botao:hover{

    background:#00915d;
}

.cards{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(260px,1fr));

    gap:20px;

    margin-bottom:35px;
}

.card{

    background:white;

    padding:30px;

    border-radius:24px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);

    transition:.3s;
}

.card:hover{

    transform:translateY(-5px);
}

.card h3{

    color:#6b7280;

    margin-bottom:15px;

    font-size:15px;
}

.valor{

    color:#00a86b;

    font-size:32px;

    font-weight:bold;
}

.info{

    margin-top:12px;

    color:#4b5563;

    line-height:1.7;
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

    line-height:1.8;

    color:#d1d5db;
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

    padding:30px;

    border-radius:24px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.grafico h2{

    margin-bottom:20px;

    color:#111827;
}

canvas{

    max-width:100%;
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

        gap:20px;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="topo">

        <h1>

            📊 Indicadores Financeiros

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

                Patrimônio Médio

            </h3>

            <div class="valor">

                R$
                <?= number_format(
                    $patrimonioMedio,
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="card">

            <h3>

                Ativo Mais Valioso

            </h3>

            <div class="valor">

                <?= htmlspecialchars(
                    $ativoNome
                ) ?>

            </div>

            <div class="info">

                R$
                <?= number_format(
                    $ativoValor,
                    2,
                    ",",
                    "."
                ) ?>

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

                Carteira Dominante

            </h2>

            <p>

                <?= htmlspecialchars(
                    $carteiraDominante
                ) ?>

            </p>

        </div>

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

                Concentração da Carteira

            </h2>

            <p>

                <?= number_format(
                    $concentracao,
                    1,
                    ",",
                    "."
                ) ?>%

                do patrimônio está concentrado
                na principal categoria.

            </p>

        </div>

    </div>

    <div class="graficos">

        <div class="grafico">

            <h2>

                Estrutura Patrimonial

            </h2>

            <canvas id="grafico1"></canvas>

        </div>

        <div class="grafico">

            <h2>

                Distribuição da Carteira

            </h2>

            <canvas id="grafico2"></canvas>

        </div>

    </div>

</div>

<script>

const grafico1 =
document.getElementById(
"grafico1"
);

new Chart(grafico1,{

    type:"bar",

    data:{

        labels:[

            "Carteiras",
            "Ativos",
            "Patrimônio"

        ],

        datasets:[{

            data:[

                <?= $totalCarteiras ?>,
                <?= $totalAtivos ?>,
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

const grafico2 =
document.getElementById(
"grafico2"
);

new Chart(grafico2,{

    type:"doughnut",

    data:{

       
        labels:
        <?= json_encode(
            $labelsCategorias
        ) ?>,

        datasets:[{

            data:
            <?= json_encode(
                $valoresCategorias
            ) ?>

        }]

    },

    options:{

        responsive:true
    }
});

</script>

</body>
</html>