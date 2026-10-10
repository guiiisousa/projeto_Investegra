<?php

$servidor = "localhost";
$usuario = "root";
$senha = "Beto2009!";
$banco = "Investegra";

$conexao = new mysqli(
    $servidor,
    $usuario,
    $senha,
    $banco
);

if ($conexao->connect_error) {
    die("Erro na conexão com o banco de dados.");
}

$conexao->set_charset("utf8mb4");

?>