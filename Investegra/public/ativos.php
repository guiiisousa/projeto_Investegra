<?php

session_start();

include("conexao.php");

if(!isset($_SESSION["id"])){

    header("Location: login.php");
    exit;
}

if(!isset($_GET["carteira"])){

    die("Carteira não informada.");
}

$carteira_id =
(int) $_GET["carteira"];

/* ==========================
   BUSCAR CARTEIRA
========================== */

$sql = "
SELECT *
FROM carteiras
WHERE id = ?
";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $carteira_id
);

$stmt->execute();

$carteira =
$stmt
->get_result()
->fetch_assoc();

if(!$carteira){

    die("Carteira não encontrada.");
}

/* ==========================
   BUSCAR ATIVOS
========================== */

$sql = "
SELECT *
FROM ativos
WHERE carteira_id = ?
ORDER BY data_compra DESC
";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $carteira_id
);

$stmt->execute();

$resultado =
$stmt->get_result();

/* ==========================
   PATRIMÔNIO
========================== */

$sql = "

SELECT

COALESCE(
SUM(
quantidade *
preco_medio
),
0
)

AS patrimonio

FROM ativos

WHERE carteira_id = ?

";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $carteira_id
);

$stmt->execute();

$patrimonio =

$stmt
->get_result()
->fetch_assoc()["patrimonio"];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Ativos - INVESTEGRA</title>

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

    font-size:36px;

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

    gap:25px;

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

    margin-bottom:10px;

    font-size:15px;
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
   ATIVO
====================== */

.ativo{

    background:white;

    border-radius:28px;

    padding:35px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);

    transition:.3s;
}

.ativo:hover{

    transform:translateY(-4px);
}

.ativo-topo{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:20px;

    flex-wrap:wrap;

    margin-bottom:25px;
}

.ativo h2{

    color:#111827;

    font-size:32px;
}

.badge{

    background:#dcfce7;

    color:#00a86b;

    padding:10px 18px;

    border-radius:999px;

    font-size:14px;

    font-weight:bold;
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

    padding:18px;

    border-radius:18px;
}

.info-titulo{

    color:#6b7280;

    font-size:13px;

    margin-bottom:8px;
}

.info-valor{

    color:#111827;

    font-size:20px;

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
   OBSERVAÇÃO
====================== */

.observacao{

    margin-top:15px;

    background:#f9fafb;

    padding:20px;

    border-radius:18px;

    color:#4b5563;

    line-height:1.8;
}

/* ======================
   AÇÕES
====================== */

.acoes{

    display:flex;

    gap:20px;

    margin-top:25px;

    flex-wrap:wrap;
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

.editar:hover,
.excluir:hover{

    text-decoration:underline;
}

/* ======================
   VAZIO
====================== */

.vazio{

    background:white;

    padding:60px;

    border-radius:24px;

    text-align:center;

    box-shadow:
    0 10px 25px rgba(0,0,0,.06);
}

.vazio h3{

    color:#111827;

    margin-bottom:12px;

    font-size:28px;
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

    .ativo h2{

        font-size:26px;
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

            Gestão de Ativos

        </h1>

        <p>

            <?= htmlspecialchars($carteira["nome"]) ?>

        </p>

    </div>

    <div class="botoes">

        <a
        href="carteiras.php"
        class="botao botao-secundario">

            Voltar

        </a>

        <a
        href="novo_ativo.php?carteira=<?= $carteira_id ?>"
        class="botao">

            + Novo Ativo

        </a>

    </div>

</div>

<div class="resumo">

    <div class="resumo-card">

        <h3>

            Patrimônio da Carteira

        </h3>

        <div class="resumo-valor">

            R$
            <?= number_format(
                $patrimonio,
                2,
                ",",
                "."
            ) ?>

        </div>

    </div>

    <div class="resumo-card">

        <h3>

            Quantidade de Ativos

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

        Nenhum ativo cadastrado

    </h3>

    <p>

        Adicione o primeiro ativo desta carteira.

    </p>

</div>

<?php

}else{

while(
    $ativo =
    $resultado->fetch_assoc()
){

    $valorTotal =

    $ativo["quantidade"]
    *
    $ativo["preco_medio"];

    $percentual = 0;

    if($patrimonio > 0){

        $percentual =

        (
            $valorTotal /
            $patrimonio
        )
        * 100;
    }

?>

<div class="ativo">

    <div class="ativo-topo">

        <h2>

            <?= htmlspecialchars(
                $ativo["ticker"]
            ) ?>

        </h2>

        <div class="badge">

            <?= htmlspecialchars(
                $ativo["categoria"]
            ) ?>

        </div>

    </div>

    <div class="info-grid">

        <div class="info-card">

            <div class="info-titulo">

                Quantidade

            </div>

            <div class="info-valor">

                <?= $ativo["quantidade"] ?>

            </div>

        </div>

        <div class="info-card">

            <div class="info-titulo">

                Preço Médio

            </div>

            <div class="info-valor">

                R$
                <?= number_format(
                    $ativo["preco_medio"],
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="info-card destaque">

            <div class="info-titulo">

                Valor Investido

            </div>

            <div class="info-valor">

                R$
                <?= number_format(
                    $valorTotal,
                    2,
                    ",",
                    "."
                ) ?>

            </div>

        </div>

        <div class="info-card">

            <div class="info-titulo">

                Participação na Carteira

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

                Data da Compra

            </div>

            <div class="info-valor">

                <?= date(
                    "d/m/Y",
                    strtotime(
                        $ativo["data_compra"]
                    )
                ) ?>

            </div>

        </div>

    </div>

    <?php

    if(!empty(
        $ativo["observacao"]
    )){

    ?>

    <div class="observacao">

        <strong>

            Observações

        </strong>

        <br><br>

        <?= nl2br(
            htmlspecialchars(
                $ativo["observacao"]
            )
        ) ?>

    </div>

    <?php } ?>

    <div class="acoes">

        <a
        class="editar"
        href="editar_ativo.php?id=<?= $ativo["id"] ?>">

            Editar

        </a>

        <a
        class="excluir"
        href="excluir_ativo.php?id=<?= $ativo["id"] ?>"
        onclick="return confirm('Deseja realmente excluir este ativo?')">

            Excluir

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