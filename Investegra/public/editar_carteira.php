<?php

include("conexao.php");

$id = $_GET["id"];

$sql =
"SELECT * FROM carteiras WHERE id = ?";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$carteira =
$stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title>Editar Carteira - INVESTEGRA</title>

    <style>

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:'Segoe UI',sans-serif;
    }

    body{

        background:#f6f8fa;

        display:flex;
        justify-content:center;
        align-items:center;

        min-height:100vh;

        padding:20px;
    }

    .container{

        width:500px;

        background:white;

        padding:40px;

        border-radius:24px;

        box-shadow:
        0 15px 35px rgba(0,0,0,.08);

        animation:fadeIn .5s ease;
    }

    .logo{

        text-align:center;

        margin-bottom:30px;
    }

    .logo h1{

        color:#00a86b;

        font-size:2rem;

        margin-bottom:8px;
    }

    .logo p{

        color:#6b7280;

        font-size:14px;
    }

    h2{

        text-align:center;

        margin-bottom:10px;

        color:#1f2937;

        font-size:1.5rem;
    }

    .subtitulo{

        text-align:center;

        color:#6b7280;

        margin-bottom:25px;

        font-size:14px;
    }

    input,
    textarea{

        width:100%;

        padding:14px;

        margin-bottom:15px;

        border:1px solid #d1d5db;

        border-radius:12px;

        font-size:15px;

        background:#fff;

        transition:.3s;
    }

    textarea{

        min-height:120px;

        resize:none;
    }

    input:focus,
    textarea:focus{

        outline:none;

        border-color:#00a86b;

        box-shadow:
        0 0 0 4px rgba(0,168,107,.12);
    }

    button{

        width:100%;

        padding:15px;

        border:none;

        border-radius:12px;

        background:#00a86b;

        color:white;

        font-size:16px;

        font-weight:600;

        cursor:pointer;

        transition:.3s;
    }

    button:hover{

        background:#00915d;

        transform:translateY(-2px);
    }

    .voltar{

        margin-top:20px;

        text-align:center;
    }

    .voltar a{

        color:#00a86b;

        text-decoration:none;

        font-weight:600;
    }

    .voltar a:hover{

        text-decoration:underline;
    }

    @keyframes fadeIn{

        from{

            opacity:0;
            transform:translateY(20px);
        }

        to{

            opacity:1;
            transform:translateY(0);
        }
    }

    </style>

</head>

<body>

    <div class="container">

        <div class="logo">

            <h1>INVESTEGRA</h1>

            <p>
                Gerencie e acompanhe seus investimentos
            </p>

        </div>

        <h2>Editar Carteira</h2>

        <div class="subtitulo">
            Atualize as informações da sua carteira
        </div>

        <form
        action="atualizar_carteira.php"
        method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $carteira["id"] ?>">

            <input
                type="text"
                name="nome"
                value="<?= htmlspecialchars($carteira["nome"]) ?>"
                placeholder="Nome da carteira"
                required>

            <textarea
                name="descricao"
                placeholder="Descrição da carteira"><?= htmlspecialchars($carteira["descricao"]) ?></textarea>

            <button type="submit">
                Salvar Alterações
            </button>

        </form>

        <div class="voltar">

            <a href="carteiras.php">
                ← Voltar para Minhas Carteiras
            </a>

        </div>

    </div>

</body>

</html>