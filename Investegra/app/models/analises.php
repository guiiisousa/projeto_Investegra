<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["id"];

/* ==========================
   DADOS DO GRÁFICO
========================== */

$sql = "

SELECT

a.categoria,

SUM(
a.quantidade * a.preco_medio
) AS total

FROM ativos a

INNER JOIN carteiras c
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

GROUP BY a.categoria

ORDER BY total DESC

";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$categorias = [];
$valores = [];

while($linha = $resultado->fetch_assoc()){

    $categorias[] = $linha["categoria"];

    $valores[] = (float)$linha["total"];
}

/* ==========================
   PATRIMÔNIO TOTAL
========================== */

$sql = "

SELECT

COALESCE(
SUM(
a.quantidade * a.preco_medio
),
0
)

AS patrimonio

FROM ativos a

INNER JOIN carteiras c
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$patrimonio = $stmt
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

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$totalAtivos = $stmt
->get_result()
->fetch_assoc()["total"];

/* ==========================
   TOTAL DE CATEGORIAS
========================== */

$totalCategorias = count($categorias);

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

    background:#f4f6f9;

    padding:40px;
}

.topo{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:30px;
}

.topo h1{

    color:#1f2937;
}

.botao{

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:12px 20px;

    border-radius:10px;

    font-weight:bold;

    transition:.3s;
}

.botao:hover{

    background:#00915d;
}

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

    border-radius:20px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);
}

.card h3{

    color:#6b7280;

    margin-bottom:10px;
}

.valor{

    color:#00a86b;

    font-size:28px;

    font-weight:bold;
}

.grafico{

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);

    margin-bottom:30px;
}

.grafico h2{

    color:#1f2937;

    margin-bottom:20px;
}

#graficoPizza{

    max-width:700px;

    max-height:450px;

    margin:auto;
}

.resumo{

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);
}

.resumo h2{

    color:#1f2937;

    margin-bottom:15px;
}

.resumo p{

    color:#6b7280;

    line-height:1.8;
}

.sem-dados{

    text-align:center;

    color:#6b7280;

    font-size:18px;

    padding:40px;
}

</style>

</head>

<body>

<div class="topo">

    <h1>
        📊 Análises da Carteira
    </h1>

    <a
    href="dashboard.php"
    class="botao">

        ← Dashboard

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
            Ativos Cadastrados
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

</div>

<div class="grafico">

    <h2>
        Distribuição da Carteira
    </h2>

    <?php if(count($categorias) > 0){ ?>

        <canvas id="graficoPizza"></canvas>

    <?php } else { ?>

        <div class="sem-dados">

            Nenhum ativo cadastrado para análise.

        </div>

    <?php } ?>

</div>

<div class="resumo">

    <h2>
        Resumo Geral
    </h2>

    <p>

        Sua carteira possui

        <strong>
        <?= $totalAtivos ?>
        </strong>

        ativo(s) distribuídos em

        <strong>
        <?= $totalCategorias ?>
        </strong>

        categoria(s).

        O patrimônio estimado é de

        <strong>

            R$
            <?= number_format(
                $patrimonio,
                2,
                ",",
                "."
            ) ?>

        </strong>.

    </p>

</div>

<?php if(count($categorias) > 0){ ?>

<script>

const ctx =
document.getElementById(
"graficoPizza"
);

new Chart(ctx,{

    type:"pie",

    data:{

        labels:
        <?= json_encode($categorias) ?>,

        datasets:[{

            data:
            <?= json_encode($valores) ?>

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

</script>

<?php } ?>

</body>
</html>