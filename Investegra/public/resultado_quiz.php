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
   BUSCAR DADOS
========================== */

$sql = "

SELECT

nome,
perfil_risco,
objetivo,
descricao_pessoal,
analise_ia,
score_risco,
idade,
experiencia,
renda_mensal,
horizonte_investimento,
tolerancia_risco

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

/* ==========================
   CORES DO PERFIL
========================== */

$corPerfil = "#f59e0b";

if($usuario["perfil_risco"] == "Conservador"){

    $corPerfil = "#10b981";

}elseif($usuario["perfil_risco"] == "Arrojado"){

    $corPerfil = "#ef4444";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Resultado do Quiz</title>

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

    min-height:100vh;

    padding:40px;
}

/* ==========================
   CONTAINER
========================== */

.container{

    max-width:1200px;

    margin:auto;
}

/* ==========================
   TOPO
========================== */

.topo{

    margin-bottom:35px;
}

.topo h1{

    color:#1f2937;

    margin-bottom:10px;
}

.topo p{

    color:#6b7280;

    line-height:1.7;
}

/* ==========================
   PERFIL
========================== */

.perfil-card{

    background:white;

    border-radius:28px;

    padding:45px;

    text-align:center;

    margin-bottom:30px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.08);
}

.perfil{

    font-size:52px;

    font-weight:bold;

    margin-top:15px;

    color:
    <?= $corPerfil ?>;
}

.score{

    margin-top:20px;

    color:#6b7280;

    font-size:18px;
}

/* ==========================
   GRID
========================== */

.grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(350px,1fr));

    gap:25px;

    margin-bottom:30px;
}

/* ==========================
   CARDS
========================== */

.card{

    background:white;

    border-radius:24px;

    padding:30px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.card h2{

    color:#111827;

    margin-bottom:20px;

    font-size:22px;
}

.card p{

    color:#4b5563;

    line-height:1.9;
}

/* ==========================
   INFO
========================== */

.info{

    margin-bottom:18px;
}

.info strong{

    color:#111827;
}

/* ==========================
   RECOMENDAÇÕES
========================== */

.recomendacao{

    background:#f9fafb;

    padding:18px;

    border-radius:16px;

    margin-bottom:15px;

    border-left:
    5px solid #00a86b;
}

/* ==========================
   BOTÕES
========================== */

.botoes{

    display:flex;

    gap:15px;

    margin-top:35px;

    flex-wrap:wrap;
}

.botao{

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:14px 24px;

    border-radius:12px;

    font-weight:bold;

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

/* ==========================
   GRÁFICO
========================== */

.grafico{

    margin-top:20px;
}

canvas{

    max-width:300px;

    margin:auto;
}

</style>

</head>

<body>

<div class="container">

    <!-- TOPO -->

    <div class="topo">

        <h1>

            Resultado Inteligente do Perfil

        </h1>

        <p>

            O INVESTEGRA analisou suas respostas,
            comportamento financeiro e objetivos
            para identificar seu perfil de investidor.

        </p>

    </div>

    <!-- PERFIL -->

    <div class="perfil-card">

        <h2>

            Perfil Identificado

        </h2>

        <div class="perfil">

            <?= htmlspecialchars(
                $usuario["perfil_risco"]
            ) ?>

        </div>

        <div class="score">

            Score de Risco:
            <strong>

                <?= $usuario["score_risco"] ?>

            </strong>

        </div>

    </div>

    <!-- GRID -->

    <div class="grid">

        <!-- DADOS -->

        <div class="card">

            <h2>

                Informações do Investidor

            </h2>

            <div class="info">

                <strong>Idade:</strong>

                <?= $usuario["idade"] ?>

            </div>

            <div class="info">

                <strong>Experiência:</strong>

                <?= htmlspecialchars(
                    $usuario["experiencia"]
                ) ?>

            </div>

            <div class="info">

                <strong>Renda Mensal:</strong>

                R$
                <?= number_format(
                    $usuario["renda_mensal"],
                    2,
                    ",",
                    "."
                ) ?>

            </div>

            <div class="info">

                <strong>Horizonte:</strong>

                <?= htmlspecialchars(
                    $usuario["horizonte_investimento"]
                ) ?>

            </div>

            <div class="info">

                <strong>Tolerância:</strong>

                <?= htmlspecialchars(
                    $usuario["tolerancia_risco"]
                ) ?>

            </div>

        </div>

        <!-- OBJETIVO -->

        <div class="card">

            <h2>

                Objetivo Financeiro

            </h2>

            <p>

                <?= nl2br(
                    htmlspecialchars(
                        $usuario["objetivo"]
                    )
                ) ?>

            </p>

        </div>

        <!-- TEXTO -->

        <div class="card">

            <h2>

                Relação com Dinheiro

            </h2>

            <p>

                <?= nl2br(
                    htmlspecialchars(
                        $usuario["descricao_pessoal"]
                    )
                ) ?>

            </p>

        </div>

        <!-- IA -->

        <div class="card">

            <h2>

                Análise Inteligente

            </h2>

            <p>

                <?= nl2br(
                    htmlspecialchars(
                        $usuario["analise_ia"]
                    )
                ) ?>

            </p>

        </div>

    </div>

    <!-- RECOMENDAÇÕES -->

    <div class="card">

        <h2>

            Recomendações do Sistema

        </h2>

        <?php

        if($usuario["perfil_risco"] == "Conservador"){

        ?>

            <div class="recomendacao">

                Priorize ativos de menor volatilidade
                e foco em preservação patrimonial.

            </div>

            <div class="recomendacao">

                Considere maior exposição em
                renda fixa e produtos previsíveis.

            </div>

        <?php

        }elseif($usuario["perfil_risco"] == "Moderado"){

        ?>

            <div class="recomendacao">

                Busque equilíbrio entre
                segurança e crescimento.

            </div>

            <div class="recomendacao">

                Uma carteira diversificada entre
                renda fixa, ETFs e ações pode ser interessante.

            </div>

        <?php

        }else{

        ?>

            <div class="recomendacao">

                Você demonstra elevada
                tolerância ao risco.

            </div>

            <div class="recomendacao">

                Estratégias de crescimento patrimonial
                e maior exposição à renda variável
                podem fazer sentido.

            </div>

        <?php } ?>

    </div>

    <!-- GRÁFICO -->

    <div class="card grafico">

        <h2>

            Perfil de Risco

        </h2>

        <canvas id="graficoPerfil"></canvas>

    </div>

    <!-- BOTÕES -->

    <div class="botoes">

        <a
        href="dashboard.php"
        class="botao">

            Dashboard

        </a>

        <a
        href="questionario_perfil.php"
        class="botao botao-secundario">

            Refazer Quiz

        </a>

    </div>

</div>

<script>

const ctx =
document.getElementById(
"graficoPerfil"
);

let valor = 50;

<?php

if($usuario["perfil_risco"] == "Conservador"){

?>

valor = 25;

<?php

}elseif($usuario["perfil_risco"] == "Moderado"){

?>

valor = 60;

<?php

}else{

?>

valor = 90;

<?php } ?>

new Chart(ctx,{

    type:"doughnut",

    data:{

        labels:[
            "Nível de risco",
            "Restante"
        ],

        datasets:[{

            data:[
                valor,
                100 - valor
            ]

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