<?php

session_start();

include("conexao.php");

$id_usuario = $_SESSION["id"];

$sql = "
SELECT
nome,
email,
perfil_risco
FROM usuarios
WHERE id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$usuario = $stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<title>Meu Perfil - INVESTEGRA</title>

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
}

.container{

    width:500px;

    background:white;

    padding:40px;

    border-radius:24px;

    box-shadow:
    0 15px 35px rgba(0,0,0,.08);
}

.logo{

    text-align:center;

    margin-bottom:25px;
}

.logo h1{

    color:#00a86b;
}

.logo p{

    color:#6b7280;
}

h2{

    text-align:center;

    margin-bottom:25px;

    color:#1f2937;
}

input,
select{

    width:100%;

    padding:14px;

    margin-bottom:15px;

    border:1px solid #d1d5db;

    border-radius:12px;
}

input:focus,
select:focus{

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

    cursor:pointer;
}

button:hover{

    background:#00915d;
}

.voltar{

    margin-top:20px;

    text-align:center;
}

.voltar a{

    color:#00a86b;

    text-decoration:none;

    font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

    <div class="logo">

        <h1>INVESTEGRA</h1>

        <p>
            Perfil do Investidor
        </p>

    </div>

    <h2>Meu Perfil</h2>

    <form
    action="salvar_perfil.php"
    method="POST">

        <input
            type="text"
            name="nome"
            value="<?= htmlspecialchars($usuario["nome"]) ?>"
            required>

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($usuario["email"]) ?>"
            required>

        <select
            name="perfil_risco"
            required>

            <option
            value="Conservador"
            <?= $usuario["perfil_risco"] == "Conservador" ? "selected" : "" ?>>
                Conservador
            </option>

            <option
            value="Moderado"
            <?= $usuario["perfil_risco"] == "Moderado" ? "selected" : "" ?>>
                Moderado
            </option>

            <option
            value="Arrojado"
            <?= $usuario["perfil_risco"] == "Arrojado" ? "selected" : "" ?>>
                Arrojado
            </option>

        </select>

        <button type="submit">

            Salvar Alterações

        </button>

    </form>

    <div class="voltar">

        <a href="dashboard.php">

            ← Voltar ao Dashboard

        </a>

    </div>

</div>

</body>
</html>