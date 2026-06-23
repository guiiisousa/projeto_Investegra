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
   USUÁRIO
========================== */

$sql = "

SELECT
nome,
perfil_risco

FROM usuarios

WHERE id = ?

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$usuario =

$stmt
->get_result()
->fetch_assoc();

$perfil =
$usuario["perfil_risco"];

/* ==========================
   DISTRIBUIÇÃO ATUAL
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
   PATRIMÔNIO TOTAL
========================== */

$patrimonio =
array_sum($valores);

/* ==========================
   PERCENTUAIS ATUAIS
========================== */

$percentuaisAtuais = [];

foreach($valores as $valor){

    if($patrimonio > 0){

        $percentuaisAtuais[] =

        round(
            (
                $valor /
                $patrimonio
            )
            * 100,
            1
        );

    }else{

        $percentuaisAtuais[] = 0;
    }
}

/* ==========================
   DIVERSIFICAÇÃO
========================== */

$totalCategorias =
count($categorias);

$diversificacao =
"Baixa";

if($totalCategorias >= 5){

    $diversificacao =
    "Alta";

}elseif($totalCategorias >= 3){

    $diversificacao =
    "Média";
}

/* ==========================
   CONCENTRAÇÃO
========================== */

$concentracao = 0;

$categoriaDominante =
$categorias[0]
?? "N/A";

if($patrimonio > 0){

    $concentracao =

    (
        $valores[0]
        /
        $patrimonio
    )

    * 100;
}

/* ==========================
   RECOMENDAÇÕES
========================== */

if($perfil == "Conservador"){

    $nivel = "Baixo";

    $descricao =

    "Você prioriza segurança e previsibilidade.";

    $recomendacoes = [

        "Tesouro Selic",
        "CDBs",
        "LCI/LCA",
        "Fundos de Renda Fixa"

    ];

    $alocacao = [

        "Renda Fixa" => 80,
        "FIIs" => 10,
        "Ações" => 10
    ];

}elseif($perfil == "Moderado"){

    $nivel = "Médio";

    $descricao =

    "Você busca equilíbrio entre segurança e crescimento.";

    $recomendacoes = [

        "Tesouro IPCA",
        "ETFs",
        "Fundos Imobiliários",
        "Ações de Dividendos"

    ];

    $alocacao = [

        "Renda Fixa" => 50,
        "FIIs" => 20,
        "Ações" => 30
    ];

}else{

    $nivel = "Alto";

    $descricao =

    "Você aceita oscilações em busca de maior retorno.";

    $recomendacoes = [

        "Ações de Crescimento",
        "ETFs Internacionais",
        "FIIs",
        "Criptomoedas"

    ];

    $alocacao = [

        "Renda Fixa" => 20,
        "FIIs" => 20,
        "Ações" => 50,
        "Cripto" => 10
    ];
}

/* ==========================
   ALERTAS
========================== */

$alertas = [];

if($concentracao >= 60){

    $alertas[] =

    "Sua carteira está muito concentrada em " .
    $categoriaDominante .
    ".";

}

if($diversificacao == "Baixa"){

    $alertas[] =

    "Sua carteira possui baixa diversificação.";

}

if(

    $perfil == "Conservador"

    &&

    in_array(
        "Cripto",
        $categorias
    )

){

    $alertas[] =

    "Seu perfil conservador possui exposição em ativos agressivos.";

}

/* ==========================
   INSIGHTS
========================== */

$insights = [];

$insights[] =

"Categoria dominante: " .
$categoriaDominante;

$insights[] =

"Diversificação atual: " .
$diversificacao;

$insights[] =

"Concentração patrimonial: " .
number_format(
    $concentracao,
    1,
    ",",
    "."
) . "%";

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Recomendações</title>

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

.topo{

    display:flex;

    justify-content:space-between;

    align-items:center;

    flex-wrap:wrap;

    gap:20px;

    margin-bottom:35px;
}

.titulo{

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

.card{

    background:white;

    padding:35px;

    border-radius:28px;

    margin-bottom:30px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.card h2{

    color:#111827;

    margin-bottom:18px;
}

.card p{

    color:#4b5563;

    line-height:1.9;
}

.badge{

    display:inline-block;

    background:#00a86b;

    color:white;

    padding:10px 18px;

    border-radius:999px;

    font-weight:bold;

    margin-top:15px;
}

.lista{

    margin-top:20px;
}

.lista li{

    margin-bottom:14px;

    color:#374151;

    line-height:1.7;
}

.alerta{

    background:#fef2f2;

    border-left:5px solid #ef4444;

    padding:18px;

    border-radius:14px;

    margin-bottom:15px;

    color:#991b1b;
}

.insights{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(280px,1fr));

    gap:20px;

    margin-bottom:30px;
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
}

.insight h3{

    color:#00c17c;

    margin-bottom:15px;
}

.tabela{

    width:100%;

    border-collapse:collapse;

    margin-top:20px;
}

.tabela th,
.tabela td{

    border-bottom:1px solid #e5e7eb;

    padding:14px;

    text-align:left;
}

.tabela th{

    background:#f9fafb;
}

.grafico{

    max-width:500px;

    margin:auto;
}

@media(max-width:900px){

    body{

        padding:20px;
    }

    .titulo{

        font-size:30px;
    }

    .topo{

        flex-direction:column;

        align-items:flex-start;
    }
}

</style>

</head>

<body>

<div class="topo">

    <h1 class="titulo">

        💡 Recomendações Inteligentes

    </h1>

    <a
    href="dashboard.php"
    class="botao">

        Dashboard

    </a>

</div>

<div class="card">

    <h2>

        Perfil Atual

    </h2>

    <p>

        Perfil:

        <strong>

            <?= htmlspecialchars(
                $perfil
            ) ?>

        </strong>

    </p>

    <p>

        <?= $descricao ?>

    </p>

    <div class="badge">

        Risco <?= $nivel ?>

    </div>

</div>

<?php if(!empty($alertas)){ ?>

<div class="card">

    <h2>

        Alertas Estratégicos

    </h2>

    <?php foreach($alertas as $alerta){ ?>

    <div class="alerta">

        <?= $alerta ?>

    </div>

    <?php } ?>

</div>

<?php } ?>

<div class="insights">

<?php foreach($insights as $insight){ ?>

<div class="insight">

    <h3>

        Insight

    </h3>

    <p>

        <?= $insight ?>

    </p>

</div>

<?php } ?>

</div>

<div class="card">

    <h2>

        Ativos Recomendados

    </h2>

    <ul class="lista">

        <?php foreach($recomendacoes as $item){ ?>

        <li>

            <?= $item ?>

        </li>

        <?php } ?>

    </ul>

</div>

<div class="card">

    <h2>

        Comparativo de Alocação

    </h2>

    <table class="tabela">

        <tr>

            <th>

                Categoria

            </th>

            <th>

                Atual

            </th>

            <th>

                Ideal

            </th>

        </tr>

        <?php

        foreach(
            $alocacao
            as $categoria =>
            $ideal
        ){

            $atual = 0;

            $indice =
            array_search(
                $categoria,
                $categorias
            );

            if($indice !== false){

                $atual =
                $percentuaisAtuais[$indice];
            }

        ?>

        <tr>

            <td>

                <?= $categoria ?>

            </td>

            <td>

                <?= number_format(
                    $atual,
                    1,
                    ",",
                    "."
                ) ?>%

            </td>

            <td>

                <?= $ideal ?>%

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

<div class="card">

    <h2>

        Distribuição Atual da Carteira

    </h2>

    <div class="grafico">

        <canvas id="grafico"></canvas>

    </div>

</div>

<script>

const ctx =
document.getElementById(
"grafico"
);

new Chart(ctx,{

    type:"doughnut",

    data:{

        labels:
        <?= json_encode(
            $categorias
        ) ?>,

        datasets:[{

            data:
            <?= json_encode(
                $percentuaisAtuais
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

</script>

</body>
</html>