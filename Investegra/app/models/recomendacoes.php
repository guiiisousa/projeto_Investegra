<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION["id"];

$sql = "
SELECT
nome,
perfil_risco
FROM usuarios
WHERE id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$usuario =
$stmt->get_result()
->fetch_assoc();

$perfil =
$usuario["perfil_risco"];

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
}

elseif($perfil == "Moderado"){

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
}

else{

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

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Recomendações - INVESTEGRA</title>

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

.titulo{

    color:#1f2937;
}

.botao{

    background:#00a86b;

    color:white;

    text-decoration:none;

    padding:12px 20px;

    border-radius:10px;

    font-weight:bold;
}

.botao:hover{

    background:#00915d;
}

.card{

    background:white;

    padding:30px;

    border-radius:20px;

    margin-bottom:25px;

    box-shadow:
    0 5px 15px rgba(0,0,0,.06);
}

.card h2{

    color:#1f2937;

    margin-bottom:15px;
}

.card p{

    color:#6b7280;

    line-height:1.8;
}

.badge{

    display:inline-block;

    background:#00a86b;

    color:white;

    padding:8px 15px;

    border-radius:999px;

    font-weight:bold;

    margin-top:10px;
}

.lista{

    margin-top:15px;
}

.lista li{

    margin-bottom:10px;

    color:#374151;
}

.tabela{

    width:100%;

    border-collapse:collapse;

    margin-top:15px;
}

.tabela th,
.tabela td{

    border-bottom:1px solid #e5e7eb;

    padding:12px;

    text-align:left;
}

.tabela th{

    background:#f9fafb;
}

</style>

</head>

<body>

<div class="topo">

    <h1 class="titulo">

        💡 Recomendações

    </h1>

    <a
    href="dashboard.php"
    class="botao">

        ← Dashboard

    </a>

</div>

<div class="card">

    <h2>

        Perfil Atual

    </h2>

    <p>

        Perfil:
        <strong>
            <?= htmlspecialchars($perfil) ?>
        </strong>

    </p>

    <p>

        <?= $descricao ?>

    </p>

    <div class="badge">

        Risco <?= $nivel ?>

    </div>

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

        Distribuição Sugerida

    </h2>

    <table class="tabela">

        <tr>

            <th>
                Categoria
            </th>

            <th>
                Percentual
            </th>

        </tr>

        <?php foreach($alocacao as $categoria => $percentual){ ?>

        <tr>

            <td>

                <?= $categoria ?>

            </td>

            <td>

                <?= $percentual ?>%

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>