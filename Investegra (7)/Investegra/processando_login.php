<?php

session_start();

require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";


if ($email === "" || $senha === "") {

    header("Location: login.php?erro=1");
    exit;

}


$sql = "
    SELECT
        id,
        nome,
        email,
        senha,
        perfil_risco
    FROM usuarios
    WHERE email = ?
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "s",
    $email
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows !== 1) {

    $stmt->close();

    header("Location: login.php?erro=1");
    exit;

}


$usuario = $resultado->fetch_assoc();

$stmt->close();


if (!password_verify($senha, $usuario["senha"])) {

    header("Location: login.php?erro=1");
    exit;

}


/* Criar sessão */

session_regenerate_id(true);

$_SESSION["id"] = $usuario["id"];
$_SESSION["nome"] = $usuario["nome"];
$_SESSION["email"] = $usuario["email"];
$_SESSION["perfil_risco"] = $usuario["perfil_risco"];


header("Location: dashboard.php");
exit;

?>