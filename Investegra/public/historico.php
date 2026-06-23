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
   BUSCAR HISTÓRICO
========================== */

$sql = "

SELECT
valor_total,
data_registro

FROM historico_patrimonio

WHERE usuario_id = ?

ORDER BY data_registro ASC

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

$historico = [];

$datas = [];
$valores = [];

while(
    $linha =
    $resultado->fetch_assoc()
){

    $historico[] = $linha;

    $datas[] =

    date(
        "d/m",
        strtotime(
            $linha["data_registro"]
        )
    );

    $valores[] =

    (float)
    $linha["valor_total"];
}

/* ==========================
   INDICADORES
========================== */

$totalRegistros =
count($historico);

$patrimonioAtual =
$valores[
    $totalRegistros - 1
] ?? 0;

$patrimonioMaximo =
!empty($valores)
? max($valores)
: 0;

$patrimonioMinimo =
!empty($valores)
? min($valores)
: 0;

$crescimento = 0;

if(
    $totalRegistros > 1 &&
    $valores[0] > 0
){

    $crescimento =

    (
        (
            $patrimonioAtual -
            $valores[0]
        )

        /

        $valores[0]
    )

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

<title>Histórico Patrimonial</title>

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

.container{

    max-width:1300px;

    margin:auto;
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

    font-size:15px;
}

.valor{

    color:#00a86b;

    font-size:32px;

    font-weight:bold;
}

.info{

    margin-top:10px;

    color:#4b5563;
}

/* ======================
   GRÁFICO
====================== */

.grafico{

    background:white;

    padding:35px;

    border-radius:28px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);

    margin-bottom:35px;
}

.grafico h2{

    margin-bottom:25px;

    color:#111827;
}

/* ======================
   TABELA
====================== */

.tabela{

    background:white;

    padding:35px;

    border-radius:28px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.tabela h2{

    margin-bottom:25px;

    color:#111827;
}

table{

    width:100%;

    border-collapse:collapse;
}

th,
td{

    padding:18px;

    text-align:left;

    border-bottom:1px solid #e5e7eb;
}

th{

    background:#f9fafb;

    color:#374151;
}

td{

    color:#4b5563;
}

tr:hover{

    background:#f9fafb;
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

    .valor{

        font-size:26px;
    }

    table{

        display:block;

        overflow-x:auto;
    }
}

</style>

</head>

<body>

<div class="container">

    <div class="topo">

        <h1>

            📈 Histórico Patrimonial

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

                Patrimônio Atual

            </h3>

            <div class="valor">

                R$
                <?= number_format(
                    $patrimonioAtual,
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="card">

            <h3>

                Patrimônio Máximo

            </h3>

            <div class="valor">

                R$
                <?= number_format(
                    $patrimonioMaximo,
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="card">

            <h3>

                Crescimento Patrimonial

            </h3>

            <div class="valor">

                <?= number_format(
                    $crescimento,
                    1,
                    ",",
                    "."
                ) ?>%

            </div>

        </div>

        <div class="card">

            <h3>

                Registros Salvos

            </h3>

            <div class="valor">

                <?= $totalRegistros ?>

            </div>

        </div>

    </div>

    <div class="grafico">

        <h2>

            Evolução Patrimonial

        </h2>

        <canvas id="graficoHistorico"></canvas>

    </div>

    <div class="tabela">

        <h2>

            Histórico Completo

        </h2>

        <table>

            <thead>

                <tr>

                    <th>

                        Data

                    </th>

                    <th>

                        Patrimônio

                    </th>

                </tr>

            </thead>

            <tbody>

                <?php

                foreach(
                    array_reverse(
                        $historico
                    )
                    as $linha
                ){

                ?>

                <tr>

                    <td>

                        <?= date(
                            "d/m/Y H:i",
                            strtotime(
                                $linha["data_registro"]
                            )
                        ) ?>

                    </td>

                    <td>

                        R$
                        <?= number_format(
                            $linha["valor_total"],
                            2,
                            ",",
                            "."
                        ) ?>

                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<script>

const ctx =
document.getElementById(
"graficoHistorico"
);

new Chart(ctx,{

    type:"line",

    data:{

        labels:
        <?= json_encode(
            $datas
        ) ?>,

        datasets:[{

            label:"Patrimônio",

            data:
            <?= json_encode(
                $valores
            ) ?>,

            tension:0.4,

            fill:true,

            borderWidth:4,

            pointRadius:5

        }]
    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                position:"bottom"
            }
        },

        scales:{

            y:{

                beginAtZero:true
            }
        }
    }
});

</script>

</body>
</html>