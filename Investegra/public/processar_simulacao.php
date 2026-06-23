<?php

session_start();

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

/* ==========================
   DADOS DO FORMULÁRIO
========================== */

$tipoJuros =
$_POST["tipo_juros"];

$aporteInicial =
(float) $_POST["aporte_inicial"];

$aporteMensal =
(float) $_POST["aporte_mensal"];

$rentabilidadeAnual =
(float) $_POST["rentabilidade"];

$anos =
(int) $_POST["anos"];

/* ==========================
   CONFIGURAÇÕES
========================== */

$meses =
$anos * 12;

$taxaMensal =

pow(
    1 + ($rentabilidadeAnual / 100),
    1 / 12
)

- 1;

$historico = [];

$totalInvestido =

$aporteInicial +
($aporteMensal * $meses);

/* ==========================
   JUROS COMPOSTOS
========================== */

if($tipoJuros == "compostos"){

    $montante =
    $aporteInicial;

    for(
        $i = 1;
        $i <= $meses;
        $i++
    ){

        $montante =

        ($montante + $aporteMensal)

        *

        (1 + $taxaMensal);

        $valorInvestido =

        $aporteInicial +
        ($aporteMensal * $i);

        $historico[] = [

            "mes" => $i,

            "patrimonio" => round(
                $montante,
                2
            ),

            "investido" => round(
                $valorInvestido,
                2
            )
        ];
    }
}

/* ==========================
   JUROS SIMPLES
========================== */

else{

    $montante = 0;

    /*
    Cada aporte rende apenas
    pelo tempo restante.
    */

    for(
        $i = 0;
        $i <= $meses;
        $i++
    ){

        $tempoRestante =

        $meses - $i;

        if($i == 0){

            $aporte =
            $aporteInicial;

        }else{

            $aporte =
            $aporteMensal;
        }

        $valorFuturo =

        $aporte *

        (
            1 +
            ($taxaMensal * $tempoRestante)
        );

        $montante +=
        $valorFuturo;
    }

    /*
    Histórico para gráfico
    */

    for(
        $i = 1;
        $i <= $meses;
        $i++
    ){

        $valorInvestido =

        $aporteInicial +
        ($aporteMensal * $i);

        $montanteTemp = 0;

        for(
            $j = 0;
            $j <= $i;
            $j++
        ){

            $tempoRestante =

            $i - $j;

            if($j == 0){

                $aporte =
                $aporteInicial;

            }else{

                $aporte =
                $aporteMensal;
            }

            $valorFuturo =

            $aporte *

            (
                1 +
                ($taxaMensal * $tempoRestante)
            );

            $montanteTemp +=
            $valorFuturo;
        }

        $historico[] = [

            "mes" => $i,

            "patrimonio" => round(
                $montanteTemp,
                2
            ),

            "investido" => round(
                $valorInvestido,
                2
            )
        ];
    }
}

/* ==========================
   RESULTADOS
========================== */

$lucro =

$montante -
$totalInvestido;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Resultado da Simulação</title>

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

    max-width:1000px;

    margin:auto;

    background:white;

    padding:40px;

    border-radius:24px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.08);
}

h1{

    text-align:center;

    color:#1f2937;

    margin-bottom:30px;
}

.resultado{

    text-align:center;

    margin-bottom:35px;
}

.valor-final{

    font-size:48px;

    color:#00a86b;

    font-weight:bold;

    margin-top:10px;
}

.cards{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));

    gap:20px;

    margin-bottom:35px;
}

.card{

    background:#f9fafb;

    padding:25px;

    border-radius:18px;

    border:1px solid #e5e7eb;
}

.card h3{

    color:#6b7280;

    margin-bottom:10px;

    font-size:15px;
}

.card .valor{

    color:#111827;

    font-size:28px;

    font-weight:bold;
}

.grafico{

    background:#ffffff;

    padding:25px;

    border-radius:20px;

    border:1px solid #e5e7eb;

    margin-bottom:30px;
}

.grafico h2{

    color:#1f2937;

    margin-bottom:20px;
}

canvas{

    max-height:450px;
}

.info{

    background:#f9fafb;

    padding:25px;

    border-radius:18px;

    line-height:2;

    color:#4b5563;
}

.botao{

    display:inline-block;

    margin-top:30px;

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:14px 25px;

    border-radius:12px;

    font-weight:bold;
}

.botao:hover{

    background:#00915d;
}

.destaque{

    color:#00a86b;

    font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

    <h1>

        Resultado da Simulação

    </h1>

    <div class="resultado">

        <p>

            Patrimônio Final

        </p>

        <div class="valor-final">

            R$
            <?= number_format(
                $montante,
                2,
                ",",
                "."
            ) ?>

        </div>

    </div>

    <div class="cards">

        <div class="card">

            <h3>

                Tipo de Juros

            </h3>

            <div class="valor">

                <?= ucfirst($tipoJuros) ?>

            </div>

        </div>

        <div class="card">

            <h3>

                Total Investido

            </h3>

            <div class="valor">

                R$
                <?= number_format(
                    $totalInvestido,
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="card">

            <h3>

                Lucro Obtido

            </h3>

            <div class="valor destaque">

                R$
                <?= number_format(
                    $lucro,
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="card">

            <h3>

                Prazo

            </h3>

            <div class="valor">

                <?= $anos ?> anos

            </div>

        </div>

    </div>

    <div class="grafico">

        <h2>

            Crescimento Patrimonial

        </h2>

        <canvas id="graficoCrescimento"></canvas>

    </div>

    <div class="info">

        <strong>

            Rentabilidade anual:

        </strong>

        <?= $rentabilidadeAnual ?>%

        ao ano

        <br>

        <strong>

            Aporte Inicial:

        </strong>

        R$
        <?= number_format(
            $aporteInicial,
            2,
            ",",
            "."
        ) ?>

        <br>

        <strong>

            Aporte Mensal:

        </strong>

        R$
        <?= number_format(
            $aporteMensal,
            2,
            ",",
            "."
        ) ?>

    </div>

    <a
    href="simulacao.php"
    class="botao">

        Nova Simulação

    </a>

</div>

<script>

const historico =
<?= json_encode($historico) ?>;

const labels =
historico.map(item => item.mes);

const patrimonio =
historico.map(item => item.patrimonio);

const investido =
historico.map(item => item.investido);

const ctx =
document.getElementById(
"graficoCrescimento"
);

new Chart(ctx,{

    type:"line",

    data:{

        labels:labels,

        datasets:[

            {

                label:"Patrimônio",

                data:patrimonio,

                tension:0.3,

                fill:true
            },

            {

                label:"Valor Investido",

                data:investido,

                tension:0.3
            }
        ]
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