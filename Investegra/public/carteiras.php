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
   BUSCAR CARTEIRAS
========================== */

$sql = "

SELECT

c.*,

COALESCE(
SUM(
a.quantidade *
a.preco_medio
),
0
)

AS patrimonio,

COUNT(a.id)
AS total_ativos

FROM carteiras c

LEFT JOIN ativos a
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

GROUP BY c.id

ORDER BY patrimonio DESC

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

AS total

FROM ativos a

INNER JOIN carteiras c
ON a.carteira_id = c.id

WHERE c.usuario_id = ?

";

$stmtTotal =
$conn->prepare($sql);

$stmtTotal->bind_param(
    "i",
    $id_usuario
);

$stmtTotal->execute();

$patrimonioTotal =

$stmtTotal
->get_result()
->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Carteiras - INVESTEGRA</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    background:#f3f4f6;

    min-height:100vh;

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

.titulo h1{

    color:#111827;

    font-size:38px;

    margin-bottom:8px;
}

.titulo p{

    color:#6b7280;

    font-size:17px;
}

/* ======================
   BOTÕES
====================== */

.botoes{

    display:flex;

    gap:12px;

    flex-wrap:wrap;
}

.botao{

    background:#00a86b;

    color:white;

    padding:14px 22px;

    border-radius:14px;

    text-decoration:none;

    font-weight:bold;

    transition:.3s;
}

.botao:hover{

    background:#00915d;

    transform:translateY(-2px);
}

.botao-secundario{

    background:#1f2937;
}

.botao-secundario:hover{

    background:#111827;
}

/* ======================
   RESUMO
====================== */

.resumo{

    background:white;

    border-radius:24px;

    padding:30px;

    margin-bottom:35px;

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));

    gap:20px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.resumo-card{

    background:#f9fafb;

    padding:25px;

    border-radius:20px;
}

.resumo-card h3{

    color:#6b7280;

    margin-bottom:12px;
}

.resumo-valor{

    color:#00a86b;

    font-size:34px;

    font-weight:bold;
}

/* ======================
   LISTA
====================== */

.lista{

    display:flex;

    flex-direction:column;

    gap:25px;
}

/* ======================
   CARTEIRA
====================== */

.carteira{

    background:white;

    border-radius:28px;

    padding:35px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);

    transition:.3s;
}

.carteira:hover{

    transform:translateY(-4px);
}

.carteira-topo{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:20px;

    flex-wrap:wrap;

    margin-bottom:20px;
}

.carteira h2{

    color:#111827;

    font-size:30px;
}

.badge{

    background:#dcfce7;

    color:#00a86b;

    padding:10px 18px;

    border-radius:999px;

    font-size:14px;

    font-weight:bold;
}

.descricao{

    color:#6b7280;

    line-height:1.8;

    margin-bottom:25px;
}

/* ======================
   GRID
====================== */

.info-grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));

    gap:18px;

    margin-bottom:25px;
}

.info-card{

    background:#f9fafb;

    padding:20px;

    border-radius:18px;
}

.info-titulo{

    color:#6b7280;

    font-size:13px;

    margin-bottom:8px;
}

.info-valor{

    color:#111827;

    font-size:22px;

    font-weight:bold;
}

.destaque{

    background:#ecfdf5;

    border:1px solid #bbf7d0;
}

.destaque .info-valor{

    color:#00a86b;
}

/* ======================
   BARRA
====================== */

.barra-container{

    margin-top:10px;

    background:#e5e7eb;

    height:12px;

    border-radius:999px;

    overflow:hidden;
}

.barra{

    height:100%;

    background:#00a86b;
}

/* ======================
   AÇÕES
====================== */

.acoes{

    display:flex;

    gap:20px;

    flex-wrap:wrap;

    margin-top:20px;
}

.acoes a{

    text-decoration:none;

    font-weight:bold;
}

.editar{

    color:#00a86b;
}

.excluir{

    color:#ef4444;
}

.ativos{

    color:#2563eb;
}

.editar:hover,
.excluir:hover,
.ativos:hover{

    text-decoration:underline;
}

/* ======================
   VAZIO
====================== */

.vazio{

    background:white;

    padding:60px;

    border-radius:28px;

    text-align:center;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.vazio h3{

    color:#111827;

    font-size:30px;

    margin-bottom:12px;
}

.vazio p{

    color:#6b7280;
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

    .titulo h1{

        font-size:30px;
    }
}

</style>

</head>

<body>

<div class="topo">

    <div class="titulo">

        <h1>

            Central de Carteiras

        </h1>

        <p>

            Gerencie seus patrimônios
            e distribuições financeiras

        </p>

    </div>

    <div class="botoes">

        <a
        href="dashboard.php"
        class="botao botao-secundario">

            Dashboard

        </a>

        <a
        href="nova_carteira.php"
        class="botao">

            + Nova Carteira

        </a>

    </div>

</div>

<div class="resumo">

    <div class="resumo-card">

        <h3>

            Patrimônio Total

        </h3>

        <div class="resumo-valor">

            R$
            <?= number_format(
                $patrimonioTotal,
                2,
                ",",
                "."
            ) ?>

        </div>

    </div>

    <div class="resumo-card">

        <h3>

            Carteiras Criadas

        </h3>

        <div class="resumo-valor">

            <?= $resultado->num_rows ?>

        </div>

    </div>

</div>

<div class="lista">

<?php

if($resultado->num_rows == 0){

?>

<div class="vazio">

    <h3>

        Nenhuma carteira cadastrada

    </h3>

    <p>

        Crie sua primeira carteira
        para começar seus investimentos.

    </p>

</div>

<?php

}else{

while(
    $carteira =
    $resultado->fetch_assoc()
){

    $percentual = 0;

    if($patrimonioTotal > 0){

        $percentual =

        (
            $carteira["patrimonio"]
            /
            $patrimonioTotal
        )

        * 100;
    }

?>

<div class="carteira">

    <div class="carteira-topo">

        <h2>

            <?= htmlspecialchars(
                $carteira["nome"]
            ) ?>

        </h2>

        <div class="badge">

            <?= $carteira["total_ativos"] ?>

            ativo(s)

        </div>

    </div>

    <div class="descricao">

        <?= htmlspecialchars(
            $carteira["descricao"]
        ) ?>

    </div>

    <div class="info-grid">

        <div class="info-card destaque">

            <div class="info-titulo">

                Patrimônio

            </div>

            <div class="info-valor">

                R$
                <?= number_format(
                    $carteira["patrimonio"],
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="info-card">

            <div class="info-titulo">

                Participação Geral

            </div>

            <div class="info-valor">

                <?= number_format(
                    $percentual,
                    1,
                    ",",
                    "."
                ) ?>%

            </div>

            <div class="barra-container">

                <div
                class="barra"
                style="
                width:
                <?= $percentual ?>%;
                ">

                </div>

            </div>

        </div>

        <div class="info-card">

            <div class="info-titulo">

                Criada em

            </div>

            <div class="info-valor">

                <?= date(
                    "d/m/Y",
                    strtotime(
                        $carteira["criado_em"]
                    )
                ) ?>

            </div>

        </div>

    </div>

    <div class="acoes">

        <a
        class="editar"
        href="editar_carteira.php?id=<?= $carteira["id"] ?>">

            Editar

        </a>

        <a
        class="excluir"
        href="excluir_carteira.php?id=<?= $carteira["id"] ?>"
        onclick="return confirm('Deseja realmente excluir esta carteira?')">

            Excluir

        </a>

        <a
        class="ativos"
        href="ativos.php?carteira=<?= $carteira["id"] ?>">

            Ver Ativos

        </a>

    </div>

</div>

<?php

}

}

?>

</div>

</body>
</html>