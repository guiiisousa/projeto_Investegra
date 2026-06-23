<?php

include("conexao.php");

$nome = trim($_POST["nome"]);
$email = trim($_POST["email"]);
$documento = trim($_POST["documento"]);

$tipo_usuario = $_POST["tipo_usuario"];

$senha = $_POST["senha"];
$confirmar_senha = $_POST["confirmar_senha"];

/*
|--------------------------------------------------------------------------
| Verificar senhas
|--------------------------------------------------------------------------
*/

if ($senha !== $confirmar_senha) {

    die("As senhas não coincidem.");

}

/*
|--------------------------------------------------------------------------
| Verificar e-mail duplicado
|--------------------------------------------------------------------------
*/

$sql = "
SELECT id
FROM usuarios
WHERE email = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $email
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    die("Este e-mail já está cadastrado.");

}

/*
|--------------------------------------------------------------------------
| Criptografar senha
|--------------------------------------------------------------------------
*/

$senha_hash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);

/*
|--------------------------------------------------------------------------
| Inserir usuário
|--------------------------------------------------------------------------
*/

$sql = "
INSERT INTO usuarios
(
    nome,
    email,
    documento,
    senha,
    tipo_usuario
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?
)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssss",
    $nome,
    $email,
    $documento,
    $senha_hash,
    $tipo_usuario
);

if ($stmt->execute()) {

    header("Location: login.php");
    exit;

} else {

    echo "Erro ao cadastrar usuário.";

}