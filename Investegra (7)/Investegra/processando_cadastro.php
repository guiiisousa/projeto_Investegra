<?php

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastro.php");
    exit;
}

$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$documento = trim($_POST["documento"] ?? "");
$tipo_usuario = $_POST["tipo_usuario"] ?? "PF";
$senha = $_POST["senha"] ?? "";
$confirmar_senha = $_POST["confirmar_senha"] ?? "";


if (
    $nome === "" ||
    $email === "" ||
    $documento === "" ||
    $senha === ""
) {
    die("Preencha todos os campos.");
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("E-mail inválido.");
}


if ($senha !== $confirmar_senha) {
    die("As senhas não coincidem.");
}


if (strlen($senha) < 6) {
    die("A senha deve possuir pelo menos 6 caracteres.");
}


if (!in_array($tipo_usuario, ["PF", "PJ"])) {
    die("Tipo de usuário inválido.");
}


/* Verificar se o e-mail já existe */

$sql = "SELECT id FROM usuarios WHERE email = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    $stmt->close();

    die("Este e-mail já está cadastrado.");

}

$stmt->close();


/* Criar hash da senha */

$senha_hash = password_hash(
    $senha,
    PASSWORD_DEFAULT
);


/* Inserir usuário */

$sql = "
    INSERT INTO usuarios
    (
        nome,
        email,
        documento,
        senha,
        tipo_usuario
    )
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sssss",
    $nome,
    $email,
    $documento,
    $senha_hash,
    $tipo_usuario
);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: login.php?cadastro=sucesso");
    exit;

}


$stmt->close();

die("Erro ao realizar o cadastro.");

?>