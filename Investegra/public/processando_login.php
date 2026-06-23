<?php

session_start();

include("conexao.php");

$email = $_POST["email"];
$senha = $_POST["senha"];

$sql =
"SELECT * FROM usuarios
 WHERE email = ?";

$stmt =
$conn->prepare($sql);

$stmt->bind_param(
    "s",
    $email
);

$stmt->execute();

$resultado =
$stmt->get_result();

$usuario =
$resultado->fetch_assoc();

if(
    $usuario &&
    password_verify(
        $senha,
        $usuario["senha"]
    )
){

    $_SESSION["id"] =
    $usuario["id"];

    $_SESSION["nome"] =
    $usuario["nome"];

    header(
        "Location: dashboard.php"
    );

}else{

    echo "Login inválido";

}